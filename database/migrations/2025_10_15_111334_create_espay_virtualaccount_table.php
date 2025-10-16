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
        Schema::create('espay_virtualaccount', function (Blueprint $table) {
            // 🔹 UUID sebagai Primary Key
            $table->uuid('id')->primary();

            // ===== REQUEST PARAMETERS =====
            $table->string('rq_uuid')->unique()->comment('UUID unik request ke Espay');
            $table->dateTime('rq_datetime')->comment('Waktu request dikirim ke Espay');
            $table->string('order_id')->index()->comment('Order ID unik merchant');
            $table->string('ccy', 5)->default('IDR')->comment('Kode mata uang');
            $table->string('comm_code', 50)->comment('Kode perusahaan / merchant Espay');
            $table->string('bank_code', 10)->nullable()->comment('Kode bank penerbit VA');
            $table->integer('va_expired')->default(60)->comment('Durasi expired VA (menit)');
            $table->dateTime('expired_date')->comment('Tanggal kadaluarsa VA (rq_datetime + va_expired menit)');
            $table->string('signature', 255)->comment('Signature SHA256 dari request');
            $table->enum('update_flag', ['Y', 'N'])->default('N')->comment('Menandakan request create/update');

            // ===== RESPONSE PARAMETERS =====
            $table->dateTime('rs_datetime')->nullable()->comment('Waktu respon Espay');
            $table->string('error_code', 10)->nullable()->comment('Kode error Espay (00 = sukses)');
            $table->string('error_message', 255)->nullable()->comment('Pesan status dari Espay');
            $table->string('va_number', 50)->nullable()->comment('Nomor Virtual Account dari Espay');
            $table->string('description', 255)->nullable()->comment('Deskripsi tambahan dari Espay');

            // ===== CUSTOMER INFO (remark1–remark4) =====
            $table->string('remark1')->nullable()->comment('Nomor HP pelanggan');
            $table->string('remark2')->nullable()->comment('Nama pelanggan');
            $table->string('remark3')->nullable()->comment('Email pelanggan');
            $table->string('remark4')->nullable()->comment('Keterangan tambahan');

            // ===== STATUS & TIMESTAMP =====
            $table->enum('status', ['ACTIVE', 'EXPIRED', 'FAILED'])
                ->default('ACTIVE')
                ->comment('Status VA, aktif atau sudah kadaluarsa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('espay_virtualaccount');
    }
};
