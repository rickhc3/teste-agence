<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'cao_cliente';
    protected $primaryKey = 'co_cliente';

    public function invoices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Invoice::class, 'co_cliente', 'co_cliente');
    }

    public function orderServices(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderService::class, 'co_cliente', 'co_cliente');
    }
}
