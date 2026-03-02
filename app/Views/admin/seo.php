<?php $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-magnifying-glass-chart" style="color:var(--blue);margin-right:8px;"></i>SEO Settings</h1>
    <p>Manage page titles, meta descriptions, and social sharing tags for each page.</p>
</div>

<?php if ($saved): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> SEO settings saved successfully.</div>
<?php endif; ?>

<form method="POST" action="<?= $base ?>/admin/seo">

    <!-- Page Tabs -->
    <div class="tabs">
        <?php foreach (array_keys($seoData) as $i => $p): ?>
        <button type="button" class="tab-btn <?= $i === 0 ? 'active' : '' ?>" onclick="showTab('<?= $p ?>')">
            <i class="fa-solid fa-<?= $p === 'home' ? 'house' : 'calendar-check' ?>"></i>
            <?= ucfirst($p) ?> Page
        </button>
        <?php endforeach; ?>
    </div>

    <?php foreach ($seoData as $page => $row): ?>
    <div class="tab-pane <?= $page === 'home' ? 'active' : '' ?>" id="tab-<?= $page ?>">

        <div class="card">
            <div class="card-head">
                <div>
                    <h2><i class="fa-solid fa-<?= $page === 'home' ? 'house' : 'calendar-check' ?>"></i> <?= ucfirst($page) ?> Page SEO</h2>
                    <p>These values populate the &lt;title&gt; and &lt;meta&gt; tags for the <?= ucfirst($page) ?> page.</p>
                </div>
            </div>
            <div class="card-body">

                <div class="fg">
                    <label>
                        Page Title
                        <span class="hint">Recommended: 50–60 characters</span>
                    </label>
                    <input type="text" name="<?= $page ?>_title" id="title-<?= $page ?>"
                           value="<?= htmlspecialchars($row['title']) ?>"
                           maxlength="80" placeholder="e.g. Professional Cleaning Services — BronxHomeServices"
                           oninput="updateCounter('title-<?= $page ?>','cnt-title-<?= $page ?>',60)">
                    <div class="counter" id="cnt-title-<?= $page ?>"><?= mb_strlen($row['title']) ?> / 60</div>
                </div>

                <div class="fg">
                    <label>
                        Meta Description
                        <span class="hint">Recommended: 120–160 characters</span>
                    </label>
                    <textarea name="<?= $page ?>_description" id="desc-<?= $page ?>"
                              maxlength="320" placeholder="A short description that appears in search results…"
                              oninput="updateCounter('desc-<?= $page ?>','cnt-desc-<?= $page ?>',160)"><?= htmlspecialchars($row['description']) ?></textarea>
                    <div class="counter" id="cnt-desc-<?= $page ?>"><?= mb_strlen($row['description']) ?> / 160</div>
                </div>

                <div class="fg">
                    <label>Keywords <span class="hint">Comma-separated (optional)</span></label>
                    <input type="text" name="<?= $page ?>_keywords"
                           value="<?= htmlspecialchars($row['keywords']) ?>"
                           placeholder="cleaning service, home cleaning, NYC">
                </div>

                <div class="fg" style="margin-bottom:0;">
                    <label>OG Image URL <span class="hint">Used for social sharing previews</span></label>
                    <input type="url" name="<?= $page ?>_og_image"
                           value="<?= htmlspecialchars($row['og_image']) ?>"
                           placeholder="https://example.com/og-image.jpg">
                </div>

            </div>
        </div>

        <!-- Live preview -->
        <div class="card">
            <div class="card-head">
                <div><h2><i class="fa-brands fa-google"></i> Search Preview</h2><p>Approximate appearance in Google results</p></div>
            </div>
            <div class="card-body">
                <div style="background:#fff;border-radius:8px;padding:18px 22px;color:#000;max-width:600px;">
                    <div style="font-size:.72rem;color:#006621;margin-bottom:2px;">yoursite.com/<?= $page === 'home' ? '' : $page ?></div>
                    <div id="preview-title-<?= $page ?>" style="font-size:1.1rem;color:#1a0dab;font-weight:500;font-family:Arial,sans-serif;line-height:1.3;"><?= htmlspecialchars($row['title'] ?: 'Page Title') ?></div>
                    <div id="preview-desc-<?= $page ?>" style="font-size:.85rem;color:#545454;font-family:Arial,sans-serif;margin-top:4px;line-height:1.5;"><?= htmlspecialchars($row['description'] ?: 'Meta description will appear here.') ?></div>
                </div>
            </div>
        </div>

    </div>
    <?php endforeach; ?>

    <div class="form-actions">
        <a href="<?= $base ?>/admin" class="btn btn-ghost"><i class="fa-solid fa-xmark"></i> Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save SEO Settings</button>
    </div>

</form>

<script>
function showTab(page) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + page).classList.add('active');
    event.target.closest('.tab-btn').classList.add('active');
}
function updateCounter(inputId, counterId, max) {
    const el   = document.getElementById(inputId);
    const cnt  = document.getElementById(counterId);
    const len  = el.value.length;
    cnt.textContent = len + ' / ' + max;
    cnt.className   = 'counter' + (len > max ? ' over' : len > max * .85 ? ' warn' : '');
    // Live preview
    const page = inputId.split('-')[1];
    if (inputId.startsWith('title')) {
        const prev = document.getElementById('preview-title-' + page);
        if (prev) prev.textContent = el.value || 'Page Title';
    } else if (inputId.startsWith('desc')) {
        const prev = document.getElementById('preview-desc-' + page);
        if (prev) prev.textContent = el.value || 'Meta description will appear here.';
    }
}
// Init counters
document.querySelectorAll('[id^="title-"]').forEach(el => {
    const page = el.id.split('-')[1];
    updateCounter(el.id, 'cnt-title-' + page, 60);
});
document.querySelectorAll('[id^="desc-"]').forEach(el => {
    const page = el.id.split('-')[1];
    updateCounter(el.id, 'cnt-desc-' + page, 160);
});
</script>
