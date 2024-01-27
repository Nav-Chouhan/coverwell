<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Company;
use Illuminate\Http\Request;
use Hashids\Hashids;

class RegisterController extends Controller
{
    public function index(Request $request) {
        $visitorData['hosted_by'] = false;
        if (!(session()->has('jasLogin'))) {
            return redirect('/jas-login');
        }
        if ($request->route('id')) {
            if (session()->get('jasLogin') != $request->route('id')) {
                return redirect('/jas-login');
            }
            $visitorData = Visitor::find($request->route('id'));
            $company['companyName'] = $visitorData->company->id;
            $visitorData = $visitorData->toArray();
            // $companyDetails = Company::find($visitorData['company_id'])->toArray();
            $visitorData['departure_date_time'] = (!empty($visitorData['departure_date_time'])) ? date('Y-m-d\TH:i', strtotime($visitorData['departure_date_time'])): "";
            $visitorData['check_in_date'] = (!empty($visitorData['check_in_date'])) ? date('Y-m-d', strtotime($visitorData['check_in_date'])): "";
            $visitorData['check_out_date'] = (!empty($visitorData['check_out_date'])) ? date('Y-m-d', strtotime($visitorData['check_out_date'])): "";
            $visitorData['nri'] = (!empty($visitorData['country'])) ? "nri" : "";
            $visitorData['hosted_by'] = false;
            // $visitorData = array_merge($visitorData, $company);
        } else if ($request->route('buyer_id')) {
            if (session()->get('jasLogin') != $request->route('buyer_id')) {
                return redirect('/jas-login');
            }
            // $exhibitor = Visitor::find($request->route('buyer_id'))->toArray();
            $exhibitor = Visitor::where('id', $request->route('buyer_id'))->where('category_id', 47)->first();
            $exhibitor->company->id;
            $exhibitorHostedByCnt = Visitor::where('hosted_by', $request->route('buyer_id'))->count();
            if ($exhibitorHostedByCnt == 5) {
                return view('noregisty')->with(['error' => 'You have already register 5 buyer as hosted buyer.']);
            }
            if(empty($exhibitor)) {
                return view('noregisty')->with(['error' => 'Host registry not found!!']);
            }
            $visitorData['hosted_by'] = true;
            $visitorData['id'] = $exhibitor['id'];
            $visitorData['exhibitor_name'] = $exhibitor['name'];
            $visitorData['stall_no'] = $exhibitor['company']['stall_no'];
            $visitorData['compName'] = $exhibitor['company']['name'];
            $visitorData['exhibitor_contact'] = $exhibitor['contact'];
            $visitorData['exhibitorHostedByCnt'] = $exhibitorHostedByCnt;
            $visitorData['nri'] = "";
        }
        return view('registration')->with($visitorData);
    }

    public function registration(Request $request) {
        $visitorData = [];
        $visitorData['hosted_by'] = false;
        $visitorData['travel_by'] = "Flight";
        $visitorData['nri'] = !empty($request->nri) ? $request->nri : "";
        return view('registration')->with($visitorData);
    }

    public function checkHosted(Request $request) {
        $visitorData = Visitor::where('name', $request->pname)->whereRaw("name COLLATE utf8mb4_general_ci = '". $request->pname."'")->where('contact', $request->mno)->whereIn('category_id', [1, 46])->first();
        // $visitorDataWithOutHostedCatId = Visitor::where('name', $request->pname)->where('contact', $request->mno)->first();
        // $companyData = Company::where('name', $request->cname)->first();
        // if ($visitorData && $companyData && $visitorData->company_barcode == $companyData->barcode) {
        if ($visitorData) {
            return [
                'hosted' => 1,
                'id' => $visitorData->id
            ];
        }
        // } elseif ($visitorDataWithOutHostedCatId && $companyData && $visitorDataWithOutHostedCatId->company_barcode == $companyData->barcode) {
        //     return [
        //         'visitorRec' => [
        //             'id' => $visitorDataWithOutHostedCatId->id,
        //             'city' => $visitorDataWithOutHostedCatId->city,
        //             'email' => $visitorDataWithOutHostedCatId->email
        //         ]
        //     ];
        // }
        return [
            'hosted' => 0
        ];
    }

