<?php

require_once __DIR__ . '/../../../shared/views/components/password-ui.php';

if (!function_exists('flytoAuthUrl')) {
    function flytoAuthUrl(string $basePath, string $path): string
    {
        return htmlspecialchars(rtrim($basePath, '/') . $path, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('flytoAuthField')) {
    function flytoAuthField(
        string $label,
        string $name,
        string $type = 'text',
        string $placeholder = '',
        string $autocomplete = '',
        string $extraAttributes = '',
        string $value = '',
        string $error = ''
    ): void {
        $fieldId = 'field-' . preg_replace('/[^a-z0-9_-]+/i', '-', $name);
        $rules = match ($name) {
            'nombre', 'apellido' => 'maxlength="80"',
            'email' => 'maxlength="120"',
            'telefono' => 'maxlength="15" pattern="[0-9]*" inputmode="numeric"',
            'password', 'password_confirmation' => 'minlength="8" maxlength="40"',
            'token' => 'minlength="6" maxlength="6" pattern="[0-9]{6}" inputmode="numeric"',
            default => '',
        };
        ?>
        <label class="block">
            <span class="font-mono text-[10.4px] font-medium uppercase tracking-[0.26px] text-flyto-muted"><?= $label ?><?= $name === 'telefono' ? ' (opcional)' : '' ?></span>
            <div class="<?= $type === 'password' ? 'relative' : '' ?>">
                <input
                    <?= $name !== 'telefono' ? 'required' : '' ?>
                    id="<?= $fieldId ?>"
                    name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                    type="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                    class="mt-1 h-[41.6px] w-full border border-flyto-ink/10 bg-white px-3 text-sm text-flyto-ink outline-none placeholder:text-flyto-muted/40 focus:border-flyto-navy <?= $type === 'password' ? 'pr-11' : '' ?>"
                    placeholder="<?= htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8') ?>"
                    <?php if ($type !== 'password' && $value !== ''): ?>value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                    <?php if ($autocomplete !== ''): ?>autocomplete="<?= htmlspecialchars($autocomplete, ENT_QUOTES, 'UTF-8') ?>"<?php endif; ?>
                    <?= $extraAttributes ?>
                    <?= $rules ?>
                >
                <?php if ($type === 'password'): ?>
                    <?php flytoPasswordToggleButton($fieldId); ?>
                <?php endif; ?>
            </div>
            <p id="error-<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" data-field-error="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" role="alert" aria-hidden="<?= $error === '' ? 'true' : 'false' ?>" class="form-field-error mt-1 text-xs leading-5 text-red-700"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
        </label>
        <?php
    }
}

if (!function_exists('flytoAuthShellStart')) {
    function flytoAuthShellStart(string $eyebrow, string $title, string $maxWidth = 'max-w-md'): void
    {
        ?>
        <section class="bg-flyto-sand px-6 py-16 md:flex md:min-h-[590px] md:items-center md:justify-center">
            <div class="w-full <?= htmlspecialchars($maxWidth, ENT_QUOTES, 'UTF-8') ?>">
                <p class="font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted"><?= $eyebrow ?></p>
                <h1 class="mt-2 font-display text-[28px] font-medium leading-[35px] text-flyto-ink"><?= $title ?></h1>
        <?php
    }
}

if (!function_exists('flytoAuthShellEnd')) {
    function flytoAuthShellEnd(): void
    {
        ?>
            </div>
        </section>
        <?php
    }
}

if (!function_exists('flytoBackIcon')) {
    function flytoBackIcon(): string
    {
        return '<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
    }
}

if (!function_exists('flytoStatusCard')) {
    function flytoStatusCard(
        string $iconPath,
        string $eyebrow,
        string $title,
        string $body,
        string $actions
    ): void {
        ?>
        <section class="bg-flyto-sand px-6 py-16 md:flex md:min-h-[590px] md:items-center md:justify-center">
            <div class="w-full max-w-md border border-flyto-ink/10 bg-white p-10 text-center">
                <span class="mx-auto flex h-12 w-12 items-center justify-center bg-flyto-navy/10 text-flyto-navy" aria-hidden="true">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                        <?= $iconPath ?>
                    </svg>
                </span>
                <p class="mt-6 font-mono text-xs uppercase tracking-[1.2px] text-flyto-muted"><?= $eyebrow ?></p>
                <h1 class="mt-2 font-display text-[25.6px] font-medium leading-8 text-flyto-ink"><?= $title ?></h1>
                <p class="mx-auto mt-4 max-w-[367px] text-sm leading-[22.75px] text-flyto-muted"><?= $body ?></p>
                <div class="mt-8"><?= $actions ?></div>
            </div>
        </section>
        <?php
    }
}
