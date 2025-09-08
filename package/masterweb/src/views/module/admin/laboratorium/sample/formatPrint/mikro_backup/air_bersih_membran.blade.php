<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>MIKRO-{!! $no_LHU !!}</title>
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
            margin: 20px 20px 20px 50px;
        }

        body {
            font-size: 12px;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body style="margin: 10px; padding: 0;">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td><img src="{{ asset('assets/admin/images/logo/kop_perusahaan.png') }}" width="730px"></td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0">
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
                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->isoFormat('D MMMM
                                                                                                                                                                Y')
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
    <table width="40%" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2">
                Yang Terhormat :
            </td>

        </tr>
        @if ($sample->permohonanuji->delegation_permohonan_uji == 1)
            <tr>
                <td colspan="2">
                    {{ $sample->permohonanuji->name_delegation_permohonan_uji }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ $sample->permohonanuji->address_delegation_permohonan_uji }}
                </td>

            </tr>
        @else
            <tr>
                <td colspan="2">
                    {{ $sample->permohonanuji->customer->name_customer }}
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    {{ $sample->permohonanuji->customer->address_customer }}
                </td>

            </tr>
        @endif
        <tr>
            <td colspan="2">
                di-
            </td>

        </tr>
        <tr>
            <td width="3%">
            </td>
            <td>
                @if (
                    $sample->permohonanuji->customer->kecamatan_customer != null &&
                        $sample->permohonanuji->customer->kecamatan_customer != '-')
                    <u>{!! strtoupper($sample->permohonanuji->customer->kecamatan_customer) !!}</u>
                @else
                    -
                @endif
            </td>
        </tr>
    </table>
    <br>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td>Disampaikan dengan hormat hasil pemeriksaan laboratorium kami adalah sebagai berikut:</td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0">
        {{-- <tr>
      <td width="30%">
        No. Lab
      </td>
      <td width="1%">
        :
      </td>
      <td>
        @if (isset($lab_string))
        {{ $lab_string }}
        @else
        - /AB-BAK/{{ getRomawi(date('m')) }}/{{ date('Y') }}
        @endif
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
                {{ (int) explode('/', $number_max->codesample_samples)[2] ==
                (int) explode('/', $number_min->codesample_samples)[2]
                    ? str_pad((int) explode('/', $number_max->codesample_samples)[2], 4, '0', STR_PAD_LEFT)
                    : str_pad((int) explode('/', $number_min->codesample_samples)[2], 4, '0', STR_PAD_LEFT) .
                        ' - ' .
                        str_pad((int) explode('/', $number_max->codesample_samples)[2], 4, '0', STR_PAD_LEFT) }}
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
                {{-- @php
        $no = 1;
        @endphp
        @foreach ($table as $mytable)
        {{ $no }}. {!! $mytable['sample_type']->name_sample_type !!} <br>
        @php
        $no++;
        @endphp
        @endforeach --}}
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
                {{ $permohonan_uji->location_permohonan_uji }}
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
                {{ $permohonan_uji->pdam_pengirim_sample }}
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

                {{ isset($diambil_min)
                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $diambil_min)->isoFormat('D MMMM Y')
                    : '-' }}
                /
                {{ isset($diambil_max)
                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $diambil_max)->isoFormat('D MMMM Y')
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
                {{ isset($checking_min)
                    ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $checking_min)->isoFormat('D MMMM Y')
                    : '-' }}
                s.d
                {{ isset($done_max) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $done_max)->isoFormat('D MMMM Y') : '-' }}
            </td>
        </tr>

        <tr>
            <td width="30%">
                Parameter pemeriksaan
            </td>
            <td width="1%">
                :
            </td>
            <td>
                @php
                    $prefix = $parameterList = '';

                    foreach ($method_all as $method) {
                        $parameterList .= $prefix . $method->name_report;
                        $prefix = ', ';
                    }

                    echo $parameterList;
                @endphp
            </td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left"><strong>Hasil pemeriksaan</strong></td>
        </tr>
    </table>

    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center; width: 10%" rowspan="2">No Sampel</td>
            <td style="text-align: center; width: 20%" rowspan="2">Jenis Sampel / Lokasi</td>
            <td style="text-align: center; width: 40%" colspan="{{ count($method_all) }}">Hasil Pemeriksaan</td>
            <td style="text-align: center; width: 15%" rowspan="2">Unit</td>
            <td style="text-align: center; width: 15%" rowspan="2">Standar Baku Mutu (kadar Maksimum)</td>
        </tr>

        <tr>
            @foreach ($method_all as $method)
                <td style="text-align: center; width: 10%">{{ $method->name_report }}</td>
            @endforeach
        </tr>

        @php
            $no = 1;
            $lastColumnPrinted = false;
        @endphp

        @foreach ($table as $mytable)
            <tr>
                <td style="text-align: center">{{ explode('/', $mytable['sample_type']->codesample_samples)[2] }}</td>
                <td style="text-align: center">{!! $mytable['sample_type']->location_samples !!}</td>

                @foreach ($mytable['result'] as $result)
                    <td style="text-align: center">{!! cek_hasil_color(
                        isset($result['hasil']) ? $result['hasil'] : (isset($result['equal']) ? $result['equal'] : ''),
                        isset($result['min']) ? $result['min'] : '',
                        $result['max'],
                        $result['equal'],
                        'result_output_method_' . $result['method_id'],
                        $result['offset_baku_mutu'],
                    ) !!}</td>
                @endforeach

                <!--Add The Line Below -->
                <?php if($lastColumnPrinted == false): $lastColumnPrinted = true;?>
                <td rowspan="{{ count($table) }}" style="text-align: center">
                    {{ $mytable['result'][0]['satuan_bakumutu'] }}
                </td>
                <td rowspan="{{ count($table) }}" style="text-align: center">
                    @foreach ($mytable['result'] as $result)
                        {{ $result['name_report'] }} :{!! $result['nilai_baku_mutu'] !!}
                        <br>
                    @endforeach

                </td>
                <?php endif; ?>
            </tr>

            @php
                $no++;
            @endphp
        @endforeach
    </table>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left">Ket: Hasil pemeriksaan dengan tanda (*) tidak memenuhi syarat baku mutu yang
                ditetapkan</td>
        </tr>
    </table>
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 10px; margin-bottom: 10px">
        <tr>
            <td style="text-align: left; width: 15%;vertical-align: text-top;">Rujukan Baku Mutu</td>
            <td style="text-align: left; width: 1%;vertical-align: text-top;">:</td>
            {{-- <td style="text-align: left; width: 75%">{{ $all_acuan_baku_mutu[0]['title_library'] }}</td> --}}
            <td style="text-align: left; width: 75%;vertical-align: text-top;">
                @if (count($all_acuan_baku_mutu) > 1)

                    @foreach ($all_acuan_baku_mutu as $all_acuan_baku_mutu)
                        - {{ $all_acuan_baku_mutu->title_library }}<br>
                    @endforeach
                @else
                    @if (count($all_acuan_baku_mutu) > 0)
                        {{ $all_acuan_baku_mutu[0]->title_library }}
                    @else
                        - Baku mutu belum diinput
                    @endif
                @endif
            </td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: left">Demikian hasil pemeriksaan ini untuk dapat digunakan seperlunya.</td>
        </tr>
    </table>

    <div class="row batas" style="float: right; justify-content: center;">
        <div class="col-md-12">
            <div class="justify-content-end" style="text-align: center;">
                Kepala UPT Laboratorium Kesehatan<br>
                Kabupaten Boyolali<br>
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
            </div>
        </div>
    </div>

</body>

</html>
