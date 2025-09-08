@php
    $x_baku_mutu = ['2. Hasil analisa hanya berlaku untuk sample yang diuji', '3. Baku Mutu :'];
@endphp

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

    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._head_data')
    <br>

    <table class="result" width="100%" border="1" cellspacing="0" cellpadding="0">
        <tr>
            <td style="text-align: center; width: 0;" rowspan="3">Nomor <br> Lab.</td>
            <td style="text-align: center; width: 220px" rowspan="3">Lokasi</td>
            <td style="text-align: center; width: 0" rowspan="3">Jam Sampling</td>
            <td style="text-align: center; width: 0" colspan="{{ count($method_all) * 2 }}"> Parameter Wajib</td>
            <td style="text-align: center; width: 50px" rowspan="3">Satuan</td>
            <td style="text-align: center; width: 50px" rowspan="3">Ket</td>
        </tr>



        <tr>
            @foreach ($method_all as $method)
                <td style="text-align: center; width: 0%" colspan="2">{{ $method->name_report }}</td>
            @endforeach
        </tr>

        <tr align="center">
            @foreach (range(1, count($method_all)) as $num)
                <td style="font-size: 9pt"> Hasil <br> Pemeriksaan </td>
                <td style="font-size: 9pt"> Kadar <br> Maksimum yg <br> diperbolehkan </td>
            @endforeach
        </tr>


        @foreach ($table as $mytable)
            @php
                $loop_lab_num = data_get($lab_nums, data_get($mytable, 'sample_type.id_samples'));
            @endphp
            <tr>
                <td style="text-align: center">
                    {{ !empty($loop_lab_num) ? sprintf('%04d', (int) $loop_lab_num) : '' }}
                </td>
                <td class="wysiwyg-data" style="text-align: start">
                    {!! $mytable['sample_type']->location_samples !!}
                </td>
                <td style="text-align: center" width="20px" title="$mytable['sample_type']->datesampling_samples">
                    {{ \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        data_get($mytable, 'sample_type.datesampling_samples'),
                    )->isoFormat('HH:mm') }}
                </td>

                @foreach ($method_all as $method)
                    @foreach ($mytable['result'] as $result)
                        @if ($method->id_method == $result['method_id'])
                            @php
                                $hasil = cek_hasil_color(
                                    isset($result['hasil'])
                                        ? $result['hasil']
                                        : (isset($result['equal'])
                                            ? $result['equal']
                                            : ''),
                                    $result['min'] ?? null,
                                    $result['max'] ?? null,
                                    $result['equal'] ?? null,
                                    'result_output_method_' . $result['method_id'] ?? null,
                                    $result['offset_baku_mutu'] ?? null,
                                );

                            @endphp
                            @if ($method->name_report == 'E.Coli')
                                <td style="text-align: center" title="$result->hasil">-</td>
                                <td style="text-align: center" title="$result->max">-</td>
                            @else
                                <td style="text-align: center" title="$result->hasil">{!! $hasil !!}</td>
                                <td style="text-align: center" title="$result->max">{!! data_get($result, 'nilai_baku_mutu') !!}</td>
                            @endif
                        @endif
                    @endforeach
                @endforeach

                <td style="text-align: center" title="$result->satuan_bakumutu">{!! $result['satuan_bakumutu'] !!}</td>
                <td title="(static)"> - </td>
            </tr>
        @endforeach
    </table>

    <br>

    <p style="margin: 0; padding: 0"> Keterangan </p>
    <ul class="keterangan">
        <li>
            <span style="padding-right: 3em">1. MPN </span>
            <span> : Most Portable Number </span>
        </li>

        @foreach ($x_baku_mutu as $baku_mutu)
            <li>
                <span> {{ $baku_mutu }} </span>
            </li>
        @endforeach
        <li>
            <span>
                &nbsp;&nbsp;&nbsp;&nbsp;- Air Limbah RS mengacu Perda Jateng No.5 Tahun 2012
            </span>
        </li>
        <li>
            <span>
                &nbsp;&nbsp;&nbsp;&nbsp;- Peraturan Menteri Lingkungan Hidup dan Kehutanan Republik Indonesia Nomor P.68
                /
                Menlhk / Setjen / Kum.1 / 8 / 2016
            </span>
        </li>
        <li>
            <span>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;tentang Baku Mutu Air Limbah Domestik
            </span>
        </li>

    </ul>
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
