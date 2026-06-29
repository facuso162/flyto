<?php

require_once __DIR__ . '/../components/auth-ui.php';

$cambiarContrasenaFlash = $flash ?? [];
$cambiarContrasenaOldInput = $oldInput ?? [];
$cambiarContrasenaValidationErrors = $validationErrors ?? [];
$cambiarContrasenaUsuarioId = (int) ($usuarioId ?? 0);
$cambiarContrasenaError = fn (string $key): string => (string) ($cambiarContrasenaValidationErrors[$key] ?? '');

flytoAuthShellStart('Cambio de contrase&ntilde;a', 'Cre&aacute; tu nueva contrase&ntilde;a');
?>
<form
    id="recuperar-contrasena-cambiar-form"
    action="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena/cambiar') ?>"
    method="post"
    class="mt-8 border border-flyto-ink/10 bg-white p-8"
    novalidate
>
    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars((string) $cambiarContrasenaUsuarioId, ENT_QUOTES, 'UTF-8') ?>">

    <?php if (!empty($cambiarContrasenaFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $cambiarContrasenaFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($cambiarContrasenaFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $cambiarContrasenaFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($cambiarContrasenaError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($cambiarContrasenaError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <?php flytoAuthField('Nueva contrase&ntilde;a', 'password', 'password', '********', 'new-password', 'id="recuperar-contrasena-password"', '', $cambiarContrasenaError('password')); ?>
        <?php flytoAuthField('Repetir contrase&ntilde;a', 'password_confirmation', 'password', '********', 'new-password', 'id="recuperar-contrasena-password-confirmation"', '', $cambiarContrasenaError('password_confirmation')); ?>
    </div>

    <div class="mt-5">
        <?php flytoPasswordRequirements(); ?>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-2">
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena/codigo') ?>" class="flex h-[45.6px] items-center justify-center gap-2 border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            <?= flytoBackIcon() ?> Volver
        </a>
        <button
            id="recuperar-contrasena-cambiar-submit"
            type="submit"
            class="h-[45.6px] cursor-not-allowed border border-flyto-ink/10 bg-[#e5e4e0] px-5 text-sm font-medium text-flyto-muted"
            disabled
        >
            Cambiar contrase&ntilde;a
        </button>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