    public function store(Request $request)
    {
        // ALTER TABLE `visitors` ADD `travel_booked_by` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `departure_pnr` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `flight_train_name` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `flight_train_number` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `return_pnr` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `return_city` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `return_date` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `return_flight_train_name` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `return_flight_train_number` VARCHAR(255) NOT NULL AFTER `travel_by`;
        // ALTER TABLE `visitors` ADD `departure_city` VARCHAR(255) NOT NULL AFTER `departure_city_id`;
        // ALTER TABLE `visitors` ADD `hosted_buyer` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `hospitality_status`;
        // ALTER TABLE `visitors` ADD `hosted_on` DATETIME NULL DEFAULT NULL AFTER `hosted_buyer`;
        // ALTER TABLE `visitors` CHANGE `travel_ticket` `travel_ticket` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `iata_airline` `iata_airline` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `iata_airport` `iata_airport` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `arrival_city_id` `arrival_city_id` INT(11) NULL DEFAULT NULL, CHANGE `departure_city_id` `departure_city_id` INT(11) NULL DEFAULT NULL, CHANGE `departure_city` `departure_city` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `return_flight_train_number` `return_flight_train_number` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `return_flight_train_name` `return_flight_train_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `return_date` `return_date` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `return_city` `return_city` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `return_pnr` `return_pnr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `flight_train_number` `flight_train_number` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `flight_train_name` `flight_train_name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `departure_pnr` `departure_pnr` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `travel_booked_by` `travel_booked_by` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `hotel_id` `hotel_id` INT(11) NULL DEFAULT NULL, CHANGE `room_number` `room_number` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL, CHANGE `hospitality_status` `hospitality_status` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
        // ALTER TABLE `visitors` ADD `car_number` VARCHAR(50) NULL AFTER `room_number`;
        // ALTER TABLE `visitors` ADD `hosted_by` INT(11) NULL DEFAULT NULL AFTER `hosted_on`;
        $alreadyHostedBy = "";
        $visitorBarcode = "";
        if (!empty($request->id)) {
            $visitor = Visitor::find($request->id);
            $alreadyHostedBy = $visitor->hosted_by;
            $visitorBarcode = $visitor->barcode;
        } else {
            $visitor = new Visitor();
            $visitor->src = "JAS";
        }

        if (!empty($request->id) && !empty($request->hosted_by)) {
            $visitor->category_id = 46;
            $visitor->hosted_by = $request->hosted_by;
            $visitor->save();
            return redirect()->back()->with('success', 'Record created successfully!');
        }

        $existingCompany = Company::where('name', $request->nameOfTheCompany)->first();
        if (empty($existingCompany)) {
            $statement = \DB::select("SHOW TABLE STATUS LIKE 'companies'");
            $nextId = $statement[0]->Auto_increment;
            $hashids = new Hashids('', 5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
            $only_company["barcode"] = $hashids->encode($nextId);
            $only_company["name"] =  $request->nameOfTheCompany;
            $only_company["contact"] =  $request->mobileNumber;
            $only_company["gstin"] =  $request->gstNumber;
            if ($request->file('gstCert')) {
                $file = $request->file('gstCert');
                $filename = date('YmdHi') . '_gstCert' . $file->getClientOriginalName();
                $file->storeAs('uploads/photo', $filename, 'public');
                // $file->move(public_path('public/uploads/photo'), $filename);
                $only_company["gst_certificate"] = '/uploads/photo/' . $filename;
            }
            $company_obj = Company::updateOrCreate(['barcode' => $only_company["barcode"]], $only_company);
        } else {
            if ($request->gstNumber && ($request->gstNumber != $existingCompany->gstin)) {
                $existingCompany->update([
                    'gstin' => $request->gstNumber,
                ]);
                $existingCompany->save();
            }
            // $existingCompany = $existingCompany->toArray();
            $only_company["barcode"] = $existingCompany->barcode;
            if ($request->file('gstCert')) {
                $file = $request->file('gstCert');
                $filename = $only_company["barcode"] . '.' . $file->getClientOriginalExtension();
                $file->storeAs('uploads/gst_certificate', $filename, 'public');
                // $file->move(public_path('public/uploads/photo'), $filename);
                // $visitor->travel_ticket = '/uploads/photo/' . $filename;
                $existingCompany->gst_certificate = '/uploads/gst_certificate/' . $filename;
                $existingCompany->save();
            }
        }

        if (empty($visitorBarcode)) {
            $statement = \DB::select("SHOW TABLE STATUS LIKE 'visitors'");
            $nextId = $statement[0]->Auto_increment;
            $hashids = new Hashids('', 5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
            $visitorBarcode = $hashids->encode($nextId);
        }

        $visitor->name = (!empty($request->nameOfThePerson)) ? $request->nameOfThePerson : null;
        $visitor->contact = (!empty($request->mobileNumber)) ? $request->mobileNumber : null;
        $visitor->email = (!empty($request->email)) ? $request->email : null;
        $visitor->company_barcode = $only_company["barcode"];
        $visitor->city = (!empty($request->city)) ? $request->city : null;
        $visitor->member_of = $request->designation;
        $visitor->check_in_date = (!empty($request->checkInDate)) ? date('Y-m-d H:i:s', strtotime($request->checkInDate)) : null;
        $visitor->check_out_date = (!empty($request->checkOutDate)) ? date('Y-m-d H:i:s', strtotime($request->checkOutDate)) : null;
        $visitor->travel_booked_by = (!empty($request->ticketsBookedBy)) ? $request->ticketsBookedBy : null;
        $visitor->departure_pnr = (!empty($request->departurePNR)) ? $request->departurePNR : null;
        $visitor->departure_city = (!empty($request->departureCity)) ? $request->departureCity : null;
        $visitor->departure_date_time = (!empty($request->datetimeOfDeparture)) ? date('Y-m-d H:i:s', strtotime($request->datetimeOfDeparture)) : null;
        $visitor->flight_train_name = (!empty($request->ftName)) ? $request->ftName : null;
        $visitor->flight_train_number = (!empty($request->ftNumber)) ? $request->ftNumber : null;
        $visitor->return_pnr = (!empty($request->returningPNR)) ? $request->returningPNR : null;
        $visitor->return_city = (!empty($request->returningCity)) ? $request->returningCity : null;
        $visitor->return_date = (!empty($request->returnFlightDate)) ? $request->returnFlightDate : null;
        $visitor->return_flight_train_name = (!empty($request->returnftName)) ? $request->returnftName : null;
        $visitor->return_flight_train_number = (!empty($request->returnftNumber)) ? $request->returnftNumber : null;
        $visitor->travel_by = (!empty($request->travelMode)) ? $request->travelMode : null;
        $visitor->pincode = (!empty($request->pincode)) ? $request->pincode : null;
        $visitor->address = (!empty($request->address)) ? $request->address : null;
        $visitor->country = (!empty($request->country)) ? $request->country : null;
        $visitor->iata_airline = "na";
        $visitor->iata_airport = "na";
        $visitor->arrival_city_id = 1;
        $visitor->hotel_id = 1;
        $visitor->room_number = "na";
        $visitor->hospitality_status = "na";
        $visitor->hospitality_status = "na";
        $visitor->departure_city_id = 1;
        if (empty($alreadyHostedBy)) {
            $visitor->hosted_by = (!empty($request->hosted_by)) ? $request->hosted_by : null;
        }
        $visitor->category_id = (!empty($request->hosted_by)) ? 46 : 1;


        if ($request->file('profile')) {
            $image = $request->file('profile');
            $imageName = $visitorBarcode . '.' . $image->getClientOriginalExtension();
            $image->storeAs('uploads/photo', $imageName, 'public');
            $visitor->photo = '/uploads/photo/'.$imageName;
        }
        
        if ($request->file('id_proof_front')) {
            $file = $request->file('id_proof_front');
            $filename = $visitorBarcode . '_id_proof_front.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/idproof', $filename, 'public');
            // $file->move(public_path('public/uploads/photo'), $filename);
            $visitor->idproof = '/uploads/idproof/'.$filename;
        }

        if ($request->file('id_proof_back')) {
            $file = $request->file('id_proof_back');
            $filename = $visitorBarcode . '_id_proof_back.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/idproof', $filename, 'public');
            // $file->move(public_path('public/uploads/photo'), $filename);
            $visitor->idproof_back = '/uploads/idproof/' . $filename;
        }

        if ($request->file('ticket')) {
            $file = $request->file('ticket');
            $filename = $visitorBarcode . '.' . $file->getClientOriginalExtension();
            $file->storeAs('uploads/ticket', $filename, 'public');
            // $file->move(public_path('public/uploads/photo'), $filename);
            $visitor->travel_ticket = '/uploads/ticket/' . $filename;
        }
        $visitor->save();

        return redirect()->back()->with('success', 'Record created successfully!');
    }

