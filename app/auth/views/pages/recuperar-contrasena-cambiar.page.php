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
        <div>
            <label class="block" for="recuperar-contrasena-password">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Nueva contrase&ntilde;a</span>
            </label>
            <div class="relative">
                <input
                    id="recuperar-contrasena-password"
                    required
                    name="password"
                    type="password"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="********"
                    autocomplete="new-password"
                >
                <button type="button" id="toggle_recovery_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar contraseña" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <?php if ($cambiarContrasenaError('password') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($cambiarContrasenaError('password'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block" for="recuperar-contrasena-password-confirmation">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Repetir contrase&ntilde;a</span>
            </label>
            <div class="relative">
                <input
                    id="recuperar-contrasena-password-confirmation"
                    required
                    name="password_confirmation"
                    type="password"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="********"
                    autocomplete="new-password"
                >
                <button type="button" id="toggle_recovery_password_conf" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar contraseña" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <?php if ($cambiarContrasenaError('password_confirmation') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($cambiarContrasenaError('password_confirmation'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-5">
        <div class="border border-flyto-ink/10 bg-flyto-mist/40 px-4 py-4" aria-live="polite">
            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Requisitos de contrase&ntilde;a</p>
            <ul class="mt-3 grid gap-1.5" role="list">
                <li id="req-recup-length" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-length-icon" aria-hidden="true">✗</span>
                    <span>M&iacute;nimo 8 caracteres</span>
                    <span id="req-recup-length-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-recup-upper" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-upper-icon" aria-hidden="true">✗</span>
                    <span>Al menos una letra may&uacute;scula</span>
                    <span id="req-recup-upper-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-recup-lower" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-lower-icon" aria-hidden="true">✗</span>
                    <span>Al menos una letra min&uacute;scula</span>
                    <span id="req-recup-lower-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-recup-number" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-number-icon" aria-hidden="true">✗</span>
                    <span>Al menos un n&uacute;mero</span>
                    <span id="req-recup-number-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-recup-special" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-special-icon" aria-hidden="true">✗</span>
                    <span>Al menos un car&aacute;cter especial (!@#$%...)</span>
                    <span id="req-recup-special-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-recup-match" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-recup-match-icon" aria-hidden="true">✗</span>
                    <span>Las contrase&ntilde;as coinciden</span>
                    <span id="req-recup-match-sr" class="sr-only">Pendiente</span>
                </li>
            </ul>
        </div>
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
