<?php

declare(strict_types=1);

namespace Src\Company\Domain\Entities;

use DateTimeImmutable;
use Src\Company\Domain\Events\CompanyActivated;
use Src\Company\Domain\Events\CompanyAddressChanged;
use Src\Company\Domain\Events\CompanyApiAccessUpdated;
use Src\Company\Domain\Events\CompanyBasicInfoUpdated;
use Src\Company\Domain\Events\CompanyCeisa40CredentialsUpdated;
use Src\Company\Domain\Events\CompanyCreated;
use Src\Company\Domain\Events\CompanyDeactivated;
use Src\Company\Domain\Events\CompanyLogosUpdated;
use Src\Company\Domain\Events\CompanyTaxInformationUpdated;
use Src\Company\Domain\Events\CompanyTpsConfigurationUpdated;
use Src\Company\Domain\ValueObjects\ApiAccessConfiguration;
use Src\Company\Domain\ValueObjects\Ceisa40Credentials;
use Src\Company\Domain\ValueObjects\CompanyAddress;
use Src\Company\Domain\ValueObjects\CompanyLogos;
use Src\Company\Domain\ValueObjects\CompanyName;
use Src\Company\Domain\ValueObjects\CompanyPhoneNumbers;
use Src\Company\Domain\ValueObjects\LegacyCompanyId;
use Src\Company\Domain\ValueObjects\TaxInformation;
use Src\Company\Domain\ValueObjects\TpsConfiguration;
use Src\Shared\Framework\Domain\Entities\BaseAggregateRoot;
use Src\Shared\Framework\Domain\ValueObjects\Timestamp;

/**
 * Class Company
 *
 * Aggregate Root for Company domain.
 * Enforces business invariants, manages lifecycle, and publishes domain events.
 */
class Company extends BaseAggregateRoot
{
    private function __construct(
        public readonly LegacyCompanyId $id,
        public private(set) CompanyName $name,
        public private(set) CompanyPhoneNumbers $phoneNumbers,
        public private(set) CompanyAddress $address,
        public private(set) TaxInformation $taxInformation,
        public private(set) TpsConfiguration $tpsConfiguration,
        public private(set) Ceisa40Credentials $ceisa40Credentials,
        public private(set) ApiAccessConfiguration $apiAccessConfiguration,
        public private(set) CompanyLogos $logos,
        public private(set) bool $isActive,
    ) {}

    public static function create(
        LegacyCompanyId $id,
        CompanyName $name,
        ?CompanyPhoneNumbers $phoneNumbers = null,
        ?CompanyAddress $address = null,
        ?TaxInformation $taxInformation = null,
        ?TpsConfiguration $tpsConfiguration = null,
        ?Ceisa40Credentials $ceisa40Credentials = null,
        ?ApiAccessConfiguration $apiAccessConfiguration = null,
        ?CompanyLogos $logos = null,
        bool $isActive = true,
    ): self {
        $phoneNumbers ??= CompanyPhoneNumbers::empty();
        $address ??= CompanyAddress::empty();
        $taxInformation ??= TaxInformation::empty();
        $tpsConfiguration ??= TpsConfiguration::empty();
        $ceisa40Credentials ??= Ceisa40Credentials::empty();
        $apiAccessConfiguration ??= ApiAccessConfiguration::empty();
        $logos ??= CompanyLogos::empty();

        $company = new self(
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
        );

        $company->setIsNew(true);
        $company->recordEvent(new CompanyCreated($id, $name));

        return $company;
    }

    public static function reconstitute(
        LegacyCompanyId $id,
        CompanyName $name,
        CompanyPhoneNumbers $phoneNumbers,
        CompanyAddress $address,
        TaxInformation $taxInformation,
        TpsConfiguration $tpsConfiguration,
        Ceisa40Credentials $ceisa40Credentials,
        ApiAccessConfiguration $apiAccessConfiguration,
        CompanyLogos $logos,
        bool $isActive,
        ?Timestamp $timestamps = null,
    ): self {
        $company = new self(
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
        );

        $company->setIsNew(false);
        if ($timestamps !== null) {
            $company->timestamps = $timestamps;
        }

        $company->makeCheckPoint();

        return $company;
    }

