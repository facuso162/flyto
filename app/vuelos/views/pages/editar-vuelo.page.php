<?php

use App\Ciudades\Models\Ciudad;
use App\Vuelos\Models\Vuelo;

$basePath = $basePath ?? '';
$vuelo = $vuelo ?? null;
$vuelo = $vuelo instanceof Vuelo ? $vuelo : null;
$ciudades = $ciudades ?? null;
$ciudades = is_array($ciudades) ? $ciudades : [];
$flash = $flash ?? null;
$flash = is_array($flash) ? $flash : [];
$oldInput = $oldInput ?? null;
$oldInput = is_array($oldInput) ? $oldInput : [];

if ($vuelo === null) {
    return;
}

$validationErrors = is_array($flash['validationErrors'] ?? null) ? $flash['validationErrors'] : [];
$defaults = [
    'codigoVuelo' => $vuelo->codigoVuelo,
    'precio' => (string) $vuelo->precio,
    'asientosDisponibles' => (string) $vuelo->asientosDisponibles,
    'fechaSalida' => $vuelo->fechaSalida->format('Y-m-d\TH:i'),
    'fechaLlegada' => $vuelo->fechaLlegada->format('Y-m-d\TH:i'),
    'origenCiudadId' => (string) $vuelo->ciudadOrigen['idCiudad'],
    'destinoCiudadId' => (string) $vuelo->ciudadDestino['idCiudad'],
    'duracionHoras' => (string) $vuelo->duracion,
    'distanciaKm' => (string) $vuelo->distancia,
];
$value = static fn (string $field): string => htmlspecialchars(
    (string) ($oldInput[$field] ?? $defaults[$field] ?? ''),
    ENT_QUOTES,
    'UTF-8'
);
$error = static fn (string $field): string => isset($validationErrors[$field])
    ? (string) $validationErrors[$field]
    : '';
$inputClass = static fn (string $field): string => 'mt-1.5 h-[42px] w-full border bg-white px-3 text-sm outline-none transition focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy '
    . ($error($field) !== '' ? 'border-red-700' : 'border-flyto-ink/15');
$fieldError = static function (string $field) use ($error): void {
    if ($error($field) !== '') {
        echo '<span id="error-' . $field . '" class="mt-1 block text-xs text-red-700">'
            . htmlspecialchars($error($field), ENT_QUOTES, 'UTF-8') . '</span>';
    }
};
$minDateTime = (new DateTimeImmutable('+1 minute'))->format('Y-m-d\TH:i');
$e = static fn (string $text): string => htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[704px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Edición de vuelo</p>
        <h1 class="mt-1 font-display text-[29px] font-medium leading-[43px] tracking-tight">Editar vuelo · <?= $e($vuelo->codigoVuelo) ?></h1>

        <?php if (!empty($flash['error'])): ?>
            <div class="mt-5 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $e((string) $flash['error']) ?></div>
        <?php endif; ?>

        <?php if ($error('general') !== ''): ?>
            <div class="mt-3 border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert"><?= $e($error('general')) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= $e($basePath) ?>/ceo/vuelos/editar" class="mt-6" novalidate>
            <input type="hidden" name="vueloId" value="<?= $vuelo->id ?>">

            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos del vuelo</div>
                <div class="grid gap-x-5 gap-y-5 p-6 sm:grid-cols-2">
                    <label class="block sm:col-span-2">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.025em] text-flyto-muted">Código del vuelo</span>
                        <input type="text" name="codigoVuelo" value="<?= $value('codigoVuelo') ?>" maxlength="10" required class="<?= $inputClass('codigoVuelo') ?>" <?= $error('codigoVuelo') !== '' ? 'aria-invalid="true" aria-describedby="error-codigoVuelo"' : '' ?>>
                        <?php $fieldError('codigoVuelo'); ?>
                    </label>

                    <?php foreach ([
                        ['precio', 'Precio (ARS)', 'number', '0.01'],
                        ['asientosDisponibles', 'Asientos disponibles', 'number', '1'],
                        ['fechaSalida', 'Fecha y hora de salida', 'datetime-local', null],
                        ['fechaLlegada', 'Fecha y hora de llegada', 'datetime-local', null],
                    ] as [$name, $label, $type, $step]): ?>
                        <label class="block">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.025em] text-flyto-muted"><?= $label ?></span>
                            <input type="<?= $type ?>" name="<?= $name ?>" value="<?= $value($name) ?>" <?= $type === 'number' ? 'min="0" max="' . ($name === 'precio' ? '99999999.99' : '2147483647') . '" step="' . $step . '"' : 'min="' . $minDateTime . '"' ?> required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>

                    <?php foreach ([['origenCiudadId', 'Ciudad de origen'], ['destinoCiudadId', 'Ciudad de destino']] as [$name, $label]): ?>
                        <label class="block">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.025em] text-flyto-muted"><?= $label ?></span>
                            <select name="<?= $name ?>" required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                                <option value="">Seleccionar ciudad</option>
                                <?php foreach ($ciudades as $ciudad): ?>
                                    <?php if ($ciudad instanceof Ciudad): ?>
                                        <option value="<?= $ciudad->id ?>" <?= $value($name) === (string) $ciudad->id ? 'selected' : '' ?>><?= $e($ciudad->nombre) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>

                    <?php foreach ([['duracionHoras', 'Duración estimada (horas)', '0.01'], ['distanciaKm', 'Distancia (km)', '1']] as [$name, $label, $step]): ?>
                        <label class="block">
                            <span class="font-mono text-[10px] font-medium uppercase tracking-[0.025em] text-flyto-muted"><?= $label ?></span>
                            <input type="number" name="<?= $name ?>" value="<?= $value($name) ?>" min="0" max="<?= $name === 'duracionHoras' ? '999.99' : '2147483647' ?>" step="<?= $step ?>" <?= $name === 'duracionHoras' ? 'readonly' : '' ?> required class="<?= $inputClass($name) ?>" <?= $error($name) !== '' ? 'aria-invalid="true" aria-describedby="error-' . $name . '"' : '' ?>>
                            <?php $fieldError($name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= $e($basePath) ?>/ceo/vuelos" class="flex h-[42px] items-center justify-center border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">Volver</a>
                <button type="submit" class="flex h-[42px] items-center justify-center gap-2 bg-flyto-navy px-6 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">
                    Guardar cambios
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
            </div>
        </form>
    </div>
</section>
