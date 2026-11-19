<?php

namespace App\Admin\Controllers;

use App\Customers\Models\Customer;
use App\Customers\Services\CustomerMatcher;
use App\Customers\Services\CustomerService;
use App\Http\Controllers\Controller;
use App\Orders\Models\Order;
use App\Orders\Models\OrderNote;
use App\Orders\Services\OrderNotifier;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $allowed = ['new', 'confirmed', 'done', 'cancelled'];

        $orders = Order::query()
            ->when(in_array($status, $allowed, true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('admin.orders.index', [
            'orders' => $orders,
            'status' => in_array($status, $allowed, true) ? $status : '',
            'counts' => Order::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Request $request, Order $order, CustomerMatcher $matcher): View
    {
        $order->load([
            'notes.user',
            'product',
            'customer.phones',
            'customer.emails',
            'customer.addresses',
        ]);

        $query = trim($request->string('q')->toString());
        $customers = Customer::query()
            ->with(['phones', 'emails', 'addresses'])
            ->orderBy('name')
            ->get();

        $matches = $query === ''
            ? ($order->customer_id === null ? $matcher->suggest($customers, $order) : collect())
            : $matcher->search($customers, $query);

        return view('admin.orders.show', [
            'order' => $order,
            'query' => $query,
            'matches' => $matches->take(8),
            'searched' => $query !== '',
        ]);
    }

    public function update(Request $request, Order $order, OrderNotifier $mail): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'confirmed', 'done', 'cancelled'])],
        ]);

        $previous = $order->status;
        $order->update(['status' => $data['status']]);

        if ($previous !== $order->status) {
            $mail->statusChanged($order);
        }

        return back()->with('status', __('admin.saved'));
    }

    public function updateDetails(Request $request, Order $order, OrderNotifier $mail): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email', 'max:160'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $note = isset($data['note']) && trim($data['note']) !== '' ? $data['note'] : null;
        $next = [
            'customer_name' => $data['customer_name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'note' => $note,
        ];

        $locale = $order->locale ?: (string) config('app.locale');
        $labels = [
            'customer_name' => 'mail.name',
            'phone' => 'mail.phone',
            'email' => 'mail.email',
            'address' => 'mail.address',
            'note' => 'mail.note',
        ];
        $changes = [];

        foreach ($labels as $field => $label) {
            if ((string) $order->{$field} !== (string) ($next[$field] ?? '')) {
                $changes[] = Lang::get($label, [], $locale).': '.($next[$field] ?? '');
            }
        }

        $order->update($next);
        $mail->updated($order, $changes);

        return back()->with('status', __('admin.saved'));
    }

    public function storeNote(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        OrderNote::query()->create([
            'order_id' => $order->id,
            'user_id' => $request->user()?->id,
            'body' => $data['body'],
        ]);

        return back()->with('status', __('admin.note_saved'));
    }

    public function attachCustomer(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
        ]);

        $order->update(['customer_id' => $data['customer_id']]);

        return back()->with('status', __('admin.customer_attached'));
    }

    public function detachCustomer(Order $order): RedirectResponse
    {
        $order->update(['customer_id' => null]);

        return back()->with('status', __('admin.customer_detached'));
    }

    public function createCustomer(Request $request, Order $order, CustomerService $customers): RedirectResponse
    {
        $customer = $request->filled('name')
            ? $customers->save(new Customer, $request->all())
            : $customers->createFromOrder($order);

        $order->update(['customer_id' => $customer->id]);

        return back()->with('status', __('admin.customer_created'));
    }
}
