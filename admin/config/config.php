<?php


$path = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$basePath = dirname($path, 2);

require_once $basePath . '/config/database.php';

// Sesión para panel de administración
session_name('admin_session');
session_start();

/**
 * URL de la tienda
 *
 * Agregar / al final
 */
define('SITE_URL', 'http://localhost/tienda_online/');

/**
 * URL del panel de administración
 */
define('ADMIN_URL', SITE_URL . 'admin/');

/**
 * Clave o contraseña para cifrado.
 */
define("KEY_CIFRADO", "ABCD.1234-");

/**
 * Metodo de cifrado OpenSSL.
 *
 * https://www.php.net/manual/es/function.openssl-get-cipher-methods.php
 */
define("METODO_CIFRADO", "aes-128-cbc");
