<?php

namespace App\Http\Controllers\Admin\Operations;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

trait ImportOperation
{
    /**
     * Define which routes are needed for this operation.
     *
     * @param string $segment    Name of the current entity (singular). Used as first URL segment.
     * @param string $routeName  Prefix of the route name.
     * @param string $controller Name of the current CrudController.
     */
    protected function setupImportRoutes($segment, $routeName, $controller)
    {
        Route::post($segment . '/import', [
            'as'        => $routeName . '.import',
            'uses'      => $controller . '@import',
            'operation' => 'import',
        ]);
    }

    /**
     * Add the default settings, buttons, etc that this operation needs.
     */
    protected function setupImportDefaults()
    {
        $this->crud->allowAccess('import');

        $this->crud->operation('import', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();
        });

        $this->crud->operation('list', function () {
            // $this->crud->addButton('top', 'import', 'view', 'crud::buttons.import');
            // $this->crud->addButton('line', 'import', 'view', 'crud::buttons.import');
        });
    }

    /**
     * Show the view for performing the operation.
     *
     * @return Response
     */
    public function import(Request $request)
    {
        $this->crud->hasAccessOrFail('import');

        $validator = \Validator::make($request->all(), [
            'import_file' => 'required|file|mimes:xlsx',
        ]);

        if ($validator->fails()) {
            \Alert::warning($validator->errors()->first())->flash();
            return back();
        }
        ini_set('max_execution_time', 600);
        if (get_class($this->crud->model) == "App\Models\Company") {

            try {
                $path = $request->file('import_file')->getRealPath();
                (new FastExcel)->import($path, function ($line) {
                    foreach ($line as $i => $value) {
                        if ($value === "" || $value === null || $value === "null" | $value === "NULL") // $line[$i] = null;
                            unset($line[$i]);
                    }
                    if (isset($line['barcode']))
                        $company = $this->crud->model->updateOrCreate(['barcode' => $line['barcode']], $line);
                    else
                        $company = $this->crud->model->updateOrCreate(['name' => $line['name']], $line);
                    return $company;
                });
                \Alert::success("Companies Successfully Imported")->flash();
            } catch (Exception $e) {
                Log::error($e);
                \Alert::error($e->getMessage())->flash();
            }
        }
        if (get_class($this->crud->model) == "App\Models\Visitor") {
            try {
                $path = $request->file('import_file')->getRealPath();
                (new FastExcel)->import($path, function ($line) {
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
                    $visitor = $this->crud->model->updateOrCreate(['barcode' => $line['barcode']], $line);
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
                });
                \Alert::success("Visitors Successfully Imported")->flash();
            } catch (Exception $e) {
                Log::error($e);
                \Alert::error($e->getMessage())->flash();
            }
        }
        if (get_class($this->crud->model) == "App\Models\User") {
            try {
                $path = $request->file('import_file')->getRealPath();
                (new FastExcel)->import($path, function ($line) {
                    foreach ($line as $i => $value) {
                        if ($value === "" || $value === null || $value === "null" | $value === "NULL") // $line[$i] = null;
                            unset($line[$i]);
                    }
                    if (isset($line['roles'])){
                        $roles = $line['roles'];
                        unset($line['roles']);
                    }
                    if (isset($line['password'])){
                        $line['password'] = Hash::make($line['password']);
                    }
                    
                    $user = $this->crud->model->updateOrCreate(['id'=> $line['id']],$line);
                    if (isset($roles)){
                        $user->syncRoles(explode(',',$roles));
                    }                    
                    return $user;
                });
                \Alert::success("Visitors Successfully Imported")->flash();
            } catch (Exception $e) {
                Log::error($e);
                \Alert::error($e->getMessage())->flash();
            }
        }
        return back();
    }
}
