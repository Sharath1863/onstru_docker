<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class DropdownList extends Model
{
    protected $fillable = ['dropdown_id', 'value', 'status'];
    public function dropdown()
    {
        return $this->belongsTo(dropdowns::class, 'dropdown_id');
    }
}
