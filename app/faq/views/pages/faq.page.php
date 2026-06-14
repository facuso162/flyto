<?php

$faqs = [
    [
        'question' => '¿Cómo puedo realizar una reserva en Flyto?',
        'answer' => 'Para reservar un vuelo, ingresá el origen, destino, fechas y cantidad de pasajeros en el buscador de la página principal. Seleccioná el vuelo que mejor se adapte a tus necesidades, completá los datos de los pasajeros y confirmá la reserva. Podés elegir pagar en el momento o hacerlo más adelante dentro del período permitido.',
    ],
    [
        'question' => '¿Puedo reservar un vuelo y pagar en otro momento?',
        'answer' => 'Sí. Flyto ofrece la modalidad «reservar ahora, pagar después». Una vez confirmados los datos de los pasajeros, tu asiento queda garantizado por hasta 72 horas con el precio congelado. Podés acceder al pago desde la sección «Mis reservas» en cualquier momento dentro de ese período.',
    ],
    [
        'question' => '¿Qué métodos de pago aceptan?',
        'answer' => 'Aceptamos tarjetas de crédito y débito de las principales redes (Visa, Mastercard, American Express) y transferencias bancarias. En el caso de transferencia, los datos de la cuenta aparecen al momento del pago y la reserva se confirma una vez acreditado el importe. Todos los pagos se procesan de forma segura con certificación PCI-DSS.',
    ],
    [
        'question' => '¿Cómo puedo cancelar o modificar una reserva?',
        'answer' => 'Podés gestionar tus reservas desde la sección «Mis reservas» en tu cuenta. Las cancelaciones y cambios están sujetos a las condiciones tarifarias del vuelo seleccionado: algunos permiten modificaciones sin cargo, mientras que otras tarifas son no reembolsables. Te recomendamos revisar las condiciones antes de confirmar la compra.',
    ],
    [
        'question' => '¿Cómo recibo la confirmación de mi vuelo?',
        'answer' => 'Una vez completada la reserva y el pago, recibirás un correo electrónico con el itinerario completo, el código de reserva y, cuando corresponda, las tarjetas de embarque. Si no encontrás el correo en tu bandeja de entrada, revisá la carpeta de spam o accedé directamente a «Mis reservas» para descargarlo.',
    ],
    [
        'question' => '¿Flyto incluye el equipaje en el precio del vuelo?',
        'answer' => 'Depende de la tarifa y la aerolínea. En los resultados de búsqueda, cada opción indica claramente qué equipaje está incluido: equipaje de mano, equipaje facturado de 23 kg o únicamente artículo personal. Podés agregar equipaje adicional durante el proceso de reserva o directamente en el sitio de la aerolínea con tu código de reserva.',
    ],
    [
        'question' => '¿Qué hago si mi vuelo sufre una demora o cancelación?',
        'answer' => 'En caso de demoras o cancelaciones operadas por la aerolínea, el equipo de Flyto te notificará por correo electrónico con las alternativas disponibles. Las aerolíneas están obligadas a ofrecer reubicación en otro vuelo o reembolso según la normativa vigente. Podés contactar a nuestro equipo de soporte desde la sección «Ayuda» para recibir asistencia personalizada.',
    ],
    [
        'question' => '¿Es seguro ingresar mis datos de pago en Flyto?',
        'answer' => 'Sí. Flyto utiliza encriptación SSL en todas las transacciones y cumple con el estándar de seguridad PCI-DSS para el procesamiento de pagos con tarjeta. Los datos de tu tarjeta nunca se almacenan en nuestros servidores: son procesados directamente por la pasarela de pago certificada. Podés verificar la conexión segura mediante el candado en la barra de dirección de tu navegador.',
    ],
];

?>
<section class="bg-flyto-navy px-6 pt-16 pb-8 text-flyto-sand">
    <div class="mx-auto max-w-7xl">
        <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-gold">Centro de ayuda</p>
        <h1 class="mt-4 font-display text-[32px] font-medium leading-10 md:text-[47.8px] md:leading-[59.76px]">Preguntas frecuentes</h1>
    </div>
</section>

<section class="bg-flyto-sand px-6 py-10 md:py-12">
    <div class="mx-auto max-w-[1072px] border border-flyto-ink/10 bg-white">
        <?php foreach ($faqs as $index => $faq): ?>
            <article class="<?= $index < count($faqs) - 1 ? 'border-b border-flyto-ink/10' : '' ?> px-6 py-7 md:px-8">
                <h2 class="font-display text-[18.4px] font-medium leading-[25.3px] text-flyto-ink">
                    <?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
                <p class="mt-4 text-sm leading-[22.75px] text-flyto-muted">
                    <?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8') ?>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
