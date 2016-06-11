<?php

namespace ERS\Models;

/**
 *
 * @property int id
 * @property string name
 * @property int project_id
 * @property Project project
 */
class Rule extends \stdClass
{
    /**
     * @param array $data
     * @param null|Project $project
     * @return Rule
     */
    public static function instance($data, $project = null)
    {
        $rule = new self();
        foreach ($data as $property => $value) {
            $rule->{$property} = $value;
        }
        if ($project) {
            $rule->project = $project;
        }
        return $rule;
    }
}