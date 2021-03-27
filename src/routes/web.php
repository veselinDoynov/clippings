<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->get('/', function () use ($router) {
    return "Clippings API!";
});

$router->group(['prefix' => 'documents'], function (Router $router) {
    $router->post('/calculation', 'Document@calculation');
});



