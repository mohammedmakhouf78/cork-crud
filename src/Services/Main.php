<?php

namespace Mohammedmakhlouf78\CorkCrud\Services;

class Main
{
    public function run(string $model, object $crudInfo, array $services)
    {
        $modelLower = lcfirst($model);

        foreach ($services as $service) {
            $service = new $service();
            $stub = $service->runStub();

            $stub = str_replace("{model}", $model, $stub);
            $stub = str_replace("{model_lower}", $modelLower, $stub);

            $service->setStub($stub);

            $service->run($model, $crudInfo);
        }
    }
}
