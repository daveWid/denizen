<?php

$base = dirname(__DIR__).DIRECTORY_SEPARATOR;

require $base.'vendor/autoload.php';

$app = new \Denizen\Application(array(
	'db' => parse_ini_file($base.'config/database.ini')
));

$app->add(new \Slim\Middleware\ContentTypes);
$app->add(new \Denizen\Middleware\TransformBody);

require $base.'/config/routes.php';

$app->run();
