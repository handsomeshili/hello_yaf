<?php

/**
 * Class     Base_Models
 * 全局的模型基类
 *
 * @author   yangyang3
 */
class Base_Models {

    /**
     * Variable  _module_name
     * 当前实例对应的模块名
     *
     * @author   yangyang3
     * @var      null
     */
    private $_module_name = null;

    /**
     * Variable  _table_name
     * 当前实例对应的表名
     *
     * @author   yangyang3
     * @var      null
     */
    private $_table_name = null;

    /**
     * Variable  _condition
     * 更新/删除的条件列表
     *
     * @author   yangyang3
     * @var      array
     */
    private $_condition = array();

    /**
     * Variable  _database_config
     * 数据库配置信息
     *
     * @author   yangyang3
     * @var      array
     */
    private $_database_config = array();

    /**
     * Variable  _master_connections
     * 主库连接对象
     *
     * @author   yangyang3
     * @static
     * @var      null
     */
    private static $_master_connections = null;

    /**
     * Variable  _slave_connections
     * 从库连接对象
     *
     * @author   yangyang3
     * @static
     * @var      object
     */
    private static $_slave_connections = null;

    /**
     * Variable  _is_begin_transactions
     * 是否开始事务
     *
     * @author   yangyang3
     * @static
     * @var      bool
     */
    private static $_is_begin_transactions = false;

    /**
     * Method  __construct
     * 构造方法
     *
     * @author yangyang3
     *
     * @param string $module_name
     * @param string $table_name
     */
    public function __construct($module_name = null, $table_name = null) {
        //验证table_name
        if ($table_name === null) {
            //验证module_name
            if (null === $module_name) {
                $table_name = str_replace('Model', '', get_called_class());
            } else {
                $table_name = str_replace($module_name, '', str_replace('Model', '', get_called_class()));
            }
        } elseif (stripos($table_name, 'Model') !== false) {
            $table_name = str_replace('Model', '', $table_name);
        }

        //驼峰命名转下划线命名
        $table_name = strtolower(trim(camel_to_underline($table_name), '_'));

        //设置数据库表名
        $this->setTableName($table_name);

        //设置数据库配置信息
        if (null === $module_name) {
            $module_name = 'default';
        } else {
            $module_name = strtolower($module_name);
        }

        $this->setModuleName($module_name);

        // $this->_database_config = get_config_by_key('database')->$module_name->toArray();
        $this->_database_config = get_config_by_key('database')->toArray();
    }

    /**
     * Method  getMasterConnection
     * 获取主库连接对象
     *
     * @author yangyang3
     * @return Zend_Db_Adapter_Abstract
     */
    public function getMasterConnection() {
        $module_name = $this->getModuleName();

        if (!isset(self::$_master_connections[$module_name])) {
            //Zend_Db::factory 接受两个参数，一个是配置文件中的database.master.adapter, 另一个是数据库配置参数，在数据库配置文件里定义
            self::$_master_connections[$module_name] = Zend_Db::factory($this->_database_config['database']['master']['adapter'], $this->_database_config['database']['master']['params']);
        }

        return self::$_master_connections[$module_name];
    }

    /**
     * Method  getSlaveConnection
     * 获取从库连接对象
     *
     * @author yangyang3
     * @return Zend_Db_Adapter_Abstract
     */
    public function getSlaveConnection() {
        if (!empty($this->_database_config['slave']['enable'])) {
            $module_name = $this->getModuleName();

            if (!isset(self::$_slave_connections[$module_name])) {
                self::$_slave_connections[$module_name] = Zend_Db::factory($this->_database_config['slave']['adapter'], $this->_database_config['slave']['params']);
            }

            return self::$_slave_connections[$module_name];
        } else {
            return $this->getMasterConnection();
        }
    }

    /**
     * Method  setModuleName
     * 设置数据库模块名, 仅在类内调用
     *
     * @author yangyang3
     *
     * @param string $module_name
     */
    public function setModuleName($module_name) {
        $this->_module_name = $module_name;
    }
    
    /**
     * Method  getModuleName
     * 获取数据库模块名
     *
     * @author yangyang3
     * @return string
     */
    public function getModuleName() {
        return $this->_module_name;
    }

    /**
     * Method  setTableName
     * 设置数据库表名, 仅在类内调用
     *
     * @author yangyang3
     *
     * @param string $table_name
     */
    public function setTableName($table_name) {
        $this->_table_name = $table_name;
    }

