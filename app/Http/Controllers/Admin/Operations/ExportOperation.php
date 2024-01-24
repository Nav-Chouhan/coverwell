<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Rap2hpoutre\FastExcel\FastExcel;

trait ExportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupExportRoutes($segment, $routeName, $controller)
    {
        Route::get($segment.'/export', [
            'as'        => $routeName.'.export',
            'uses'      => $controller.'@export',
            'operation' => 'export',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupExportDefaults()
    {
        $this->crud->allowAccess('export');

        $this->crud->operation('export', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            $this->crud->addButton('top', 'export', 'view', 'crud::buttons.export');
            // $this->crud->addButton('line', 'export', 'view', 'crud::buttons.export');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function export()
    {
        $selectedFilters = request()->query();
        if (!empty($selectedFilters['category'])) {
            $cats = json_decode($selectedFilters['category'], true);
            $cats = array_map('intval', $cats);
            $this->crud->hasAccessOrFail('export');
            // $items = $this->crud->model->select("barcode", "name", "contact", "email", "address", "company_barcode", "hosted_by", "registered_on")->whereIn('category_id', $cats)->get();
            // Build your SQL query based on the selected filters
            $sql = "SELECT v.barcode, v.name, v.category_id, c.name as company, v.contact, v.city, v.address, v.registered_on, cp.name as ProposedBy, vj.barcode as ProposedBy_barcode FROM visitors as v left join visitors as vj on v.hosted_by = vj.id join companies as c on v.company_barcode = c.barcode left join companies as cp on vj.company_barcode = cp.barcode where v.category_id IN (".implode(",", $cats).") ORDER BY v.registered_on ASC";
            // Execute the raw SQL query
            $items = \DB::select($sql);
            return (new FastExcel($items))->download($this->crud->entity_name_plural.'.xlsx');
        } else {
            $this->crud->hasAccessOrFail('export');
            return (new FastExcel($this->crud->model->all()))->download($this->crud->entity_name_plural.'.xlsx');
        }
    }
}
