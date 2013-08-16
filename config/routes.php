<?php

/** =================================
    $app is the application variable
    ================================= **/

$app->post('/oauth/token', function() use ($app){
	$app->controller('\Denizen\Controller\OAuth2')->execute('token');
});
