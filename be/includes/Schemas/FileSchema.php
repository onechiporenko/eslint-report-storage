<?php

namespace ERS\Schemas;

use \ERS\Models\File;
use \Neomerx\JsonApi\Schema\SchemaProvider;

class FileSchema extends SchemaProvider
{
    protected $resourceType = 'file';

    /**
     * @param File $file
     * @return mixed
     */
    public function getId($file)
    {
        return $file->id;
    }

    /**
     * @param File $file
     * @return array
     */
    public function getAttributes($file)
    {
        return [
            'path' => $file->path,
            'reports' => property_exists($file, 'reports') ? $file->reports : []
        ];
    }

    /**
     * @param File $file
     * @return array
     */
    public function getRelationships($file)
    {
        return [
            'project' => [
                self::DATA => $file->project
            ]
        ];
    }

    /**
     * @param File $file
     * @return null
     */
    public function getResourceLinks($file)
    {
        return null;
    }
}
