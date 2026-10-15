<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Exceptions\Tps;

use DomainException;
use Src\Shared\Framework\Domain\Exceptions\VisibleExceptionInterface;

/**
 * Class PersetujuanPlpException
 *
 * Domain exception for business rule and invariant violations in PersetujuanPlp aggregate.
 */
final class PersetujuanPlpException extends DomainException implements VisibleExceptionInterface
{
    public static function duplicateKontainer(string $nomorKontainer): self
    {
        return new self(sprintf(
            'Kontainer dengan nomor "%s" sudah terdaftar dalam dokumen Persetujuan PLP ini.',
            $nomorKontainer
        ));
    }

    public static function kontainerNotFound(string $nomorKontainer): self
    {
        return new self(sprintf(
            'Kontainer dengan nomor "%s" tidak ditemukan dalam dokumen Persetujuan PLP ini.',
            $nomorKontainer
        ));
    }

    public static function duplicateKemasan(string $nomorBlAwb, string $nomorPosBc11): self
    {
        return new self(sprintf(
            'Kemasan dengan nomor B/L atau AWB "%s" dan Pos BC 1.1 "%s" sudah terdaftar dalam dokumen Persetujuan PLP ini.',
            $nomorBlAwb,
            $nomorPosBc11
        ));
    }

    public static function kemasanNotFound(string $nomorBlAwb, string $nomorPosBc11): self
    {
        return new self(sprintf(
            'Kemasan dengan nomor B/L atau AWB "%s" dan Pos BC 1.1 "%s" tidak ditemukan dalam dokumen Persetujuan PLP ini.',
            $nomorBlAwb,
            $nomorPosBc11
        ));
    }

    public static function sameOriginAndDestination(string $tps, string $gudang): self
    {
        return new self(sprintf(
            'Lokasi asal dan tujuan pemindahan tidak boleh sama (TPS: "%s", Gudang: "%s").',
            $tps,
            $gudang
        ));
    }

    public static function negativeJumlahKemasan(float|int $jumlah): self
    {
        return new self(sprintf(
            'Jumlah kemasan tidak boleh negatif, diberikan: %s.',
            $jumlah
        ));
    }

    public static function emptyConsignee(): self
    {
        return new self('Nama penerima barang (consignee) tidak boleh kosong.');
    }
}

