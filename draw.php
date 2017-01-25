<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/generated-conf/config.php';

use \Gizmo\Space\Box;
use \Gizmo\Space\Rotation;
use \Gizmo\Space\Vector;
use \NumPHP\Core\NumArray;
use \Gizmo\Space\ImageXYZ;
use \Gizmo\Space\Rect;

$i = new ImageXYZ(800, 10000);
$i->output('draw.png');
