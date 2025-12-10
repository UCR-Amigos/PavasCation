<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('model_type')->nullable()->after('payload');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->string('event')->nullable()->after('model_id');
            $table->json('changes_before')->nullable()->after('event');
            $table->json('changes_after')->nullable()->after('changes_before');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['model_type','model_id','event','changes_before','changes_after']);
        });
    }
};
