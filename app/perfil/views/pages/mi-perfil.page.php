<?php

require_once __DIR__ . '/../../../shared/views/components/password-ui.php';

$profileUser = is_array($profileUser ?? null) ? $profileUser : [];
$flash = is_array($flash ?? null) ? $flash : [];
$oldInput = is_array($oldInput ?? null) ? $oldInput : [];
$validationErrors = is_array($validationErrors ?? null) ? $validationErrors : [];
$passwordModal = $passwordModal ?? null;
$basePath = $basePath ?? '';
$profileSection = 'datos';

$value = static function (string $field) use ($oldInput, $profileUser): string {
    return (string) (array_key_exists($field, $oldInput) ? $oldInput[$field] : ($profileUser[$field] ?? ''));
};
$error = static fn (string $field): string => (string) ($validationErrors[$field] ?? '');
$html = static fn (mixed $text): string => htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
$url = static fn (string $path): string => htmlspecialchars(rtrim($basePath, '/') . $path, ENT_QUOTES, 'UTF-8');

$nombre = trim((string) ($profileUser['nombre'] ?? ''));
$apellido = trim((string) ($profileUser['apellido'] ?? ''));
$email = trim((string) ($profileUser['email'] ?? ''));
$nombreCompleto = trim($nombre . ' ' . $apellido);
$nombreCompleto = $nombreCompleto !== '' ? $nombreCompleto : 'Usuario Flyto';

