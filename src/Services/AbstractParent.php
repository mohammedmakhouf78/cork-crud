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

    public function run(string $model, object $crudInfo)
    {
        $this->model = $model;
        $this->modelLower = lcfirst($this->model);
        $this->hasImage = $crudInfo->hasImages;
        $this->columns = $crudInfo->columns;
        $this->metaColumns = $crudInfo->metaColumns;
        $this->tableName = $crudInfo->tableName;

        $this->prepareStub();
        $this->putInFile();
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    abstract function prepareStub();
    abstract function putInFile();
}
