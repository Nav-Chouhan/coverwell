<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mail;
use App\Mail\SendMail;
use Rap2hpoutre\FastExcel\FastExcel;

trait ReportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupReportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/report', [
            'as'        => $routeName . '.report',
            'uses'      => $controller . '@report',
            'operation' => 'report',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupReportDefaults()
    {
        $this->crud->operation('Report', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list'], function () {
            $this->crud->addButton('top', 'report', 'view', 'crud::buttons.report');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function report()
    {
            // Build your SQL query based on the selected filters
            // $sql = 'SELECT v.barcode, v.name, v.category_id, c.name as company, v.contact, v.city, v.address, v.registered_on, cp.name as ProposedBy, vj.barcode as ProposedBy_barcode, v.travel_by, DATE_FORMAT(v.departure_date_time, "%Y-%m-%d") as arival_date, DATE_FORMAT(v.departure_date_time, "%h:%i:%s %p") as arival_time, v.flight_train_name, v.flight_train_number, v.departure_pnr as PNR FROM visitors as v left join visitors as vj on v.hosted_by = vj.id join companies as c on v.company_barcode = c.barcode left join companies as cp on vj.company_barcode = cp.barcode where v.category_id IN (1) order by v.travel_by, arival_date, arival_time';
            $sql = 'SELECT v.barcode, v.name, v.category_id, c.name as company, v.contact, v.city, v.address, v.registered_on, cp.name as ProposedBy, vj.barcode as ProposedBy_barcode, v.travel_by, DATE_FORMAT(v.departure_date_time, "%Y-%m-%d") as arival_date, DATE_FORMAT(v.departure_date_time, "%h:%i:%s %p") as arival_time, v.flight_train_name, v.flight_train_number, v.departure_pnr as PNR, h.name as hotelName, v.check_in_date, v.check_out_date FROM visitors as v left join visitors as vj on v.hosted_by = vj.id join companies as c on v.company_barcode = c.barcode left join companies as cp on vj.company_barcode = cp.barcode join hotels h on v.hotel_id = h.id where v.category_id IN (1) order by v.travel_by, arival_date, arival_time';
            // Execute the raw SQL query
            $items = \DB::select($sql);
            return (new FastExcel($items))->download($this->crud->entity_name_plural . '.xlsx');
        
    }
}
