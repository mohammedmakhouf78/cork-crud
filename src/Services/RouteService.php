<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class RouteService
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
        $this->stub = File::get(base_path("routes/web.php"));
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $routeData = "Route::resource('{$this->modelLower}', {$this->model}Controller::class);\n";
        $routeData .= "//{route}\n";

        $this->stub = str_replace("//{route}\n", $routeData, $this->stub);
    }

    public function putInFile()
    {
        File::put(base_path("routes/web.php"), $this->stub);
    }
}
