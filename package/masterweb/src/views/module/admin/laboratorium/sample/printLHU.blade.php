<html lang="">



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>LHU.ELIT-2002006-AP </title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <style>
        .starter-template {
            text-align: center;
        }

        table>tr>td {
            cell-padding: 5px !important;
        }

        @media print {
            #cetak {
                display: none;
            }
        }

        .garis {
            border: 1px solid
        }

        .table2 {
            font-size: 5px;
            text-align: center
        }

        .result {
            border-collapse: collapse;
        }

        .result td {
            border: 1px solid black;
            text-align: center;
        }

        @page {
            size: 794px 1248px;
            margin: 20px 20px 20px 20px;
        }

        body {
            font-size: 12px;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body style="margin: 10px; padding:0">
    <div class="row text-center" id="header">
        {{-- <img src="/mnt/sda1/laravel/BOYOLALI-prod/public/storage/admin/images/logo/kop_BOYOLALI.png"
      width="730px"> --}}
        <img src="{{ public_path('assets/admin/images/logo/kop_perusahaan.png') }}" width="730px" class="img-fluid">
    </div>

    <div class="container">

        <div class="row batas">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <table width="100%" class="table">
                    <tr>
                        <td width="10%">
                            Nomor
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {!! $no_LHU !!}
                        </td>
                        <td align="right">
                            Boyolali,
                            {{ isset($pengesahan_hasil->pengesahan_hasil_date)
                                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->isoFormat('D MMMM Y')
                                : '' }}

                        </td>
                    </tr>
                    <tr>
                        <td width="10%">
                            Hal
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            Hasil Pemeriksaan {{ $laboratorium->nama_laboratorium }}
                        </td>
                        <td align="right">

                        </td>
                    </tr>
                </table>

                <br>

                <table width="40%" class="table">
                    <tr>
                        <td colspan="2">
                            Yang Terhormat :
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2">
                            {{ $sample->name_customer }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            {{ $sample->address_customer }}
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2">
                            di-
                        </td>

                    </tr>
                    <tr>
                        <td width="3%">
                        </td>
                        <td>
                            <u>{{ $sample->kecamatan_customer }}</u>
                        </td>
                    </tr>
                </table>
                <br>
                <br>
                Disampaikan dengan hormat hasil pemeriksaan laboratorium kami adalah sebagai berikut:<br>

                <table width="100%" class="table">
                    {{-- <tr>
            <td width="30%">
              No. Lab
            </td>
            <td width="1%">
              :
            </td>
            <td>
            {{ isset($lab_num->lab_number)?
              sprintf("%04d",(int)$lab_num->lab_number):""
              }}
            </td>
          </tr> --}}
                    <tr>
                        <td width="30%">
                            No. Sampel
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {{ explode('/', $sample->codesample_samples)[2] }}
                        </td>
                    </tr>

                    <tr>
                        <td width="30%">
                            Jenis sampel
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {{ $sample->name_sample_type }}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            Lokasi sampel
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {{ $sample->location_samples }}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            Pengambil sampel
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {{ $sample->nama_pengambil }}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            Tanggal diambil/diterima
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>

                            {{ isset($sample->datesampling_samples)
                                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->datesampling_samples)->isoFormat('D MMMM Y')
                                : '-' }}
                            /
                            {{ isset($sample->date_sending)
                                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->isoFormat('D MMMM Y')
                                : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td width="30%">
                            Tanggal pemeriksaan
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                            {{ isset($sample->date_checking)
                                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_checking)->isoFormat('D MMMM Y')
                                : '-' }}
                            s.d
                            {{ isset($sample->date_done_estimation_labs)
                                ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_done_estimation_labs)->isoFormat('D MMMM Y')
                                : '-' }}
                        </td>
                    </tr>

                    <tr>
                        <td width="30%">
                            Hasil pemeriksaan
                        </td>
                        <td width="1%">
                            :
                        </td>
                        <td>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" colspan="3">
                            <table class="table table-bordered result" border="1" cellspacing="0" cellpadding="0"
                                width="100%">
                                <tbody>
                                    <tr>
                                        <th width="5%" style="text-align: center">No</th>
                                        <th width="30%" style="text-align: center">Parameter Pemeriksaan</th>
                                        <th width="25%" style="text-align: center">Hasil Pemeriksaan</th>
                                        <th width="25%" style="text-align: center">Batas Syarat</th>
                                    </tr>
                                </tbody>

                                <tbody>
                                    @if (count($laboratoriummethods) > 0)
                                        @php
                                            $no = 1;
                                        @endphp

                                        @foreach ($laboratoriummethods as $laboratoriummethod)
                                            <tr>
                                                <td width="5%"><br>{{ $no }}<br><br></td>
                                                <td width="30%"><br>{!! $laboratoriummethod->name_report !!}<br><br></td>
                                                @php

                                                    $hasil = cek_hasil_color(
                                                        isset($laboratoriummethod->hasil)
                                                            ? $laboratoriummethod->hasil
                                                            : (isset($laboratoriummethod->equal)
                                                                ? $laboratoriummethod->equal
                                                                : ''),
                                                        $laboratoriummethod->min,
                                                        $laboratoriummethod->max,
                                                        $laboratoriummethod->equal,
                                                        'result_output_method_' . $laboratoriummethod->method_id,
                                                        $laboratoriummethod->offset_baku_mutu,
                                                    );
                                                    $unit = $laboratoriummethod->shortname_unit;

                                                    if (isset($unit)) {
                                                        $unit = '';
                                                        if (
                                                            trim($laboratoriummethod->shortname_unit) != '-' &&
                                                            trim($hasil) != '-'
                                                        ) {
                                                            $unit = $laboratoriummethod->shortname_unit;
                                                        }
                                                    } else {
                                                        $unit = '';
                                                    }
                                                @endphp


                                                @if ($laboratoriummethod->nilai_baku_mutu == $unit)
                                                    <td width="25%"><br>{!! $hasil !!}<br><br></td>
                                                    <td width="25%"><br> {!! $laboratoriummethod->nilai_baku_mutu !!} <br><br></td>
                                                @else
                                                    <td width="25%"><br>{!! $hasil . $unit !!}<br><br></td>
                                                    <td width="25%"><br> {!! $laboratoriummethod->nilai_baku_mutu !!}
                                                        {!! $unit !!}<br><br></td>
                                                @endif
                                            </tr>
                                            . {{ $laboratoriummethod->params_method }}
                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach
                                    @endif
                                    </thead>
                            </table>
                        </td>
                    </tr>
                </table>
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="text-align: left">Ket: Hasil pemeriksaan dengan tanda (*) tidak memenuhi syarat baku
                            mutu yang
                            ditetapkan</td>
                    </tr>
                </table>
                <br>

                <table width="100%" class="table">
                    <tr>
                        <td width="30%" style="vertical-align:top;">Rujukan baku mutu</td>
                        <td width="1%" style="vertical-align:top;">:</td>
                        <td width="69%" style="vertical-align:top;">
                            <table width="100%" class="table">
                                @if (count($all_acuan_baku_mutu) > 0)

                                    @if (count($all_acuan_baku_mutu) > 1)
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($all_acuan_baku_mutu as $acuan_baku_mutu)
                                            <tr>
                                                <td width="3%" style="vertical-align:top;">{{ $no }}.
                                                </td>
                                                <td width="93%" style="vertical-align:top;">
                                                    {{ $acuan_baku_mutu->title_library }}</td>
                                            </tr>

                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td width="100%" style="vertical-align:top;">
                                                {{ $all_acuan_baku_mutu[0]->title_library }}</td>
                                        </tr>

                                    @endif
                                @endif
                            </table>
                        </td>

                    </tr>
                </table>

                Demikian hasil pemeriksaan ini untuk dapat digunakan seperlunya.
                <p>&nbsp;</p>

                <div class="row batas"></div>

                <table border="0" cellpadding="0" cellspacing="0" width="100%" class="border">

                </table>

                <div class="row batas" style="float: right; justify-content: center;">
                    <div class="col-md-8"></div>
                    <div class="col-md-4" style="text-align: center;">
                        Kepala UPT Laboratorium Kesehatan<br>
                        Dinas Kesehatan<br>

                        @if (isset($verifikasi))
                            {{-- <img src="{{ asset('assets/admin/images/ttd.png') }}" width="100px" class="img-fluid">
            <div style="clear: right">
            </div> --}}
                            <br>
                            <br>
                            <br>
                            <br>
                        @else
                            <br>
                            <br>
                            <br>
                            <br>
                        @endif
                        <strong><u>dr. Muharyati</u></strong><br>
                        Pembina<br>
                        NIP. 19721106 200212 2 001
                        {{-- <strong><u>{{$laboratorium->nama_ttd_kepala_laboratorium}}</u></strong><br>
            NIP.{{$laboratorium->nip_kepala_laboratorium}} --}}
                    </div>
                </div>

            </div>
        </div>
</body>

</html>
