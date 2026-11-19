<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('departments');
    }

    public function down(): void
    {
        // The sample departments table is not recreated.
    }
};
