<?php

namespace Tests\Feature;

use Tests\TestCase;

class SchoolPanelRouteTest extends TestCase
{
    public function test_school_login_page_is_available_without_post_login_confirmation(): void
    {
        $response = $this->get('/truong/login');

        $response
            ->assertOk()
            ->assertSee('Đăng nhập');

        $this->assertStringNotContainsString('xác nhận thông tin', $response->getContent());
        $this->assertStringNotContainsString('xác nhận email', $response->getContent());
    }

    public function test_school_management_pages_require_authentication(): void
    {
        $this->get('/truong/managed-schools')->assertRedirect('/truong/login');
        $this->get('/admin/school-accounts')->assertRedirect('/admin/login');
    }
}
