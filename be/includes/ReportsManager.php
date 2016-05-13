<?php

namespace ERS;

use ERS\Db;
use ERS\DataManager;

class ReportsManager extends DataManager
{

    public function getById($id)
    {
        $db = Db::obtain();
        $report = $db->queryFirstPDO('select * from reports where id = ?', ['id' => $id]);
        $path = $db->queryFirstPDO('select path from projects where id = ?', ['id' => $report['project_id']])['path'];
        if (!$report) {
            return false;
        }
        $report = $this->_reformatSingle($report, 'report');
        $file_details = $db->fetchArrayPDO('select d.file_id, d.errors, d.warnings, f.path from report_details_by_file as d, files as f where f.id = d.file_id and report_id = ?', ['id' => $id]);
        $rule_details = $db->fetchArrayPDO('select d.rule_id, d.errors, d.warnings, r.name from report_details_by_rule as d, rules as r where r.id = d.rule_id and report_id = ?', ['id' => $id]);

        if ($report && $file_details && $rule_details) {
            $errors = $report['data']['attributes']['errors'];
            $warnings = $report['data']['attributes']['warnings'];
            $files = $this->_withPercents($file_details, $errors, $warnings);
            foreach($files as $k=>$file) {
                $files[$k]['path'] = str_replace($path, '', $file['path']);
            }
            $report['data']['attributes']['details'] = [
                'files' => $files,
                'rules' => $this->_withPercents($rule_details, $errors, $warnings)
            ];
            return $report;
        }
        return false;
    }

    public function deleteById($id)
    {
        $db = Db::obtain();
        $db->deletePDO('report_details_by_file', ['report_id' => $id], -1);
        $db->deletePDO('report_details_by_rule', ['report_id' => $id], -1);
        $db->deletePDO('reports', ['id' => $id]);
    }

    public function getMany($query = [])
    {
        $db = Db::obtain();
        $additionalSql = array_key_exists('project_id', $query) ? ' where project_id = ' . intval($query['project_id']) : '';
        $sql = 'select * from reports' . $additionalSql;
        $reports = $db->fetchArrayPDO($sql);
        $reports = $reports ? $reports : [];
        return $this->_reformatMultiple($reports, 'report');
    }

    protected function _withPercents($data, $errors, $warnings)
    {
        foreach ($data as $k => $row) {
            $data[$k]['errors_percents'] = round($data[$k]['errors'] / $errors * 100, 2);
            $data[$k]['warnings_percents'] = round($data[$k]['warnings'] / $warnings * 100, 2);
        }
        return $data;
    }

}