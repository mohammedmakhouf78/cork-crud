<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class IndexViewService
{
    private $stub;
    private $model;
    private $modelLower;

    private $hasImage = false;
    private $columns;

    public function run(string $model, object $crudInfo)
    {
        $this->model = $model;
        $this->modelLower = lcfirst($this->model);
        $this->hasImage = $crudInfo->hasImages;
        $this->columns = $crudInfo->columns;

        $this->prepareStub();
        $this->putInFile();
    }

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/IndexView.php.stub");
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $this->stub = str_replace("{model}", $this->model, $this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("resources/views/admin/pages/{$this->modelLower}"))) {
            mkdir(base_path("resources/views/admin/pages/{$this->modelLower}"), 0777, true);
        }

        File::put(base_path("resources/views/admin/pages/{$this->modelLower}/index.blade.php"), $this->stub);
    }
}
