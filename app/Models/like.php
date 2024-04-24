<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

# corrected the type of the model name
class Like extends Model
{
    use HasFactory;

    public $timestamps = false;
}
