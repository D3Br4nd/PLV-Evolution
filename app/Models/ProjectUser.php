<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectUser extends Pivot
{
    use HasUuids, LogsActivity;

    protected $table = 'project_user';

    public $incrementing = false;

    protected $keyType = 'string';

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
