<?php

declare(strict_types=1);

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'admin' => [
        'path' => './assets/admin.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '6.7.0',
    ],
    '@fortawesome/fontawesome-free/css/all.css' => [
        'version' => '6.7.0',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '6.7.0',
        'type' => 'css',
    ],
    'trix' => [
        'version' => '2.1.8',
    ],
    'trix/dist/trix.min.css' => [
        'version' => '2.1.8',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'tom-select' => [
        'version' => '2.4.0',
    ],
    'tom-select/dist/css/tom-select.bootstrap5.css' => [
        'version' => '2.4.0',
        'type' => 'css',
    ],
    'clipboard' => [
        'version' => '2.0.11',
    ],
    'leaflet' => [
        'version' => '1.9.4',
    ],
    'leaflet/dist/leaflet.min.css' => [
        'version' => '1.9.4',
        'type' => 'css',
    ],
    '@orchidjs/sifter' => [
        'version' => '1.1.0',
    ],
    '@orchidjs/unicode-variants' => [
        'version' => '1.1.2',
    ],
];
