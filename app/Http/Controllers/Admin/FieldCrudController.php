<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FieldRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class FieldCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class FieldCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Field::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/field');
        CRUD::setEntityNameStrings('field', 'fields');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
  protected function setupListOperation()
    {
        $this->crud->addColumns([
            [
               'name' => 'name', // The db column name
               'label' => "Tag Name" // Table column heading
            ],
            [
               'name' => 'readonly', // The db column name
               'label' => "Readonly", // Table column heading
               'type' => 'check'
            ],
            [
               'name' => 'visibility', // The db column name
               'label' => "Visibility", // Table column heading
               'type' => 'check'
            ],
            [
               'name' => 'required', // The db column name
               'label' => "Required", // Table column heading
               'type' => 'check'
            ],
            [
               'name' => 'active', // The db column name
               'label' => "Main Tab", // Table column heading
               'type' => 'check'
            ],
            ]);
            $this->crud->orderBy('lft');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
   protected function setupCreateOperation()
    {
        CRUD::setValidation(FieldRequest::class);

        $this->crud->addFields([
            [
                    'name' => 'name',
                    'label' => "Name",
                    'type' => 'text',
                   /*  'attributes' => [
                        'readonly'=>'readonly',
                    ], */
                  
            ],
            [   // Checkbox
                'name' => 'readonly',
                'label' => 'Readonly',
                'type' => 'checkbox'
            ],
            [   // Checkbox
                'name' => 'visibility',
                'label' => 'Visibility',
                'type' => 'checkbox'
            ],
            [   // Checkbox
                'name' => 'required',
                'label' => 'Required',
                'type' => 'checkbox'
            ],
            [   // Checkbox
                'name' => 'active',
                'label' => 'Main Tab',
                'type' => 'checkbox'
            ],
        ]);

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
    }
}
