<?php
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$nombres = array_map(
    static fn (array $pasajero): string => trim($pasajero['nombre'] . ' ' . $pasajero['apellido']),
    $datosPasajeros->pasajeros
);
?>
<section class="min-h-[420px] bg-flyto-sand px-6 py-16">
    <div class="mx-auto max-w-3xl border border-flyto-ink/10 bg-white p-8">
        <p class="text-base leading-7 text-flyto-ink">
            Datos recibidos correctamente para el usuario <?= $html($usuario['id']) ?>,
            vuelo <?= $html($vuelo->codigoVuelo) ?> (ID <?= $html($vuelo->id) ?>),
            con <?= $html($datosPasajeros->cantidadPasajeros) ?> pasajero(s):
            <?= $html(implode(', ', $nombres)) ?>.
        </p>
    </div>
</section>
