<?php

namespace Tests\Feature;

use Tests\TestCase;

class BusinessPanelRouteTest extends TestCase
{
    public function test_business_login_page_is_available(): void
    {
        $this->get('/doanh-nghiep/login')->assertOk()->assertSee('Đăng nhập');
    }

    public function test_business_management_pages_require_authentication(): void
    {
        $this->get('/doanh-nghiep/managed-workforce-demands')->assertRedirect('/doanh-nghiep/login');
        $this->get('/doanh-nghiep/managed-employers')->assertRedirect('/doanh-nghiep/login');
        $this->get('/doanh-nghiep/received-proposals')->assertRedirect('/doanh-nghiep/login');
    }
}