    public function openChb (Request $request) {
        
        $visitor = Visitor::where('id', $request->route('id'))->whereIn('category_id', [1, 46, 47])->first();
        if (empty($visitor)) {
            return response()->json([
                'html' => "Sorry this feature is not available.",
            ]);
        }
        $visitor->company->id;
        $viewObj = [
            'exhibitorName' => [],
            'visitors' => [],
        ];
        if (in_array($visitor->category_id, [1, 46]) && !empty($visitor->hosted_by)) {
            $exhibitor = Visitor::where('id', $visitor->hosted_by)->first();
            $viewObj['exhibitorName'] = $exhibitor;
            return response()->json([
                'html' => view('chb', $viewObj)->render(),
                'title' => 'Exhibitor Details'
            ]);
        } if (in_array($visitor->category_id, [1]) && empty($visitor->hosted_by)) {
            $exhibitor = Visitor::where('id', $visitor->hosted_by)->first();
            $viewObj['exhibitorName'] = $exhibitor;
            return response()->json([
                'html' => view('chb', $viewObj)->render(),
                'title' => 'Exhibitor Details'
            ]);
        } else {
            $visitors = Visitor::where('hosted_by', $visitor->id)->get();
            $viewObj['visitors'] = $visitors;
            return response()->json([
                'html' => view('chb', $viewObj)->render(),
                'title' => 'Buyers Details'
            ]);
        }
    }
}