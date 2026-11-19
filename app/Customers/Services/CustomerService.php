<?php

namespace App\Customers\Services;

use App\Customers\Models\Customer;
use App\Orders\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CustomerService
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function save(Customer $customer, array $input): Customer
    {
        $data = Validator::make($input, [
            'name' => ['required', 'string', 'max:160'],
            'phones' => ['required', 'array'],
            'phones.*' => ['nullable', 'string', 'max:40'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['nullable', 'email', 'max:160'],
            'addresses' => ['nullable', 'array'],
            'addresses.*' => ['nullable', 'string', 'max:255'],
        ])->validate();

        $phones = $this->filled($data['phones'] ?? []);

        if ($phones === []) {
            throw ValidationException::withMessages([
                'phones' => __('admin.customer_phone_required'),
            ]);
        }

        return DB::transaction(function () use ($customer, $data, $phones): Customer {
            $customer->fill(['name' => trim($data['name'])])->save();
            $this->replace($customer, 'phones', 'phone', $phones);
            $this->replace($customer, 'emails', 'email', $this->filled($data['emails'] ?? []));
            $this->replace($customer, 'addresses', 'address', $this->filled($data['addresses'] ?? []));

            return $customer->load(['phones', 'emails', 'addresses']);
        });
    }

    public function createFromOrder(Order $order): Customer
    {
        return DB::transaction(function () use ($order): Customer {
            $customer = Customer::query()->create([
                'name' => $order->customer_name,
            ]);

            if (trim($order->phone) !== '') {
                $customer->phones()->create(['phone' => $order->phone]);
            }

            if (is_string($order->email) && trim($order->email) !== '') {
                $customer->emails()->create(['email' => $order->email]);
            }

            if (trim($order->address) !== '') {
                $customer->addresses()->create(['address' => $order->address]);
            }

            return $customer->load(['phones', 'emails', 'addresses']);
        });
    }

    /**
     * @param  array<int, mixed>  $values
     * @return list<string>
     */
    private function filled(array $values): array
    {
        $clean = [];

        foreach ($values as $value) {
            $value = trim((string) $value);

            if ($value !== '') {
                $clean[] = $value;
            }
        }

        return $clean;
    }

    /**
     * @param  list<string>  $values
     */
    private function replace(Customer $customer, string $relation, string $column, array $values): void
    {
        $customer->{$relation}()->delete();

        foreach ($values as $value) {
            $customer->{$relation}()->create([$column => $value]);
        }
    }
}
