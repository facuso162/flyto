<?php

$basePath = $basePath ?? '';
$promocionId = isset($promocionId) ? (int) $promocionId : 0;
$e = static fn (string $valor): string => htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');

?>
<section class="px-5 py-8 sm:px-8">
    <div class="mx-auto max-w-[768px]">
        <p class="font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Promociones</p>
        <h1 class="mt-1 font-display text-3xl font-medium tracking-tight">Solicitar activación</h1>

        <form method="post" action="<?= $e($basePath) ?>/ceo/promociones/solicitar-activacion" class="mt-6" novalidate>
            <input type="hidden" name="id" value="<?= $promocionId ?>">

            <div class="border border-flyto-ink/10 bg-white shadow-flyto">
                <div class="border-b border-flyto-ink/10 px-6 py-4 font-mono text-xs uppercase tracking-[0.1em] text-flyto-muted">Datos de la solicitud</div>

                <div class="p-6">
                    <label class="block">
                        <span class="font-mono text-[10px] font-medium uppercase tracking-[0.1em] text-flyto-muted">Fecha de fin de la promoción</span>
                        <input type="date" name="fecha_fin" min="<?= (new DateTimeImmutable('tomorrow'))->format('Y-m-d') ?>" required class="mt-1.5 h-[42px] w-full border border-flyto-ink/15 bg-white px-3 text-sm outline-none transition focus:border-flyto-navy focus:ring-1 focus:ring-flyto-navy">
                    </label>
                </div>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="<?= $e($basePath) ?>/ceo/promociones" class="flex h-[42px] items-center justify-center border border-flyto-ink/15 px-6 text-sm font-medium transition hover:border-flyto-navy hover:text-flyto-navy">Cancelar</a>
                <button type="submit" class="flex h-[42px] items-center justify-center bg-flyto-navy px-6 text-sm font-medium text-flyto-sand transition hover:bg-flyto-ink">Realizar solicitud</button>
            </div>
        </form>
    </div>
</section>
