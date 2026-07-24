<?php

$basePath = $basePath ?? '';
$homeUrl = $basePath !== '' ? $basePath : '/';

?>
<section class="flex min-h-[590px] items-center justify-center px-4 py-16" aria-labelledby="not-found-title">
    <div class="w-full max-w-md text-center">
        <p class="font-display text-[clamp(7rem,20vw,10rem)] font-medium leading-none text-flyto-muted/15" aria-hidden="true">
            404
        </p>
        <p class="mt-6 font-mono text-xs uppercase leading-4 tracking-[0.1em] text-flyto-muted">
            Página no encontrada
        </p>
        <h1 id="not-found-title" class="mt-3 font-display text-[25.6px] font-medium leading-8 text-flyto-ink">
            Esta ruta no existe
        </h1>
        <p class="mt-4 text-sm leading-[22.75px] text-flyto-muted">
            La página que buscás no está disponible o fue movida. Volvé al inicio para continuar explorando.
        </p>
        <a
            href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8') ?>"
            class="mt-8 inline-flex h-11 items-center justify-center bg-flyto-navy px-7 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-flyto-navy"
        >
            Ir a la página principal
        </a>
    </div>
</section>
