<?php

$basePath = $basePath ?? '';
$paises = $paises ?? null;
$paises = is_array($paises) ? $paises : [];
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];
$value = static fn (string $field): string => htmlspecialchars((string) ($oldInput[$field] ?? ''), ENT_QUOTES, 'UTF-8');
$selected = static fn (int $paisId): string => (string) ($oldInput['paisId'] ?? '') === (string) $paisId ? ' selected' : '';
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
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Nueva aerol&iacute;nea</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Agregar aerol&iacute;nea</h1>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/aerolineas/crear" class="mt-6" novalidate>
            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos de la aerol&iacute;nea</div>

                <div class="grid gap-x-5 gap-y-4 p-6 sm:grid-cols-2">
                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Nombre de la aerol&iacute;nea</span>
                        <input type="text" name="nombre" value="<?= $value('nombre') ?>" maxlength="100" required placeholder="Ej: Aerol&iacute;neas Argentinas" class="<?= $fieldClass('nombre') ?> h-[42px]" <?= $error('nombre') !== '' ? 'aria-invalid="true" aria-describedby="error-nombre"' : '' ?>>
                        <?php $fieldError('nombre'); ?>
                    </label>

                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">C&oacute;digo IATA</span>
                        <input type="text" name="codigoIata" value="<?= $value('codigoIata') ?>" maxlength="3" required placeholder="Ej: AR" class="<?= $fieldClass('codigoIata') ?> h-[42px] uppercase" oninput="this.value = this.value.toUpperCase()" <?= $error('codigoIata') !== '' ? 'aria-invalid="true" aria-describedby="error-codigoIata"' : '' ?>>
                        <?php $fieldError('codigoIata'); ?>
                    </label>

                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Pa&iacute;s</span>
                        <select name="paisId" required class="<?= $fieldClass('paisId') ?> h-[42px]" <?= $error('paisId') !== '' ? 'aria-invalid="true" aria-describedby="error-paisId"' : '' ?>>
                            <option value="">Ej: Argentina</option>
                            <?php foreach ($paises as $pais): ?>
                                <option value="<?= (int) $pais->id ?>"<?= $selected((int) $pais->id) ?>>
                                    <?= htmlspecialchars($pais->nombre, ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php $fieldError('paisId'); ?>
                    </label>

                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Descripci&oacute;n</span>
                        <textarea name="descripcion" rows="4" maxlength="500" required placeholder="Describ&iacute; brevemente la aerol&iacute;nea, su historia y cobertura..." class="<?= $fieldClass('descripcion') ?> min-h-[102px] resize-y py-2.5" <?= $error('descripcion') !== '' ? 'aria-invalid="true" aria-describedby="error-descripcion"' : '' ?>><?= $value('descripcion') ?></textarea>
                        <?php $fieldError('descripcion'); ?>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-between gap-3">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/admin/aerolineas" class="flex h-[42px] items-center justify-center gap-2 border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Volver
                </a>
                <button type="submit" class="flex h-[42px] items-center justify-center gap-2 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    Crear aerol&iacute;nea
                </button>
            </div>
        </form>
    </div>
</section>
