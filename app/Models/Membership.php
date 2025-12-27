<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Membership extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'year',
        'qr_token',
        'status',
    ];

    /**
     * Get the user that owns the membership.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
