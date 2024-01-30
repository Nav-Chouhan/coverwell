<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\Company;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use GuzzleHttp\Client;
use Clarkeash\Doorman\Facades\Doorman;
use Backpack\PageManager\app\Models\Page;
use App\Models\Invite;
use Validator;
use Config;
use Carbon\Carbon;
use Hashids\Hashids;


class VisitorController extends Controller
{

    public function confirmation(Request $request) {

        $visitor = Visitor::where('barcode', '=', $request->hidden_barcode)->firstOrFail();
        /* if ($visitor->arival) {
            return response()->json(
                [
                    'barcode' => $visitor->barcode,
                    'message' => 'You are already verified.'
                ],
                200
            );
        } */
        $visitor->arival = $request->arrival_time;
        $visitor->save();
        return response()->json(
            [
                'barcode' => $visitor->barcode,
                'message' => 'Thankyou for confirmation. Your pass can avail free entry at JAS B2B Show 2022.'
            ],
            200
        );
    }

    public function ja_member(Request $request) {

        if ($request->action == 1) {
            $validations = [
                'membership_no' => 'required|digits:4',
            ];
            $this->validate($request, $validations);
            $visitor = Visitor::where('membership_no', '=', $request->membership_no)->firstOrFail();
            $visitor->sendJAOTP();
            return [
                "mobile" => $visitor->contact,
            ];
        } elseif ($request->action == 2) {
            $validations = [
                'membership_no' => 'required|digits:4',
                'otp' => 'required|digits:4',
            ];
            $this->validate($request, $validations);
            $visitor = Visitor::where('membership_no', '=', $request->membership_no)->firstOrFail();
            if ($visitor->verifyOTP($request->otp)) {
                return [
                    "name" => $visitor->name,
                    "company" => $visitor->company?$visitor->company->name:'',
                    "mobile" => $visitor->contact,
                    "postal_address" => $visitor->address,
                    "photo" => $visitor->getPhoto64(),
                ];
            } else {
                return response()->json([
                    'message' => "The otp is invalid.",
                    'errors'  => [
                        'otp' => ["The otp is invalid."]
                    ]
                ], 422);
            }
        } elseif ($request->action == 3) {
            $validations = [
                'membership_no' => 'required|digits:4',
                'otp' => 'required|digits:4',
                'address' => 'required',
                'photo' => 'required',
            ];
            $this->validate($request, $validations);

            $visitor = Visitor::where('membership_no', '=', $request->membership_no)->firstOrFail();
            $visitor->address = $request->address;
            $visitor->photo = $request->photo;
            $visitor->verified_on = Carbon::now();
            $visitor->save();
            return [
                'message' => 'Thankyou. Your pass can avail free entry at JAS B2B Show 2023.',
                "error" => "error"
            ];
        }
    }

    public function simpleForm(Request $request) {
         
        $validations = [
            'invite_code' => 'required|invite_code',
            'name' => 'required',

            'contact' => 'required|digits:10',
            'photo' => 'required|base64image',
            //'idproof' => 'required|base64image',
            //'idproof_back' => 'required|base64image',
            //'slug' => 'required|exists:pages,slug',
            //'city' => 'required',
        ];

        $this->validate($request, $validations);
        $only_company = [
            //'barcode' => $request->invite_code,
            'name' => Invite::where('code', $request->invite_code)->first()->name,
            //'city' => $request->city,
        ];
        
        $company = Company::updateOrCreate($only_company, $only_company);
       

        $visitor = Visitor::firstOrCreate([
            'name' => $request->name,
            'contact' => $request->contact,
            'company_barcode' => $company->id
        ]);
        if ($visitor->first_verified_on) {
            return response()->json(
                [
                    'barcode' => $visitor->barcode,
                    'message' => 'You are already verified.'
                ],
                200
            );
        }
        $visitor->fill($request->all());
        $visitor->idproof_back = $request->idproof_back;
        //$visitor->vaccine = $request->vaccine;
        //$visitor->first_registered_on = Carbon::now();
        $visitor->category_id = Invite::where('code', $request->invite_code)->first()->category_id;
        $visitor->ticket_invite_code = $request->invite_code;
        $visitor->save();
        Doorman::redeem($request->invite_code);
        return response()->json(
            [
                'barcode' => $visitor->barcode,
                'message' => 'Successfully Registered. Your Barcode is ' . $visitor->barcode
            ],
            200
        );
    }

