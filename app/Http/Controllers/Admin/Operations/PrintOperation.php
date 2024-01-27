<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Str;

trait PrintOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupPrintRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/print', [
            'as'        => $routeName . '.print',
            'uses'      => $controller . '@print',
            'operation' => 'print',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupPrintDefaults()
    {
        $this->crud->allowAccess('print');

        $this->crud->operation('print', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation(['list', 'show'], function () {
            // $this->crud->addButton('top', 'print', 'view', 'crud::buttons.print');
            $this->crud->addButton('line', 'print', 'view', 'crud::buttons.print');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function print($id)
    {
        $this->data['entry'] = $this->crud->getEntry($id);
        $payment_categories = explode(',',\Config::get('settings.payment_categories'));
        if (in_array($this->data['entry']->category_id, $payment_categories)) {
            if($this->data['entry']->payment_status != 'Received'){
                return "Please make payment.";
            }
        }

        if (
            ($this->data['entry']->printed_on != null && \Carbon\Carbon::parse($this->data['entry']->printed_on)->diffInMinutes(Carbon::now()) > 10)
            && !backpack_user()->can('PrintDuplicate')
        ) {
            $this->crud->denyAccess(['delete', 'revise', 'export', 'import', 'bulkdelivery', 'print']);
        }

        $this->crud->hasAccessOrFail('print');

        // prepare the fields you need to show
        $this->data['crud'] = $this->crud;
        $this->data['title'] = $this->crud->getTitle() ?? 'print ' . $this->crud->entity_name;

        if (
            ($this->data['entry']->printed_on != null && \Carbon\Carbon::parse($this->data['entry']->printed_on)->diffInMinutes(Carbon::now()) > 10)
        ) {
            $count = $this->data['entry']->print_count();
            $replaced = Str::replaceLast('-D'.$count,'',$this->data['entry']->barcode);
            $replaced = Str::replaceLast('-D','',$replaced);
            $this->data['entry']->barcode = $replaced.'-D'.($count+1);
        }

        $this->data['entry']->printed_on = Carbon::now();
        $this->data['entry']->verified_on = Carbon::now();
        $this->data['entry']->save();
        // load the view
        return view("backpack.theme-coreuiv2::operations.print", $this->data);
    }
}
