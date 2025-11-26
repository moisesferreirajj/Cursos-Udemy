<?php

namespace App\Http\Controllers;

use App\Models\RfidReading;
use App\Models\RfidReader;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display dashboard
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_readings_today' => RfidReading::whereDate('read_at', today())->count(),
            'total_readers' => RfidReader::count(),
            'readers_online' => RfidReader::online()->count(),
            'readers_offline' => RfidReader::offline()->count(),
            'total_tags' => RfidTag::active()->count(),
            'entradas_hoje' => RfidReading::whereDate('read_at', today())->where('status', 'entrada')->count(),
            'saidas_hoje' => RfidReading::whereDate('read_at', today())->where('status', 'saida')->count(),
            'movimentacoes_hoje' => RfidReading::whereDate('read_at', today())->where('status', 'movimentacao')->count(),
        ];

        // Leituras recentes (últimas 10)
        $recentReadings = RfidReading::with(['reader', 'tag'])
            ->orderBy('read_at', 'desc')
            ->limit(10)
            ->get();

        // Status dos leitores
        $readers = RfidReader::withCount([
            'readings' => function ($query) {
                $query->whereDate('read_at', today());
            }
        ])->get();

        // Gráfico de leituras por hora (últimas 24h)
        $readingsByHour = RfidReading::select(
            DB::raw('HOUR(read_at) as hour'),
            DB::raw('COUNT(*) as total')
        )
            ->where('read_at', '>=', now()->subHours(24))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Top 5 tags mais lidas hoje
        $topTags = RfidReading::select('tag_id', 'product_name', DB::raw('COUNT(*) as total'))
            ->whereDate('read_at', today())
            ->groupBy('tag_id', 'product_name')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Leituras por localização
        $readingsByLocation = RfidReading::select('location', DB::raw('COUNT(*) as total'))
            ->whereDate('read_at', today())
            ->groupBy('location')
            ->orderBy('total', 'desc')
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentReadings',
            'readers',
            'readingsByHour',
            'topTags',
            'readingsByLocation'
        ));
    }

    /**
     * API endpoint para receber dados do ESP32
     */
    public function storeReading(Request $request)
    {
        $validated = $request->validate([
            'tag_id' => 'required|string|max:50',
            'reader_id' => 'required|string|max:50',
            'location' => 'nullable|string|max:100',
            'product_name' => 'nullable|string|max:255',
            'product_code' => 'nullable|string|max:100',
            'status' => 'nullable|in:entrada,saida,movimentacao',
            'temperature' => 'nullable|numeric',
            'signal_strength' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        // Pegar IP do ESP32
        $ipAddress = $request->ip();
        
        // Tentar obter localização aproximada do IP
        $locationFromIp = $this->getLocationFromIp($ipAddress);

        // Atualizar/criar reader
        $reader = RfidReader::updateOrCreate(
            ['reader_id' => $validated['reader_id']],
            [
                'name' => 'Leitor ' . $validated['reader_id'],
                'location' => $locationFromIp ?: ($validated['location'] ?? 'Não especificado'),
                'ip_address' => $ipAddress,
                'status' => 'online',
                'last_ping' => now(),
            ]
        );

        // ===================================================
        // CORRIGIDO: Verificar/criar TAG sem sobrescrever
        // ===================================================
        if (!empty($validated['tag_id'])) {
            $existingTag = RfidTag::where('tag_id', $validated['tag_id'])->first();
            
            if (!$existingTag) {
                // TAG NÃO EXISTE - Criar nova
                RfidTag::create([
                    'tag_id' => $validated['tag_id'],
                    'product_name' => $validated['product_name'] ?? 'Produto ' . $validated['tag_id'],
                    'product_code' => $validated['product_code'] ?? null,
                    'type' => 'produto',
                    'status' => 'ativo',
                ]);
            }
        }

        // ===================================================
        // NOVO: Buscar dados da TAG se não fornecidos
        // ===================================================
        $productName = $validated['product_name'] ?? null;
        $productCode = $validated['product_code'] ?? null;
        
        // Se product_name ou product_code forem null/vazios, buscar da tabela rfid_tags
        if (empty($productName) || empty($productCode)) {
            $tag = RfidTag::where('tag_id', $validated['tag_id'])->first();
            
            if ($tag) {
                // Se não foi enviado product_name, pega da TAG
                if (empty($productName)) {
                    $productName = $tag->product_name;
                }
                
                // Se não foi enviado product_code, pega da TAG
                if (empty($productCode)) {
                    $productCode = $tag->product_code;
                }
            }
        }

        // ===================================================
        // SEMPRE criar nova leitura com dados da TAG
        // ===================================================
        $reading = RfidReading::create([
            'tag_id' => $validated['tag_id'],
            'reader_id' => $validated['reader_id'],
            'location' => $locationFromIp ?: ($validated['location'] ?? $reader->location),
            'product_name' => $productName, // ← Usa nome da TAG se não foi enviado
            'product_code' => $productCode, // ← Usa código da TAG se não foi enviado
            'status' => $validated['status'] ?? 'movimentacao',
            'temperature' => $validated['temperature'] ?? null,
            'signal_strength' => $validated['signal_strength'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leitura registrada com sucesso',
            'data' => [
                'reading_id' => $reading->id,
                'tag_id' => $reading->tag_id,
                'reader_id' => $reading->reader_id,
                'product_name' => $reading->product_name,
                'product_code' => $reading->product_code,
                'status' => $reading->status,
                'timestamp' => $reading->read_at,
            ],
            'ip_address' => $ipAddress,
            'location' => $locationFromIp,
        ], 201);
    }

    /**
     * Obter localização aproximada a partir do IP
     */
    private function getLocationFromIp($ip)
    {
        // IPs locais não tem localização geográfica
        if ($this->isPrivateIp($ip)) {
            return "Rede Local ({$ip})";
        }

        try {
            // Usando API gratuita do ip-api.com
            $response = file_get_contents("http://ip-api.com/json/{$ip}?fields=status,city,regionName,country,lat,lon");
            $data = json_decode($response, true);

            if ($data && $data['status'] === 'success') {
                $location = [];
                
                if (!empty($data['city'])) {
                    $location[] = $data['city'];
                }
                if (!empty($data['regionName'])) {
                    $location[] = $data['regionName'];
                }
                if (!empty($data['country'])) {
                    $location[] = $data['country'];
                }

                $locationString = implode(', ', $location);
                
                // Adicionar coordenadas se disponíveis
                if (!empty($data['lat']) && !empty($data['lon'])) {
                    $locationString .= " (Lat: {$data['lat']}, Lon: {$data['lon']})";
                }

                return $locationString ?: null;
            }
        } catch (\Exception $e) {
            // Se falhar, retorna null
            \Log::warning("Erro ao obter localização do IP {$ip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Verificar se é IP privado/local
     */
    private function isPrivateIp($ip)
    {
        $private_ranges = [
            '10.0.0.0|10.255.255.255',
            '172.16.0.0|172.31.255.255',
            '192.168.0.0|192.168.255.255',
            '127.0.0.0|127.255.255.255',
        ];

        $long_ip = ip2long($ip);
        if ($long_ip != -1) {
            foreach ($private_ranges as $range) {
                list($start, $end) = explode('|', $range);
                if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * API para obter estatísticas em tempo real
     */
    public function getRealtimeStats()
    {
        $stats = [
            'total_today' => RfidReading::whereDate('read_at', today())->count(),
            'last_hour' => RfidReading::where('read_at', '>=', now()->subHour())->count(),
            'readers_online' => RfidReader::online()->count(),
            'readers_total' => RfidReader::count(),
            'last_reading' => RfidReading::with(['reader', 'tag'])
                ->latest('read_at')
                ->first(),
        ];

        return response()->json($stats);
    }

    /**
     * API para obter leituras recentes
     */
    public function getRecentReadings(Request $request)
    {
        $limit = $request->input('limit', 20);

        $readings = RfidReading::with(['reader', 'tag'])
            ->orderBy('read_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($readings);
    }
}