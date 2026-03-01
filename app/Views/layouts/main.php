<?php
// Pull SEO from DB for this page; fall back to controller-provided values
$_seoPage = $seoPage ?? 'home';
$_seo     = Database::getSeo($_seoPage);
$_title   = $_seo['title']       ?: ($pageTitle ?? 'Clean27 — Professional Cleaning Services');
$_desc    = $_seo['description'] ?: ($pageDesc  ?? 'Background-checked, insured cleaners delivering consistent results. Book online in 60 seconds.');
$_kw      = $_seo['keywords']    ?: '';
$_og      = $_seo['og_image']    ?: '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($_desc) ?>">
    <?php if ($_kw): ?><meta name="keywords" content="<?= htmlspecialchars($_kw) ?>"><?php endif; ?>
    <!-- Open Graph -->
    <meta property="og:title"       content="<?= htmlspecialchars($_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($_desc) ?>">
    <meta property="og:type"        content="website">
    <?php if ($_og): ?><meta property="og:image" content="<?= htmlspecialchars($_og) ?>"><?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= View::asset('css/app.css') ?>">
    <?php if (!empty($extraCss)): ?>
        <link rel="stylesheet" href="<?= View::asset($extraCss) ?>">
    <?php endif; ?>
</head>

<body>

<?= $content ?>

<?php if (!empty($extraJs)): ?>
    <script src="<?= View::asset($extraJs) ?>"></script>
<?php endif; ?>

</body>
</html>

