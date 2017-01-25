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
$i->output('asdf.png');

/*
$anim = new \GifCreator\AnimGif();

$field = Rect::axisAligned(-200, 200, -200, 200);

// Create car and do rotation
$b = new Box(120, 80, 30);
$b->update(new Vector(0, 0, 0), new Rotation(0, 0, 20));

// Generate top view
$i = new Image(400, $field);
$car_color = $i->createColor(255, 200, 0);
$car_color2 = $i->createColor(100, 76, 0);
$car_color3 = $i->createColor(100, 100, 100);
$text_color = $i->createColor(0, 255, 255);
$i->drawText('Top', 0, 0, $text_color);
list($r1, $r2) = $b->viewRectsAlongAxis('z');
draw_car($i, $r1, $r2, $car_color, $car_color2, $car_color3);
$i->output('view_top.png');

// Generate side view
$i = new Image(400, $field);
$i->drawText('Side', 0, 0, $text_color);
list($r1, $r2) = $b->viewRectsAlongAxis('y');
draw_car($i, $r1, $r2, $car_color, $car_color2, $car_color3);
$i->output('view_side.png');

// Generate front view
$i = new Image(400, $field);
$i->drawText('Front', 0, 0, $text_color);
list($r1, $r2) = $b->viewRectsAlongAxis('x');
draw_car($i, $r1, $r2, $car_color, $car_color2, $car_color3);
$i->output('view_front.png');

function draw_car($i, $r1, $r2, $color1, $color2, $color3)
{
	$i->drawRect($r2, $color2);
	$i->drawLine($r1->p0, $r2->p0, $color3);
	$i->drawLine($r1->p1, $r2->p1, $color3);
	$i->drawLine($r1->p2, $r2->p2, $color3);
	$i->drawLine($r1->p3, $r2->p3, $color3);
	$i->drawRect($r1, $color1);
}
*/

/*
$b->update(new Vector(1, 2, 3));
print_r($b);
exit;

$vector = new Vector(1, 0, 0);
$rotation = new Rotation(90, 0, 0);
print $vector . "\n";
$vector->orient($rotation);
//$vector->yaw(90);
print $vector . "\n";

*/
