<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class invoices extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    public function section()
   {
   return $this->belongsTo('App\sections');
   }
}

