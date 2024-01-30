<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait; // <------------------------------- this one
use Spatie\Permission\Traits\HasRoles;// <---------------------- and this one
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use CrudTrait; // <----- this
    use HasRoles; // <------ and this
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password', 'direction','location_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

       public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function scanVisitor($barcode)
    {
        $error = null;
        $visitor = Visitor::where('barcode', $barcode)->firstOrFail();
        if ($visitor->verified_on == null)
            $error = 'Visitor Not Verified';
        elseif ($visitor->threat == true)
            $error = 'Visitor Threat!!';
        elseif (!in_array($visitor->category_id, $this->location->categories->pluck('id')->toArray()))//&& !in_array($this->location->id, $visitor->additionalAccess->pluck('id')->toArray()))
            $error = 'Not Allowed!!';
        elseif ($this->location->valid_times != null) {
            if ($this->location->each_day) {
                $times = Activity::where('subject_id', $visitor->id)->where('log_name', $this->location->name)->whereDate('created_at', Carbon::today())->count();
            } else {
                $times = Activity::where('subject_id', $visitor->id)->where('log_name', $this->location->name)->count();
            }
            if ($times >= $this->location->valid_times)
                $error = 'PASS Already Consumed!!';
        }

        if ($error == null) {
            if ($this->hasRole('Exhibitor')) {
                if ($visitor->current_location != $this->location->id) {
                    $visitor->current_location = $this->location->id;
                    activity($this->location->name)
                        ->performedOn($visitor)
                        ->causedBy($this)
                        ->withProperties(["location" => $this->location->name, "direction" => 'In'])
                        ->log("Going :causer.direction :causer.location.name:  via :causer.name on " . Carbon::now());
                }
                activity('meet')
                    ->performedOn($visitor)
                    ->causedBy($this)
                    ->withProperties(["location" => $this->location->name, "direction" => $this->direction])
                    ->log("Meeting in :causer.location.name:  with :causer.name on " . Carbon::now());
            } else {
                activity($this->location->name)
                    ->performedOn($visitor)
                    ->causedBy($this)
                    ->withProperties(["location" => $this->location->name, "direction" => $this->direction])
                    ->log("Going :causer.direction :causer.location.name:  via :causer.name on " . Carbon::now());
                if ($this->direction == "In")
                    $visitor->current_location = $this->location->id;
                else
                    $visitor->current_location = null;
            }

            if (strpos($visitor->last_visited_years, (string)Carbon::now()->year) !== false) {
                //contains
            } else {
                if ($visitor->last_visited_years)
                    $visitor->last_visited_years .= "," . Carbon::now()->year;
                else
                    $visitor->last_visited_years .= Carbon::now()->year;
            }
            $visitor->last_movement = Carbon::now();
            $visitor->save();
        } else {
            activity('denied')
                ->performedOn($visitor)
                ->causedBy($this)
                ->withProperties(["location" => $this->location->name, "direction" => $this->direction])
                ->log("Access Denied($error) while going :causer.direction :causer.location.name:  via :causer.name on " . Carbon::now());
        }

        return ['visitor' => $visitor, 'error' => $error];
    }
}