    /**
     * Method  getTableName
     * 获取数据库表名
     *
     * @author yangyang3
     * @return string
     */
    public function getTableName() {
        return $this->_table_name;
    }

    /**
     * Method  _setCondition
     * 设置condition
     *
     * @author yangyang3
     *
     * @param array $condition
     */
    private function _setCondition(array $condition = array()) {
        $this->_condition = $condition;
    }

    /**
     * Method  _getCondition
     * 获取condition
     *
     * @author yangyang3
     * @return array
     */
    private function _getCondition() {
        return $this->_condition;
    }

    /**
     * Method  select
     * 获取select实例
     *
     * @author yangyang3
     *
     * @param string $table_name
     * @param string $columns
     * @param bool   $is_connect_master
     *
     * @return Zend_Db_Select
     */
    public function select($table_name = null, $columns = '*', $is_connect_master = false) {
        //验证表名
        if ($table_name === null) {
            $table_name = $this->getTableName();
        }
        
        //实例化Zend_Db_Select
        if ($is_connect_master === false) {
            $select = new Zend_Db_Select($this->getSlaveConnection());
            // echo "die";
        } else {
            $select = new Zend_Db_Select($this->getMasterConnection());
        }

        //设置默认表名和列名
        $select->from($table_name, $columns);
        
        //返回实例
        return $select;
    }

    /**
     * Method  where
     * 设置更新/删除的条件
     *
     * @author yangyang3
     *
     * @param array $data
     *
     * @return $this|bool
     */
    public function where(array $data) {
        //验证sql
        if (empty($data)) {
            return false;
        }

        $condition = array();
        //循环quoteInto每一个元素
        foreach ($data as $key => $info) {
            $condition[] = $this->quoteInto($key, $info);
        }

        //设置condition
        $this->_setCondition($condition);
        //返回实例  
        return $this;
    }

    /**
     * Method  insert
     * 插入数据
     *
     * @author yangyang3
     *
     * @param array $data
     *
     * @return bool|int
     */
    public function insert(array $data) {
        //验证数据
        if (empty($data)) {
            return false;
        }

        //连接主库, 执行插入操作, 返回插入的行数
        $affected_rows = $this->getMasterConnection()->insert($this->getTableName(), $data);

        //验证结果并返回
        if ($affected_rows !== false) {
            return $last_insert_id = $this->getMasterConnection()->lastInsertId();
        } else {
            return false;
        }
    }

    /**
     * Method  update
     * 更新数据
     *
     * @author yangyang3
     *
     * @param array $data
     * @param array $where
     *
     * @return bool|int
     */
    public function update(array $data, $where = null) {
        //验证$data
        if (empty($data)) {
            return false;
        }

        //验证$where
        if ($where === null) {
            $condition = $this->_getCondition();

            if (empty($condition)) {
                return false;
            }

            $this->_setCondition(array());

            $where = $condition;
        }

        //连接主库, 执行更新操作, 返回影响的行数
        $affected_rows = $this->getMasterConnection()->update($this->getTableName(), $data, $where);

        //验证返回结果
        if ($affected_rows !== false) {
            return $affected_rows;
        } else {
            return false;
        }
    }

    /**
     * Method  delete
     * 删除数据
     *
     * @author yangyang3
     *
     * @param null $where
     *
     * @return bool|int
     */
    public function delete($where = null) {
        //验证$where
        if ($where === null) {
            $condition = $this->_getCondition();

            if (empty($condition)) {
                return false;
            }

            $this->_setCondition(array());

            $where = $condition;
        }

        //连接主库, 执行删除操作, 返回影响的行数
        $affected_rows = $this->getMasterConnection()->delete($this->getTableName(), $where);

        //验证返回结果
        if ($affected_rows !== false) {
            return $affected_rows;
        } else {
            return false;
        }
    }

    /**
     * Method  getCount
     * 获取记录条数
     *
     * @author yangyang3
     *
     * @param array $where
     * @param bool  $is_connect_master
     *
     * @return bool|int
     */
    public function getCount(array $where = array(), $is_connect_master = false) {
        $select = $this->select($this->getTableName(), 'COUNT(*) AS count', $is_connect_master);

        foreach ($where as $key => $value) {
            $select = $select->where($key, $value);
        }

        $count = $this->fetchOne($select, $is_connect_master);

        if (is_numeric($count)) {
            return intval($count);
        } else {
            return false;
        }
    }

