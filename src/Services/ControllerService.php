<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

use Illuminate\Support\Facades\File;

class ControllerService extends AbstractParent
{
    public function runStub()
    {
        $this->stub = File::get(__DIR__ . "/../Console/stubs/Controller.php.stub");
        return $this->stub;
    }

    public function prepareStub()
    {
        if ($this->hasImage) {
            $imageServiceNS = config('corkcrud.image_service_fully_qualified_class_name');
            $imageServiceClass = config('corkcrud.image_service_class_name');
            $imageServiceName = lcfirst($imageServiceClass);

            $this->stub = str_replace("{image_namespace}", $imageServiceNS, $this->stub);
            $this->stub = str_replace("{image_service_injection}", "private {$imageServiceClass} \${$imageServiceName}", $this->stub);
        }



        //store
        $storeData = "";
        $imageStoreData = "";
        $columnsStoreData = "";
        foreach ($this->columns as $column) {
            if ($column->type == "image") {
                $columnNameUpper = strtoupper($column->name);
                $imageStoreData .= "$$column->name = \$this->{$imageServiceName}->uploadImage(\$request->file('$column->name'), $this->model::{$columnNameUpper}_PATH);\n";

                $columnsStoreData .= "'$column->name' => $$column->name,\n";
            } else if ($column->lang == true) {
                $columnsStoreData .= "'$column->name' => [
                    'en' => \$request->{$column->name}_en,
                    'ar' => \$request->{$column->name}_ar
                 ],\n";
            } else {
                $columnsStoreData .= "'$column->name' => \$request->{$column->name},\n";
            }
        }
        $storeData .= $imageStoreData . "\n";
        $storeData .= "$this->model::create([
            $columnsStoreData
        ]);";





        $updateData = "";
        $imagesUpdateData = "";
        $columnsUpdateData = "";
        foreach ($this->columns as $column) {
            if ($column->type == "image") {
                $columnNameCapital = ucwords($column->name);
                $columnNameUpper = strtoupper($column->name);
                $imagesUpdateData .= "\$$column->name = \${$this->modelLower}->getRawOriginal('$column->name');

                if (\$request->file('$column->name')) {
                    \$this->imageService->deleteImage(path: \${$this->modelLower}->get{$columnNameCapital}Path());
        
                    \$$column->name = \$this->imageService->uploadImage(
                        imageObject: \$request->file('$column->name'),
                        path: $this->model::{$columnNameUpper}_PATH
                    );
                } \n";

                $columnsUpdateData .= "'$column->name' => $$column->name,\n";
            } else if ($column->lang == true) {
                $columnsUpdateData .= "'$column->name' => [
                    'en' => \$request->{$column->name}_en,
                    'ar' => \$request->{$column->name}_ar
                 ],\n";
            } else {
                $columnsUpdateData .= "'$column->name' => \$request->{$column->name},\n";
            }
        }
        $updateData .= $imagesUpdateData . "\n";
        $updateData .= "\${$this->modelLower}->update([
            $columnsUpdateData
        ]);";



        $deleteData = "";
        foreach ($this->columns as $column) {
            if ($column->type == "image") {
                $columnNameCapital = ucwords($column->name);
                $deleteData .= "\$this->imageService->deleteImage(path: \${$this->modelLower}->get{$columnNameCapital}Path());\n";
            }
        }
        $deleteData .= "\${$this->modelLower}->delete();";




        $relationData = "";
        $compact = "";
        foreach ($this->relations as $relation) {
            $relationVariable = $this->prepareRelationName($relation->name);
            $relationModel = ucfirst($relationVariable);
            $modelsNamespace = config('corkcrud.models_name_space');

            $relationData .= "\${$relationVariable}Collection = \\$modelsNamespace\\$relationModel::forSelect()->get();\n";
            $compact .= "'{$relationVariable}Collection',";
        }
        $compact = rtrim($compact, ",");


        $this->stub = str_replace("{store}", $storeData, $this->stub);
        $this->stub = str_replace("{update}", $updateData, $this->stub);
        $this->stub = str_replace("{delete}", $deleteData, $this->stub);
        $this->stub = str_replace("{relation}", $relationData, $this->stub);
        $this->stub = str_replace("{compact}", $compact, $this->stub);
    }

    public function putInFile()
    {
        File::put(base_path(config('corkcrud.controllers_path') . "/{$this->model}Controller.php"), $this->stub);
    }
}
