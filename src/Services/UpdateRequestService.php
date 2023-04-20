<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class UpdateRequestService
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
        $this->stub = File::get(__DIR__ . "/../Console/stubs/UpdateRequest.php.stub");
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $rulesData = "";
        foreach ($this->columns as $column) {
            if ($column->lang == true) {
                $rulesData .= "'{$column->name}_en' => '$column->validation',\n";
                $rulesData .= "'{$column->name}_ar' => '$column->validation',\n";
            } else {
                $rulesData .= "'$column->name' => '$column->validation',\n";
            }
        }

        $this->stub = str_replace("{model}", $this->model, $this->stub);
        $this->stub = str_replace("{rules}", $rulesData, $this->stub);

        dd($this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("App/Http/Requests/{$this->model}"))) {
            mkdir(base_path("App/Http/Requests/{$this->model}"), 0777, true);
        }

        File::put(base_path("App/Http/Requests/{$this->model}/UpdateRequest.php"), $this->stub);
    }
}
