<?php

/**
 * class Bootstrap
 * 它是Yaf提供的一个全局配置的入口, 在Bootstrap中, 你可以做很多全局自定义的工作.
 *
 * @author sily
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    /**
     * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
     * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
     * 调用的次序, 和申明的次序相同
     */
    
    public function _initconfig(Yaf_Dispatcher $dispatcher) {
        $config = Yaf_Application::app()->getconfig();
        Yaf_Registry::set('config', $config);

        // $dispatcher->disableView();
    }

    // public function initDefinePath(Yaf_Dispatcher $dispatcher) {
    //     define(HOST_NAME, "http://yaf_host.com");
    // }



    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setDefaultModule('Index')->setDefaultController('Index')->setDefaultAction('index');
    }

    /**
     * Method _initIncludePath
     * 导入library类库
     * @author sily
     */
    public function _initIncludePath(Yaf_Dispatcher $dispatcher) {
        set_include_path(get_include_path() . PATH_SEPARATOR . Yaf_Registry::get('config')->application->library);
    }



    /**
     * Method _initFunctions
     * 注册导入公共函数库
     *
     */
    public function _initFunctions(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import(Yaf_Registry::get('config')->application->functions . '/Common.php');
    }


    /**
     * 注册一个插件
     */
    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        // codes.....
        
        if (!$dispatcher->getRequest()->iscli()) {
            //
            $default_plugin = new DefaultPlugin();
            $dispatcher->registerPlugin($default_plugin);
        } else {
            $user_plugin = new UserPlugin();
            $dispatcher->registerPlugin($user_plugin);
        }

        // var_dump($dispatcher->getRequest());

    }

    /**
     * 添加配置中的路由
     */
    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        //codes....
    }

    public function _initLocalNamespace(Yaf_Dispatcher $dispatcher) {
        $namespace = array(
            'AdUC',
            'Base',
            'Type',
            'Zend',
            'Db',
        );
        Yaf_Loader::getInstance()->registerLocalNamespace($namespace);
    }
    
}






?>