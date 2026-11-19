<?php

namespace Tests\Feature;

use App\Customers\Models\Customer;
use App\Models\User;
use App\Orders\Models\Order;
use Database\Seeders\CatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OrderCustomerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CatalogSeeder::class);
        config([
            'services.visio.key' => 'vm_test_key',
            'services.visio.url' => 'https://mail.visio.md',
        ]);
        Http::fake();
    }

    public function test_an_order_emails_the_customer_on_create_status_and_details_but_not_for_private_notes(): void
    {
        $this->post(route('catalog.orders.store', 'sony-playstation-5'), [
            'name' => 'Ion Popescu',
            'phone' => '069123456',
            'email' => 'ion@example.com',
            'address' => 'Chișinău, bd. Dacia 1',
            'days' => '1',
            'note' => 'seara',
            'privacy' => '1',
        ])->assertRedirect();

        $order = Order::query()->firstOrFail();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.orders.notes.store', $order), [
            'body' => 'cod intern 77',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'confirmed',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('admin.orders.details', $order), [
            'customer_name' => 'Ion Popescu',
            'phone' => '069123456',
            'email' => 'ion@example.com',
            'address' => 'Chișinău, str. Alba 2',
            'note' => 'seara',
        ])->assertRedirect();

        $sent = Http::recorded();
        $this->assertCount(3, $sent);

        $events = [];
        $subjects = [];
        $bodies = '';

        foreach ($sent as [$request]) {
            $data = $request->data();
            $events[] = $data['metadata']['event'] ?? null;
            $subjects[] = $data['subject'] ?? '';
            $this->assertSame('ion@example.com', $data['to']['email'] ?? null);
            $this->assertTrue($request->hasHeader('X-API-Key', 'vm_test_key'));
            $bodies .= ($data['html'] ?? '').($data['text'] ?? '');
        }

        $this->assertSame(['created', 'confirmed', 'updated'], $events);
        $this->assertStringContainsString('Am primit comanda', $subjects[0]);
        $this->assertStringContainsString('confirmată', $subjects[1]);
        $this->assertStringContainsString('actualizat', $subjects[2]);
        $this->assertStringContainsString('400 MDL', $bodies);
        $this->assertStringContainsString('Total', $bodies);
        $this->assertStringNotContainsString('cod intern 77', $bodies);
        $this->assertStringContainsString('Chișinău, str. Alba 2', $bodies);
    }

    public function test_a_cancelled_order_gets_its_own_email_with_the_total(): void
    {
        $this->post(route('catalog.orders.store', 'sony-playstation-5'), [
            'name' => 'Ion Popescu',
            'phone' => '069123456',
            'email' => 'ion@example.com',
            'address' => 'Chișinău, bd. Dacia 1',
            'days' => '1',
            'privacy' => '1',
        ])->assertRedirect();

        $order = Order::query()->firstOrFail();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'cancelled',
        ])->assertRedirect();

        $sent = Http::recorded();
        $this->assertCount(2, $sent);

        $last = $sent[1][0]->data();

        $this->assertSame('cancelled', $last['metadata']['event'] ?? null);
        $this->assertStringContainsString('anulată', $last['subject'] ?? '');
        $this->assertStringContainsString('Comandă anulată', $last['html'] ?? '');
        $this->assertStringContainsString('400 MDL', ($last['html'] ?? '').($last['text'] ?? ''));
        $this->assertStringNotContainsString('Comandă confirmată', $last['html'] ?? '');
    }

    public function test_support_can_attach_a_suggested_customer_and_create_one_when_search_is_empty(): void
    {
        $support = User::factory()->create();

        $this->actingAs($support)->post(route('admin.customers.store'), [
            'name' => 'Ștefan Țurcanu',
            'phones' => ['079111222', '069000111'],
            'emails' => ['stefan@example.com', ''],
            'addresses' => ['or. Chișinău, str. Alba 4'],
        ])->assertRedirect();

        $order = Order::query()->create([
            'product_name' => 'Sony PlayStation 5',
            'days_label' => '1',
            'price' => 400,
            'customer_name' => 'Stefan Turcanu',
            'phone' => '+37379111222',
            'email' => 'alt@example.com',
            'address' => 'Chișinău',
            'addons' => [],
            'status' => 'new',
        ]);

        $this->actingAs($support)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Ștefan Țurcanu')
            ->assertSee(__('admin.suggestions'));

        $customerId = Customer::query()->value('id');

        $this->actingAs($support)->post(route('admin.orders.customer.attach', $order), [
            'customer_id' => $customerId,
        ])->assertRedirect();

        $this->assertSame($customerId, $order->fresh()->customer_id);

        $fresh = Order::query()->create([
            'product_name' => 'JBL',
            'days_label' => '1',
            'price' => 200,
            'customer_name' => 'Nimeni Nou',
            'phone' => '060000999',
            'email' => 'nou@example.com',
            'address' => 'Orhei',
            'addons' => [],
            'status' => 'new',
        ]);

        $this->actingAs($support)
            ->get(route('admin.orders.show', ['order' => $fresh, 'q' => 'nimeni-de-negasit']))
            ->assertOk()
            ->assertSee(__('admin.create_from_order'));

        $this->actingAs($support)
            ->post(route('admin.orders.customer.create', $fresh))
            ->assertRedirect();

        $fresh->refresh();
        $this->assertNotNull($fresh->customer_id);
        $this->assertDatabaseHas('customer_phones', [
            'customer_id' => $fresh->customer_id,
            'phone' => '060000999',
        ]);
        $this->assertDatabaseHas('customer_emails', [
            'customer_id' => $fresh->customer_id,
            'email' => 'nou@example.com',
        ]);

        $edited = Order::query()->create([
            'product_name' => 'Dyson',
            'days_label' => '1',
            'price' => 150,
            'customer_name' => 'Ana Veche',
            'phone' => '068111222',
            'email' => 'ana-veche@example.com',
            'address' => 'Bălți',
            'addons' => [],
            'status' => 'new',
        ]);

        $this->actingAs($support)->post(route('admin.orders.customer.create', $edited), [
            'from_order' => '1',
            'name' => 'Ana Nouă',
            'phones' => ['068111222'],
            'emails' => ['ana-noua@example.com'],
            'addresses' => ['Bălți, str. Independenței 1'],
        ])->assertRedirect();

        $edited->refresh();
        $this->assertDatabaseHas('customers', [
            'id' => $edited->customer_id,
            'name' => 'Ana Nouă',
        ]);

        $this->actingAs($support)
            ->get(route('admin.customers.index', ['q' => 'turcanu']))
            ->assertOk()
            ->assertSee('Ștefan Țurcanu');

        Http::assertNothingSent();
    }
}
