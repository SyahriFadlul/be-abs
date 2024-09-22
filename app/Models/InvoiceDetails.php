<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function invoices() : BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function products() : BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    
}
