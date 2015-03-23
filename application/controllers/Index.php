<?php

class IndexController extends Yaf_Controller_Abstract {
    
    /**
     * index action
     */
    public function indexAction() {
        $this ->getView() ->assign("content", "hello world!");
    }

    public function devAction() {
        // $this ->getView() ->assign("dev", "asdas");
        echo "dev";
    }
}





?>
