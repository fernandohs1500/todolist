<?php

namespace model;

use Symfony\Component\Config\Definition\Exception\Exception;

class TodoListModel
{

    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getListByUser($user)
    {
        try {
            if (empty($user)) {
                return false;
            }

            $sql = $this->app['db']->createQueryBuilder()
                ->select('*')
                ->from('todo_list', 'td')
                ->where("td.id_user = '{$user}'");
            $listToDo = $this->app['db']->fetchAll($sql->getSql());

            return $listToDo;

        } catch (Exception $e) {
            throw new \Exception( "Error when try to find list of task");
        }
    }

    public function saveTask(array $task)
    {
        try {
            $queryBuilder = $this->app['db']->createQueryBuilder();

            if (!empty($task) && !empty($task['content'])) {

                if (!empty($task['uuid'])) { //update

                    $queryBuilder
                        ->update('todo_list')
                        ->set('type', "{$task['type']}")
                        ->set('content', "'{$task['content']}'")
                        ->set('done', "{$task['done']}")
                        ->where("uuid = '{$task['uuid']}'");

                } else { //Insert

                    $queryBuilder
                        ->insert('todo_list')
                        ->values(
                            array(
                                'type' => "{$task['type']}",
                                'content' => "'{$task['content']}'",
                                'sort_order' => "{$task['sort_order']}",
                                'done' => "{$task['done']}",
                                'id_user' => "{$task['id_user']}"
                            )
                        );
                }

            } else {
               return array();
            }

            $prepare = $this->app['db']->prepare($queryBuilder->getSql());
            $prepare->execute();

            return $this->app['db']->lastInsertId();

        } catch (Exception $e) {
            throw new \Exception( "Error when try to save task");
        }
    }

    public function getTaskByUser($user, $task)
    {
        try {
            if (empty($user) || empty($task)) {
                return array();
            }

            $sql = $this->app['db']->createQueryBuilder()
                ->select('*')
                ->from('todo_list', 'td')
                ->where("td.uuid = '{$task}' AND id_user = '{$user}'");

            $listToDo = $this->app['db']->fetchAssoc($sql->getSql());

            return $listToDo;

        } catch (Exception $e) {
            throw new \Exception( "Error when try to find the task");
        }
    }

    public function deleteTask($user, $task)
    {
        try {

            if (!empty($task)) {

                $queryBuilder = $this->app['db']->createQueryBuilder()
                    ->delete('todo_list')
                    ->where("uuid = {$task} and id_user = {$user}");

                $prepare = $this->app['db']->prepare($queryBuilder->getSql());

                $prepare->execute();

            }

        } catch (Exception $e) {
            throw new \Exception( "Error when try to save task");
        }
    }

    public function prioritizeTask(array $task, $user)
    {
        try {

            if (!empty($task) && !empty($task['sort_order'])) {

                if (!empty($task['uuid'])) { //update

                    //Reorder Tasks
                    $taskOrder = array();
                    $lstTasks = $this->getListByUser($user);

                    foreach ($lstTasks as $taskUp) {
                        if($taskUp["uuid"] != $task['uuid']) {
                            $taskOrder[$taskUp['sort_order']] = $taskUp["uuid"];
                        }
                    }

                    $array1 = array_slice($taskOrder, 0, $task['sort_order'] -1);
                    $array2 = array_slice($taskOrder, $task['sort_order'] -1);
                    array_push($array1, $task['uuid']);
                    $resultOrder = array_merge($array1, $array2);

                    //End reorder

                    foreach($resultOrder as $key => $uuid) {

                        $queryBuilder = $this->app['db']->createQueryBuilder();

                        $queryBuilder
                            ->update('todo_list')
                            ->set('sort_order', $key+1)
                            ->where("uuid = '{$uuid}'");

                        $prepare = $this->app['db']->prepare($queryBuilder->getSql());
                        $prepare->execute();
                    }
                }

            }

            return $this->app['db']->lastInsertId();

        } catch (Exception $e) {
            throw new \Exception( "Error when try to save task");
        }
    }
}
