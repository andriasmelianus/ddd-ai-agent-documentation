<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum Status
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum Status: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case PEREKAMAN_DOKUMEN = '001';
    case PENERIMAAN_DATA = '100';
    case KIRIM_DOKUMEN_KE_INSW = '104';
    case LNSW_PENERIMAAN_DOKUMEN = '105';
    case LNSW_CEK_MANDATORY_CONTENT = '106';
    case LNSW_ANALYZING_POINT = '107';
    case LNSW_KONFIRMASI_SPEK = '108';
    case VALIDASI = '110';
    case PROSES_VALIDASI = '111';
    case REJECT = '120';
    case PENERIMAAN_DOKUMEN = '130';
    case ANALYZING_POINT = '200';
    case PENGECEKAN_JAMINAN = '205';
    case PAYMENT_VERIFICATION = '210';
    case PRE_NOTIFICATION = '220';
    case APPROVAL_SIMILARITY = '225';
    case SIAP_JALUR = '230';
    case PENJALURAN = '240';
    case PEMERIKSAAN_FISIK = '300';
    case PEMERIKSAAN_BARANG = '330';
    case PEMERIKSAAN_DOKUMEN = '400';
    case INP = '410';
    case LAB = '415';
    case SPTNP = '440';
    case GATE_IN_TPS = '500';
    case GATE_OUT_TPS = '501';
    case PELEKATAN_SEGEL = '502';
    case PEMBUKAAN_SEGEL = '503';
    case GATE_IN_TPB = '510';
    case PEMBONGKARAN = '511';
    case STUFFING = '512';
    case AREA_TRANSIT = '513';
    case GATE_OUT_TPB = '514';
    case SELESAI_PROSES = '800';
    case PEMBATALAN = '900';
    case PENELITIAN_UNIT_PENGAWAS = '981';
    case PEREKAMAN_PERBAIKAN_PORTAL = '960';
    case PENELITIAN_PERBAIKAN = '961';
    case PEREKAMAN_PERBAIKAN_INHOUSE = '962';
    case VALIDASI_PERBAIKAN = '963';
    case PENOLAKAN_PERBAIKAN = '965';
    case PERSETUJUAN_PERBAIKAN = '966';
    case PEMBATALAN_PERBAIKAN = '969';
    case PERBAIKAN_RH = '970';
    case PERBAIKAN_RH_SELESAI = '971';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::PEREKAMAN_DOKUMEN => 'Perekaman Dokumen',
            self::PENERIMAAN_DATA => 'Penerimaan Data',
            self::KIRIM_DOKUMEN_KE_INSW => 'Kirim Dokumen Ke INSW',
            self::LNSW_PENERIMAAN_DOKUMEN => 'LNSW - Penerimaan Dokumen',
            self::LNSW_CEK_MANDATORY_CONTENT => 'LNSW - Cek Mandatory Content',
            self::LNSW_ANALYZING_POINT => 'LNSW - Analyzing Point',
            self::LNSW_KONFIRMASI_SPEK => 'LNSW - Konfirmasi SPEK',
            self::VALIDASI => 'Validasi',
            self::PROSES_VALIDASI => 'Proses Validasi',
            self::REJECT => 'Reject',
            self::PENERIMAAN_DOKUMEN => 'Penerimaan Dokumen',
            self::ANALYZING_POINT => 'Analyzing Point',
            self::PENGECEKAN_JAMINAN => 'Pengecekan Jaminan',
            self::PAYMENT_VERIFICATION => 'Payment Verification',
            self::PRE_NOTIFICATION => 'Pre-Notification',
            self::APPROVAL_SIMILARITY => 'Approval Similarity',
            self::SIAP_JALUR => 'Siap Jalur',
            self::PENJALURAN => 'Penjaluran',
            self::PEMERIKSAAN_FISIK => 'Pemeriksaan Fisik',
            self::PEMERIKSAAN_BARANG => 'Pemeriksaan Barang',
            self::PEMERIKSAAN_DOKUMEN => 'Pemeriksaan Dokumen',
            self::INP => 'INP',
            self::LAB => 'LAB',
            self::SPTNP => 'SPTNP',
            self::GATE_IN_TPS => 'Gate In TPS',
            self::GATE_OUT_TPS => 'Gate Out TPS',
            self::PELEKATAN_SEGEL => 'Pelekatan Segel',
            self::PEMBUKAAN_SEGEL => 'Pembukaan Segel',
            self::GATE_IN_TPB => 'Gate In TPB',
            self::PEMBONGKARAN => 'Pembongkaran',
            self::STUFFING => 'Stuffing',
            self::AREA_TRANSIT => 'Area Transit',
            self::GATE_OUT_TPB => 'Gate Out TPB',
            self::SELESAI_PROSES => 'Selesai Proses',
            self::PEMBATALAN => 'Pembatalan',
            self::PENELITIAN_UNIT_PENGAWAS => 'Penelitian Unit Pengawas',
            self::PEREKAMAN_PERBAIKAN_PORTAL => 'Perekaman Perbaikan Portal',
            self::PENELITIAN_PERBAIKAN => 'Penelitian Perbaikan',
            self::PEREKAMAN_PERBAIKAN_INHOUSE => 'Perekaman Perbaikan Inhouse',
            self::VALIDASI_PERBAIKAN => 'Validasi Perbaikan',
            self::PENOLAKAN_PERBAIKAN => 'Penolakan Perbaikan',
            self::PERSETUJUAN_PERBAIKAN => 'Persetujuan Perbaikan',
            self::PEMBATALAN_PERBAIKAN => 'Pembatalan Perbaikan',
            self::PERBAIKAN_RH => 'Perbaikan RH',
            self::PERBAIKAN_RH_SELESAI => 'Perbaikan RH Selesai',
        };
    }
}
