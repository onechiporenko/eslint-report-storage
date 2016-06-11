<?php

namespace ERS\Managers;

use ERS\Db;

class FilesManager extends DataManager
{

    use SubResForProjects;

    protected $_modelClass = 'ERS\Models\File';
    protected $_modelTable = 'files';

    public function oneById($id)
    {
        $db = Db::obtain();
        $instance = $db->queryFirstPDO('select * from '.$this->_modelTable.' where id = ?', ['id' => $id]);
        $reports = $db->fetchArrayPDO('select * from report_details_by_file where file_id = ?', ['id' => $id]);
        if ($instance) {
            $instance['reports'] = [];
            foreach ($reports as $report) {
                unset ($report['file_id']);
                unset ($report['id']);
                $report['errors'] = intval($report['errors']);
                $report['warnings'] = intval($report['warnings']);
                $report['report_id'] = intval($report['report_id']);
                array_push($instance['reports'], $report);
            }
            return $this->createModelInstance($instance);
        }
        return false;
    }

    public function getResultDetailsByReportId($fileId, $reportId)
    {
        $result = Db::obtain()->queryFirstPDO('select * from report_details_by_file where file_id = ? and report_id = ?', ['file_id' => $fileId, 'report_id' => $reportId]);
        $result = ['data' => unserialize($result['lines'])];
        return $result;
    }

}