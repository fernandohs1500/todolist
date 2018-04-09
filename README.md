PROJECT 
==============
PHP (SILEX FRAMEWORK) + MYSQL + APACHE 

TODO PROJECT INSTRUCTIONS
==============

create a index.php file inside /web/

index.php:

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

    $ composer update


DATABASE RELATIONSHIP
----------------------------
![der](https://user-images.githubusercontent.com/1281429/38481143-ae9f9250-3b9f-11e8-8518-4a7daf9cdf38.jpg)

[todo_db.pdf](https://github.com/fernandohs1500/todolist/files/1888596/todo_db.pdf)

HOW TO TEST ?
----------------------------

https://www.getpostman.com/

SERVICES LIST
----------------------------

    GET  - http://todolist.com/guest/list-all
    GET  - http://todolist.com/guest/delete/{TASK_ID}
    POST - http://todolist.com/guest/save
    POST - http://todolist.com/guest/update
    POST - http://todolist.com/guest/prioritize

SCREENSHOTS
----------------------------


![captura de tela de 2018-04-09 01-14-14](https://user-images.githubusercontent.com/1281429/38481147-b7e01b50-3b9f-11e8-99b4-e27f8bdcaded.png)
![captura de tela de 2018-04-09 01-21-53](https://user-images.githubusercontent.com/1281429/38481148-b805e916-3b9f-11e8-9a0f-e1533b6f92a4.png)
![captura de tela de 2018-04-09 01-22-14](https://user-images.githubusercontent.com/1281429/38481149-b82d4bfa-3b9f-11e8-842f-56e0b8191c9f.png)
![captura de tela de 2018-04-09 01-23-14](https://user-images.githubusercontent.com/1281429/38481150-b8657dae-3b9f-11e8-9b14-c268e17d2538.png)
![captura de tela de 2018-04-09 01-24-04](https://user-images.githubusercontent.com/1281429/38481151-b8a4c018-3b9f-11e8-8e5a-b8530f2355cf.png)
![captura de tela de 2018-04-09 01-24-45](https://user-images.githubusercontent.com/1281429/38481152-b8caef9a-3b9f-11e8-947c-1b914f14c63a.png)
![captura de tela de 2018-04-09 01-25-55](https://user-images.githubusercontent.com/1281429/38481153-b94125de-3b9f-11e8-9be4-1f038e24ec0b.png)
![captura de tela de 2018-04-09 01-26-50](https://user-images.githubusercontent.com/1281429/38481154-b9bdebbe-3b9f-11e8-885b-fc304e4dc78f.png)
![captura de tela de 2018-04-09 01-31-23](https://user-images.githubusercontent.com/1281429/38481155-b9e3247e-3b9f-11e8-803b-ceea99ea2361.png)
![captura de tela de 2018-04-09 01-37-01](https://user-images.githubusercontent.com/1281429/38481156-ba525a60-3b9f-11e8-928e-6c3fae6372f2.png)
![captura de tela de 2018-04-09 01-37-20](https://user-images.githubusercontent.com/1281429/38481158-bac47c12-3b9f-11e8-8676-95b8040e360f.png)
![captura de tela de 2018-04-09 01-51-41](https://user-images.githubusercontent.com/1281429/38481159-baeb8028-3b9f-11e8-8f3f-fe17c5e14c0c.png)
![captura de tela de 2018-04-09 01-52-24](https://user-images.githubusercontent.com/1281429/38481160-bb5debf4-3b9f-11e8-9e32-ac2d969a015e.png)
![captura de tela de 2018-04-09 01-52-38](https://user-images.githubusercontent.com/1281429/38481161-bb895ab4-3b9f-11e8-8d50-508e0cb80ce7.png)


META
----------------------------

Fernando Henrique – [@fernandohs1500](https://www.linkedin.com/in/fernandohs1500/) – fernandimgts@gmail.com

Distribuído sob a licença MIT.

[https://github.com/fernandohs1500/](https://github.com/fernandohs1500/)