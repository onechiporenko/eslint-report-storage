<?php

namespace ERS;

use ERS\Db;
use ERS\DataManager;

class FilesManager extends DataManager
{

    public function getMany() {
        $files = Db::obtain()->fetchArrayPDO('select * from files');
        return $this->_reformatMultiple($files, 'file');
    }

    public function getById($id)
    {
        $db = Db::obtain();
        $file = $db->queryFirstPDO('select * from files where id = ?', ['id' => $id]);
        $reports = $db->fetchArrayPDO('select * from report_details_by_file where file_id = ?', ['id' => $id]);
        if ($file) {
            $file = $this->_reformatSingle($file, 'file');
            $file['data']['attributes']['reports'] = [];
            foreach($reports as $report) {
                unset ($report['file_id']);
                unset ($report['id']);
                $report['errors'] = intval($report['errors']);
                $report['warnings'] = intval($report['warnings']);
                array_push($file['data']['attributes']['reports'], $report);
            }
            return $file;
        }
        return false;
    }

    public function getResultDetailsByReportId($fileId, $reportId) {
        $result = Db::obtain()->queryFirstPDO('select * from report_details_by_file where file_id = ? and report_id = ?', ['file_id' => $fileId, 'report_id' => $reportId]);
        $result = ['data' => unserialize($result['lines'])];
        return $result;
    }

}