    /**
     * Method  getLastSql
     * 获取最后一条SQL
     *
     * @author yangyang3
     * @return string|bool
     */
    public function getLastSql() {
        $last_sql_string = Yaf_Registry::get('last_sql_string');
        $last_sql_data   = Yaf_Registry::get('last_sql_data');

        if (empty($last_sql_string)) {
            return false;
        }

        $sql    = '';
        $count  = 0;
        $length = strlen($last_sql_string);

        for ($i = 0; $i < $length; $i++) {
            if ($last_sql_string[$i] !== '?') {
                $sql .= $last_sql_string[$i];
            } else {
                if (isset($last_sql_data[$count])) {
                    $sql .= "'" . $last_sql_data[$count++] . "'";
                } else {
                    $sql .= $last_sql_string[$i];
                }
            }
        }

        return $sql;
    }

    /**
     * Method  fetchAll
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchAll($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchAll($sql);
        } else {
            return $this->getMasterConnection()->fetchAll($sql);
        }
    }

    /**
     * Method  fetchRow
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchRow($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchRow($sql);
        } else {
            return $this->getMasterConnection()->fetchRow($sql);
        }
    }

    /**
     * Method  fetchAssoc
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchAssoc($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchAssoc($sql);
        } else {
            return $this->getMasterConnection()->fetchAssoc($sql);
        }
    }

    /**
     * Method  fetchCol
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchCol($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchCol($sql);
        } else {
            return $this->getMasterConnection()->fetchCol($sql);
        }
    }

    /**
     * Method  fetchPairs
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchPairs($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchPairs($sql);
        } else {
            return $this->getMasterConnection()->fetchPairs($sql);
        }
    }

    /**
     * Method  fetchOne
     *
     * @author yangyang3
     *
     * @param string $sql
     * @param bool   $is_connect_master
     *
     * @return array
     */
    public function fetchOne($sql, $is_connect_master = false) {
        if ($is_connect_master === false) {
            return $this->getSlaveConnection()->fetchOne($sql);
        } else {
            return $this->getMasterConnection()->fetchOne($sql);
        }
    }

    /**
     * Method  quote
     *
     * @author yangyang3
     *
     * @param string $value
     * @param int    $type
     *
     * @return mixed
     */
    public function quote($value, $type = null) {
        return $this->getSlaveConnection()->quote($value, $type);
    }

    /**
     * Method  quoteInto
     *
     * @author yangyang3
     *
     * @param string $text
     * @param string $value
     * @param int    $type
     * @param int    $count
     *
     * @return string
     */
    public function quoteInto($text, $value, $type = null, $count = null) {
        return $this->getSlaveConnection()->quoteInto($text, $value, $type, $count);
    }

    /**
     * Method  getTransactionStatus
     *
     * @author yangyang3
     * @return bool
     */
    public function getTransactionStatus() {
        $module_name = $this->getModuleName();

        return self::$_is_begin_transactions[$module_name];
    }

    /**
     * Method  beginTransaction
     *
     * @author yangyang3
     * @return Zend_Db_Adapter_Abstract
     */
    public function beginTransaction() {
        $module_name = $this->getModuleName();

        if (isset(self::$_is_begin_transactions[$module_name]) && self::$_is_begin_transactions[$module_name] === true) {
            return true;
        }

        self::$_is_begin_transactions[$module_name] = true;

        self::$_slave_connections[$module_name] = $this->getMasterConnection();

        return $this->getMasterConnection()->beginTransaction();
    }

    /**
     * Method  commit
     *
     * @author yangyang3
     * @return Zend_Db_Adapter_Abstract
     */
    public function commit() {
        $module_name = $this->getModuleName();

        if (!isset(self::$_is_begin_transactions[$module_name]) || self::$_is_begin_transactions[$module_name] !== true) {
            return false;
        }

        self::$_is_begin_transactions[$module_name] = false;

        self::$_slave_connections[$module_name] = $this->getSlaveConnection();

        return $this->getMasterConnection()->commit();
    }

    /**
     * Method  rollBack
     *
     * @author yangyang3
     * @return Zend_Db_Adapter_Abstract
     */
    public function rollBack() {
        $module_name = $this->getModuleName();

        if (!isset(self::$_is_begin_transactions[$module_name]) || self::$_is_begin_transactions[$module_name] !== true) {
            return false;
        }

        self::$_is_begin_transactions[$module_name] = false;

        self::$_slave_connections[$module_name] = $this->getSlaveConnection();

        return $this->getMasterConnection()->rollBack();
    }

}