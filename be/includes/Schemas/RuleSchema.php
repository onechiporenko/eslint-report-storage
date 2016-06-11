<?php

namespace ERS\Schemas;

use \ERS\Models\Rule;
use \Neomerx\JsonApi\Schema\SchemaProvider;

class RuleSchema extends SchemaProvider
{
    protected $resourceType = 'rule';

    /**
     * @param Rule $rule
     * @return mixed
     */
    public function getId($rule)
    {
        return $rule->id;
    }

    /**
     * @param Rule $rule
     * @return array
     */
    public function getAttributes($rule)
    {
        return [
            'name' => $rule->name,
            'reports' => property_exists($rule, 'reports') ? $rule->reports : []
        ];
    }

    /**
     * @param Rule $rule
     * @return array
     */
    public function getRelationships($rule)
    {
        return [
            'project' => [
                self::DATA => $rule->project
            ]
        ];
    }

    /**
     * @param Rule $rule
     * @return null
     */
    public function getResourceLinks($rule)
    {
        return null;
    }
}
