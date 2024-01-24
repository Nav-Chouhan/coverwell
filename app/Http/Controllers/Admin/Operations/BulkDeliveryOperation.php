<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;

trait BulkDeliveryOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupBulkDeliveryRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/bulkdelivery', [
            'as'        => $routeName . '.bulkdelivery',
            'uses'      => $controller . '@bulkdelivery',
            'operation' => 'bulkdelivery',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupBulkDeliveryDefaults()
    {
        $this->crud->allowAccess('bulkdelivery');

        $this->crud->operation('bulkdelivery', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'bulkdelivery', 'view', 'crud::buttons.bulkdelivery');
            // $this->crud->addButton('line', 'bulkdelivery', 'view', 'crud::buttons.bulkdelivery');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function bulkdelivery()
    {
        $this->crud->hasAccessOrFail('bulkdelivery');
        \App\Jobs\ProcessDeliverySync::dispatch();
        return;
        try {
            $docket_nos = $this->crud->model->select('docket_no')
                ->whereNotNull('docket_no')
                ->where('docket_status', '!=', 'Delivered')
                //->orWhere('docket_status', null)
                ->groupBy('docket_no')
                ->get()->toArray('docket_no');
            $arr = array_chunk($docket_nos, 100);
            
            foreach ($arr as $dockets) {
                $json = [
                    "strConsignmentIds" => Arr::flatten($dockets),
                    "numStatus" => "1",
                    "numVersion" => "1.0"
                ];
                //dd($json);
                $client = new Client(
                    ['headers' => ['X-Access-Token' => '23328e199e0061cf85262f2b0280890e']]
                );
                $response = $client->post('https://blktracksvc.dtdc.com/dtdc-bulktracking-api/dtdc/TrackingAPI/TrackBulkConsignmentDetails', [
                    \GuzzleHttp\RequestOptions::JSON => $json
                ]);
                
                if ($response->getStatusCode() == 200) {
                    $body = json_decode($response->getBody()->getContents());
                    foreach ($body as $docket) {
                        $this->crud->model->where('docket_no', $docket->trackHeader->strShipmentNo)->update(['docket_status' => $docket->trackHeader->strStatus]);
                    }
                }
            }
            echo $response->getStatusCode();
        } catch (Exception $e) {
            Log::error($e);            
        }
        //return back();
    }
}
