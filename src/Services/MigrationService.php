<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class MigrationService extends AbstractParent
{
    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/Migration.php.stub");
        return $this->stub;
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

        File::put(base_path("database/migrations/$migrationName"), $this->stub);
    }
}
