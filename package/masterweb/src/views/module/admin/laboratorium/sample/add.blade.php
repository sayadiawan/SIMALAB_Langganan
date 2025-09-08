@extends('masterweb::template.admin.layout')
@section('title')
    Input Data Sampel
@endsection

@section('css')
    <style>
        * {
            margin: 0;
            padding: 0
        }

        html {
            height: 100%
        }
    </style>
@endsection

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">



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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-samples', [Request::segment(3)]) }}">
                                        Input Data
                                        Sampel</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>create</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Input Data Sampel Kesmas</h4>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-fill-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('elits-samples.store', [Request::segment(3)]) }}" method="POST" id="form-create-sample"
                enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />

                <h5 class="card-title">Detail Sampel</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">

                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="form-group" id="code_sample_kimia">
                                        <label for="code_sample_kimia"> Kode Sampel Kimia:</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="code_sample_kimia"
                                                id="input_code_sample_kimia" data-type="code_sample"
                                                data-idlabs="{{ $lab_keys['kimia'] }}" placeholder="Kode Sampel Kimia"
                                                value="{{ $code_samples['kimia'] ?? '' }}">

                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group" id="code_sample_mikro">
                                        <label for="input_code_sample_mikro"> Kode Sampel Mikro:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="code_sample_mikro"
                                                id="input_code_sample_mikro" data-type="code_sample"
                                                data-idlabs="{{ $lab_keys['mikrobiologi'] }}"
                                                placeholder="Kode Sampel Mikro"
                                                value="{{ $code_samples['mikrobiologi'] ?? '' }}">

                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="name_pelanggan"> Nama Pelanggan:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="name_pelanggan"
                                                id="name_pelanggan" data-type="name_pelanggan" placeholder="Nama Pelanggan"
                                                value="{{ old('name_pelanggan') ?? $permohonan_uji->customer->name_customer }}">

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="datesampling_samples">Tanggal Pengambilan</label>


                                        <input id="datesampling_samples" class="form-control" name="datesampling_samples"
                                            placeholder="--/--/--- --:--" type="datetime" />


                                        <!-- Sertakan Flatpickr -->

                                        <!-- Input Field -->




                                        <script>
                                            var m = moment(new Date()).format('DD/MM/yyyy HH:mm');

                                            $('#datesampling_samples').val(m);
                                        </script>

                                        <script>
                                            flatpickr("#datesampling_samples", {
                                                enableTime: true,
                                                allowInput: true,
                                                locale: "id",
                                                dateFormat: "d/m/Y H:i", // 24-hour format
                                                time_24hr: true
                                            });

                                            $('#datesampling_samples').inputmask("datetime", {
                                                placeholder: "dd/mm/yyyy hh:mm",

                                            });
                                        </script>

                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="datelab_samples">Tanggal Pengiriman</label>
                                        <input id="date_sending" class="form-control" name="date_sending"
                                            placeholder="--/--/--- --:--" type="datetime" />
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="datelab_samples">Tanggal Selesai Pengiriman</label>
                                        <input id="date_sending_stop" class="form-control" name="date_sending_stop"
                                            placeholder="--/--/--- --:--" type="datetime" />
                                    </div>
                                </div>
                                <script>
                                    var now = moment();
                                    $('#date_sending').val(now.format('DD/MM/YYYY HH:mm'));
                                    $('#date_sending_stop').val(now.add(10, 'minutes').format('DD/MM/YYYY HH:mm'));

                                    // Update date_sending_stop when date_sending changes
                                    $('#date_sending').on('input', function() {
                                        var dateSendingStr = $(this).val();
                                        var dateSending = moment(dateSendingStr, 'DD/MM/YYYY HH:mm');

                                        if (dateSending.isValid()) {
                                            var dateSendingStop = dateSending.clone().add(10, 'minutes');
                                            $('#date_sending_stop').val(dateSendingStop.format('DD/MM/YYYY HH:mm'));
                                        } else {
                                            // Optional: Handle invalid date input
                                            $('#date_sending_stop').val('');
                                        }
                                    });


                                    $('#date_sending').inputmask("datetime", {

                                        placeholder: "dd/mm/yyyy hh:mm",

                                    });

                                    $('#date_sending_stop').inputmask("datetime", {
                                        placeholder: "dd/mm/yyyy hh:mm",

                                    });
                                </script>

                                <script>
                                    flatpickr("#date_sending", {
                                        enableTime: true,
                                        allowInput: true,
                                        locale: "id",
                                        dateFormat: "d/m/Y H:i", // 24-hour format
                                        time_24hr: true
                                    });
                                    flatpickr("#date_sending_stop", {
                                        enableTime: true,
                                        allowInput: true,
                                        locale: "id",
                                        dateFormat: "d/m/Y H:i", // 24-hour format
                                        time_24hr: true
                                    });


                                    // var input_1 = document.querySelectorAll('#datesampling_samples')[0];




                                    // var input_2 = document.querySelectorAll('#date_sending')[0];


                                    // var input_3 = document.querySelectorAll('#date_sending_stop')[0];

                                    // var dateInputMask = function dateInputMask(elm) {
                                    //     elm.addEventListener('keypress', function(e) {
                                    //         if (e.keyCode < 47 || e.keyCode > 57) {
                                    //             e.preventDefault();
                                    //         }

                                    //         var len = elm.value.length;

                                    //         // If we're at a particular place, let the user type the slash
                                    //         // i.e., 12/12/1212
                                    //         if (len !== 1 || len !== 3) {
                                    //             if (e.keyCode == 47) {
                                    //                 e.preventDefault();
                                    //             }
                                    //         }

                                    //         console.log(elm.val );


                                    //         // If they don't add the slash, do it for them...
                                    //         if (len === 2) {
                                    //             elm.value += '/';
                                    //         }

                                    //         // If they don't add the slash, do it for them...
                                    //         if (len === 5) {
                                    //             elm.value += '/';
                                    //         }

                                    //         // If they don't add the slash, do it for them...
                                    //         if (len === 10) {
                                    //             elm.value += ' ';
                                    //         }

                                    //         if (len === 13) {
                                    //             elm.value += ':';
                                    //         }

                                    //         if (len > 15) {
                                    //             e.preventDefault();
                                    //         }
                                    //     });
                                    // };

                                    // dateInputMask(input_1);

                                    // dateInputMask(input_2);

                                    // dateInputMask(input_3);
                                </script>


                            </div>
                        </div>

                        {{-- <div class="col-lg-12"> --}}
                        {{-- <div class="form-group"> --}}
                        {{-- <label for="lokasi_pengambilan">Objek (Lokasi, Makanan, Minuman, Alat Makan, dll) --}}
                        {{-- Pengambilan:</label> --}}
                        {{-- <div class="input-group date"> --}}
                        {{-- <input type="text" class="form-control" name="lokasi_pengambilan" --}} {{-- id="lokasi_pengambilan"
                  placeholder="Lokasi Pengambilan" --}} {{-- value="{{ old('lokasi_pengambilan') }}"> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- </div> --}}

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="nama_pengambilan"> Titik Pengambilan: </label>

                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="is_pudam" name="is_pudam"
                                            type="checkbox" value="true" checked>
                                        Nama dan Detail Titik Pengambilan
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="input-group date" id="titik_pengambilan_text" hidden="true">
                                    <textarea class="form-control" id="titik_pengambilan" name="titik_pengambilan" rows="3">{{ old('titik_pengambilan') }}</textarea>
                                </div>
                                <div class="row" id="pdam">
                                    <div class="col-6">
                                        <label for="nama_pengambilan"> Nama Titik (Kimia): </label>

                                        <input type="text" class="form-control" name="name_customer_pdam"
                                            id="name_customer_pdam" placeholder="Nama Titik"
                                            value="{{ old('name_customer_pdam') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="nama_pengambilan"> Detail Titik (Mikro): </label>

                                        <textarea class="form-control" id="address_location_pdam" name="address_location_pdam" rows="3">{{ old('address_location_pdam') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12 mt-2 mb-4">
                            <div class="card">
                                <div class="card-body">

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="customer_samples"><span style="color: red">*</span>Jenis
                                                Sampel:</label>
                                            <select id="jenis_sampel" name="jenis_sampel"
                                                class="js-customer-basic-multiple js-states form-control"
                                                style="width: 100%">
                                                <option value="" selected> Pilih Jenis Sampel </option>

                                                @foreach ($sampletypes as $sampletype)
                                                    <option data-code="{{ $sampletype->code_sample_type }}"
                                                        value="{{ $sampletype->id_sample_type }}">
                                                        @empty($sampletype->code_sample_type)
                                                        @else
                                                            [{{ $sampletype->code_sample_type }}]
                                                        @endempty {{ $sampletype->name_sample_type }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="mt-4" id="form_jenis_makanan" style="display: none">
                                                <label for="jenis_makanan_minuman">Jenis Makanan</label>
                                                <input type="text" class="form-control" name="jenis_makanan_minuman"
                                                    id="jenis_makanan_minuman" placeholder="Jenis makanan">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="is_rectal_swab" style="display: none">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Jenis Kelamin</label>

                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input"
                                                                    name="gender_samples" id="gender_samples_1"
                                                                    value="L">
                                                                Laki-Laki
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="radio" class="form-check-input"
                                                                    name="gender_samples" id="gender_samples_2"
                                                                    value="P">
                                                                Perempuan
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="umur_samples">Umur</label>

                                                <input type="number" class="form-control" name="umur_samples"
                                                    id="umur_samples" placeholder="Umur..">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="is_paket" style="display: none">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <div class="form-check form-check-flat form-check-primary">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id="is_paket"
                                                            name="is_paket" type="checkbox" value="true">
                                                        Paket
                                                        <i class="input-helper"></i></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="jenis_makanan_id" style="display: none">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="packet">Jenis Makanan:</label>
                      <select id="jenis_makanan_id" name="jenis_makanan_id"
                        class="js-customer-basic-multiple js-states form-control" style="width: 100%">
                        <option value="" disabled selected> Pilih Jenis Makanan</option>
                        @foreach ($all_jenis_makanan as $jenis_makanan)
                        <option value="{{ $jenis_makanan->id_jenis_makanan }}">
                          {{ $jenis_makanan->name_jenis_makanan }}
                        </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                </div> --}}

                                    {{-- Jenis Sarana --}}
                                    {{-- <div class="jenis_sarana_id" style="display: none">
                  <div class="col-lg-12">
                    <div class="form-group">
                      <label for="jenis_sarana">Jenis Sarana:</label>
                      <input type="text" class="form-control" name="jenis_sarana" id="jenis_sarana"
                        placeholder="Jenis Sarana">
                    </div>
                  </div>
                </div> --}}

                                    <div class="packet" style="display: none">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="packet">Paket:</label>
                                                <select id="packet" name="packet[]"
                                                    class="js-customer-basic-multiple js-states form-control"
                                                    style="width: 100%" multiple>

                                                    {{-- @foreach ($sampletypes as $sampletype)
                        <option value="{{ $sampletype->id_sample_type }}">{{ $sampletype->name_sample_type }}
                        </option>
                        @endforeach --}}

                                                    @foreach ($packets as $paket)
                                                        <option value="{{ $paket->id_packet }}">{{ $paket->name_packet }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                  {{--Jenis sample--}}
                                  <div id="jenis_sample_uji_usap" style="display: none">
                                    <div class="col-lg-12">
                                      <div class="form-group">
                                        <select class="form-control" name="jenis_sample_uji_usap">
                                          <option value="Alat Masak">Alat Masak</option>
                                          <option value="Alat Makan">Alat Makan</option>
                                        </select>
                                      </div>
                                    </div>
                                  </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12" hidden>
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name_send_sample"> Nama Pengirim Sampel (<span
                                                style="color: red">*Nama Titik
                                                Lokasi</span>):</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" value="-"
                                                name="name_send_sample" id="name_send_sample"
                                                placeholder="Nama Pengirim Sampel" value="{{ old('name_send_sample') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="code_sample_customer"> Kode Sampel Pelanggan:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="code_sample_customer"
                                                value="-" id="code_sample_customer"
                                                placeholder="Kode Sampel Pelanggan"
                                                value="{{ old('code_sample_customer') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12" hidden>
                            <div class="form-group">
                                <label for="customer_samples"><span style="color: red">*</span>Program:</label>
                                <select id="program_samples" name="program_samples"
                                    class="js-customer-basic-multiple js-states form-control" style="width: 100%"
                                    required>
                                    <option value="" disabled selected> Pilih Program</option>
                                    <option value="{{ $programs[0]->id_program }}" selected>
                                        {{ $programs[0]->name_program }}</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id_program }}">{{ $program->name_program }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="datelab_samples">Catatan Sampel</label>
                                <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="10">{{ old('note') ?? '-' }}</textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="container method">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            @php
                                                $char = 'A';
                                            @endphp

                                            @for ($i = 0; $i < count($data_methods); $i++)
                                                @if ($i % 2 == 0 && $i != 0)
                                                    <div class="col-4">
                                                        <table>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="form-group">
                                                                        <div class="form-check">
                                                                            <h5>{{ $char }}. Parameter
                                                                                {{ $data_methods[$i]->name }} : </h5>
                                                                            @php
                                                                                $char++;
                                                                            @endphp
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            @foreach ($data_methods[$i]->method as $method)
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <div class="form-check">
                                                                                <input name="method[]"
                                                                                    class="form-check-input checkbox checkbox-{{ $method->id_method }}"
                                                                                    data-price="{{ $method->price_method }}"
                                                                                    data-idlabs="{{ $data_methods[$i]->id_lab }}"
                                                                                    data-idmethod="{{ $method->id_method }}"
                                                                                    type="checkbox"
                                                                                    value="{{ $method->id_method }}_{{ $data_methods[$i]->id_lab }}_{{ $method->price_method }}"
                                                                                    id="defaultCheck3">
                                                                                <label class="form-check-label"
                                                                                    for="defaultCheck3">
                                                                                    {{ $method->name_method }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </div>
                                        </div>

                                        <div class="row">
                                        @else
                                            <div class="col-4">
                                                <table>
                                                    <tr>
                                                        <td colspan="2">
                                                            <div class="form-group">
                                                                <div class="form-check">
                                                                    <h5>{{ $char }}. Parameter
                                                                        {{ $data_methods[$i]->name }} : </h5>
                                                                    @php
                                                                        $char++;
                                                                    @endphp
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @foreach ($data_methods[$i]->method as $method)
                                                        <tr>
                                                            <td>
                                                                <div class="form-group">
                                                                    <div class="form-check">
                                                                        <input name="method[]"
                                                                            class="form-check-input checkbox checkbox-{{ $method->id_method }}"
                                                                            data-price="{{ $method->price_method }}"
                                                                            data-idlabs="{{ $data_methods[$i]->id_lab }}"
                                                                            data-idmethod="{{ $method->id_method }}"
                                                                            type="checkbox"
                                                                            value="{{ $method->id_method }}_{{ $data_methods[$i]->id_lab }}_{{ $method->price_method }}"
                                                                            id="{{ $method->id_method }}_{{ $data_methods[$i]->id_lab }}">
                                                                        <label class="form-check-label"
                                                                            for="defaultCheck3">
                                                                            {{ $method->name_method }}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                            @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="cost_samples">Harga</label>
                            <div class="input-group">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Rp.
                                    </span>
                                </div>
                                <input type="number" class="form-control" id="cost_samples" name="cost_samples"
                                    value="0" placeholder="Isikan Harga" readonly required>
                            </div>
                        </div>
                    </li>
                </ul>

                <br>
                <h5 class="card-title"><span style="color: red">*</span>Penerimaan Sampel</h5>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="col-md-12">
                            <div class="form-group">

                                <label for="tempat_kemasan"><b>1. Tempat / Kemasan</b></label>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_tempat_kemasan" type="radio"
                                        value="layak" id="tempat_kemasan_layak">
                                    <label class="form-check-label" for="tempat_kemasan_layak">
                                        Layak
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_tempat_kemasan" type="radio"
                                        value="tidak layak" id="tempat_kemasan_tidak_layak">
                                    <label class="form-check-label" for="tempat_kemasan_tidak_layak">
                                        Tidak Layak
                                    </label>
                                </div>
                            </div>


                            <div class="form-group">

                                <label for="berat_vol"><b>2. Berat / Vol</b></label>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_berat_vol" type="radio"
                                        value="layak" id="berat_vol_layak">
                                    <label class="form-check-label" for="berat_vol_layak">
                                        Layak
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_berat_vol" type="radio"
                                        value="tidak layak" id="berat_vol_tidak_layak">
                                    <label class="form-check-label" for="berat_vol_tidak_layak">
                                        Tidak Layak
                                    </label>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <br>

                <button type="submit" id="submitAll" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
                <button type="button" class="btn btn-light" onclick="window.history.back()">Kembali</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var methods

        var methods_sample_type = []
        var jenis_sample, jenis_makanan

        var price_sample_type = 0;

        let is_multiple_labs = false;
        let select_multiple_codes = false;

        let lab_id = null;
        let lab_keys = [];
        let lab_keys_sequences = [];

        function integerToRoman(integer) {
            // Convert the integer into an integer (just to make sure)
            integer = parseInt(integer);
            let result = '';

            // Create a lookup array that contains all of the Roman numerals.
            const lookup = {
                'M': 1000,
                'CM': 900,
                'D': 500,
                'CD': 400,
                'C': 100,
                'XC': 90,
                'L': 50,
                'XL': 40,
                'X': 10,
                'IX': 9,
                'V': 5,
                'IV': 4,
                'I': 1
            };

            for (const roman in lookup) {
                // Determine the number of matches
                const value = lookup[roman];
                const matches = Math.floor(integer / value);

                // Add the same number of characters to the string
                result += roman.repeat(matches);

                // Set the integer to be the remainder of the integer and the value
                integer = integer % value;
            }

            // The Roman numeral should be built, return it
            return result;
        }



        $('#ispacket').val("false")

        $(".checkbox").change(function() {
            var total = 0;
            methods = [];
            $(".checkbox:checked").each(function() {
                var idmethod = $(this).data('idmethod');
                var foundMethod = methods.find(function(item) {
                    return item == idmethod;
                });
                if (!foundMethod) {
                    total = total + parseInt($(this).data('price'))
                    methods.push(idmethod)
                }
            });
            let difference = methods.filter(x => !methods_sample_type.includes(x));

            if (arrayContainsArray(methods, methods_sample_type)) {
                $('#ispacket').val("true")
                if (price_sample_type != 0) {
                    let difference = methods.filter(x => !methods_sample_type.includes(x));
                    var total_difference = 0;
                    $(".checkbox:checked").each(function() {

                        var idmethod = $(this).data('idmethod');
                        var foundMethod = difference.find(function(item) {
                            return item == idmethod;
                        });
                        if (foundMethod) {
                            total_difference = total_difference + $(this).data('price')
                            // total=total+parseInt($(this).data('price'))
                            // methods.push(idmethod)
                        }
                    });
                    $('#cost_samples').val(price_sample_type + total_difference)
                } else {
                    $('#cost_samples').val(total)
                }
            } else {
                $('#ispacket').val("false")
                $('#cost_samples').val(total)
            }

            checkMultipleLabs();
        });

        let arrayContainsArray = (a_array, b_array) => {


            for (let i = 0; i < b_array.length; i++) {
                if (a_array.includes(b_array[i])) {
                    let index = a_array.indexOf(b_array[i])
                    a_array.splice(index, 1)
                } else {
                    return false
                }
            }
            return true
        }

        $(".packet").css('display', 'none');



        // $code_year       $(".checkbox").prop("disabled", true);

        $("#is_paket").change(function() {

            $(".checkbox").prop("disabled", false);
            $(".checkbox").attr("data-toggle", "tooltip");
            $(".checkbox").attr("data-placement", "right");
            $(".checkbox").attr("data-original-title", "Data Baku Mutu Belum di input");
            $("[data-toggle='tooltip']").tooltip();
            $('#cost_samples').val(0);
            $(".checkbox").prop("readonly", false);

            var jenis_sample_text = $("#jenis_sampel").children(":selected").text();


            var url = "{{ route('elits-sampletypes.getbaku_mutu', '#') }}"
            url = url.replace('#', jenis_sample);



            $.ajax({
                url: url,
                type: "GET",
                datatype: 'json',
                success: function(response) {
                    var results = response.data;
                    results.forEach(result => {
                        $(".checkbox-" + result.id_method).prop("disabled", false);
                        $(".checkbox-" + result.id_method).removeAttr("title")
                        $(".checkbox-" + result.id_method).removeAttr("data-toggle");
                        $(".checkbox-" + result.id_method).removeAttr("data-placement");
                        $(".checkbox-" + result.id_method).removeAttr(
                            "data-original-title");

                    })
                },
            })

            if (this.checked) {
                $(".packet").css('display', 'block');

                $('#packet').select2()

                methods_sample_type = [];
                price_sample_type = 0;
                $(".checkbox").prop("checked", false);
                $('#cost_samples').val(0);


                if (jenis_sample != null && jenis_sample != undefined) {

                    $(".checkbox").prop("checked", false);



                    var url = "{{ route('elits-sampletypes.getbaku_mutu', '#') }}"
                    url = url.replace('#', jenis_sample);


                    $.ajax({
                        url: url,
                        type: "GET",
                        datatype: 'json',
                        success: function(response) {
                            var results = response.data;

                            results.forEach(result => {
                                $(".checkbox-" + result.id_method).prop("disabled", false);
                                $(".checkbox-" + result.id_method).removeAttr("title")
                                $(".checkbox-" + result.id_method).removeAttr(
                                    "data-toggle");
                                $(".checkbox-" + result.id_method).removeAttr(
                                    "data-placement");
                                $(".checkbox-" + result.id_method).removeAttr(
                                    "data-original-title");

                            })
                        },
                    })
                    var url = "/api/packet/#"
                    url = url.replace('#', jenis_sample);


                    $.ajax({
                        url: url,
                        type: "POST",
                        datatype: 'json',
                        success: function(response) {
                            $(".is_paket").css('display', 'block');

                            // $(".packet").css('display', 'none');
                            // $("#is_paket").prop('checked', false);
                            // $('#packet').val(null).trigger("change");
                            $('#packet')
                                .find('option')
                                .remove()
                                .end();
                            var results = response.results
                            results.forEach(result => {
                                $('#packet')
                                    .append('<option value="' + result.id +
                                        '" data-extra="' + result.data_extra + '">' +
                                        result.text + '</option>');
                            })

                            $("#packet").change(function() {



                                var packet = $(this).val();

                                var data = $("#packet option:selected").text();

                                if (data === 'ALT/AKK') {
                                  console.log("tampil jenis sample")
                                  $('#jenis_sample_uji_usap').css('display', 'block');
                                }else {
                                  $('#jenis_sample_uji_usap').css('display', 'none');
                                }

                              // $("#test").val(data);

                                // console.log(data);
                                if (data.includes("Fisika")) {
                                    let parsed_sample_code = $('#input_code_sample_kimia')
                                        .val();
                                    let result_fisika = parsed_sample_code.replace("- K",
                                        "- F");
                                    console.log(result_fisika);

                                    $('#input_code_sample_kimia').val(result_fisika);

                                } else {
                                    let parsed_sample_code = $('#input_code_sample_kimia')
                                        .val();
                                    let result_fisika = parsed_sample_code.replace("- F",
                                        "- K");
                                    console.log(result_fisika);

                                    $('#input_code_sample_kimia').val(result_fisika);

                                }
                                var url =
                                    "{{ route('elits-sampletypes.getdetail_sample_type', '#') }}"
                                url = url.replace('#', packet);


                                $('#ispacket').val("true")

                                $.ajax({
                                    url: url,
                                    type: "GET",
                                    datatype: 'json',
                                    success: function(response) {


                                        methods_sample_type = response.methods;


                                        $(".checkbox").prop("checked", false);
                                        var harga = 0;
                                        response.data.forEach(data => {
                                            harga = harga + parseInt(
                                                data[
                                                    'price_total_method'
                                                ]);
                                            $(".checkbox-" + data[
                                                'method_id']).prop(
                                                "checked", true);
                                            $(".checkbox-" + data[
                                                'method_id']).prop(
                                                "readonly", true);
                                            $(".checkbox-" + data[
                                                'method_id']).prop(
                                                "disabled", false);

                                            let current_lab_id = $(
                                                ".checkbox-" + data[
                                                    'method_id']).data(
                                                'idlabs');
                                            if (!!lab_id && lab_id !=
                                                current_lab_id) {
                                                is_multiple_labs = true;
                                            }
                                            lab_id = current_lab_id;
                                        })

                                        multipleLabs();

                                        if (response['price'] == 0) {
                                            $('#cost_samples').val(harga)
                                        } else {
                                            $('#cost_samples').val(response[
                                                'price'])

                                            price_sample_type = response[
                                                'price'];
                                        }


                                        //   var url = "{{ route('elits-packet.index') }}";
                                        //   window.location.href = url;

                                    },
                                    error: function(XMLHttpRequest, textStatus,
                                        errorThrown) {
                                        alert(XMLHttpRequest.responseJSON
                                            .message);
                                    }
                                });
                            })
                        },
                    })
                }
            } else {
                $(".packet").css('display', 'none');
                methods_sample_type = [];
                price_sample_type = 0;
                $(".checkbox").prop("checked", false);
                $('#cost_samples').val(0);
            }

        })

        function ajax_getNewSampleNumberSequence(lab_key, id_permohonan_uji, is_makanan = false) {
            let url = "{{ route('elits-samples.getNewNumberSequence', '#') }}";
            url = url.replace('#', lab_key);
            url = url + "/#";
            url = url.replace('#', id_permohonan_uji);
            url = url + "/#";
            url = url.replace('#', is_makanan);


            $.ajax({
                url: url,
                type: "GET",
                datatype: 'json',
                success: function(response) {
                    // console.log(response)
                    return response;
                    // $('#code_sample').val(response)
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(XMLHttpRequest.responseJSON.message);
                }
            })
        }

        function checkMultipleLabs() {
            let checkbockCheckeds = $(".checkbox:checked");
            lab_keys = [];

            checkbockCheckeds.each((index, element) => {
                let current_lab_id = $(element).data('idlabs');

                if (!!lab_id && lab_id != current_lab_id) {
                    is_multiple_labs = true;
                }

                lab_id = current_lab_id;
                lab_keys = [...lab_keys, lab_id];
            })

            lab_keys = [...new Set(lab_keys)];
            console.log(lab_keys);
        }

        function multipleLabs() {

            console.log("cek");
            return;
            if (lab_keys.length > 1 && $('#code_sample').prop('disabled')) {


                select_multiple_codes = true;

                // Get the lab num sequence each lab keys.
                // todo: make this more efficient
                $code_sample_type = $('#jenis_sample').children(":selected").data('code');

                console.log($code_sample_type);

                for (let lab_key of lab_keys) {

                    let lab_sequence = ajax_getNewSampleNumberSequence(lab_key, '{{ $id }}');
                    lab_keys_sequences[lab_key] = lab_sequence;
                }

                console.log(lab_sequence);

                // Disable and hide the original input element
                $('#code_sample').prop('disabled', true);
                $('#code_sample_form_group').hide();

                // Clone the form group twice
                var clone1 = $('#code_sample_form_group').clone(true, true);
                var clone2 = $('#code_sample_form_group').clone(true, true);

                // Get the sample code
                $code_sample_type = $('#jenis_sample').children(":selected").data('code');
                let parsed_sample_code = $('input#code_sample').val().split('/');

                // Modify the first clone
                let kimia_parsed_sample_code = [...parsed_sample_code];
                kimia_parsed_sample_code[0] += '.K';
                let kimia_sample_code = kimia_parsed_sample_code.join('/');
                clone1.find('label').text('Kode Sample Kimia:');
                clone1.find('input').prop('disabled', false)
                    .attr('id', 'code_sample_kimia')
                    .attr('name', 'code_sample_kimia')
                    .val(kimia_sample_code)
                clone1.show(); // Ensure it's visible

                // Modify the second clone
                let mikrobiologi_parsed_sample_code = [...parsed_sample_code];
                mikrobiologi_parsed_sample_code[0] += '.M';
                let mikrobiologi_sample_code = mikrobiologi_parsed_sample_code.join('/');
                clone2.find('label').text('Kode Sample Mikrobiologi:');
                clone2.find('input').prop('disabled', false)
                    .attr('id', 'code_sample_mikrobiologi')
                    .attr('name', 'code_sample_mikrobiologi')
                    .val(mikrobiologi_sample_code)
                clone2.show(); // Ensure it's visible


                // Append the clones to the form
                $('#code_sample_form_group').after(clone1);
                clone1.after(clone2);
            } else {
                console.log("cek2");
                select_multiple_codes = false;

                $('#code_sample').prop('disabled', false);
                $('#code_sample_form_group').show();
                $('#code_sample_form_group').nextAll().remove();
            }
        }

        function pad(n) {
            var s = "000" + n;
            return s.substr(s.length - 4);
        }

        tinymce.init({
            selector: 'textarea#titik_pengambilan',
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
        tinymce.init({
            selector: 'textarea#address_location_pdam',
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



        $("#is_pudam").change(async function() {
            // console.log($(this).val());

            if ($(this).prop("checked")) {

                console.log("yes");
                $('#titik_pengambilan_text').attr('hidden', true);
                $('#pdam').attr('hidden', false);
            } else {
                console.log("no");
                $('#titik_pengambilan_text').attr('hidden', false);
                $('#pdam').attr('hidden', true);
            }

        });

        $("#jenis_sampel").change(async function() {

            jenis_sample = $(this).val();
            var jenis_sample_text = $(this).children(":selected").text();

            let codes = ['input_code_sample_kimia', 'input_code_sample_mikro'];


            for (let code of codes) {
                let inputCodeSampleElement = $('#' + code);
                var code_sample_type = $(this).children(":selected").data('code') /* .toUpperCase() */ || '...';
                let parsed_sample_code = $(inputCodeSampleElement).val().split(
                    '/'); // Example: 1242/.../2024/07 => [1242, ..., 2024, 07]
                parsed_sample_code[1] = code_sample_type; // Change second code (...) into new code


                if (jenis_sample_text.includes("Makanan/Minuman/Lainnya")) {





                    let url = "{{ route('elits-samples.getNewNumberSequence', '#') }}";
                    url = url.replace('#', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5");
                    url = url + "/#";
                    url = url.replace('#', '{{ $id }}');
                    url = url + "/#";
                    url = url.replace('#', true);
                    $.ajax({
                        url: url,
                        type: "GET",
                        datatype: 'json',
                        success: function(response) {
                            // console.log(response)
                            parsed_sample_code[0] = pad(parseInt(response) + 1);
                            // console.log(parsed_sample_code);
                            // console.log(response);

                            $(inputCodeSampleElement).val(parsed_sample_code.join('/'));
                            // $('#code_sample').val(response)
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(XMLHttpRequest.responseJSON.message);
                        }
                    })


                }


                let xcode = code == 'input_code_sample_mikro' ? 'Bact' :
                    code == 'input_code_sample_kimia' ? 'K' : '';
                if (!parsed_sample_code.includes(xcode)) {

                    parsed_sample_code[1] += xcode;
                    $(inputCodeSampleElement).val(parsed_sample_code.join('/'));
                }

            }

            if (jenis_sample_text.includes("Rectal Swab (Jasa Boga)") || jenis_sample ==
                "ab516530-aed0-481b-ab9c-86c8ccbcabb3" || jenis_sample_text.includes("Rectal Swab")) {
                $('.is_rectal_swab').show();
            } else {
                $('.is_rectal_swab').hide();
            }

            // if (jenis_sample_text.includes("Uji Usap")) {
            //     $(".jenis_sarana_id").css("display", "block")
            // } else {
            //     $(".jenis_sarana_id").css("display", "none")
            // }

            $(".jenis_makanan_id").css("display", "none")
            methods_sample_type = [];
            price_sample_type = 0;
            $(".checkbox").prop("checked", false);
            $(".checkbox").prop("disabled", false);
            $(".checkbox").attr("data-toggle", "tooltip");
            $(".checkbox").attr("data-placement", "right");
            $(".checkbox").attr("data-original-title", "Data Baku Mutu Belum di input");
            $("[data-toggle='tooltip']").tooltip();
            $('#cost_samples').val(0);

            $(".checkbox").prop("readonly", false);

            if (jenis_sample != undefined) {

                var url = "{{ route('elits-sampletypes.getbaku_mutu', '#') }}"
                url = url.replace('#', jenis_sample);


                $('#ispacket').val("true")

                $.ajax({
                    url: url,
                    type: "GET",
                    datatype: 'json',
                    success: function(response) {
                        var results = response.data;
                        results.forEach(result => {
                            $(".checkbox-" + result.id_method).prop("disabled", false);
                            $(".checkbox-" + result.id_method).removeAttr("title")
                            $(".checkbox-" + result.id_method).removeAttr("data-toggle");
                            $(".checkbox-" + result.id_method).removeAttr("data-placement");
                            $(".checkbox-" + result.id_method).removeAttr(
                                "data-original-title");

                        })
                    },
                })

                var url = "/api/packet/#"
                url = url.replace('#', jenis_sample);


                $.ajax({
                    url: url,
                    type: "POST",
                    datatype: 'json',
                    success: function(response) {
                        $(".is_paket").css('display', 'block');
                        $(".packet").css('display', 'none');
                        $("#is_paket").prop('checked', false);
                        // $('#packet').val(null).trigger("change");
                        $('#packet')
                            .find('option')
                            .remove()
                            .end();
                        var results = response.results
                        results.forEach(result => {
                            $('#packet')
                                .append('<option value="' + result.id + '" data-extra="' +
                                    result.data_extra + '">' +
                                    result
                                    .text + '</option>');
                        })

                        $("#packet").change(function() {
                            var packet = $(this).val();
                            var url =
                                "{{ route('elits-sampletypes.getdetail_sample_type', '#') }}"
                            url = url.replace('#', packet);


                            $('#ispacket').val("true")

                            $.ajax({
                                url: url,
                                type: "GET",
                                datatype: 'json',
                                success: function(response) {


                                    methods_sample_type = response.methods;
                                    $(".checkbox").prop("checked", false);
                                    var harga = 0;
                                    response.data.forEach(data => {
                                        harga = harga + parseInt(data[
                                            'price_total_method'
                                        ]);
                                        $(".checkbox-" + data[
                                            'method_id']).prop(
                                            "checked", true);
                                        $(".checkbox-" + data[
                                            'method_id']).prop(
                                            "readonly", true);
                                    })

                                    if (response['price'] == 0) {
                                        $('#cost_samples').val(harga)
                                    } else {
                                        $('#cost_samples').val(response[
                                            'price'])

                                        price_sample_type = response['price'];
                                    }


                                    //   var url = "{{ route('elits-packet.index') }}";
                                    //   window.location.href = url;

                                },
                                error: function(XMLHttpRequest, textStatus,
                                    errorThrown) {
                                    alert(XMLHttpRequest.responseJSON.message);
                                }
                            });

                        })
                    },
                })


            }
        });

        $(document).ready(function() {
            $.fn.select2.defaults.set("theme", "classic");
            $('#jenis_sampel').select2();
        })

        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy'
        });

        $('.datepicker').datepicker('update', new Date());

        $('.datelab_samples').datepicker({
            format: 'dd/mm/yyyy'
        });

        $('.datelab_samples').datepicker('update', new Date());
        $('.datesampling_samples').datepicker('update', new Date());

        $(document).ready(function() {
            $.fn.select2.defaults.set("theme", "classic");

            $('#unitAttributes').select2({
                placeholder: "Pilih Unit",
                allowClear: true
            });

            $('.js-unit-basic-multiple').select2({
                placeholder: "Pilih Unit",
                allowClear: true,
                ajax: {
                    url: "{{ url('/api/unit/') }}",
                    method: "post",
                    dataType: 'json',
                    params: { // extra parameters that will be passed to ajax
                        contentType: "application/json;",
                    },
                    data: function(term) {
                        return {
                            term: term.term || '',
                            page: term.page || 1
                        };
                    },
                    cache: true
                }
            });

            var element2 = document.getElementById('pengawet_others');


            $('input[type=radio][name=pengawet]').change(function() {

                if (this.value == '0') {
                    element2.style.display = 'block';
                } else {
                    element2.style.display = 'none';
                }

            });

            var CSRF_TOKEN = $('#csrf-token').val();

            $("#form-create-sample").validate({
                // in 'rules' user have to specify all the constraints for respective fields
                rules: {
                    jenis_sampel: "required",
                    cost_samples: "required",
                    program_samples: "required",

                },
                // in 'messages' user have to specify message as per rules
                messages: {
                    jenis_sampel: " Masukan Jenis Sample",
                    cost_samples: " Masukan harga",
                    program_samples: " Masukkan Program",
                },
                submitHandler: function(form) {
                    $('.btn-simpan').prop("disabled", true);
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        },
                        success: function(response) {
                            if (response.status == true) {


                                swal({
                                        title: "Success!",
                                        text: response.pesan,
                                        icon: "success"
                                    })
                                    .then(function() {
                                        document.location = response.url_redirect;
                                    });
                            } else {
                                var pesan = "";
                                var data_pesan = response.pesan;
                                const wrapper = document.createElement('div');

                                $('.btn-simpan').prop("disabled", false);

                                if (typeof(data_pesan) == 'object') {
                                    jQuery.each(data_pesan, function(key, value) {
                                        pesan += value + '. <br>';
                                        wrapper.innerHTML = pesan;
                                    });

                                    swal({
                                        title: "Error!",
                                        content: wrapper,
                                        icon: "warning"
                                    });
                                } else {
                                    swal({
                                        title: "Error!",
                                        text: response.pesan,
                                        icon: "warning"
                                    });
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            $('.btn-simpan').prop("disabled", false);

                            var err = eval("(" + xhr.responseText + ")");
                            swal("Error!", err.Message, "error");
                        }
                    })
                }
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#jenis_sampel').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue === "d34b4a50-4560-4fce-96c3-046c7080a986") {
                    $('#form_jenis_makanan').show();
                    $('#jenis_makanan_minuman').val('');
                } else {
                    $('#form_jenis_makanan').hide();
                    $('#jenis_makanan_minuman').val('');
                }
            });
        });
    </script>
@endsection
