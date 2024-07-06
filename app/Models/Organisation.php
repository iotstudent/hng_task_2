<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\UUID;

class Organisation extends Model
{
    use HasFactory,UUID;

    protected $primaryKey = 'orgId';

    protected $fillable = [

        'orgId',
        'name',
        'description',

    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
