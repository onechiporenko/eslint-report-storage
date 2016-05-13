<?php

namespace ERS;

use ERS\Db;
use ERS\DataManager;

class RulesManager extends DataManager
{

    public function getMany($query = [])
    {
        $additionalSql = array_key_exists('project_id', $query) ? ' where project_id = ' . intval($query['project_id']) : '';
        $sql = 'select * from rules ' . $additionalSql;
        $files = Db::obtain()->fetchArrayPDO($sql);
        return $this->_reformatMultiple($files, 'rule');
    }

    public function getById($id)
    {
        $db = Db::obtain();
        $file = $db->queryFirstPDO('select * from rules where id = ?', ['id' => $id]);
        $reports = $db->fetchArrayPDO('select * from report_details_by_rule where rule_id = ?', ['id' => $id]);
        if ($file) {
            $file = $this->_reformatSingle($file, 'rule');
            $file['data']['attributes']['reports'] = [];
            foreach ($reports as $report) {
                unset ($report['rule_id']);
                unset ($report['id']);
                $report['errors'] = intval($report['errors']);
                $report['warnings'] = intval($report['warnings']);
                array_push($file['data']['attributes']['reports'], $report);
            }
            return $file;
        }
        return false;
    }

}