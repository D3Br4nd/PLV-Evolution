<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the members associated with the committee.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'committee_user')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Get the posts for the committee.
     */
    public function posts()
    {
        return $this->hasMany(CommitteePost::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the user who created the committee.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
