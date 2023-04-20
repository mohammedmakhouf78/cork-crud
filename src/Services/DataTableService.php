<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class DataTableService
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
        $this->stub = File::get(__DIR__ . "/../Console/stubs/DataTable.php.stub");
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $selectData = ['id'];
        $columnsData = [];
        $columnsData[] = [
            'name' => 'id',
            'title' => trans('main.id'),
            'orderable' => true,
            'order' => 'ASC',
            'searchable' => true,
            'exact' => true,
            'search' => '',
            'type' => 'text'
        ];
        foreach ($this->columns as $column) {
            $selectData[] = $column->name;
            $columnsData[] = [
                'name' => $column->name,
                'title' => trans('main.' . $column->name),
                'orderable' => $column->type == "image" ? false : true,
                'searchable' => $column->type == "image" ? false : true,
                'exact' => false,
                'search' => '',
                'type' => $column->type,
                'lang' => $column->lang ?? false
            ];
        }

        $columnsData[] =  [
            'name' => 'edit',
            'title' => trans('main.edit'),
            'type' => 'edit'
        ];

        $columnsData[] =  [
            'name' => 'destroy',
            'title' => trans('main.destroy'),
            'type' => 'destroy'
        ];

        $columnsData = var_export($columnsData, true);

        foreach ($this->columns as $column) {
            $columnsData = str_replace("'main.$column->name'", "trans(\"main.$column->name\")", $columnsData);
        }



        $this->stub = str_replace("{select}", "'" . join("', '", $selectData) . "'", $this->stub);
        $this->stub = str_replace("{columns}", $columnsData, $this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("app/Http/Livewire/Admin"))) {
            mkdir(base_path("app/Http/Livewire/Admin"), 0777, true);
        }

        File::put(base_path("app/Http/Livewire/Admin/{$this->model}Datatable.php"), $this->stub);
    }
}
