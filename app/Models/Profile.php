<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    /**
     * Inverse of the one-to-one relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
