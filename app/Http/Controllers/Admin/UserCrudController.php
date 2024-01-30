<?php

namespace App\Http\Controllers\Admin;


use Backpack\PermissionManager\app\Http\Controllers\UserCrudController as CrudController;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;

class UserCrudController extends CrudController
{
    use \App\Http\Controllers\Admin\Operations\ExportOperation;
    use \App\Http\Controllers\Admin\Operations\ImportOperation;
    
    
    public function setupListOperation()
    {
        parent::setupListOperation();
        $this->crud->addColumns([
            [
                'type' => 'select',
                'name' => 'location_id',
                'entity' => 'location',
                'attribute' => 'name',
                'model' => "App\Models\Location",
             ],
             [
                 'name' => 'direction',
             ]
        ]);

        
    }

    protected function addUserFields()
    {
        parent::addUserFields();
        $this->crud->addFields([
            [
                'type' => 'select',
                'name' => 'location_id',
                'entity' => 'location',
                'attribute' => 'name',
                'model' => "App\Models\Location",
             ],
             [
                'name' => 'direction',
                'label' => "Direction",
                'type' => 'select_from_array',
                'options' => ['In'=>'In','Out'=>'Out'],
                'allows_null' => true,
                'default' => null,

            ]
        ]);
    }
}