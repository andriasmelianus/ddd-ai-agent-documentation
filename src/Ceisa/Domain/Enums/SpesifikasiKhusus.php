<?php

declare(strict_types=1);

namespace Src\Ceisa\Domain\Enums;

/**
 * Enum SpesifikasiKhusus
 *
 * Referensi data CEISA 4.0 DJBC per regulasi resmi.
 */
enum SpesifikasiKhusus: string implements CeisaReferenceEnumInterface
{
    use CeisaReferenceEnumTrait;

    case NOMOR_MESIN = '1';
    case NOMOR_RANGKA = '2';
    case SILINDER = '3';
    case TAHUN_PEMBUATAN = '4';
    case JENIS_KENDARAAN = '5';
    case MERK = '6';
    case MODEL = '7';
    case NOMOR_BPKB = '8';
    case TANGGAL_BPKB = '9';
    case NOMOR_FORM_A = '10';
    case TANGGAL_FORM_A = '11';
    case NOMOR_STNK = '12';
    case TANGGAL_STNK = '13';
    case TIPE = '14';
    case NOMOR_CAS = '15';
    case MUTU = '16';
    case DAYA = '17';
    case KUALITAS_WARNA_GULA = '18';
    case CBU = '19';
    case ALAT_DAPUR = '1001';
    case TINGKAT_KEPECAHAN = '1002';
    case JENIS_PREKURSOR = '1003';
    case KUALITAS_WARNA_GULA_1004 = '1004';
    case TPT_BATIK_MOTIF_BATIK = '1005';
    case JENIS_PERKAKAS_TANGAN = '1006';
    case JENIS_REFRIGERANT = '1007';
    case JENIS_BATERAI_LITHIUM_TIDAK_BARU = '1008';
    case JENIS_FITUR_TELEKOMUNIKASI = '1009';
    case KOMPUTER_TABLET_ATAU_GENGGAM = '1010';
    case SMARTPHONE_ATAU_SMARTWATCH_PHONE = '1011';
    case TELEPON_SELULER_ATAU_SATELIT = '1012';
    case TELEPON_ANALOG_DAN_PABX_SYSTEM = '1013';
    case BASE_TRANSCEIVER_STATION = '1014';
    case ANTENA = '1015';
    case PENYIARAN = '1016';
    case RADAR_MARTIM_DAN_PENERBANGAN = '1017';
    case RADAR_KENDARAAN_DAN_CUACA = '1018';
    case RADAR_SURVEILLANCE_DAN_GPS_TRACKER = '1019';
    case RADIO_MARITIM_PENERBANGAN_BEACON = '1020';
    case JENIS = '3001';
    case BENTUK = '3002';
    case KADAR = '3003';
    case JENIS_3004 = '3004';
    case BENTUK_3005 = '3005';
    case JENIS_3006 = '3006';
    case JENIS_3007 = '3007';

    /**
     * Mendapatkan deskripsi / uraian resmi dari DJBC untuk kode referensi ini.
     */
    public function description(): string
    {
        return match ($this) {
            self::NOMOR_MESIN => 'NOMOR MESIN',
            self::NOMOR_RANGKA => 'NOMOR RANGKA',
            self::SILINDER => 'SILINDER',
            self::TAHUN_PEMBUATAN => 'TAHUN PEMBUATAN',
            self::JENIS_KENDARAAN => 'JENIS KENDARAAN',
            self::MERK => 'MERK',
            self::MODEL => 'MODEL',
            self::NOMOR_BPKB => 'NOMOR BPKB',
            self::TANGGAL_BPKB => 'TANGGAL BPKB',
            self::NOMOR_FORM_A => 'NOMOR FORM A',
            self::TANGGAL_FORM_A => 'TANGGAL FORM A',
            self::NOMOR_STNK => 'NOMOR STNK',
            self::TANGGAL_STNK => 'TANGGAL STNK',
            self::TIPE => 'TIPE',
            self::NOMOR_CAS => 'NOMOR CAS',
            self::MUTU => 'MUTU',
            self::DAYA => 'DAYA',
            self::KUALITAS_WARNA_GULA => 'KUALITAS WARNA GULA',
            self::CBU => 'CBU',
            self::ALAT_DAPUR => 'ALAT DAPUR',
            self::TINGKAT_KEPECAHAN => 'TINGKAT KEPECAHAN',
            self::JENIS_PREKURSOR => 'JENIS PREKURSOR',
            self::KUALITAS_WARNA_GULA_1004 => 'KUALITAS WARNA GULA',
            self::TPT_BATIK_MOTIF_BATIK => 'TPT BATIK MOTIF BATIK',
            self::JENIS_PERKAKAS_TANGAN => 'JENIS PERKAKAS TANGAN',
            self::JENIS_REFRIGERANT => 'JENIS REFRIGERANT',
            self::JENIS_BATERAI_LITHIUM_TIDAK_BARU => 'JENIS BATERAI LITHIUM TIDAK BARU',
            self::JENIS_FITUR_TELEKOMUNIKASI => 'JENIS FITUR TELEKOMUNIKASI',
            self::KOMPUTER_TABLET_ATAU_GENGGAM => 'Komputer Tablet Atau Genggam',
            self::SMARTPHONE_ATAU_SMARTWATCH_PHONE => 'Smartphone Atau Smartwatch Phone',
            self::TELEPON_SELULER_ATAU_SATELIT => 'Telepon Seluler atau Satelit',
            self::TELEPON_ANALOG_DAN_PABX_SYSTEM => 'Telepon Analog dan PABX System',
            self::BASE_TRANSCEIVER_STATION => 'Base Transceiver Station',
            self::ANTENA => 'Antena',
            self::PENYIARAN => 'Penyiaran',
            self::RADAR_MARTIM_DAN_PENERBANGAN => 'Radar Martim dan Penerbangan',
            self::RADAR_KENDARAAN_DAN_CUACA => 'Radar Kendaraan dan Cuaca',
            self::RADAR_SURVEILLANCE_DAN_GPS_TRACKER => 'Radar Surveillance dan GPS Tracker',
            self::RADIO_MARITIM_PENERBANGAN_BEACON => 'Radio Maritim Penerbangan Beacon',
            self::JENIS => 'Jenis',
            self::BENTUK => 'Bentuk',
            self::KADAR => 'Kadar',
            self::JENIS_3004 => 'Jenis',
            self::BENTUK_3005 => 'Bentuk',
            self::JENIS_3006 => 'Jenis',
            self::JENIS_3007 => 'Jenis',
        };
    }

