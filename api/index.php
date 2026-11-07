<?php

/**
 * Entry point untuk Vercel Serverless Function.
 *
 * Vercel hanya mengizinkan penulisan file di direktori /tmp, sedangkan
 * direktori aplikasi bersifat read-only. Karena itu semua path yang
 * biasanya ditulis Laravel (view cache, log, cache framework) diarahkan
 * ke /tmp.
 */

// Pastikan environment variable dari Vercel terbaca oleh Laravel.
foreach (getenv() as $key => $value) {
    if (! array_key_exists($key, $_ENV)) {
        $_ENV[$key] = $value;
    }
    if (! array_key_exists($key, $_SERVER)) {
        $_SERVER[$key] = $value;
    }
}

// Siapkan direktori tulis di /tmp.
$writableDirs = [
    '/tmp/views',
    '/tmp/cache',
    '/tmp/sessions',
    '/tmp/logs',
    '/tmp/fonts',
    '/tmp/storage/app/public',
];

foreach ($writableDirs as $dir) {
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Default konfigurasi yang aman untuk lingkungan serverless.
// Nilai dari dashboard Vercel tetap menang jika sudah diisi.
$defaults = [
    'VIEW_COMPILED_PATH'  => '/tmp/views',
    'APP_CONFIG_CACHE'    => '/tmp/config.php',
    'APP_EVENTS_CACHE'    => '/tmp/events.php',
    'APP_PACKAGES_CACHE'  => '/tmp/packages.php',
    'APP_ROUTES_CACHE'    => '/tmp/routes.php',
    'APP_SERVICES_CACHE'  => '/tmp/services.php',
    'LOG_CHANNEL'         => 'stderr',
    'LOG_DEPRECATIONS_CHANNEL' => 'null',
];

foreach ($defaults as $key => $value) {
    if (empty($_ENV[$key])) {
        $_ENV[$key] = $value;
    }
    if (empty($_SERVER[$key])) {
        $_SERVER[$key] = $value;
    }
}

// CA certificate untuk MySQL cloud yang mewajibkan TLS (mis. TiDB Cloud).
$mysqlCa = realpath(__DIR__ . '/../storage/certs/isrgrootx1.pem');
if ($mysqlCa && empty($_ENV['MYSQL_ATTR_SSL_CA'])) {
    $_ENV['MYSQL_ATTR_SSL_CA'] = $mysqlCa;
    $_SERVER['MYSQL_ATTR_SSL_CA'] = $mysqlCa;
}

require __DIR__ . '/../public/index.php';
