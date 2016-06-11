<?php

namespace ERS\Models;

/**
 *
 * @property int id
 * @property string name
 * @property string path
 * @property string subpath
 * @property string description
 * @property string repo
 * @property Report[] reports
 * @property File[] files
 * @property Rule[] rules
 * @property string raw
 */
class Project extends \stdClass
{
    /**
     * @param array $data
     * @param Report[] $reports
     * @param File[] $files
     * @param Rule[] $rules
     * @return Project
     */
    public static function instance($data, $reports = [], $files = [], $rules = [])
    {
        $project = new self();
        foreach ($data as $property => $value) {
            $project->{$property} = $value;
        }
        $project->reports = $reports;
        $project->files = $files;
        $project->rules = $rules;
        return $project;
    }
}