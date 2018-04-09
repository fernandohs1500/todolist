<?php
use model\TokenModel;
use Symfony\Component\HttpFoundation\JsonResponse;

$token = $app['controllers_factory'];

$token->get('/generate-token/{userId}', function ($userId) use ($app) {

    try{

        //Checando para ver se já nao existe o token para este usuário
        $queryBuilder = $app['db']->createQueryBuilder();

        $result = $queryBuilder
            ->select('count(1) as qtd')
            ->from('usuario', 'u')
            ->where("u.id = '{$userId}'")
            ->andWhere('u.token is not null');

        $post = $app['db']->fetchAssoc($result->getSql());

        if ($post['qtd'] == 1) {
            return new JsonResponse(array('msg' => 'Usuario ja tem token gerado', 'success' => 0 ));
        } else {

            $tokenModel = new TokenModel($app);
            $affected = $tokenModel->gerarToken($userId);

            if($affected == 1){
                return new JsonResponse(array('msg' => 'Token Gerado', 'success' => 1));
            }
        }

    }catch (Exception $e) {
        return new JsonResponse(array('msg' => "Erro ao gerar token {$e->getMessage()}", 'success' => 0));
    }

});


$token->get('/update-token/{userId}', function ($userId) use ($app) {

    try{

        //Checando para ver se já nao existe o token para este usuário
        $queryBuilder = $app['db']->createQueryBuilder();

        $result = $queryBuilder
            ->select('count(1) as qtd')
            ->from('usuario', 'u')
            ->where("u.id = '{$userId}'")
            ->andWhere('u.token is not null');

        $post = $app['db']->fetchAssoc($result->getSql());

        if ($post['qtd'] == 1) {

            $tokenModel = new TokenModel($app);
            $affected = $tokenModel->gerarToken($userId);

             if($affected == 1){
                 return new JsonResponse(array('msg' => 'Token Atualizado com sucesso!','success' => 1));
             }

        } else {
            return new JsonResponse(array('msg' => 'Usuário não tem Token Gerado', 'success' => 0 ));
        }

    }catch (Exception $e) {
        return new JsonResponse(array('msg' => "Erro ao gerar token {$e->getMessage()}", 'success' => 0));
    }

});

$token->get('/token/{userId}', function ($userId) use ($app) {

    $tokenModel = new TokenModel($app);
    $retorno = $tokenModel->getToken($userId);

    return new JsonResponse(array('data' => $retorno, 'success' => 1));

});

