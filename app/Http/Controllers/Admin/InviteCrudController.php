<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\InviteRequest;
use App\Models\Invite;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Clarkeash\Doorman\Facades\Doorman;
use Carbon\Carbon;

/**
 * Class InviteCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InviteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Invite::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/invite');
        CRUD::setEntityNameStrings('invite', 'invites');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->setFromDb();
        $this->crud->enableExportButtons();
        $this->crud->removeColumns(['for']);        
        $this->crud->addColumn([
            'name' => 'contact',
            'label' => 'contact',
        ])->beforeColumn('code');
        $this->crud->addColumn([
            'name' => 'name',
            'label' => 'Name(For)',
        ])->beforeColumn('contact');
        $this->crud->addColumn([
            'name' => 'visitors',
            'type'      => 'relationship_count',
            'wrapper'   => [
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('visitor?category_id='.$entry->getKey());
                },
            ],
        ])->beforeColumn('valid_until');

        CRUD::column('category_id')->type('select')->label('Category')->entity('category')->attribute('name')->model(App\Models\VisitorCategory::class);
    }


    protected function setupCreateOperation()
    {
        $this->crud->setValidation(InviteRequest::class);
        $this->crud->addFields([
            /*[   
            'name' => 'times',
            'label' => 'Number Of Codes to Generate',
            'type' => 'number',
            'default'   => 1,
            ],*/
            [
                'name' => 'name',
                'label' => 'Name(FOR)',
            ],
            [
                'name' => 'contact',
                'label' => 'Contact',
            ],
            [
                'name' => 'max',
                'label' => 'Max Times Can be used',
                'type' => 'number',
                'default'   => 1,
            ],
            [
                'name' => 'uses',
                'label' => 'Uses',
                'type' => 'number',
                'default'   => 0,
            ],
            [
                'name' => 'valid_until',
                'label' => 'Valid till',
                'type' => 'date'
            ],
        ]);
        CRUD::field('category_id')->type('relationship');


        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        //$this->crud->removeFields(['times']);
    }

    public function store()
    {
        
        $this->crud->hasAccessOrFail('create');
        $request = $this->crud->validateRequest();

        $code = Doorman::generate();
        $code
        //->max($request->max)
        ->uses($request->max)
        //->for($request->for)
        ->expiresOn(Carbon::createFromFormat('Y-m-d', $request->valid_until));
        $invites = $code->make();

        
        $item = $this->crud->update($invites[0]->id,$this->crud->getStrippedSaveRequest());


        \Alert::success(trans('backpack::crud.insert_success'))->flash();
        // save the redirect choice for next time
        $this->crud->setSaveAction();
        //return $this->crud->performSaveAction($item->getKey());
        return $this->crud->performSaveAction(null);
    }
}
