<?php

namespace App\Http\Controllers\Admin;

use Artisan;
use Exception;
use League\Flysystem\Adapter\Local;
use Log;
use Illuminate\Support\Facades\Hash;
use Response;
use Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use App\User;
use App\Models\Visitor;
use App\Models\Company;
use App\Models\VisitorCategory;
use App\Models\Location;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Charts\Chartjs;
use Spatie\Activitylog\Models\Activity;
use Backpack\CRUD\app\Http\Controllers\AdminController as Controller;

class AdminController extends Controller
{
    public function operations()
    {
        $this->data['title'] = "Operations";
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin')     => backpack_url('dashboard'),
            trans('backpack::base.dashboard') => false,
        ];
        return view(backpack_view('operations'), $this->data);
    }

    public function importVisitors(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'import_file' => 'required|file',
        ]);
        if ($validator->fails()) {
            \Alert::warning($validator->errors()->first())->flash();
            return back();
        }

        //ini_set('max_execution_time', 600);
        $path = $request->file('import_file')->getRealPath();
        (new FastExcel)->import($path, function ($line) {
            try {
                foreach ($line as $i => $value) {

                    if ($value === "" || $value === null || $value === "null" | $value === "NULL") // $line[$i] = null;
                        unset($line[$i]);
                }
                if (isset($line['professions'])) {
                    $professions = $line['professions'];
                    unset($line['professions']);
                }
                if (isset($line['additional_access'])) {
                    $additional_access = $line['additional_access'];
                    unset($line['additional_access']);
                }

                $only_company = [
                    'name' => isset($line['company']) ? $line['company'] : null,
                    'city' => isset($line['city']) ? $line['city'] : null,
                ];
                $company = Company::updateOrCreate($only_company, $only_company);
                $line['company_barcode'] = $company->barcode;

                $visitor = Visitor::updateOrCreate([
                    'barcode' => $line['barcode']
                ], $line);
                if (isset($professions))
                    $visitor->professions()->sync(explode(',', $professions));
                if (isset($additional_access))
                    $visitor->additionalAccess()->sync(explode(',', $additional_access));

                $str = "Imported by :causer.name on " . Carbon::now();
                activity('import')
                    ->performedOn($visitor)
                    ->causedBy(backpack_user())
                    ->log($str);
                return $visitor;
            } catch (Exception $e) {
                Log::error($e);
                \Alert::error($e->getMessage())->flash();
            }
        });
        \Alert::success("Successfully Imported")->flash();


        return back();
    }

    public function report($type, $print = 'chart')
    {

        switch ($type) {
            case 'printed':
                $this->data['title'] = "Printed Badges";
                $this->data['widgets']['before_content'] = [[
                    'type' => $print,
                    'wrapperClass' => 'col-md-12',
                    'controller' => \App\Http\Controllers\Admin\Charts\PrintedVisitorsChartController::class,
                    'content' => [
                        'header' => "<b>" . $this->data['title'] . "</b>", // optional
                        //'body' => "<b>$type</b>" // optional
                    ]
                ]];
                break;
            case 'registered':
                $this->data['title'] = "People Registered";
                $this->data['widgets']['before_content'] = [[
                    'type' => $print,
                    'wrapperClass' => 'col-md-12',
                    'controller' => \App\Http\Controllers\Admin\Charts\RegisteredVisitorsChartController::class,
                    'content' => [
                        'header' => "<b>" . $this->data['title'] . "</b>", // optional
                        //'body' => "<b>$type</b>" // optional
                    ]
                ]];
                break;
            case 'inside':
                $this->data['title'] = "Visitors Inside";
                $this->data['widgets']['before_content'] = [[
                    'type' => $print,
                    'wrapperClass' => 'col-md-12',
                    'controller' => \App\Http\Controllers\Admin\Charts\InsideVisitorsChartController::class,
                    'content' => [
                        'header' => "<b>" . $this->data['title'] . "</b>", // optional
                        //'body' => "<b>$type</b>" // optional
                    ]
                ]];
                break;
            case 'footfall':
                $this->data['title'] = "People Visited";
                $this->data['widgets']['before_content'] = [[
                    'type' => $print,
                    'wrapperClass' => 'col-md-12',
                    'controller' => \App\Http\Controllers\Admin\Charts\FootfallVisitorsChartController::class,
                    'content' => [
                        'header' => "<b>" . $this->data['title'] . "</b>", // optional
                        //'body' => "<b>$type</b>" // optional
                    ]
                ]];
                break;
            default:
                abort(404);
                break;
        }
        $this->data['breadcrumbs'] = [
            trans('backpack::crud.admin')     => backpack_url('dashboard'),
            'Report' => false,
            $type => false,
        ];



        return view(backpack_view('blank'), $this->data);
    }

    public function checkData(){
        $visitors = Visitor::all();
        foreach ($visitors as $visitor) {
            $visitor->checkSelf();
        }
        \Alert::success("Data Successfully Checked!!")->flash();
        $this->data['title'] = 'Operations';
        return redirect(backpack_url('operations'));
    }
}