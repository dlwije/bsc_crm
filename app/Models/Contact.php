<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = ['serialNo', 'date', 'customerName', 'companyName', 'phone', 'email', 'reason', 'description', 'responsiblePerson', 'username', 'datetime', 'updateusername', 'updatedatetime'];
}
