<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteePost extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'committee_id',
        'author_id',
        'title',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the committee that owns the post.
     */
    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    /**
     * Get the author of the post.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
