<?php
/**
 * class User_InfoModel
 * 用户信息模型
 * @author sily
 */

class User_InfoModel extends Base_User_Models {

    /**
     * Method __construct
     * 构造方法
     *
     * @author sily
     */
    public function __construct($module_name = null, $table_name = null) {
        //codes..
        parent::__construct($module_name, $table_name);
    }


    /**
     * Method getUserInfo
     * 获取用户基本数据
     * @param string $where 获取数据的where条件
     * @return array $select
     * 
     * @author sily
     */
    public function getUserInfo($where = null) {
        $order = 'id';

        //注意两个where的区别,一个是Base_Model类定义的，另一个是Zend_Db_Select自带的
        // $select = $this->select()->where($where, $condition)->order($order . ' ' . 'DESC');

        //获取Zend_Db_Select 实例
        if ($where !== null) {
            $select = $this->select()->where($where)->order($order . ' ' . 'ASC');
        } else {
            $select = $this->select()->order($order . ' ' . 'ASC');
        }
        
        //设置where条件
        $data = $this->fetchAll($select);
        return $data;
    }   


    /**
     * Method addUserInfo
     * 添加用户信息
     *
     * @param array $data
     * @return bool | int
     *
     * @author sily
     */
    public function addUserInfo($data = array()) {
        // $insert = $this->select();
        if (empty($data)) {
            return false;
        }

        $result = $this->insert($data);

        if ($result !== false) {
            //返回新插入的行id
            return $result;
        } else {
            return false;
        }
        
    }

    /**
     * Method updateUserInfo
     * 更新user信息
     *
     * @param array $data
     * @return bool | int
     *
     * @author sily
     */
    public function updateUserInfo($data) {
        //更新用户信息
        if (empty($data)) {
            return false;
        }
        $where = "id = " . $data['id'];
        return $this->update($data, $where);
    }

    /**
     * Method deleteUserInfo
     * @param int $id  要删除的user的id 
     * @return $result 返回影响的行数
     * 
     * @author sily
     */
    public function deleteUserInfo($id = 0) {
        //codes.....
        $where = array(
                'id = ?' =>$id
            );
        $this->where($where);
        $result = $this->delete();
        return $result;
    }


}




?>