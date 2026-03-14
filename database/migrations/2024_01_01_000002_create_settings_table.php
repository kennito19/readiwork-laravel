<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->string('value', 500)->default('');
            $table->string('label', 150)->default('');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        DB::table('settings')->insertOrIgnore([
            ['key' => 'price_identity_verification', 'value' => '99',  'label' => 'Identity Verification Price (KES)'],
            ['key' => 'price_credit_score_check',    'value' => '199', 'label' => 'Credit Score Check Price (KES)'],
            ['key' => 'price_crb_blacklist_check',   'value' => '149', 'label' => 'CRB Blacklist Check Price (KES)'],
            ['key' => 'price_loan_eligibility',      'value' => '349', 'label' => 'Loan Eligibility Check Price (KES)'],
            ['key' => 'price_full_credit_report',    'value' => '499', 'label' => 'Full Credit Report Price (KES)'],
            ['key' => 'site_name',                   'value' => 'Readiwork', 'label' => 'Site Name'],
            ['key' => 'support_email',               'value' => 'support@readi.work', 'label' => 'Support Email'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
