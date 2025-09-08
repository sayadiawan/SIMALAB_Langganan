@extends('masterweb::template.admin.layout')
@section('title')
    Edit Data Sampel
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
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />

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
                                <li class="breadcrumb-item"><a
                                        href="{{ url('/elits-samples', [$sample->permohonan_uji_id]) }}"> Edit
                                        Data
                                        Sampel</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>edit</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Edit Data Sampel Non Klinik</h4>

        </div>

        <div class="card-body">
            <form action="{{ route('elits-samples.update', [Request::segment(3)]) }}" method="POST" id="form-edit-sample">
                @csrf
                <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />

                <h5 class="card-title">Detail Sampel</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">

                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="code_permohonan_uji"> Kode Sampel:</label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="code_sample" id="code_sample"
                                                placeholder="Kode Sampel" value="{{ $sample->codesample_samples }}">

                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name_pelanggan"> Nama Pelanggan:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="name_pelanggan"
                                                id="name_pelanggan" data-type="name_pelanggan" placeholder="Nama Pelanggan"
                                                value="{{ $sample->name_pelanggan ?? $permohonan_uji->customer->name_customer }}">

                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">

                                <div class="col-lg-6">

                                    <div class="form-group">
                                        <label for="datesampling_samples">Tanggal Pengambilan</label>


                                        <input id="datesampling_samples" class="form-control" name="datesampling_samples"
                                            placeholder="--/--/--- --:--" />

                                        <script>
                                            var m = moment(new Date("{{ $sample->datesampling_samples }}")).format('DD/MM/yyyy HH:mm');
                                        </script>

                                        <script>
                                            var datesampling_samples = flatpickr("#datesampling_samples", {
                                                enableTime: true,
                                                allowInput: true,
                                                locale: "id",
                                                dateFormat: "d/m/Y H:i", // 24-hour format
                                                time_24hr: true
                                            });


                                            datesampling_samples.setDate(m, true); //  
                                            $('#datesampling_samples').inputmask("datetime", {
                                                placeholder: "dd/mm/yyyy hh:mm",

                                            });
                                        </script>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="datelab_samples">Tanggal Pengiriman</label>
                                        <input id="date_sending" class="form-control" name="date_sending"
                                            placeholder="--/--/--- --:--" />
                                        <script>
                                            var m = moment(new Date("{{ $sample->date_sending }}")).format('DD/MM/yyyy HH:mm');
                                        </script>
                                        <script>
                                            var date_sending = flatpickr("#date_sending", {
                                                enableTime: true,
                                                allowInput: true,
                                                locale: "id",
                                                dateFormat: "d/m/Y H:i", // 24-hour format
                                                time_24hr: true
                                            });




                                            date_sending.setDate(m, true); //  

                                            $('#date_sending').inputmask("datetime", {
                                                placeholder: "dd/mm/yyyy hh:mm",

                                            });
                                        </script>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="col-lg-12">
                            <div class="form-group">
                                <label for="lokasi_pengambilan">Objek (Lokasi, Makanan, Minuman, Alat Makan, dll)
                                    Pengambilan:</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control" name="lokasi_pengambilan"
                                        id="lokasi_pengambilan" placeholder="Lokasi Pengambilan"
                                        value="{{ $sample->location_samples ?? old('lokasi_pengambilan') }}">
                                </div>
                            </div>
                        </div> --}}

                        {{-- <div class="col-lg-12">
                            <div class="form-group">
                                <label for="titik_pengambilan"> Titik Pengambilan:</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control" name="titik_pengambilan"
                                        id="titik_pengambilan" placeholder="Titik Pengambilan"
                                        value="{{ $sample->titik_pengambilan ?? old('titik_pengambilan') }}">
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-lg-12">
                            <div class="form-group">


                                <label for="nama_pengambilan"> Titik Pengambilan: </label>

                                <div class="form-check form-check-flat form-check-primary">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" id="is_pudam" name="is_pudam"
                                            @if ($sample->is_pudam) checked="true" @endif type="checkbox"
                                            value="true">
                                        Nama dan Detail Titik Pengambilan
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="input-group date" id="titik_pengambilan_text"
                                    @if ($sample->is_pudam == 1 && isset($sample->is_pudam)) hidden="true" @endif>
                                    <textarea class="form-control" id="titik_pengambilan" name="titik_pengambilan" rows="3">{{ $sample->titik_pengambilan ?? old('titik_pengambilan') }}</textarea>
                                </div>
                                <div class="row" id="pdam"@if ($sample->is_pudam != 1 || !isset($sample->is_pudam)) hidden="true" @endif>
                                    <div class="col-6">
                                        <label for="nama_pengambilan">Nama Titik (Kimia): </label>

                                        <input type="text" class="form-control" name="name_customer_pdam"
                                            id="name_customer_pdam" placeholder="Nama Customer"
                                            value="{{ $sample->name_customer_pdam ?? old('name_customer_pdam') }}">
                                    </div>
                                    <div class="col-6">
                                        <label for="nama_pengambilan"> Detail Titik (Mikro): </label>

                                        <textarea class="form-control" id="address_location_pdam" name="address_location_pdam" rows="3">{{ $sample->address_location_pdam ?? old('address_location_pdam') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-lg-12 mt-2 mb-4" hidden>
                            <div class="card">
                                <div class="card-body">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="customer_samples"><span style="color: red">*</span>Jenis
                                                Sampel:</label>
                                            <select id="jenis_sampel" name="jenis_sampel"
                                                class="js-customer-basic-multiple js-states form-control" required
                                                style="width: 100%">
                                                <option value="" disabled selected> Pilih Jenis Sampel</option>
                                                @foreach ($sampletypes as $sampletype)
                                                    <option data-code="{{ $sampletype->code_sample_type }}"
                                                        value="{{ $sampletype->id_sample_type }}"
                                                        {{ $sample->typesample_samples == $sampletype->id_sample_type ? 'selected' : '' }}>
                                                        @empty($sampletype->code_sample_type)
                                                        @else
                                                            [{{ $sampletype->code_sample_type }}]
                                                        @endempty {{ $sampletype->name_sample_type }}
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="is_rectal_swab"
                                        style="{{ $sample->typesample_samples == 'ab516530-aed0-481b-ab9c-86c8ccbcabb3' ? '' : 'display: none' }}">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Jenis Kelamin</label>

                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="radio"
                                                                    class="form-check-input gender_samples"
                                                                    name="gender_samples" id="gender_samples_1"
                                                                    value="L"
                                                                    {{ $sample->gender_samples == 'L' ? 'checked' : '' }}>
                                                                Laki-Laki
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <label class="form-check-label">
                                                                <input type="radio"
                                                                    class="form-check-input gender_samples"
                                                                    name="gender_samples" id="gender_samples_2"
                                                                    value="P"
                                                                    {{ $sample->gender_samples == 'P' ? 'checked' : '' }}>
                                                                Perempuan
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label for="umur_samples">Umur</label>

                                                <input type="number" class="form-control" name="umur_samples"
                                                    id="umur_samples" placeholder="Umur.."
                                                    value="{{ $sample->umur_samples }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="jenis_makanan_id"
                                        style="{{ isset($sample->jenis_makanan_id) ? '' : 'display: none' }}">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="packet">Jenis Makanan:</label>
                                                <select id="jenis_makanan_id" name="jenis_makanan_id" readonly
                                                    class="js-customer-basic-multiple js-states form-control"
                                                    style="width: 100%">
                                                    <option value="" disabled selected> Pilih Jenis Makanan</option>
                                                    @foreach ($all_jenis_makanan as $jenis_makanan)
                                                        <option value="{{ $jenis_makanan->id_jenis_makanan }}"
                                                            {{ $sample->jenis_makanan_id == $jenis_makanan->id_jenis_makanan ? 'selected' : '' }}>
                                                            {{ $jenis_makanan->name_jenis_makanan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="is_paket" style="{{ isset($sample->packet_id) ? '' : 'display: none' }}">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <div class="form-check form-check-flat form-check-primary">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input" id=""
                                                            name="is_paket" type="checkbox" value="true"
                                                            {{ isset($sample->packet_id) ? 'checked' : '' }}>
                                                        Paket
                                                        <i class="input-helper"></i></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="packet" style="{{ isset($sample->packet_id) ? '' : 'display: none' }}">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="packet">Paket:</label>
                                                <select id="packet" name="packet"
                                                    class="js-customer-basic-multiple js-states form-control"
                                                    style="width: 100%">
                                                    <option value="" disabled selected> Pilih Jenis Sampel</option>
                                                    @foreach ($packets as $paket)
                                                        <option value="{{ $paket->id_packet }}">{{ $paket->name_packet }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="datelab_samples">Catatan Sampel</label>
                                <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="10">{{ $sample->note_samples ?? old('note') }}</textarea>
                            </div>
                        </div>

                        <div class="form-group" hidden>
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
                                    value="{{ $sample->cost_samples }}" placeholder="Isikan Harga" readonly required>
                            </div>
                        </div>
                    </li>
                </ul>

                <br>

                <h5 class="card-title">Penerimaan Sampel</h5>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="col-md-12">
                            <div class="form-group">

                                <label for="tempat_kemasan"><b>1. Tempat / Kemasan</b></label>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_tempat_kemasan" type="radio"
                                        value="layak" id="tempat_kemasan_layak"
                                        {{ $penerimaan_sample->kelayakan_tempat_kemasan == 'layak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tempat_kemasan_layak">
                                        Layak
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_tempat_kemasan" type="radio"
                                        value="tidak layak" id="tempat_kemasan_tidak_layak"
                                        {{ $penerimaan_sample->kelayakan_tempat_kemasan == 'tidak layak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tempat_kemasan_tidak_layak">
                                        Tidak Layak
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">

                                <label for="berat_vol"><b>2. Berat / Vol</b></label>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_berat_vol" type="radio"
                                        value="layak" id="berat_vol_layak"
                                        {{ $penerimaan_sample->kelayakan_berat_vol == 'layak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="berat_vol_layak">
                                        Layak
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" name="kelayakan_berat_vol" type="radio"
                                        value="tidak layak" id="berat_vol_tidak_layak"
                                        {{ $penerimaan_sample->kelayakan_berat_vol == 'tidak layak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="berat_vol_tidak_layak">
                                        Tidak Layak
                                    </label>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <br>

            </form>
            <button type="submit" id="submitAll" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
            <button type="button" class="btn btn-light" onclick="window.history.back()">Kembali</button>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var methods

        var methods_sample_type = []
        var jenis_sample = "{{ $sample->typesample_samples }}"
        var jenis_makanan

        var methods_selected = @json($methods);
        // console.log(methods_selected);

        var price_sample_type = 0;

        $(".checkbox").prop("disabled", true);
        $(".checkbox").attr("data-toggle", "tooltip");
        $(".checkbox").attr("data-placement", "right");
        $(".checkbox").attr("data-original-title", "Data Baku Mutu Belum di input");
        $("[data-toggle='tooltip']").tooltip();
        $('#cost_samples').val("{{ $sample->cost_samples }}");
        $(".checkbox").prop("readonly", false);

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
                    $(".checkbox-" + result.id_method).removeAttr("data-original-title");

                })
            },
        })

        // $(".packet").css('display', 'block');

        $('#packet').select2()

        var packet_id = "{{ $sample->packet_id }}"

        $(".checkbox").prop("checked", false);
        methods_selected.forEach(data => {
            // harga=harga+parseInt(data['price_total_method']);
            $(".checkbox-" + data['method_id']).prop("checked", true);
            $(".checkbox-" + data['method_id']).prop("readonly", true);
        })

        methods_sample_type = [];
        price_sample_type = 0;

        if (jenis_sample != null && jenis_sample != undefined) {
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
                        $(".checkbox-" + result.id_method).removeAttr("data-original-title");

                    })
                },
            })
            var url = "/api/packet/#"

            console.log(jenis_sample);
            url = url.replace('#', jenis_sample);

            $.ajax({
                url: url,
                type: "POST",
                datatype: 'json',
                success: function(response) {
                    $('#packet')
                        .find('option')
                        .remove()
                        .end()
                        .append('<option value="" disabled selected >Pilih Paket</option>');
                    var results = response.results
                    results.forEach(result => {
                        $('#packet')
                            .append('<option value="' + result.id + '" data-extra="' + result
                                .data_extra + '">' + result
                                .text + '</option>');
                    })

                    $("#packet").change(function() {
                        var packet = $(this).val();
                        var url = "{{ route('elits-sampletypes.getdetail_sample_type', '#') }}"
                        url = url.replace('#', packet);

                        var data = $("#packet option:selected").text();
                        // $("#test").val(data);

                        // console.log(data);
                        if (data.includes("Fisika")) {
                            let parsed_sample_code = $('#code_sample')
                                .val();
                            let result_fisika = parsed_sample_code.replace("- K",
                                "- F");
                            console.log(result_fisika);

                            $('#code_sample').val(result_fisika);

                        } else {
                            let parsed_sample_code = $('#code_sample')
                                .val();
                            let result_fisika = parsed_sample_code.replace("- F",
                                "- K");
                            console.log(result_fisika);

                            $('#code_sample').val(result_fisika);

                        }


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
                                        'price_total_method']);
                                    $(".checkbox-" + data['method_id']).prop(
                                        "checked", true);
                                    $(".checkbox-" + data['method_id']).prop(
                                        "readonly", true);
                                    // $(".checkbox-"+data['method_id']).prop("disabled", true);
                                })

                                if (response['price'] == 0) {
                                    $('#cost_samples').val(harga)
                                } else {
                                    $('#cost_samples').val(response['price'])

                                    price_sample_type = response['price'];
                                }


                                //   var url = "{{ route('elits-packet.index') }}";
                                //   window.location.href = url;

                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert(XMLHttpRequest.responseJSON.message);
                            }
                        });

                    })

                    $("#packet").val(packet_id)
                },
            })
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

        $(".checkbox").prop("disabled", true);

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


        $("#jenis_sampel").change(function() {
            jenis_sample = $(this).val();

            $("#is_paket").prop("checked", false);
            $(".packet").css('display', 'none');
            $(".packet").val([' ']).trigger("change");

            $(".jenis_makanan_id").css("display", "none")
            $("#jenis_makanan_id").val([' ']).trigger("change")

            $('.gender_samples').prop("checked", false);
            $('#umur_samples').val(null);

            var jenis_sample_text = $(this).children(":selected").text();




            // # 1. Initialization
            var input_sample_code = $('#code_sample')
            var code_sample_type = $(this).children(":selected").data('code') /* .toUpperCase() */ || '...';


            // # 2. Checking Sample Type
            // if (jenis_sample_text.includes('Air Minum Baktereologi')) {
            //     code_sample_type = 'AMB';
            // } else if (jenis_sample_text.includes('Air Bersih Bakteorologi')) {
            //     code_sample_type = 'ABB';
            // } else if (jenis_sample_text.includes('Air Minum Fisika')) {
            //     code_sample_type = 'AMF';
            // } else if (jenis_sample_text.includes('Air Minum Kimia')) {
            //     code_sample_type = 'AMK';
            // } else if (jenis_sample_text.includes('Air Limbah Bakteorologi')) {
            //     code_sample_type = 'ALB';
            // } else if (jenis_sample_text.includes('Alat Makan') || jenis_sample_text.includes('Usap Alat Makan')) {
            //     code_sample_type = 'ALT/AKK';
            // } else {
            //   // # Convert the string into its capital characters
            //     code_sample_type = jenis_sample_text.split(' ')
            //               .map(word => word.charAt(0))
            //               .join('')
            //               .toUpperCase();

            // }

            // # 3. Setting Sample Code
            let parsed_sample_code = input_sample_code.val().split(
                '/'); // Example: 1242/.../2024/07 => [1242, ..., 2024, 07]
            parsed_sample_code[1] = code_sample_type; // Change second code (...) into new code
            input_sample_code.val(parsed_sample_code.join('/'));






            if (jenis_sample_text.includes("Rectal Swab (Jasa Boga)") || jenis_sample ==
                "ab516530-aed0-481b-ab9c-86c8ccbcabb3" || jenis_sample_text.includes("Rectal Swab")) {
                $('.is_rectal_swab').show();
            } else {
                $('.is_rectal_swab').hide();
            }

            if (jenis_sample_text.includes("Makanan") || jenis_sample == "d34b4a50-4560-4fce-96c3-046c7080a986") {
                $(".jenis_makanan_id").css("display", "block")
                $(document).ready(function() {

                    $.fn.select2.defaults.set("theme", "classic");
                    $('#jenis_makanan_id').select2();
                    console.log("Jenis: " + jenis_makanan)
                    $('#jenis_makanan_id').change(function() {

                        jenis_makanan = $(this).val();


                        methods_sample_type = [];
                        price_sample_type = 0;
                        $(".checkbox").prop("checked", false);
                        $(".checkbox").prop("disabled", true);
                        $(".checkbox").attr("data-toggle", "tooltip");
                        $(".checkbox").attr("data-placement", "right");
                        $(".checkbox").attr("data-original-title", "Data Baku Mutu Belum di input");
                        $("[data-toggle='tooltip']").tooltip();
                        $('#cost_samples').val(0);

                        $(".checkbox").prop("readonly", false);

                        // if (jenis_sample != undefined) {

                        jenis_makanan = $(this).val();
                        var url = "{{ route('elits-sampletypes.getbaku_mutu', ['#', '%']) }}"
                        console.log("Jenis2: " + jenis_makanan)
                        console.log("Jenis2: " + url)

                        url = url.replace('#', jenis_sample);
                        url = url.replace('%', jenis_makanan);


                        $('#ispacket').val("true")

                        $.ajax({
                            url: url,
                            type: "GET",
                            datatype: 'json',
                            success: function(response) {
                                console.log(response)
                                var results = response.data;
                                results.forEach(result => {
                                    $(".checkbox-" + result.id_method).prop(
                                        "disabled", false);
                                    $(".checkbox-" + result.id_method)
                                        .removeAttr("title")
                                    $(".checkbox-" + result.id_method)
                                        .removeAttr("data-toggle");
                                    $(".checkbox-" + result.id_method)
                                        .removeAttr("data-placement");
                                    $(".checkbox-" + result.id_method)
                                        .removeAttr("data-original-title");

                                })
                            },
                        })
                        jenis_makanan = $('#jenis_makanan_id').val();
                        var url = "/api/packet/#/%"
                        url = url.replace('#', jenis_sample);
                        url = url.replace('%', jenis_makanan);



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
                                    .end()
                                    .append(
                                        '<option value="" disabled selected >Pilih Paket</option>'
                                    );
                                var results = response.results
                                results.forEach(result => {
                                    $('#packet')
                                        .append('<option value="' + result.id +
                                            '" data-extra="' + result
                                            .data_extra +
                                            '">' + result
                                            .text + '</option>');
                                })

                                $("#packet").change(function() {
                                    var packet = $(this).val();
                                    var url =
                                        "{{ route('elits-sampletypes.getdetail_sample_type', ['#']) }}"
                                    url = url.replace('#', packet);
                                    $('#ispacket').val("true")

                                    $.ajax({
                                        url: url,
                                        type: "GET",
                                        datatype: 'json',
                                        success: function(response) {


                                            methods_sample_type =
                                                response.methods;
                                            $(".checkbox").prop(
                                                "checked", false
                                            );
                                            var harga = 0;
                                            response.data.forEach(
                                                data => {
                                                    harga =
                                                        harga +
                                                        parseInt(
                                                            data[
                                                                'price_total_method'
                                                            ]
                                                        );
                                                    $(".checkbox-" +
                                                            data[
                                                                'method_id'
                                                            ]
                                                        )
                                                        .prop(
                                                            "checked",
                                                            true
                                                        );
                                                    $(".checkbox-" +
                                                            data[
                                                                'method_id'
                                                            ]
                                                        )
                                                        .prop(
                                                            "readonly",
                                                            true
                                                        );
                                                })

                                            if (response['price'] ==
                                                0) {
                                                $('#cost_samples')
                                                    .val(harga)
                                            } else {
                                                $('#cost_samples')
                                                    .val(response[
                                                        'price'
                                                    ])

                                                price_sample_type =
                                                    response[
                                                        'price'];
                                            }


                                            //   var url = "{{ route('elits-packet.index') }}";
                                            //   window.location.href = url;

                                        },
                                        error: function(XMLHttpRequest,
                                            textStatus, errorThrown
                                        ) {
                                            alert(XMLHttpRequest
                                                .responseJSON
                                                .message);
                                        }
                                    });

                                })
                            },
                        })

                        // }
                    })

                })
            } else {
                $(".jenis_makanan_id").css("display", "none")
                methods_sample_type = [];
                price_sample_type = 0;
                $(".checkbox").prop("checked", false);
                $(".checkbox").prop("disabled", true);
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
                            console.log(response)
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
                                .end()
                                .append('<option value="" disabled selected >Pilih Paket</option>');
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
            }
        });

        $("#is_paket").change(function() {
            $(".checkbox").prop("disabled", true);
            $(".checkbox").attr("data-toggle", "tooltip");
            $(".checkbox").attr("data-placement", "right");
            $(".checkbox").attr("data-original-title", "Data Baku Mutu Belum di input");
            $("[data-toggle='tooltip']").tooltip();
            $('#cost_samples').val(0);
            $(".checkbox").prop("readonly", false);

            var jenis_sample_text = $("#jenis_sampel").children(":selected").text();

            if (jenis_sample_text.includes("Makanan")) {
                jenis_makanan = $('#jenis_makanan_id').val();
                var url = "{{ route('elits-sampletypes.getbaku_mutu', ['#', '%']) }}"
                url = url.replace('#', jenis_sample);
                url = url.replace('%', jenis_makanan);

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


                        jenis_makanan = $('#jenis_makanan_id').val();
                        var url = "{{ route('elits-sampletypes.getbaku_mutu', ['#', '%']) }}"
                        url = url.replace('#', jenis_sample);
                        url = url.replace('%', jenis_makanan);

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
                        var url = "/api/packet/#/%"
                        jenis_makanan = $('#jenis_makanan_id').val();
                        url = url.replace('#', jenis_sample);
                        url = url.replace('%', jenis_makanan);

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
                                    .end()
                                    .append('<option value="" disabled selected >Pilih Paket</option>');
                                var results = response.results
                                results.forEach(result => {
                                    $('#packet')
                                        .append('<option value="' + result.id +
                                            '" data-extra="' + result.data_extra + '">' +
                                            result.text + '</option>');
                                })

                                $("#packet").change(function() {
                                    var packet = $(this).val();
                                    var url =
                                        "{{ route('elits-sampletypes.getdetail_sample_type', ['#']) }}"

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
                                                // $(".checkbox-"+data['method_id']).prop("disabled", true);
                                            })

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
            } else {
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
                                    .end()
                                    .append('<option value="" disabled selected >Pilih Paket</option>');
                                var results = response.results
                                results.forEach(result => {
                                    $('#packet')
                                        .append('<option value="' + result.id +
                                            '" data-extra="' + result.data_extra + '">' +
                                            result.text + '</option>');
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
                                                // $(".checkbox-"+data['method_id']).prop("disabled", true);
                                            })

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
            }
        })

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


        // function CheckWadah(val) {
        //     var element = document.getElementById('wadah_samples_others');

        //     if (val == '0')
        //         element.style.display = 'block';
        //     else

        //         element.style.display = 'none';
        // }

        $(document).ready(function() {
            $.fn.select2.defaults.set("theme", "classic");
            // $('#unitAttributes').select2({
            //     placeholder: "Pilih Unit",
            //     allowClear: true
            // });

            // $('.js-unit-basic-multiple').select2({
            //     placeholder: "Pilih Unit",
            //     allowClear: true,
            //     ajax: {
            //         url: "{{ url('/api/unit/') }}",
            //         method: "post",
            //         dataType: 'json',

            //         params: { // extra parameters that will be passed to ajax
            //             contentType: "application/json;",
            //         },
            //         data: function(term) {
            //             return {
            //                 term: term.term || '',
            //                 page: term.page || 1
            //             };
            //         },
            //         cache: true
            //     }
            // });

            // var element = document.getElementById('wadah_samples_others');

            // if ($('input[type=radio][name=wadah]:checked').val() == '0') {

            //     element.style.display = 'block';
            // } else {
            //     element.style.display = 'none';
            // }

            // $('input[type=radio][name=wadah]').change(function() {

            //     if (this.value == '0') {
            //         element.style.display = 'block';
            //     } else {
            //         element.style.display = 'none';
            //     }

            // });

            // var element2 = document.getElementById('pengawet_others');


            // $('input[type=radio][name=pengawet]').change(function() {
            //     if (this.value == '0') {
            //         element2.style.display = 'block';
            //     } else {
            //         element2.style.display = 'none';
            //     }

            // });


            var CSRF_TOKEN = $('#csrf-token').val();


            $('.btn-simpan').on('click', function() {
                $('#form-edit-sample').ajaxSubmit({
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

                            if (typeof(data_pesan) == 'object') {
                                jQuery.each(data_pesan, function(key, value) {
                                    console.log(value);
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
                    error: function() {
                        swal("Error!", "System gagal menyimpan!", "error");
                    }
                })
            })
        });
    </script>
@endsection
