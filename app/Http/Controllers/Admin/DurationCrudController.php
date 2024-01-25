<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\DurationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

class DurationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Duration::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/duration');
        CRUD::setEntityNameStrings('duration', 'durations');
        $this->crud->orderBy('begin');
    }

    protected function setupListOperation()
    {
        CRUD::setFromDb(); // columns

    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(DurationRequest::class);
        CRUD::setFromDb(); // fields
        $this->crud->addFields([
            [   // date_range
                'name'  => 'begin,end', // db columns for start_date & end_date
                'label' => 'Duration',
                'type'  => 'date_range',

                // OPTIONALS
                // default values for start_date & end_date
                'default'            => [Carbon::now(), Carbon::now()],
                // options sent to daterangepicker.js
                'date_range_options' => [
                    'drops' => 'down', // can be one of [down/up/auto]
                    'timePicker' => true,
                    'locale' => ['format' => 'DD/MM/YYYY HH:mm']
                ]
            ]
        ]);
        $this->crud->removeFields(['begin', 'end']);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
