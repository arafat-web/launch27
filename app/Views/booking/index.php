<!-- NAV -->
<nav>
    <a href="<?= View::url('/') ?>" class="logo">Bronx<span>HomeServices</span></a>
    <div class="nav-right">
        <div class="secure-pill"><i class="fa-solid fa-lock"></i> Secure Booking</div>
        <a href="<?= View::url('/') ?>" class="nav-back"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>
    </div>
</nav>

<div class="page">
    <!-- MAIN -->
    <div>
        <!-- PROGRESS -->
        <div class="progress-card">
            <div class="progress-top">
                <h1><i class="fa-solid fa-calendar-check" style="color:var(--blue);margin-right:8px;"></i>Book Your
                    Cleaning</h1>
                <span class="step-count" id="stepLabel">Step 1 of 4</span>
            </div>
            <div class="steps">
                <div class="step-item active" id="si1">
                    <div class="step-circle" id="sc1">1</div><span class="step-lbl">Service</span>
                </div>
                <div class="step-item" id="si2">
                    <div class="step-circle" id="sc2">2</div><span class="step-lbl">Schedule</span>
                </div>
                <div class="step-item" id="si3">
                    <div class="step-circle" id="sc3">3</div><span class="step-lbl">Details</span>
                </div>
                <div class="step-item" id="si4">
                    <div class="step-circle" id="sc4">4</div><span class="step-lbl">Confirm</span>
                </div>
            </div>
        </div>

        <!-- STEP 1 -->
        <div class="panel active" id="panel1">
            <div class="card">
                <div class="card-head">
                    <h2><i class="fa-solid fa-broom" style="color:var(--blue);margin-right:6px;"></i>Choose Your Service
                    </h2>
                    <p>Select the cleaning package that suits your home.</p>
                </div>
                <div class="card-body">
                    <div id="svcAlert"></div>
                    <div id="svcList">
                        <div class="sk"></div>
                        <div class="sk"></div>
                    </div>
                </div>
            </div>
            <div class="card" id="extrasCard" style="display:none;">
                <div class="card-head">
                    <h2><i class="fa-solid fa-plus" style="color:var(--blue);margin-right:6px;"></i>Optional Add-ons
                    </h2>
                    <p>Enhance your clean with these extras.</p>
                </div>
                <div class="card-body">
                    <div id="extrasList"></div>
                </div>
            </div>
            <div class="nav-row">
                <div></div>
                <button class="btn btn-blue" id="toStep2" disabled onclick="goStep(2)"><i
                        class="fa-solid fa-arrow-right"></i> Date &amp; Time</button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div class="panel" id="panel2">
            <div class="card">
                <div class="card-head">
                    <h2><i class="fa-solid fa-calendar-days" style="color:var(--blue);margin-right:6px;"></i>Pick a Date
                        &amp; Time</h2>
                    <p>Choose your preferred date from the calendar, then select an available time slot.</p>
                </div>
                <div class="card-body">
                    <div class="cal-wrapper">
                        <div class="cal-nav">
                            <button class="cal-btn" onclick="prevMonth()"><i
                                    class="fa-solid fa-chevron-left"></i></button>
                            <span class="cal-month" id="calMonth"></span>
                            <button class="cal-btn" onclick="nextMonth()"><i
                                    class="fa-solid fa-chevron-right"></i></button>
                        </div>
                        <div class="cal-grid">
                            <div class="cal-lbl">Su</div>
                            <div class="cal-lbl">Mo</div>
                            <div class="cal-lbl">Tu</div>
                            <div class="cal-lbl">We</div>
                            <div class="cal-lbl">Th</div>
                            <div class="cal-lbl">Fr</div>
                            <div class="cal-lbl">Sa</div>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                    </div>
                    <div class="time-section" id="timeSection" style="display:none;">
                        <div class="time-label"><i class="fa-regular fa-clock" style="color:var(--blue);"></i> Available
                            times for <span class="date-chip" id="selDateLabel"></span></div>
                        <div class="slot-grid" id="timeSlots"></div>
                    </div>
                </div>
            </div>
            <div class="nav-row">
                <button class="btn btn-ghost" onclick="goStep(1)"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button class="btn btn-blue" id="toStep3" disabled onclick="goStep(3)"><i
                        class="fa-solid fa-arrow-right"></i> Your Details</button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="panel" id="panel3">
            <div class="card">
                <div class="card-head">
                    <h2><i class="fa-solid fa-user" style="color:var(--blue);margin-right:6px;"></i>Your Details</h2>
                    <p>We need a few details to complete your booking.</p>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="fg"><label>First Name *</label><input id="fName" placeholder="Jane"
                                oninput="checkForm()"></div>
                        <div class="fg"><label>Last Name *</label><input id="lName" placeholder="Smith"
                                oninput="checkForm()"></div>
                    </div>
                    <div class="form-row">
                        <div class="fg"><label>Email Address *</label><input id="email" type="email"
                                placeholder="jane@example.com" oninput="checkForm()"></div>
                        <div class="fg"><label>Phone Number *</label><input id="phone" type="tel"
                                placeholder="+1 (555) 000-0000" oninput="checkForm()"></div>
                    </div>
                    <div class="divider">Service Address</div>
                    <div class="fg"><label>Street Address *</label><input id="addr" placeholder="123 Main Street"
                            oninput="checkForm()"></div>
                    <div class="form-row">
                        <div class="fg"><label>City *</label><input id="city" placeholder="New York"
                                oninput="checkForm()"></div>
                        <div class="fg"><label>State</label><input id="state" placeholder="NY" maxlength="3" value="NY">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="fg"><label>ZIP Code *</label><input id="zip" placeholder="10001"
                                oninput="checkForm()"></div>
                        <div class="fg"><label>Frequency</label>
                            <div class="freq-pills">
                                <label class="freq-pill"><input type="radio" name="freqRadio" value="once" checked
                                        onchange="document.getElementById('freq').value=this.value; checkForm()">
                                    <span>One-time</span></label>
                                <label class="freq-pill"><input type="radio" name="freqRadio" value="weekly"
                                        onchange="document.getElementById('freq').value=this.value; checkForm()">
                                    <span>Weekly <small>(15% off)</small></span></label>
                                <label class="freq-pill"><input type="radio" name="freqRadio" value="biweekly"
                                        onchange="document.getElementById('freq').value=this.value; checkForm()">
                                    <span>Every 2 Weeks <small>(10% off)</small></span></label>
                                <label class="freq-pill"><input type="radio" name="freqRadio" value="monthly"
                                        onchange="document.getElementById('freq').value=this.value; checkForm()">
                                    <span>Monthly <small>(5% off)</small></span></label>
                            </div>
                            <input type="hidden" id="freq" value="once">
                        </div>
                    </div>
                    <div class="fg"><label>Special Instructions</label><textarea id="notes"
                            placeholder="e.g. Gate code 1234, focus on kitchen…"></textarea></div>
                </div>
            </div>
            <div class="nav-row">
                <button class="btn btn-ghost" onclick="goStep(2)"><i class="fa-solid fa-arrow-left"></i> Back</button>
                <button class="btn btn-blue" id="toStep4" disabled onclick="goStep(4)"><i
                        class="fa-solid fa-arrow-right"></i> Review Booking</button>
            </div>
        </div>

        <!-- STEP 4 -->
        <div class="panel" id="panel4">
            <div class="card">
                <div class="card-head">
                    <h2><i class="fa-solid fa-circle-check" style="color:var(--blue);margin-right:6px;"></i>Review &amp;
                        Confirm</h2>
                    <p>Please verify all details before confirming.</p>
                </div>
                <div class="card-body" id="reviewBody"></div>
            </div>
            <div id="confirmAlert"></div>
            <div class="nav-row">
                <button class="btn btn-ghost" onclick="goStep(3)"><i class="fa-solid fa-arrow-left"></i> Edit
                    Details</button>
                <button class="btn btn-blue" id="submitBtn" onclick="submitBooking()"><i class="fa-solid fa-check"></i>
                    Confirm Booking</button>
            </div>
        </div>
    </div>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-card">
            <div class="sidebar-head">
                <h3>Booking Summary</h3>
                <p>Your selections at a glance</p>
            </div>
            <div class="sidebar-body" id="summaryBody">
                <div class="empty-sum"><i class="fa-solid fa-clipboard-list"></i>
                    <p>Select a service to see your summary here.</p>
                </div>
            </div>
        </div>
        <div class="trust-card">
            <h4><i class="fa-solid fa-shield-halved" style="color:var(--blue);margin-right:6px;"></i>You're Protected
            </h4>
            <div class="trust-line"><i class="fa-solid fa-shield-halved"></i> Fully insured &amp; bonded</div>
            <div class="trust-line"><i class="fa-solid fa-user-check"></i> Background-checked pros</div>
            <div class="trust-line"><i class="fa-solid fa-leaf"></i> Eco-friendly products</div>
            <div class="trust-line"><i class="fa-solid fa-rotate"></i> 100% satisfaction guarantee</div>
            <div class="trust-line"><i class="fa-solid fa-calendar-xmark"></i> Easy to reschedule</div>
        </div>
    </div>
</div>

<!-- ── MOBILE BOTTOM SUMMARY BAR ─────────────────────────────────────────── -->
<div class="mob-bar" id="mobBar">
    <div class="mob-bar-strip" onclick="toggleMobBar()">
        <div class="mob-bar-left">
            <span class="mob-bar-label">Booking Total</span>
            <span class="mob-bar-total" id="mobTotal"><span>$0.00</span></span>
        </div>
        <div class="mob-bar-toggle">
            <span id="mobToggleTxt">View Summary</span>
            <i class="fa-solid fa-chevron-up" id="mobChevron"></i>
        </div>
    </div>
    <div class="mob-bar-drawer" id="mobDrawer">
        <div class="mob-drawer-body" id="mobDrawerBody">
            <div class="empty-sum" style="padding:12px 0;">
                <i class="fa-solid fa-clipboard-list"></i>
                <p>Select a service to see your summary.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // ── Inject the app-relative base path for JS fetch calls ──────────────────
    const BASE_URL = <?= json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\')) ?>;
</script>
<script src="<?= View::asset('js/booking.js') ?>"></script>