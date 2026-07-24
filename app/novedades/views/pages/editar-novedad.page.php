<?php

use App\Novedades\Models\Novedad;

$basePath = $basePath ?? '';
$novedad = $novedad ?? null;
$novedad = $novedad instanceof Novedad ? $novedad : null;
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];

if ($novedad === null) {
    return;
}

$html = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$defaults = [
    'titulo' => $novedad->titulo,
    'categoria' => $novedad->categoria,
    'texto' => $novedad->texto,
    'fechaExpiracion' => $novedad->fechaExpiracion->format('Y-m-d'),
];
$formData = array_merge($defaults, $oldInput);
$value = static fn (string $field): string => $html($formData[$field] ?? '');
$error = static fn (string $field): string => isset($validationErrors[$field]) ? (string) $validationErrors[$field] : '';
$fieldClass = static fn (string $field): string => 'mt-1.5 w-full border bg-white px-3 text-sm outline-none transition placeholder:text-flyto-muted/40 focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy '
    . ($error($field) !== '' ? 'border-red-700' : 'border-flyto-ink/15');
$fieldError = static function (string $field) use ($error, $html): void {
    if ($error($field) !== '') {
        echo '<span id="error-' . $html($field) . '" class="mt-1 block text-xs text-red-700">'
            . $html($error($field)) . '</span>';
    }
};

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[768px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Edicion de novedad</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight text-flyto-ink">Editar novedad</h1>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($flash['error']) ?></div>
        <?php endif; ?>

        <?php if (!empty($validationErrors['general'])): ?>
            <div class="mt-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $html($validationErrors['general']) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $html($basePath) ?>/admin/novedades/editar" class="mt-6" novalidate>
            <input type="hidden" name="id" value="<?= (int) ($novedad->id ?? 0) ?>">

            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos de la novedad</div>

                <div class="p-6">
                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Titulo</span>
                        <input type="text" name="titulo" value="<?= $value('titulo') ?>" maxlength="100" required placeholder="Titulo de la novedad" class="<?= $fieldClass('titulo') ?> h-[42px]" <?= $error('titulo') !== '' ? 'aria-invalid="true" aria-describedby="error-titulo"' : '' ?>>
                        <?php $fieldError('titulo'); ?>
                    </label>

                    <label class="mt-5 block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Categoria</span>
                        <input type="text" name="categoria" value="<?= $value('categoria') ?>" maxlength="50" required placeholder="Categoria de la novedad" class="<?= $fieldClass('categoria') ?> h-[42px]" <?= $error('categoria') !== '' ? 'aria-invalid="true" aria-describedby="error-categoria"' : '' ?>>
                        <?php $fieldError('categoria'); ?>
                    </label>

                    <label class="mt-5 block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Contenido</span>
                        <textarea name="texto" rows="6" maxlength="200" required placeholder="Texto de la novedad..." class="<?= $fieldClass('texto') ?> min-h-[142px] resize-y py-2.5" <?= $error('texto') !== '' ? 'aria-invalid="true" aria-describedby="error-texto"' : '' ?>><?= $value('texto') ?></textarea>
                        <?php $fieldError('texto'); ?>
                    </label>

                    <label class="mt-5 block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Fecha de expiracion</span>
                        <input type="date" name="fechaExpiracion" min="<?= (new DateTimeImmutable('tomorrow'))->format('Y-m-d') ?>" value="<?= $value('fechaExpiracion') ?>" required class="<?= $fieldClass('fechaExpiracion') ?> h-[42px]" <?= $error('fechaExpiracion') !== '' ? 'aria-invalid="true" aria-describedby="error-fechaExpiracion"' : '' ?>>
                        <?php $fieldError('fechaExpiracion'); ?>
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="<?= $html($basePath) ?>/admin/novedades" class="flex h-[42px] items-center justify-center gap-2 border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
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