$fieldClass = 'mt-1 h-[42px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy';
$labelClass = 'font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted';
?>
<section class="bg-flyto-sand">
    <?php if (!empty($flash['success']) || !empty($flash['error'])): ?>
        <?php $toastIsSuccess = !empty($flash['success']); ?>
        <div
            data-toast
            class="fixed right-4 top-20 z-50 flex max-w-md items-start gap-3 border px-4 py-3 text-sm shadow-lg <?= $toastIsSuccess ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' ?>"
            role="<?= $toastIsSuccess ? 'status' : 'alert' ?>"
            aria-live="<?= $toastIsSuccess ? 'polite' : 'assertive' ?>"
        >
            <span><?= $html($toastIsSuccess ? $flash['success'] : $flash['error']) ?></span>
            <button type="button" data-toast-close class="ml-auto text-lg leading-4" aria-label="Cerrar mensaje">&times;</button>
        </div>
    <?php endif; ?>

    <div class="bg-flyto-navy px-6 py-7 text-flyto-sand">
        <div class="mx-auto flex max-w-7xl items-start gap-4">
            <span class="flex h-12 w-12 items-center justify-center bg-flyto-sand/20 text-flyto-sand" aria-hidden="true">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path d="M12 12C14.2091 12 16 10.2091 16 8C16 5.79086 14.2091 4 12 4C9.79086 4 8 5.79086 8 8C8 10.2091 9.79086 12 12 12Z" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M5 20C5.8 16.8 8.2 15.2 12 15.2C15.8 15.2 18.2 16.8 19 20" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-xs leading-4 text-flyto-sand/50">Cuenta de usuario</p>
                <h1 class="mt-1 font-display text-2xl font-medium leading-8 text-flyto-sand"><?= $html($nombreCompleto) ?></h1>
                <p class="text-sm leading-5 text-flyto-sand/60"><?= $html($email) ?></p>
            </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-6 px-6 py-7 md:grid-cols-[259px_1fr]">
        <?php require __DIR__ . '/../components/profile-sidebar.php'; ?>

        <div>
            <h2 class="font-display text-[23px] font-medium leading-8 text-flyto-ink">Editar perfil</h2>
            <p class="mt-1 text-sm leading-5 text-flyto-muted">Actualiz&aacute; tus datos personales o cambi&aacute; la contrase&ntilde;a de tu cuenta.</p>

            <?php if ($error('general') !== ''): ?>
                <p class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($error('general')) ?></p>
            <?php endif; ?>

            <form action="<?= $url('/mi-perfil/datos') ?>" method="post" class="mt-5 border border-flyto-ink/10 bg-white">
                <div class="border-b border-flyto-ink/10 px-6 py-4">
                    <p class="font-mono text-xs uppercase leading-4 tracking-[1.2px] text-flyto-muted">Informaci&oacute;n de la cuenta</p>
                </div>

                <div class="grid gap-4 p-6 sm:grid-cols-2">
                    <label class="block">
                        <span class="<?= $labelClass ?>">Nombre</span>
                        <input class="<?= $fieldClass ?>" type="text" name="nombre" value="<?= $html($value('nombre')) ?>" autocomplete="given-name" maxlength="80" required>
                        <p id="error-nombre" data-field-error="nombre" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('nombre') === '' ? 'true' : 'false' ?>"><?= $html($error('nombre')) ?></p>
                    </label>

                    <label class="block">
                        <span class="<?= $labelClass ?>">Apellido</span>
                        <input class="<?= $fieldClass ?>" type="text" name="apellido" value="<?= $html($value('apellido')) ?>" autocomplete="family-name" maxlength="80" required>
                        <p id="error-apellido" data-field-error="apellido" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('apellido') === '' ? 'true' : 'false' ?>"><?= $html($error('apellido')) ?></p>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="<?= $labelClass ?>">Tel&eacute;fono (opcional)</span>
                        <input class="<?= $fieldClass ?>" type="tel" name="telefono" value="<?= $html($value('telefono')) ?>" autocomplete="tel" inputmode="numeric" pattern="[0-9]*" maxlength="20" placeholder="1123456789">
                        <p id="error-telefono" data-field-error="telefono" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('telefono') === '' ? 'true' : 'false' ?>"><?= $html($error('telefono')) ?></p>
                    </label>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-flyto-ink/10 px-6 py-4 sm:flex-row sm:items-center">
                    <a href="<?= $url('/mi-perfil/datos?modal=contrasena') ?>" class="inline-flex h-11 items-center justify-center border border-flyto-navy px-5 text-sm font-medium text-flyto-navy hover:bg-flyto-navy/5">
                        Cambiar contrase&ntilde;a
                    </a>
                    <div class="flex gap-3 sm:ml-auto">
                        <a href="<?= $url('/mi-perfil/datos') ?>" class="inline-flex h-11 flex-1 items-center justify-center border border-flyto-ink/20 px-5 text-sm font-medium text-flyto-muted hover:bg-flyto-sand sm:flex-none">
                            Cancelar
                        </a>
                        <button type="submit" class="h-11 flex-1 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand sm:flex-none">
                            Editar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?php if ($passwordModal !== null): ?>
    <div class="fixed inset-0 z-40 flex items-center justify-center bg-flyto-ink/60 px-4 py-8" role="presentation">
        <section class="max-h-full w-full max-w-md overflow-y-auto border border-flyto-ink/10 bg-white shadow-xl" role="dialog" aria-modal="true" aria-labelledby="password-modal-title">
            <div class="flex items-start gap-4 border-b border-flyto-ink/10 px-6 py-5">
                <div>
                    <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Seguridad de la cuenta</p>
                    <h2 id="password-modal-title" class="mt-1 font-display text-2xl font-medium text-flyto-ink">
                        <?= $passwordModal === 'new' ? 'Nueva contrase&ntilde;a' : 'Cambiar contrase&ntilde;a' ?>
                    </h2>
                </div>
                <form action="<?= $url('/mi-perfil/contrasena/cancelar') ?>" method="post" class="ml-auto">
                    <button type="submit" class="text-2xl leading-6 text-flyto-muted hover:text-flyto-ink" aria-label="Cerrar modal">&times;</button>
                </form>
            </div>

            <?php if ($passwordModal === 'current'): ?>
                <form action="<?= $url('/mi-perfil/contrasena/verificar') ?>" method="post" class="p-6">
                    <p class="text-sm leading-5 text-flyto-muted">Ingres&aacute; tu contrase&ntilde;a actual para continuar.</p>
                    <label class="mt-5 block">
                        <span class="<?= $labelClass ?>">Contrase&ntilde;a actual</span>
                        <div class="relative">
                            <input id="current-password" class="<?= $fieldClass ?> pr-11" type="password" name="current_password" autocomplete="current-password" maxlength="40" data-password-policy="none" required autofocus>
                            <?php flytoPasswordToggleButton('current-password'); ?>
                        </div>
                        <p id="error-current_password" data-field-error="current_password" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('current_password') === '' ? 'true' : 'false' ?>"><?= $html($error('current_password')) ?></p>
                    </label>
                    <div class="mt-5 flex justify-end gap-3">
                        <button type="submit" class="h-11 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">Continuar</button>
                    </div>
                </form>
            <?php else: ?>
                <form action="<?= $url('/mi-perfil/contrasena/cambiar') ?>" method="post" class="p-6">
                    <div class="grid gap-4">
                        <label class="block">
                            <span class="<?= $labelClass ?>">Contrase&ntilde;a nueva</span>
                            <div class="relative">
                                <input id="new-password" class="<?= $fieldClass ?> pr-11" type="password" name="password" autocomplete="new-password" minlength="8" maxlength="40" required autofocus>
                                <?php flytoPasswordToggleButton('new-password'); ?>
                            </div>
                            <p id="error-password" data-field-error="password" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('password') === '' ? 'true' : 'false' ?>"><?= $html($error('password')) ?></p>
                        </label>

                        <label class="block">
                            <span class="<?= $labelClass ?>">Repetir contrase&ntilde;a</span>
                            <div class="relative">
                                <input id="new-password-confirmation" class="<?= $fieldClass ?> pr-11" type="password" name="password_confirmation" autocomplete="new-password" minlength="8" maxlength="40" required>
                                <?php flytoPasswordToggleButton('new-password-confirmation'); ?>
                            </div>
                            <p id="error-password_confirmation" data-field-error="password_confirmation" class="form-field-error mt-1 text-xs leading-5 text-red-700" role="alert" aria-hidden="<?= $error('password_confirmation') === '' ? 'true' : 'false' ?>"><?= $html($error('password_confirmation')) ?></p>
                        </label>
                    </div>

                    <div class="mt-5"><?php flytoPasswordRequirements(); ?></div>

                    <div class="mt-5 flex justify-end gap-3">
                        <button type="submit" class="h-11 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">Confirmar</button>
                    </div>
                </form>
            <?php endif; ?>

            <form action="<?= $url('/mi-perfil/contrasena/cancelar') ?>" method="post" class="border-t border-flyto-ink/10 px-6 py-4">
                <button type="submit" class="h-11 w-full border border-flyto-ink/20 px-5 text-sm font-medium text-flyto-muted hover:bg-flyto-sand">Cancelar</button>
            </form>
        </section>
    </div>
<?php endif; ?>
