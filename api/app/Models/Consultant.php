<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultant extends Model
{
    protected $table = 'cao_usuario';
    protected $primaryKey = 'co_usuario';
    public $incrementing = false;

    public function orderServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderService::class, 'co_usuario', 'co_usuario');
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SystemPermission::class, 'co_usuario', 'co_usuario');
    }

    public function salary()
    {
        return $this->hasOne(Salary::class, 'co_usuario', 'co_usuario');
    }

}
