<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name','stock','code','id_category','weight','image','price','description'];

    protected $defaultImagePath = 'uploads/no-image.png';

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetails::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function getImageAttribute($value)
    {        
        return $value ? $value : $this->defaultImagePath;
    }
}
