<?php

/** =================================
    $app is the application variable
    ================================= **/

/** OAuth **/

$app->post('/oauth/token', function() use ($app){
	$app->controller('\\Denizen\\Controller\\OAuth2')->execute('token');
});

/** Users **/

$app->get('/users', function() use ($app){
	$app->controller('\\Denizen\\Controller\\Users')->execute('getAll');
});