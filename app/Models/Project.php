<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Project extends Model
{
    use HasFactory, HasUuids, \App\Traits\LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'content',
        'status',
        'priority',
        'deadline',
        'committee_id',
        'assignee_id',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->using(ProjectUser::class)
            ->withTimestamps();
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
