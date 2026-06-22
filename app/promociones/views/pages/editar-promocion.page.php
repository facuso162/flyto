<?php

use App\Promociones\Models\Promocion;

$basePath = $basePath ?? '';
$promocion = $promocion ?? null;
$promocion = $promocion instanceof Promocion ? $promocion : null;
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];
$defaults = $promocion === null ? [] : [
    'descripcion' => $promocion->descripcion,
    'descuento' => (string) ((int) round($promocion->descuento * 100)),
];
$formData = array_merge($defaults, $oldInput);
$value = static fn (string $field): string => htmlspecialchars((string) ($formData[$field] ?? ''), ENT_QUOTES, 'UTF-8');
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
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Edición de promoción</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Editar promoción</h1>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/promociones/editar" class="mt-6" novalidate>
            <input type="hidden" name="id" value="<?= (int) ($promocion?->id ?? 0) ?>">

            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos de la promoción</div>

                <div class="p-6">
                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Descripción</span>
                        <textarea name="descripcion" rows="4" maxlength="200" required placeholder="Describí los detalles y condiciones de la promoción..." class="<?= $fieldClass('descripcion') ?> min-h-[102px] resize-y py-2.5" <?= $error('descripcion') !== '' ? 'aria-invalid="true" aria-describedby="error-descripcion"' : '' ?>><?= $value('descripcion') ?></textarea>
                        <?php $fieldError('descripcion'); ?>
                    </label>

                    <label class="mt-5 block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Descuento (porcentaje entre 0 y 100)</span>
                        <input type="number" name="descuento" value="<?= $value('descuento') ?>" min="0" max="100" step="1" inputmode="numeric" required class="<?= $fieldClass('descuento') ?> h-[42px]" <?= $error('descuento') !== '' ? 'aria-invalid="true" aria-describedby="error-descuento"' : '' ?>>
                        <span class="mt-1 block font-mono text-xs text-flyto-muted">Equivale a un <?= $value('descuento') ?>% de descuento sobre el precio total.</span>
                        <?php $fieldError('descuento'); ?>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/promociones" class="flex h-[42px] items-center justify-center gap-2 border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Volver
                </a>
                <button type="submit" class="flex h-[42px] items-center justify-center gap-2 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 16-1 4 4-1L19 8l-3-3L5 16Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</section>
