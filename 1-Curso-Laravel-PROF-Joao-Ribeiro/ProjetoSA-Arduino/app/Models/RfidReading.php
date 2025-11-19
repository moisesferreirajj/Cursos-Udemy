<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RfidReading extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tag_id',
        'reader_id',
        'location',
        'product_name',
        'product_code',
        'status',
        'temperature',
        'signal_strength',
        'notes',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'temperature' => 'decimal:2',
        'signal_strength' => 'integer',
    ];

    /**
     * Relacionamento com o leitor RFID
     */
    public function reader()
    {
        return $this->belongsTo(RfidReader::class, 'reader_id', 'reader_id');
    }

    /**
     * Relacionamento com a tag RFID
     */
    public function tag()
    {
        return $this->belongsTo(RfidTag::class, 'tag_id', 'tag_id');
    }

    /**
     * Scope para filtrar leituras recentes
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('read_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por leitor
     */
    public function scopeByReader($query, $readerId)
    {
        return $query->where('reader_id', $readerId);
    }

    /**
     * Scope para filtrar por tag
     */
    public function scopeByTag($query, $tagId)
    {
        return $query->where('tag_id', $tagId);
    }

    /**
     * Estatísticas de leituras
     */
    public static function getStats($period = 'today')
    {
        $query = self::query();

        switch ($period) {
            case 'today':
                $query->whereDate('read_at', today());
                break;
            case 'week':
                $query->whereBetween('read_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('read_at', now()->month);
                break;
        }

        return [
            'total' => $query->count(),
            'entradas' => (clone $query)->where('status', 'entrada')->count(),
            'saidas' => (clone $query)->where('status', 'saida')->count(),
            'movimentacoes' => (clone $query)->where('status', 'movimentacao')->count(),
        ];
    }
} 