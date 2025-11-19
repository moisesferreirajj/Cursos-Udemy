<?php

namespace App\Http\Controllers;

use App\Models\RfidReading;
use App\Models\RfidReader;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RastreamentoController extends Controller
{
    /**
     * Display the tracking page
     */
    public function index(Request $request)
    {
        // Buscar todas as tags para o dropdown
        $allTags = RfidTag::orderBy('product_name')->get();
        
        // Inicializar variáveis
        $selectedTag = null;
        $lastReading = null;
        $reader = null;
        $recentMovements = collect();

        // Se houver tag_id na requisição
        if ($request->has('tag_id')) {
            $selectedTag = $this->getSelectedTag($request->tag_id);
            
            if ($selectedTag) {
                $lastReading = $this->getLastReading($request->tag_id);
                
                if ($lastReading) {
                    $reader = $this->getReaderInfo($lastReading->reader_id);
                }
                
                $recentMovements = $this->getRecentMovements($request->tag_id);
            }
        }

        return view('rastreamento', compact(
            'allTags',
            'selectedTag',
            'lastReading',
            'reader',
            'recentMovements'
        ));
    }

    /**
     * Get selected tag by ID
     */
    private function getSelectedTag($tagId)
    {
        return RfidTag::where('tag_id', $tagId)->first();
    }

    /**
     * Get last reading (current location)
     */
    private function getLastReading($tagId)
    {
        return RfidReading::where('tag_id', $tagId)
            ->orderBy('read_at', 'desc')
            ->first();
    }

    /**
     * Get reader information with IP and location
     */
    private function getReaderInfo($readerId)
    {
        return RfidReader::where('reader_id', $readerId)->first();
    }

    /**
     * Get recent movements (last 10)
     */
    private function getRecentMovements($tagId, $limit = 10)
    {
        return RfidReading::where('tag_id', $tagId)
            ->with('reader')
            ->orderBy('read_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * API: Get real-time location
     */
    public function getCurrentLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:rfid_tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tag = $this->getSelectedTag($request->tag_id);
        $lastReading = $this->getLastReading($request->tag_id);

        if (!$lastReading) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma leitura encontrada para esta tag',
            ], 404);
        }

        $reader = $this->getReaderInfo($lastReading->reader_id);

        return response()->json([
            'success' => true,
            'tag' => [
                'tag_id' => $tag->tag_id,
                'product_name' => $tag->product_name,
                'product_code' => $tag->product_code,
            ],
            'location' => [
                'current' => $lastReading->location,
                'reader_id' => $lastReading->reader_id,
                'reader_name' => $reader ? $reader->name : null,
                'ip_address' => $reader ? $reader->ip_address : null,
                'last_seen' => $lastReading->read_at->toIso8601String(),
                'time_ago' => $lastReading->read_at->diffForHumans(),
            ],
            'status' => $lastReading->status,
        ]);
    }

    /**
     * API: Get movement history
     */
    public function getMovementHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:rfid_tags,tag_id',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $limit = $request->input('limit', 10);
        $movements = $this->getRecentMovements($request->tag_id, $limit);

        return response()->json([
            'success' => true,
            'tag_id' => $request->tag_id,
            'total_movements' => $movements->count(),
            'movements' => $movements->map(function ($reading) {
                return [
                    'location' => $reading->location,
                    'reader_id' => $reading->reader_id,
                    'reader_name' => $reading->reader ? $reading->reader->name : null,
                    'ip_address' => $reading->reader ? $reading->reader->ip_address : null,
                    'status' => $reading->status,
                    'timestamp' => $reading->read_at->toIso8601String(),
                    'time_ago' => $reading->read_at->diffForHumans(),
                ];
            }),
        ]);
    }

    /**
     * API: Track multiple tags
     */
    public function trackMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_ids' => 'required|array|min:1|max:10',
            'tag_ids.*' => 'exists:rfid_tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $tracking = [];

        foreach ($request->tag_ids as $tagId) {
            $tag = $this->getSelectedTag($tagId);
            $lastReading = $this->getLastReading($tagId);

            if ($lastReading) {
                $reader = $this->getReaderInfo($lastReading->reader_id);

                $tracking[] = [
                    'tag_id' => $tagId,
                    'product_name' => $tag->product_name,
                    'location' => $lastReading->location,
                    'reader_id' => $lastReading->reader_id,
                    'ip_address' => $reader ? $reader->ip_address : null,
                    'status' => $lastReading->status,
                    'last_seen' => $lastReading->read_at->toIso8601String(),
                    'is_recent' => $lastReading->read_at->diffInMinutes(now()) <= 5,
                ];
            } else {
                $tracking[] = [
                    'tag_id' => $tagId,
                    'product_name' => $tag->product_name,
                    'location' => null,
                    'reader_id' => null,
                    'ip_address' => null,
                    'status' => null,
                    'last_seen' => null,
                    'is_recent' => false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'total_tags' => count($tracking),
            'tracking' => $tracking,
        ]);
    }

    /**
     * API: Get location by IP
     */
    public function getLocationByIp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ip_address' => 'required|ip',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Buscar leitores com esse IP
        $reader = RfidReader::where('ip_address', $request->ip_address)
            ->first();

        if (!$reader) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum leitor encontrado com este IP',
            ], 404);
        }

        // Buscar última leitura deste leitor
        $lastReading = RfidReading::where('reader_id', $reader->reader_id)
            ->with('tag')
            ->orderBy('read_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'ip_address' => $request->ip_address,
            'reader' => [
                'reader_id' => $reader->reader_id,
                'name' => $reader->name,
                'location' => $reader->location,
                'status' => $reader->status,
                'is_online' => $reader->isOnline(),
            ],
            'last_reading' => $lastReading ? [
                'tag_id' => $lastReading->tag_id,
                'product_name' => $lastReading->tag ? $lastReading->tag->product_name : null,
                'status' => $lastReading->status,
                'timestamp' => $lastReading->read_at->toIso8601String(),
            ] : null,
        ]);
    }

    /**
     * API: Get all active locations (heatmap data)
     */
    public function getActiveLocations()
    {
        // Últimas leituras de todas as tags (última hora)
        $recentReadings = RfidReading::where('read_at', '>=', now()->subHour())
            ->with(['tag', 'reader'])
            ->orderBy('read_at', 'desc')
            ->get()
            ->groupBy('tag_id')
            ->map(function ($group) {
                return $group->first();
            });

        $locations = $recentReadings->map(function ($reading) {
            return [
                'tag_id' => $reading->tag_id,
                'product_name' => $reading->tag ? $reading->tag->product_name : null,
                'location' => $reading->location,
                'reader_id' => $reading->reader_id,
                'ip_address' => $reading->reader ? $reading->reader->ip_address : null,
                'status' => $reading->status,
                'last_seen' => $reading->read_at->diffForHumans(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'total_active_tags' => $locations->count(),
            'locations' => $locations,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}