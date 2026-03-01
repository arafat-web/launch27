// booking.js — Multi-step booking form logic
// BASE_URL is injected by the PHP view before this script loads

const L = {
    info: (m, d) => console.log('[INFO]', m, d || ''),
    error: (m, d) => console.error('[ERR]', m, d || '')
};

const S = {
    step: 1, services: [], selection: null, pricingParams: [],
    extraConfigs: [], extras: {},
    date: null, time: null, arrivalWindow: null, spotsCache: {},
    calY: new Date().getFullYear(), calM: new Date().getMonth(),
};

// ── API ────────────────────────────────────────────────────────────────────────
async function api(path, method = 'GET', body = null) {
    if (path === '/bookings' && method === 'POST') {
        const fd = new FormData();
        const m = {
            first_name: body.customer.first_name, last_name: body.customer.last_name,
            email: body.customer.email, phone: body.customer.phone,
            address: body.address, city: body.city, state: body.state, zip: body.zip,
            service_id: body.service_id,
            pricing_parameters: JSON.stringify(body.pricing_parameters || []),
            date: body.date, time: body.time,
            arrival_window: body.arrival_window ?? 0
        };
        Object.entries(m).forEach(([k, v]) => fd.append(k, v));
        const r = await fetch(BASE_URL + '/api/book', { method: 'POST', body: fd });
        const j = await r.json();
        if (!r.ok) throw new Error(j.message || j.error || `HTTP ${r.status}`);
        return j;
    }
    const r = await fetch(`${BASE_URL}/api/proxy?path=${encodeURIComponent(path)}`, {
        method,
        headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
        body: body ? JSON.stringify(body) : undefined
    });
    const j = await r.json();
    if (!r.ok) throw new Error(j.message || j.error || `HTTP ${r.status}`);
    return j;
}

