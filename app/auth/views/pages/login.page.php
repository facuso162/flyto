<?php

require_once __DIR__ . '/../components/auth-ui.php';

$loginFlash = $flash ?? [];
$loginOldInput = $oldInput ?? [];
$loginValidationErrors = $validationErrors ?? [];
$loginValue = fn (string $key): string => (string) ($loginOldInput[$key] ?? '');
$loginError = fn (string $key): string => (string) ($loginValidationErrors[$key] ?? '');

flytoAuthShellStart('Acceso a tu cuenta', 'Inici&aacute; sesi&oacute;n en Flyto');
?>
<form action="<?= flytoAuthUrl($basePath ?? '', '/auth/login') ?>" method="post" class="mt-8 border border-flyto-ink/10 bg-white p-8">
    <?php if (!empty($loginFlash['success'])): ?>
        <p class="mb-5 border border-flyto-navy bg-flyto-navy/10 px-4 py-3 text-sm leading-5 text-flyto-navy">
            <?= htmlspecialchars((string) $loginFlash['success'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php elseif (!empty($loginFlash['error'])): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-flyto-sand px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars((string) $loginFlash['error'], ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if ($loginError('general') !== ''): ?>
        <p class="mb-5 border border-flyto-ink/10 bg-white px-4 py-3 text-sm leading-5 text-flyto-ink">
            <?= htmlspecialchars($loginError('general'), ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <div class="grid gap-4">
        <div>
            <label class="block" for="login_email">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Correo electr&oacute;nico</span>
            </label>
            <input
                id="login_email"
                required
                name="email"
                type="email"
                class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                placeholder="nombre@ejemplo.com"
                value="<?= htmlspecialchars($loginValue('email'), ENT_QUOTES, 'UTF-8') ?>"
                autocomplete="email"
            >
            <?php if ($loginError('email') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($loginError('email'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label class="block" for="login_password">
                <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted">Contrase&ntilde;a</span>
            </label>
            <div class="relative">
                <input
                    id="login_password"
                    required
                    name="password"
                    type="password"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 pr-10 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy"
                    placeholder="********"
                    autocomplete="current-password"
                >
                <button type="button" id="toggle_login_password" class="absolute right-3 top-1/2 -translate-y-1/2 text-flyto-muted hover:text-flyto-navy" aria-label="Mostrar contraseña" aria-pressed="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
            </div>
            <?php if ($loginError('password') !== ''): ?>
                <p class="mt-1 text-xs leading-5 text-flyto-ink"><?= htmlspecialchars($loginError('password'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/recuperar-contrasena') ?>" class="mt-5 inline-block text-xs font-medium leading-4 text-flyto-navy">
        Olvid&eacute; mi contrase&ntilde;a
    </a>

    <div class="mt-6 grid gap-3">
        <button id="btnSubmitLogin" type="submit" class="h-11 w-full bg-flyto-navy px-6 text-sm font-medium text-flyto-sand disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
            Iniciar sesi&oacute;n
        </button>
        <a href="<?= flytoAuthUrl($basePath ?? '', '/auth/registro') ?>" class="flex h-[45.6px] items-center justify-center border border-flyto-ink/10 bg-white px-6 text-sm font-medium text-flyto-ink">
            Registrarse
        </a>
    </div>
</form>
<?php flytoAuthShellEnd(); ?>
