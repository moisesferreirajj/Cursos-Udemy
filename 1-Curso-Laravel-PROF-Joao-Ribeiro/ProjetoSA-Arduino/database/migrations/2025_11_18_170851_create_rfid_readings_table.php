<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rfid_readings', function (Blueprint $table) {
            $table->id();
            $table->string('tag_id', 50)->index(); // ID da tag RFID
            $table->string('reader_id', 50)->index(); // ID do leitor RFID
            $table->string('location', 100)->nullable(); // Localização do leitor
            $table->string('product_name', 255)->nullable(); // Nome do produto
            $table->string('product_code', 100)->nullable(); // Código do produto
            $table->enum('status', ['entrada', 'saida', 'movimentacao'])->default('movimentacao');
            $table->decimal('temperature', 5, 2)->nullable(); // Temperatura (se disponível)
            $table->integer('signal_strength')->nullable(); // Força do sinal
            $table->text('notes')->nullable(); // Observações
            $table->timestamp('read_at')->useCurrent(); // Data/hora da leitura
            $table->timestamps();
            $table->softDeletes(); // Para exclusão lógica

            // Índices compostos para melhor performance
            $table->index(['tag_id', 'read_at']);
            $table->index(['reader_id', 'read_at']);
            $table->index(['status', 'read_at']);
        });

        Schema::create('rfid_readers', function (Blueprint $table) {
            $table->id();
            $table->string('reader_id', 50)->unique(); // ID único do leitor
            $table->string('name', 100); // Nome do leitor
            $table->string('location', 255); // Localização física
            $table->string('ip_address', 45)->nullable(); // IP do ESP32
            $table->enum('status', ['online', 'offline', 'maintenance'])->default('offline');
            $table->timestamp('last_ping')->nullable(); // Último ping recebido
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('rfid_tags', function (Blueprint $table) {
            $table->id();
            $table->string('tag_id', 50)->unique(); // ID único da tag
            $table->string('product_name', 255); // Nome do produto
            $table->string('product_code', 100)->nullable(); // Código do produto
            $table->string('category', 100)->nullable(); // Categoria
            $table->enum('type', ['pallet', 'produto', 'ferramenta', 'outro'])->default('produto');
            $table->enum('status', ['ativo', 'inativo', 'perdido'])->default('ativo');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfid_readings');
        Schema::dropIfExists('rfid_readers');
        Schema::dropIfExists('rfid_tags');
    }
};