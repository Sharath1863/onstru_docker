<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $table = 'commission';

    protected $fillable = [
        'category_id',
        'commission',
        'status',
        'created_by',
    ];

    public function categoryRelation()
    {
        return $this->belongsTo(DropdownList::class, 'category_id');
    }

}
