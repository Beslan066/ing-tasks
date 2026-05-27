<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_admin'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    // Связь с тикетом
    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    // Связь с администратором
    public function admin()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
