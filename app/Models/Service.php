<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'duracao',
        'valor',
        'ativo',
        'is_admin_only',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'is_admin_only' => 'boolean',
        'valor' => 'decimal:2',
    ];
}