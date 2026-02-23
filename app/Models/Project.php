<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_url',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function backlinks()
    {
        return $this->hasMany(Backlink::class);
    }

    public function metrics()
    {
        return $this->hasMany(Metric::class);
    }
}
