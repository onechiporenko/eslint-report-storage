<?php

namespace ERS\Models;

/**
 *
 * @property int id
 * @property string path
 * @property array reports
 * @property Project $project
 */
class File extends \stdClass
{
    /**
     * @param array $data
     * @param null|Project $project
     * @return File
     */
    public static function instance($data, $project = null)
    {
        $file = new self();
        foreach ($data as $property => $value) {
            $file->{$property} = $value;
        }
        if ($project) {
            $file->project = $project;
        }
        return $file;
    }
}