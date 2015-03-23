<?php

/**
 * Class     DefaultPlugin
 * 默认插件
 *
 * @author   yangyang3
 */
class DefaultPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        /* 获取base_uri */
        $base_uri = $request->getBaseUri();
        // var_dump($request);
        // var_dump($request ->getModuleName());
        // var_dump($request ->getControllerName());
        // var_dump($request ->getActionName());
        // die;
        /* 获取request_uri */
        $request_uri = $request->getRequestUri();

        /* 删除base_uri*/
        if ($base_uri !== '') {
            $request_uri = substr($request_uri, strlen($base_uri));
        }

        /* 删除uri左右的'/'字符 */
        $request_uri = trim($request_uri, '/');

        /* 初始化查询列表长度 */
        $length = 0;

        /* 初始化查询列表 */
        $query_list = array();

        /* 初始化请求后缀 */
        $request_suffix = '';

        if ($request_uri !== '') {
            /* 分割查询列表 */
            $query_list = explode('/', $request_uri);

            /* 获取查询列表长度 */
            $length = count($query_list);
        }

        /* 验证查询列表长度 */
        if ($length > 0) {
            /* 取得最后一个字符串 */
            $last_string = $query_list[$length - 1];

            /* 获取字符"."位置 */
            $dot_locate = strpos($last_string, '.');

            /* 验证"."位置 */
            if ($dot_locate !== false) {
                /* 截取"."之后的字符 */
                $request_suffix = substr($last_string, $dot_locate + 1);

                /* 截取"."之前的字符 */
                $query_list[$length - 1] = substr($last_string, 0, $dot_locate);
            }
        }


        /* 验证查询列表长度 */
        if ($length === 1) {
            /* 从配置文件中获取默认controller/action */
            $request->setModuleName(underline_to_camel(ucfirst($query_list[0])));
            $request->setControllerName(Yaf_Registry::get('config')->application->dispatcher->defaultController);
            $request->setActionName(Yaf_Registry::get('config')->application->dispatcher->defaultAction);
        } elseif ($length === 2) {
            /* 从配置文件中获取action */
            $request->setModuleName(underline_to_camel(ucfirst($query_list[0])));
            $request->setControllerName(underline_to_camel(ucfirst($query_list[1])));
            $request->setActionName(Yaf_Registry::get('config')->application->dispatcher->defaultAction);
        } elseif ($length >= 3) {
            /* 从url中获取 */
            $request->setModuleName(ucfirst($query_list[0]));
            $request->setControllerName(underline_to_camel(ucfirst($query_list[1])));
            $request->setActionName(underline_to_camel($query_list[2]));
        }

        //考虑有参数的情况
        //  else {
        //     /* 从配置文件中获取默认module/controller/action */
        //     $request->setModuleName(Yaf_Registry::get('config')->application->dispatcher->defaultModule);
        //     $request->setControllerName(Yaf_Registry::get('config')->application->dispatcher->defaultController);
        //     $request->setActionName(Yaf_Registry::get('config')->application->dispatcher->defaultAction);
        // }

        $request->setRequestUri($request_uri);
        // var_dump($request);die;
        
        /* 保存请求地址 */
        Yaf_Registry::set('request_uri', $request_uri);

        /* 保存请求后缀 */
        Yaf_Registry::set('request_suffix', $request_suffix);
    }

    public function dispatchLoopStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $module_name = $request->getModuleName();

        import_service_by_module_name($module_name);

        Yaf_Registry::set('module_name', $module_name);
    }

    public function preDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function postDispatch(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
    }

}