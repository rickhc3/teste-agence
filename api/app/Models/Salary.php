<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'cao_salario';
    public $incrementing = false;

    protected $primaryKey = 'co_usuario';

    public function consultant()
    {
        return $this->belongsTo(Consultant::class, 'co_usuario', 'co_usuario');
    }
}
