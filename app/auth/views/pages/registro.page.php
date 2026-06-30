<?php

require_once __DIR__ . '/../components/auth-ui.php';

$registroFlash = $flash ?? [];
$registroOldInput = $oldInput ?? [];
$registroValidationErrors = $validationErrors ?? [];
$registroValue = fn (string $key): string => (string) ($registroOldInput[$key] ?? '');
$registroError = fn (string $key): string => (string) ($registroValidationErrors[$key] ?? '');

flytoAuthShellStart('Registro de usuario', 'Cre&aacute; tu cuenta en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/registro') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if (!empty($registroFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $registroFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($registroFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $registroFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($registroError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($registroError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block" for="reg_nombre">
                    <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Nombre</span>
                </label>
                <input
                    id="reg_nombre"
                    required
                    name="nombre"
                    type="text"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="Maria"
                    value="<?= htmlspecialchars($registroValue('nombre'), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="given-name"
                >
                <?php if ($registroError('nombre') !== ''): ?>
                    <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('nombre'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block" for="reg_apellido">
                    <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Apellido</span>
                </label>
                <input
                    id="reg_apellido"
                    required
                    name="apellido"
                    type="text"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="Gonzalez"
                    value="<?= htmlspecialchars($registroValue('apellido'), ENT_QUOTES, 'UTF-8') ?>"
                    autocomplete="family-name"
                >
                <?php if ($registroError('apellido') !== ''): ?>
                    <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('apellido'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block" for="reg_email">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Correo electr&oacute;nico</span>
            </label>
            <input
                id="reg_email"
                required
                name="email"
                type="email"
                class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                placeholder="nombre@ejemplo.com"
                value="<?= htmlspecialchars($registroValue('email'), ENT_QUOTES, 'UTF-8') ?>"
                autocomplete="email"
            >
            <?php if ($registroError('email') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('email'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block" for="reg_telefono">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Tel&eacute;fono</span>
            </label>
            <input
                id="reg_telefono"
                required
                name="telefono"
                type="tel"
                class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                placeholder="1123456789"
                value="<?= htmlspecialchars($registroValue('telefono'), ENT_QUOTES, 'UTF-8') ?>"
                autocomplete="tel"
            >
            <?php if ($registroError('telefono') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('telefono'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block" for="reg_password">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Contrase&ntilde;a</span>
            </label>
            <div class="relative">
                <input
                    id="reg_password"
                    required
                    name="password"
                    type="password"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="********"
                    autocomplete="new-password"
                >
                <button type="button" id="toggle_reg_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar contraseña" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <?php if ($registroError('password') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('password'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block" for="reg_password_conf">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Repetir contrase&ntilde;a</span>
            </label>
            <div class="relative">
                <input
                    id="reg_password_conf"
                    required
                    name="password_confirmation"
                    type="password"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="********"
                    autocomplete="new-password"
                >
                <button type="button" id="toggle_reg_password_conf" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar contraseña" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <?php if ($registroError('password_confirmation') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($registroError('password_confirmation'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-5">
        <div class="border border-flyto-ink/10 bg-flyto-mist/40 px-4 py-4" aria-live="polite">
            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Requisitos de contrase&ntilde;a</p>
            <ul class="mt-3 grid gap-1.5" role="list">
                <li id="req-length" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-length-icon" aria-hidden="true">✗</span>
                    <span>M&iacute;nimo 8 caracteres</span>
                    <span id="req-length-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-upper" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-upper-icon" aria-hidden="true">✗</span>
                    <span>Al menos una letra may&uacute;scula</span>
                    <span id="req-upper-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-lower" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-lower-icon" aria-hidden="true">✗</span>
                    <span>Al menos una letra min&uacute;scula</span>
                    <span id="req-lower-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-number" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-number-icon" aria-hidden="true">✗</span>
                    <span>Al menos un n&uacute;mero</span>
                    <span id="req-number-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-special" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-special-icon" aria-hidden="true">✗</span>
                    <span>Al menos un car&aacute;cter especial (!@#$%...)</span>
                    <span id="req-special-sr" class="sr-only">Pendiente</span>
                </li>
                <li id="req-match" class="flex items-center gap-3 text-xs leading-4 text-flyto-muted" role="listitem">
                    <span id="req-match-icon" aria-hidden="true">✗</span>
                    <span>Las contrase&ntilde;as coinciden</span>
                    <span id="req-match-sr" class="sr-only">Pendiente</span>
                </li>
            </ul>
        </div>
    </div>

    <button id="btnSubmitRegistro" type="submit" class="mt-5 h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
        Registrarse
    </button>

    <p class="mt-5 text-center text-xs leading-6 text-flyto-muted">
        &iquest;Ya ten&eacute;s una cuenta?
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" class="text-base font-medium text-flyto-navy">Inici&aacute; sesi&oacute;n</a>
    </p>
</form>
<?php flytoAuthShellEnd(); ?>
