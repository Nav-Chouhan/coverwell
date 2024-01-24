<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Visitor;

class VisitorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $val = [
            'name' => 'required|min:5|max:255',
            'category_id' => 'required|exists:visitor_categories,id',
            //'company' => 'required',
            'photo' => 'required',
            //'contact' => 'required|min:10',
            //'city' => 'required|min:3|max:255',
            //'profession_id' => 'nullable|exists:professions,id',
            //'gender' => 'required|in:Male,Female',

        ];

        $payment_categories = explode(',', \Config::get('settings.payment_categories'));
        if (in_array($this->get("category_id"), $payment_categories)) {
            if($this->request->get('id')){
                $visitor = Visitor::find($this->request->get('id'));
                // if($visitor->payment_status != "Received"){
                    $val['receipt_no'] = 'required|unique:visitors,receipt_no,' . $visitor->id;
                    $val['amount'] = 'required|numeric';
                // }
            }else{
                $val['receipt_no'] = 'required|unique:visitors';
                $val['amount'] = 'required|numeric';
            }
        }

        return $val;
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
