<?php
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use model\TodoListModel;

$todoListController = $app['controllers_factory'];

$todoListController->get('/guest/list-all', function (Request $request) use ($app)
{
    try {

        $model = new TodoListModel($app);

        $list = $model->getListByUser(GUEST_USER);

        if (empty($list)) {
            return new JsonResponse(
                array('msg' => 'Wow. You have nothing else to do. Enjoy the rest of your day!'));
        }

        return new JsonResponse(array('data' => $list));

    } catch (Exception $e) {
        return new JsonResponse(array('msg' => "Error - Contact administrator", 'success' => 0));
    }

});


$todoListController->post('/guest/save', function (Request $request) use ($app)
{
    try {
        $data = $request->request->all();

        if ($data['type'] != TYPE_SHOPPING && $data['type'] != TYPE_WORK) {
            return new JsonResponse(
                array('msg' => 'The task type you provided is not supported. You can only use shopping or work.',
                    'success' => 0)
            );
        }
        
        $task = array(
            'type' => $data['type'],
            'content' => $data['content'],
            'sort_order' => $data['sort_order'],
            'done' => 0,
            'id_user' => GUEST_USER
        );

        $model = new TodoListModel($app);
        $task = $model->saveTask($task);

        if(!empty($task)) {
            return new JsonResponse(array('msg' => 'OK', 'data' => $task, 'success' => 1));
        }else {
            return new JsonResponse(
                array('msg' => 'Bad move! Try removing the task instead of deleting its content.',
                    'success' => 0)
            );
        }

    } catch (Exception $e) {
        return new JsonResponse(array('msg' => "Error - Contact administrator", 'success' => 0));
    }

});

$todoListController->post('/guest/update', function (Request $request) use ($app)
{

    try {
        $data = $request->request->all();

        $model = new TodoListModel($app);

        $task = $model->getTaskByUser(GUEST_USER, $data['uuid']);

        if (empty($task)) {
            return new JsonResponse(
                array('msg' => 'Are you a hacker or something? The task you were trying to edit doesn\'t exist.',
                    'success' => 0)
            );
        }

        if($data['type'] != TYPE_SHOPPING && $data['type'] != TYPE_WORK) {
            return new JsonResponse(
                array('msg' => 'The task type you provided is not supported. You can only use shopping or work.',
                    'success' => 0)
            );
        }

        $task = array(
            'uuid' => $data['uuid'],
            'type' => $data['type'],
            'content' => $data['content'],
            'done' => $data['done'],
            'id_user' => GUEST_USER
        );

        $task = $model->saveTask($task);

        return new JsonResponse(array('msg' => 'OK', 'success' => 1));

    } catch (Exception $e) {
        return new JsonResponse(array('msg' => "Error - Contact administrator", 'success' => 0));
    }

});

$todoListController->post('/guest/prioritize', function (Request $request) use ($app)
{
    try {
        $data = $request->request->all();

        $model = new TodoListModel($app);

        $task = $model->getTaskByUser(GUEST_USER, $data['uuid']);

        if (empty($task)) {
            return new JsonResponse(
                array('msg' => 'Are you a hacker or something? The task you were trying to edit doesn\'t exist.',
                    'success' => 0)
            );
        }

        $task = array(
            'uuid' => $data['uuid'],
            'sort_order' => $data['sort_order'],
            'id_user' => GUEST_USER
        );

        $model->prioritizeTask($task, GUEST_USER);

        return new JsonResponse(array('msg' => 'OK', 'data' => $task, 'success' => 1));

    } catch (Exception $e) {
        return new JsonResponse(array('msg' => "Error - Contact administrator", 'success' => 0));
    }

});

$todoListController->get('/guest/delete/{task_id}', function ($task_id) use ($app)
{
    try {

        $model = new TodoListModel($app);

        $task = $model->getTaskByUser(GUEST_USER, $task_id);

        if (empty($task)) {
            return new JsonResponse(
                array('msg' => 'Good news! The task you were trying to delete didn\'t even exist..',
                    'success' => 0)
            );
        }

        $model->deleteTask(GUEST_USER, $task_id);

        return new JsonResponse(
            array('msg' => 'Task Deleted!',
                'success' => 1)
        );

    } catch (Exception $e) {
        return new JsonResponse(array('msg' => "Error - Contact administrator", 'success' => 0));
    }
});
