<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use model\TokenModel;

$app->before(function (Request $request) use ($app) {

    //IF user is admin then check if token is valid
    if (!preg_match("/guest/", $request->get('_route'))) {
        $tokenModel = new TokenModel($app);
        $valid = $tokenModel->checkIfTokenIsValid($request->request->get('token'));

        if (!$valid) {
            return new JsonResponse(array('msg' => "Invalid Token", 'success' => 0));
        }
    }

});

$app->after(function (Request $request, Response $response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', '*');
});

include_once 'defines.php';

//Rotas de Token
include_once 'controller/TokenController.php';
$app->mount('/', $token);

include_once 'controller/TodoListController.php';
$app->mount('/', $todoListController);

$app->get('/', function() use($app) {
    die('index');
});

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    die('<h3>Invalid Rote</h3>');
});
