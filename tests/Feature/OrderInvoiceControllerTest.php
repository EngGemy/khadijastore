<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderInvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Brand $brand;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'brand_admin', 'brand_staff'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->brand = Brand::create([
            'name' => 'عطارة الأصالة',
            'slug' => 'attar',
            'is_active' => true,
        ]);

        $this->order = Order::create([
            'brand_id' => $this->brand->id,
            'customer_name' => 'أحمد محمد',
            'customer_phone' => '01012345678',
            'governorate' => 'القاهرة',
            'address' => 'مدينة نصر، شارع 9',
            'payment_method' => 'cod',
            'status' => 'pending',
            'subtotal' => 350,
            'shipping' => 50,
            'total' => 400,
        ]);
    }

    #[Test]
    public function guest_cannot_view_invoice(): void
    {
        $this->get(route('orders.invoice', $this->order))
            ->assertRedirect();
    }

    #[Test]
    public function super_admin_can_view_invoice(): void
    {
        $admin = User::create([
            'name' => 'سوبر أدمن',
            'email' => 'super@test.test',
            'password' => Hash::make('password'),
            'brand_id' => null,
            'is_active' => true,
        ]);
        $admin->assignRole('super_admin');

        $this->actingAs($admin)
            ->get(route('orders.invoice', $this->order))
            ->assertOk()
            ->assertSeeText($this->order->order_no, false)
            ->assertSeeText($this->order->customer_name, false);
    }

    #[Test]
    public function brand_staff_can_view_own_brand_invoice(): void
    {
        $staff = User::create([
            'name' => 'موظف',
            'email' => 'staff@test.test',
            'password' => Hash::make('password'),
            'brand_id' => $this->brand->id,
            'is_active' => true,
        ]);
        $staff->assignRole('brand_staff');

        $this->actingAs($staff)
            ->get(route('orders.invoice', $this->order))
            ->assertOk()
            ->assertSeeText('فاتورة طلب', false);
    }

    #[Test]
    public function brand_staff_cannot_view_other_brand_invoice(): void
    {
        $otherBrand = Brand::create([
            'name' => 'براند آخر',
            'slug' => 'other',
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'موظف آخر',
            'email' => 'other-staff@test.test',
            'password' => Hash::make('password'),
            'brand_id' => $otherBrand->id,
            'is_active' => true,
        ]);
        $staff->assignRole('brand_staff');

        $this->actingAs($staff)
            ->get(route('orders.invoice', $this->order))
            ->assertForbidden();
    }
}
