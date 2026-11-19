<?php

namespace App\Customers\Services;

use App\Customers\Models\Customer;
use App\Orders\Models\Order;
use Illuminate\Support\Collection;

class CustomerMatcher
{
    /**
     * @param  Collection<int, Customer>  $customers
     * @return Collection<int, Customer>
     */
    public function suggest(Collection $customers, Order $order): Collection
    {
        return $customers
            ->filter(fn (Customer $customer): bool => $this->nameMatches($order->customer_name, $customer->name)
                || $customer->phones->contains(fn ($phone): bool => $this->phonesMatch($order->phone, $phone->phone)))
            ->values();
    }

    /**
     * @param  Collection<int, Customer>  $customers
     * @return Collection<int, Customer>
     */
    public function search(Collection $customers, string $query): Collection
    {
        $query = trim($query);

        if ($query === '') {
            return $customers->values();
        }

        return $customers
            ->filter(function (Customer $customer) use ($query): bool {
                if ($this->nameMatches($query, $customer->name)) {
                    return true;
                }

                foreach ($customer->phones as $phone) {
                    if ($this->phonesMatch($query, $phone->phone)) {
                        return true;
                    }
                }

                foreach ($customer->emails as $email) {
                    if ($this->emailsMatch($query, $email->email)) {
                        return true;
                    }
                }

                foreach ($customer->addresses as $address) {
                    if ($this->wordsOverlap($query, $address->address)) {
                        return true;
                    }
                }

                return false;
            })
            ->values();
    }

    public function nameMatches(string $left, string $right): bool
    {
        return $this->wordsOverlap($left, $right);
    }

    public function phonesMatch(string $left, string $right): bool
    {
        $a = $this->phoneKey($left);
        $b = $this->phoneKey($right);

        if (strlen($a) < 6 || strlen($b) < 6) {
            return false;
        }

        return $a === $b || str_contains($a, $b) || str_contains($b, $a);
    }

    public function emailsMatch(string $query, string $email): bool
    {
        $query = $this->fold($query);
        $email = $this->fold($email);

        return $query !== ''
            && mb_strlen($query) >= 3
            && ($email === $query || str_contains($email, $query));
    }

    public function fold(string $value): string
    {
        $value = mb_strtolower(trim($value));

        return strtr($value, [
            'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ş' => 's', 'ț' => 't', 'ţ' => 't',
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ö' => 'o', 'ü' => 'u',
            'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        ]);
    }

    /**
     * @return list<string>
     */
    public function words(string $value): array
    {
        $parts = preg_split('/[^\p{L}\p{N}]+/u', $this->fold($value), -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === false) {
            return [];
        }

        return array_values(array_filter(
            $parts,
            fn (string $word): bool => mb_strlen($word) >= 2,
        ));
    }

    private function wordsOverlap(string $left, string $right): bool
    {
        foreach ($this->words($left) as $word) {
            foreach ($this->words($right) as $other) {
                if ($this->wordsClose($word, $other)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function wordsClose(string $left, string $right): bool
    {
        if ($left === $right) {
            return true;
        }

        $short = mb_strlen($left) <= mb_strlen($right) ? $left : $right;
        $long = $short === $left ? $right : $left;

        return mb_strlen($short) >= 3 && str_starts_with($long, $short);
    }

    private function phoneKey(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '373') && strlen($digits) > 8) {
            $digits = substr($digits, 3);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }
}
