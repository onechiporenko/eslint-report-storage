<?php

namespace ERS;

use ERS\Db;
use ERS\DataManager;

class ProjectsManager extends DataManager
{

    public function getById($id)
    {
        $db = Db::obtain();
        $project = $db->queryFirstPDO('select * from projects where id = ?', ['id' => $id]);
        if (!$project) {
            return false;
        }
        $project = $this->_reformatSingle($project, 'project');
        if ($project) {
            return $project;
        }
        return false;
    }

    public function getMany()
    {
        $db = Db::obtain();
        $projects = $db->fetchArrayPDO('select * from projects order by id');
        $projects = $projects ? $projects : [];
        $projects = $this->_reformatMultiple($projects, 'project');
        $reports = $db->fetchArrayPDO('select id, project_id from reports');
        $groupedReports = [];
        foreach ($reports as $report) {
            $pId = $report['project_id'];
            if (!array_key_exists($pId, $groupedReports)) {
                $groupedReports[$pId] = [];
            }
            array_push($groupedReports[$pId], ['type' => 'report', 'id' => $report['id']]);
        }
        foreach ($projects['data'] as $k => $project) {
            $pId = $project['id'];
            $projects['data'][$k]['relationships']['report']['data'] = array_key_exists($pId, $groupedReports) ? $groupedReports[$pId] : [];
        }
        return $projects;
    }

}