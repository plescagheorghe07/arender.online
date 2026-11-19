<?php

namespace App\Admin\Controllers;

use App\Customers\Models\Customer;
use App\Customers\Services\CustomerMatcher;
use App\Customers\Services\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request, CustomerMatcher $matcher): View
    {
        $query = trim($request->string('q')->toString());
        $customers = Customer::query()
            ->with(['phones', 'emails', 'addresses'])
            ->orderBy('name')
            ->get();

        if ($query !== '') {
            $customers = $matcher->search($customers, $query);
        }

        return view('admin.customers.index', [
            'customers' => $customers,
            'query' => $query,
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.form', [
            'customer' => new Customer,
            'phones' => $this->values('phones', []),
            'emails' => $this->values('emails', []),
            'addresses' => $this->values('addresses', []),
        ]);
    }

    public function store(Request $request, CustomerService $customers): RedirectResponse
    {
        $customer = $customers->save(new Customer, $request->all());

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('status', __('admin.saved'));
    }

    public function edit(Customer $customer): View
    {
        $customer->load(['phones', 'emails', 'addresses']);

        return view('admin.customers.form', [
            'customer' => $customer,
            'phones' => $this->values('phones', $customer->phones->pluck('phone')->all()),
            'emails' => $this->values('emails', $customer->emails->pluck('email')->all()),
            'addresses' => $this->values('addresses', $customer->addresses->pluck('address')->all()),
            'recentOrders' => $customer->orders()->limit(5)->get(),
        ]);
    }

    public function update(Request $request, Customer $customer, CustomerService $customers): RedirectResponse
    {
        $customers->save($customer, $request->all());

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('status', __('admin.saved'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('admin.customers.index')
            ->with('status', __('admin.deleted'));
    }

    /**
     * @param  list<string>  $current
     * @return list<string>
     */
    private function values(string $key, array $current): array
    {
        $old = old($key);
        $values = array_values(is_array($old) ? $old : $current);

        return $values === [] ? [''] : $values;
    }
}
