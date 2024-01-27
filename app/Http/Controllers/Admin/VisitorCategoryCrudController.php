<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VisitorCategoryRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class VisitorCategoryCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VisitorCategoryCrudController extends CrudController
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
        CRUD::setModel(\App\Models\VisitorCategory::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/visitor-category');
        CRUD::setEntityNameStrings('visitor category', 'visitor categories');
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
                'name' => 'id', // The db column name
                'label' => "Category Id" // Table column heading
            ],
            [
                'name' => 'name', // The db column name
                'label' => "Name" // Table column heading
            ],
          [
                'name' => "total_visitors",
                'label' => "Total",
                'type' => "model_function",
                'function_name' => 'visitors',
                'function_parameters' => [true],
            ], 
            [
                'name' => 'img', // The db column name
                'label' => "Background", // Table column heading
                'type' => 'image',
                // 'prefix' => 'folder/subfolder/',
            ],
        ]);
        if (!$this->crud->has('order')) {
            $this->crud->orderBy('lft');
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupReorderOperation()
    {

        $this->crud->set('reorder.label', 'name');
        $this->crud->set('reorder.max_level', 1);

        // $this->crud->disableReorder();
        // $this->crud->isReorderEnabled();
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
   protected function setupCreateOperation()
    {
        $this->crud->setValidation(VisitorCategoryRequest::class);
        $this->crud->addFields([
            [ // Text
                'name' => 'name',
                'label' => "Name",
                'type' => 'text',

            ],
            [   // Upload
                'name' => 'img',
                'label' => 'Background Image',
                'type' => 'browse'
            ],
        ]);
    }

     protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
