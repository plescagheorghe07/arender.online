<?php

namespace Tests\Feature;

use App\Models\StaffRole;
use App\Models\User;
use App\Orders\Models\Order;
use Database\Seeders\CatalogSeeder;
use Database\Seeders\NewsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            CatalogSeeder::class,
            NewsSeeder::class,
        ]);
    }

    public function test_home_shows_the_rental_offer_and_only_the_first_products(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Chirie tehnică în Chișinău.')
            ->assertSee('Livrare în decurs de o oră')
            ->assertSee('Fără gaj')
            ->assertSee('Sony PlayStation 5')
            ->assertSee('Română')
            ->assertSee('Русский')
            ->assertDontSee('Nintendo Switch 2');
    }

    public function test_the_catalog_lists_every_product(): void
    {
        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('Sony PlayStation 5')
            ->assertSee('Nintendo Switch 2')
            ->assertSee('Dezumidificator');
    }

    public function test_a_product_page_shows_prices_and_accepts_an_order(): void
    {
        $this->get(route('catalog.show', 'sony-playstation-5'))
            ->assertOk()
            ->assertSee('400 MDL')
            ->assertSee('2 gamepad supl.');

        $this->post(route('catalog.orders.store', 'sony-playstation-5'), [
            'name' => 'Ion Popescu',
            'phone' => '069123456',
            'email' => 'ion@example.com',
            'address' => 'Chișinău, bd. Dacia 1',
            'days' => '1',
            'note' => 'seara',
            'privacy' => '1',
        ])->assertRedirect(route('catalog.show', 'sony-playstation-5'));

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ion Popescu',
            'email' => 'ion@example.com',
            'locale' => 'ro',
            'product_name' => 'Sony PlayStation 5',
            'price' => 400,
            'status' => 'new',
        ]);

        $this->assertNotNull(Order::query()->where('email', 'ion@example.com')->firstOrFail()->privacy_accepted_at);
    }

    public function test_an_order_needs_personal_data_consent_and_the_notice_is_public(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Î.I. «PASTUHOV OLEG»', false)
            ->assertSee('1026600005428', false)
            ->assertSee('133/2011', false)
            ->assertSee('arender_consent', false);

        $this->get(route('privacy', ['locale' => 'ru']))
            ->assertOk()
            ->assertSee('Закону № 133/2011', false);

        $this->get(route('catalog.show', 'sony-playstation-5'))
            ->assertOk()
            ->assertSee('name="privacy"', false)
            ->assertSee(__('ui.cookie_title'), false);

        $this->from(route('catalog.show', 'sony-playstation-5'))
            ->post(route('catalog.orders.store', 'sony-playstation-5'), [
                'name' => 'Ion Popescu',
                'phone' => '069123456',
                'email' => 'ion@example.com',
                'address' => 'Chișinău, bd. Dacia 1',
                'days' => '1',
            ])
            ->assertRedirect(route('catalog.show', 'sony-playstation-5'))
            ->assertSessionHasErrors('privacy');

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_russian_uses_the_saved_translation(): void
    {
        $this->get(route('catalog.show', ['locale' => 'ru', 'product' => 'jbl-partybox-120']))
            ->assertOk()
            ->assertSee('Мощный звук и глубокий бас для любой вечеринки')
            ->assertSee('Каталог');
    }

    public function test_the_sitemap_lists_both_languages(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee('/ro/catalog/sony-playstation-5', false)
            ->assertSee('/ru/news', false);
    }

    public function test_a_finished_game_stores_the_promo_code(): void
    {
        $this->postJson(route('minigame.reward'), ['code' => 'ABCD'])
            ->assertOk()
            ->assertJsonPath('code', 'ABCD');

        $this->assertDatabaseHas('promo_codes', [
            'code' => 'ABCD',
            'for_game' => true,
        ]);
    }

    public function test_news_comes_from_the_saved_articles(): void
    {
        $this->get(route('news.index'))
            ->assertOk()
            ->assertSee('The Witcher 3');
    }

    public function test_guests_cannot_open_the_admin_panel(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_an_admin_can_sign_in_and_see_the_catalog(): void
    {
        User::factory()->admin()->create([
            'email' => 'admin@arender.online',
            'password' => 'secret-pass',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'admin@arender.online',
            'password' => 'secret-pass',
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Sony PlayStation 5');
    }

    public function test_support_sees_orders_and_cannot_open_the_catalog_editor(): void
    {
        $support = User::factory()->create();

        $this->actingAs($support)
            ->get(route('admin.orders.index'))
            ->assertOk();

        $this->actingAs($support)
            ->get(route('admin.products.index'))
            ->assertForbidden();

        $this->actingAs($support)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_a_manager_cannot_administer_accounts(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)
            ->get(route('admin.products.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_an_admin_can_add_an_account_and_a_note_on_an_order(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Mara',
            'email' => 'mara@arender.online',
            'password' => 'secret-pass',
            'role' => StaffRole::Manager->value,
        ])->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'mara@arender.online',
            'role' => StaffRole::Manager->value,
        ]);

        $order = Order::query()->create([
            'product_name' => 'Sony PlayStation 5',
            'days_label' => '1',
            'price' => 400,
            'customer_name' => 'Ion Popescu',
            'phone' => '069123456',
            'address' => 'Chișinău',
            'addons' => [],
            'status' => 'new',
        ]);

        $this->actingAs($admin)->post(route('admin.orders.notes.store', $order), [
            'body' => 'Livrare la 18:00.',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('admin.orders.update', $order), [
            'status' => 'confirmed',
        ])->assertRedirect();

        $this->assertDatabaseHas('order_notes', [
            'order_id' => $order->id,
            'body' => 'Livrare la 18:00.',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
        ]);
    }
}
