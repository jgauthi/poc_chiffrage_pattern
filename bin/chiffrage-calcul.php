#!/usr/bin/env php
<?php
use Jgauthi\Tools\Chiffrage\Pattern as ChiffragePattern;

if (is_readable(__DIR__.'/../../../autoload.php')) {
    require_once __DIR__.'/../../../autoload.php';
} elseif (is_readable(__DIR__.'/../vendor/autoload.php')) {
    require_once __DIR__.'/../vendor/autoload.php';
} else {
    die('Autoloader not found');
}

if (PHP_SAPI === 'cli' && !empty($argv[1])) {
    $pattern = $argv[1];
} elseif (isset($_GET['c'])) {
    $pattern = $_GET['id'];
} else {
    die('empty args, add ?c=XXX');
}

$chiffrage = new ChiffragePattern;
$calcul = $chiffrage->calcul($pattern);

echo $calcul . "h\n";
