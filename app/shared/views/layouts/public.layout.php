<?php

$pageTitle = $pageTitle ?? 'Flyto';
$content = $content ?? '';
$basePath = $basePath ?? '';
$currentPath = $currentPath ?? '/';

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Flyto, plataforma pública de reservas de vuelos.">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Inter:wght@400;500;600&family=Playfair+Display:ital,wght@0,500;0,600;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css?v=3">
</head>
<body>
    <?php require __DIR__ . '/../components/site-header.php'; ?>
    <main id="contenido-principal">
        <?= $content ?>
    </main>
    <?php require __DIR__ . '/../components/site-footer.php'; ?>
    <script src="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/js/app.js?v=3" defer></script>
</body>
</html>
