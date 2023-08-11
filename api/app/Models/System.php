<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    protected $table = 'cao_sistema';
    protected $primaryKey = 'co_sistema';

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class, 'co_sistema', 'co_sistema');
    }

    public function permissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SystemPermission::class, 'co_sistema', 'co_sistema');
    }


}
