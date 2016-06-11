<?php

namespace ERS\Managers;

use ERS\Db;
use ERS\Models\Project;

/**
 * @property string _modelClass
 * @property string _modelTable
 */
trait SubResForProjects
{

    public function getGroupedByProjectId()
    {
        $db = Db::obtain();
        $sql = 'select * from ' . $this->_modelTable;
        $instances = $db->fetchArrayPDO($sql);
        if (!$instances) {
            return [];
        }
        $groupedInstances = [];
        foreach ($instances as $instance) {
            $pId = $instance['project_id'];
            if (!array_key_exists($pId, $groupedInstances)) {
                $groupedInstances[$pId] = [];
            }
            array_push($groupedInstances[$pId], call_user_func([$this->_modelClass, 'instance'], $instance));
        }
        return $groupedInstances;
    }

    public function all($query = [])
    {
        $db = Db::obtain();
        $projectIdExists = array_key_exists('project_id', $query);
        $pId = $projectIdExists ? intval($query['project_id']) : null;
        $additionalSql = $projectIdExists ? ' where project_id = ' . $pId : '';
        $sql = 'select * from '.$this->_modelTable.' ' . $additionalSql;
        $instances = $db->fetchArrayPDO($sql);
        return array_map(function ($instance) {
            return call_user_func([$this->_modelClass, 'instance'], $instance, Project::instance(['id' => $instance['project_id']]));
        }, $instances);
    }

    public function createModelInstance($arg)
    {
        return call_user_func_array([$this->_modelClass, 'instance'], [$arg, Project::instance(['id' => $arg['project_id']])]);
    }

}