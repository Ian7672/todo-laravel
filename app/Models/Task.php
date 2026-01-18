<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_task';

    // Isi field yang dapat diisi secara massal
    protected $fillable = ['title', 'deadline','description', 'id', 'completed'];
    // App\Models\Task.php
protected $casts = [
        'completed' => 'boolean',
        'deadline' => 'datetime',
];

}
