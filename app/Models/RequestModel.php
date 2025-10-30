<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestModel extends Model
{
    use HasFactory;
     protected $table = 'requests';

        protected $fillable = [
        'request_type',
        'user_id',
        'driver_id',
        'car_id',
        'description',
        'date',
        'time',
        'image_url',
        'status',
        'driver_status',
        'approval_date',
        'approval_time',
        'created_at',
        'updated_at'
    ];
}
