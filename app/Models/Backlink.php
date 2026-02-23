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
        'rel_attribute',
        'anchor_text',
        'is_indexed',
    ];

    protected $casts = [
        'last_checked_at' => 'datetime',
        'is_indexed' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
