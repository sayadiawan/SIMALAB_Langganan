<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Nota Kesmas">
    <meta name="author" content="Labkes Boyolali">
    <title>Nota BOYOLALI-KESMAS</title>
    <link rel="shortcut icon" href="favicon.ico">
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            font-size: 10px;
            font-family: Arial, sans-serif;
        }




        hr {
            border: none;
            border-top: 2px dashed black;
        }

        .nota {
            width: 100%;
            height: 31%;
        }

        .nota-kop {
            width: 15%;
            height: 31%;
            padding: 0;
        }

        .nota-body {
            width: 85%;
            padding-left: 20px;
            padding-right: 20px;
            padding-top: 8px;
            padding-bottom: 8x;
            vertical-align: top;
        }

        .nota-body-2 {
            background-color: #FFE3F0;
        }

        .nota-body-3 {
            background-color: #C1EDFF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 2px;
        }

        @media print {
            #cetak {
                display: none;
            }
        }

        .result {
            border: 1px solid black;
        }

        .result td {
            border: 1px solid black;
            text-align: center;
            font-size: 10px;
        }

        .result2 {
            margin-top: 2px;
        }

        .result2 td {
            font-weight: bold;
            font-size: 10px;
        }

        .parallelogram {
            width: 100%;
            padding: 6px 3px;
            border: 1px solid black;
            transform: skew(-20deg);
            display: flex;
            align-items: center;
        }

        span.text {
            font-size: 10px;
            display: inline-block;
            transform: skew(-8deg);
        }

        @font-face {
            font-family: 'DejaVu Sans', sans-serif !important;
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            font-size: 11px;
        }

        span.text-date {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <table class="nota">
        <tr>
            <td class="nota-kop">
                <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated_nota.png') }}" width="100%"
                    height="32%">
            </td>
            <td class="nota-body">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2 fixed"
                    style="overflow-x:auto; ">
                    <thead>
                        <tr>
                            <td width="30%">
                                TELAH DITERIMA DARI
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                    {{ $permohonan_uji->nota_diterima_dari ?? $permohonan_uji->customer->name_customer }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                          <td>TANGGAL PEMERIKSAAN</td>
                          <td>:</td>
                          <td>
                            @if ($tanggalPemeriksaan)
                              {{ Carbon\Carbon::parse($tanggalPemeriksaan)->isoFormat('DD MMMM YYYY') }}
                            @else
                              -
                            @endif
                          </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                ALAMAT
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">
                                    {{ $permohonan_uji->customer->address_customer }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                UANG SEJUMLAH
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                <div>
                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                    @else
                                        {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                    @endif
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                GUNA MEMBAYAR
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                JENIS PEMERIKSAAN LABORATORIUM, terdiri dari:
                            </td>
                        </tr>
                    </thead>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    <tr>
                        <td width="100%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="overflow-x:auto; ">
                                <tr>
                                    <td width="4%" class="border result" align="center">
                                        No.
                                    </td>
                                    <td width="35%" class="border result" align="center">
                                        PARAMETER/PAKET
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        JUMLAH SAMPEL
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        HARGA SATUAN (Rp)
                                    </td>
                                    <td width="25%" class="border result" align="center">
                                        JUMLAH<br>
                                        (Rp)
                                    </td>
                                </tr>
                                @php
                                    $no = 1;

                                @endphp
                                @foreach ($value_items as $value_item)
                                    <tr>
                                        <td width="5%" class="border result" align="center">
                                            {{ $no }}.
                                        </td>

                                        <td width="35%" class="border result">

                                            {{ $value_item['name_item'] }}
                                        </td>
                                        <td width="15%" class="border result" align="center">

                                            {{ $value_item['count_item'] }}
                                        </td>

                                        <td width="15%" class="border result" align="center">

                                            {{ rupiah($value_item['price_item']) }}
                                        </td>
                                        <td width="25%" class="border result" align="center">
                                            {{ rupiah($value_item['total']) }}
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>
                <br>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH PERMOHONAN UJI
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? $permohonan_uji->total_harga : '0') }}

                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH BIAYA TINDAKAN RECTAL SWAB
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->biaya_tindakan_rectal_swab ? $permohonan_uji->biaya_tindakan_rectal_swab : '0') }}
                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="70%">

                            </td>

                            <td width="15%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>

                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>
                            <br>
                        </td>
                        <td width="15%">

                        </td>
                    </tr>

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                class="border result2" style="overflow-x:auto; ">
                                <tr>
                                    <td width="20%">
                                        TERBILANG
                                    </td>
                                    <td width="3%">:</td>

                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @else
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @if ($permohonan_uji->status_pembayaran)
                                    <tr>
                                        <td width="20%">
                                            STATUS
                                        </td>
                                        <td width="3%">: </td>
                                        <td>
                                            LUNAS
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>

                        </td>
                        <td width="25%">
                            <span class="text-date">
                                <p>Boyolali, @if ($permohonan_uji->status_pembayaran)
                                        {{ \Smt\Masterweb\Helpers\DateHelper::formatDateIndo(date('d F, Y')) }}
                                    @endif
                                </p>
                                Yang menerima<br>
                                Petugas,<br><br><br>
                                Pitoyo
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <table class="nota">
        <tr>
            <td class="nota-kop nota-body-2">
                <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated_nota_removebg.png') }}" width="100%"
                    height="32%">
            </td>
            <td class="nota-body nota-body-2">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2 fixed"
                    style="overflow-x:auto; ">
                    <thead>
                        <tr>
                            <td width="30%">
                                TELAH DITERIMA DARI
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                    {{ $permohonan_uji->nota_diterima_dari ?? $permohonan_uji->customer->name_customer }}
                                </span>
                        </tr>
                        <tr>
                          <td>TANGGAL PEMERIKSAAN</td>
                          <td>:</td>
                          <td>
                            @if ($tanggalPemeriksaan)
                              {{ Carbon\Carbon::parse($tanggalPemeriksaan)->isoFormat('DD MMMM YYYY') }}
                            @else
                              -
                            @endif
                          </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                ALAMAT
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                    {{ $permohonan_uji->customer->address_customer }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                UANG SEJUMLAH
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                <div>
                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                    @else
                                        {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                    @endif
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                GUNA MEMBAYAR
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                JENIS PEMERIKSAAN LABORATORIUM, terdiri dari:
                            </td>
                        </tr>
                    </thead>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    <tr>
                        <td width="100%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="overflow-x:auto; ">

                                <tr>
                                    <td width="4%" class="border result" align="center">
                                        No.
                                    </td>

                                    <td width="35%" class="border result" align="center">
                                        PARAMETER/PAKET
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        JUMLAH SAMPEL
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        HARGA SATUAN (Rp)
                                    </td>

                                    <td width="25%" class="border result" align="center">
                                        JUMLAH<br>
                                        (Rp)
                                    </td>
                                </tr>
                                @php
                                    $no = 1;

                                @endphp
                                @foreach ($value_items as $value_item)
                                    <tr>
                                        <td width="5%" class="border result" align="center">
                                            {{ $no }}.
                                        </td>

                                        <td width="35%" class="border result">

                                            {{ $value_item['name_item'] }}
                                        </td>
                                        <td width="15%" class="border result" align="center">

                                            {{ $value_item['count_item'] }}
                                        </td>

                                        <td width="15%" class="border result" align="center">

                                            {{ rupiah($value_item['price_item']) }}
                                        </td>
                                        <td width="25%" class="border result" align="center">
                                            {{ rupiah($value_item['total']) }}
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            </table>
                        </td>
                    </tr>


                </table>

                <br>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH PERMOHONAN UJI
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? $permohonan_uji->total_harga : '0') }}

                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH BIAYA TINDAKAN RECTAL SWAB
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->biaya_tindakan_rectal_swab ? $permohonan_uji->biaya_tindakan_rectal_swab : '0') }}
                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="70%">

                            </td>

                            <td width="15%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>

                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>
                            <br>
                        </td>
                        <td width="15%">

                        </td>
                    </tr>

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                class="border result2" style="overflow-x:auto; ">
                                <tr>
                                    <td width="20%">
                                        TERBILANG
                                    </td>
                                    <td width="3%">:</td>

                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @else
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @if ($permohonan_uji->status_pembayaran)
                                    <tr>
                                        <td width="20%">
                                            STATUS
                                        </td>
                                        <td width="3%">: </td>
                                        <td>
                                            LUNAS
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>

                        </td>
                        <td width="25%">
                            <span class="text-date">
                                <p>Boyolali, @if ($permohonan_uji->status_pembayaran)
                                        {{ \Smt\Masterweb\Helpers\DateHelper::formatDateIndo(date('d F, Y')) }}
                                    @endif
                                </p>
                                Yang menerima<br>
                                Petugas,<br><br><br>
                                Pitoyo
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <table class="nota">
        <tr>
            <td class="nota-kop nota-body-3">
                <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated_nota_removebg.png') }}" width="100%"
                    height="32%">
            </td>
            <td class="nota-body nota-body-3">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2 fixed"
                    style="overflow-x:auto; ">
                    <thead>
                        <tr>
                            <td width="30%">
                                TELAH DITERIMA DARI
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                    {{ $permohonan_uji->nota_diterima_dari ?? $permohonan_uji->customer->name_customer }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                          <td>TANGGAL PEMERIKSAAN</td>
                          <td>:</td>
                          <td>
                            @if ($tanggalPemeriksaan)
                              {{ Carbon\Carbon::parse($tanggalPemeriksaan)->isoFormat('DD MMMM YYYY') }}
                            @else
                              -
                            @endif
                          </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                ALAMAT
                            </td>
                            <td width="2%">
                                :
                            </td>
                            <td width="68%">
                                <span
                                    style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                    {{ $permohonan_uji->customer->address_customer }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                UANG SEJUMLAH
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                <div>
                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                    @else
                                        {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                    @endif

                                </div>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%"></td>
                            <td width="1%"></td>
                            <td width="69%"></td>
                        </tr>
                        <tr>
                            <td width="30%">
                                GUNA MEMBAYAR
                            </td>
                            <td width="1%">
                                :
                            </td>
                            <td width="69%">
                                JENIS PEMERIKSAAN LABORATORIUM, terdiri dari:
                            </td>
                        </tr>

                    </thead>
                </table>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    <tr>
                        <td width="100%">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="overflow-x:auto; ">

                                <tr>
                                    <td width="4%" class="border result" align="center">
                                        No.
                                    </td>

                                    <td width="35%" class="border result" align="center">
                                        PARAMETER/PAKET
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        JUMLAH SAMPEL
                                    </td>
                                    <td width="15%" class="border result" align="center">
                                        HARGA SATUAN (Rp)
                                    </td>

                                    <td width="25%" class="border result" align="center">
                                        JUMLAH<br>
                                        (Rp)
                                    </td>
                                </tr>
                                @php
                                    $no = 1;

                                @endphp
                                @foreach ($value_items as $value_item)
                                    <tr>
                                        <td width="5%" class="border result" align="center">
                                            {{ $no }}.
                                        </td>

                                        <td width="35%" class="border result">

                                            {{ $value_item['name_item'] }}
                                        </td>
                                        <td width="15%" class="border result" align="center">

                                            {{ $value_item['count_item'] }}
                                        </td>

                                        <td width="15%" class="border result" align="center">

                                            {{ rupiah($value_item['price_item']) }}
                                        </td>
                                        <td width="25%" class="border result" align="center">
                                            {{ rupiah($value_item['total']) }}
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            </table>
                        </td>
                    </tr>


                </table>

                <br>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border result2"
                    style="overflow-x:auto; ">

                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH PERMOHONAN UJI
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? $permohonan_uji->total_harga : '0') }}

                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH BIAYA TINDAKAN RECTAL SWAB
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->biaya_tindakan_rectal_swab ? $permohonan_uji->biaya_tindakan_rectal_swab : '0') }}
                            </td>
                        </tr>

                        <tr>
                            <td width="40%">

                            </td>

                            <td width="45%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td width="70%">

                            </td>

                            <td width="15%">
                                JUMLAH TOTAL
                            </td>

                            <td width="15%">
                                {{ rupiah($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>

                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>
                            <br>
                        </td>
                        <td width="15%">

                        </td>
                    </tr>

                    <tr>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="40%"
              @else
                width="70%" @endif>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                class="border result2" style="overflow-x:auto; ">
                                <tr>
                                    <td width="20%">
                                        TERBILANG
                                    </td>
                                    <td width="3%">:</td>

                                    @if ($permohonan_uji->biaya_tindakan_rectal_swab != null)
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga || $permohonan_uji->biaya_tindakan_rectal_swab ? (int) $permohonan_uji->total_harga + $permohonan_uji->biaya_tindakan_rectal_swab : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @else
                                        <td width="100%" colspan="3">
                                            <div class="parallelogram">
                                                <span class="text">
                                                    {{ terbilang($permohonan_uji->total_harga ? (int) $permohonan_uji->total_harga : (int) '0') }}
                                                </span>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @if ($permohonan_uji->status_pembayaran)
                                    <tr>
                                        <td width="20%">
                                            STATUS
                                        </td>
                                        <td width="3%">: </td>
                                        <td>
                                            LUNAS
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                        <td
                            @if ($permohonan_uji->biaya_tindakan_rectal_swab != null) width="45%"
              @else
                width="15%" @endif>

                        </td>
                        <td width="25%">
                            <span class="text-date">
                                <p>Boyolali, @if ($permohonan_uji->status_pembayaran)
                                        {{ \Smt\Masterweb\Helpers\DateHelper::formatDateIndo(date('d F, Y')) }}
                                    @endif
                                </p>
                                Yang menerima<br>
                                Petugas,<br><br><br>
                                Pitoyo
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <hr>
</body>

</html>
