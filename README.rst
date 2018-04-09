TODO PROJECT INSTRUCTIONS
==============

create a index.php file inside /web/

index.php:

.. code-block:: console

    ini_set('display_errors', 1);

    require_once __DIR__.'/../vendor/autoload.php';
    $app = require __DIR__.'/../src/app.php';

    $app->register(new Silex\Provider\DoctrineServiceProvider(), array(
        'db.options' => array (
                'driver'    => 'pdo_mysql',
                'host'      => 'localhost',
                'dbname'    => 'todo_db',
                'user'      => 'root',
                'password'  => '****',
                'charset'   => 'utf8'
        )
    ));

    require __DIR__.'/../src/controllers.php';

    $app->run();


Composer
----------------------------

Run the composer command

.. code-block:: console

    $ composer update