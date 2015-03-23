<?php

class AppController extends Yaf_Controller_Abstract {
    
    /**
     * index action
     */
    public function indexAction() {
        echo 'App index';
    }

    public function loginAction() {
        // $this ->getView() ->assign("dev", "asdas");
        echo "login";
    }
}





?>
