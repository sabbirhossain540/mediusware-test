<?php

namespace Bulkly;

use Illuminate\Database\Eloquent\Model;

class FreeSignUp extends Model
{
     protected $fillable = [
        'url', 'code', 'token'
    ];
}
