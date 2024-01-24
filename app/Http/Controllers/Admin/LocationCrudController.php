<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LocationRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LocationCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class LocationCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    public function setup()
    {
        $this->crud->setModel('App\Models\Location');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/location');
        $this->crud->setEntityNameStrings('location', 'locations');
        
    }

    protected function setupReorderOperation()
    {   
        $this->crud->set('reorder.label', 'name'); 
        $this->crud->set('reorder.max_level', 2);
    }

    protected function setupListOperation()
    {
        // TODO: remove setFromDb() and manually define Columns, maybe Filters
        $this->crud->setFromDb();
        $this->crud->removeColumns(['parent_id', 'lft','rgt','depth']);        
        
        $this->crud->addColumn([    // Select2Multiple = n-n relationship (with pivot table)
             'label'     => "Filter(Allowed)",
             'type'      => 'select_multiple',
             'name'      => 'categories', // the method that defines the relationship in your Model
             'entity'    => 'categories', // the method that defines the relationship in your Model
             'attribute' => 'name', // foreign key attribute that is shown to user
             'model'     => "App\Models\VisitorCategory", // foreign key model
        ]);
        $this->crud->addColumn([
            'name' => "People Inside",
            'name' => "People Inside",
            'type' => "model_function",
           'function_name' => 'visitors',
            'function_parameters' => [true],
        ]);
        if (!$this->crud->has('order')) {
            $this->crud->orderBy('lft');
        }
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(LocationRequest::class);
        $this->crud->setFromDb();
        $this->crud->removeFields(['parent_id', 'lft','rgt','depth']);
        $this->crud->addField([    // Select2Multiple = n-n relationship (with pivot table)
             'label'     => "Filter(Allowed)",
             'type'      => 'select2_multiple',
             'name'      => 'categories', // the method that defines the relationship in your Model
             'entity'    => 'categories', // the method that defines the relationship in your Model
             'attribute' => 'name', // foreign key attribute that is shown to user
             'model'     => "App\Models\VisitorCategory", // foreign key model
             'pivot'     => true, // on create&update, do you need to add/delete pivot table entries?
             'select_all' => true,
             /*'options'   => (function ($query) {
                 return $query->orderBy('name', 'ASC')->where('depth', 1)->get();
             }),*/
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
