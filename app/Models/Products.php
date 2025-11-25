<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'created_by',
        'name',
        'brand_name',
        'category',
        'd_days',
        'd_km',
        'size',
        'hsn',
        'availability',
        'location',
        'hub_id',
        'mrp',
        'sp',
        'tax_percentage',
        'product_unit',
        'cashback_price',
        'margin',
        'moq',
        'base_price',
        'ship_charge',
        'key_feature',
        'description',
        'highlighted',
        'click',
        'catlogue',
        'image',
        'cover_img',
        'video',
        'specifications',
        'cover_img',
        'status',
        'approvalstatus',
        'remark',
    ];

    public function vendor()
    {
        return $this->belongsTo(UserDetail::class, 'created_by', 'id');
    }

    public function locationRelation()
    {
        return $this->belongsTo(DropdownList::class, 'location');
    }

    public function categoryRelation()
    {
        return $this->belongsTo(DropdownList::class, 'category');
    }

    public function hubRelation()
    {
        return $this->belongsTo(Hub::class, 'hub_id');
    }

    public function hub()
    {
        return $this->belongsTo(Hub::class, 'hub_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'product_id');
    }

    public function boosts()
    {
        return $this->hasMany(ProductBoost::class, 'product_id');
    }

    public function clicks()
    {
        return $this->hasMany(Click::class, 'category_id')
            ->where('category', 'Product');
    }

    public function add_to_cart()
    {
        return $this->hasMany(Cart::class, 'product_id')->where('c_by', auth()->id());
    }

    public function wishlist()
    {
        return $this->hasMany(SavedProduct::class, 'product_id')->where('c_by', auth()->id());
    }
}
