<?php

declare(strict_types=1);

namespace AbetaIO\Laravel\Models;

class Product
{
    public function __construct(
        public int|string $id,
        public string $sku,
        public string $name,
        public ?string $description,
        public float $price_ex_vat,
        public ?float $price_inc_vat,
        public ?float $vat_percentage,
        public int $quantity,
        public ?float $price_unit,
        public ?string $unit,
        public ?string $brand,
        public ?float $weight,
        public ?string $manufacturer_number,
        public ?array $category_codes = [],
        public ?string $image_url = null,
        public ?array $categories = []
    ) {
        $this->validate();
    }

    /**
     * Validate required fields.
     *
     * @throws \InvalidArgumentException
     */
    protected function validate(): void
    {
        $requiredFields = ['id', 'sku', 'name', 'price_ex_vat', 'quantity'];

        foreach ($requiredFields as $field) {
            if (!filled($this->{$field})) {
                throw new \InvalidArgumentException("The {$field} field is required.");
            }
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? 0,
            $data['sku'] ?? '',
            $data['name'] ?? '',
            $data['description'] ?? '',
            $data['price_ex_vat'] ?? 0.0,
            $data['price_inc_vat'] ?? 0.0,
            $data['vat_percentage'] ?? 0.0,
            $data['quantity'] ?? 1,
            $data['price_unit'] ?? null,
            $data['unit'] ?? null,
            $data['brand'] ?? null,
            $data['weight'] ?? null,
            $data['manufacturer_number'] ?? null,
            $data['category_codes'] ?? [],
            $data['image_url'] ?? null,
            $data['categories'] ?? []
        );
    }

    /**
     * Convert the product to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'price_ex_vat' => $this->price_ex_vat,
            'price_inc_vat' => $this->price_inc_vat,
            'vat_percentage' => $this->vat_percentage,
            'quantity' => $this->quantity,
            'price_unit' => $this->price_unit,
            'unit' => $this->unit,
            'brand' => $this->brand,
            'weight' => $this->weight,
            'manufacturer_number' => $this->manufacturer_number,
            'category_codes' => $this->category_codes,
            'image_url' => $this->image_url,
            'categories' => $this->categories,
        ];
    }
}
