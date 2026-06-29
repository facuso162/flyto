<?php

require_once __DIR__ . '/../components/auth-ui.php';

$recuperacionFlash = $flash ?? [];
$recuperacionOldInput = $oldInput ?? [];
$recuperacionValidationErrors = $validationErrors ?? [];
$recuperacionValue = fn (string $key): string => (string) ($recuperacionOldInput[$key] ?? '');
$recuperacionError = fn (string $key): string => (string) ($recuperacionValidationErrors[$key] ?? '');

flytoAuthShellStart('Recuperaci&oacute;n de cuenta', 'Recuper&aacute; tu contrase&ntilde;a');
?>
<form
    id="recuperar-contrasena-form"
    action="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena') ?>"
    method="post"
    class="mt-8 border border-flyto-ink/10 bg-white p-8"
    novalidate
>
    <?php if (!empty($recuperacionFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $recuperacionFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($recuperacionFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $recuperacionFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($recuperacionError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($recuperacionError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <p class="text-sm leading-[22.75px] text-flyto-muted">
        Ingres&aacute; tu correo electr&oacute;nico y te enviaremos un c&oacute;digo para restablecer tu contrase&ntilde;a.
    </p>

    <div class="mt-5">
        <?php flytoAuthField('Correo electr&oacute;nico', 'email', 'email', 'nombre@ejemplo.com', 'email', 'id="recuperar-contrasena-email"', $recuperacionValue('email'), $recuperacionError('email')); ?>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-2">
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" class="flex items-center justify-center gap-2 border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink" style="height: 64px;">
            <?= flytoBackIcon() ?> Volver
        </a>
        <button
            id="recuperar-contrasena-submit"
            type="submit"
            class="cursor-not-allowed border border-flyto-ink/10 bg-[#e5e4e0] px-5 py-3 text-sm font-medium leading-5 text-flyto-muted"
            style="height: 64px;"
            disabled
        >
            Enviar email de recuperaci&oacute;n
        </button>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
