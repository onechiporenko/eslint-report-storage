<?php

namespace ERS\Models;

/**
 *
 * @property int id
 * @property string date
 * @property string hash
 * @property int errors
 * @property int warnings
 * @property int project_id
 * @property Project project
 */
class Report extends \stdClass
{
    /**
     * @param array $data
     * @param null|Project $project
     * @return Report
     */
    public static function instance($data, $project = null)
    {
        $report = new self();
        foreach ($data as $property => $value) {
            $report->{$property} = $value;
        }
        if ($project) {
            $report->project = $project;
        }
        return $report;
    }
}