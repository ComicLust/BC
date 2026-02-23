<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BacklinkCheckProgress extends Model
{
    protected $fillable = [
        'project_id', 'total', 'checked', 'status', 'result', 'started_at', 'finished_at'
    ];

    protected $casts = [
        'result' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
