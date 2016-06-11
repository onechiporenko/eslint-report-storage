<?php

namespace ERS\Managers;

use ERS\Db;

class ProjectsManager extends DataManager
{

    protected $_modelClass = 'ERS\Models\Project';
    protected $_modelTable = 'projects';

    public function oneById($id)
    {
        $project = Db::obtain()->queryFirstPDO('select * from ' . $this->_modelTable . ' where id = ?', ['id' => $id]);
        return $this->createModelInstance($project);
    }

    public function all()
    {
        $projects = Db::obtain()->fetchArrayPDO('select * from ' . $this->_modelTable . ' order by id');
        foreach ($projects as $k => $project) {
            $projects[$k] = $this->createModelInstance($project);
        }
        return $projects;
    }

}