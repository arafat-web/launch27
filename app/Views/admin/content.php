<?php $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\'); ?>

<div class="page-hdr">
    <h1><i class="fa-solid fa-file-pen" style="color:var(--blue);margin-right:8px;"></i>Site Content</h1>
    <p>Edit the live text, numbers, and copy displayed across your website.</p>
</div>

<?php if ($saved): ?>
<div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Content saved successfully. Changes are live on your site.</div>
<?php endif; ?>

<form method="POST" action="<?= $base ?>/admin/content">

    <!-- Group: Hero Section -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-star"></i> Hero Section</h2>
                <p>The main banner text visitors see first on the homepage.</p>
            </div>
        </div>
        <div class="card-body">
            <?php $fields = ['hero_headline', 'hero_subtext']; ?>
            <?php foreach ($fields as $k): $f = $content[$k] ?? []; ?>
            <div class="fg">
                <label><?= htmlspecialchars($f['label'] ?? $k) ?></label>
                <?php if (($f['type'] ?? 'text') === 'textarea' || ($f['type'] ?? '') === 'html'): ?>
                <textarea name="<?= $k ?>"><?= htmlspecialchars($f['value'] ?? '') ?></textarea>
                <?php else: ?>
                <input type="text" name="<?= $k ?>" value="<?= htmlspecialchars($f['value'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Group: Stats -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-chart-bar"></i> Hero Statistics</h2>
                <p>The four key numbers shown below the hero headline.</p>
            </div>
        </div>
        <div class="card-body">
            <div class="form-row">
                <?php $stats = ['stat_clients', 'stat_rating', 'stat_guarantee', 'stat_book_time']; ?>
                <?php foreach ($stats as $k): $f = $content[$k] ?? []; ?>
                <div class="fg">
                    <label><?= htmlspecialchars($f['label'] ?? $k) ?></label>
                    <input type="text" name="<?= $k ?>" value="<?= htmlspecialchars($f['value'] ?? '') ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Group: CTA -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-bullhorn"></i> Call to Action Section</h2>
                <p>The bottom CTA banner that drives bookings.</p>
            </div>
        </div>
        <div class="card-body">
            <?php foreach (['cta_headline', 'cta_subtext', 'discount_text'] as $k): $f = $content[$k] ?? []; ?>
            <div class="fg">
                <label><?= htmlspecialchars($f['label'] ?? $k) ?></label>
                <?php if (($f['type'] ?? 'text') === 'textarea'): ?>
                <textarea name="<?= $k ?>"><?= htmlspecialchars($f['value'] ?? '') ?></textarea>
                <?php else: ?>
                <input type="text" name="<?= $k ?>" value="<?= htmlspecialchars($f['value'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Group: Company Info -->
    <div class="card">
        <div class="card-head">
            <div>
                <h2><i class="fa-solid fa-building"></i> Company Information</h2>
                <p>Business details used in the footer and contact sections.</p>
            </div>
        </div>
        <div class="card-body">
            <?php foreach (['company_name', 'company_phone', 'company_email', 'company_address', 'footer_copyright'] as $k): $f = $content[$k] ?? []; ?>
            <div class="fg">
                <label><?= htmlspecialchars($f['label'] ?? $k) ?></label>
                <?php if (($f['type'] ?? 'text') === 'textarea'): ?>
                <textarea name="<?= $k ?>"><?= htmlspecialchars($f['value'] ?? '') ?></textarea>
                <?php else: ?>
                <input type="text" name="<?= $k ?>" value="<?= htmlspecialchars($f['value'] ?? '') ?>">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="form-actions">
        <a href="<?= $base ?>/admin" class="btn btn-ghost"><i class="fa-solid fa-xmark"></i> Cancel</a>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Content</button>
    </div>

</form>
