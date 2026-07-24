<?php

$navItems = [
    ['label' => 'Inicio', 'href' => '/'],
    ['label' => 'Novedades', 'href' => '/novedades'],
    ['label' => 'Ayuda', 'href' => '/faq'],
    ['label' => 'Contacto', 'href' => '/contacto'],
    ['label' => 'Buscar vuelos', 'href' => '/#hero-section'],
];

$basePath = $basePath ?? '';
$currentPath= $currentPath ?? '';
$isAuthenticated = $isAuthenticated ?? false;
$currentUser = $currentUser ?? null;
$currentUser = is_array($currentUser) ? $currentUser : [];
$userName = trim((string) (($currentUser['nombre'] ?? '') . ' ' . ($currentUser['apellido'] ?? '')));
$isCeo = strtolower((string) ($currentUser['tipo_usuario']['nombre'] ?? '')) === 'ceo';
$isAdmin = strtolower((string) ($currentUser['tipo_usuario']['nombre'] ?? '')) === 'administrador';

if ($userName === '') {
    $userName = 'Mi perfil';
}

?>
<header class="sticky top-0 z-50 border-b border-flyto-ink/10 bg-white">
    <a class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:px-4 focus:py-2 focus:text-sm focus:text-flyto-navy" href="#contenido-principal">
        Saltar al contenido principal
    </a>
    <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-6">
        <a href="<?= htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8') ?>" class="flex items-center gap-2" aria-label="Flyto inicio">
            <span class="flex h-7 w-7 items-center justify-center bg-flyto-navy text-flyto-sand" aria-hidden="true">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none">
                    <path d="M4 12L20 4L15 20L11 13L4 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            </span>
            <span class="font-display text-[17.6px] font-semibold leading-none tracking-normal text-flyto-navy">Flyto</span>
        </a>

        <nav class="hidden items-center gap-6 md:flex" aria-label="Navegación principal">
            <?php foreach ($navItems as $item): ?>
                <?php $isActive = $currentPath === $item['href']; ?>
                <a
                    href="<?= htmlspecialchars($basePath . $item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    class="border-b pb-0.5 text-sm font-medium <?= $isActive ? 'border-flyto-ink text-flyto-ink' : 'border-transparent text-flyto-muted hover:text-flyto-ink' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php if ($isAuthenticated): ?>
            <div class="hidden items-center gap-3 md:flex">
                <?php if ($isCeo): ?>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo" class="border border-flyto-navy px-3 py-1.5 text-sm font-medium text-flyto-navy transition hover:bg-flyto-navy hover:text-white">
                        Panel CEO
                    </a>
                <?php endif; ?>
                <?php if ($isAdmin): ?>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin" class="border border-flyto-navy px-3 py-1.5 text-sm font-medium text-flyto-navy transition hover:bg-flyto-navy hover:text-white">
                        Panel Admin
                    </a>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="text-sm font-medium text-flyto-muted hover:text-flyto-ink">
                    Mis reservas
                </a>
                <div class="group relative">
                    <button type="button" class="flex items-center gap-1 py-2 text-sm font-medium text-flyto-ink" aria-haspopup="true">
                        <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                        <svg class="h-3.5 w-3.5 text-flyto-muted transition group-hover:rotate-180 group-focus-within:rotate-180" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m7 9 5 5 5-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="invisible absolute right-0 top-full w-48 border border-flyto-ink/10 bg-white p-2 opacity-0 shadow-flyto transition group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="block px-3 py-2 text-sm text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Mis reservas</a>
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/datos" class="block px-3 py-2 text-sm text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Editar perfil</a>
                        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/logout">
                            <button type="submit" class="block w-full px-3 py-2 text-left text-sm text-red-500 transition hover:bg-red-50 hover:text-red-700">Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="hidden items-center gap-3 md:flex">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/login" class="text-sm font-medium text-flyto-muted hover:text-flyto-ink">Ingresar</a>
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/registro" class="bg-flyto-navy px-4 py-2 text-sm font-medium text-flyto-sand">Registrarse</a>
            </div>
        <?php endif; ?>

        <details class="group relative md:hidden">
            <summary class="flex h-8 w-8 cursor-pointer list-none items-center justify-center text-flyto-navy" aria-label="Abrir menú">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7H20M4 12H20M4 17H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                </svg>
            </summary>
            <nav class="absolute right-0 mt-3 w-56 border border-flyto-ink/10 bg-white p-3 shadow-flyto" aria-label="Navegación móvil">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($basePath . $item['href'], ENT_QUOTES, 'UTF-8') ?>" class="block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($isAuthenticated): ?>
                    <?php if ($isCeo): ?>
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo" class="mt-2 block border border-flyto-navy px-3 py-2 text-sm font-medium text-flyto-navy">
                            Panel CEO
                        </a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin" class="mt-2 block border border-flyto-navy px-3 py-2 text-sm font-medium text-flyto-navy">
                            Panel Admin
                        </a>
                    <?php endif; ?>
                    <div class="mt-2 border-t border-flyto-ink/10 pt-2">
                        <p class="px-3 py-2 text-sm font-semibold text-flyto-ink"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></p>
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/mis-reservas" class="block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Mis reservas</a>
                        <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/mi-perfil/datos" class="block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Editar perfil</a>
                        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/logout">
                            <button type="submit" class="block w-full px-3 py-2 text-left text-sm font-medium text-red-500 transition hover:bg-red-50 hover:text-red-700">Cerrar sesión</button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/login" class="mt-2 block px-3 py-2 text-sm font-medium text-flyto-muted hover:bg-flyto-sand hover:text-flyto-ink">Ingresar</a>
                    <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/auth/registro" class="block bg-flyto-navy px-3 py-2 text-sm font-medium text-flyto-sand">Registrarse</a>
                <?php endif; ?>
            </nav>
        </details>
    </div>
</header>
