<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => array(
                __DIR__ . '/../templates/',
                __DIR__ . '/home/templates/',
                __DIR__ . '/usuario/templates/',
                __DIR__ . '/produto/templates/',
                __DIR__ . '/cadastro/templates/',
                __DIR__ . '/setor/templates/',
                __DIR__ . '/auth/templates/',
                __DIR__ . '/material/templates/',
                __DIR__ . '/relatorios/templates'
            ),
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
