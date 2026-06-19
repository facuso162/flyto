<?php

$html = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

?>
<section class="mx-auto max-w-7xl px-6 py-12">
    <?php if ($error !== null): ?>
        <p><?= $html($error) ?></p>
    <?php else: ?>
        <p>Reserva #<?= (int) $reserva->id ?>: <?= $html($reserva->vuelo->ciudadOrigen['abreviacionCiudad'] ?? '') ?> a <?= $html($reserva->vuelo->ciudadDestino['abreviacionCiudad'] ?? '') ?>, vuelo <?= $html($reserva->vuelo->codigoVuelo) ?>, estado <?= $html($reserva->estado) ?>, <?= count($reserva->pasajeros) ?> pasajero(s), total USD <?= $html(number_format($reserva->precioTotal, 2, ',', '.')) ?>.</p>
    <?php endif; ?>
</section>
