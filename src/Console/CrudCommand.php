<?php

namespace Mohammedmakhlouf78\CorkCrud\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Mohammedmakhlouf78\CorkCrud\Services\ControllerService;
use Mohammedmakhlouf78\CorkCrud\Services\CreateViewService;
use Mohammedmakhlouf78\CorkCrud\Services\DataTableService;
use Mohammedmakhlouf78\CorkCrud\Services\EditViewService;
use Mohammedmakhlouf78\CorkCrud\Services\IndexViewService;
use Mohammedmakhlouf78\CorkCrud\Services\Main;
use Mohammedmakhlouf78\CorkCrud\Services\MigrationService;
use Mohammedmakhlouf78\CorkCrud\Services\ModelService;
use Mohammedmakhlouf78\CorkCrud\Services\RouteService;
use Mohammedmakhlouf78\CorkCrud\Services\StoreRequestService;
use Mohammedmakhlouf78\CorkCrud\Services\UpdateRequestService;

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
            'controller' => ControllerService::class,
            'model' => ModelService::class,
            'migration' => MigrationService::class,
            'storeRequest' => StoreRequestService::class,
            'updateRequest' => UpdateRequestService::class,
            'indexView' => IndexViewService::class,
            'editView' => EditViewService::class,
            'createView' => CreateViewService::class,
            'dataTable' => DataTableService::class,
            'route' => RouteService::class,
        ]);

        $this->info("Done Babhy !!!!");
    }
}
