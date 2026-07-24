<?php

require_once __DIR__ . '/../../../shared/views/components/password-ui.php';

$basePath = $basePath ?? '';
$aerolineas = $aerolineas ?? null;
$aerolineas = is_array($aerolineas) ? $aerolineas : [];
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];
$hasAerolineas = count($aerolineas) > 0;
$value = static fn (string $field): string => htmlspecialchars((string) ($oldInput[$field] ?? ''), ENT_QUOTES, 'UTF-8');
$selected = static fn (int $aerolineaId): string => (string) ($oldInput['aerolineaId'] ?? '') === (string) $aerolineaId ? ' selected' : '';
$error = static fn (string $field): string => isset($validationErrors[$field]) ? (string) $validationErrors[$field] : '';
$fieldClass = static fn (string $field): string => 'mt-1.5 w-full border bg-white px-3 text-sm outline-none transition placeholder:text-flyto-muted/40 focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy '
    . ($error($field) !== '' ? 'border-red-700' : 'border-flyto-ink/15');
$fieldError = static function (string $field) use ($error): void {
    if ($error($field) !== '') {
        echo '<span id="error-' . $field . '" class="mt-1 block text-xs text-red-700">'
            . htmlspecialchars($error($field), ENT_QUOTES, 'UTF-8') . '</span>';
    }
};

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[768px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Nuevo CEO</p>
        <h1 class="mt-1 font-display text-3xl font-medium">Agregar CEO</h1>

        <?php if (!empty($flash['success'])): ?>
            <div class="mt-5 border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status"><?= htmlspecialchars((string) $flash['success'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <?php if (!$hasAerolineas): ?>
            <div class="mt-5 border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
                No hay aerolineas disponibles para asignar. Crea una aerolinea o libera una existente antes de agregar un CEO.
            </div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/ceos/crear" class="mt-6" novalidate>
            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos del CEO</div>

                <div class="grid gap-x-5 gap-y-4 p-6 sm:grid-cols-2">
                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Nombre</span>
                        <input type="text" name="nombre" value="<?= $value('nombre') ?>" maxlength="80" required placeholder="Ej: Carlos" class="<?= $fieldClass('nombre') ?> h-[42px]" <?= $error('nombre') !== '' ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
                        <?php $fieldError('nombre'); ?>
                    </label>

                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Apellido</span>
                        <input type="text" name="apellido" value="<?= $value('apellido') ?>" maxlength="80" required placeholder="Ej: Mendez" class="<?= $fieldClass('apellido') ?> h-[42px]" <?= $error('apellido') !== '' ? 'aria-invalid="true" aria-describedby="error-apellido"' : '' ?>>
                        <?php $fieldError('apellido'); ?>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Correo electronico</span>
                        <input type="email" name="email" value="<?= $value('email') ?>" maxlength="120" required placeholder="nombre@aerolinea.com" class="<?= $fieldClass('email') ?> h-[42px]" <?= $error('email') !== '' ? 'aria-invalid="true" aria-describedby="error-email"' : '' ?>>
                        <?php $fieldError('email'); ?>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Contrasena</span>
                        <div class="relative">
                            <input id="crear-ceo-password" type="password" name="password" minlength="8" maxlength="40" autocomplete="new-password" required placeholder="********" class="<?= $fieldClass('password') ?> h-[42px] pr-11" <?= $error('password') !== '' ? 'aria-invalid="true" aria-describedby="error-password"' : '' ?>>
                            <?php flytoPasswordToggleButton('crear-ceo-password'); ?>
                        </div>
                        <?php $fieldError('password'); ?>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Confirmar contrasena</span>
                        <div class="relative">
                            <input id="crear-ceo-password-confirmation" type="password" name="password_confirmation" minlength="8" maxlength="40" autocomplete="new-password" required placeholder="********" class="<?= $fieldClass('password_confirmation') ?> h-[42px] pr-11" <?= $error('password_confirmation') !== '' ? 'aria-invalid="true" aria-describedby="error-password_confirmation"' : '' ?>>
                            <?php flytoPasswordToggleButton('crear-ceo-password-confirmation'); ?>
                        </div>
                        <?php $fieldError('password_confirmation'); ?>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Aerolinea asignada</span>
                        <select name="aerolineaId" required class="<?= $fieldClass('aerolineaId') ?> h-[42px]" <?= !$hasAerolineas ? 'disabled' : '' ?> <?= $error('aerolineaId') !== '' ? 'aria-invalid="true" aria-describedby="error-aerolineaId"' : '' ?>>
                            <option value="">Seleccionar aerolinea</option>
                            <?php foreach ($aerolineas as $aerolinea): ?>
                                <option value="<?= (int) $aerolinea->id ?>"<?= $selected((int) $aerolinea->id) ?>>
                                    <?= htmlspecialchars($aerolinea->nombre . ' (' . $aerolinea->codigoIata . ')', ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php $fieldError('aerolineaId'); ?>
                    </label>

                    <div class="sm:col-span-2">
                        <?php flytoPasswordRequirements(); ?>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/ceos" class="flex h-[42px] items-center justify-center gap-2 border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Volver
                </a>
                <button type="submit" class="flex h-[42px] items-center justify-center gap-2 px-6 text-sm font-medium transition <?= $hasAerolineas ? 'bg-flyto-navy text-flyto-sand hover:bg-flyto-ink' : 'cursor-not-allowed bg-[#e5e4e0] text-flyto-muted' ?>" <?= !$hasAerolineas ? 'disabled' : '' ?>>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    Crear CEO
                </button>
            </div>
        </form>
    </div>
</section>
