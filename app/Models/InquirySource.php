<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquirySource extends Model
{
    protected $fillable = ['Name', 'description', 'status'];
}
