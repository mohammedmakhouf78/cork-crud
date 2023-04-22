<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class DataTableService extends AbstractParent
{

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/DataTable.php.stub");
        return $this->stub;
    }

    public function prepareStub()
    {
        $selectData = ['id'];
        $columnsData = [];
        $columnsData[] = [
            'name' => 'id',
            'title' => "trans('id')",
            'orderable' => true,
            'order' => 'ASC',
            'searchable' => true,
            'exact' => true,
            'search' => '',
            'type' => 'text'
        ];
        foreach ($this->columns as $column) {
            $selectData[] = $column->name;

            if ($column->type == "select") {
                $relationName = $this->prepareRelationName($column->name);
                $title = $this->replaceUpperWith($relationName, ' ');
                $columnsData[] = [
                    'name' => $column->name,
                    'title' => "trans('$title')",
                    'orderable' => $column->type == "image" ? false : true,
                    'searchable' => $column->type == "image" ? false : true,
                    'exact' => false,
                    'search' => '',
                    'lang' => $column->lang ?? false,
                    'type' => 'custom',
                    'view' => "admin.pages.{$this->modelLower}.datatable.{$relationName}"
                ];
            } else {
                $title = str_replace('_', " ", $column->name);
                $columnsData[] = [
                    'name' => $column->name,
                    'title' => "trans('$title')",
                    'orderable' => $column->type == "image" ? false : true,
                    'searchable' => $column->type == "image" ? false : true,
                    'exact' => false,
                    'search' => '',
                    'type' => $column->type,
                    'lang' => $column->lang ?? false
                ];
            }
        }


        $withData = "";
        if ($this->relations) {
            $withData .= "->with([";
            foreach ($this->relations as $relation) {
                $relationModel = $this->prepareRelationName($relation->name);
                $withData .= "'$relationModel' => function(\$q){
                    return \$q->forSelect()->get();
                },\n";
            }
            $withData = rtrim($withData, ',');
            $withData .= "]);";
        }


        $columnsData[] =  [
            'name' => 'edit',
            'title' => "trans('edit')",
            'type' => 'edit'
        ];

        $columnsData[] =  [
            'name' => 'destroy',
            'title' => "trans('destroy')",
            'type' => 'destroy'
        ];


        $columnsData = var_export($columnsData, true);


        $columnsData = preg_replace_callback("/'trans\(.*\)'/", function ($match) {
            return trim($match[0], "'");
        }, $columnsData);



        $this->stub = str_replace("{select}", "'" . join("', '", $selectData) . "'", $this->stub);
        $this->stub = str_replace("{columns}", $columnsData, $this->stub);
        $this->stub = str_replace("{with}", $withData, $this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("app/Http/Livewire/Admin"))) {
            mkdir(base_path("app/Http/Livewire/Admin"), 0777, true);
        }

        File::put(base_path("app/Http/Livewire/Admin/{$this->model}Datatable.php"), $this->stub);
    }
}
