<?php

declare(strict_types=1);

namespace Src\Company\Domain\ValueObjects;

use Src\Shared\Framework\Domain\ValueObjects\CountryCode;

/**
 * Class CompanyAddress
 *
 * Composite Value Object representing complete physical/domicile address of a Company.
 */
readonly class CompanyAddress
{
    public function __construct(
        public ?string $address1 = null,
        public ?string $address2 = null,
        public ?string $address3 = null,
        public ?string $subDistrict = null,
        public ?string $district = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?CountryCode $country = null,
        public ?string $postalCode = null,
    ) {}

    public static function create(
        ?string $address1 = null,
        ?string $address2 = null,
        ?string $address3 = null,
        ?string $subDistrict = null,
        ?string $district = null,
        ?string $city = null,
        ?string $province = null,
        ?CountryCode $country = null,
        ?string $postalCode = null,
    ): self {
        return new self(
            $address1 !== null ? trim($address1) : null,
            $address2 !== null ? trim($address2) : null,
            $address3 !== null ? trim($address3) : null,
            $subDistrict !== null ? trim($subDistrict) : null,
            $district !== null ? trim($district) : null,
            $city !== null ? trim($city) : null,
            $province !== null ? trim($province) : null,
            $country,
            $postalCode !== null ? trim($postalCode) : null,
        );
    }

    public static function fromPrimitives(
        ?string $address1 = null,
        ?string $address2 = null,
        ?string $address3 = null,
        ?string $subDistrict = null,
        ?string $district = null,
        ?string $city = null,
        ?string $province = null,
        ?string $country = null,
        ?string $postalCode = null,
    ): self {
        $countryCode = null;
        if ($country !== null && trim($country) !== '') {
            $trimmedCountry = trim($country);
            if (strlen($trimmedCountry) === 2) {
                $countryCode = CountryCode::create(strtoupper($trimmedCountry));
            }
        }

        return self::create(
            address1: $address1,
            address2: $address2,
            address3: $address3,
            subDistrict: $subDistrict,
            district: $district,
            city: $city,
            province: $province,
            country: $countryCode,
            postalCode: $postalCode,
        );
    }

    public static function empty(): self
    {
        return new self;
    }

    public function streetAddress(): string
    {
        return implode(', ', array_filter([
            $this->address1,
            $this->address2,
            $this->address3,
        ]));
    }

    public function fullAddress(): string
    {
        return implode(', ', array_filter([
            $this->address1,
            $this->address2,
            $this->address3,
            $this->subDistrict,
            $this->district,
            $this->city,
            $this->province,
            $this->postalCode,
            $this->country?->value(),
        ]));
    }

    public function equals(self $other): bool
    {
        return $this->address1 === $other->address1
            && $this->address2 === $other->address2
            && $this->address3 === $other->address3
            && $this->subDistrict === $other->subDistrict
            && $this->district === $other->district
            && $this->city === $other->city
            && $this->province === $other->province
            && (($this->country === null && $other->country === null) || ($this->country !== null && $other->country !== null && $this->country->equals($other->country)))
            && $this->postalCode === $other->postalCode;
    }
}
