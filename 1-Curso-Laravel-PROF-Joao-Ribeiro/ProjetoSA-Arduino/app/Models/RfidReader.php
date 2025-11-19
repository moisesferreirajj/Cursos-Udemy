<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfidReader extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reader_id',
        'name',
        'location',
        'ip_address',
        'status',
        'last_ping',
        'description',
    ];

    protected $casts = [
        'last_ping' => 'datetime',
    ];

    /**
     * Relacionamento com leituras
     */
    public function readings()
    {
        return $this->hasMany(RfidReading::class, 'reader_id', 'reader_id');
    }

    /**
     * Verificar se o leitor está online
     */
    public function isOnline()
    {
        if (!$this->last_ping) {
            return false;
        }

        return $this->last_ping->diffInMinutes(now()) <= 5;
    }

    /**
     * Atualizar ping do leitor
     */
    public function updatePing()
    {
        $this->update([
            'last_ping' => now(),
            'status' => 'online',
        ]);
    }

    /**
     * Scope para leitores online
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online')
            ->where('last_ping', '>=', now()->subMinutes(5));
    }

    /**
     * Scope para leitores offline
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline')
            ->orWhere('last_ping', '<', now()->subMinutes(5));
    }
}