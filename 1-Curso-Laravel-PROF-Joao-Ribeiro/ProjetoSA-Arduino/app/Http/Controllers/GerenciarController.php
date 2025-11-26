<?php

namespace App\Http\Controllers;

use App\Models\RfidReader;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GerenciarController extends Controller
{
    // ============================================
    // READERS (LEITORES RFID)
    // ============================================
    
    /**
     * Lista todos os readers com filtros
     */
    public function indexReaders(Request $request)
    {
        $query = RfidReader::withCount('readings');
        
        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reader_id', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtro por localização
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }
        
        // Ordenação (padrão: alfabética por nome A-Z)
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['name', 'reader_id', 'location', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('name', 'asc'); // Padrão: ordem alfabética
        }
        
        $readers = $query->paginate(15)->withQueryString();
        
        return view('gerenciar.readers.index', compact('readers'));
    }
    
    /**
     * Formulário para criar novo reader
     */
    public function createReader()
    {
        return view('gerenciar.readers.create');
    }
    
    /**
     * Salvar novo reader
     */
    public function storeReader(Request $request)
    {
        $validated = $request->validate([
            'reader_id' => [
                'required',
                'string',
                'max:50',
                'unique:rfid_readers,reader_id',
                'regex:/^[A-Za-z0-9_-]+$/', // Apenas letras, números, _ e -
            ],
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'status' => 'required|in:online,offline,maintenance',
            'description' => 'nullable|string|max:1000',
        ], [
            'reader_id.required' => 'O ID do leitor é obrigatório',
            'reader_id.unique' => 'Este ID de leitor já está cadastrado',
            'reader_id.regex' => 'O ID do leitor pode conter apenas letras, números, _ e -',
            'name.required' => 'O nome do leitor é obrigatório',
            'location.required' => 'A localização é obrigatória',
            'ip_address.ip' => 'Endereço IP inválido',
            'status.required' => 'O status é obrigatório',
            'status.in' => 'Status inválido',
        ]);
        
        try {
            RfidReader::create($validated);
            
            return redirect()
                ->route('gerenciar.readers.index')
                ->with('success', '✓ Leitor RFID cadastrado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '✗ Erro ao cadastrar leitor: ' . $e->getMessage());
        }
    }
    
    /**
     * Formulário para editar reader
     */
    public function editReader($id)
    {
        $reader = RfidReader::findOrFail($id);
        return view('gerenciar.readers.edit', compact('reader'));
    }
    
    /**
     * Atualizar reader
     */
    public function updateReader(Request $request, $id)
    {
        $reader = RfidReader::findOrFail($id);
        
        $validated = $request->validate([
            'reader_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rfid_readers')->ignore($reader->id),
                'regex:/^[A-Za-z0-9_-]+$/',
            ],
            'name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'ip_address' => 'nullable|ip',
            'status' => 'required|in:online,offline,maintenance',
            'description' => 'nullable|string|max:1000',
        ], [
            'reader_id.required' => 'O ID do leitor é obrigatório',
            'reader_id.unique' => 'Este ID de leitor já está cadastrado',
            'reader_id.regex' => 'O ID do leitor pode conter apenas letras, números, _ e -',
            'name.required' => 'O nome do leitor é obrigatório',
            'location.required' => 'A localização é obrigatória',
            'ip_address.ip' => 'Endereço IP inválido',
            'status.required' => 'O status é obrigatório',
        ]);
        
        try {
            $reader->update($validated);
            
            return redirect()
                ->route('gerenciar.readers.index')
                ->with('success', '✓ Leitor RFID atualizado com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '✗ Erro ao atualizar leitor: ' . $e->getMessage());
        }
    }
    
    /**
     * Excluir reader
     */
    public function destroyReader($id)
    {
        try {
            $reader = RfidReader::findOrFail($id);
            $readerName = $reader->name;
            $reader->delete();
            
            return redirect()
                ->route('gerenciar.readers.index')
                ->with('success', "✓ Leitor '{$readerName}' excluído com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->route('gerenciar.readers.index')
                ->with('error', '✗ Erro ao excluir leitor: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // TAGS (ETIQUETAS RFID)
    // ============================================
    
    /**
     * Lista todas as tags com filtros e ordenação alfabética
     */
    public function indexTags(Request $request)
    {
        $query = RfidTag::withCount('readings')->with('lastReading');
        
        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('tag_id', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filtro por categoria
        if ($request->filled('category')) {
            $query->where('category', 'like', "%{$request->category}%");
        }
        
        // Ordenação (padrão: alfabética por nome do produto A-Z)
        $sortBy = $request->get('sort_by', 'product_name');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['product_name', 'tag_id', 'product_code', 'category', 'type', 'status', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('product_name', 'asc'); // Padrão: ordem alfabética A-Z
        }
        
        $tags = $query->paginate(15)->withQueryString();
        
        // Estatísticas para os cards
        $stats = [
            'total' => RfidTag::count(),
            'ativo' => RfidTag::where('status', 'ativo')->count(),
            'inativo' => RfidTag::where('status', 'inativo')->count(),
            'perdido' => RfidTag::where('status', 'perdido')->count(),
            'total_leituras' => \DB::table('rfid_readings')->count(),
        ];
        
        return view('gerenciar.tags.index', compact('tags', 'stats'));
    }
    
    /**
     * Formulário para criar nova tag
     */
    public function createTag()
    {
        // Buscar categorias existentes para o datalist
        $categories = RfidTag::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
        
        return view('gerenciar.tags.create', compact('categories'));
    }
    
    /**
     * Salvar nova tag
     */
    public function storeTag(Request $request)
    {
        $validated = $request->validate([
            'tag_id' => [
                'required',
                'string',
                'max:50',
                'unique:rfid_tags,tag_id',
                'regex:/^[A-Za-z0-9]+$/', // Apenas letras e números (hex)
            ],
            'product_name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:pallet,produto,ferramenta,outro',
            'status' => 'required|in:ativo,inativo,perdido',
            'description' => 'nullable|string|max:1000',
        ], [
            'tag_id.required' => 'O ID da tag é obrigatório',
            'tag_id.unique' => 'Esta tag já está cadastrada no sistema',
            'tag_id.regex' => 'O ID da tag deve conter apenas letras e números',
            'product_name.required' => 'O nome do produto é obrigatório',
            'type.required' => 'O tipo é obrigatório',
            'type.in' => 'Tipo inválido',
            'status.required' => 'O status é obrigatório',
            'status.in' => 'Status inválido',
        ]);
        
        try {
            // Converter tag_id para minúsculas (padrão RFID)
            $validated['tag_id'] = strtolower($validated['tag_id']);
            
            RfidTag::create($validated);
            
            return redirect()
                ->route('gerenciar.tags.index')
                ->with('success', '✓ Tag RFID cadastrada com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '✗ Erro ao cadastrar tag: ' . $e->getMessage());
        }
    }
    
    /**
     * Formulário para editar tag
     */
    public function editTag($id)
    {
        $tag = RfidTag::with(['readings' => function($query) {
            $query->latest('read_at')->limit(10);
        }])->findOrFail($id);
        
        // Buscar categorias existentes para o datalist
        $categories = RfidTag::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');
        
        return view('gerenciar.tags.edit', compact('tag', 'categories'));
    }
    
    /**
     * Atualizar tag
     */
    public function updateTag(Request $request, $id)
    {
        $tag = RfidTag::findOrFail($id);
        
        $validated = $request->validate([
            'tag_id' => [
                'required',
                'string',
                'max:50',
                Rule::unique('rfid_tags')->ignore($tag->id),
                'regex:/^[A-Za-z0-9]+$/',
            ],
            'product_name' => 'required|string|max:255',
            'product_code' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'type' => 'required|in:pallet,produto,ferramenta,outro',
            'status' => 'required|in:ativo,inativo,perdido',
            'description' => 'nullable|string|max:1000',
        ], [
            'tag_id.required' => 'O ID da tag é obrigatório',
            'tag_id.unique' => 'Esta tag já está cadastrada no sistema',
            'tag_id.regex' => 'O ID da tag deve conter apenas letras e números',
            'product_name.required' => 'O nome do produto é obrigatório',
            'type.required' => 'O tipo é obrigatório',
            'status.required' => 'O status é obrigatório',
        ]);
        
        try {
            // Converter tag_id para minúsculas (padrão RFID)
            $validated['tag_id'] = strtolower($validated['tag_id']);
            
            $tag->update($validated);
            
            return redirect()
                ->route('gerenciar.tags.index')
                ->with('success', '✓ Tag RFID atualizada com sucesso!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', '✗ Erro ao atualizar tag: ' . $e->getMessage());
        }
    }
    
    /**
     * Excluir tag
     */
    public function destroyTag($id)
    {
        try {
            $tag = RfidTag::findOrFail($id);
            $productName = $tag->product_name;
            $tag->delete();
            
            return redirect()
                ->route('gerenciar.tags.index')
                ->with('success', "✓ Tag '{$productName}' excluída com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->route('gerenciar.tags.index')
                ->with('error', '✗ Erro ao excluir tag: ' . $e->getMessage());
        }
    }
    
    // ============================================
    // MÉTODOS AUXILIARES
    // ============================================
    
    /**
     * Buscar estatísticas gerais
     */
    public function getStatistics()
    {
        return response()->json([
            'readers' => [
                'total' => RfidReader::count(),
                'online' => RfidReader::where('status', 'online')->count(),
                'offline' => RfidReader::where('status', 'offline')->count(),
                'maintenance' => RfidReader::where('status', 'maintenance')->count(),
            ],
            'tags' => [
                'total' => RfidTag::count(),
                'ativo' => RfidTag::where('status', 'ativo')->count(),
                'inativo' => RfidTag::where('status', 'inativo')->count(),
                'perdido' => RfidTag::where('status', 'perdido')->count(),
            ],
            'readings' => [
                'total' => \DB::table('rfid_readings')->count(),
                'today' => \DB::table('rfid_readings')->whereDate('read_at', today())->count(),
                'week' => \DB::table('rfid_readings')->whereBetween('read_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ]
        ]);
    }
    
    /**
     * Buscar tags por categoria (AJAX)
     */
    public function getTagsByCategory(Request $request)
    {
        $category = $request->get('category');
        
        $tags = RfidTag::where('category', $category)
            ->where('status', 'ativo')
            ->orderBy('product_name', 'asc')
            ->get(['id', 'tag_id', 'product_name', 'product_code']);
        
        return response()->json($tags);
    }
    
    /**
     * Alterar status em lote (múltiplas tags)
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:rfid_tags,id',
            'status' => 'required|in:ativo,inativo,perdido',
        ]);
        
        try {
            RfidTag::whereIn('id', $validated['tag_ids'])
                ->update(['status' => $validated['status']]);
            
            $count = count($validated['tag_ids']);
            
            return redirect()
                ->back()
                ->with('success', "✓ Status de {$count} tag(s) atualizado com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', '✗ Erro ao atualizar status: ' . $e->getMessage());
        }
    }
}