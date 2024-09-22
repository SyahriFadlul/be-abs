<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];
    // protected $fillable = [
    //     'id'
    // ];

    public $incrementing = false;


    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id= IdGenerator::generate([
                'table' => 'invoices', 
                'length' => 13, 
                'prefix' => 'INV-' . date('ym'),
                'reset_on_prefix_change' => true
            ]);
            $model->status = 'Unpaid';
        });
    }

    public function invoiceDetails() : HasMany
    {
        return $this->hasMany(InvoiceDetails::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
