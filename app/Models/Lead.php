<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = ['serialNo', 'date', 'customerName', 'companyName', 'phone', 'industry', 'leadSource', 'leadStatus', 'revenue', 'product', 'description', 'responsiblePerson', 'username', 'datetime', 'updateusername', 'updatedatetime'];
}
