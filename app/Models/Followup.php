<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    protected $fillable = ['serialNo', 'lead_serialNo', 'date', 'customerName', 'companyName', 'phone', 'industry', 'description', 'responsiblePerson_id', 'username', 'datetime'];
}
