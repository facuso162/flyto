<?php

use App\Reservas\Models\Reserva;

/** @var Reserva $reserva */
$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$pasajeros = array_map(
    static fn ($pasajero): string => trim($pasajero->nombre . ' ' . $pasajero->apellido),
    $reserva->pasajeros
);
?>
<section class="min-h-[420px] bg-flyto-sand px-6 py-16">
    <div class="mx-auto max-w-3xl border border-flyto-ink/10 bg-white p-8">
        <p class="text-base leading-7 text-flyto-ink">
            Reserva #<?= $html($reserva->id) ?> confirmada para el vuelo <?= $html($reserva->vuelo->codigoVuelo) ?>,
            con <?= $html(count($reserva->pasajeros)) ?> pasajero(s): <?= $html(implode(', ', $pasajeros)) ?>.
            Total: AR$ <?= $html(number_format($reserva->precioTotal, 0, ',', '.')) ?>.
        </p>
    </div>
</section>
