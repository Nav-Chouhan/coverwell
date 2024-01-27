<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\View\View;
use DB;
use Hash;
use Session;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;
use Laravel\Ui\Presets\React;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $users = User::all();
        // $users = DB::select('select * from visitors WHERE category_id=1');
        return view('auth.dashboard');
        //return view('auth.dashboard', compact('users'));
    }

    public function searchVisitor(Request $request)
    {
        $searchTerm = $request->searchTerm;
        $results = Visitor::where('category_id', '1')
            ->where('name', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('contact', 'LIKE', '%' . $searchTerm . '%')
            ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
            ->get();
        $sendResp = [];
        $user = Auth::user();
        foreach($results as $key => $rec) {
            $btn = "";
            if($user->hasRole('Hospitality Travel')) {
                $btn = 'traveler';
            } else if($user->hasRole('Hospitality Hotel')) {
                $btn = 'hoteler';
            } else if ($user->hasPermissionTo('UpdateVisitorInfo')) {
                $btn = 'updater';
            }
            $sendResp[] = [
                "id" => $rec->id,
                "name" => $rec->name,
                "email" => $rec->email,
                "contact" => $rec->contact,
                "showBtn" => $btn,
                "car_number" => $rec->car_number,
                "room_number" => $rec->room_number
            ];
        }
        return response()->json(
            [
                'results' => $sendResp
            ],
            200
        );
    }

    public function updateCarInfo(Request $req) {
    
        $visitor = Visitor::where('id', '=', $req->visitorId)->firstOrFail();
        $visitor->car_number = $req->carno;
        $visitor->save();
        return response()->json(
            [
                'message' => 'Updated'
            ],
            200
        );
    }

    public function updateRoomInfo(Request $req) {
        $visitor = Visitor::where('id', '=', $req->visitorId)->firstOrFail();
        $visitor->room_number = $req->roomno;
        $visitor->save();
        return response()->json(
            [
                'message' => 'Updated'
            ],
            200
        );
    }
}
