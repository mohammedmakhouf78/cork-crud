<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class StoreRequestService extends AbstractParent
{

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/StoreRequest.php.stub");
        return $this->stub;
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
    }

    public function putInFile()
    {
        if (!file_exists(base_path("app/Http/Requests/{$this->model}"))) {
            mkdir(base_path("app/Http/Requests/{$this->model}"), 0777, true);
        }

        File::put(base_path("app/Http/Requests/{$this->model}/StoreRequest.php"), $this->stub);
    }
}
