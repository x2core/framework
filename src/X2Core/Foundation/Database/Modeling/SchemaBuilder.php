<?php

namespace Eyrene\Database\Modeling;

use \Doctrine\DBAL\Schema\Schema as DoctrineSchema;

class SchemaBuilder
{
    /**
     * @var DoctrineSchema schemaManager
     */
    protected $schemaManager;

    public function __construct(DoctrineSchema $schemaManager)
    {
        $this->schemaManager = $schemaManager;
    }

    public function build(Model $model){
        return ;
    }
}