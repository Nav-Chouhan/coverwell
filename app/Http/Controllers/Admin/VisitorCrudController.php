<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\VisitorRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use App\Models\VisitorCategory;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;
use App\Models\Profession;
use App\Models\Location;
use App\Models\Visitor;
use App\User;
use Spatie\Activitylog\Models\Activity;


/**
 * Class VisitorCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VisitorCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\FetchOperation;
    use \App\Http\Controllers\Admin\Operations\PrintOperation;
    // use \App\Http\Controllers\Admin\Operations\HostOperation;
    use \App\Http\Controllers\Admin\Operations\ChbOperation;
    use \App\Http\Controllers\Admin\Operations\ReportOperation;

    use \Backpack\ReviseOperation\ReviseOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \App\Http\Controllers\Admin\Operations\ExportOperation;
    use \App\Http\Controllers\Admin\Operations\ImportOperation;
    use \App\Http\Controllers\Admin\Operations\BulkDeliveryOperation;
    //use \App\Http\Controllers\Admin\Operations\SMSOperation;


    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Visitor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/visitor');
        CRUD::setEntityNameStrings('visitor', 'visitors');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        $daysDetails = [
            '3' => [
                '2023-07-06' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
                '2023-07-07' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
                '2023-07-08' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
            ],
            '2' => [
                '2023-07-06' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
                '2023-07-07' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
                '2023-07-08' => [
                    'tentative' => 0,
                    'confirm' => 0
                ],
            ]
        ];
     

        $this->data['widgets']['before_content'] = [
            [
                'type' => 'iframe',
            ],
        ];


        CRUD::column('barcode');
        CRUD::column('name');
        CRUD::column('photo')->type('image');
        CRUD::column('ticket_invite_code')->label('Invite');
        //CRUD::column('photo');
        //CRUD::column('idproof');
        CRUD::column('contact');
        // CRUD::column('country');
        //CRUD::column('email');
        CRUD::column('company_barcode')->type('select')->label('Company')->entity('company')->attribute('name')->model(App\Models\Company::class);
        //CRUD::column('city');
        CRUD::column('category_id')->type('select')->label('Category')->entity('category')->attribute('name')->model(App\Models\VisitorCategory::class);
        CRUD::column('hotel_id')->type('select')->label('Hotel')->entity('hotel')->attribute('name')->model(App\Models\Hotel::class);
 
        CRUD::column('last_movement');
        CRUD::column('registered_on');
        CRUD::column('src');

        if (!backpack_user()->hasRole('Admin')) {
            $this->crud->denyAccess(['delete', 'revise', 'export', 'import', 'bulkdelivery']);
        }
        //filters
        if (backpack_user()->hasRole('Admin')) {
            $this->crud->addFilter([
                'name' => 'category',
                'type' => 'select2_multiple',
                'label' => 'Category'
            ], function () {
                return VisitorCategory::all()->pluck('name', 'id')->toArray();
            }, function ($values) {

                foreach (json_decode($values) as $key => $value) {
                    $category_ids[] = $value;
                }
                $this->crud->addClause('whereIn', 'category_id', $category_ids);
            });
            $this->crud->addFilter([
                'name' => 'visitor',
                'type' => 'select2_multiple',
                'label' => 'Visitor'
            ], function () {
                return [
                    1 => 'Registered',
                    2 => 'Verified',
                    3 => 'Printed',
                    0 => 'Not Printed',
                    4 => 'Came Today',
                    5 => 'Knockout/Threat',
                    6 => 'Arrival',
                    7 => 'Not Came'
                ];
            }, function ($values) { // if the filter is active
                foreach (json_decode($values) as $key => $value) {
                    if ($value == '1') {
                        $this->crud->addClause('where', 'registered_on', '!=', null);
                    }
                    if ($value == '2') {
                        $this->crud->addClause('where', 'verified_on', '!=', null);
                    }
                    if ($value == '3') {
                        $this->crud->addClause('where', 'printed_on', '!=', null);
                    }
                    if ($value == '4') {
                        $this->crud->addClause('whereDate', 'last_movement', Carbon::today());
                    }
                    if ($value == '7') {
                        $this->crud->addClause('where', 'last_movement', null);
                    }
                    if ($value == '5') {
                        $this->crud->addClause('where', 'threat', true);
                    }
                    if ($value == '0') {
                        $this->crud->addClause('where', 'printed_on', null);
                    }
                    if ($value == '6') {
                        $this->crud->addClause('where', 'arival', '!=', null);
                    }
                }
            });
            $this->crud->addFilter([
                'name' => 'hotel',
                'type' => 'select2_multiple',
                'label' => 'Hotel'
            ], function () {
                return Hotel::all()->pluck('name', 'id')->toArray();
            }, function ($values) {
                foreach (json_decode($values) as $key => $value) {
                    $hotel_ids[] = $value;
                }
                $this->crud->addClause('whereIn', 'hotel_id', $hotel_ids);
            });
            $this->crud->addFilter([
                'name' => 'room_number',
                'type' => 'text',
                'label' => 'Room Number'
            ], function () {
                return [];
            }, function ($values) {
                $this->crud->addClause('where', 'room_number', 'like', '%' . $values . '%');
            });
            $this->crud->addFilter([
                'name' => 'travel_by',
                'type' => 'select2_multiple',
                'label' => 'Tavel By'
            ], function () {
                return [
                    'Flight' => 'Flight',
                    'Train' => 'Train',
                    'By Road' => 'By Road'
                ];
            }, function ($values) {
                foreach (json_decode($values) as $key => $value) {
                    $travel_by[] = $value;
                }
                $this->crud->addClause('whereIn', 'travel_by', $travel_by);
            });
            $this->crud->addFilter([
                'name' => 'national_nri',
                'type' => 'select2',
                'label' => 'National Or Nri'
            ], function () {
                return [
                    1 => 'National',
                    2 => 'Nri'
                ];
            }, function ($value) {
                if ($value == '1') {
                    $this->crud->addClause('where', 'country', null);
                }
                if ($value == '2') {
                    $this->crud->addClause('where', 'country', '!=', null);
                }
            });
            $this->crud->addFilter([
                'name' => 'checkin_date',
                'type' => 'date',
                'label' => 'CheckIn Date'
            ], function () {
                return [];
            }, function ($value) {
                $this->crud->addClause('where', DB::raw("DATE_FORMAT(check_in_date, '%Y-%m-%d')"), '=', $value);
            });
            $this->crud->addFilter([
                'name' => 'checkout_date',
                'type' => 'date',
                'label' => 'CheckOut Date'
            ], function () {
                return [];
            }, function ($value) {
                $this->crud->addClause('where', DB::raw("DATE_FORMAT(check_out_date, '%Y-%m-%d')"), '=', $value);
            });
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
        CRUD::setValidation(VisitorRequest::class);
        CRUD::setOperationSetting('contentClass', 'col-md-12 bold-labels');

        CRUD::field('identity_separator')->type('custom_html')->value('<h5>#Identity Information</h5><hr>');
        CRUD::field('name')->type('text')->size(3);
 
        $this->crud->addField([
            'type' => "relationship",
            'name' => 'company', // the method on your model that defines the relationship
            'ajax' => true,
            'entity' => 'company',
            'wrapper' => ['class' => 'form-group col-md-3'],
            'inline_create' => [
                'modal_class' => 'modal-dialog modal-xl', // use modal-sm, modal-lg to change width
            
            ]
        ]);
        CRUD::field('category_id')->type('relationship')->size(3);
        CRUD::field('extra')->type('text')->size(1);
        CRUD::field('verified_on')->type('checkbox')->size(1);
        CRUD::field('threat')->type('checkbox')->size(1);
        CRUD::field('break1')->type('custom_html')->value('<div class="w-100"></div>');
        CRUD::field('contact')->size(3);
        CRUD::field('email')->type('email')->size(3);
        CRUD::field('photo')->type('cam_image')->aspect_ratio(0.75)->crop(true)->src(null)->filename(null)->size(6);

        $itm = $this->crud->getCurrentEntry();
        CRUD::field('payment_separator')->type('custom_html')->value('<h5>#Payment:'.($itm?$itm->payment_status:'').'</h5><hr>');
        CRUD::field('receipt_no')->type('text')->size(3);
        CRUD::field('amount')->type('text')->size(3);

        CRUD::field('address_separator')->type('custom_html')->value('<h5>#Address Information</h5><hr>');
        CRUD::field('address')->type('text')->size(3);
        CRUD::field('pincode')->size(3);
        CRUD::field('city')->size(3);
        CRUD::field('break2')->type('custom_html')->value('<div class="w-100"></div>');
        CRUD::field('state')->size(3);
        CRUD::field('country')->size(3);
        CRUD::field('idproof')->size(3)->type('upload')->upload(true);
        CRUD::field('idproof_back')->size(3)->type('upload')->upload(true);

        if (backpack_user()->can('hospitality')) {
            CRUD::field('hospitality_separator')->type('custom_html')->value('<h5>#Travel Info</h5><hr>');
            CRUD::field('check_in_date')->size(3);
            CRUD::field('check_out_date')->size(3);
            CRUD::field('departure_city')->label("Arrival City")->type("text")->size(3);
            CRUD::addField([   // select_from_array
                'name' => 'travel_by',
                'class' => 'travel_by',
                'label' => "Travel By",
                'type' => 'select2_from_array',
                'options' => [
                    "Select Status",
                    "Flight" => "Flight",
                    "Train" => "Train",
                    "By Road" => "By Road"
                ],
                'allows_null' => false,
                'default' => 'one',
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]);
            CRUD::field('departure_pnr')->label("Arrival PNR")->size(3);
            CRUD::field('departure_date_time')->size(3);
            CRUD::field('flight_train_name')->label("Flight/Train Name")->size(3);
            CRUD::field('flight_train_number')->label("Flight/Train Name")->size(3);
            CRUD::field('travel_ticket')->size(3)->type('upload')->upload(true);
            /* CRUD::addField([   // Upload
                'name' => 'travel_ticket',
                'label' => 'Travel Ticket',
                'type' => 'upload',
                'upload' => true,
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'disk' => 'uploads', // if you store files in the /public folder, please omit this; if you store them in /storage or S3, please specify it;
                // optional:
                // 'temporary' => 10 // if using a service, such as S3, that requires you to make temporary URLs this will make a URL that is valid for the number of minutes specified
            ]);
            CRUD::addField([   // select_from_array
                'name' => 'travel_by',
                'class' => 'travel_by',
                'label' => "Travel By",
                'type' => 'select2_from_array',
                'options' => [
                    "Select Status",
                    "flight" => "By Flight",
                    "train" => "By Train"
                ],
                'allows_null' => false,
                'default' => 'one',
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]);
            // CRUD::field('iata_airline');
            CRUD::addField([   // select_from_array
                'name' => 'iata_airline',
                'label' => "Airline",
                'type' => 'select2_from_array',
                'options' => config('global.airlineIATACodes'),
                'allows_null' => false,
                'default' => 'one',
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]);
            CRUD::addField([   // select_from_array
                'name' => 'iata_airport',
                'label' => "Departure Airport",
                'type' => 'select2_from_array',
                'options' => config('global.airportIATACodes'),
                'allows_null' => false,
                'default' => 'one',
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]);
            // CRUD::field('iata_airport');
            CRUD::field('arrival_city_id')->size(3);
            CRUD::field('departure_city_id')->size(3);
            CRUD::field('check_in_date')->size(3);
            CRUD::field('check_out_date')->size(3);
            CRUD::field('arrival_date_time')->size(3);
            CRUD::field('departure_date_time')->size(3);
            CRUD::field('hotel_id')->size(3);
            CRUD::field('room_number')->size(3);
        
            CRUD::addField([   // select_from_array
                'name' => 'hospitality_status',
                'label' => "Status",
                'type' => 'select2_from_array',
                'options' => config('global.hospitalityStatus'),
                'allows_null' => false,
                'default' => 'one',
                'wrapper' => ['class' => 'form-group col-md-3'],
                // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            ]); */
        }
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

    protected function fetchCompany()
    {
        return $this->fetch(\App\Models\Company::class);
    }

    public function scanVisitor()
    {
        $this->data['title'] = 'Scan';
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin') => backpack_url('dashboard'),
            'Scan' => false,
        ];
        return view('backpack::scan', $this->data);
    }

    public function scan($barcode)
    {
        $credentials = \Request::only('email', 'password');
        if (\Request::isMethod('post')) {
            if (\Auth::once($credentials)) {
                $visitor = \Auth::user()->scanVisitor($barcode);
                $error = $visitor['error'];
                if ($error) {
                    return $error;
                }
                $visitor['entry'] = $visitor['visitor'];
                return view("crud::operations.print", $visitor);
            }
            return "Invalid Credentials!!";
        }
        $visitor = backpack_auth()->user()->scanVisitor($barcode);
        $error = $visitor['error'];
        $visitor = $visitor['visitor'];
        return [
            'barcode' => $visitor->barcode,
            'photo' => $visitor->getPhoto64(),
            'name' => $visitor->name,
            'company' => ($visitor->company ? $visitor->company->name . ' - ' : '') . $visitor->city,
            'category' => $visitor->category->name,
            'location' => backpack_auth()->user()->location->name . '_' . backpack_auth()->user()->direction . " " . Carbon::now()->format('g:i A'),
            'error' => $error
        ];
    }

    protected function setupShowOperation()
    {
        $this->data['widgets']['before_content'] = [
            [
                'type' => 'iframe',
            ],
        ];
        //$this->crud->set('show.setFromDb', true);
        $this->crud->addColumns([
            [
                'name' => 'photo',
                'type' => 'image',
                //'prefix' => 'uploads/photos/',
                // 'height' => '30px',
                // 'width' => '30px',
            ],
            [
                'name' => 'idproof',
                'type' => 'image',
            ],
            [
                'name' => 'idproof_back',
                'type' => 'image',
            ],
            [
                'name' => 'vaccine',
                'type' => 'image',
            ],
            ['name' => 'barcode'],
            ['name' => 'name'],
            [
                'name' => 'contact',
                'type' => 'phone',
            ],
            [
                'label' => "Company",
                'type' => "select",
                'name' => 'company_barcode',
                'entity' => 'company',
                'attribute' => "name",
                'model' => "\App\Models\Company",
            ],
            [
                'label' => "Category",
                'type' => "select",
                'name' => 'category_id',
                'entity' => 'category',
                'attribute' => "name",
                'model' => "\App\Models\VisitorCategory",
            ],
            [
                'label' => "Hotel",
                'type' => "select",
                'name' => 'hotel_id',
                'entity' => 'hotel',
                'attribute' => "name",
                'model' => "\App\Models\Hotel",
            ],
            ['name' => 'city'],
            [
                'type' => "select",
                'name' => 'current_location',
                'entity' => 'location',
                'attribute' => "name",
                'model' => "\App\Models\Location",
            ],
            ['name' => 'last_movement'],
            [
                'name'  => 'activities_json',
                'label' => 'Logs',
                'type'  => 'table',
                'columns' => [
                    'log_name'        => 'log_name',
                    'description'        => 'Description',
                ]
            ]
        ]);
    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();
        // update the row in the db

        $data_r = $this->crud->getStrippedSaveRequest();
        $payment_categories = explode(',',\Config::get('settings.payment_categories'));
        if (in_array($request->get("category_id"), $payment_categories)) {
            if($request->receipt_no && ($request->amount != null && $request->amount != '')){
                $data_r['payment_status'] = 'Received';
            }
        }
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $data_r
        );
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        $this->crud->performSaveAction($item->getKey());

        if ($request->save_action == "save_and_new") {
            $page_url = url('admin/visitor/create');
        } else {
            $page_url = url('admin/visitor');
        }
        // $url = url("admin/visitor/" . $item->getKey() . "/print");
        // return "<script type='text/javascript'>window.open( '$url' );window.location.replace('$page_url');</script>";
        return "<script type='text/javascript'>window.location.replace('$page_url');</script>";
    }

    public function store()
    {
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        $data_r = $this->crud->getStrippedSaveRequest();
        $payment_categories = explode(',',\Config::get('settings.payment_categories'));
        if (in_array($request->get("category_id"), $payment_categories)) {
            if($request->receipt_no && $request->amount){
                $data_r['payment_status'] = 'Received';
            }
        }
        // insert item in the db
        $item = $this->crud->create($data_r);
        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        $this->crud->performSaveAction($item->getKey());

        if ($request->save_action == "save_and_new") {
            $page_url = url('admin/visitor/create');
        } else {
            $page_url = url('admin/visitor');
        }
        $url = url("admin/visitor/" . $item->getKey() . "/print");
        return "<script type='text/javascript'>window.open( '$url' );window.location.replace('$page_url');</script>";
    }
}
