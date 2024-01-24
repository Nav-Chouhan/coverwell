<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\CompanyRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class CompanyCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CompanyCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\InlineCreateOperation;
    use \Backpack\ReviseOperation\ReviseOperation;
    use \App\Http\Controllers\Admin\Operations\ExportOperation;
    use \App\Http\Controllers\Admin\Operations\ImportOperation;
    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Company::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/company');
        CRUD::setEntityNameStrings('company', 'companies');
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
                'name' => 'barcode', // The db column name
                'label' => "CompanyCode" // Table column heading
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
        ]);

        $this->crud->addFilter([
            'type'  => 'simple',
            'name'  => 'has_gst',
            'label' => 'GST Cerificate'
          ], 
          false, 
          function() {
            $this->crud->addClause('hasgst');
          } );

        if(!backpack_user()->hasRole('Admin')){
            $this->crud->denyAccess(['delete','revise','export','import']);
        }
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(CompanyRequest::class);
        CRUD::setOperationSetting('contentClass', 'col-md-10');
        //CRUD::setFromDb(); // fields
        CRUD::field('name')->size(4);
        CRUD::field('contact')->size(4);
        CRUD::field('type')->size(4)->type('select2_from_array')
        ->options(['Visitor' => 'Visitor', 'Buyer' => 'Buyer', 'Seller' => 'Seller'])
        ->allows_null(false)
        ->default('Visitor');
        CRUD::field('gstin')->size(4)->label('GSTIN');
        CRUD::field('gst_certificate')->size(4)->type('upload')->upload(true);
        CRUD::field('pan')->size(4)->label('PAN No');
        CRUD::field('address')->size(8);
        CRUD::field('pincode')->size(4);
        CRUD::field('city')->size(4);
        CRUD::field('state')->size(4);
        CRUD::field('country')->size(4);
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
