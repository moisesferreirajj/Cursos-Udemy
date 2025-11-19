<?php

namespace App\Http\Controllers;

use App\Models\RfidReading;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RelatoriosController extends Controller
{
    /**
     * Display the reports page
     */
    public function index(Request $request)
    {
        // Buscar todas as tags para o dropdown
        $allTags = RfidTag::orderBy('product_name')->get();
        
        // Inicializar variáveis
        $selectedTag = null;
        $readings = collect();
        $stats = null;
        $lastReading = null;

        // Se houver tag_id na requisição
        if ($request->has('tag_id')) {
            $selectedTag = $this->getSelectedTag($request->tag_id);
            
            if ($selectedTag) {
                $readings = $this->getReadings($request);
                $stats = $this->calculateStats($readings);
                $lastReading = $readings->first();
            }
        }

        return view('relatorios', compact(
            'allTags',
            'selectedTag',
            'readings',
            'stats',
            'lastReading'
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
     * Get readings filtered by request parameters
     */
    private function getReadings(Request $request)
    {
        $query = RfidReading::where('tag_id', $request->tag_id);
        
        // Filtrar por data de início
        if ($request->filled('start_date')) {
            $query->whereDate('read_at', '>=', $request->start_date);
        }
        
        // Filtrar por data final
        if ($request->filled('end_date')) {
            $query->whereDate('read_at', '<=', $request->end_date);
        }
        
        return $query->orderBy('read_at', 'desc')->get();
    }

    /**
     * Calculate statistics from readings
     */
    private function calculateStats($readings)
    {
        return [
            'total' => $readings->count(),
            'entradas' => $readings->where('status', 'entrada')->count(),
            'saidas' => $readings->where('status', 'saida')->count(),
            'movimentacoes' => $readings->where('status', 'movimentacao')->count(),
        ];
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:rfid_tags,tag_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $selectedTag = $this->getSelectedTag($request->tag_id);
        $readings = $this->getReadings($request);
        $stats = $this->calculateStats($readings);

        // TODO: Implementar geração de PDF
        // Usando DomPDF ou mPDF
        
        return response()->json([
            'message' => 'Exportação de PDF será implementada em breve',
            'tag' => $selectedTag,
            'stats' => $stats,
        ]);
    }

    /**
     * Export report to Excel
     */
    public function exportExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:rfid_tags,tag_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $selectedTag = $this->getSelectedTag($request->tag_id);
        $readings = $this->getReadings($request);

        // TODO: Implementar exportação Excel
        // Usando Laravel Excel (Maatwebsite)
        
        return response()->json([
            'message' => 'Exportação de Excel será implementada em breve',
            'tag' => $selectedTag,
            'total_readings' => $readings->count(),
        ]);
    }

    /**
     * Get report summary
     */
    public function summary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => 'required|exists:rfid_tags,tag_id',
            'period' => 'nullable|in:today,week,month,year',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $period = $request->input('period', 'today');
        $query = RfidReading::where('tag_id', $request->tag_id);

        // Aplicar filtro de período
        switch ($period) {
            case 'today':
                $query->whereDate('read_at', today());
                break;
            case 'week':
                $query->whereBetween('read_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('read_at', now()->month)
                      ->whereYear('read_at', now()->year);
                break;
            case 'year':
                $query->whereYear('read_at', now()->year);
                break;
        }

        $readings = $query->get();
        $stats = $this->calculateStats($readings);

        return response()->json([
            'success' => true,
            'period' => $period,
            'tag_id' => $request->tag_id,
            'stats' => $stats,
            'last_reading' => $readings->first(),
        ]);
    }

    /**
     * Compare multiple tags
     */
    public function compare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_ids' => 'required|array|min:2|max:5',
            'tag_ids.*' => 'exists:rfid_tags,tag_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $comparison = [];

        foreach ($request->tag_ids as $tagId) {
            $tag = RfidTag::where('tag_id', $tagId)->first();
            
            $query = RfidReading::where('tag_id', $tagId);
            
            if ($request->filled('start_date')) {
                $query->whereDate('read_at', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('read_at', '<=', $request->end_date);
            }
            
            $readings = $query->get();
            
            $comparison[] = [
                'tag_id' => $tagId,
                'product_name' => $tag->product_name,
                'stats' => $this->calculateStats($readings),
            ];
        }

        return response()->json([
            'success' => true,
            'comparison' => $comparison,
        ]);
    }
}