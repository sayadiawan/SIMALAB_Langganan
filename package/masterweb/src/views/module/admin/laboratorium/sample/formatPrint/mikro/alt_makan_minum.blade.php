<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>MIKRO-{!! $no_LHU !!}</title>
    <link rel="shortcut icon" href="">
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._head_style')
</head>

<body style="margin: 0 10px; padding: 0">
    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._head_kop')

    <br>

    <div style="float: left; width: 50%;">
        <table>
            <tr>
                <td width="40%">
                    Nomor
                </td>
                <td width="1%">
                    :
                </td>
                <td>
                    {!! $no_LHU !!}
                </td>
            </tr>

            <tr>
                <td width="0%">
                    Perihal
                </td>
                <td width="0%">
                    :
                </td>
                <td title="$laboratorium->nama_laboratorium">
                    Hasil Pemeriksaan Bakteriologi
                </td>
            </tr>

            {{-- Jenis Sampel --}}
            <tr>
                <td width="0%">
                    Jenis Sampel
                </td>
                <td width="0%">
                    :
                </td>
                <td title="$sample->name_sample_type">
                    <b>
                        @php
                            $foodType = '';
                            if ($sample->nama_jenis_makanan != '') {
                                $foodType = '(' . $sample->nama_jenis_makanan . ')';
                            }
                        @endphp
                        {{ $sample->name_sample_type }} {{ $foodType }}
                    </b>
                </td>
            </tr>

            {{-- Lokasi --}}
            <tr>
                <td width="0%">
                    Lokasi / Asal Sampel
                </td>
                <td width="0%">
                    :
                </td>
                @php
                    $locationSample = [];
                    foreach ($table as $tbl){
                      $locationSample[] = strip_tags($tbl['sample_type']->location_samples);
                    }

                    $locations = array_unique($locationSample);
                    $locations = implode(",", $locationSample);
                @endphp
                <td title="(static)"> {{ $locations }} </td>
            </tr>

            {{-- Nomor Register --}}
            <tr>
                <td width="0%">
                    Nomor Register
                </td>
                <td width="0%">
                    :
                </td>
                <td title="$no_LHU">
                    {!! $lab_string !!}
                </td>
            </tr>
        </table>
    </div>

    <div style="float: right; width: 50%">
        <table style="margin-left: auto">
            <tr>

            </tr>

            <tr>
                <td width="150px" colspan="3">
                    <table cellspacing="0" cellpadding="0" hidden>
                        <tr>
                            <td colspan="2">
                                Yth.
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>
                                    <span
                                        style="font-family: 'DejaVu Sans', sans-serif !important;  font-family: 'DejaVu Sans', sans-serif !important;">

                                        {{ $permohonan_uji->customer->name_customer }}
                                    </span>


                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {{ $permohonan_uji->customer->address_customer }}
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
                                <u>{{ $permohonan_uji->customer->kecamatan_customer }}</u>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            {{-- Jenis Sampel --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- Lokasi --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- Nomor Register --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>

        </table>
    </div>


    <div class="clearfix" style="clear:both"></div>

    <table>
        <tr>
            <td style="max-width: max-content">
            </td>
            <td style="min-width: 100%"></td>
            <td style="max-width: max-content">
            </td>
        </tr>
    </table>


    <br>

    <table class="result" width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center; width: 10%" rowspan="2">No. <br> Lab.</td>
            <td style="text-align: center; width:30%" rowspan="2">
                <u>Diterima tgl</u>
                <br>
                Diperiksa tgl
            </td>
            <td style="text-align: center; width: 30%" rowspan="2">
                Nama Sampel
            </td>

            <td style="text-align: center; width: 30%" colspan="{{ count($method_all) }}"> Hasil Pemeriksaan</td>

            <td style="text-align: center; width: 10%" rowspan="2">Satuan</td>
            <td style="text-align: center; width: 30%" rowspan="2">Ket</td>
        </tr>

        <tr align="center">

            @foreach ($method_all as $method)
                <td style="text-align: center; width: 10%">{{ $method->name_report }}</td>
            @endforeach
        </tr>

        @foreach ($table as $mytable)
            <tr>
                <td style="text-align: center">{{ $loop->iteration }}</td>
                <td style="text-align: center">
                    <u>
                        @if (data_get($mytable, 'sample_type.date_sending') !== null)
                            <span title="$mytable['sample_type']->date_sending">
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', data_get($mytable, 'sample_type.date_sending'))->isoFormat(
                                    'D MMMM Y, HH:mm',
                                ) }}
                            </span>
                        @else
                            -
                        @endif
                    </u><br>
                    @if (data_get($mytable, 'sample_type.date_analitik_sample') !== null)
                        <span title="$mytable['sample_type']->date_analitik_sample">
                            {{ \Carbon\Carbon::createFromFormat(
                                'Y-m-d H:i:s',
                                data_get($mytable, 'sample_type.date_analitik_sample'),
                            )->isoFormat('D MMMM Y, HH:mm') }}
                        </span>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center">
                    {!! $mytable['sample_type']->jenis_sarana_names !!}
                </td>

                @foreach ($mytable['result'] as $result)
                    <td style="text-align: center">
                        {!! data_get($result, 'hasil') !!}
                    </td>
                @endforeach

                <td style="text-align: center" title="$result->satuan_bakumutu">gr/ml</td>
                <td style="text-align: center" title="(static)">-</td>
            </tr>
        @endforeach
    </table>


    <br>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left">
                Keterangan :
            </td>
        </tr>
    </table>


    @if (isset($x_baku_mutu))
        <ul class="keterangan">
            @foreach ($x_baku_mutu as $baku_mutu)
                <li>
                    <span> {{ $baku_mutu }} </span>
                </li>
            @endforeach
        </ul>
    @else
        <ul class="keterangan">
            @if (count($all_acuan_baku_mutu) > 1)
                @foreach ($all_acuan_baku_mutu as $all_acuan_baku_mutu)
                    <li>
                        <span> {{ $all_acuan_baku_mutu->title_library }} </span>
                    </li>
                @endforeach
            @else
                <li>
                    <span style="padding-right: 3em"> Permenkes RI Nomor 1096/Menkes/PER/VI/2021 </span>

                </li>
                <li>
                    <span style="padding-right: 3em"> Tentang Higiene Sanitasi Jasaboga </span>

                </li>
                <li>
                    <span style="padding-right: 3em"> Batas Maksimum cemaran mikroba 0/gr (padat) 0/ml (cair)</span>

                </li>
                <li>
                    <span style="padding-right: 3em"> Metode Pemeriksaan MPN Tabung Ganda porsi 3,3,3 </span>

                </li>
                <li>
                    <span style="padding-right: 3em"> Hasil Analisa hanya berlaku untuk sampel yang diuji</span>

                </li>
            @endif
        </ul>
    @endif

    <br>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left">Demikian hasil pemeriksaan ini untuk dapat digunakan seperlunya.</td>
        </tr>
    </table>
    <br>

    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._foot_signature')
</body>

</html>
