<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    /**
     * Atribut mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
}