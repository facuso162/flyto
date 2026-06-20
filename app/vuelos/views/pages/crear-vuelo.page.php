<?php

use App\Ciudades\Models\Ciudad;

$basePath = $basePath ?? '';
$ciudades = $ciudades ?? null;
$ciudades = is_array($ciudades) ? $ciudades : [];
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];
$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];
$codigoVueloPropuesto = (string) ($codigoVueloPropuesto ?? '');
$value = static fn (string $field, mixed $default = ''): string => htmlspecialchars((string) ($oldInput[$field] ?? $default), ENT_QUOTES, 'UTF-8');
$error = static fn (string $field): string => isset($validationErrors[$field]) ? (string) $validationErrors[$field] : '';
$inputClass = static fn (string $field): string => 'mt-1.5 h-[42px] w-full border bg-white px-3 text-sm outline-none transition placeholder:text-flyto-muted/40 focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy '
    . ($error($field) !== '' ? 'border-red-700' : 'border-flyto-ink/15');
$minDateTime = (new DateTimeImmutable('+1 minute'))->format('Y-m-d\TH:i');
$fieldError = static function (string $field) use ($error): void {
    if ($error($field) !== '') {
        echo '<span id="error-' . $field . '" class="mt-1 block text-xs text-red-700">'
            . htmlspecialchars($error($field), ENT_QUOTES, 'UTF-8') . '</span>';
    }
};

?>
<section class="px-5 py-8 sm:px-8 lg:py-8">
    <div class="mx-auto max-w-[900px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Nuevo vuelo</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Crear vuelo</h1>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= htmlspecialchars((string) $flash['error'], ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos/crear" class="mt-6" novalidate>
            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos del vuelo</div>
                <div class="grid gap-x-5 gap-y-5 p-6 sm:grid-cols-2">
                    <?php foreach ([
                        ['codigoVuelo', 'Código del vuelo', 'text', $codigoVueloPropuesto, 'AAA000', 'maxlength="10"'],
                        ['precio', 'Precio (AR$)', 'number', '', '1240', 'min="0" step="0.01"'],
                        ['asientosDisponibles', 'Asientos disponibles', 'number', '', '180', 'min="0" step="1"'],
                        ['fechaSalida', 'Fecha y hora de salida', 'datetime-local', '', '', 'min="' . $minDateTime . '"'],
                        ['fechaLlegada', 'Fecha y hora de llegada', 'datetime-local', '', '', 'min="' . $minDateTime . '"'],
                    ] as [$name, $label, $type, $default, $placeholder, $attributes]): ?>
                        <label class="block <?= $name === 'codigoVuelo' ? 'sm:col-span-2' : '' ?>">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted"><?= $label ?></span>
                            <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $value($name, $default) ?>" placeholder="<?= $placeholder ?>" <?= $attributes ?> required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>

                    <?php foreach ([['origenCiudadId', 'Ciudad de origen'], ['destinoCiudadId', 'Ciudad de destino']] as [$name, $label]): ?>
                        <label class="block">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted"><?= $label ?></span>
                            <select name="<?= $name ?>" required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                                <option value="">Seleccionar ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <?php if ($ciudad instanceof Ciudad): ?>
                                        <option value="<?= $ciudad->id ?>" <?= $value($name) === (string) $ciudad->id ? 'selected' : '' ?>><?= htmlspecialchars($ciudad->nombre, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>

                    <?php foreach ([['duracionHoras', 'Duración estimada (horas)', '9.5', '0.01'], ['distanciaKm', 'Distancia (km)', '7240', '1']] as [$name, $label, $placeholder, $step]): ?>
                        <label class="block">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted"><?= $label ?></span>
                            <input type="number" name="<?= $name ?>" value="<?= $value($name) ?>" placeholder="<?= $placeholder ?>" min="0" step="<?= $step ?>" required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= htmlspecialchars($basePath, ENT_QUOTES, 'UTF-8') ?>/ceo/vuelos" class="flex h-[42px] items-center justify-center gap-2 border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m15 18-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Volver
                </a>
                <button type="submit" class="flex h-[42px] items-center justify-center gap-2 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                    Crear vuelo
                </button>
            </div>
        </form>
    </div>
</section>
