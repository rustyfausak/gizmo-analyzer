<?php

$loader = require __DIR__ . '/vendor/autoload.php';

if (!isset($argv[1])) {
	die("Usage: " . basename(__FILE__) . " <replay json>\n");
}
$data = json_decode(file_get_contents($argv[1]));
if (!$data) {
	die("Could not decode json.\n");
}
$ra = new Gizmo\ReplayAnalyzer($data);
$ra->drawField();