    public function simpleFormNoInvite(Request $request) {
        $validations = [
            'name' => 'required',
            'company' => 'required',
            'contact' => 'required|digits:10',
            'photo' => 'required|base64image',
            'idproof' => 'required|base64image',
            'idproof_back' => 'required|base64image',
            'slug' => 'required|exists:pages,slug',
            'city' => 'required',
            //'member_of' => 'required',
            //'media_gst_membership_no' => 'required',
        ];

        $this->validate($request, $validations);
        $only_company = [
            'name' => $request->company,
            'city' => $request->city,
        ];
        $company = Company::updateOrCreate($only_company, $only_company);

        $visitor = Visitor::firstOrCreate([
            'name' => $request->name,
            'contact' => $request->contact,
            'company_barcode' => $company->barcode
        ]);
        if ($visitor->first_verified_on) {
            return response()->json(
                [
                    'barcode' => $visitor->barcode,
                    'message' => 'You are already verified.'
                ],
                200
            );
        }
        $visitor->fill($request->all());
        $visitor->idproof_back = $request->idproof_back;
        $visitor->category_id = 43;
        $visitor->save();
        return response()->json(
            [
                'barcode' => $visitor->barcode,
                'message' => 'Successfully Registered. Your Barcode is ' . $visitor->barcode
            ],
            200
        );
    }

