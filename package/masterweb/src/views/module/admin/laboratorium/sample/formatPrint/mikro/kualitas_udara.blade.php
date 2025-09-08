@php
    $x_baku_mutu = [
        'Peraturan Menteri Kesehatan No. 7 Tahun 2019 tentang Kesehatan Lingkungan Rumah Sakit',
        'Permenkes No. 2 Tahun 2023 tentang Peraturan Pelaksanaan Peraturan Pemerintah Nomor 66 tentang Kesehatan
Lingkungan',
    ];
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

<body style="margin: 10px; padding: 0">
    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._head_kop')
    <br>

    {{-- @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._head_data')
    <br> --}}
    <div style="float: left; max-width: 45%">
        <table>
            {{-- Nomor Agenda --}}
            <tr>
                <td style="width: 115px">
                    Nomor Agenda
                </td>
                <td>
                    :
                </td>
                <td>
                    {!! $no_LHU !!}
                </td>
            </tr>

            {{-- Nomor Register --}}
            <tr>
                <td>
                    Nomor Register
                </td>
                <td>
                    :
                </td>
                <td>
                    <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap">
                        {{-- @foreach ($table as $mytable)
              <li> {{ data_get($mytable, 'sample_type.codesample_samples') }} </li>
              @endforeach --}}
                        {{ $lab_string }}
                    </ol>
                </td>
            </tr>

            {{-- Nama Pelanggan --}}
            <tr>
                <td>
                    Nama Pelanggan
                </td>
                <td>
                    :
                </td>
                <td>
                    <b>

                        <span>

                            @php
                                $customer = str_replace(
                                    // Hanya mencari simbol 'Π'
                                    'π',
                                    '<span style="font-family: \'DejaVu Sans\', sans-serif;">π</span>', // Ganti dengan <span> yang sesuai
                                    $sample->permohonanuji->customer->name_customer,
                                );
                            @endphp
                            {{ $sample->permohonanuji->customer->name_customer }}
                        </span>

                    </b>
                </td>
            </tr>

            {{-- Alamat Register --}}
            <tr>
                <td>
                    Alamat Register
                </td>
                <td>
                    :
                </td>
                <td>
                    {{ $sample->permohonanuji->customer->address_customer }}
                </td>
            </tr>

            {{-- Jenis Sampel --}}
            <tr>
                <td>
                    Jenis Sampel
                </td>
                <td>
                    :
                </td>
                <td>
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


            <tr>
                <td colspan="3">
                    <br>
                </td>
            </tr>

            {{-- Metode Pemeriksaan --}}
            <tr>
                <td>
                    Metode Pemeriksaan
                </td>
                <td>
                    :
                </td>
                <td>
                    <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap; font-weight: bold">
                        @if ($method_all[0]->sampletype_id == '939ea9c5-f562-43d3-a7c2-75203c990a53')
                            <li>Angka Kuman</li>
                        @else
                            @foreach ($metode_pemeriksaan as $metode)
                                <li>{{ $metode }}</li>
                            @endforeach
                        @endif
                    </ol>
                </td>
            </tr>

            {{-- Hasil Pemeriksaan --}}
            <tr>
                <td>
                    Hasil Pemeriksaan
                </td>
                <td>
                    :
                </td>
                <td></td>
            </tr>
        </table>
    </div>

    <div style="float: right; max-width: 44%">
        <table style="margin-left: auto">
            {{-- Nomor Agenda --}}
            <tr>
                <td colspan="3">
                    <br>
                </td>
            </tr>

            {{-- Alamat Register --}}
            <tr>
                <td style="width: 115px">Petugas Sampling</td>
                <td>
                    :
                </td>
                <td>
                    {{ $sample->permohonanuji->name_sampling }}
                </td>
            </tr>

            {{-- Jenis Sampel --}}
            <tr>
                <td>Tanggal Sampling</td>
                <td>
                    :
                </td>
                <td>
                    {{ \Carbon\Carbon::createFromFormat('Y-m-d', head($tanggal_pengambilan ?? []))->isoFormat('D MMMM Y') }}

                </td>
            </tr>

            {{-- Jenis Sarana --}}
            <tr style="vertical-align: top">
                <td>Parameter</td>
                <td>
                    :
                </td>
                <td>
                    <ul style="list-style: none; margin: 0; padding: 0; table">
                        @foreach ($method_all as $method)
                            @if ($method->sampletype_id != 'ba2aa1af-02c7-4656-ad00-6e19d22f5eb2')
                                <li> {{ $method->name_report }} </li>
                            @endif
                        @endforeach



                        @if ($method->sampletype_id == 'ba2aa1af-02c7-4656-ad00-6e19d22f5eb2')
                            <li> Total Coliform</li>
                        @endif
                    </ul>
                </td>
            </tr>

            {{-- Metode Pemeriksaan --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- Hasil Pemeriksaan --}}
            <tr>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>

    <div class="clearfix" style="clear: both"></div>

    <br>


    {{-- @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._table_result') --}}
    <table class="result" width="100%" border="1" style="width: 100%">
        <tr>
            <td style="text-align: center;" rowspan="2"> Nomor Sampel </td>
            <td style="text-align: center;" rowspan="2"> Jenis Sampel / Lokasi </td>
            <td style="text-align: center;" colspan="{{ count($method_all) }}"> Hasil Pemeriksaan </td>
            <td style="text-align: center;" rowspan="2"> Batas Syarat </td>
            <td style="text-align: center;" rowspan="2"> Satuan </td>
        </tr>

        <tr>
            @foreach ($method_all as $method)
                <td style="text-align: center; width: 0%">{{ $method->name_report }}</td>
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

                <td class="wysiwyg-data" style="text-align: center">
                    {!! $mytable['sample_type']->location_samples !!}
                </td>

                @foreach ($mytable['result'] as $result)
                    @php
                        // dd($result->method_id);
                        $hasil = cek_hasil_color(
                            isset($result['hasil'])
                                ? $result['hasil']
                                : (isset($result['equal'])
                                    ? $result['equal']
                                    : ''),
                            $result['min'] ?? null,
                            $result['max'] ?? null,
                            isset($result['equal']) ? $result['equal'] : '',
                            'result_output_method_' . $result['method_id'] ?? null,
                            $result['offset_baku_mutu'] ?? null,
                        );
                    @endphp
                    <td style="text-align: center">{!! $hasil !!}</td>
                    <td style="text-align: center" colspan="{{ count($method_all) }}">
                        OK Kosong : 0-35 <br>
                        OK dengan aktivitas : 0-180 <br>
                        OK Ultraclean : 0-10 <br>
                        Perinatal/Perawatan : 200 - 500 <br>
                        R. Bersalin : 200 <br>
                        R.Pemulihan/perawatan : 200-500 <br>
                        R.Observasi/perawatan bayi : 200 <br>
                        R.Perawatan premature : 200 <br>
                        ICU : 200
                    </td>
                @endforeach

                <td style="text-align: center" title="$result->satuan_bakumutu">{!! $result['satuan_bakumutu'] !!}</td>
            </tr>
        @endforeach
    </table>
    <br>

    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._foot_baku_mutu')
    <br>

    @include('masterweb::module.admin.laboratorium.sample.formatPrint.mikro._foot_signature')
</body>

</html>
