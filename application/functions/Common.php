<?php

/**
 * 全局公共函数库
 *
 * @author sily
 */


/**
 * Method underline_to_camel()
 * 下划线转驼峰
 * @author yangyang3
 *
 * @param string $string
 * @param bool   $is_ignore_uppercase
 *
 * @return string
 */

function underline_to_camel($string, $is_ignore_uppercase = false) {
    if (false === $is_ignore_uppercase) {
        return preg_replace('/_([a-zA-Z])/e', "strtoupper('\\1')", $string);
    } else {
        return preg_replace('/_([a-z])/e', "strtoupper('\\1')", $string);
    }
}

/**
 * Method  camel_to_underline
 * 驼峰转下划线
 *
 * @author yangyang3
 *
 * @param $string
 *
 * @return string
 */
function camel_to_underline($string) {
    return strtolower(preg_replace('/(?!^)(?=[A-Z])/', '_', $string));
}

/**
 * Method import_service_by_module_name()
 * 根据Module名称导入对应的Service
 *
 * @author sily
 *
 * @param $module_name
 *
 * @return bool
 */
function import_service_by_module_name($module_name) {
    $config = Yaf_Registry::get('config')->application->toArray();
    // var_dump($config);
    $config_key = strtolower($module_name);

    if (empty($config[$config_key]['services'])) {
        return false;
    }

    foreach ($config[$module_name]['services'] as $key =>$service_name) {
        Yaf_Loader::import("{$config['services']}/{$module_name}/{service_name}.{$config['ext']}");
    }
    return true;
}


/**
 * Method  get_config_by_key
 * 通过Key获取对应的配置
 *
 * @author yangyang3
 *
 * @param $key
 *
 * @return mixed|Yaf_Config_Ini
 */
function get_config_by_key($key) {
    $config_key = $key . '_config';

    $config = Yaf_Registry::get($config_key);

    if (null === $config) {
        $config_file = Yaf_Registry::get('config')->application->config->$key;

        if (null !== $config_file) {
            $config = new Yaf_Config_Ini($config_file);

            Yaf_Registry::set($config_key, $config);
        }
    }
    // var_dump($config);
    return $config;
}


?>