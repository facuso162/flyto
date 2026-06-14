<?php

require_once __DIR__ . '/../components/auth-ui.php';

$registroStatus = $_GET['registro'] ?? '';

flytoAuthShellStart('Registro de usuario', 'Cre&aacute; tu cuenta en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/api/auth/register') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if ($registroStatus === 'error'): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            No pudimos crear la cuenta. Revis&aacute; los datos e intentalo nuevamente.
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <?php flytoAuthField('Nombre', 'nombre', 'text', 'Maria', 'given-name'); ?>
            <?php flytoAuthField('Apellido', 'apellido', 'text', 'Gonzalez', 'family-name'); ?>
        </div>
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email'); ?>
        <?php flytoAuthField('Contrase&ntilde;a', 'password', 'password', '********', 'new-password'); ?>
        <?php flytoAuthField('Repetir contrase&ntilde;a', 'password_confirmation', 'password', '********', 'new-password'); ?>
    </div>

    <div class="mt-5">
        <?php flytoPasswordRequirements(); ?>
    </div>

    <button type="submit" class="mt-5 h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">
        Registrarse
    </button>

    <p class="mt-5 text-center text-xs leading-6 text-flyto-muted">
        &iquest;Ya ten&eacute;s una cuenta?
        <a href="<?= flytoAuthUrl($basePath ?? '', '/login') ?>" class="text-base font-medium text-flyto-navy">Inici&aacute; sesi&oacute;n</a>
    </p>
</form>
<?php flytoAuthShellEnd(); ?>
