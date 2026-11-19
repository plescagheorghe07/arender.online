<?php

namespace Tests\Unit;

use App\Customers\Models\Customer;
use App\Customers\Models\CustomerAddress;
use App\Customers\Models\CustomerEmail;
use App\Customers\Models\CustomerPhone;
use App\Customers\Services\CustomerMatcher;
use App\Orders\Models\Order;
use Tests\TestCase;

class CustomerMatcherTest extends TestCase
{
    public function test_names_match_after_folding_diacritics_and_splitting_words(): void
    {
        $matcher = new CustomerMatcher;
        $customer = $this->customer('Ștefan Țurcanu', '079111222', 'stefan@example.com', 'Chișinău');

        $suggested = $matcher->suggest(collect([$customer]), new Order([
            'customer_name' => 'Stefan Turcanu',
            'phone' => '+373 69 000 000',
        ]));

        $this->assertCount(1, $suggested);
        $this->assertTrue($matcher->nameMatches('ion pop', 'Ion Popescu'));
        $this->assertFalse($matcher->nameMatches('Maria Ionescu', 'Andrei Rusu'));
    }

    public function test_phones_match_with_country_code_and_search_covers_email(): void
    {
        $matcher = new CustomerMatcher;
        $customer = $this->customer('Ana Rusu', '069123456', 'ana.rusu@example.com', 'bd. Dacia');

        $byPhone = $matcher->suggest(collect([$customer]), new Order([
            'customer_name' => 'Alt Nume',
            'phone' => '+37369123456',
        ]));

        $byEmail = $matcher->search(collect([$customer]), 'ana.rusu@example.com');
        $byAddress = $matcher->search(collect([$customer]), 'Dacia');
        $miss = $matcher->search(collect([$customer]), 'nimeni-de-negasit');

        $this->assertCount(1, $byPhone);
        $this->assertCount(1, $byEmail);
        $this->assertCount(1, $byAddress);
        $this->assertCount(0, $miss);
    }

    private function customer(string $name, string $phone, string $email, string $address): Customer
    {
        $customer = new Customer(['name' => $name]);
        $customer->id = 1;
        $customer->setRelation('phones', collect([new CustomerPhone(['phone' => $phone])]));
        $customer->setRelation('emails', collect([new CustomerEmail(['email' => $email])]));
        $customer->setRelation('addresses', collect([new CustomerAddress(['address' => $address])]));

        return $customer;
    }
}
