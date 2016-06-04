<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/generated-conf/config.php';

date_default_timezone_set('America/Chicago');

if (!isset($argv[1])) {
	die("Usage: " . basename(__FILE__) . " <replay json>\n");
}
$a = new \Gizmo\Analyzer();
$a->analyze($argv[1]);
