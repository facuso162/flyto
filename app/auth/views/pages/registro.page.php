<?php

require_once __DIR__ . '/../components/auth-ui.php';

$registroFlash = $flash ?? [];
$registroOldInput = $oldInput ?? [];
$registroValidationErrors = $validationErrors ?? [];
$registroValue = fn (string $key): string => (string) ($registroOldInput[$key] ?? '');
$registroError = fn (string $key): string => (string) ($registroValidationErrors[$key] ?? '');

flytoAuthShellStart('Registro de usuario', 'Cre&aacute; tu cuenta en Flyto');
?>
<form id="registroForm" action="<?= flytoAuthUrl($basePath ?? '', '/auth/registro') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
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
            <label for="nombre" class="block">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Nombre</span>
                <input id="nombre" tabindex="1" required name="nombre" type="text" value="<?= htmlspecialchars($registroValue('nombre'), ENT_QUOTES, 'UTF-8') ?>" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="Maria" autocomplete="given-name">
                <?php if ($registroError('nombre') !== ''): ?>
                    <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('nombre'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </label>

            <label for="apellido" class="block">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Apellido</span>
                <input id="apellido" tabindex="2" required name="apellido" type="text" value="<?= htmlspecialchars($registroValue('apellido'), ENT_QUOTES, 'UTF-8') ?>" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="Gonzalez" autocomplete="family-name">
                <?php if ($registroError('apellido') !== ''): ?>
                    <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('apellido'), ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>
            </label>
        </div>

        <label for="email" class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Correo electr&oacute;nico</span>
            <input id="email" tabindex="3" required name="email" type="email" value="<?= htmlspecialchars($registroValue('email'), ENT_QUOTES, 'UTF-8') ?>" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="nombre@ejemplo.com" autocomplete="email">
            <?php if ($registroError('email') !== ''): ?>
                <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('email'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </label>

        <label for="telefono" class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Tel&eacute;fono</span>
            <input id="telefono" tabindex="4" required name="telefono" type="tel" value="<?= htmlspecialchars($registroValue('telefono'), ENT_QUOTES, 'UTF-8') ?>" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="1123456789" autocomplete="tel">
            <?php if ($registroError('telefono') !== ''): ?>
                <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('telefono'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </label>

        <label for="password" class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Contrase&ntilde;a</span>
            <div class="relative">
                <input id="password" tabindex="5" required name="password" type="password" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="********" autocomplete="new-password">
                <button type="button" id="togglePasswordRegistro" tabindex="6" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar u ocultar contraseña">
                    👁️
                </button>
            </div>
            <?php if ($registroError('password') !== ''): ?>
                <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('password'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </label>

        <label for="password_confirmation" class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Repetir contrase&ntilde;a</span>
            <input id="password_confirmation" tabindex="7" required name="password_confirmation" type="password" class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy" placeholder="********" autocomplete="new-password">
            <?php if ($registroError('password_confirmation') !== ''): ?>
                <p class="mt-1 text-xs text-flyto-ink"><?= htmlspecialchars($registroError('password_confirmation'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </label>
    </div>

    <div class="mt-5 border border-flyto-ink/10 bg-flyto-sand p-4">
        <p class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-ink mb-2">Requisitos de la contraseña:</p>
        <ul id="password-requisitos" class="text-xs text-flyto-muted space-y-1">
            <li id="req-largo" class="flex items-center gap-2"><span class="text-flyto-ink">❌</span> Mínimo 8 caracteres</li>
            <li id="req-numero" class="flex items-center gap-2"><span class="text-flyto-ink">❌</span> Al menos un número</li>
            <li id="req-mayuscula" class="flex items-center gap-2"><span class="text-flyto-ink">❌</span> Al menos una letra mayúscula</li>
        </ul>
    </div>

    <button id="btnSubmitRegistro" tabindex="8" disabled type="submit" class="mt-5 h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
        Registrarse
    </button>

    <p class="mt-5 text-center text-xs leading-6 text-flyto-muted">
        &iquest;Ya ten&eacute;s una cuenta?
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" tabindex="9" class="text-base font-medium text-flyto-navy">Inici&aacute; sesi&oacute;n</a>
    </p>
</form>
<?php flytoAuthShellEnd(); ?>