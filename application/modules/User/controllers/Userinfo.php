<?php

/**
 * class Userinfo controller
 * 用户信息控制器
 * 
 * @author sily
 */
class UserinfoController extends Yaf_Controller_Abstract{

    //User_InfoMode对象
    protected $_user_model = null;



    /**
     * method init 
     * 控制器初始化函数，必须并且第一被执行的函数
     *
     * @author sily
     */
    public function init() {
        //实例化一个user表的模型对象
        $this->_user_model = new User_InfoModel('User', 'user');
    }




    public function indexAction() {
        //codes.....
        echo "Userinfo";
    }


    /**
     * method showAction
     * 显示左右用户信息
     *
     * @author sily
     */
    public function showAction() {
        $data = $this->_user_model->getUserInfo();
        $this->getView()->assign('data', $data);
    }




    /**
     * Method addAction
     * 从前端获取数据,调用Model的add函数添加用户信息
     *
     * @author sily
     */
    public function addAction() {
        $this->getView();
    }

    public function addHandleAction() {
        $data = array();
        $data['name'] = $_POST['name'];
        $data['age'] = $_POST['age'];
        $data['phonenum'] = $_POST['phonenum'];

        $result = $this->_user_model->addUserInfo($data);


        if ($result !== false) {
            $this->redirect('/User/Userinfo/show');
        } else {
            echo 'insert failed';
        }

    }


    /**
     * Method updateAction
     * 更新user信息
     *
     * @author sily
     */
    public function updateAction() {
        //codes
        $id = $this->getRequest()->getParam('id');
        $id = $id + 0;
        $where = "id=" . $id;
        $data = $this->_user_model->getUserInfo($where);
        $data = $data[0];
        $this->getView()->assign('id', $id);
        $this->getView()->assign('data', $data);
    }

    public function updateHandleAction() {
        //更新用户信息
        $data = array();
        $data['id'] = $this->getRequest()->getPost('id');
        $data['name'] = $this->getRequest()->getPost('name');
        $data['age'] = $this->getRequest()->getPost('age');
        $data['phonenum'] = $this->getRequest()->getPost('phonenum');
        $result = $this->_user_model->updateUserInfo($data);
        $this->redirect('/User/Userinfo/show');
    }



    /**
     * Method deleteAction
     * 删除user信息
     *
     * @author sily
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $result = $this->_user_model->deleteUserInfo($id);
        // echo '影响的行数:' . $result;
        $this->redirect('/User/Userinfo/show');
    }

}




?>