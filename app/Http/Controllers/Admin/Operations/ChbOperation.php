<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use App\Mail\SendMail;

trait ChbOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupHostRoutes($segment, $routeName, $controller)
    {
        /* Route::get($segment . '/{id}/host', [
            'as'        => $routeName . '.host',
            'uses'      => $controller . '@host',
            'operation' => 'host',
        ]); */
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupHostDefaults()
    {
        $this->crud->operation('chb', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            // $this->crud->addButton('top', 'print', 'view', 'crud::buttons.print');
            $this->crud->addButton('line', 'chb', 'view', 'crud::buttons.chb');
        });
    }
}
