<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfidTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tag_id',
        'product_name',
        'product_code',
        'category',
        'type',
        'status',
        'description',
    ];

    /**
     * Relacionamento com leituras
     */
    public function readings()
    {
        return $this->hasMany(RfidReading::class, 'tag_id', 'tag_id');
    }

    /**
     * Obter última leitura
     */
    public function lastReading()
    {
        return $this->hasOne(RfidReading::class, 'tag_id', 'tag_id')
            ->latest('read_at');
    }

    /**
     * Obter localização atual
     */
    public function getCurrentLocation()
    {
        $lastReading = $this->lastReading()->first();
        return $lastReading ? $lastReading->location : 'Desconhecida';
    }

    /**
     * Scope para tags ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para tags por tipo
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}