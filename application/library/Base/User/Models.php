<?php
/**
 * class Base_User_Models
 * User模型基类
 *
 */
class Base_User_Models extends Base_Models {

    /**
     * Method __construct
     * 构造方法
     *
     * @param string $model_name
     * @param string $table_name
     *
     * @author sily
     */
    public function __construct($model_name = NULL, $table_name = NULL) {
        parent::__construct($model_name, $table_name);
    }
}

?>