<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "sender_name",
        "sender_contact_no",
        "sender_address",
        "sender_city",
        "sender_location_url",
        "recipient_name",
        "recipient_contact_no",
        "recipient_address",
        "recipient_city",
        "recipient_location_url",
        "image_url",
        "remarks",
        "status",
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
