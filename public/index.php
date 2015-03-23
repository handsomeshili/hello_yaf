<?php

//项目入口文件

date_default_timezone_set('PRC');


define("ROOT_PATH", realpath(dirname(__FILE__) . '/../')); //point to the up path
define("APPLICATION_PATH", realpath(ROOT_PATH . '/application/'));
define("HOST_NAME", "http://yaf_host.com"); 
// $router = Yaf_Dispatcher::getInstance()->getRouter();
// var_dump($router);
$app = new Yaf_Application(ROOT_PATH . "/conf/application.ini");
// var_dump($app);
$app->bootstrap()->run();





?>
