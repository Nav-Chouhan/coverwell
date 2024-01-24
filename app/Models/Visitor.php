<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;
use Carbon\Carbon;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManagerStatic as Image;
use Config;
use Exception;
use Mail;



class Visitor extends Model
{
    use CrudTrait;
    use RevisionableTrait;


    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'visitors';
    // protected $primaryKey = 'id';
    public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
        'registered_on', 'verified_on', 'printed_on', 'arival', 'departure'
    ];

    protected $revisionEnabled = false;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 1; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        static::created(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                $str = "Visitor Registered by ". $user->name . " on " . date_format(Carbon::now(), "Y/m/d G:ia");
                activity('register')
                ->performedOn($model)
                ->causedBy(backpack_user())
                ->log($str);
            }
            
        });
    }

    public function identifiableName()
    {
        return $this->name;
    }

    public function __construct(array $attributes = array())
    {
        $statement = \DB::select("SHOW TABLE STATUS LIKE 'visitors'");
        $nextId = $statement[0]->Auto_increment;
        $hashids = new Hashids('', 5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $this->setRawAttributes([
            'barcode' => $hashids->encode($nextId),
            'src' => 'panel',
            'registered_on' => Carbon::now()
        ], true);
        parent::__construct($attributes);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function setPhotoAttribute($value)
    {
        $attribute_name = "photo";
        $disk = "public";
        $destination_path = "/uploads/photo";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function getPhoto64()
    {
        try {
            return \Image::make(public_path($this->photo))->encode('data-url')->encoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public function setPrintedOnAttribute($value)
    {
        if ($value == null) {
            $this->attributes['printed_on'] = null;
            return;
        }
        date_format($value, "Y/m/d H:i:s");
        $str = "Re-Printed by :causer.name on " . date_format($value, "Y/m/d G:ia");
        if ($this->printed_on == null) {
            $this->attributes['printed_on'] = $value;
            $str = "Printed by :causer.name on " . date_format($value, "Y/m/d G:ia");;
        }
        activity('printed')
            ->performedOn($this)
            ->causedBy(backpack_user())
            ->log($str);
    }

    public function setHostedBuyerAttribute($value)
    {
        $this->attributes['hosted_buyer'] = $value;
        if ($value === "1") {
            date_format(Carbon::now(), "Y/m/d H:i:s");
            $str = "Hosted by :causer.name on " . date_format(Carbon::now(), "Y/m/d G:ia");
            activity('hosted')
            ->performedOn($this)
                ->causedBy(backpack_user())
                ->log($str);
        }
    }

    public function setVerifiedOnAttribute($value)
    {
        if ($value == true) {
            $value = Carbon::now();
        }

        if ($value == null || $value == false) {
            $this->attributes['verified_on'] = null;
            return;
        } else {
            if ($this->verified_on == null) {
                $this->attributes['verified_on'] = $value;
                activity('verified')
                    ->performedOn($this)
                    ->causedBy(backpack_user())
                    ->log("Verified by :causer.name on " . date_format($value, "Y/m/d G:ia"));
            }
        }
    }

    public function sendSMS()
    {
        $msg = "Hi $this->name, We are excited to see you at the The Grand Abhushanam'21 held on 18th %26 19th December at SS Convention Center, Vijayawada. Your Unique Registration No. is  $this->barcode. Use this registration no. with your photo id %26 business card to get your entry pass at venue. Thanks Team Grand Abhushanam *Terms and Conditions Applicable";
        $client = new \GuzzleHttp\Client();
        $url = "http://sms.mdsmedia.in/http-api.php?username=GJCTR&password=pass1234&senderid=GJCGAB&route=1&number=$this->contact&message=$msg";
        $response = $client->request('GET', $url);
        return true;
        /*Mail::send('mail', $this->toArray(), function($message) {
        $message->to($this->email, $this->name)->cc(['vaishali@jaipurjewelleryshow.org'])->subject('Jaipur Jewellery Show 2019 Registration');
        $message->from('noreply@jjsjaipur.com','JJS Registration');
        });*/
    }

    public function sendJAOTP()
    {
        $otp  = $this->generateOtp();
        $msg = "Your OTP is $otp->otp for Badge Request. Please do not share OTP to anyone.%0ARegards,%0AJewellers Association, Jaipur";
        
        $client = new \GuzzleHttp\Client();
        $date = Carbon::Now()->toDateTimeString();
        $url = "https://agranii.in/api/api_http.php?username=JASJPR&password=api@2022&senderid=JASJPR&to=$this->contact&text=$msg&route=Informative&type=text&datetime=$date";
        
        $response = $client->request('GET', $url);
        return true;
        /*Mail::send('mail', $this->toArray(), function($message) {
        $message->to($this->email, $this->name)->cc(['vaishali@jaipurjewelleryshow.org'])->subject('Jaipur Jewellery Show 2019 Registration');
        $message->from('noreply@jjsjaipur.com','JJS Registration');
        });*/
    }

    public function generateOtp()
    {
        $verificationCode = VerificationCode::where('visitor_id', $this->id)->latest()->first();

        $now = Carbon::now();

        if($verificationCode && $now->isBefore($verificationCode->expire_at)){
            return $verificationCode;
        }

        // Create a New OTP
        return VerificationCode::create([
            'visitor_id' => $this->id,
            'otp' => rand(7536, 7536),
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }

    public function verifyOTP($otp)
    {
        $verificationCode   = VerificationCode::where('visitor_id', $this->id)->where('otp', $otp)->latest()->first();
        $now = Carbon::now();
        if (!$verificationCode) {

            return false;

        } elseif ($verificationCode && $now->isAfter($verificationCode->expire_at)) {

            return false;
        }

        $verificationCode->update([
            'expire_at' => Carbon::now()
        ]);

        return true;
    }

    public function uploadFileToDisk($value, $attribute_name, $disk, $destination_path, $sufix = "")
    {
        // if a new file is uploaded, delete the file from the disk
        if (
            request()->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            $this->{$attribute_name} != null
        ) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
            // return;
        }

        // if the file input is empty, delete the file from the disk
        if (is_null($value) && $this->{$attribute_name} != null) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
            return;
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if (request()->hasFile($attribute_name) && request()->file($attribute_name)->isValid()) {
            // 1. Generate a new file name
            $file = request()->file($attribute_name);
            //$new_file_name = md5($file->getClientOriginalName().random_int(1, 9999).time()).'.'.$file->getClientOriginalExtension();
            $new_file_name = $this->barcode . $sufix . '.' . $file->getClientOriginalExtension();

            // 2. Move the new file to the correct path
            $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

            // 3. Save the complete path to the database
            $this->attributes[$attribute_name] = $file_path;
            return;
        }
        if (\Str::startsWith($value, 'data:image')) {
            $image = \Image::make($value)->encode('jpeg', 100);
            $filename = $this->barcode . $sufix . '.jpeg';
            $file_path = \Storage::disk($disk)->put($destination_path . '/' . $filename, $image->stream());
            $this->attributes[$attribute_name] = $destination_path . '/' . $filename;
            return;
        }
        $this->attributes[$attribute_name] = $value;
    }

    public function setIdproofAttribute($value)
    {
        $attribute_name = "idproof";
        $disk = "public";
        $destination_path = "/uploads/idproof";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setIdproofBackAttribute($value)
    {
        $attribute_name = "idproof_back";
        $disk = "public";
        $destination_path = "/uploads/idproof";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path, "-back");
    }

    public function setVaccineAttribute($value)
    {
        $attribute_name = "vaccine";
        $disk = "public";
        $destination_path = "/uploads/vaccine";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setTravelTicketAttribute($value)
    {
        $attribute_name = "travel_ticket";
        $disk = "public";
        $destination_path = "/uploads/traveltickets";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function setHospitalityStatusAttribute($value)
    {
        /* if ($this->attributes['hospitality_status'] != $value) {
            $str =  "Status Updated to " . $value . " by :causer.name on " . date_format(Carbon::now(), "Y/m/d G:ia");
            activity('hospitality')
                ->performedOn($this)
                ->causedBy(backpack_user())
                ->log($str);
            $this->attributes['hospitality_status'] = $value;
        } */
    }

    public function arrival_city()
    {
        return $this->belongsTo(City::class);
    }

    /* public function departure_city()
    {
        return $this->belongsTo(City::class);
    } */

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_barcode', 'barcode', 'company_barcode');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\VisitorCategory', 'category_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'current_location');
    }

    public function activities()
    {
        return $this->hasMany('Spatie\Activitylog\Models\Activity', 'subject_id');
    }
    public function getActivitiesJsonAttribute($value)
    {
        return $this->activities->toJSON();
    }

    public function hospitality()
    {
        return $this->hasOne(Hospitality::class);
    }

    public function checkSelf()
    {
        $change = false;
        if (!file_exists(base_path("./public$this->photo"))) {
            $this->photo = null;
            $change = true;
        }
        if ($change) {
            $this->save();
        }
    }

    public function print_count()
    {
        return $this->hasMany('Spatie\Activitylog\Models\Activity', 'subject_id')->where('log_name', 'printed')->count();
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
