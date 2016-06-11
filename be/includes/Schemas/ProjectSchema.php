<?php

namespace ERS\Schemas;

use \ERS\Models\Project;
use \Neomerx\JsonApi\Schema\SchemaProvider;

class ProjectSchema extends SchemaProvider
{
    protected $resourceType = 'project';

    /**
     * @param Project $project
     * @return mixed
     */
    public function getId($project)
    {
        return $project->id;
    }

    /**
     * @param Project $project
     * @return array
     */
    public function getAttributes($project)
    {
        return [
            'name' => $project->name,
            'path' => $project->path,
            'subpath' => $project->subpath,
            'description' => $project->description,
            'repo' => $project->repo,
            'raw' => $project->raw
        ];
    }

    /**
     * @param Project $project
     * @return array
     */
    public function getRelationships($project)
    {
        return [
            'reports' => [
                self::DATA => $project->reports
            ],
            'files' => [
                self::DATA => $project->files
            ],
            'rules' => [
                self::DATA => $project->rules
            ]
        ];
    }

    /**
     * @param Project $project
     * @return null
     */
    public function getResourceLinks($project)
    {
        return null;
    }
}
