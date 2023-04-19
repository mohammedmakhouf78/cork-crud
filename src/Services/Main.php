<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

class Main
{

    private $model;
    private $modelLower;

    private $hasImage = false;
    private $columns;

    public function run(string $model, object $crudInfo, array $services)
    {
        $this->model = $model;
        $this->modelLower = lcfirst($this->model);
        $this->hasImage = $crudInfo->hasImages;
        $this->columns = $crudInfo->columns;

        foreach ($services as $service) {
            $service = new $service();
            $stub = $service->runStub();

            $stub = str_replace("{model}", $this->model, $stub);
            $stub = str_replace("{model_lower}", $this->modelLower, $stub);

            $service->setStub($stub);



            $service->run($model, $crudInfo);
        }
    }
}
