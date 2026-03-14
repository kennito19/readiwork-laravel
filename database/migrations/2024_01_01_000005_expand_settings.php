<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add type and group columns
        Schema::table('settings', function (Blueprint $table) {
            $table->string('type', 50)->default('text')->after('label');
            $table->string('group', 50)->default('general')->after('type');
        });

        // Update existing settings with type and group
        DB::table('settings')->where('key', 'like', 'price_%')->update(['type' => 'number', 'group' => 'pricing']);
        DB::table('settings')->where('key', 'site_name')->update(['type' => 'text', 'group' => 'branding']);
        DB::table('settings')->where('key', 'support_email')->update(['type' => 'email', 'group' => 'contact']);

        // Insert new settings
        DB::table('settings')->insertOrIgnore([
            // Branding
            ['key' => 'site_tagline',    'value' => 'Instant credit checks for Kenyan individuals',  'label' => 'Site Tagline',         'type' => 'text',     'group' => 'branding'],
            ['key' => 'brand_color',     'value' => '#0FA958',                                        'label' => 'Brand Colour',         'type' => 'color',    'group' => 'branding'],
            ['key' => 'secondary_color', 'value' => '#0B1F3B',                                        'label' => 'Secondary Colour',     'type' => 'color',    'group' => 'branding'],
            ['key' => 'logo_path',       'value' => '',                                               'label' => 'Site Logo',            'type' => 'image',    'group' => 'branding'],
            // Contact
            ['key' => 'contact_email',   'value' => 'hello@readiwork.co.ke',  'label' => 'Contact Email',    'type' => 'email', 'group' => 'contact'],
            ['key' => 'support_phone',   'value' => '+254722175570',          'label' => 'Support Phone',    'type' => 'tel',   'group' => 'contact'],
            ['key' => 'support_whatsapp','value' => '254722175570',           'label' => 'WhatsApp Number',  'type' => 'tel',   'group' => 'contact'],
            ['key' => 'office_address',  'value' => 'Nairobi, Kenya',         'label' => 'Office Address',   'type' => 'text',  'group' => 'contact'],
            // Social
            ['key' => 'social_facebook', 'value' => '#', 'label' => 'Facebook URL',    'type' => 'url', 'group' => 'social'],
            ['key' => 'social_twitter',  'value' => '#', 'label' => 'Twitter / X URL', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social_instagram','value' => '#', 'label' => 'Instagram URL',   'type' => 'url', 'group' => 'social'],
            ['key' => 'social_linkedin', 'value' => '#', 'label' => 'LinkedIn URL',    'type' => 'url', 'group' => 'social'],
            ['key' => 'social_tiktok',   'value' => '#', 'label' => 'TikTok URL',      'type' => 'url', 'group' => 'social'],
            // Content
            ['key' => 'hero_title',       'value' => 'Run Instant Credit Checks on Any Kenyan',                                                                       'label' => 'Hero Title',          'type' => 'text',     'group' => 'content'],
            ['key' => 'hero_subtitle',    'value' => 'Identity verification, CRB status, credit score and full credit reports in seconds.',                           'label' => 'Hero Subtitle',       'type' => 'textarea', 'group' => 'content'],
            ['key' => 'footer_tagline',   'value' => 'Instant credit checks for Kenyan individuals — identity, defaults, score, and full reports in seconds.',        'label' => 'Footer Tagline',      'type' => 'textarea', 'group' => 'content'],
            ['key' => 'meta_description', 'value' => 'Readiwork — run instant identity verification, CRB blacklist checks, credit scores and full reports on any Kenyan in seconds.', 'label' => 'Meta Description (SEO)', 'type' => 'textarea', 'group' => 'content'],
        ]);
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['type', 'group']);
        });

        DB::table('settings')->whereIn('key', [
            'site_tagline','brand_color','secondary_color','logo_path',
            'contact_email','support_phone','support_whatsapp','office_address',
            'social_facebook','social_twitter','social_instagram','social_linkedin','social_tiktok',
            'hero_title','hero_subtitle','footer_tagline','meta_description',
        ])->delete();
    }
};
