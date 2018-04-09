<?php
namespace model;

class TokenModel
{
    private $app;

    public function __construct($app){
        $this->app = $app;
    }

    public function generateToken($userId) {

        $queryBuilder = $this->app['db']->createQueryBuilder();

        //Gerando token para o usuário
        $token = hash('sha512', microtime()) . '-' . $userId;

        $queryBuilder
            ->update('user', 'u')
            ->set('u.token', "'{$token}'")
            ->set('u.dat_token_expiration	', "'" .date('Y-m-d H:i:s', strtotime("+7 days")) . "'" )
            ->where("u.id = '{$userId}' and u.active = 1");

        $prepare = $this->app['db']->prepare($queryBuilder->getSql());

        return $prepare->execute();
    }

    public function checkIfTokenIsValid($token) {

        $queryBuilder = $this->app['db']->createQueryBuilder();

        $result = $queryBuilder
            ->select('count(1) as qtd')
            ->from('user', 'u')
            ->where("u.token = '{$token}'")
            ->andWhere("dat_token_expiration > NOW()")
            ->andWhere("active = 1");

        $post = $this->app['db']->fetchAssoc($result->getSql());

        return $post['qtd'];

    }

    public function getToken($codUser) {

        $queryBuilder = $this->app['db']->createQueryBuilder();

        $result = $queryBuilder
            ->select('u.token', 'dat_token_expiration as expiration')
            ->from('user', 'u')
            ->where("u.id = '{$codUser}'");

        $result = $this->app['db']->fetchAssoc($result->getSql());

        return $result;

    }
}