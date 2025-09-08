@extends('masterweb::template.admin.layout')
@section('title')
    Pengesahan Hasil
@endsection

@section('content')
    <style>

    </style>








    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="">
                    <div class="template-demo">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i
                                            class="fa fa-home menu-icon mr-1"></i>
                                        Beranda</a></li>

                                <li class="breadcrumb-item">
                                    <a href="{{ url('/elits-permohonan-uji') }}">
                                        Permohonan Uji</a>
                                </li>

                                <li class="breadcrumb-item">
                                    <a href="{{ url('/elits-samples', [$sample->permohonan_uji_id]) }}">
                                        Daftar Pengujian</a>
                                </li>

                                <li class="breadcrumb-item">
                                    <a
                                        href="{{ url('/elits-samples/verification-2', [Request::segment(2), Request::segment(3)]) }}">
                                        Analys</a>
                                </li>

                                <li class="breadcrumb-item active" aria-current="page"><span>Pengesahan Hasil</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <H4>Pengesahan Hasil</H4>
        </div>
        <div class="card-body">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- utama -->

                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th><b>Nama Pelanggan</b></th>
                                    <td>

                                        @php
                                            $customer = str_replace(
                                                // Hanya mencari simbol 'Π'
                                                'π',
                                                '<span style="font-family: \'DejaVu Sans\', sans-serif;">π</span>', // Ganti dengan <span> yang sesuai
                                                $sample->name_pelanggan ??
                                                    $sample->permohonanuji->customer->name_customer,
                                            );
                                        @endphp
                                    </td>
                                    <th><b>Tanggal Pengambilan</b></th>
                                    <td>
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->datesampling_samples)->isoFormat('D MMMM Y HH:mm') }}
                                    </td>
                                    <th><b>Jenis Sampel</b></th>
                                    <td>{{ $sample->name_sample_type }}{{ !isset($sample->nama_jenis_makanan) ? '' : ' - ' . $sample->nama_jenis_makanan }}
                                    </td>
                                </tr>
                                <tr>
                                    <th><b>Nomor Sampel</b></th>
                                    <td>{{ $sample->codesample_samples }}</td>
                                    <th><b>Tanggal Pengiriman</b></th>
                                    <td>
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->isoFormat('D MMMM Y HH:mm') }}
                                    </td>
                                    <th><b>Nama Pengambil</b></th>
                                    <td>{{ $sample->permohonanuji->name_sampling }}</td>
                                </tr>
                            </table>
                            <br>
                            <h5>Parameter {{ $sample->nama_laboratorium }} :</h5>
                            <div class="row">
                                @foreach ($laboratoriummethods as $index => $laboratoriummethod)
                                    <div class="col-md-3">
                                        - {{ $laboratoriummethod->params_method }} <br>
                                    </div>

                                    @if (($index + 1) % 4 == 0)
                            </div>
                            <div class="row">
                                @endif
                                @endforeach
                            </div>
                            <br>
                            <div class="col-md-12">

                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered result" width="100%">
                                            <thead>
                                                <tr>
                                                    <td width="5%"><br>No <br><br></td>

                                                    <td width="30%"><br>Parameter Pemeriksaan<br><br></td>
                                                    <td width="25%"><br>Hasil Pemeriksaan<br><br></td>
                                                    <td width="25%"><br>Batas Syarat<br><br></td>
                                                </tr>

                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                    $tidak_simpan = false;
                                                @endphp
                                                @foreach ($laboratoriummethods as $laboratoriummethod)
                                                    @if (count($laboratoriummethod['detail']) == 0)
                                                        @if (isset($laboratoriummethod->name_report))
                                                            <tr>
                                                                <td width="5%"><br>{{ $no }}<br><br></td>
                                                                <td width="30%"><br> <b>{!! $laboratoriummethod->name_report !!}
                                                                    </b><br><br></td>
                                                                @php

                                                                    $unit = $laboratoriummethod->shortname_unit;

                                                                    if (isset($unit)) {
                                                                        $unit = '';
                                                                        if (
                                                                            trim($laboratoriummethod->shortname_unit) !=
                                                                                '-' &&
                                                                            trim(
                                                                                cek_hasil_color(
                                                                                    isset($laboratoriummethod->hasil)
                                                                                        ? $laboratoriummethod->hasil
                                                                                        : (isset(
                                                                                            $laboratoriummethod->equal,
                                                                                        )
                                                                                            ? $laboratoriummethod->equal
                                                                                            : ''),
                                                                                    $laboratoriummethod->min,
                                                                                    $laboratoriummethod->max,
                                                                                    $laboratoriummethod->equal,
                                                                                    'result_output_method_' .
                                                                                        $laboratoriummethod->method_id,
                                                                                    $laboratoriummethod->offset_baku_mutu,
                                                                                ),
                                                                            ) != '-'
                                                                        ) {
                                                                            // print_r($hasil);
                                                                            $unit = $laboratoriummethod->shortname_unit;
                                                                        }
                                                                    } else {
                                                                        $unit = '';
                                                                    }
                                                                @endphp
                                                                <td width="25%">
                                                                    @if ($laboratoriummethod->nilai_baku_mutu == $unit)
                                                                        <br>{!! cek_hasil_color(
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
                                                                        ) !!}
                                                                    @else
                                                                        <br>{!! cek_hasil_color(
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
                                                                        ) !!}
                                                                        {!! $unit !!}<br><br>
                                                                    @endif
                                                                </td>
                                                                <td width="25%"><br>

                                                                    @if ($laboratoriummethod->nilai_baku_mutu == $unit)
                                                                        {!! $laboratoriummethod->nilai_baku_mutu !!}
                                                                    @else
                                                                        {!! $laboratoriummethod->nilai_baku_mutu !!} {!! $unit !!}
                                                                    @endif
                                                                    <br><br>
                                                                </td>

                                                            </tr>
                                                        @else
                                                            @php
                                                                $tidak_simpan = true;
                                                            @endphp
                                                            <tr
                                                                style="background-color: rgb(240, 19, 19); color: #fff; text-align: center">
                                                                <td>{{ $no }}</td>
                                                                @php
                                                                    $jenis_makanan = $sample->jenis_makanan;
                                                                    if (isset($jenis_makanan)) {
                                                                        $jenis_makanan =
                                                                            $jenis_makanan->name_jenis_makanan;
                                                                    }
                                                                @endphp
                                                                <td colspan="6">
                                                                    Baku mutu untuk parameter
                                                                    <b>{{ $laboratoriummethod->params_method }}</b>, untuk
                                                                    jenis sarana
                                                                    <u><b>{{ $sample->name_sample_type }}{{ !isset($jenis_makanan) ? '' : ' - ' . $jenis_makanan }}</b></u>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @else
                                                        @if (isset($laboratoriummethod->name_report))
                                                            <tr>
                                                                <td style="vertical-align:top"
                                                                    rowspan="{{ count($laboratoriummethod['detail']) + 1 }}">
                                                                    {{ $no }}</td>
                                                                <td colspan="3">
                                                                    <b>{!! $laboratoriummethod->name_report !!}</b>
                                                                </td>
                                                            </tr>
                                                            @foreach ($laboratoriummethod['detail'] as $detail)
                                                                <tr>
                                                                    <td width="30%"><br>{!! $detail->name_sample_result_detail !!}<br><br>
                                                                    </td>
                                                                    @php
                                                                        $hasil = cek_hasil_color(
                                                                            isset($detail->hasil)
                                                                                ? $detail->hasil
                                                                                : (isset(
                                                                                    $detail->equal_sample_result_detail,
                                                                                )
                                                                                    ? $detail->equal_sample_result_detail
                                                                                    : ''),
                                                                            $detail->min_sample_result_detail,
                                                                            $detail->max_sample_result_detail,
                                                                            $detail->equal_sample_result_detail,
                                                                            'result_output_method_' .
                                                                                $detail->id_sample_result_detail,
                                                                            $detail->offset_baku_mutu,
                                                                        );
                                                                        if (isset($unit)) {
                                                                            $unit = '';
                                                                            if (
                                                                                trim(
                                                                                    $laboratoriummethod->shortname_unit,
                                                                                ) != '-' &&
                                                                                trim(
                                                                                    cek_hasil_color(
                                                                                        isset($detail->hasil)
                                                                                            ? $detail->hasil
                                                                                            : (isset(
                                                                                                $detail->equal_sample_result_detail,
                                                                                            )
                                                                                                ? $detail->equal_sample_result_detail
                                                                                                : ''),
                                                                                        $detail->min_sample_result_detail,
                                                                                        $detail->max_sample_result_detail,
                                                                                        $detail->equal_sample_result_detail,
                                                                                        'result_output_method_' .
                                                                                            $detail->id_sample_result_detail,
                                                                                        $detail->offset_baku_mutu,
                                                                                    ),
                                                                                ) != '-'
                                                                            ) {
                                                                                // print_r($hasil);
                                                                                $unit =
                                                                                    $laboratoriummethod->shortname_unit;
                                                                            }
                                                                        } else {
                                                                            $unit = '';
                                                                        }
                                                                    @endphp
                                                                    <td width="25%">
                                                                        <br>{!! cek_hasil_color(
                                                                            isset($detail->hasil)
                                                                                ? $detail->hasil
                                                                                : (isset($detail->equal_sample_result_detail)
                                                                                    ? $detail->equal_sample_result_detail
                                                                                    : ''),
                                                                            $detail->min_sample_result_detail,
                                                                            $detail->max_sample_result_detail,
                                                                            $detail->equal_sample_result_detail,
                                                                            'result_output_method_' . $detail->id_sample_result_detail,
                                                                            $detail->offset_baku_mutu,
                                                                        ) !!}{!! $unit !!}<br><br>
                                                                    </td>
                                                                    <td width="25%"><br> {!! $detail->nilai_sample_result_detail !!}
                                                                        {!! $unit !!}<br><br></td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            @php
                                                                $tidak_simpan = true;
                                                            @endphp
                                                            <tr
                                                                style="background-color: rgb(240, 19, 19); color: #fff; text-align: center">
                                                                <td>{{ $no }}</td>
                                                                @php
                                                                    $jenis_makanan = $sample->jenis_makanan;
                                                                    if (isset($jenis_makanan)) {
                                                                        $jenis_makanan =
                                                                            $jenis_makanan->name_jenis_makanan;
                                                                    }
                                                                @endphp
                                                                <td colspan="6">
                                                                    Baku mutu untuk parameter
                                                                    <b>{{ $laboratoriummethod->params_method }}</b>, untuk
                                                                    jenis sarana
                                                                    <u><b>{{ $sample->name_sample_type }}{{ !isset($jenis_makanan) ? '' : ' - ' . $jenis_makanan }}</b></u>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endif
                                                    @php
                                                        $no++;
                                                    @endphp
                                                @endforeach


                                                </thead>
                                        </table>
                                        <br>
                                        <form
                                            action="{{ route('elits-pengesahan-hasil.store', [Request::segment(2), Request::segment(3), Request::segment(4)]) }}"
                                            method="POST">
                                            @csrf
                                            @php
                                                if (isset($pengesahan_hasil->pengesahan_hasil_date)) {
                                                    $pengesahan_hasil = \Carbon\Carbon::createFromFormat(
                                                        'Y-m-d H:i:s',
                                                        $pengesahan_hasil->pengesahan_hasil_date,
                                                    )->isoFormat('Y/M/D');
                                                } else {
                                                    $pengesahan_hasil = '';
                                                }
                                            @endphp
                                            @if (!$tidak_simpan)
                                                <div class="form-group" hidden>
                                                    <label for="wadah_samples"><b>Pengesahan Hasil dilakukan:</b></label>

                                                    <div class="input-group date">
                                                        <input type="text" class="form-control pengesahan_hasil"
                                                            name="pengesahan_hasil" id="pengesahan_hasil"
                                                            placeholder="Isikan Tanggal Pengesahan Hasil"
                                                            data-date-format="dd/mm/yyyy" required>
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>


                                                <!-- <div class="form-group">
                                                                                                                <label for="wadah_samples"><b>Apakah Pengesahan Hasil sudah siap?</b></label>

                                                                                                                <div class="form-check">
                                                                                                                    <input class="form-check-input" name="pengesahan_hasil" type="radio" value="ya" id="ya" >
                                                                                                                    <label class="form-check-label" for="flexCheckChecked">
                                                                                                                        Ya
                                                                                                                    </label>
                                                                                                                </div>
                                                                                                                <div class="form-check">
                                                                                                                    <input class="form-check-input" name="pengesahan_hasil" type="radio" value="tidak" id="tidak" >
                                                                                                                    <label class="form-check-label" for="flexCheckChecked">
                                                                                                                        Tidak
                                                                                                                    </label>
                                                                                                                </div>

                                                                                                            </div> -->





                                                <button type="submit" id="submitAll"
                                                    class="btn btn-primary mr-2">Simpan</button>


                                                <button type="button" class="btn btn-light"
                                                    onclick="window.history.back()">Kembali</button>
                                            @endif
                                        </form>




                                    </div>
                                </div>


                            </div>


                        </div>






                    </div>

                    <!-- utama -->
                </div>
                <!-- /.row -->
            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $('.pengesahan_hasil').datepicker({
            format: 'dd/mm/yyyy'
        });
        var tanggal
        if ("{{ $pengesahan_hasil }}" != undefined && "{{ $pengesahan_hasil }}" != "") {
            tanggal = new Date("{{ $pengesahan_hasil }}")
        } else {

            tanggal = new Date()
        }
        $('.pengesahan_hasil').datepicker('update', tanggal);
    </script>
@endsection
