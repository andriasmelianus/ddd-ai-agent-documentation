<?php

declare(strict_types=1);

namespace Src\Company\Infrastructure\Mappers;

use App\Models\Company as CompanyModel;
use Src\Company\Domain\Entities\Company;
use Src\Company\Domain\ReadModels\CompanyRM;
use Src\Company\Domain\ValueObjects\ApiAccessConfiguration;
use Src\Company\Domain\ValueObjects\Ceisa40Credentials;
use Src\Company\Domain\ValueObjects\CompanyAddress;
use Src\Company\Domain\ValueObjects\CompanyLogos;
use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\CompanyPhoneNumbers;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TaxInformation;
use Src\Company\Domain\ValueObjects\TpsConfiguration;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;

final readonly class CompanyMapper
{
    /**
     * Reconstitute a Company Domain Aggregate Root from Eloquent Model or attribute array.
     *
     * @param  CompanyModel|array<string, mixed>  $data
     */
    public function toDomain(CompanyModel|array $data): Company
    {
        $attributes = $data instanceof CompanyModel ? $data->getAttributes() : $data;

        $id = LegacyCompanyId::fromInt((int) ($attributes['id'] ?? 0));
        $name = CompanyName::fromString((string) ($attributes['name'] ?? ''));

        $phoneNumbers = CompanyPhoneNumbers::fromStrings(
            primary: $attributes['phone_number1'] ?? null,
            secondary: $attributes['phone_number2'] ?? null,
        );

        $address = CompanyAddress::fromPrimitives(
            address1: $attributes['address1'] ?? null,
            address2: $attributes['address2'] ?? null,
            address3: $attributes['address3'] ?? null,
            subDistrict: $attributes['sub_district'] ?? null,
            district: $attributes['district'] ?? null,
            city: $attributes['city'] ?? null,
            province: $attributes['province'] ?? null,
            country: $attributes['country'] ?? null,
            postalCode: $attributes['postal_code'] ?? null,
        );

        $taxInformation = TaxInformation::fromPrimitives(
            taxId: $attributes['tax_id'] ?? null,
            taxTkuId: $attributes['tax_tku_id'] ?? null,
            taxName: $attributes['tax_name'] ?? null,
            taxAddress: $attributes['tax_address'] ?? null,
            buyerDocument: $attributes['tax_buyer_document'] ?? null,
            buyerCountry: $attributes['tax_buyer_country'] ?? null,
        );

        $tpsConfiguration = TpsConfiguration::fromPrimitives(
            code: $attributes['tps_code'] ?? null,
            warehouseCode: $attributes['tps_warehouse_code'] ?? null,
            decreeNumber: $attributes['decree_number'] ?? null,
            decreeDate: isset($attributes['decree_date']) ? (string) $attributes['decree_date'] : null,
            capacity: isset($attributes['warehouse_capacity']) ? (float) $attributes['warehouse_capacity'] : null,
            username: $attributes['tps_username'] ?? null,
            password: $attributes['tps_password'] ?? null,
        );

        $ceisa40Credentials = Ceisa40Credentials::create(
            username: $attributes['ceisa40_username'] ?? null,
            password: $attributes['ceisa40_password'] ?? null,
            apiKey: $attributes['ceisa40_api_key'] ?? null,
        );

        $allowedIps = $attributes['api_allowwed_ips'] ?? $attributes['api_allowed_ips'] ?? null;
        $apiAccessConfiguration = ApiAccessConfiguration::fromPrimitives(
            username: $attributes['api_username'] ?? null,
            password: $attributes['api_password'] ?? null,
            apiKey: $attributes['api_key'] ?? null,
            allowedIps: $allowedIps,
            lastHitAt: isset($attributes['api_last_hit_at']) ? (string) $attributes['api_last_hit_at'] : null,
        );

        $logos = CompanyLogos::create(
            default: $attributes['logo_default'] ?? null,
            square: $attributes['logo_square'] ?? null,
            landscape: $attributes['logo_landscape'] ?? null,
        );

        $isActive = (bool) ($attributes['is_active'] ?? true);

        $timestamps = null;
        if (isset($attributes['created_at']) || isset($attributes['updated_at'])) {
            $createdAt = isset($attributes['created_at']) ? strtotime((string) $attributes['created_at']) : null;
            $updatedAt = isset($attributes['updated_at']) ? strtotime((string) $attributes['updated_at']) : null;
            $timestamps = new Timestamp(
                createdBy: isset($attributes['created_by']) ? (string) $attributes['created_by'] : null,
                updatedBy: isset($attributes['updated_by']) ? (string) $attributes['updated_by'] : null,
                createdAt: $createdAt ?: null,
                updatedAt: $updatedAt ?: null,
            );
        }

        return Company::reconstitute(
            id: $id,
            name: $name,
            phoneNumbers: $phoneNumbers,
            address: $address,
            taxInformation: $taxInformation,
            tpsConfiguration: $tpsConfiguration,
            ceisa40Credentials: $ceisa40Credentials,
            apiAccessConfiguration: $apiAccessConfiguration,
            logos: $logos,
            isActive: $isActive,
            timestamps: $timestamps,
        );
    }

    /**
     * Map a Company Domain Aggregate Root to database persistence attributes array.
     *
     * @return array<string, mixed>
     */
    public function toModel(Company $company): array
    {
        return [
            'name' => $company->name->value(),
            'phone_number1' => $company->phoneNumbers->primary?->value(),
            'phone_number2' => $company->phoneNumbers->secondary?->value(),
            'address1' => $company->address->address1,
            'address2' => $company->address->address2,
            'address3' => $company->address->address3,
            'sub_district' => $company->address->subDistrict,
            'district' => $company->address->district,
            'city' => $company->address->city,
            'province' => $company->address->province,
            'country' => $company->address->country?->value(),
            'postal_code' => $company->address->postalCode,
            'tax_id' => $company->taxInformation->npwp?->value(),
            'tax_id_clean' => $company->taxInformation->npwp?->clean(),
            'tax_tku_id' => $company->taxInformation->nitku?->value(),
            'tax_name' => $company->taxInformation->taxName,
            'tax_address' => $company->taxInformation->taxAddress,
            'tax_buyer_document' => $company->taxInformation->buyerDocument,
            'tax_buyer_country' => $company->taxInformation->buyerCountry?->value(),
            'tps_code' => $company->tpsConfiguration->code?->value(),
            'tps_warehouse_code' => $company->tpsConfiguration->warehouseCode?->value(),
            'decree_number' => $company->tpsConfiguration->decree?->number ?? '',
            'decree_date' => $company->tpsConfiguration->decree?->date?->format('Y-m-d'),
            'warehouse_capacity' => $company->tpsConfiguration->capacity?->value() ?? 0.0,
            'tps_username' => $company->tpsConfiguration->username,
            'tps_password' => $company->tpsConfiguration->password,
            'ceisa40_username' => $company->ceisa40Credentials->username,
            'ceisa40_password' => $company->ceisa40Credentials->password,
            'ceisa40_api_key' => $company->ceisa40Credentials->apiKey,
            'api_username' => $company->apiAccessConfiguration->username,
            'api_password' => $company->apiAccessConfiguration->password,
            'api_key' => $company->apiAccessConfiguration->apiKey,
            'api_allowwed_ips' => $company->apiAccessConfiguration->allowedIps?->value(),
            'api_last_hit_at' => $company->apiAccessConfiguration->lastHitAt?->format('Y-m-d H:i:s'),
            'logo_default' => $company->logos->default,
            'logo_square' => $company->logos->square,
            'logo_landscape' => $company->logos->landscape,
            'is_active' => $company->isActive,
        ];
    }

    /**
     * Map a raw database record / model into an optimized CompanyRM ReadModel.
     *
     * @param  array<string, mixed>|object  $data
     */
    public function toReadModel(array|object $data): CompanyRM
    {
        if ($data instanceof CompanyModel) {
            $array = $data->getAttributes();
        } elseif (is_array($data)) {
            $array = $data;
        } else {
            $array = (array) $data;
        }

        return CompanyRM::fromArray($array);
    }
}
