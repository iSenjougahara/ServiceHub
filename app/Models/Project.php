<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'email',
        'status',
        'start_date',
        'end_date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function userProfiles(): BelongsToMany
    {
        return $this->belongsToMany(UserProfile::class);
    }
}
