@extends('masterweb::template.admin.layout')
@section('title')
    Parameter Paket Klinik
@endsection

@section('content')
    <style>
        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-input {
            position: relative;
            width: 30px;
            height: 15px;
            -webkit-appearance: none;
            appearance: none;
            background-color: #ccc;
            outline: none;
            cursor: pointer;
            border-radius: 15px;
            transition: background-color 0.3s;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
        }

        .form-check-input:before {
            content: "";
            position: absolute;
            width: 13px;
            height: 13px;
            background-color: white;
            border-radius: 50%;
            top: 1px;
            left: 1px;
            transition: transform 0.3s;
        }

        .form-check-input:checked:before {
            transform: translateX(15px);
        }

        .form-check-label {
            margin-left: 2px;
            font-size: 16px;
        }
    </style>



    <script src="{{ asset('assets/admin/js/bootstrap-birthday.js') }}"></script>
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
                                        Uji
                                        Klinik
                                        Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>Edit</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <h4>Edit Permohonan Uji Klinik
            </h4>
        </div>
        <div class="card-body">
            <form action="{{ route('elits-permohonan-uji-klinik-2.update', $item->id_permohonan_uji_klinik) }}"
                method="POST" enctype="multipart/form-data" id="form">

                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="code_register">No. SAMPLE <span style="color: red">*</span></label>
                    <div class="input-group date">
                        <input type="text" class="form-control" readonly name="noregister_permohonan_uji_klinik"
                            id="noregister_permohonan_uji_klinik" placeholder="No. REGISTER"
                            value="{{ $item->noregister_permohonan_uji_klinik }}">
                    </div>
                </div>

                {{-- view untuk menampilkan data pasien yang sudah ada --}}
                <label for="pasien">PASIEN <span style="color: red">*</span></label>
                <div class="card" id="display-detail-pasien" style="margin-bottom: 20px">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width: 20%">ID Satu Sehat</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="id_satu_sehat_1" name="id_satu_sehat"
                                                    class="form-control" value="{{ $pasien->id_pasien_satu_sehat }}"
                                                    required readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">NIK Pasien</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="nik_pasien" name="nik_pasien" class="form-control"
                                                    value="{{ $pasien->nik_pasien }}" required readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">No Rekam Medis</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="no_rekam_medis_pasien" name="no_rekammedis_pasien"
                                                    value="{{ $pasien->no_rekammedis_pasien }}" class="form-control"
                                                    value="" required readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">Nama Lengkap (Sesuai KTP)</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="nama_pasien" name="nama_pasien"
                                                    class="form-control" value="{{ $pasien->nama_pasien }}" required
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">Jenis Kelamin Pasien</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="gender_pasien" name="gender_pasien"
                                                    class="form-control" value="{{ $pasien->gender_pasien }}" required
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">Tanggal Lahir Pasien</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="tgllahir_pasien" name="tgllahir_pasien"
                                                    class="form-control" value="{{ $pasien->tgllahir_pasien }}" required
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">Nomor Telepon</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="phone_pasien" name="phone_pasien"
                                                    class="form-control" value="{{ $pasien->phone_pasien }}" required
                                                    readonly>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 20%">Alamat Pasien</th>
                                            <th style="width: 2%">:</th>
                                            <td style="width: 78%">
                                                <input type="text" id="alamat_pasien" name="alamat_pasien"
                                                    class="form-control" value="{{ $pasien->alamat_pasien }}" required
                                                    readonly>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="datelab_samples">UMUR PASIEN</label>
                    <div class="row">
                        <div class="col-sm">
                            <div class="input-group">
                                <input type="text" class="form-control" name="umurtahun_pasien_permohonan_uji_klinik"
                                    id="umurtahun_pasien_permohonan_uji_klinik"
                                    value="{{ $item->umurtahun_pasien_permohonan_uji_klinik }}" placeholder="Umur"
                                    readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        tahun
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="input-group">
                                <input type="text" class="form-control" name="umurbulan_pasien_permohonan_uji_klinik"
                                    id="umurbulan_pasien_permohonan_uji_klinik"
                                    value="{{ $item->umurbulan_pasien_permohonan_uji_klinik }}" placeholder="Umur"
                                    readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Bulan
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm">
                            <div class="input-group">
                                <input type="text" class="form-control" name="umurhari_pasien_permohonan_uji_klinik"
                                    id="umurhari_pasien_permohonan_uji_klinik"
                                    value="{{ $item->umurhari_pasien_permohonan_uji_klinik }}" placeholder="Umur"
                                    readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        Hari
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="row">
                    <div class="col-md-12">
                        <div class="form-group table-sample">
                            <label for="jenis_spesimen">JENIS SPESIMEN<span style="color: red">*</span></label>
                            <div class="input-group date">
                                <select class="form-control" name="jenis_spesimen" id="jenis_spesimen" required>
                                    <option value="" {{ $item->jenis_spesimen == '' ? 'selected' : '' }}>Pilih jenis
                                        spesimen</option>
                                    <option value="Darah Beku"
                                        {{ $item->jenis_spesimen == 'Darah Beku' ? 'selected' : '' }}>Darah Beku</option>
                                    <option value="NaF" {{ $item->jenis_spesimen == 'NaF' ? 'selected' : '' }}>NaF
                                    </option>
                                    <option value="EDTA" {{ $item->jenis_spesimen == 'EDTA' ? 'selected' : '' }}>EDTA
                                    </option>
                                    <option value="Urine" {{ $item->jenis_spesimen == 'Urine' ? 'selected' : '' }}>Urine
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group table-sample">
                            <label for="#">TGL. REGISTER <span style="color: red">*</span></label>
                            <div class="input-group date">
                                <input type="datetime-local" class="form-control" autocomplete="on"
                                    name="tglregister_permohonan_uji_klinik" id="tglregister_permohonan_uji_klinik"
                                    value="{{ \Carbon\Carbon::parse($item->tglregister_permohonan_uji_klinik)->format('Y-m-d\TH:i') }}">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="form-group">
                    <label for="nama_dokter_pengirim_permohonan_uji_klinik">NAMA DOKTER PENGIRIM</label>
                    <input type="text" class="form-control" name="nama_dokter_pengirim_permohonan_uji_klinik"
                        id="nama_dokter_pengirim_permohonan_uji_klinik"
                        value="{{ $item->nama_dokter_pengirim_permohonan_uji_klinik }}"
                        placeholder="Masukkan nama dokter pengirim">
                </div>

                <div class="form-group">
                    <label for="hp_dokter_pengirim_permohonan_uji_klinik">No. HP DOKTER PENGIRIM</label>
                    <input type="text" class="form-control" name="hp_dokter_pengirim_permohonan_uji_klinik"
                        id="hp_dokter_pengirim_permohonan_uji_klinik"
                        value="{{ $item->hp_dokter_pengirim_permohonan_uji_klinik }}"
                        placeholder="Masukkan no. hp dokter pengirim">
                    <script>
                        $(document).ready(function() {
                            $('#hp_dokter_pengirim_permohonan_uji_klinik').on('input', function() {
                                this.value = this.value.replace(/[^\d]+/g, '');
                            });
                        });
                    </script>
                </div>


                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="isPerwakilan"
                        {{ $item->nama_perwakilan_permohonan_uji_klinik ? 'checked' : '' }}>
                    <label class="form-check-label" for="flexSwitchCheckDefault"
                        style="font-size: 16px; margin-left: 12px;">Perwakilan</label>
                </div>

                <div class="form-group" id="form_perwakilan"
                    style="{{ $item->nama_perwakilan_permohonan_uji_klinik ? 'display: block;' : 'display: none;' }}">
                    <label for="nama_perwakilan_permohonan_uji_klinik">NAMA PERWAKILAN</label>
                    <input type="text" class="form-control" name="nama_perwakilan_permohonan_uji_klinik"
                        id="nama_perwakilan_permohonan_uji_klinik" placeholder="Masukkan perwakilan"
                        value="{{ $item->nama_perwakilan_permohonan_uji_klinik }}">

                    <label for="gender_perwakilan_permohonan_uji_klinik" class="mt-3">JENIS KELAMIN</label>
                    <select class="form-control" id="gender_perwakilan_permohonan_uji_klinik"
                        name="gender_perwakilan_permohonan_uji_klinik">
                        <option value=""
                            {{ $item->gender_perwakilan_permohonan_uji_klinik == '' ? 'selected' : '' }}>Pilih jenis
                            kelamin</option>
                        <option value="L"
                            {{ $item->gender_perwakilan_permohonan_uji_klinik == 'L' ? 'selected' : '' }}>Laki Laki
                        </option>
                        <option value="P"
                            {{ $item->gender_perwakilan_permohonan_uji_klinik == 'P' ? 'selected' : '' }}>Perempuan
                        </option>
                    </select>

                    <label for="tanggal_lahir_perwakilan" class="mt-3">TANGGAL LAHIR PERWAKILAN</label>

                    <input type="text" name="tanggal_lahir_perwakilan"
                        value="{{ $item->tanggal_lahir_perwakilan_permohonan_uji_klinik }}" id="basic2" />
                    <script type="text/javascript">
                        $('#basic2').bootstrapBirthday({
                            dateFormat: "littleEndian"
                        });
                    </script>

                    <label for="alamat_perwakilan" class="mt-3">ALAMAT PERWAKILAN</label>
                    <textarea class="form-control" name="alamat_perwakilan" id="alamat_perwakilan" rows="3">{{ $item->alamat_perwakilan_permohonan_uji_klinik }}</textarea>
                </div>

                <div class="form-group">
                    <label for="tipe_pemeriksaan_prolanis">TIPE PEMERIKSAAN</label>
                    <div class="input-group">
                        <select class="form-control" name="tipe_pemeriksaan_prolanis" id="tipe_pemeriksaan_prolanis">
                            <option value="" {{ is_null($item->tipe_pemeriksaan_prolanis) ? 'selected' : '' }}>
                                Pilih Tipe Pemeriksaan
                            </option>
                            <option value="PROLANIS DM"
                                {{ $item->tipe_pemeriksaan_prolanis === 'PROLANIS DM' ? 'selected' : '' }}>
                                PROLANIS DM
                            </option>
                            <option value="PROLANIS HT"
                                {{ $item->tipe_pemeriksaan_prolanis === 'PROLANIS HT' ? 'selected' : '' }}>
                                PROLANIS HT
                            </option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="petugas_penerima">PETUGAS PENERIMA</label>
                    <div class="input-group date">
                        <select class="form-control" name="petugas_penerima" id="petugas_penerima">
                            <option value="" {{ $verifikasi->nama_petugas == '' ? 'selected' : '' }}>Pilih petugas
                                penerima</option>
                            <option value="Umi Sulistiyana"
                                {{ $verifikasi->nama_petugas == 'Umi Sulistiyana' ? 'selected' : '' }}>Umi Sulistiyana
                            </option>
                            <option value="Liyana" {{ $verifikasi->nama_petugas == 'Liyana' ? 'selected' : '' }}>Liyana
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="diagnosa_permohonan_uji_klinik">DIAGNOSA</label>
                    <textarea class="form-control" name="diagnosa_permohonan_uji_klinik" id="diagnosa_permohonan_uji_klinik"
                        placeholder="Masukkan diagnosa">{{ $item->diagnosa_permohonan_uji_klinik }}</textarea>
                </div>

                <div class="form-group">
                    <label>METODE PEMBAYARAN</label>
                    <select class="form-control" name="metode_pembayaran">
                        <option value="" {{ $item->metode_pembayaran == '' ? 'selected' : '' }}>Pilih metode
                            pembayaran</option>
                        <option value="0" {{ $item->metode_pembayaran == 0 ? 'selected' : '' }}>Cash</option>
                        <option value="1" {{ $item->metode_pembayaran == 1 ? 'selected' : '' }}>Transfer</option>
                    </select>
                </div>
                <br>

                <button type="submit" class="btn btn-warning mr-2 btn-simpan">Update</button>
                <button type="button" onclick="document.location='{{ url('/elits-parameter-paket-extra') }}'"
                    class="btn btn-light">Kembali</button>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="//cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>

    <script src="{{ asset('assets/admin/js/bootstrap-birthday.js') }}"></script>

    <script>
        function goBack() {
            window.history.back();
        }

        $(function() {
            var CSRF_TOKEN = "{{ csrf_token() }}";

            $(document).ready(function() {
                $('.select2-multiple').select2({
                    placeholder: "Pilih Paket..",
                    allowClear: true,
                    theme: "bootstrap4",
                });

            });

            $('.btn-simpan').on('click', function() {
                $('#form').ajaxForm({
                    success: function(response) {
                        if (response.status == true) {
                            swal({
                                    title: "Success!",
                                    text: response.pesan,
                                    icon: "success"
                                })
                                .then(function() {
                                    document.location = '/elits-permohonan-uji-klinik-2';
                                });
                        } else {
                            var pesan = "";

                            jQuery.each(response.pesan, function(key, value) {
                                pesan += value + '. ';
                            });

                            swal({
                                title: "Error!",
                                text: pesan,
                                icon: "warning"
                            });
                        }
                    },
                    error: function() {
                        swal("Error!", "System gagal menyimpan!", "error");
                    }
                })
            })
        })

        // jQuery for handling the checkbox and form display
        $('#flexSwitchCheckDefault').on('change', function() {
            var formPerwakilan = $('#form_perwakilan');
            if (this.checked) {
                formPerwakilan.find('input, textarea, select').prop('disabled', false);
                formPerwakilan.show();
            } else {
                formPerwakilan.find('input, textarea, select').prop('disabled', true);
                formPerwakilan.hide();
            }
        });

        // // Initialize bootstrapBirthday plugin
        // $('#basic2').bootstrapBirthday({
        //     dateFormat: "littleEndian" // Format dd-mm-yyyy
        // });


        CKEDITOR.replace('diagnosa_permohonan_uji_klinik', {
            // Disable security notifications.
            versionCheck: false
        });
    </script>
@endsection
