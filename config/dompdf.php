<?php

return [
    'mode'                  => 'utf-8',
    'format'                => 'A4',
    'default_font'          => 'sans-serif',
    'margin_left'           => 10,
    'margin_right'          => 10,
    'margin_top'            => 10,
    'margin_bottom'         => 10,
    'margin_header'         => 0,
    'margin_footer'         => 0,
    'orientation'           => 'P',
    'contentSecurityPolicy' => false, // Disable CSP untuk PDF loading
    'logOutputFile' => null,
    'tempDir'       => sys_get_temp_dir(),
    'chroot'        => realpath(base_path()),
    'allowedProtocols' => [
        'file://',
        'http://',
        'https://',
    ],
    'allowedRemoteHosts' => [
        'localhost',
        '127.0.0.1',
        $_SERVER['HTTP_HOST'] ?? 'localhost',
    ],
    'isPhpEnabled'  => true,
    'isRemoteEnabled' => true, // Enable untuk local resources
    'isJavascriptEnabled' => false,
    'isHtmlEnabled' => true,
    'isFontSubsettingEnabled' => false,
    'debugPdf' => false,
    'debugKeepTemp' => false,
    'debugCss' => false,
    'debugLayout' => false,
    'debugLayoutLines' => false,
    'debugLayoutBlocks' => false,
    'debugLayoutInline' => false,
    'debugLayoutPaddingBox' => false,
    'publicPath' => env('APP_URL'),
];
