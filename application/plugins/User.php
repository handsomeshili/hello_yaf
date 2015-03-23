<?php

/**
 * Class User Plugin
 * 用户自定义插件例子
 * 
 * @author sily
 */

class UserPlugin extends Yaf_Plugin_Abstract {

    /**
     * Method RouterStartup
     * 路由开始之前的操作 可以接受俩个参数
     *
     * @param Yaf_Request_Abstract $requset
     * @param Yaf_Response_Abstract $reponse
     *
     * @author sily
     */
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $reponse) {
        //codes....
        // echo "routerStartup";
    }

    /**
     * Method routerShutdown
     * 路由结束之后触发，但是如果路由处理不正确，此事件就不会被触发。可以接受俩个参数
     *
     * @param Yaf_Requset_Abstract $request
     * @param Yaf_Response_Abstract $response
     *
     * @author sily
     */
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $reponse) {
        //codes.....
        // echo "routerShutdown";
    }
}


?>