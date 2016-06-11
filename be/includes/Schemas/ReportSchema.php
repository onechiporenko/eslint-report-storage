<?php

namespace ERS\Schemas;

use \ERS\Models\Report;
use \Neomerx\JsonApi\Schema\SchemaProvider;

class ReportSchema extends SchemaProvider
{
    protected $resourceType = 'report';

    /**
     * @param Report $report
     * @return mixed
     */
    public function getId($report)
    {
        return $report->id;
    }

    /**
     * @param Report $report
     * @return array
     */
    public function getAttributes($report)
    {
        return [
            'date' => $report->date,
            'hash' => $report->hash,
            'errors' => $report->errors,
            'warnings' => $report->warnings,
            'details' => property_exists($report, 'details') ? $report->details : []
        ];
    }

    /**
     * @param Report $report
     * @return array
     */
    public function getRelationships($report)
    {
        return [
            'project' => [
                self::DATA => $report->project
            ]
        ];
    }

    /**
     * @param Report $report
     * @return null
     */
    public function getResourceLinks($report)
    {
        return null;
    }
}
