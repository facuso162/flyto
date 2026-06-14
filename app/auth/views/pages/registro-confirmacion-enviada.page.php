<?php

require_once __DIR__ . '/../components/auth-ui.php';

$homeUrl = flytoAuthUrl($basePath ?? '', '/');
$actions = '<a href="' . $homeUrl . '" class="flex h-11 items-center justify-center bg-flyto-navy px-6 text-sm font-medium text-flyto-sand">Ir a la p&aacute;gina principal</a>';

flytoStatusCard(
    '<path d="M4 6H20V18H4V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M4 7L12 13L20 7" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>',
    'Verificaci&oacute;n pendiente',
    'Revis&aacute; tu correo electr&oacute;nico',
    'Te enviamos un enlace de confirmaci&oacute;n. Hac&eacute; clic en el link del correo para activar tu cuenta. Si no lo encontr&aacute;s, revis&aacute; la carpeta de spam.',
    $actions
);
