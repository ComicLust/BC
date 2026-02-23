<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_url',
        'target_url',
        'status',
        'project_id',
        'last_checked_at',
        'details',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
