<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
      public function up(): void {
                Schema::create('attendances', function (Blueprint $table) {
                              $table->id();
                              $table->foreignId('user_id')->constrained()->onDelete('cascade');
                              $table->foreignId('device_id')->constrained()->onDelete('cascade');
                              $table->timestamp('check_in');
                              $table->timestamp('check_out')->nullable();
                              $table->text('signature_in');
                              $table->text('signature_out')->nullable();
                              $table->enum('status', ['a_tiempo', 'tardanza', 'incompleto'])->default('a_tiempo');
                              $table->timestamps();
                });
      }
      public function down(): void { Schema::dropIfExists('attendances'); }
};
