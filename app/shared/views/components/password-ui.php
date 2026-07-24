<?php

if (!function_exists('flytoPasswordToggleButton')) {
    function flytoPasswordToggleButton(string $inputId): void
    {
        ?>
        <button
            type="button"
            class="absolute bottom-0 right-0 flex h-[42px] w-11 items-center justify-center text-flyto-muted transition hover:text-flyto-navy focus:outline-none focus:ring-2 focus:ring-inset focus:ring-flyto-navy"
            data-password-toggle
            aria-controls="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8') ?>"
            aria-label="Mostrar contrase&ntilde;a"
            aria-pressed="false"
        >
            <svg data-password-eye-open class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.6"/>
            </svg>
            <svg data-password-eye-closed class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="m3 3 18 18M10.6 6.1c.5-.1.9-.1 1.4-.1 6 0 9.5 6 9.5 6a16.8 16.8 0 0 1-2.5 3.2M6.3 7.3A17.2 17.2 0 0 0 2.5 12s3.5 6 9.5 6c1.4 0 2.7-.3 3.8-.8M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <?php
    }
}

if (!function_exists('flytoPasswordRequirements')) {
    function flytoPasswordRequirements(): void
    {
        $requirements = [
            'length' => 'Entre 8 y 40 caracteres',
            'uppercase' => 'Al menos una letra may&uacute;scula',
            'lowercase' => 'Al menos una letra min&uacute;scula',
            'number' => 'Al menos un n&uacute;mero',
            'special' => 'Al menos un car&aacute;cter especial',
            'match' => 'Las contrase&ntilde;as coinciden',
        ];
        ?>
        <div data-password-requirements class="border border-flyto-ink/10 bg-flyto-mist/40 px-4 py-4">
            <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted">Requisitos de contrase&ntilde;a</p>
            <ul class="mt-3 grid gap-1.5">
                <?php foreach ($requirements as $key => $requirement): ?>
                    <li data-password-requirement="<?= $key ?>" data-fulfilled="false" class="password-requirement flex items-center gap-3 text-xs leading-4">
                        <span data-password-requirement-icon class="flex h-4 w-4 items-center justify-center rounded-full border text-[10px]" aria-hidden="true">&times;</span>
                        <span><?= $requirement ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
    }
}
