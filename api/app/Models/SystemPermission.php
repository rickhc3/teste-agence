<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPermission extends Model
{
    protected $table = 'permissao_sistema';
    protected $primaryKey = 'co_usuario';

    public function user()
    {
        return $this->belongsTo(Consultant::class, 'co_usuario', 'co_usuario');
    }
}
