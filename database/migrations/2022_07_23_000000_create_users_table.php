<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * {@inheritdoc}
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('sex');
            $table->tinyInteger('age');
            $table->timestamps();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
