<?php

namespace ERS\Managers;

class DataManager
{

    protected $_modelTable = '';

    protected $_modelClass = '';

    public function all($query = [])
    {

    }

    public function oneById($id)
    {

    }

    public function createModelInstance($args = [])
    {
        $func = [$this->_modelClass, 'instance'];
        $flag = false;
        if (is_array($args)) {
            if (count($args)) {
                $values = array_values($args);
                if (is_array($values[0])) {
                    $flag = true;
                }
            }
        }
        return $flag ? call_user_func_array($func, $args) : call_user_func($func, $args);
    }
}