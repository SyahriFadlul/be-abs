<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];

    // protected $fillable =[
    //     'user_id',
    //     'fullname',
    //     'phonenumber',
    //     'addresslabel',
    //     'city',
    //     'streetbuilding',
    //     'detail',
    // ];

    protected $attributes = [
        'is_main_address'=>false
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}

