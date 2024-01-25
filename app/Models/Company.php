<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Hashids\Hashids;
use \Venturecraft\Revisionable\RevisionableTrait;

class Company extends Model
{
     use CrudTrait;
    use RevisionableTrait;
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'companies';
    // protected $primaryKey = 'id';
     public $timestamps = true;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];

    protected $revisionEnabled = false;
    protected $revisionCleanup = true; //Remove old revisions (works only when used with $historyLimit)
    protected $historyLimit = 500; //Maintain a maximum of 500 changes at any point of time, while cleaning up old revisions.

    public function __construct(array $attributes = array())
    {
        $statement = \DB::select("SHOW TABLE STATUS LIKE 'companies'");
        $nextId = $statement[0]->Auto_increment;
        $hashids = new Hashids('', 5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890');
        $this->setRawAttributes([
            'barcode' => $hashids->encode($nextId)
        ], true);
        parent::__construct($attributes);
    }

    public function identifiableName()
    {
        return $this->name;
    }

    public function scopeHasGst($query)
    {
        return $query->where('gst_certificate', '!=', null);
    }

    public function visitors($count = false)
    {
        if ($count)
            return $this->hasMany('App\Models\Visitor','company_barcode','barcode')->count();
        else
            return $this->hasMany('App\Models\Visitor', 'company_barcode','barcode');
    }

    public function setGstCertificateAttribute($value)
    {
        $attribute_name = "gst_certificate";
        $disk = "public";
        $destination_path = "uploads/gst_certificate";
        $this->uploadFileToDisk($value, $attribute_name, $disk, $destination_path);
    }

    public function uploadFileToDisk($value, $attribute_name, $disk, $destination_path)
    {
        // if a new file is uploaded, delete the file from the disk
        if (
            request()->hasFile($attribute_name) &&
            $this->{$attribute_name} &&
            $this->{$attribute_name} != null
        ) {
            \Storage::disk($disk)->delete($this->{$attribute_name});
            $this->attributes[$attribute_name] = null;
            return;
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
            $new_file_name = $this->barcode . '.' . $file->getClientOriginalExtension();

            // 2. Move the new file to the correct path
            $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

            // 3. Save the complete path to the database
            $this->attributes[$attribute_name] = $file_path;
            return;
        }
        $this->attributes[$attribute_name] = $value;
    }

    public function getGstCertificateAttribute($value)
    {
        
        $attribute_name = "gst_certificate";
        $disk = "public";
        $destination_path = "uploads/gst_certificate";
         
        if ($this->getRawOriginal($attribute_name) && \Storage::disk($disk)->exists($this->getRawOriginal($attribute_name)))
            return $value;
        return null;
    }

    public static function boot()
    {
        parent::boot();
        /* static::deleting(function($obj) {
            //\Storage::disk('public_folder')->delete($obj->gst_certificate);
        }); */
    }
}
