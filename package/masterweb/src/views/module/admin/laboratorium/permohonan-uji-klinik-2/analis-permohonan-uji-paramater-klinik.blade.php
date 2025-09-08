@extends('masterweb::template.admin.layout')
@section('title')
    Permohonan Uji Klinik
@endsection


@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> --}}
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="//cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>

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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji-klinik-2') }}">Permohonan
                                        Uji Klinik
                                        Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>analis permohonan uji paket
                                        klinik</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form
        action="{{ route('elits-permohonan-uji-klinik-2.store-permohonan-uji-analis2', $item_permohonan_uji_klinik->id_permohonan_uji_klinik) }}"
        method="POST" enctype="multipart/form-data" id="form">
        {{-- <form action=""> --}}

        @csrf
        @method('PUT')

        <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
        <div class="card">
            <div class="card-header">
                <h4>Analis Permohonan Uji Paket Klinik
                </h4>
            </div>

            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="250px">No. Register</th>
                                    <td>{{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. Rekam Medis</th>
                                    <td>
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $item_permohonan_uji_klinik->pasien->tgllahir_pasien)->format('dmY') . str_pad((int) $item_permohonan_uji_klinik->pasien->no_rekammedis_pasien, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Tgl. Register</th>
                                    <td>{{ $tgl_register_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Nama Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->nama_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Usia</th>
                                    <td>
                                        {{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik .
                                            ' tahun ' .
                                            $item_permohonan_uji_klinik->umurbulan_pasien_permohonan_uji_klinik .
                                            ' bulan ' .
                                            $item_permohonan_uji_klinik->umurhari_pasien_permohonan_uji_klinik .
                                            ' hari' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Jenis Kelamin</th>
                                    <td>
                                        {{ $item_permohonan_uji_klinik->pasien->gender_pasien == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Alamat Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->alamat_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. Telepon</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->phone_pasien }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="250px">No. Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. KTP</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->nik_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Tanggal Lahir</th>
                                    <td>
                                        {{ isset($item_permohonan_uji_klinik->pasien->tgllahir_pasien)
                                            ? \Carbon\Carbon::createFromFormat('Y-m-d', $item_permohonan_uji_klinik->pasien->tgllahir_pasien)->isoFormat(
                                                'D MMMM Y',
                                            )
                                            : '' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Pengirim</th>
                                    <td>{{ $item_permohonan_uji_klinik->namapengirim_permohonan_uji_klinik }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 p-4">
                        @if ($data_verifikasi_analitik)
                            <div class="form-group">
                                <label for="tglpengujian_permohonan_uji_klinik">Tanggal Pengujian</label>

                                <input type="text" class="form-control" name="tglpengujian_permohonan_uji_klinik"
                                    id="tglpengujian_permohonan_uji_klinik" placeholder="--/--/--- --:--"
                                    value="{{ \Carbon\Carbon::parse($tgl_pengujian)->format('d/m/Y H:i') ?? old('tglpengujian_permohonan_uji_klinik') }}"
                                    readonly>

                                {{-- <script>
                                var date = "{{ $tgl_pengujian }}";
                                var m = moment(Date.parse(date)).format('DD/MM/yyyy HH:mm');

                                $('#tglpengujian_permohonan_uji_klinik').datetimepicker({
                                    format: 'dd/mm/yyyy HH:MM',
                                    value: m,
                                    footer: true,
                                    modal: true
                                });
                            </script> --}}
                            </div>
                        @else
                            <div class="form-group">
                                <label for="tglpengujian_permohonan_uji_klinik">Tanggal Pengujian</label>

                                <input type="text" class="form-control" name="tglpengujian_permohonan_uji_klinik"
                                    id="tglpengujian_permohonan_uji_klinik" placeholder="--/--/--- --:--"
                                    value="{{ $tgl_pengujian ?? old('tglpengujian_permohonan_uji_klinik') }}">

                                <script>
                                    var date = "{{ $tgl_pengujian }}";
                                    var m = moment(Date.parse(date)).format('DD/MM/yyyy HH:mm');

                                    $('#tglpengujian_permohonan_uji_klinik').datetimepicker({
                                        format: 'dd/mm/yyyy HH:MM',
                                        value: m,
                                        footer: true,
                                        modal: true
                                    });
                                </script>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="analis_permohonan_uji_klinik">Analis<span style="color: red">*</span></label>
                            <select class="form-control" id="analis_permohonan_uji_klinik"
                                name="name_analis_permohonan_uji_klinik" required>
                                <option value="" selected disabled>Pilih Analis</option>
                                <option value="HEWI YULIYATI" {{ $nama_analis == 'HEWI YULIYATI' ? 'selected' : '' }}>Hewi
                                    Yuliyati</option>
                                <option value="SRI PARTININGSIH"
                                    {{ $nama_analis == 'SRI PARTININGSIH' ? 'selected' : '' }}>Sri Partiningsih</option>
                                <option value="DESSI WIDIASTUTI"
                                    {{ $nama_analis == 'DESSI WIDIASTUTI' ? 'selected' : '' }}>Dessi Widiastuti</option>
                            </select>
                        </div>


                        {{-- <div class="form-group">
                            <label for="tgl_sampling_permohonan_uji_klinik">Tanggal Sampling<span style="color: red">*</span></label>
                            <input type="datetime-local" class="form-control" name="tgl_sampling_permohonan_uji_klinik"
                                id="tgl_sampling_permohonan_uji_klinik" placeholder="Masukkan nama analis"
                                value="">
                        </div> --}}

                        {{-- <div class="form-group">
                            <label for="plebotomist_permohonan_uji_klinik">Plebotomist<span style="color: red">*</span></label>
                            <input type="text" class="form-control" name="plebotomist_permohonan_uji_klinik"
                                id="plebotomist_permohonan_uji_klinik" placeholder="Masukkan plebotomist"
                                value="{{ $plebotomist ? $plebotomist : old('plebotomist_permohonan_uji_klinik')}}">
                        </div>

                        <div class="form-group">
                            <label for="plebotomist_permohonan_uji_klinik">Vena Lengan Kanan/Kiri<span style="color: red">*</span></label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($vena == 'R')
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_1" value="R" checked>
                                            Kanan
                                        <i class="input-helper"></i>
                                    @else
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_1" value="R">
                                            Kanan
                                        <i class="input-helper"></i>
                                    @endif

                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($vena == 'L')
                                    <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                        id="vena_permohonan_uji_klinik_2" value="L" checked>
                                        Kiri
                                    <i class="input-helper"></i>
                                    @else
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_2" value="L">
                                            Kiri
                                        <i class="input-helper"></i>
                                    @endif

                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lokasi_lain_permohonan_uji_klinik">Lokasi Lain<span style="color: red">*</span></label>
                            <input type="text" class="form-control" name="lokasi_lain_permohonan_uji_klinik"
                                id="lokasi_lain_permohonan_uji_klinik" placeholder="Masukkan lokasi lain" value="{{ $lokasi_lain ? $lokasi_lain : old('lokasi_lain_permohonan_uji_klinik')}}">
                        </div>

                        <div class="form-group">
                            <label for="penanganan_hasil_permohonan_uji_klinik">Penanganan Hasil<span style="color: red">*</span></label>
                            <select class="form-control" name="penanganan_hasil_permohonan_uji_klinik" id="penanganan_hasil_permohonan_uji_klinik">
                                <option value="" {{ is_null($penanganan_hasil) ? 'selected' : '' }}></option>
                                @foreach (['Diambil Pasien', 'Diambil Dokter', 'Dikirim ke Pasien', 'Via Internet'] as $option)
                                    <option value="{{ $option }}" {{ $penanganan_hasil == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function() {
                                    $("#penanganan_hasil_permohonan_uji_klinik").select2({
                                        placeholder: 'Pilih penanganan hasil',
                                        theme: 'bootstrap4',
                                        allowClear: true
                                    });

                                    // Check if there is a selected value
                                    let selectedValue = "{{ $penanganan_hasil ?? old('penanganan_hasil_permohonan_uji_klinik') }}";
                                    if (selectedValue) {
                                        $("#penanganan_hasil_permohonan_uji_klinik").val(selectedValue).trigger('change');
                                    } else {
                                        $("#penanganan_hasil_permohonan_uji_klinik").val(null).trigger('change');
                                    }
                                });
                            </script>
                        </div>

                        <div class="form-group">
                            <label for="catatan_permohonan_uji_klinik">Catatan</label>
                            <textarea class="form-control" name="catatan_permohonan_uji_klinik" id="catatan_permohonan_uji_klinik"
                                rows="3" placeholder="Isikan catatan pasien">{{ old('catatan_permohonan_uji_klinik') }}</textarea>
                            <script>
                                $(document).ready(function() {
                                    CKEDITOR.replace('catatan_permohonan_uji_klinik');
                                });
                            </script>
                        </div>

                        <div class="form-group">
                            <label for="riwayat_obat_permohonan_uji_klinik">Rwayat Penggunaan Obat</label>
                            <textarea class="form-control" name="riwayat_obat_permohonan_uji_klinik" id="riwayat_obat_permohonan_uji_klinik"
                                rows="3" placeholder="Isikan riwayat penggunaan obat pasien">{{ old('riwayat_obat_permohonan_uji_klinik') }}</textarea>
                            <script>
                                $(document).ready(function() {
                                    CKEDITOR.replace('riwayat_obat_permohonan_uji_klinik');
                                });
                            </script>
                        </div>  --}}
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="table-parameter" class="table">
                        <thead>
                            <tr>
                                <th style="width: 20%">Nama Test</th>
                                <th style="width: 15%">Hasil</th>
                                <th style="width: 15%">Satuan</th>
                                <th style="width: 15%">Nilai Rujukan</th>
                                {{-- <th style="width: 15%">Flag</th> --}}
                                <th style="width: 15%">Metode</th>
                                <th style="width: 20%">Keterangan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @php
                                $no = 0;
                            @endphp



                            @foreach ($arr_permohonan_parameter as $key_parameter_jenis_klinik => $item_parameter_jenis_klinik)
                                <tr>
                                    <th colspan="6">
                                        <strong>{{ $item_parameter_jenis_klinik['name_parameter_jenis_klinik'] }}</strong>
                                    </th>
                                </tr>
                                @foreach ($item_parameter_jenis_klinik['item_permohonan_parameter_satuan'] as $key_satuan_klinik => $item_satuan_klinik)
                                    @if (count($item_satuan_klinik['data_permohonan_uji_subsatuan_klinik']) > 0)
                                        <tr>
                                            <td colspan="6">
                                                -{{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}


                                                <input type="hidden"
                                                    name="permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="permohonan_uji_parameter_klinik_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}"
                                                    readonly>
                                            </td>
                                        </tr>

                                        @php
                                            $no_sub = 0;
                                        @endphp

                                        {{-- melakukan mapping data permohonan uji parameter satuan yang memiliki permohonan uji parametersubsatuan --}}
                                        @foreach ($item_satuan_klinik['data_permohonan_uji_subsatuan_klinik'] as $key_subsatuan_klinik => $item_subsatuan_klinik)
                                            <tr>
                                                <td style="width: 20%">
                                                    <p style="padding-left: 30px">
                                                        {{ $item_subsatuan_klinik['nama_parameter_sub_satuan_klinik_id'] }}
                                                        ~</p>

                                                    <input type="hidden"
                                                        name="parameter_sub_satuan_klinik_id[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="parameter_sub_satuan_klinik_id_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}"
                                                        readonly>
                                                </td>

                                                <td style="width: 15%">
                                                    <input type="text" class="form-control"
                                                        name="hasil_permohonan_uji_sub_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="hasil_permohonan_uji_sub_parameter_klinik_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['hasil_permohonan_uji_sub_parameter_klinik'] ?? old('hasil_permohonan_uji_sub_parameter_klinik') }}"
                                                        onkeydown="GetHasilSubParameter({{ $no_sub }})"
                                                        onkeyup="GetHasilSubParameter({{ $no_sub }})">
                                                </td>

                                                <td style="width: 15%">
                                                    @if ($item_subsatuan_klinik['satuan_permohonan_uji_sub_parameter_klinik'] != null)
                                                        <select class="form-control satuan_permohonan_uji_parameter_klinik"
                                                            name="satuan_permohonan_uji_sub_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                            id="satuan_permohonan_uji_sub_parameter_klinik_{{ $no_sub }}">

                                                            <option
                                                                value="{{ $item_subsatuan_klinik['satuan_permohonan_uji_sub_parameter_klinik'] }}"
                                                                selected>
                                                                {{ $item_subsatuan_klinik['nama_satuan_permohonan_uji_sub_parameter_klinik'] }}
                                                            </option>
                                                        </select>
                                                    @else
                                                        <select class="form-control satuan_permohonan_uji_parameter_klinik"
                                                            name="satuan_permohonan_uji_sub_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                            id="satuan_permohonan_uji_sub_parameter_klinik_{{ $no_sub }}">

                                                            <option value=""></option>
                                                        </select>
                                                    @endif
                                                </td>

                                                <td style="width: 15%">
                                                    <input type="text" class="form-control"
                                                        name="baku_mutu_permohonan_uji_sub_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="baku_mutu_permohonan_uji_sub_parameter_klinik_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['nilai_baku_mutu_detail_parameter_klinik'] }}"
                                                        readonly>

                                                    <input type="hidden" class="form-control"
                                                        name="min_baku_mutu_detail_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="min_baku_mutu_detail_parameter_klinik_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['min_baku_mutu_detail_parameter_klinik'] }}"
                                                        readonly>
                                                    <input type="hidden" class="form-control"
                                                        name="max_baku_mutu_detail_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="max_baku_mutu_detail_parameter_klinik_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['max_baku_mutu_detail_parameter_klinik'] }}"
                                                        readonly>
                                                    <input type="hidden" class="form-control"
                                                        name="equal_baku_mutu_detail_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        id="equal_baku_mutu_detail_parameter_klinik_{{ $no_sub }}"
                                                        value="{{ $item_subsatuan_klinik['equal_baku_mutu_detail_parameter_klinik'] }}"
                                                        readonly>
                                                </td>


                                                <td style="width: 15%">
                                                    <input type="text" class="form-control"
                                                        name="method_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                        id="method_permohonan_uji_parameter_klinik_{{ $no }}"
                                                        value="{{ $item_satuan_klinik['method_permohonan_uji_parameter_klinik'] ?? old('method_permohonan_uji_parameter_klinik') }}">
                                                </td>

                                                <td style="width: 20%">
                                                    <textarea class="form-control" id="keterangan_permohonan_uji_sub_parameter_klinik_{{ $no_sub }}"
                                                        name="keterangan_permohonan_uji_sub_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}][{{ $item_subsatuan_klinik['id_permohonan_uji_sub_parameter_klinik'] }}]"
                                                        cols="5" rows="5">{{ $item_subsatuan_klinik['keterangan_permohonan_uji_sub_parameter_klinik'] ?? old('keterangan_permohonan_uji_sub_parameter_klinik') }}</textarea>
                                                </td>
                                            </tr>

                                            @php
                                                $no_sub++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr>
                                            <td style="width: 20%">
                                                -
                                                {{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}


                                                <input type="hidden"
                                                    name="permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="permohonan_uji_parameter_klinik_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}"
                                                    readonly>
                                            </td>

                                            @if (isset($item_satuan_klinik['equal']))
                                                <td style="width: 15%">
                                                    <input type="text" class="form-control"
                                                        name="hasil_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                        id="hasil_permohonan_uji_parameter_klinik_{{ $no }}"
                                                        value="{{ $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] ?? $item_satuan_klinik['equal'] }}"
                                                        onkeyup="GetHasilParameter({{ $no }})"
                                                        onchange="GetHasilParameter({{ $no }})">
                                                </td>
                                            @else
                                                <td style="width: 15%">
                                                    <input type="text" class="form-control"
                                                        name="hasil_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                        id="hasil_permohonan_uji_parameter_klinik_{{ $no }}"
                                                        value="{{ $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] ?? old('hasil_permohonan_uji_parameter_klinik') }}"
                                                        onkeyup="GetHasilParameter({{ $no }})"
                                                        onchange="GetHasilParameter({{ $no }}); @if($item_satuan_klinik['nama_parameter_satuan_klinik'] == "Kreatinin") calculateEfgr('{{ $item_permohonan_uji_klinik->pasien->gender_pasien }}', {{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik }}, this.value) @endif"
                                                        data-name="{{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}"
                                                    >
                                                </td>
                                            @endif

                                            <td style="width: 15%">

                                                @if ($item_satuan_klinik['satuan_permohonan_uji_parameter_klinik'] != null)
                                                    <select class="form-control satuan_permohonan_uji_parameter_klinik"
                                                        name="satuan_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                        id="satuan_permohonan_uji_parameter_klinik_{{ $no }}">

                                                        <option
                                                            value="{{ $item_satuan_klinik['satuan_permohonan_uji_parameter_klinik'] }}"
                                                            selected>
                                                            {{ $item_satuan_klinik['nama_satuan_permohonan_uji_parameter_klinik'] }}
                                                        </option>
                                                    </select>
                                                @else
                                                    <select class="form-control satuan_permohonan_uji_parameter_klinik"
                                                        name="satuan_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                        id="satuan_permohonan_uji_parameter_klinik_{{ $no }}">

                                                        <option value=""></option>
                                                    </select>
                                                @endif

                                            </td>

                                            <td style="width: 15%">
                                                <input type="text" class="form-control"
                                                    name="baku_mutu_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="baku_mutu_permohonan_uji_parameter_klinik_{{ $no }}"
                                                    value="{!! rubahNilaikeForm($item_satuan_klinik['nilai_baku_mutu']) !!}" readonly>

                                                <input type="hidden" class="form-control"
                                                    name="min[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="min_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['min'] }}" readonly>
                                                <input type="hidden" class="form-control"
                                                    name="max[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="max_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['max'] }}" readonly>
                                                <input type="hidden" class="form-control"
                                                    name="equal[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="equal_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['equal'] }}" readonly>
                                            </td>


                                            <td style="width: 15%">
                                                <input type="text" class="form-control"
                                                    name="method_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="method_permohonan_uji_parameter_klinik_{{ $no }}"
                                                    value="{{ $item_satuan_klinik['method_permohonan_uji_parameter_klinik'] ?? old('method_permohonan_uji_parameter_klinik') }}">
                                            </td>
                                            <td style="width: 20%">
                                                <textarea class="form-control"
                                                    name="keterangan_permohonan_uji_parameter_klinik[{{ $item_satuan_klinik['id_permohonan_uji_parameter_klinik'] }}]"
                                                    id="keterangan_permohonan_uji_parameter_klinik_{{ $no }}" cols="5" rows="5">{!! rubahNilaikeForm(
                                                        $item_satuan_klinik['keterangan_permohonan_uji_parameter_klinik'] ??
                                                            old('keterangan_permohonan_uji_parameter_klinik'),
                                                    ) !!}</textarea>
                                            </td>
                                        </tr>
                                    @endif

                                    @php
                                        $no++;
                                    @endphp
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

    </form>
    <button type="button" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
    <button type="button" class="btn btn-light"
        onclick="document.location='{{ url('/elits-permohonan-uji-klinik-2/verification/' . $item_permohonan_uji_klinik->id_permohonan_uji_klinik) }}'">Kembali</button>
    </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>



    <script>
        function GetHasilParameter(row) {
            // mengkalkulasi hitungan nilai rujukan dan nilai hasil sehingga mendapatkan hasil flagnya dari baku mutu
            var val_baku_mutu = $('#baku_mutu_permohonan_uji_parameter_klinik_' + row).val();
            var val_min = $('#min_' + row).val();
            var val_max = $('#max_' + row).val();
            var val_equal = $('#equal_' + row).val();
            var val_hasil = $('#hasil_permohonan_uji_parameter_klinik_' + row).val();
            var cetak_hasil = '';



            $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
            $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');

            if (val_hasil != null && val_hasil != '') {
                if (val_equal != null && val_equal != '') {
                    console.log(val_equal);
                    var cetak_hasil = "";

                    if (val_equal != val_hasil) {
                        cetak_hasil = '<h4 style="margin-top: 0!important;margin-bottom: 0!important;">*</h4>';
                        $('#flag_permohonan_uji_parameter_klinik_' + row).val(1);
                        $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
                    } else {

                        $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
                        $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');
                    }

                } else {

                    var cetak_hasil = "";

                    if ((val_min != null && val_min != '') && (val_max != null && val_max != '')) {
                        var val_hasil = $('#hasil_permohonan_uji_parameter_klinik_' + row).val();
                        if (parseFloat(val_hasil) >= parseFloat(val_min) && parseFloat(val_hasil) <= parseFloat(val_max)) {
                            cetak_hasil = val_hasil;
                            $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
                            $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');

                        } else {
                            cetak_hasil = '<h4 style="margin-top: 0!important;margin-bottom: 0!important;">*</h4>';
                            $('#flag_permohonan_uji_parameter_klinik_' + row).val(1);
                            $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);

                        }


                    } else {

                        if (val_min != null && val_min != '') {

                            if (parseFloat(val_hasil) < parseFloat(val_min)) {
                                cetak_hasil = '<h4 style="margin-top: 0!important;margin-bottom: 0!important;">*</h4>';
                                $('#flag_permohonan_uji_parameter_klinik_' + row).val(1);
                                $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
                            } else {
                                cetak_hasil = val_hasil;
                                $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
                                $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');

                            }
                        }

                        if (val_max != null && val_max != '') {
                            if (parseFloat(val_hasil) > parseFloat(val_max)) {

                                cetak_hasil = '<h4 style="margin-top: 0!important;margin-bottom: 0!important;">*</h4>';
                                $('#flag_permohonan_uji_parameter_klinik_' + row).val(1);
                                $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);

                            } else {
                                cetak_hasil = val_hasil;
                                $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
                                $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');

                            }
                        }
                    }
                }


            } else {

                $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
                $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('');
            }

            // if (val_baku_mutu !== null && val_baku_mutu !== '-') {
            //   if (val_min != 0 && val_max != 0) {
            //     var val_hasil = $('#hasil_permohonan_uji_parameter_klinik_' + row).val();
            //     var cetak_hasil = "";

            //     console.log(val_hasil);

            //     // kondisi mendapatkan nilai between

            //     if (val_hasil == null || val_hasil == '') {
            //       console.log('kosong');
            //       $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
            //       $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('-');
            //     } else {
            //       if (val_hasil >= val_min && val_hasil <= val_max) {
            //         cetak_hasil = val_hasil;

            //         $('#flag_permohonan_uji_parameter_klinik_' + row).val(cetak_hasil);
            //         $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
            //       } else {
            //         cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';

            //         $('#flag_permohonan_uji_parameter_klinik_' + row).val(cetak_hasil);
            //         $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
            //       }
            //     }
            //   }

            //   // mendeteksi jika yang dimasukkan positif atau negatif
            //   if (val_equal != null && val_equal != 0) {
            //     var val_hasil = $('#hasil_permohonan_uji_parameter_klinik_' + row).val();

            //     if (val_hasil == null || val_hasil == '') {
            //       $('#flag_permohonan_uji_parameter_klinik_' + row).val('');
            //       $('#flag_permohonan_uji_parameter_klinik_text_' + row).html('-');
            //     } else {
            //       if (val_hasil == val_equal) {
            //         cetak_hasil = val_hasil;

            //         $('#flag_permohonan_uji_parameter_klinik_' + row).val(cetak_hasil);
            //         $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
            //       } else {
            //         cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';

            //         $('#flag_permohonan_uji_parameter_klinik_' + row).val(cetak_hasil);
            //         $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
            //       }
            //     }
            //   }
            // }
        }

        function calculateEfgr(gender, age, kreatinin){
          let scr = parseFloat(kreatinin);
          let gfr = 0;

          if (gender === 'L') {
            gfr = 142 *
              Math.pow(Math.min(scr / 0.9, 1), -0.302) *
              Math.pow(Math.max(scr / 0.9, 1), -1.2) *
              Math.pow(0.9938, age);
          } else {
            gfr = 142 *
              Math.pow(Math.min(scr / 0.7, 1), -0.241) *
              Math.pow(Math.max(scr / 0.7, 1), -1.2) *
              Math.pow(0.9938, age) *
              1.012;
          }


          let input = document.querySelector('[data-name="e-GFR (CKD-EPI)"]');
          if (input) {
            input.value = gfr.toFixed(0);
          }
        }

        function GetHasilSubParameter(row) {
            // mengkalkulasi hitungan nilai rujukan dan nilai hasil sehingga mendapatkan hasil flagnya dari baku mutu sub parameter
            var val_baku_mutu = $('#baku_mutu_permohonan_uji_sub_parameter_klinik_' + row).val();
            var val_min = $('#min_baku_mutu_detail_parameter_klinik_' + row).val();
            var val_max = $('#max_baku_mutu_detail_parameter_klinik_' + row).val();
            var val_equal = $('#equal_baku_mutu_detail_parameter_klinik_' + row).val();
            var cetak_hasil = '';


            // $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
            // $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html(0);

            if (val_baku_mutu !== null && val_baku_mutu !== '-') {
                // mendeteksi jika yang dimasukkan nilai antara

                if (val_hasil != null && val_hasil != '') {

                    if (val_equal != null && val_equal != '') {
                        var cetak_hasil = "";
                        if (val_equal != val_hasil) {
                            cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';
                            $('#flag_permohonan_uji_parameter_klinik_' + row).val(cetak_hasil);
                            $('#flag_permohonan_uji_parameter_klinik_text_' + row).html(cetak_hasil);
                        } else {
                            cetak_hasil = val_hasil;
                            $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
                            $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html('-');
                        }

                    } else {
                        var cetak_hasil = "";

                        if ((val_min != null && val_min != '') && (val_max != null && val_max != '')) {
                            var val_hasil = $('#hasil_permohonan_uji_parameter_klinik_' + row).val();
                            if (parseFloat(val_hasil) >= parseFloat(val_min) && parseFloat(val_hasil) <= parseFloat(
                                    val_max)) {
                                cetak_hasil = val_hasil;
                                $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
                                $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html('-');

                            } else {
                                cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';
                                $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val(cetak_hasil);
                                $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html(cetak_hasil);


                            }

                        } else {

                            if (val_min != null && val_min != '') {

                                if (parseFloat(val_hasil) < parseFloat(val_min)) {
                                    cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';
                                    $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
                                    $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html('-');
                                } else {
                                    cetak_hasil = val_hasil;
                                    $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val(cetak_hasil);
                                    $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html(cetak_hasil);


                                }
                            }

                            if (val_max != null && val_max != '') {
                                if (parseFloat(val_hasil) > parseFloat(val_max)) {
                                    cetak_hasil = '<strong>' + val_hasil + '</strong> <sup style="color: red">*</sup>';
                                    $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
                                    $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html('-');

                                } else {
                                    cetak_hasil = val_hasil;
                                    $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val(cetak_hasil);
                                    $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html(cetak_hasil);


                                }
                            }
                        }
                    }


                } else {

                    $('#flag_permohonan_uji_sub_parameter_klinik_' + row).val('');
                    $('#flag_permohonan_uji_sub_parameter_klinik_text_' + row).html('-');
                }



            }
        }

        $('#is_urine').on('change', function() {
            if ($(this).is(":checked")) {
                $("#spesimen_urine_permohonan_uji_klinik").css('display', 'none')
            } else {
                $("#spesimen_urine_permohonan_uji_klinik").css('display', 'block')
            }
        });

        $('#is_darah').on('change', function() {
            if ($(this).is(":checked")) {
                $("#spesimen_darah_permohonan_uji_klinik").css('display', 'none')
            } else {
                $("#spesimen_darah_permohonan_uji_klinik").css('display', 'block')
            }
        });

        $('#by_account').prop('checked', false);
        $('#by_account').on('change', function() {
            if ($(this).is(":checked")) {
                $("#name_analis_permohonan_uji_klinik").val($(this).data('name'))
                $("#nip_analis_permohonan_uji_klinik").val($(this).data('nip'))
                $("#analis_permohonan_uji_klinik").val($(this).data('id'))
                console.log("checked");

                // it is checked
            } else {
                $("#name_analis_permohonan_uji_klinik").val("Estu Lentera")
                $("#nip_analis_permohonan_uji_klinik").val("111111111111111111")
            }
        })
        var CSRF_TOKEN = $('#csrf-token').val();

        $("#dokter_permohonan_uji_klinik").select2({
            ajax: {
                url: "{{ route('get-dokter-by-select') }}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function(response) {
                    return {
                        results: $.map(response, function(obj) {
                            return {
                                id: obj.id,
                                text: obj.text
                            };
                        })
                    };
                },
                cache: true
            },
            placeholder: 'Pilih dokter',
            allowClear: true
        });

        $(".satuan_permohonan_uji_parameter_klinik").select2({
            ajax: {
                url: "{{ route('getDataUnitBySelect') }}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        _token: CSRF_TOKEN,
                        search: params.term // search term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response
                    };
                },
                cache: true,
            },
            placeholder: 'Pilih unit',
            allowClear: true
        });
        {{-- $(document).ready(function() { --}}
        {{--    $('.btn-simpan').on('click', function() { --}}
        {{--        $('#form').ajaxSubmit({ --}}
        {{--            success: function(response) { --}}
        {{--                if (response.status == true) { --}}
        {{--                    swal({ --}}
        {{--                            title: "Success!", --}}
        {{--                            text: response.pesan, --}}
        {{--                            icon: "success" --}}
        {{--                        }) --}}
        {{--                        .then(function() { --}}
        {{--                            document.location = --}}
        {{--                                '/elits-permohonan-uji-klinik-2/verification/{{ $item_permohonan_uji_klinik->id_permohonan_uji_klinik }}'; --}}
        {{--                        }); --}}
        {{--                } else { --}}
        {{--                    var pesan = ""; --}}
        {{--                    var data_pesan = response.pesan; --}}
        {{--                    const wrapper = document.createElement('div'); --}}

        {{--                    if (typeof(data_pesan) == 'object') { --}}
        {{--                        jQuery.each(data_pesan, function(key, value) { --}}
        {{--                            console.log(value); --}}
        {{--                            pesan += value + '. <br>'; --}}
        {{--                            wrapper.innerHTML = pesan; --}}
        {{--                        }); --}}

        {{--                        swal({ --}}
        {{--                            title: "Error!", --}}
        {{--                            content: wrapper, --}}
        {{--                            icon: "warning" --}}
        {{--                        }); --}}
        {{--                    } else { --}}
        {{--                        swal({ --}}
        {{--                            title: "Error!", --}}
        {{--                            text: response.pesan, --}}
        {{--                            icon: "warning" --}}
        {{--                        }); --}}

        {{--                    } --}}
        {{--                } --}}
        {{--            }, --}}
        {{--            error: function() { --}}

        {{--                swal("Error!", "System gagal menyimpan!", "error"); --}}

        {{--            } --}}
        {{--        }) --}}
        {{--    }); --}}
        {{-- }) --}}
        $(document).ready(function() {
            $('.btn-simpan').on('click', function() {
                swal({
                    title: "Menyimpan...",
                    text: "Harap tunggu beberapa saat.",
                    icon: "info",
                    buttons: false,
                    closeOnClickOutside: false,
                });

                $('#form').ajaxSubmit({
                    success: function(response) {
                        if (response.status == true) {
                            swal({
                                    title: "Success!",
                                    text: response.pesan,
                                    icon: "success"
                                })
                                .then(function() {
                                    document.location =
                                        '/elits-permohonan-uji-klinik-2/verification/{{ $item_permohonan_uji_klinik->id_permohonan_uji_klinik }}';
                                });
                        } else {
                            var pesan = "";
                            var data_pesan = response.pesan;
                            const wrapper = document.createElement('div');

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
                    error: function() {
                        swal("Error!", "System gagal menyimpan!", "error");
                    }
                });
            });
        });
    </script>
@endsection
