<?php

require_once __DIR__ . '/../components/auth-ui.php';

$loginStatus = $_GET['login'] ?? '';

flytoAuthShellStart('Acceso a tu cuenta', 'Inici&aacute; sesi&oacute;n en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/api/auth/login') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if ($loginStatus === 'error'): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            No pudimos iniciar sesi&oacute;n con esos datos. Revis&aacute; tu correo y contrase&ntilde;a.
        </p>
    <?php elseif ($loginStatus === 'recuperacion-pendiente'): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            La recuperaci&oacute;n de contrase&ntilde;a todav&iacute;a no est&aacute; disponible.
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email'); ?>
        <?php flytoAuthField('Contrase&ntilde;a', 'password', 'password', '********', 'current-password'); ?>
    </div>

    <a href="<?= flytoAuthUrl($basePath ?? '', '/recuperar-contrasena') ?>" class="mt-5 inline-block text-xs font-medium leading-4 text-flyto-navy">
        Olvid&eacute; mi contrase&ntilde;a
    </a>

    <div class="mt-6 grid gap-3">
        <button type="submit" class="h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">
            Iniciar sesi&oacute;n
        </button>
        <a href="<?= flytoAuthUrl($basePath ?? '', '/registro') ?>" class="flex h-[45.6px] items-center justify-center border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            Registrarse
        </a>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
