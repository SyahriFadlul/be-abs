<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    protected $guarded = [];
    // protected $fillable = [
    //     'firstName',
    //     'lastName',
    //     'gender',
    //     'birthday',
    //     'phone_number',
    //     'photo_profile',
    //     'user_id'
    // ];
    protected $date = ['birthday'];

    public function setBirthdayAttribute($birthday)
    {
        $this->attributes['birthday'] = Carbon::parse($birthday);
    }

}
