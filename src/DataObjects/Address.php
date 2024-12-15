<?php

namespace AbetaIO\Laravel\DataObjects;


class Address
{
    public $first_name;
    public $last_name;
    public $company;
    public $address_1;
    public $address_2;
    public $city;
    public $state;
    public $postcode;
    public $country;
    public $email;
    public $phone;

    public function __construct(
        $first_name,
        $last_name,
        $company,
        $address_1,
        $address_2,
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
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "company" => $this->company,
            "address_1" => $this->address_1,
            "address_2" => $this->address_2,
            "city" => $this->city,
            "state" => $this->state,
            "postcode" => $this->postcode,
            "country" => $this->country,
            "email" => $this->email,
            "phone" => $this->phone,
        ];
    }
}