    public function updateBasicInfo(CompanyName $name, CompanyPhoneNumbers $phoneNumbers): void
    {
        if ($this->name->equals($name) && $this->phoneNumbers->equals($phoneNumbers)) {
            return;
        }

        $this->name = $name;
        $this->phoneNumbers = $phoneNumbers;

        $this->recordEvent(new CompanyBasicInfoUpdated($this->id, $this->name, $this->phoneNumbers));
    }

    public function changeAddress(CompanyAddress $address): void
    {
        if ($this->address->equals($address)) {
            return;
        }

        $this->address = $address;

        $this->recordEvent(new CompanyAddressChanged($this->id, $this->address));
    }

    public function updateTaxInformation(TaxInformation $taxInformation): void
    {
        if ($this->taxInformation->equals($taxInformation)) {
            return;
        }

        $this->taxInformation = $taxInformation;

        $this->recordEvent(new CompanyTaxInformationUpdated($this->id, $this->taxInformation));
    }

    public function updateTpsConfiguration(TpsConfiguration $tpsConfiguration): void
    {
        if ($this->tpsConfiguration->equals($tpsConfiguration)) {
            return;
        }

        $this->tpsConfiguration = $tpsConfiguration;

        $this->recordEvent(new CompanyTpsConfigurationUpdated($this->id, $this->tpsConfiguration));
    }

    public function updateCeisa40Credentials(Ceisa40Credentials $ceisa40Credentials): void
    {
        if ($this->ceisa40Credentials->equals($ceisa40Credentials)) {
            return;
        }

        $this->ceisa40Credentials = $ceisa40Credentials;

        $this->recordEvent(new CompanyCeisa40CredentialsUpdated($this->id));
    }

    public function updateApiAccess(ApiAccessConfiguration $apiAccessConfiguration): void
    {
        if ($this->apiAccessConfiguration->equals($apiAccessConfiguration)) {
            return;
        }

        $this->apiAccessConfiguration = $apiAccessConfiguration;

        $this->recordEvent(new CompanyApiAccessUpdated($this->id));
    }

    public function recordApiHit(DateTimeImmutable $hitAt): void
    {
        $this->apiAccessConfiguration = $this->apiAccessConfiguration->withLastHitAt($hitAt);
    }

    public function activate(): void
    {
        if ($this->isActive) {
            return;
        }

        $this->isActive = true;

        $this->recordEvent(new CompanyActivated($this->id));
    }

    public function deactivate(): void
    {
        if (! $this->isActive) {
            return;
        }

        $this->isActive = false;

        $this->recordEvent(new CompanyDeactivated($this->id));
    }

    public function updateLogos(CompanyLogos $logos): void
    {
        if ($this->logos->equals($logos)) {
            return;
        }

        $this->logos = $logos;

        $this->recordEvent(new CompanyLogosUpdated($this->id));
    }

    public function id(): LegacyCompanyId
    {
        return $this->id;
    }

    public function name(): CompanyName
    {
        return $this->name;
    }

    public function phoneNumbers(): CompanyPhoneNumbers
    {
        return $this->phoneNumbers;
    }

    public function address(): CompanyAddress
    {
        return $this->address;
    }

    public function taxInformation(): TaxInformation
    {
        return $this->taxInformation;
    }

    public function tpsConfiguration(): TpsConfiguration
    {
        return $this->tpsConfiguration;
    }

    public function ceisa40Credentials(): Ceisa40Credentials
    {
        return $this->ceisa40Credentials;
    }

    public function apiAccessConfiguration(): ApiAccessConfiguration
    {
        return $this->apiAccessConfiguration;
    }

    public function logos(): CompanyLogos
    {
        return $this->logos;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
