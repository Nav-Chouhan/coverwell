<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfessionRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Visitor;
use App\Models\VisitorCategory;

/**
 * Class ProfessionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read CrudPanel $crud
 */
class ProfessionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;

    protected $category = false;
    protected $city = false;
    protected $visited_year = false;

    public function setup()
    {
        $this->crud->setModel('App\Models\Profession');
        $this->crud->setRoute(config('backpack.base.route_prefix') . '/profession');
        $this->crud->setEntityNameStrings('profession', 'professions');
    }

    protected function setupListOperation()
    {
      $this->crud->enableExportButtons();
        $this->crud->addColumns([
                [
                   'name' => 'id', // The db column name
                ],
                [
                   'name' => 'name', // The db column name
                   'label' => "Name" // Table column heading
                ]
        ]);
    }

    protected function setupReorderOperation()
    {   

        $this->crud->set('reorder.label', 'name'); 
        $this->crud->set('reorder.max_level', 1);

        // $this->crud->disableReorder();
        // $this->crud->isReorderEnabled();
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(ProfessionRequest::class);

        // TODO: remove setFromDb() and manually define Fields
        //$this->crud->setFromDb();
        $this->crud->addFields([
                [ // Text
                    'name' => 'name',
                    'label' => "Name",
                    'type' => 'text',
                  
                ]
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
