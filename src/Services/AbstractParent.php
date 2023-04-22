<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

abstract class AbstractParent
{
    protected $stub;
    protected $model;
    protected $modelLower;

    protected $hasImage = false;
    protected $columns;
    protected $metaColumns;
    protected $tableName;
    protected $relations;

    public function run(string $model, object $crudInfo)
    {
        $this->model = $model;
        $this->modelLower = lcfirst($this->model);
        $this->hasImage = $crudInfo->hasImages;
        $this->columns = $crudInfo->columns;
        $this->metaColumns = $crudInfo->metaColumns ?? [];
        $this->tableName = $crudInfo->tableName;
        $this->relations = $crudInfo->relations ?? [];

        $this->prepareStub();
        $this->putInFile();
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareRelationName(string $column)
    {
        $relationModel = str_replace("_id", '', $column);
        $relationModel = str_replace("_", ' ', $relationModel);
        $relationModel = ucwords($relationModel);
        $relationModel = str_replace(" ", '', $relationModel);
        return lcfirst($relationModel);
    }
    public function replaceUpperWith(string $word, string $rep)
    {
        return preg_replace('/([A-Z])/', $rep . '$1', $word);
    }

    abstract function prepareStub();
    abstract function putInFile();
}
