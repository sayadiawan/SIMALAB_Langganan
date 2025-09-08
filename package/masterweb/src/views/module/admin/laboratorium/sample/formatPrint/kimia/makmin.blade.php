<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Makmin-{!! $no_LHU !!}</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <style>
        .starter-template {
            text-align: center;
        }


        table>tr>td {
            /* cell-padding: 5px !important; */
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
            margin: 5px 30px;
        }

        body {
            font-size: 12px;
        }

        .page_break {
            page-break-before: always;
        }

        .table-container {
            flex: 2;
            margin-right: 10px;
        }

        .table-container table {
            width: 60%;
            border-collapse: collapse;
            font-size: 16px;
        }

        .tembusan ol {
            padding-left: 16px;
        }
    </style>
</head>

<body style="margin:50px 10px 50px 10px; padding: 0; font-size: 10pt">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated.png') }}" width="100%">
            </td>
        </tr>
    </table>

    <div class="row batas" style="float: right; justify-content: center; width: 250px;">
        <div class="">
            <div class="justify-content-end" style="text-align: center;">
                KEPADA<br>
                Yth. <span style="font-family: 'DejaVu Sans', sans-serif !important;">
                    {{ $sample->name_customer }}
                </span>
                <br>
                  @if (isset($sample->address_customer) && $sample->address_customer !== '')
                    @php
                      $addresArray = explode(',', $sample->address_customer);
                      $kabupaten = $addresArray[count($addresArray) - 1];

                      unset($addresArray[count($addresArray) - 1]);

                      $address = implode(',', $addresArray);

                    @endphp
                    d.a {{ $address }}<br>
                    <div style="display: inline-block; vertical-align: top;">
                      Di_ <span
                        style="display: inline-block; padding-top: 24px;"><strong><u>{{ $kabupaten }}</u></strong></span>
                    </div>
                  @else
                    Di <strong>{{ $sample->kecamatan_customer }}</strong>
                  @endif
            </div>
        </div>
    </div>
    <div class="table-container">
        <table style="font-size: 10pt;">
            <tr>
                <td>No. Agenda</td>
                <td>:</td>
                <td>{!! $no_LHU !!}</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td>Pemeriksaan Kimia Makanan-Minuman</td>
            </tr>
            <tr>
                <td>Lokasi / Asal Sampel</td>
                <td>:</td>
                <td class="wrap-text">
                    @if ($sample->is_pudam == 1)
                        @if (isset($samples[0]->location_samples))
                            @php
                                $location = str_replace("\n", '<br>', $samples[0]->location_samples);
                                $location = str_replace(
                                    '<div id="simple-translate" class="simple-translate-system-theme">&nbsp;</div>',
                                    '',
                                    $location,
                                );
                                $location = str_replace('<p>', '', $location);
                                $location = str_replace('</p>', '', $location);

                            @endphp



                            {!! $location !!}
                        @else
                            {{ $sample->name_customer_pdam }}
                        @endif
                    @else
                        @php
                            $location = str_replace("\n", '<br>', $samples[0]->location_samples);

                            $location = str_replace('<p>', '', $location);
                            $location = str_replace('</p>', '', $location);

                        @endphp


                        {!! $location !!}
                    @endif
                </td>
            </tr>
            <tr>
                <td>Nomor Register</td>
                <td>:</td>
                <td>{{ $samples[0]->codesample_samples }}
                </td>
            </tr>
        </table>
    </div>

    <table width="100%" border="1" cellspacing="0" cellpadding="5"
        style="border-color: #000000; margin-top: 60px; font-size: 10pt;">
        <thead>
            <tr style="text-align: center">
                <th rowspan="2">NO LAB</th>
                <th rowspan="2">JENIS SAMPEL</th>
                <th rowspan="2">
                    <u>Tgl diterima</u><br>Tgl diperiksa
                </th>
                <th colspan="{{ count($param_methods) }}">HASIL PEMERIKSAAN</th>
            </tr>
            <tr style="text-align: center">
                @foreach ($param_methods as $param)
                    <th>{{ $param }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($laboratoriummethodsArray as $laboratoriummethods)
                <tr style="text-align: center">
                    <td>{{ $laboratoriummethods[1]['lab_num'] }}</td>
                    <td>{{ $laboratoriummethods[1]['jenis_sarana'] }}

                        @php
                            $foodType = '';
                            if ($laboratoriummethods[1]['nama_jenis_makanan'] != '') {
                                $foodType = '(' . $laboratoriummethods[1]['nama_jenis_makanan'] . ')';
                            }
                        @endphp
                        {{ $foodType }}
                    </td>
                    <td><u>{{ \Smt\Masterweb\Helpers\DateHelper::formatOnlyDate($laboratoriummethods[1]['date_sending']) }}</u><br>{{ isset($laboratoriummethods[1]['date_analytic']) ? \Smt\Masterweb\Helpers\DateHelper::formatOnlyDate($laboratoriummethods[1]['date_analytic']) : '-' }}
                    </td>
                    @foreach ($laboratoriummethods[0] as $laboratoriummethod)
                        <td style="color: {{ $laboratoriummethod->hasil == 'Positif' ? 'red' : 'black' }}">
                            {{ $laboratoriummethod->hasil }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row batas" style="float: right; justify-content: center; margin-top: 40px;">
        <div class="col-md-12">
            <div class="justify-content-end" style="text-align: center;">
                @php
                    $nullDate = '..................';
                @endphp
                Boyolali,{{ isset($validasi) ? \Smt\Masterweb\Helpers\DateHelper::formatDateIndo($validasi->stop_date) : $nullDate }}
                <br>
                Kepala Laboratorium Kesehatan<br>
                Kabupaten Boyolali<br>
                @if(isset($validasi))
                  <br>
                  <br>
                  @php
                    $petugas = "dr. Muharyati";
                    $nip = "NIP. 19721106 200212 2 001";
                  @endphp
                  @if(isset($signOption) and $signOption == 0)
                    <br>
                    <br>
                    <br>
                    <br>
                    <strong><u>{{ $petugas }}</u></strong><br>
                    Pembina<br>
                    {{ $nip }}
                  @else
                    @include("masterweb::module.admin.laboratorium.template.TTD_BSRE")
                  @endif
                @else
                  <br>
                  <br>
                  <br>
                  <br>
                  <strong><u>dr. Muharyati</u></strong><br>
                  Pembina<br>
                  NIP. 19721106 200212 2 001
                @endif
            </div>
        </div>
    </div>
    <div style="margin-top: 220px">
        <div class="tembusan">
            <p><u>Tembusan dikirim Kepada Yth:</u></p>
            {!! $tembusans !!}
        </div>
    </div>
</body>

</html>