    /**
     * Mendapatkan grup spesifikasi khusus.
     */
    public function grup(): string
    {
        return match ($this) {
            self::NOMOR_MESIN => 'KENDARAAN BERMOTOR',
            self::NOMOR_RANGKA => 'KENDARAAN BERMOTOR',
            self::SILINDER => 'KENDARAAN BERMOTOR',
            self::TAHUN_PEMBUATAN => 'KENDARAAN BERMOTOR',
            self::JENIS_KENDARAAN => 'KENDARAAN BERMOTOR',
            self::MERK => 'KENDARAAN BERMOTOR',
            self::MODEL => 'KENDARAAN BERMOTOR',
            self::NOMOR_BPKB => 'KENDARAAN BERMOTOR',
            self::TANGGAL_BPKB => 'KENDARAAN BERMOTOR',
            self::NOMOR_FORM_A => 'KENDARAAN BERMOTOR',
            self::TANGGAL_FORM_A => 'KENDARAAN BERMOTOR',
            self::NOMOR_STNK => 'KENDARAAN BERMOTOR',
            self::TANGGAL_STNK => 'KENDARAAN BERMOTOR',
            self::TIPE => 'KENDARAAN BERMOTOR',
            self::NOMOR_CAS => 'CHEMICAL ABSTRACT SERVICE (CAS)',
            self::MUTU => 'CHEMICAL ABSTRACT SERVICE (CAS)',
            self::DAYA => 'KENDARAAN BERMOTOR',
            self::KUALITAS_WARNA_GULA => 'ICUMSA',
            self::CBU => 'KENDARAAN BERMOTOR',
            self::ALAT_DAPUR => 'LARTAS',
            self::TINGKAT_KEPECAHAN => 'LARTAS',
            self::JENIS_PREKURSOR => 'LARTAS',
            self::KUALITAS_WARNA_GULA_1004 => 'LARTAS',
            self::TPT_BATIK_MOTIF_BATIK => 'LARTAS',
            self::JENIS_PERKAKAS_TANGAN => 'LARTAS',
            self::JENIS_REFRIGERANT => 'LARTAS',
            self::JENIS_BATERAI_LITHIUM_TIDAK_BARU => 'LARTAS',
            self::JENIS_FITUR_TELEKOMUNIKASI => 'LARTAS',
            self::KOMPUTER_TABLET_ATAU_GENGGAM => 'LARTAS',
            self::SMARTPHONE_ATAU_SMARTWATCH_PHONE => 'LARTAS',
            self::TELEPON_SELULER_ATAU_SATELIT => 'LARTAS',
            self::TELEPON_ANALOG_DAN_PABX_SYSTEM => 'LARTAS',
            self::BASE_TRANSCEIVER_STATION => 'LARTAS',
            self::ANTENA => 'LARTAS',
            self::PENYIARAN => 'LARTAS',
            self::RADAR_MARTIM_DAN_PENERBANGAN => 'LARTAS',
            self::RADAR_KENDARAAN_DAN_CUACA => 'LARTAS',
            self::RADAR_SURVEILLANCE_DAN_GPS_TRACKER => 'LARTAS',
            self::RADIO_MARITIM_PENERBANGAN_BEACON => 'LARTAS',
            self::JENIS => 'GETAH PINUS',
            self::BENTUK => 'KAOLIN DAN TANAH LIAT KAOLIN LAINNYA',
            self::KADAR => 'KORUNDUM ARTIFISIAL',
            self::JENIS_3004 => 'BARANG DARI KARBON',
            self::BENTUK_3005 => 'BARANG DARI BAMBU',
            self::JENIS_3006 => 'BARANG LAINNYA DARI LOGAM MULIA',
            self::JENIS_3007 => 'TEMBAGA DIMURNIKAN',
        };
    }
}
