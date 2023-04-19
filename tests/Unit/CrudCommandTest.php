<?php

namespace Mohammedmakhlouf78\CorkCrud\Tests\Unit;

use Illuminate\Support\Facades\Artisan;
use Mohammedmakhlouf78\CorkCrud\Tests\TestCase;

class TestCrudCommand extends TestCase
{
    public function test_command()
    {
        Artisan::call("crud:make Brand");
    }
}
