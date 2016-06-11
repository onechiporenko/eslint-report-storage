<?php

namespace ERS\Managers;

use ERS\Db;

class RulesManager extends DataManager
{

    use SubResForProjects;

    protected $_modelClass = 'ERS\Models\Rule';
    protected $_modelTable = 'rules';

    public function oneById($id)
    {
        $db = Db::obtain();
        $instance = $db->queryFirstPDO('select * from ' . $this->_modelTable . ' where id = ?', ['id' => $id]);
        $reports = $db->fetchArrayPDO('select * from report_details_by_rule where rule_id = ?', ['id' => $id]);
        if ($instance) {
            $instance['reports'] = [];
            foreach ($reports as $report) {
                unset ($report['rule_id']);
                unset ($report['id']);
                $report['errors'] = intval($report['errors']);
                $report['warnings'] = intval($report['warnings']);
                array_push($instance['reports'], $report);
            }
            return $this->createModelInstance($instance);
        }
        return false;
    }

}