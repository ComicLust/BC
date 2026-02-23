<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'type',
        'value',
        'date',
    ];

    protected $casts = [
        'value' => 'float',
        'date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
