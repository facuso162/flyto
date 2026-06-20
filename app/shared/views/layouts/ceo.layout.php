<?php

$pageTitle = $pageTitle ?? 'Panel CEO - Flyto';
$content = $content ?? '';
$basePath = $basePath ?? '';
$currentPath = $currentPath ?? '/ceo';
$currentUser = $currentUser ?? null;
$currentUser = is_array($currentUser) ? $currentUser : [];

?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/assets/css/app.css">
</head>
<body class="min-h-screen bg-flyto-sand text-flyto-ink">
    <?php require __DIR__ . '/../../../ceo/views/components/ceo-navbar.php'; ?>

    <div class="flex min-h-[calc(100vh-56px-70px)]">
        <?php require __DIR__ . '/../../../ceo/views/components/ceo-sidebar.php'; ?>
        <main id="contenido-principal" class="min-w-0 flex-1">
            <?= $content ?>
        </main>
    </div>

    <?php require __DIR__ . '/../../../ceo/views/components/ceo-footer.php'; ?>
</body>
</html>
