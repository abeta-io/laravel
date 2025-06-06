<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\DataObjects;

class Address
{
    public ?string $first_name;

    public ?string $last_name;

    public ?string $company;

    public ?string $address_1;

    public ?string $address_2;

    public ?string $street;

    public ?string $city;

    public ?string $state;

    public ?string $postcode;

    public ?string $country;

    public ?string $email;

    public ?string $phone;

    public function __construct(
        $first_name,
        $last_name,
        $company,
        $address_1,
        $address_2,
        $street,
        $city,
        $state,
        $postcode,
        $country,
        $email,
        $phone
    ) {
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->company = $company;
        $this->address_1 = $address_1;
        $this->address_2 = $address_2;
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->postcode = $postcode;
        $this->country = $country;
        $this->email = $email;
        $this->phone = $phone;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['company'] ?? null,
            $data['address_1'] ?? null,
            $data['address_2'] ?? null,
            $data['street'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['postcode'] ?? null,
            $data['country'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'company' => $this->company,
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'street' => $this->street,
            'city' => $this->city,
            'state' => $this->state,
            'postcode' => $this->postcode,
            'country' => $this->country,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
