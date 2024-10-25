<?php

namespace RedSnapper\OneKey;

use Illuminate\Support\Arr;

class OneKeyUser
{

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getId(): string
    {
        return Arr::get($this->data, 'UID');
    }

    public function getFirstName(): ?string
    {
        return Arr::get($this->data, 'firstname');
    }

    public function getLastName(): ?string
    {
        return Arr::get($this->data, 'name');
    }

    public function getFullName(): ?string
    {
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getEmail(): ?string
    {
        return Arr::get($this->data, 'email');
    }

    public function getPhone(): ?string
    {
        return Arr::get($this->data, 'professionalPhone');
    }

    public function getCity(): ?string
    {
        return Arr::get($this->data, 'city');
    }

    public function getProfession(): ?string
    {
        return Arr::get($this->data, 'profession');
    }

    public function getSpecialties(): array
    {
        return collect([
            Arr::get($this->data, 'specialite1'),
            Arr::get($this->data, 'specialite2'),
            Arr::get($this->data, 'specialite3'),
        ])->filter()->values()->toArray();
    }

    public function isHCP(): bool
    {
        return Arr::get($this->data, 'usertype') === "PS";
    }

    public function trustLevel(): int
    {
        return Arr::get($this->data, 'Cegedim_security_level');
    }

    /**
     * Get the raw user array.
     *
     * @return array
     */
    public function getRaw(): array
    {
        return $this->data;
    }
}