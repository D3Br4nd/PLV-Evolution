<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteePost extends Model
{
    use HasFactory, HasUuids, \App\Traits\LogsActivity;

    protected $fillable = [
        'committee_id',
        'author_id',
        'title',
        'content',
        'featured_image_path',
        'attachment_path',
        'attachment_name',
    ];

    protected $appends = ['featured_image_url', 'attachment_url'];

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

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the users who have read this post.
     */
    public function readers()
    {
        return $this->belongsToMany(User::class, 'committee_post_read', 'post_id', 'user_id')
            ->withPivot('read_at');
    }

    /**
     * Get the featured image URL.
     */
    public function getFeaturedImageUrlAttribute(): ?string
    {
        if (!$this->featured_image_path) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->featured_image_path);
    }

    /**
     * Get the attachment URL.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->attachment_path);
    }
}
