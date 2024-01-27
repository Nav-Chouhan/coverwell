<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Hash;
use Session;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Facades\Auth;

class CustomAuthController extends Controller
{
    public function index()
    {
        return view('auth.hospitality-login');
    }  
      
    public function hospitalityLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
   
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            session()->put('jasInternalLogin', $user['id']);
            if($user->hasAnyRole(['Hospitality Travel','Hospitality Hotel'])) {
                return redirect()->intended('dashboard')->withSuccess('Signed in');
            }
            if ($user->hasPermissionTo('UpdateVisitorInfo')) {
                return redirect()->intended('dashboard')->withSuccess('Signed in');
            }
        }
        return redirect("hospitality-login")->withSuccess('Login details are not valid');
    }

   
    public function create(array $data)
    {
      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }    
    
    public function dashboard()
    {
        if(Auth::check()){
            return view('auth.dashboard');
        }
  
        return redirect("login")->withSuccess('You are not allowed to access');
    }
    
    public function signOut() {
        Session::flush();
        Auth::logout();
  
        return Redirect('login');
    }

    public function jasLoginFrm()
    {
        return view('auth.jas-login');
    } 

    public function jasLogin(Request $request)
    {
        $validator = $request->validate([
            'phoneNumber' => 'required'
        ]);
        
        $visitorData = Visitor::where('contact', $request->phoneNumber)->whereIn('category_id', [47,46])->first();
        if(empty($visitorData)) {
            return redirect("/jas-login")->withErrors(['phoneNumber' => "No Record Found."]);
        }
        session()->put('jasLogin', $visitorData['id']);
        if ($visitorData['category_id'] == 47) {
            return redirect('/hosted-by/' . $visitorData['id']);
        }
        return redirect('/registration/' . $visitorData['id']);
    }
}