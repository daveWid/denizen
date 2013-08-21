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

$app->get('/users/:id', function($id) use ($app){
	$app->controller('\\Denizen\\Controller\\Users')->execute('getOne', $id);
});

$app->post('/users', function() use ($app){
	$app->controller('\\Denizen\\Controller\\Users')->execute('create');
});

$app->put('/users/:id', function($id) use ($app){
	$app->controller('\\Denizen\\Controller\\Users')->execute('update', $id);
});

$app->delete('/users/:id', function($id) use ($app){
	$app->controller('\\Denizen\\Controller\\Users')->execute('delete', $id);
});