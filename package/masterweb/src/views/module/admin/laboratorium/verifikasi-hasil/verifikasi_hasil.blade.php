@extends('masterweb::template.admin.layout')
@section('title')
    Verifikasi Hasil
@endsection

@section('content')
    <style>

    </style>





    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">


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

                                <li class="breadcrumb-item active" aria-current="page"><span>Verifikasi
                                        Hasil</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <H4>Verifikasi Hasil</H4>
        </div>
        <div class="card-body">
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <!-- utama -->

                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="vertical-align: top"><b>Nama Pelanggan</b></th>
                                    <td style="vertical-align: top">
                                        @php
                                            $customer = str_replace(
                                                // Hanya mencari simbol 'Π'
                                                'π',
                                                '<span style="font-family: \'DejaVu Sans\', sans-serif;">π</span>', // Ganti dengan <span> yang sesuai
                                                $sample->name_pelanggan ??
                                                    $sample->permohonanuji->customer->name_customer,
                                            );
                                        @endphp
                                        {{ $customer }}
                                    </td>
                                    <th><b>Tanggal Pengambilan</b></th>
                                    <td>
                                        {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->datesampling_samples)->isoFormat('D MMMM Y
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  HH:mm') }}
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
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <b>Nama Pengambil</b>
                                            <button type="button" class="btn btn-sm btn-warning rounded-circle"
                                                style="width: 40px; height: 40px; padding: 0;" data-toggle="modal"
                                                data-target="#editNamaPengambilModal" title="Edit">
                                                <i class="fa fa-edit" style="font-size: 16px;"></i>
                                            </button>
                                        </div>
                                    </th>

                                    <td>{{ $sample->permohonanuji->name_sampling }}</td>
                                </tr>
                                <tr>
                                    <th><b>Laboratorium</b></th>
                                    <td>{{ $sample->nama_laboratorium }}</td>
                                    <th colspan="4"></th>
                                </tr>
                            </table>
                            <br>

                            {{-- Note sample dengan kondisi --}}
                            @if ($sample->note_samples !== null)
                                <div class="alert alert-warning" role="alert">
                                    {{ $sample->note_samples }}
                                </div>
                            @endif



                            @if ($sample->is_pudam == 1)
                                <br>
                                <h5>Nama Pengirim : </h5> {{ $sample->name_customer_pdam }}
                                <br>
                                <br>
                                <h5>Alamat Pengirim : </h5> {!! $sample->address_location_pdam !!}
                                <br>
                            @endif

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
                            <br>
                            <br>
                            <div class="col-md-12">

                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-12">


                                            <form
                                                action="{{ route('elits-verifikasi-hasil.store', [Request::segment(2), Request::segment(3), Request::segment(4)]) }}"
                                                method="POST">
                                                @csrf



                                                <div class="form-group">

                                                    <div class="form-group mt-2">
                                                        @if ($sample->kode_laboratorium == 'MBI')
                                                            <label for="lokasi_pengambilan"><b>Lokasi Sampel:</b></label>
                                                        @else
                                                            <label for="lokasi_pengambilan"><b>Asal Contoh Air/ Lokasi
                                                                    Sampel:</b></label>
                                                        @endif

                                                        @if (isset($sample->location_samples) && $sample->location_samples != '')
                                                            @if ($sample->kode_laboratorium == 'MBI')
                                                                <textarea class="form-control" id="lokasi_pengambilan" name="lokasi_pengambilan" rows="3">{!! $sample->location_samples !!}</textarea>
                                                            @else
                                                                <div class="input-group date">
                                                                    <textarea class="form-control" id="lokasi_pengambilan_kimia" name="lokasi_pengambilan" rows="3">{!! $sample->location_samples !!}</textarea>
                                                                </div>
                                                            @endif
                                                        @else
                                                            @php
                                                                if ($sample->is_pudam) {
                                                                    if ($sample->kode_laboratorium == 'MBI') {
                                                                        $location =
                                                                            $sample->address_location_pdam ??
                                                                            old('address_location_pdam');
                                                                    } else {
                                                                        $location =
                                                                            $sample->name_customer_pdam ??
                                                                            old('name_customer_pdam');
                                                                    }
                                                                } else {
                                                                    $location =
                                                                        $sample->titik_pengambilan ??
                                                                        old('titik_pengambilan');
                                                                }
                                                            @endphp
                                                            @if ($sample->kode_laboratorium == 'MBI')
                                                                <textarea class="form-control" id="lokasi_pengambilan" name="lokasi_pengambilan" rows="3">{!! $location !!}</textarea>
                                                            @else
                                                                <div class="input-group date">
                                                                    <textarea class="form-control" id="lokasi_pengambilan_kimia" name="lokasi_pengambilan" rows="3">{!! $location !!}</textarea>
                                                                </div>
                                                            @endif
                                                        @endif

                                                    </div>

                                                    {{-- ### Jenis Sarana ### --}}
                                                    @if ($sample->kode_laboratorium == 'MBI')
                                                        <div class="form-group">
                                                            <label for="input_jenis_sarana">
                                                                Jenis Sarana:
                                                            </label>
                                                            @isset($jenis_sarana_options)
                                                                <select id="input_jenis_sarana" name="jenis_sarana"
                                                                    class="js-customer-basic-multiple js-states form-control"
                                                                    style="width: 100%">
                                                                    <option value="" @selected(empty(old('jenis_sarana')))> Pilih
                                                                        Jenis
                                                                        Sarana </option>

                                                                    @foreach ($jenis_sarana_options as $jenis_sarana)
                                                                        <option value="{{ $jenis_sarana['value'] }}"
                                                                            {{ old('jenis_sarana') ?? $sample->jenis_sarana_names === $jenis_sarana['value'] ? 'selected' : '' }}>
                                                                            {{ $jenis_sarana['value'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                <input type="text" name="jenis_sarana"
                                                                    id="input_jenis_sarana_lainnya" class="form-control"
                                                                    value="{{ old('jenis_sarana', $sample->jenis_sarana_names) }}"
                                                                    style="width: 100%; margin-top: 1em"
                                                                    placeholder="Masukkan jenis sarana..." disabled hidden>
                                                            @else
                                                                <input type="text" class="form-control" name="jenis_sarana"
                                                                    id="jenis_sarana" placeholder="Jenis Sarana"
                                                                    value="{{ old('jenis_sarana', $sample->jenis_sarana_names ?? '') }}">
                                                            @endisset
                                                        </div>
                                                    @endif
                                                </div>



                                                <div class="form-group">
                                                    <table class="table">
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th width="15%">Jenis Parameter</th>
                                                            <th width="10%">Tidak Dikosongi</th>
                                                            <th width="10%">Kadar Maksimum Yang diperbolehkan</th>
                                                            <th width="10%">Satuan</th>
                                                            <th width="15%">Hasil</th>
                                                            <th width="20">Metode</th>
                                                            <th width="20">Keterangan</th>
                                                            <th width="10%">Dianggap melewati baku mutu</th>
                                                        </tr>
                                                        @php
                                                            $no = 1;
                                                            $tidak_simpan = false;
                                                        @endphp
                                                        @foreach ($laboratoriummethods as $laboratoriummethod)
                                                            @if (count($laboratoriummethod['detail']) == 0)
                                                                @if (isset($laboratoriummethod->name_report))
                                                                    <tr>
                                                                        <td>{{ $no }}</td>
                                                                        <td>
                                                                            <b>{!! $laboratoriummethod->name_report !!}</b>
                                                                        </td>
                                                                        <td>
                                                                            <input type="checkbox"
                                                                                id="status_{{ $laboratoriummethod->method_id }}"
                                                                                value="true"
                                                                                name="status_{{ $laboratoriummethod->method_id }}"
                                                                                class="status-relay" checked>
                                                                        </td>
                                                                        <td>{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
                                                                        <td>{!! isset($laboratoriummethod->shortname_unit) ? $laboratoriummethod->shortname_unit : '-' !!}</td>
                                                                        <td>
                                                                            <span
                                                                                class="not_show_{{ $laboratoriummethod->method_id }}"
                                                                                style="display: none;">-</span>
                                                                            <div
                                                                                class="show_{{ $laboratoriummethod->method_id }}">
                                                                                <div data-html="true"
                                                                                    data-toggle="tooltip"
                                                                                    data-tip="Syarat: &#10;&#013;Apabila angka desimal (dengan koma) maka gunakan titik. ">
                                                                                    <textarea class="form-control result_method result_method_{{ $laboratoriummethod->method_id }}"
                                                                                        id="result_method_{{ $laboratoriummethod->method_id }}"
                                                                                        name="result_method_{{ $laboratoriummethod->method_id }}" data-min="{{ $laboratoriummethod->min }}"
                                                                                        data-max="{{ $laboratoriummethod->max }}" data-equal="{{ $laboratoriummethod->equal }}" placeholder="Hasil"
                                                                                        required>{{ isset($laboratoriummethod->hasil) ? rubahNilaikeForm($laboratoriummethod->hasil) : (isset($laboratoriummethod->equal) ? rubahNilaikeForm($laboratoriummethod->equal) : '') }}</textarea>
                                                                                    <br>

                                                                                    <b>Output: {!! cek_hasil_color(
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
                                                                                    ) !!}</b>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <textarea class="form-control" name="metode_{{ $laboratoriummethod->method_id }}">{{ isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method }}</textarea>
                                                                            <br><br>
                                                                        </td>
                                                                        <td>
                                                                            <textarea class="form-control" name="keterangan_{{ $laboratoriummethod->method_id }}">{{ $laboratoriummethod->keterangan }}</textarea>
                                                                            <br><br>
                                                                        </td>
                                                                        <td>
                                                                            <input type="radio"
                                                                                id="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                value="true"
                                                                                name="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                class="offset_baku_mutu"
                                                                                {{ $laboratoriummethod->offset_baku_mutu == 'true' ? 'checked' : '' }}>
                                                                            Ya<br>
                                                                            <input type="radio"
                                                                                id="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                value="false"
                                                                                name="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                class="offset_baku_mutu"
                                                                                {{ $laboratoriummethod->offset_baku_mutu == 'false' ? 'checked' : '' }}>Tidak<br>
                                                                            <input type="radio"
                                                                                id="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                value="default"
                                                                                name="offset_baku_mutu_{{ $laboratoriummethod->method_id }}"
                                                                                class="offset_baku_mutu"
                                                                                {{ $laboratoriummethod->offset_baku_mutu == 'default' || !isset($laboratoriummethod->offset_baku_mutu)
                                                                                    ? 'checked'
                                                                                    : '' }}>Default
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
                                                                        <td colspan="7">
                                                                            Baku mutu untuk parameter
                                                                            <b>{{ $laboratoriummethod->params_method }}</b>,
                                                                            untuk
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
                                                                        <td colspan="6">
                                                                            <b>{!! $laboratoriummethod->name_report !!}</b>
                                                                        </td>
                                                                    </tr>
                                                                    @foreach ($laboratoriummethod['detail'] as $detail)
                                                                        <tr>
                                                                            <td>
                                                                                {!! $detail->name_sample_result_detail !!}
                                                                            </td>
                                                                            <td>
                                                                                <input type="checkbox"
                                                                                    id="status_{{ $detail->id_sample_result_detail }}"
                                                                                    value="true"
                                                                                    name="status_{{ $detail->id_sample_result_detail }}"
                                                                                    class="status-relay" checked>
                                                                            </td>
                                                                            <td>{!! $detail->nilai_sample_result_detail !!}</td>
                                                                            <td>{!! isset($laboratoriummethod->shortname_unit) ? $laboratoriummethod->shortname_unit : '-' !!}</td>
                                                                            <td>
                                                                                <span
                                                                                    class="not_show_{{ $detail->id_sample_result_detail }}"
                                                                                    style="display: none;">-</span>
                                                                                <div
                                                                                    class="show_{{ $detail->id_sample_result_detail }}">
                                                                                    <div data-html="true"
                                                                                        data-toggle="tooltip"
                                                                                        data-tip="Syarat: &#10;&#013; Apabila angka desimal (dengan koma) maka gunakan titik. &#10;&#013;">
                                                                                        <textarea class="form-control result_method result_method_{{ $detail->id_sample_result_detail }}"
                                                                                            id="result_method_{{ $detail->id_sample_result_detail }}"
                                                                                            name="result_method_{{ $detail->id_sample_result_detail }}" data-min="{{ $detail->min_sample_result_detail }}"
                                                                                            data-max="{{ $detail->max_sample_result_detail }}" data-equal="{{ $detail->equal_sample_result_detail }}"
                                                                                            placeholder="Hasil" required>{{ isset($detail->hasil) ? rubahNilaikeForm($detail->hasil) : (isset($detail->equal_sample_result_detail) ? rubahNilaikeForm($detail->equal_sample_result_detail) : '') }}</textarea>
                                                                                        <br>
                                                                                        <b>Output:
                                                                                            {!! cek_hasil_color(
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
                                                                                            ) !!}</b>

                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <textarea class="form-control" name="keterangan_{{ $laboratoriummethod->method_id }}">{{ $laboratoriummethod->keterangan }}</textarea>
                                                                                <br><br>
                                                                            </td>
                                                                            <td>
                                                                                <input type="radio"
                                                                                    id="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    value="true"
                                                                                    name="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    class="offset_baku_mutu"
                                                                                    {{ $detail->offset_baku_mutu == 'true' ? 'checked' : '' }}>
                                                                                Ya<br>
                                                                                <input type="radio"
                                                                                    id="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    value="false"
                                                                                    name="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    class="offset_baku_mutu"
                                                                                    {{ $detail->offset_baku_mutu == 'false' ? 'checked' : '' }}>
                                                                                Tidak<br>
                                                                                <input type="radio"
                                                                                    id="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    value="default"
                                                                                    name="offset_baku_mutu_{{ $detail->id_sample_result_detail }}"
                                                                                    class="offset_baku_mutu"
                                                                                    {{ $detail->offset_baku_mutu == 'default' || !isset($detail->offset_baku_mutu) ? 'checked' : '' }}>
                                                                                Default
                                                                            </td>
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
                                                                        <td colspan="7">
                                                                            Baku mutu untuk parameter
                                                                            <b>{{ $laboratoriummethod->params_method }}</b>,
                                                                            untuk
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
                                                    </table>
                                                    {{-- <label for="wadah_samples"><b>Apakah baca hasil sudah siap?</b></label> --}}


                                                    {{-- <div class="form-check">
                          <input class="form-check-input" name="persiapan_reagen" type="radio" value="tidak" id="tidak">
                          <label class="form-check-label" for="flexCheckChecked">
                            Tidak
                          </label>
                        </div> --}}

                                                </div>
                                                @if (!$tidak_simpan)
                                                    <div class="form-group">

                                                        <label for="wadah_samples"><b> Verifikasi Hasil
                                                                dilakukan:</b></label>

                                                        @php
                                                            if (isset($verifikasi_hasil->verifikasi_hasil_date)) {
                                                                $verifikasi_hasil = \Carbon\Carbon::createFromFormat(
                                                                    'Y-m-d H:i:s',
                                                                    $verifikasi_hasil->verifikasi_hasil_date,
                                                                )->isoFormat('Y/M/D');
                                                            } else {
                                                                $verifikasi_hasil = '';
                                                            }
                                                        @endphp
                                                        <div class="input-group date" hidden>
                                                            <input type="text" class="form-control verifikasi_hasil"
                                                                name="verifikasi_hasil" id="verifikasi_hasil"
                                                                placeholder="Isikan Tanggal Verifikasi Hasil"
                                                                data-date-format="dd/mm/yyyy" required>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text">
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                </span>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input" name="baca_hasil" type="checkbox"
                                                            value="ya" id="ya" required>
                                                        <label class="form-check-label" for="flexCheckChecked">
                                                            Pengisian Hasil sudah benar dan siap disahkan.
                                                        </label>
                                                    </div>

                                                    <br>


                                                    <button type="submit" id="submitAll"
                                                        class="btn btn-primary mr-2">Simpan</button>


                                                    <button type="button" class="btn btn-light"
                                                        onclick="window.history.back()">Kembali</button>
                                                @endif
                                            </form>





                                        </div>

                                        <br>














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

    <div class="modal fade" id="editNamaPengambilModal" tabindex="-1" aria-labelledby="editNamaPengambilLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editNamaPengambilLabel">Edit Nama Pengambil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('elits-samples.update-nama-pengambil', $sample->sample_id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name_send_sample">Nama Pengambil</label>
                            <input type="text" class="form-control" id="name_send_sample" name="name_send_sample"
                                value="{{ $sample->permohonanuji->name_sampling }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button> --}}
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // saveAll
        $(".offset_baku_mutu").click(function() {
            var value_offset_baku_mutu = $(this).attr('value')

            var id = $(this).attr('id')

            id = id.substring(17, id.length);
            var value = $("#result_method_" + id).val();
            var min = $("#result_method_" + id).attr('data-min')
            min = (min != "") ? parseFloat(min) : "";
            var max = parseFloat($("#result_method_" + id).attr('data-max'));
            max = (max != "") ? parseFloat(max) : "";
            var equal = $("#result_method_" + id).attr('data-equal');


            value = toFormatHtml(value)

            if (value_offset_baku_mutu == "false") {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:black");
            } else if (value_offset_baku_mutu == "true") {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:red");
                $("#result_output_method_" + id).append("*")
            } else {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:black");
                if (min != "") {
                    if (parseFloat(value) < min) {
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }

                if (max != "") {
                    if (parseFloat(value) > max) {
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }

                if (equal != "") {
                    if (value.toUpperCase() != equal.toUpperCase()) {
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }
            }

        })
        $(".result_method").bind('input propertychange', function() {
            var id = $(this).attr('id')
            id = id.substring(14, id.length);
            var min = $(this).attr('data-min')
            min = (min != "") ? parseFloat(min) : "";
            var max = parseFloat($(this).attr('data-max'));
            max = (max != "") ? parseFloat(max) : "";
            var equal = $(this).attr('data-equal');

            var offset_baku_mutu = $('[name="offset_baku_mutu_' + id + '"]:checked').val();
            var value = this.value


            value = toFormatHtml(value)

            if (offset_baku_mutu == "false") {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:black");
            } else if (offset_baku_mutu == "true") {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:red");
                $("#result_output_method_" + id).append("*")
            } else {
                $("#result_output_method_" + id).empty()
                $("#result_output_method_" + id).append(value)
                $("#result_output_method_" + id).attr("style", "color:black");
                if (min != "") {
                    if (parseFloat(value) < min) {
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }

                if (max != "") {

                    if (parseFloat(value) > max) {
                        // console.log("#result_output_method_"+id);
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }

                if (equal != "") {
                    if (value.toUpperCase() != equal.toUpperCase()) {
                        $("#result_output_method_" + id).attr("style", "color:red");
                        $("#result_output_method_" + id).append("*")
                    }
                }
            }


        })

        var tanggal
        if ("{{ $verifikasi_hasil }}" != undefined && "{{ $verifikasi_hasil }}" != "") {
            tanggal = new Date("{{ $verifikasi_hasil }}")
        } else {

            tanggal = new Date()
        }

        $('.verifikasi_hasil').datepicker({
            format: 'dd/mm/yyyy'
        });
        $('.verifikasi_hasil').datepicker('update', tanggal);

        var laboratoriummethods = @json($laboratoriummethods);

        laboratoriummethods.forEach(laboratoriummethod => {
            $('#status_' + laboratoriummethod.method_id).change(function() {
                // console.log($(this).val())
                if ($(this).is(':checked')) {
                    $(".not_show_" + laboratoriummethod.method_id).hide();
                    $(".show_" + laboratoriummethod.method_id).show();
                    // $("#result_method_"+laboratoriummethod.method_id).val("");

                } else {
                    // $("#result_method_"+laboratoriummethod.method_id).val("-");
                    $(".show_" + laboratoriummethod.method_id).hide();
                    $(".not_show_" + laboratoriummethod.method_id).show();


                }
            })

            laboratoriummethod.detail.forEach(detail => {
                $('#status_' + detail.id_sample_result_detail).change(function() {
                    // console.log($(this).val())
                    if ($(this).is(':checked')) {
                        $(".not_show_" + detail.id_sample_result_detail).hide();
                        $(".show_" + detail.id_sample_result_detail).show();
                        // $("#result_method_"+laboratoriummethod.method_id).val("");

                    } else {
                        // $("#result_method_"+laboratoriummethod.method_id).val("-");
                        $(".show_" + detail.id_sample_result_detail).hide();
                        $(".not_show_" + detail.id_sample_result_detail).show();


                    }
                })
            })
        })



        $(document).ready(function() {
            @if ($sample->kode_laboratorium === 'MBI')
                tinymce.init({
                    selector: 'textarea#lokasi_pengambilan',
                    height: 50,
                    menubar: false,
                    plugins: [
                        'advlist autolink autosave lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount',
                    ],
                    toolbar: 'undo redo | bold italic | removeformat | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                    setup: function(editor) {
                        editor.on('change blur', function() {
                            tinymce.triggerSave();
                        });
                    }
                });
            @else
                tinymce.init({
                    selector: 'textarea#lokasi_pengambilan_kimia',
                    height: 50,
                    menubar: false,
                    plugins: [
                        'advlist autolink autosave lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen',
                        'insertdatetime media table paste code help wordcount',
                    ],
                    toolbar: 'undo redo | bold italic | removeformat | help',
                    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                    setup: function(editor) {
                        editor.on('change blur', function() {
                            tinymce.triggerSave();
                        });
                    }
                });
            @endif

        });

        function toFormatHtml(value) {
            value = value.replaceAll('^(', "<sup>");
            value = value.replaceAll(')', "</sup>");
            value = value.replaceAll("<=", '&#8804;');
            value = value.replaceAll(">=", '&#8805;');
            value = value.replaceAll("<", '&#60;');
            value = value.replaceAll(">", '&#62;');
            // console.log(value)
            return value;
            // let result = value.indexOf("^");
            // console.log(value.substring(result+1,value.length))
        }
    </script>
@endsection
