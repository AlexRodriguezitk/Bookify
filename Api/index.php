<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Database;
use App\Middlewares\Init;
use App\Middlewares\Auth;

$config = require __DIR__ . '/App/config/config.php';

// Conexión DB
$db = Database::getInstance();

// Init
$init = new Init($db, $config['is_installed']);
$init->register();


// ==================

require 'routes/public.php';
require 'routes/login.php';
require 'routes/test.php';
require 'routes/router.php';



// ==================
// Start
// ==================
Flight::start();
