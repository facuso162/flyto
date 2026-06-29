<?php

require_once __DIR__ . '/../components/auth-ui.php';

$recuperacionTokenFlash = $flash ?? [];

flytoAuthShellStart('Verificaci&oacute;n de identidad', 'Ingres&aacute; el c&oacute;digo recibido');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena/cambiar') ?>" method="get" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if (!empty($recuperacionTokenFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $recuperacionTokenFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($recuperacionTokenFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $recuperacionTokenFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <p class="text-sm leading-[22.75px] text-flyto-muted">
        Te enviamos un c&oacute;digo de 6 d&iacute;gitos al correo registrado. Revis&aacute; tu bandeja de entrada y de spam.
    </p>

    <div class="mt-5">
        <?php flytoAuthField('C&oacute;digo de verificaci&oacute;n', 'token', 'text', '123456', 'one-time-code', 'inputmode="numeric" pattern="[0-9]{6}" maxlength="6"'); ?>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-2">
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena') ?>" class="flex h-[45.6px] items-center justify-center gap-2 border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            <?= flytoBackIcon() ?> Volver
        </a>
        <button type="submit" class="h-[45.6px] bg-flyto-navy px-5 text-sm font-medium text-flyto-sand">
            Cambiar contrase&ntilde;a
        </button>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