// ── STEPS ─────────────────────────────────────────────────────────────────────
function goStep(n) {
    if (n === S.step) return;
    if (n > S.step) {
        if (n >= 2 && !S.selection) return;
        if (n >= 3 && (!S.date || !S.time)) return;
        if (n >= 4 && !formOk()) return;
    }
    S.step = n;
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
    document.getElementById(`panel${n}`).classList.add('active');
    document.getElementById('stepLabel').textContent = `Step ${n} of 4`;
    ['1', '2', '3', '4'].forEach(i => {
        const item = document.getElementById(`si${i}`), circ = document.getElementById(`sc${i}`), num = +i;
        item.className = 'step-item';
        if (num < n) { item.classList.add('done'); circ.innerHTML = '<i class="fa-solid fa-check" style="font-size:.6rem"></i>'; }
        else if (num === n) { item.classList.add('active'); circ.textContent = num; }
        else circ.textContent = num;
    });
    if (n === 4) buildReview();
    updateSidebar();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── SERVICES ──────────────────────────────────────────────────────────────────
function svcIcon(name) {
    name = (name || '').toLowerCase();
    if (name.includes('deep')) return 'fa-solid fa-sparkles';
    if (name.includes('move') || name.includes('out')) return 'fa-solid fa-box-open';
    if (name.includes('office')) return 'fa-solid fa-building';
    return 'fa-solid fa-broom';
}

async function loadServices() {
    try {
        const d = await api('/services');
        S.services = d.services || d || [];
    } catch (e) { showAlert('svcAlert', e.message, 'error'); S.services = []; }
    const el = document.getElementById('svcList');
    if (!S.services.length) { el.innerHTML = '<p style="color:var(--muted);font-size:.85rem;">No services found.</p>'; return; }
    el.innerHTML = `<div class="svc-grid">${S.services.map(s => `
    <div class="svc-card" id="sc-${s.id}" onclick="pickService(${s.id})">
      <div class="svc-card-top">
        <div class="svc-icon"><i class="${svcIcon(s.name)}"></i></div>
        <div class="svc-check" id="chk-${s.id}"><i class="fa-solid fa-check" style="font-size:.55rem"></i></div>
      </div>
      <div class="svc-name">${s.name}</div>
      <p class="svc-desc">${s.description || ''}</p>
      <div class="svc-price">$${Number(s.price).toFixed(2)}<span style="font-size:.75rem;font-weight:500;color:var(--muted);"> /visit</span></div>
      ${s.duration ? `<div style="font-size:.72rem;color:var(--muted);margin-top:3px;"><i class="fa-regular fa-clock" style="margin-right:3px;"></i>~${Math.floor(s.duration / 60)}h${s.duration % 60 ? s.duration % 60 + 'm' : ''}</div>` : ''}
    </div>`).join('')}</div>`;
    loadExtras();
}

async function loadExtras() {
    try { const d = await api('/extras'); S.extraConfigs = d.extras || d || []; } catch { }
    S.extraConfigs.forEach(e => { S.extras[e.id] = 0; });
    if (!S.extraConfigs.length) { document.getElementById('extrasCard').style.display = 'none'; return; }
    document.getElementById('extrasCard').style.display = 'block';
    document.getElementById('extrasList').innerHTML = S.extraConfigs.map(e => `
    <div class="extra-row">
      <div class="extra-info">
        <i class="fa-solid fa-plus extra-icon"></i>
        <div><div class="extra-name">${e.name}</div><div class="extra-price">+$${Number(e.price).toFixed(2)}/item</div></div>
      </div>
      <div class="qty-ctrl">
        <button class="qty-btn" onclick="chgExtra(${e.id},-1)"><i class="fa-solid fa-minus"></i></button>
        <span class="qty-val" id="qty-${e.id}">0</span>
        <button class="qty-btn" onclick="chgExtra(${e.id},1)"><i class="fa-solid fa-plus"></i></button>
      </div>
    </div>`).join('');
}

function pickService(id) {
    S.selection = S.services.find(s => s.id === id);
    S.pricingParams = S.selection?.pricing_parameters || [];
    document.querySelectorAll('.svc-card').forEach(c => c.classList.remove('selected'));
    document.getElementById(`sc-${id}`).classList.add('selected');
    document.getElementById('toStep2').disabled = false;
    updateSidebar();
}

function chgExtra(id, d) {
    S.extras[id] = Math.max(0, (S.extras[id] || 0) + d);
    document.getElementById(`qty-${id}`).textContent = S.extras[id];
    updateSidebar();
}

// ── CALENDAR ──────────────────────────────────────────────────────────────────
function renderCal() {
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const now = new Date(), y = S.calY, m = S.calM;
    document.getElementById('calMonth').textContent = `${months[m]} ${y}`;
    const first = new Date(y, m, 1).getDay(), days = new Date(y, m + 1, 0).getDate();
    const grid = document.getElementById('calGrid'); grid.innerHTML = '';
    for (let i = 0; i < first; i++) { const c = document.createElement('div'); c.className = 'cal-day empty'; grid.appendChild(c); }
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    for (let d = 1; d <= days; d++) {
        const dt = new Date(y, m, d);
        const ds = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
        const past = dt < today, isToday = dt.toDateString() === today.toDateString();
        const c = document.createElement('div');
        c.textContent = d;
        c.className = `cal-day${past ? ' past' : ''}${isToday ? ' today' : ''}${S.date === ds ? ' selected' : ''}`;
        if (!past) c.onclick = () => pickDate(ds, dt);
        grid.appendChild(c);
    }
}

function pickDate(ds, dt) {
    S.date = ds; S.time = null; S.arrivalWindow = null;
    document.getElementById('toStep3').disabled = true;
    renderCal();
    document.getElementById('selDateLabel').textContent = dt.toLocaleDateString(undefined, { weekday: 'short', month: 'long', day: 'numeric' });
    document.getElementById('timeSection').style.display = 'block';
    loadSlots(); updateSidebar();
}

async function loadSlots() {
    const el = document.getElementById('timeSlots');
    el.innerHTML = `<div class="slot-loading"><div class="dot-pulse"><span></span><span></span><span></span></div>Loading times…</div>`;
    try {
        let spots;
        if (S.spotsCache[S.date]) {
            spots = S.spotsCache[S.date];
        } else {
            const r = await fetch(`${BASE_URL}/api/proxy?path=/spots&service_id=${S.selection.id}&date=${S.date}`, { headers: { Accept: 'application/json' } });
            const raw = await r.json();
            const days = Array.isArray(raw) ? raw : [];
            const found = days.find(d => d.date === S.date);
            spots = found ? found.spots : [];
            S.spotsCache[S.date] = spots;
        }
        const free = spots.filter(s => s.free);
        if (!free.length) { el.innerHTML = '<p style="color:var(--muted);font-size:.82rem;grid-column:1/-1;">No available times on this date. Please pick another day.</p>'; return; }
        el.innerHTML = free.map(s => {
            const h = s.hours, mn = s.minutes, p = h < 12 ? 'AM' : 'PM', h12 = h % 12 || 12;
            const lbl = `${h12}:${String(mn).padStart(2, '0')} ${p}`;
            return `<div class="slot${S.time === lbl ? ' selected' : ''}" onclick="pickTime('${lbl}',${s.arrival_window})">${lbl}</div>`;
        }).join('');
    } catch (e) {
        el.innerHTML = '<p style="color:var(--error);font-size:.82rem;grid-column:1/-1;">Could not load times. Try another date.</p>';
    }
}

function pickTime(t, aw) {
    S.time = t; S.arrivalWindow = aw;
    document.getElementById('toStep3').disabled = false;
    loadSlots(); updateSidebar();
}

function prevMonth() { S.calM--; if (S.calM < 0) { S.calM = 11; S.calY--; } renderCal(); }
function nextMonth() { S.calM++; if (S.calM > 11) { S.calM = 0; S.calY++; } renderCal(); }

// ── FORM ──────────────────────────────────────────────────────────────────────
function formOk() {
    return ['fName', 'lName', 'email', 'phone', 'addr', 'city', 'zip'].every(f => document.getElementById(f)?.value.trim());
}
function checkForm() { document.getElementById('toStep4').disabled = !formOk(); updateSidebar(); }

// ── REVIEW ────────────────────────────────────────────────────────────────────
function calcTotal() {
    let extras = 0;
    S.extraConfigs.filter(e => S.extras[e.id] > 0).forEach(e => { extras += e.price * S.extras[e.id]; });
    const freq = document.getElementById('freq')?.value || 'once';
    const disc = { once: 0, weekly: .15, biweekly: .10, monthly: .05 }[freq] || 0;
    return { svc: S.selection.price, extras, disc: (S.selection.price + extras) * disc, total: (S.selection.price + extras) * (1 - disc) };
}

function buildReview() {
    const t = calcTotal(), freq = document.getElementById('freq')?.value || 'once';
    const fn = { once: 'One-time', weekly: 'Weekly', biweekly: 'Every 2 Weeks', monthly: 'Monthly' };
    const extraLines = S.extraConfigs.filter(e => S.extras[e.id] > 0).map(e =>
        `<div class="review-row"><span class="lbl"><i class="fa-solid fa-plus"></i>${e.name} ×${S.extras[e.id]}</span><span class="val">$${(e.price * S.extras[e.id]).toFixed(2)}</span></div>`
    ).join('');
    document.getElementById('reviewBody').innerHTML = `
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-user"></i>Name</span><span class="val">${document.getElementById('fName').value} ${document.getElementById('lName').value}</span></div>
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-envelope"></i>Email</span><span class="val">${document.getElementById('email').value}</span></div>
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-broom"></i>Service</span><span class="val">${S.selection.name}</span></div>
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-calendar"></i>Date &amp; Time</span><span class="val">${S.date} at ${S.time}</span></div>
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-location-dot"></i>Address</span><span class="val">${document.getElementById('addr').value}, ${document.getElementById('city').value}</span></div>
    <div class="review-row"><span class="lbl"><i class="fa-solid fa-rotate"></i>Frequency</span><span class="val">${fn[freq]}</span></div>
    <div class="review-row"><span class="lbl">Service price</span><span class="val">$${t.svc.toFixed(2)}</span></div>
    ${extraLines}
    ${t.disc > 0 ? `<div class="review-row"><span class="lbl">Recurring discount</span><span class="val" style="color:var(--success)">−$${t.disc.toFixed(2)}</span></div>` : ''}
    <div class="review-row total"><span class="lbl"><strong>Total</strong></span><span class="val"><strong>$${t.total.toFixed(2)}</strong></span></div>`;
    document.getElementById('confirmAlert').innerHTML = '';
}

// ── SIDEBAR ───────────────────────────────────────────────────────────────────
function updateSidebar() {
    const el = document.getElementById('summaryBody');
    const mobTotal = document.getElementById('mobTotal');
    const mobBody = document.getElementById('mobDrawerBody');

    if (!S.selection) {
        const empty = '<div class="empty-sum"><i class="fa-solid fa-clipboard-list"></i><p>Select a service to see your summary.</p></div>';
        el.innerHTML = empty;
        if (mobTotal) mobTotal.innerHTML = '<span>$0.00</span>';
        if (mobBody) mobBody.innerHTML = '<div class="empty-sum" style="padding:12px 0;"><i class="fa-solid fa-clipboard-list"></i><p>Select a service first.</p></div>';
        return;
    }

    const t = calcTotal();
    const rows = `
    <div class="sum-row"><span class="lbl">Service</span><span class="val">${S.selection.name}</span></div>
    ${S.date ? `<div class="sum-row"><span class="lbl"><i class="fa-solid fa-calendar" style="color:var(--blue);width:14px;margin-right:4px;"></i>Date</span><span class="val">${S.date}</span></div>` : ''}
    ${S.time ? `<div class="sum-row"><span class="lbl"><i class="fa-regular fa-clock" style="color:var(--blue);width:14px;margin-right:4px;"></i>Time</span><span class="val">${S.time}</span></div>` : ''}
    <div class="sum-row"><span class="lbl">Subtotal</span><span class="val">$${t.svc.toFixed(2)}</span></div>
    ${t.extras > 0 ? `<div class="sum-row"><span class="lbl">Add-ons</span><span class="val">$${t.extras.toFixed(2)}</span></div>` : ''}
    ${t.disc > 0 ? `<div class="sum-row"><span class="lbl" style="color:var(--success)">Discount</span><span class="val" style="color:var(--success)">\u2212$${t.disc.toFixed(2)}</span></div>` : ''}
    <div class="sum-row total"><span class="lbl"><strong>Total</strong></span><span class="val"><strong>$${t.total.toFixed(2)}</strong></span></div>`;

    el.innerHTML = rows;
    if (mobTotal) mobTotal.innerHTML = `<span>$${t.total.toFixed(2)}</span>`;
    if (mobBody) mobBody.innerHTML = rows;
}

// ── SUBMIT ────────────────────────────────────────────────────────────────────
async function submitBooking() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Processing…';
    const freqId = { once: 1, weekly: 2, biweekly: 3, monthly: 4 };
    const freq = document.getElementById('freq')?.value || 'once';
    const payload = {
        service_id: S.selection.id, date: S.date, time: S.time,
        arrival_window: S.arrivalWindow, frequency: freq, frequency_id: freqId[freq] || 1,
        pricing_parameters: S.pricingParams,
        customer: {
            first_name: document.getElementById('fName').value,
            last_name: document.getElementById('lName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value
        },
        address: document.getElementById('addr').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value,
        zip: document.getElementById('zip').value,
        notes: document.getElementById('notes').value,
        extras: Object.entries(S.extras).filter(([, q]) => q > 0).map(([id, q]) => ({ id: +id, quantity: q })),
    };
    L.info('Submitting', { date: payload.date, time: payload.time, aw: payload.arrival_window });
    try {
        const d = await api('/bookings', 'POST', payload);
        const bid = d.id || d.booking_id || d.data?.id || 'Confirmed';
        const homeUrl = BASE_URL + '/';
        document.getElementById('panel4').innerHTML = `
        <div class="card"><div class="success-wrap">
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h2>Booking Confirmed!</h2>
            <p>Your cleaning is scheduled for <strong>${S.date}</strong> at <strong>${S.time}</strong>.</p>
            <div class="booking-id">${bid}</div>
            <p>Confirmation sent to <strong>${document.getElementById('email').value}</strong>.</p>
            <div class="success-actions">
                <a href="${homeUrl}" class="btn btn-ghost"><i class="fa-solid fa-house"></i> Back to Home</a>
                <button onclick="location.reload()" class="btn btn-blue"><i class="fa-solid fa-plus"></i> Book Another</button>
            </div>
        </div></div>`;
    } catch (e) {
        L.error('Failed', { e: e.message });
        document.getElementById('confirmAlert').innerHTML = `<div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> ${e.message}</div>`;
        btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-check"></i> Confirm Booking';
    }
}

function showAlert(id, msg, type) {
    document.getElementById(id).innerHTML = `<div class="alert alert-${type}"><i class="fa-solid fa-circle-exclamation"></i> ${msg}</div>`;
}

// ── MOBILE BOTTOM BAR ─────────────────────────────────────────────────────────
function toggleMobBar() {
    const bar = document.getElementById('mobBar');
    const txt = document.getElementById('mobToggleTxt');
    if (!bar) return;
    const isOpen = bar.classList.toggle('open');
    txt.textContent = isOpen ? 'Hide Summary' : 'View Summary';
}

// ── INIT ──────────────────────────────────────────────────────────────────────
renderCal();
loadServices();
