<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use App\Models\Company;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Hashids\Hashids;

class InternalController extends Controller
{
    public function index(Request $request) {
        if (!(session()->has('jasInternalLogin'))) {
            return redirect('/jas-internal-login');
        }
        $visitorData = [];
        if ($request->route('id')) {
            $visitorData = Visitor::find($request->route('id'));
            $company['companyName'] = $visitorData->company->id;
            $company['HotelName'] = $visitorData->hotel->id;
            $visitorData = $visitorData->toArray();
            $visitorData['departure_date_time'] = (!empty($visitorData['departure_date_time'])) ? date('Y-m-d\TH:i', strtotime($visitorData['departure_date_time'])) : "";
            $visitorData['check_in_date'] = (!empty($visitorData['check_in_date'])) ? date('Y-m-d', strtotime($visitorData['check_in_date'])) : "";
            $visitorData['check_out_date'] = (!empty($visitorData['check_out_date'])) ? date('Y-m-d', strtotime($visitorData['check_out_date'])) : "";
            $visitorData['hotels'] = Hotel::all()->toArray();
        }
        return view('internalFrm')->with($visitorData);
    }

    public function checkVisitor(Request $request) {
        $visitorData = Visitor::where('name', $request->pname)->whereRaw("name COLLATE utf8mb4_general_ci = '". $request->pname."'")->where('contact', $request->mno)->whereIn('category_id', [1, 46])->first();
        if ($visitorData) {
            return [
                'hosted' => 1,
                'id' => $visitorData->id
            ];
        }
        return [
            'hosted' => 0
        ];
    }

    public function store(Request $request)
    {
        $alreadyHostedBy = "";
        $visitorBarcode = "";
        if (empty($request->id)) {
            return redirect()->back()->with('success', 'Record Not Found!');
        }
        if (!empty($request->id)) {
            $visitor = Visitor::find($request->id);
            $alreadyHostedBy = $visitor->hosted_by;
            $visitorBarcode = $visitor->barcode;
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
        $visitor->hotel_id = (!empty($request->hotel)) ? $request->hotel : 1;
        $visitor->room_number = (!empty($request->hotelRoomNo)) ? $request->hotelRoomNo : 0;
        $visitor->hospitality_status = "na";
        $visitor->hospitality_status = "na";
        $visitor->departure_city_id = 1;

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