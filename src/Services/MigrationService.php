<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class MigrationService
{
    private $stub;
    private $model;
    private $modelLower;

    private $hasImage = false;
    private $columns;
    private $metaColumns;
    private $tableName;

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

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/Migration.php.stub");
        return $this->stub;
    }

    public function setStub($stub)
    {
        $this->stub = $stub;
    }

    public function prepareStub()
    {
        $columnsData = "";
        foreach ($this->columns as $column) {
            $columnsData .= "\$table->{$column->db->type}('$column->name')";
            if ($column->db->nullable ?? false) {
                $columnsData .= "->nullable()";
            }
            if (isset($column->db->default)) {
                $columnsData .= "->default({$column->db->default})";
            }
            $columnsData .= ";\n";
        }

        foreach (($this->metaColumns ?? []) as $column) {
            $columnsData .= "\$table->{$column->db->type}('$column->name')";
            if ($column->db->nullable ?? false) {
                $columnsData .= "->nullable()";
            }
            if (isset($column->db->default)) {
                $columnsData .= "->default({$column->db->default})";
            }
            $columnsData .= ";\n";
        }

        $this->stub = str_replace("{tableName}", $this->tableName, $this->stub);
        $this->stub = str_replace("{columns}", $columnsData, $this->stub);
    }

    public function putInFile()
    {
        $migrationName = date("Y_m_d_His") . "_create_" . $this->tableName . "_table.php";

        File::put(base_path("Database/Migrations/$migrationName"), $this->stub);
    }
}
