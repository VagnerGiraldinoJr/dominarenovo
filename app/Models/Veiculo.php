<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    protected $fillable = [
            "placa","modelo","celular","tipo","observacao","unidade_id"
        ];
}
