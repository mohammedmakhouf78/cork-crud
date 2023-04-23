<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class IndexViewService extends AbstractParent
{

    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/IndexView.php.stub");
        return $this->stub;
    }

    public function prepareStub()
    {
        $tableName = $this->replaceUpperWith("productDetail", '-');
        $this->stub = str_replace("{datatable_name}", $tableName, $this->stub);
    }

    public function putInFile()
    {
        if (!file_exists(base_path("resources/views/admin/pages/{$this->modelLower}"))) {
            mkdir(base_path("resources/views/admin/pages/{$this->modelLower}"), 0777, true);
        }

        File::put(base_path("resources/views/admin/pages/{$this->modelLower}/index.blade.php"), $this->stub);
    }
}
