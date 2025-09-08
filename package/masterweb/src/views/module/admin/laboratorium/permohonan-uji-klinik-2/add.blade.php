@extends('masterweb::template.admin.layout')
@section('title')
    Permohonan Uji Klinik
@endsection


@section('content')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="//cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>



    <script src="{{ asset('assets/admin/js/bootstrap-birthday.js') }}"></script>
    <style>
        .modal-dialog.custom-width {
            max-width: 80% !important;
            width: 80% !important;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .single-date-field {
            width: 120px;
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


    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="template-demo">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i
                                        class="fa fa-home menu-icon mr-1"></i>
                                    Beranda</a></li>
                            <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji-klinik-2') }}">Permohonan Uji
                                    Klinik
                                    Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><span>Create</span></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Tambah Permohonan Uji Klinik
            </h4>
        </div>

        <div class="mx-4 mb-4 mt-4" id="search_satu_sehat" style="display: none;">
            <h5>Search Data Satu Sehat</h5>
            <form id="searchForm">
                <div class="form-group">
                    <label for="identifier">NIK</label>
                    <input type="text" class="form-control" id="identifier" name="identifier"
                        placeholder="Masukan NIK pasien">
                </div>
                <div class="form-group">
                    <label for="name">Nama Pasien</label>
                    <input type="text" class="form-control" id="name" name="name"
                        placeholder="Masukan nama pasien">
                </div>
                <div class="form-group">
                    <label for="birthdate">Tanggal Lahir</label>
                    <input type="text" name="birthday" value="" id="basic" />


                </div>

                <script type="text/javascript">
                    $('#basic').bootstrapBirthday({
                        dateFormat: "littleEndian"
                    });
                </script>
                <div class="form-group">
                    <label for="gender">Jenis Kelamin</label>
                    <select class="form-control" id="gender" name="gender">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male">Laki Laki</option>
                        <option value="female">Perempuan</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary" id="fetchPatientsBtn">Cari Pasien</button>
                <button type="button" class="btn btn-danger" id="close_button_search_satu_sehat">Close</button>
            </form>
        </div>

        {{-- Search Silaboy --}}
        <div class="mx-4 mbPEMERIKSAAM-4 mt-4" id="search_silaboy" style="display: none; ">
            <div class="mb-4" style="display: flex; align-items: center; justify-content: space-between;">
                <P style="font-size: 16pt"><strong>Search Pasien Silaboy</strong></P>
                <button type="button" class="btn btn-danger" id="close_button_silaboy">Close</button>
            </div>
            <div class="form-group">
                <select class="form-control" name="pasien_permohonan_uji_klinik" id="pasien_permohonan_uji_klinik"
                    style="width: 100%">
                    <option value=""></option>
                </select>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade modal-xl" id="patientsModal" tabindex="-1" role="dialog"
            aria-labelledby="patientsModalLabel" aria-hidden="true">
            <div class="modal-dialog custom-width" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="patientsModalLabel">Patient Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="text" id="searchAddress" class="form-control" placeholder="Search Address">
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>NIK</th>
                                    <th>Gender</th>
                                    <th>ID</th>
                                    <th>Address</th>
                                    <th>Birth Date</th>
                                    <th>Name</th>
                                    <th>Telepon</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="patientsTableBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <ul class="list-group list-group-flush">
            <div class="px-4" id="button_action_add" style="display: block;">
                <button class="btn btn-primary" id="button_search_satu_sehat">Cari Pasien Satu Sehat</button>
                <a href="javascript:void(0)" class="btn btn-link btn-new-pasien">
                    <title>Tambah pasien jika data pasien tidak ditemukan di Satu Sehat</title>
                    <button type="button" class="btn btn-primary">Tambah Data Pasien</button>
                </a>
                <button class="btn btn-primary" id="button_search_silaboy">Cari Pasien Silaboy</button>
            </div>
            <li class="list-group-item">
                <form action="{{ route('elits-permohonan-uji-klinik-2.store') }}" method="POST"
                    enctype="multipart/form-data" id="form">

                    @csrf

                    <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" class="form-control" name="nourut_permohonan_uji_klinik"
                        id="nourut_permohonan_uji_klinik" value="{{ $set_count }}" readonly>

                    <div class="form-group">
                        <label for="code_register">No. SAMPLE <span style="color: red">*</span></label>
                        <div class="input-group date">
                            <input type="text" class="form-control" readonly name="noregister_permohonan_uji_klinik"
                                id="noregister_permohonan_uji_klinik" placeholder="No. REGISTER"
                                value="{{ $code }}">
                        </div>
                    </div>

                    <input type="hidden" class="form-control" readonly name="pasien_permohonan_uji_klinik"
                        id="seccond_pasien_permohonan_uji_klinik">

                    <input type="hidden" class="form-control" readonly name="nopasien_permohonan_uji_klinik"
                        id="nopasien_permohonan_uji_klinik">

                    {{-- view untuk menampilkan data pasien yang sudah ada --}}
                    <div class="card" id="display-detail-pasien" style="display: none; margin-bottom: 20px">
                        <div class="card-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th style="width: 20%">ID Satu Sehat</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="id_satu_sehat_1" name="id_satu_sehat"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">NIK Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="nik_pasien_1" name="nik_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">No Rekam Medis</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="no_rekam_medis_pasien_1"
                                                            name="no_rekammedis_pasien" class="form-control"
                                                            value="{{ str_pad((int) $count_pasien, 4, '0', STR_PAD_LEFT) }}"
                                                            required readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Nama Lengkap (Sesuai KTP)</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="nama_pasien_1" name="nama_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Jenis Kelamin Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="gender_pasien_1" name="gender_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Tanggal Lahir Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="tgllahir_pasien_1"
                                                            name="tgllahir_pasien" class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Nomor Telepon</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="phone_pasien_1" name="phone_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Alamat Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="alamat_pasien_1" name="alamat_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- view untuk menampilkan data pasien yang sudah ada silaboy --}}
                    <div class="card" id="display-detail-pasien-silaboy" style="display: none; margin-bottom: 20px">
                        <div class="card-body">
                            <div class="row">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tr>
                                                    <th style="width: 20%">ID Satu Sehat</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="id_satu_sehat_3" name="id_satu_sehat"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">NIK Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="nik_pasien_3" name="nik_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">No Rekam Medis</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="no_rekam_medis_pasien_3"
                                                            name="no_rekammedis_pasien" class="form-control"
                                                            value="{{ str_pad((int) $count_pasien, 4, '0', STR_PAD_LEFT) }}"
                                                            required readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Nama Lengkap (Sesuai KTP)</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="nama_pasien_3" name="nama_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Jenis Kelamin Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="gender_pasien_3" name="gender_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Tanggal Lahir Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="tgllahir_pasien_3"
                                                            name="tgllahir_pasien" class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Nomor Telepon</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="phone_pasien_3" name="phone_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th style="width: 20%">Alamat Pasien</th>
                                                    <th style="width: 2%">:</th>
                                                    <td style="width: 78%">
                                                        <input type="text" id="alamat_pasien_3" name="alamat_pasien"
                                                            class="form-control" required>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{--                    view untuk menampilkan data pasien baru --}}
                    <div class="card" id="display-new-pasien" style="display: none; margin-bottom: 20px">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="close cancel-new-pasien" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nik_pasien-2">NIK PASIEN <span style="color: red">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="nik_pasien"
                                                id="nik_pasien_2" placeholder="Masukkan NIK Pasien"
                                                value="{{ old('nikpasien_pasien') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nama_pasien-2">NAMA LENGKAP (SESUAI KTP) <span
                                                style="color: red">*</span></label>
                                        <div class="input-group date">
                                            <input type="text" class="form-control" name="nama_pasien"
                                                id="nama_pasien_2" placeholder="Masukkan Nama Pasien">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="no_rekammedis_pasien">NOMOR REKAM MEDIS <span
                                        style="color: red">*</span></label>
                                <input type="text" class="form-control no_rekammedis_pasien"
                                    name="no_rekammedis_pasien" id="no_rekammedis_pasien_2"
                                    placeholder="Nomor rekam medis"
                                    value="{{ str_pad((int) $count_pasien, 4, '0', STR_PAD_LEFT) }}" readonly>
                            </div>

                            <div class="form-group" hidden>
                                <label for="divisi_instansi_pasien-2">DIVISI/INSTANSI</label>
                                <input type="text" class="form-control divisi_instansi_pasien"
                                    name="divisi_instansi_pasien" id="divisi_instansi_pasien-2"
                                    placeholder="Divisi/instansi" value="{{ old('divisi_instansi_pasien') }}">
                            </div>

                            <div class="form-group">
                                <label for="jenis_kelamin-2">JENIS KELAMIN <span style="color: red">*</span></label>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="gender_pasien"
                                            id="gender_pasien_2" value="L" checked>
                                        Laki-laki
                                        <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="gender_pasien"
                                            id="gender_pasien_2" value="P">
                                        Perempuan
                                        <i class="input-helper"></i></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="datelab_samples">TANGGAL LAHIR <span style="color: red">*</span></label>
                                <input type="text" class="form-control js-date datepicker" name="tgllahir_pasien"
                                    id="tgllahir_pasien_2" placeholder="dd/mm/yyyy">
                            </div>


                            <script>
                                var input = document.querySelectorAll('.js-date')[0];

                                var dateInputMask = function dateInputMask(elm) {
                                    elm.addEventListener('keypress', function(e) {
                                        if (e.keyCode < 47 || e.keyCode > 57) {
                                            e.preventDefault();
                                        }

                                        var len = elm.value.length;

                                        // If we're at a particular place, let the user type the slash
                                        // i.e., 12/12/1212
                                        if (len !== 1 || len !== 3) {
                                            if (e.keyCode == 47) {
                                                e.preventDefault();
                                            }
                                        }

                                        // If they don't add the slash, do it for them...
                                        if (len === 2) {
                                            elm.value += '/';
                                        }

                                        // If they don't add the slash, do it for them...
                                        if (len === 5) {
                                            elm.value += '/';
                                        }
                                    });
                                };

                                dateInputMask(input);
                            </script>


                            <div class="form-group">
                                <label for="phone_pasien_2">No. TELP/HP</label>
                                <input type="text" class="form-control" name="phone_pasien" id="phone_pasien_2"
                                    placeholder="Masukkan no. telp/hp">
                            </div>

                            <div class="form-group">
                                <label for="alamat_pasien-2">ALAMAT <span style="color: red">*</span></label>
                                <textarea class="form-control" name="alamat_pasien" id="alamat_pasien_2" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" class="form-control date_birth_last"
                        name="tgllahir_pasien_permohonan_uji_klinik" id="tgllahir_pasien_permohonan_uji_klinik"
                        placeholder="dd/mm/yyyy" readonly>

                    <div class="form-group">
                        <label for="datelab_samples">UMUR PASIEN</label>
                        <div class="row">
                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                        name="umurtahun_pasien_permohonan_uji_klinik"
                                        id="umurtahun_pasien_permohonan_uji_klinik" placeholder="Umur" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            tahun
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                        name="umurbulan_pasien_permohonan_uji_klinik"
                                        id="umurbulan_pasien_permohonan_uji_klinik" placeholder="Umur" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            Bulan
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                        name="umurhari_pasien_permohonan_uji_klinik"
                                        id="umurhari_pasien_permohonan_uji_klinik" placeholder="Umur" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            Hari
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" hidden>
                        <div class="col-md-12">
                            <div class="form-group table-sample">
                                <label for="jenis_spesimen">JENIS SPESIMEN<span style="color: red">*</span></label>
                                <div class="input-group date">
                                    <select class="form-control" name="jenis_spesimen" id="jenis_spesimen" required>
                                        <option value="Darah Beku">Darah Beku</option>
                                        <option value="NaF">NaF</option>
                                        <option value="EDTA">EDTA</option>
                                        <option value="Urine">Urine</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group table-sample">
                                <label for="#">TGL. REGISTER <span style="color: red">*</span></label>
                                <div class="input-group date">
                                    <input type="datetime-local" class="form-control" autocomplete="on"
                                        name="tglregister_permohonan_uji_klinik" id="tglregister_permohonan_uji_klinik"
                                        value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label for="nama_dokter_pengirim_permohonan_uji_klinik">NAMA DOKTER PENGIRIM</label>
                        <input type="text" class="form-control" name="nama_dokter_pengirim_permohonan_uji_klinik"
                            id="nama_dokter_pengirim_permohonan_uji_klinik" placeholder="Masukkan nama dokter pengirim">
                    </div>

                    <div class="form-group">
                        <label for="hp_dokter_pengirim_permohonan_uji_klinik">No. HP DOKTER PENGIRIM</label>
                        <input type="text" class="form-control" name="hp_dokter_pengirim_permohonan_uji_klinik"
                            id="hp_dokter_pengirim_permohonan_uji_klinik" placeholder="Masukkan no. hp dokter pengirim">
                        <script>
                            $(document).ready(function() {
                                $('#hp_dokter_pengirim_permohonan_uji_klinik').on('input', function() {
                                    this.value = this.value.replace(/[^\d]+/g, '');
                                });
                            });
                        </script>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault" name="isPerwakilan">
                        <label class="form-check-label" for="flexSwitchCheckDefault"
                            style="font-size: 16px; margin-left: 12px;">Perwakilan</label>
                    </div>

                    <div class="form-group" id="form_perwakilan" style="display: none;">
                        <label for="nama_perwakian_permohonan_uji_klinik">NAMA PERWAKILAN</label>
                        <input type="text" class="form-control" name="nama_perwakian_permohonan_uji_klinik"
                            id="nama_perwakian_permohonan_uji_klinik" placeholder="Masukkan perwakilan">
                        <label for="gender" class="mt-3">JENIS KELAMIN</label>
                        <select class="form-control" id="gender_perwakilan_permohonan_uji_klinik"
                            name="gender_perwakilan_permohonan_uji_klinik">
                            <option value="L">Laki Laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                        <label for="tanggal_lahir_perwakilan" class="mt-3">TANGGAL LAHIR PERWAKILAN</label>
                        <input type="text" name="tanggal_lahir_perwakilan" value="" id="basic2" />
                        <script type="text/javascript">
                            $('#basic2').bootstrapBirthday({
                                dateFormat: "littleEndian"
                            });
                        </script>
                        <label for="alamat_perwakilan" class="mt-3">ALAMAT PERWAKILAN</label>
                        <textarea class="form-control" name="alamat_perwakilan" id="alamat_perwakilan" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tipe_pemeriksaan_prolanis">TIPE PEMERIKSAAN</label>
                        <div class="input-group date">
                            <select class="form-control" name="tipe_pemeriksaan_prolanis" id="tipe_pemeriksaan_prolanis">
                                <option value="" selected>Pemeriksaan Klinik</option>
                                <option value="PROLANIS DM">PROLANIS DM</option>
                                <option value="PROLANIS HT">PROLANIS HT</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="petugas_penerima">PETUGAS PENERIMA</label>
                        <div class="input-group date">
                            <select class="form-control" name="petugas_penerima" id="petugas_penerima">
                                <option value="Umi Sulistiyana">Umi Sulistiyana</option>
                                <option value="Liyana">Liyana</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="diagnosa_permohonan_uji_klinik">DIAGNOSA</label>
                        <input type="text" class="form-control" name="diagnosa_permohonan_uji_klinik"
                            id="diagnosa_permohonan_uji_klinik" placeholder="Masukkan diagnosa">
                    </div>
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select class="form-control" name="metode_pembayaran">
                            <option disabled>Metode Pembayaran</option>
                            <option value="0">Cash</option>
                            <option value="1">Transfer</option>
                        </select>
                    </div>
                </form>

                <button type="button" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
                {{-- <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button> --}}
                <button type="button" class="btn btn-light"
                    onclick="document.location='{{ url('/elits-permohonan-uji-klinik-2') }}'">Kembali</button>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/admin/vendors/Inputmask-5.x/dist/inputmask.js') }}"></script>
    <script>
        $(document).ready(function() {


            // Autotab
            // $('.date-field').autotab('number');


            (function(e) {
                var t = {
                    nodiff: "",
                    year: "year",
                    years: "years",
                    month: "month",
                    months: "months",
                    day: "day",
                    days: "days",
                    hour: "hour",
                    hours: "hours",
                    minute: "minute",
                    minutes: "minutes",
                    second: "second",
                    seconds: "seconds",
                    delimiter: " "
                };
                e.fn.preciseDiff = function(t) {
                    return e.preciseDiff(this, t)
                };
                e.preciseDiff = function(n, r) {
                    function d(e, n) {
                        return e + " " + t[n + (e === 1 ? "" : "s")]
                    }
                    var i = e(n),
                        s = e(r);
                    if (i.isSame(s)) {
                        return t.nodiff
                    }
                    if (i.isAfter(s)) {
                        var o = i;
                        i = s;
                        s = o
                    }
                    var u = s.year() - i.year();
                    var a = s.month() - i.month();
                    var f = s.date() - i.date();
                    var l = s.hour() - i.hour();
                    var c = s.minute() - i.minute();
                    var h = s.second() - i.second();
                    if (h < 0) {
                        h = 60 + h;
                        c--
                    }
                    if (c < 0) {
                        c = 60 + c;
                        l--
                    }
                    if (l < 0) {
                        l = 24 + l;
                        f--
                    }
                    if (f < 0) {
                        var p = e(s.year() + "-" + (s.month() + 1), "YYYY-MM").subtract("months", 1)
                            .daysInMonth();
                        if (p < i.date()) {
                            f = p + f + (i.date() - p)
                        } else {
                            f = p + f
                        }
                        a--
                    }
                    if (a < 0) {
                        a = 12 + a;
                        u--
                    }
                    var v = [];
                    if (u) {
                        v.push(u)
                    } else {
                        v.push(0)
                    }
                    if (a) {
                        v.push(a)
                    } else {
                        v.push(0)
                    }
                    if (f) {
                        v.push(f)
                    } else {
                        v.push(0)
                    }
                    // if(l){v.push(d(l,"hour"))}
                    // if(c){v.push(d(c,"minute"))}
                    // if(h){v.push(d(h,"second"))}

                    return v
                }
            })(moment)

            // $(document).ready(function() {
            var CSRF_TOKEN = $('#csrf-token').val();
            CKEDITOR.replace('diagnosa_permohonan_uji_klinik', {
                // Disable security notifications.
                versionCheck: false
            });

            $.fn.select2.defaults.set("theme", "classic");

            $("#pasien_permohonan_uji_klinik").select2({
                ajax: {
                    url: "{{ route('get-pasien-by-select') }}",
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: CSRF_TOKEN,
                            search: params.term
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
                placeholder: 'Pilih pasien',
                allowClear: true
            });

            $("#umurtahun_pasien_permohonan_uji_klinik").val('')
            $("#umurbulan_pasien_permohonan_uji_klinik").val('')
            $("#umurhari_pasien_permohonan_uji_klinik").val('')

            $(".date_birth").datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });

            $('.date_birth').datepicker('update', moment("YYYYY-MM-DD").toDate());

            // $('.datepicker').datepicker('update', moment("YYYYY-MM-DD").toDate());

            $(".date_birth").change(function() {
                var datevalue = $(this).val()
                getAge(datevalue);
            });

            $("#pasien_permohonan_uji_klinik").change(function() {
                $('#display-new-pasien').css('display', 'none');
                $('#display-detail-pasien-silaboy').css('display', 'block');
                $("#umurtahun_pasien_permohonan_uji_klinik").val('')
                $("#umurbulan_pasien_permohonan_uji_klinik").val('')
                $("#umurhari_pasien_permohonan_uji_klinik").val('')
                if ($("#pasien_permohonan_uji_klinik").val()) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('get-pasien-by-id') }}",
                        data: {
                            _token: CSRF_TOKEN,
                            pasien_id: $("#pasien_permohonan_uji_klinik").val()
                        },
                        dataType: "JSON",
                        success: function(response) {
                            console.log(response)

                            $('#id_satu_sehat_3').val(response.id_pasien_satu_sehat);
                            if ($('#id_satu_sehat_3').val() == "" || $('#id_satu_sehat_3')
                                .val() === '-') {
                                $('#id_satu_sehat_3').prop('readonly', false);
                            } else {
                                $('#id_satu_sehat_3').prop('readonly', true);
                            }

                            $('#nik_pasien_3').val(response.nik_pasien);
                            if ($('#nik_pasien_3').val() === "" || $('#nik_pasien_3').val() ===
                                '-') {
                                $('#nik_pasien_3').prop('readonly', false);
                            } else {
                                $('#nik_pasien_3').prop('readonly', true);
                            }

                            $('#no_rekammedis_detail_pasien').val(response
                                .no_rekammedis_pasien);
                            $('#nama_pasien_3').val(response.nama_pasien);
                            if ($('#nama_pasien_3').val() === "" || $('#nama_pasien_3')
                                .val() === '-') {
                                $('#nama_pasien_3').prop('readonly', false);
                            } else {
                                $('#nama_pasien_3').prop('readonly', true);
                            }

                            $("#gender_pasien_3").val(response.gender_pasien);
                            if ($('#gender_pasien_3').val() === "" || $('#gender_pasien_3')
                                .val() === '-') {
                                $('#gender_pasien_3').prop('readonly', false);
                            } else {
                                $('#gender_pasien_3').prop('readonly', true);
                            }

                            $('#tgllahir_pasien_3').val(response.tgllahir_pasien_normal);
                            if ($('#tgllahir_pasien_3').val() === "" || $('#tgllahir_pasien_3')
                                .val() === '-') {
                                $('#tgllahir_pasien_3').prop('readonly', false);
                            } else {
                                $('#tgllahir_pasien_3').prop('readonly', true);
                            }

                            $('#phone_pasien_3').val(response.phone_pasien);
                            if ($('#phone_pasien_3').val() === "" || $('#phone_pasien_3')
                                .val() === '-') {
                                $('#phone_pasien_3').prop('readonly', false);
                            } else {
                                $('#phone_pasien_3').prop('readonly', true);
                            }

                            $('#alamat_pasien_3').val(response.alamat_pasien);
                            if ($('#alamat_pasien_3').val() === "" || $('#alamat_pasien_3')
                                .val() === '-') {
                                $('#alamat_pasien_3').prop('readonly', false);
                            } else {
                                $('#alamat_pasien_3').prop('readonly', true);
                            }

                            $('#nopasien_permohonan_uji_klinik').val(response.nik_pasien);

                            $('#seccond_pasien_permohonan_uji_klinik').val($(
                                "#pasien_permohonan_uji_klinik").val());


                            // if ($('#nopasien_permohonan_uji_klinik').val() === "" || $('#nopasien_permohonan_uji_klinik').val() === '-'){
                            //   $('#nopasien_permohonan_uji_klinik').prop('readonly', false);
                            // }else {
                            //   $('#nopasien_permohonan_uji_klinik').prop('readonly', true);
                            // }
                            //
                            $('#tgllahir_pasien_permohonan_uji_klinik').val(response
                                .tgllahir_pasien_normal);
                            getAge(response.tgllahir_pasien_normal);

                            $("#display-detail-pasien input, #display-detail-pasien textarea")
                                .prop("disabled", true);
                            $('#display-new-pasien input, #display-new-pasien textarea').prop(
                                'disabled', true);
                        },
                        error: function() {
                            swal("Error!", "System gagal mendapatkan data pasien!", "error");
                        }
                    });
                } else {
                    $('#display-detail-pasien').css('display', 'none');
                }
            })

            // jika element tersebut kosong maka form umur kosong
            if ($('#tgllahir_pasien_permohonan_uji_klinik').val().length === 0) {
                $("#umurtahun_pasien_permohonan_uji_klinik").val('')
                $("#umurbulan_pasien_permohonan_uji_klinik").val('')
                $("#umurhari_pasien_permohonan_uji_klinik").val('')
            }

            // jika user mengisi data pasien baru
            // jika user mengisikan data pasien maka isian select2 kosong dan tmapilan detail pasien hilang
            $('.btn-new-pasien').click(function() {
                $('#display-new-pasien').css('display', 'block');
                $('#display-detail-pasien').css('display', 'none');


                $('.cancel-new-pasien').click(function() {
                    // reset form
                    $('#nik_pasien_2').val('');
                    $('#nama_pasien_2').val('');
                    $('#gender_pasien_2').prop('checked', true);
                    $('#tgllahir_pasien_2').val('');
                    $('#phone_pasien_2').val('');
                    $('#alamat_pasien_2').val('');

                    $("#umurtahun_pasien_permohonan_uji_klinik").val('');
                    $("#umurbulan_pasien_permohonan_uji_klinik").val('');
                    $("#umurhari_pasien_permohonan_uji_klinik").val('');

                    $('#nopasien_permohonan_uji_klinik').val('');

                    $("#pasien_permohonan_uji_klinik").prop("disabled", false);
                    $('#display-new-pasien').css('display', 'none');

                    location.reload();
                })

                $("#display-detail-pasien input, #display-detail-pasien textarea").prop("disabled", true);
                $('#display-detail-pasien-silaboy input, #display-detail-pasien-silaboy textarea').prop(
                    'disabled', true);
            });

            // mengisikan input nik
            $('#nik_pasien').keyup(function(e) {
                $('#nopasien_permohonan_uji_klinik').val($(this).val());
            });

            $('.datepicker').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });

            $('#tgllahir_pasien_2').keyup(function(e) {
                getAge($(this).val());
            });

            $('#tgllahir_pasien_2').change(function(e) {
              getAge($(this).val());
            });

            function getAge(dateString) {
                var today = moment().toDate();
                var birthDate = moment(dateString, "DD/MM/YYYY").toDate();

                var diff = moment.preciseDiff(today, birthDate, true);

                $("#umurtahun_pasien_permohonan_uji_klinik").val(diff[0])
                $("#umurbulan_pasien_permohonan_uji_klinik").val(diff[1])
                $("#umurhari_pasien_permohonan_uji_klinik").val(diff[2])
            }

        });


        $('.btn-simpan').on('click', function() {
            var $button = $(this); // Simpan referensi tombol simpan
            $button.prop('disabled', true); // Disable tombol simpan
            $button.html('Loading...'); // Ganti teks tombol dengan "Loading..."
            $('#form').ajaxSubmit({
                success: function(response) {
                    console.log((response))
                    if (response.status == true) {
                        swal({
                                title: "Success!",
                                text: response.pesan,
                                icon: "success"
                            })
                            .then(function() {

                                // Ambil nilai tanggal sampling, nama pasien, dan no registrasi
                                // const tgl_sampling = $('#tgl_sampling_permohonan_uji_klinik').val();
                                console.log(response)
                                const id_pasien = response.id;
                                //
                                // console.log(nama_pasien);
                                //
                                const no_reg = $('#noregister_permohonan_uji_klinik').val();

                                // Bangun URL target dengan menggunakan template string untuk kemudahan pembacaan
                                const base_url =
                                    '{{ url('elits-permohonan-uji-klinik-2/add-parameter') }}';
                                const target_url =
                                    `${base_url}?complete-step=1&id_pasien=${encodeURIComponent(id_pasien)}&no_reg=${encodeURIComponent(no_reg)}`;

                                // Alihkan ke URL target
                                window.location.href = target_url;

                            });
                    } else {
                        // Pastikan tombol diaktifkan kembali pada error
                        $button.prop('disabled', false); // Aktifkan kembali tombol simpan
                        $button.html('Simpan'); // Kembalikan teks tombol semula

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
                    // Pastikan tombol diaktifkan kembali pada error
                    $button.prop('disabled', false); // Aktifkan kembali tombol simpan
                    $button.html('Simpan'); // Kembalikan teks tombol semula
                    swal("Error!", "System gagal menyimpan!", "error");
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Function to calculate age in years, months, and days
            function calculateAge(birthDate) {

                // console.log(birthDate);
                birthDate = moment(birthDate, 'DD-MM-YYYY', true).format("YYYY-MM-DD");
                const today = new Date();
                const birth = new Date(birthDate);
                let years = today.getFullYear() - birth.getFullYear();
                let months = today.getMonth() - birth.getMonth();
                let days = today.getDate() - birth.getDate();

                console.log(birth);
                console.log(today);
                console.log(days);

                if (days < 0) {
                    months--;
                    days += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                }
                if (months < 0) {
                    years--;
                    months += 12;
                }

                return {
                    years,
                    months,
                    days
                };
            }

            // Fetch patient data and populate form
            $('#fetchPatientsBtn').on('click', function() {
                let formData = $('#searchForm').serialize();

                $.ajax({
                    url: "{{ route('get-list-pasien-satu-sehat') }}",
                    method: 'GET',
                    data: formData,
                    success: function(response) {
                        let tableBody = $('#patientsTableBody');
                        tableBody.empty();

                        response.forEach(function(patient) {
                            let row = `<tr>
                      <td>${patient.nik || 'N/A'}</td>
                      <td>${patient.gender=="L"?"Laki - Laki":"Perempuan" || 'N/A'}</td>
                      <td>${patient.id || 'N/A'}</td>
                      <td class="address">${patient.address || 'N/A'}</td>
                      <td>${patient.birthDate || 'N/A'}</td>
                      <td>${patient.name || 'N/A'}</td>
                      <td>${patient.telepon || 'N/A'}</td>
                      <td><button type="button" class="btn btn-success pilih-pasien" data-patient='${JSON.stringify(patient)}'>Pilih</button></td>
                    </tr>`;
                            tableBody.append(row);
                        });

                        $('#patientsModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching patient data:', error);
                    }
                });
            });

            // Untuk memilih pasien
            $(document).on('click', '.pilih-pasien', function() {
                let patient = $(this).data('patient');
                let age = calculateAge(patient.birthDate);

                $('#id_satu_sehat_1').val(patient.id || 'N/A');
                if ($('#id_satu_sehat_1').val() === 'N/A') {
                    $('#id_satu_sehat_1').prop('readonly', false);
                } else {
                    $('#id_satu_sehat_1').prop('readonly', true);
                }

                $('#nik_pasien_1').val(patient.nik || 'N/A');
                if ($('#nik_pasien_1').val() === 'N/A') {
                    $('#nik_pasien_1').prop('readonly', false);
                } else {
                    $('#nik_pasien_1').prop('readonly', true);
                }

                $('#nama_pasien_1').val(patient.name || 'N/A');
                if ($('#nama_pasien_1').val() === 'N/A') {
                    $('#nama_pasien_1').prop('readonly', false);
                } else {
                    $('#nama_pasien_1').prop('readonly', true);
                }

                let gender = patient.gender === 'L' ? 'Laki-Laki' : 'Perempuan'

                $('#gender_pasien_1').val(gender || 'N/A');
                if ($('#gender_pasien_1').val() === 'N/A') {
                    $('#gender_pasien_1').prop('readonly', false);
                } else {
                    $('#gender_pasien_1').prop('readonly', true);
                }

                $('#tgllahir_pasien_1').val(patient.birthDate || 'N/A');
                if ($('#tgllahir_pasien_1').val() === 'N/A') {
                    $('#tgllahir_pasien_1').prop('readonly', false);
                } else {
                    $('#tgllahir_pasien_1').prop('readonly', true);
                }


                $('#phone_pasien_1').val(patient.telepon || 'N/A');
                if ($('#phone_pasien_1').val() === 'N/A') {
                    $('#phone_pasien_1').prop('readonly', false);
                } else {
                    $('#phone_pasien_1').prop('readonly', true);
                }

                $('#alamat_pasien_1').val(patient.address || 'N/A');
                if ($('#alamat_pasien_1').val() === 'N/A') {
                    $('#alamat_pasien_1').prop('readonly', false);
                } else {
                    $('#alamat_pasien_1').prop('readonly', true);
                }

                // Update age fields
                $('#umurtahun_pasien_permohonan_uji_klinik').val(age.years);
                $('#umurbulan_pasien_permohonan_uji_klinik').val(age.months);
                $('#umurhari_pasien_permohonan_uji_klinik').val(age.days);

                // Show the patient detail form
                $('#display-detail-pasien').show();

                $('#display-new-pasien input, #display-new-pasien textarea').prop('disabled', true);
                $('#display-detail-pasien-silaboy input, #display-detail-pasien-silaboy textarea').prop(
                    'disabled', true);

                // Close the modal
                $('#patientsModal').modal('hide');
            });

            // Filter addresses based on search input
            $('#searchAddress').on('keyup', function() {
                let value = $(this).val().toLowerCase();
                $('#patientsTableBody tr').filter(function() {
                    $(this).toggle($(this).find('.address').text().toLowerCase().indexOf(value) > -
                        1);
                });
            });
        });

        // Form Perwakilan
        document.getElementById('flexSwitchCheckDefault').addEventListener('change', function() {
            var formPerwakilan = document.getElementById('form_perwakilan');
            if (this.checked) {
                $('#form_perwakilan input, #form_perwakilan textarea').prop('disabled', false)
                formPerwakilan.style.display = 'block';
            } else {
                $('#form_perwakilan input, #form_perwakilan textarea').prop('disabled', true)
                formPerwakilan.style.display = 'none';
            }
        });

        // Form Search Satu Sehat
        $('#button_search_satu_sehat').on('click', function() {
            $('#search_satu_sehat').css('display', 'block');
            $('#button_action_add').css('display', 'none');

        })

        $('#close_button_search_satu_sehat').on('click', function() {
            $('#search_satu_sehat').css('display', 'none');
            $('#button_action_add').css('display', 'block');
            $('#display-detail-pasien').css('display', 'none');

            location.reload();
        })

        // Form Search Silaboy
        $('#button_search_silaboy').on('click', function() {
            $('#search_silaboy').css('display', 'block');
            $('#button_action_add').css('display', 'none');
        })

        $('#close_button_silaboy').on('click', function() {
            $('#search_silaboy').css('display', 'none');
            $('#button_action_add').css('display', 'block');
            $('#display-detail-pasien-silaboy').css('display', 'none');

            location.reload();
        })
    </script>
@endsection