    public function sync(Request $request, $slug) {
        if ($slug != "jovi") abort(404);
        if (!$request->isJson() || empty($request->json()->all())) {
            abort(422, 'Invalid JSON');
        }
        $validations = [
            // '*.barcode' => 'required|string',
            '*.name' => 'required|string',
            '*.contact' => 'required',
            '*.visitors.*.barcode' => 'required|string',
            '*.visitors.*.category_id' => 'required|string',
            '*.visitors.*.name' => 'required|string',
            '*.visitors.*.contact' => 'required|string',
            '*.visitors.*.photo' => 'required|string',
            // '*.visitors.*.city' => 'required|string',
            // '*.visitors.*.payment_status' => 'required',
        ];
        $request->validate($validations);
        
        foreach ($request->json()->all() as $company) {
            $company_inputs = [
                'barcode',
                'name',
                'contact',
                'email',
                'gstin',
                'gst_certificate',
                'pan',
                'address',
                'pincode',
                'city',
                'state',
                'country',
                'type',
                'stall_no',
                'hall',
            ];
            foreach ($company as $key => $value) {
                if ($value == "" || $value == null) {
                    unset($company[$key]);
                }
            }
            $only_company = Arr::only($company, $company_inputs);
            /* if (isset($only_company["gst_certificate"])) {
                try {
                    $temp_file = tempnam(sys_get_temp_dir(), 'tmp');
                    $client = new Client();
                    $client->request('GET', $only_company["gst_certificate"], ['sink' => $temp_file]);
                } catch (\Throwable $th) {
                    unset($only_company["gst_certificate"]);
                } finally {
                    $mimes = new \Mimey\MimeTypes;
                    $ext = $mimes->getExtension(mime_content_type($temp_file));
                    $path = "uploads/gst_certificate/{$only_company["barcode"]}.{$ext}";
                    $only_company["gst_certificate"] = $path;
                    copy($temp_file, public_path($path));
                }
            } */

            $existingCompany = Company::where('name', $only_company['name'])->first();

            if (empty($existingCompany)) {
                $statement = \DB::select("SHOW TABLE STATUS LIKE 'companies'");
                $nextId = $statement[0]->Auto_increment;
                $hashids = new Hashids('', 5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
                $only_company["barcode"] = $hashids->encode($nextId);
            } else {
                $existingCompany = $existingCompany->toArray();
                $only_company["barcode"] = $existingCompany['barcode'];
            }

            $company_obj = Company::updateOrCreate(['barcode' => $only_company["barcode"]], $only_company);

            $visitor_inputs = [
                'barcode',
                'photo',
                'idproof',
                'idproof_back',
                'name',
                'contact',
                'email',
                'address',
                'pincode',
                'city',
                'state',
                'country',
                'payment_status',
                'category_id',
            ];

            foreach ($company["visitors"] as $visitor_json_arr) {
                foreach ($visitor_json_arr as $key => $value) {
                    if ($value == "" || $value == null) {
                        unset($visitor_json_arr[$key]);
                    }
                }
                $only_visitor = Arr::only($visitor_json_arr, $visitor_inputs);
                
                // $existingVisitor = Visitor::find(['name' => $only_visitor['name'], 'contact' => $only_visitor['contact']])->toArray();
                $existingVisitor = Visitor::where('name', $only_visitor['name'])->where('contact', $only_visitor['contact'])->first();
                
                if (empty($existingVisitor)) {
                    if (empty($only_visitor['barcode'])) {
                        $statement = \DB::select("SHOW TABLE STATUS LIKE 'visitors'");
                        $nextId = $statement[0]->Auto_increment;
                        $hashids = new Hashids('', 5, '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ');
                        $only_visitor["barcode"] = $hashids->encode($nextId);
                    }
                } else {
                    $existingVisitor = $existingVisitor->toArray();
                    $only_visitor["barcode"] = $existingVisitor['barcode'];
                }

                /* if (isset($only_visitor["photo"])) {
                    try {
                        $temp_file = tempnam(sys_get_temp_dir(), 'tmp');
                        $client = new Client();
                        $client->request('GET', $only_visitor["photo"], ['sink' => $temp_file]);
                    } catch (\Throwable $th) {
                        unset($only_visitor["photo"]);
                    } finally {
                        $mimes = new \Mimey\MimeTypes;
                        $ext = $mimes->getExtension(mime_content_type($temp_file));
                        $path = "/uploads/photo/{$only_visitor["barcode"]}.{$ext}";
                        $only_visitor['photo'] = $path;
                        copy($temp_file, public_path($path));
                    }
                } */
                $only_visitor['src'] = $slug;
                $only_visitor['company_barcode'] = $company_obj->barcode;
                if ($only_visitor['category_id'] == "exhibitor")
                    $only_visitor['category_id'] = 3; //exhibitor
                elseif ($only_visitor['category_id'] == "trade_visitor")
                    $only_visitor['category_id'] = 16; //trade_visitor
                elseif ($only_visitor['category_id'] == "media")
                    $only_visitor['category_id'] = 13; //media
                elseif ($only_visitor['category_id'] == "student")
                    $only_visitor['category_id'] = 22; //student
                elseif ($only_visitor['category_id'] == "international")
                    $only_visitor['category_id'] = 23; //international
                elseif ($only_visitor['category_id'] == "pre-register")
                    $only_visitor['category_id'] = 30; //pre-register
                elseif ($only_visitor['category_id'] == "Proposed Exhibitor")
                    $only_visitor['category_id'] = 47; //Proposed Exhibitor
                elseif ($only_visitor['category_id'] == "BUYER")
                    $only_visitor['category_id'] = 1; //BUYER
                elseif ($only_visitor['category_id'] == "JA MEMBERS")
                    $only_visitor['category_id'] = 44; //JA MEMBERS
                /* else
                    $only_visitor['category_id'] = 0; //trade_visitor */
                //$only_visitor['extra'] = json_encode(Arr::except($visitor_json_arr, $visitor_inputs));
                $visitor_obj = Visitor::updateOrCreate(['barcode' => $only_visitor["barcode"]], $only_visitor);
            }
        }
        \App\Jobs\ProcessSync::dispatch();
        return [
            'message' => 'Thankyou.'
        ];
        // return "Synced Successfully";
    }
}
