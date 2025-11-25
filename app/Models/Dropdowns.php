<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DropdownList;

class Dropdowns extends Model
{
    protected $fillable = ['dropdowns', 'status'];
    public function dropdownLists()
    {
        return $this->hasMany(DropdownList::class, 'dropdown_id');
    }
}
