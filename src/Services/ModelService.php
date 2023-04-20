<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class ModelService
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
        $this->stub = File::get(__DIR__ . "/../Console/stubs/Model.php.stub");
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $fillables = [];
        $imageColumns = [];
        $langColumns = [];
        $trans = [];
        foreach ($this->columns as $column) {
            $fillables[] = $column->name;
            if ($column->type == "image") {
                $imageColumns[] = $column;
            }
            if ($column->lang) {
                $langColumns[] = $column;
                $trans[] = $column->name;
            }
        }

        $imagesData = "";

        foreach ($imageColumns as $imageColumn) {
            $imageData = File::get(__DIR__ . "/../Console/stubs/image.php.stub");
            $imageData = str_replace("{columnName_upper}", strtoupper($imageColumn->name), $imageData);
            $imageData = str_replace("{model_lower}", $this->modelLower, $imageData);
            $imageData = str_replace("{columnName}", $imageColumn->name, $imageData);
            $imageData = str_replace("{columnName_capital}", ucfirst($imageColumn->name), $imageData);
            $imageData = str_replace("{model_capital}", $this->model, $imageData);
            $imagesData .= $imageData . "\n";
        }


        $this->stub = str_replace("{fillable}", "'" . join("', '", $fillables) . "'", $this->stub);
        $this->stub = str_replace("{trans}", "'" . join("', '", $trans) . "'", $this->stub);
        $this->stub = str_replace("//{image}", $imagesData, $this->stub);
    }

    public function putInFile()
    {
        File::put(base_path("app/Models{$this->model}.php"), $this->stub);
    }
}
