<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'cao_fatura';
    protected $primaryKey = 'co_fatura';

    public function orderService(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OrderService::class, 'co_os', 'co_os');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class, 'co_cliente', 'co_cliente');
    }

    public function system(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(System::class, 'co_sistema', 'co_sistema');
    }
}
