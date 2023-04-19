<?php

namespace Mohammedmakhlouf78\CorkCrud\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mohammedmakhlouf78\CorkCrud\Services\ControllerService;
use Mohammedmakhlouf78\CorkCrud\Services\Main;

class CrudCommand extends Command
{
    protected $signature = 'crud:make {name}';

    protected $description = 'Install the BlogPackage';

    private $model;

    public function handle(Main $main)
    {
        $this->model = $this->argument('name');
        $crudInfo = json_decode(File::get(__DIR__ . "/{$this->model}.json"));


        $main->run($this->model, $crudInfo, [
            'controller' => ControllerService::class
        ]);

        // dump($stub);
        // dd($crudInfo);

        $this->info("Done Babhy !!!!");
    }

    private function handelController($stub)
    {
        // image service injection
        if ($this->hasImage) {
            $imageServiceNS = config('corkcrud.image_service_fully_qualified_class_name');
            $imageServiceClass = config('corkcrud.image_service_class_name');
            $imageServiceName = lcfirst($imageServiceClass);

            $stub = str_replace("{image_namespace}", $imageServiceNS, $stub);
            $stub = str_replace("{image_service_injection}", "private {$imageServiceClass} \${$imageServiceName}", $stub);
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


        $stub = str_replace("{store}", $storeData, $stub);
        $stub = str_replace("{update}", $updateData, $stub);
        $stub = str_replace("{delete}", $deleteData, $stub);

        dd($stub);
    }

    private function replaceStuff($stub)
    {
        $stub = str_replace("{model}", $this->model, $stub);
        $stub = str_replace("{model_lower}", $this->modelLower, $stub);
        return $stub;
    }

    // use App\Http\Services\ImageService;
    // private ImageService $imageService
}
