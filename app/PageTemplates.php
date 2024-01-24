<?php

namespace App;

trait PageTemplates
{
    /*
    |--------------------------------------------------------------------------
    | Page Templates for Backpack\PageManager
    |--------------------------------------------------------------------------
    |
    | Each page template has its own method, that define what fields should show up using the Backpack\CRUD API.
    | Use snake_case for naming and PageManager will make sure it looks pretty in the create/update form
    | template dropdown.
    |
    | Any fields defined here will show up after the standard page fields:
    | - select template
    | - page name (only seen by admins)
    | - page title
    | - page slug
    */

    private function simple_form(){
        $this->crud->addFields([
            [   
            'name' => 'fields',
            'label' => "Choose Fields",
            'type' => 'select2_from_array',
            'options' => ['name'=>'Name','contact'=>'Contact','company'=>'Company','invite_code'=>'Invite Code','hidden_barcode'=>'Hidden Barcode','arrival_time'=>'Arrival Time','photo'=>'Photo','idproof'=>'ID Proof','vaccine'=>'Vaccine Certificate','membership_no'=>'Membership Number','address'=>'Address','city'=>'City'],
            'allows_null' => false,
            'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            'fake' => true,
            'store_in' => 'extras',
            ],
            [   // DateTime
                'name'  => 'default_date_time',
                'label' => 'Event Default Date Time',
                'type'  => 'datetime',
                'fake' => true,
                'store_in' => 'extras'
            ],
            [   // Text
                'name' => 'api',
                'label' => "API",
                'type' => 'text',
                'fake' => true,
                'store_in' => 'extras',
            ]
        ]);
    }
}