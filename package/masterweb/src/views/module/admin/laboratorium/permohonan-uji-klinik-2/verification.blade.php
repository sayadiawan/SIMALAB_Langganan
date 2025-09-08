@extends('masterweb::template.admin.layout')
@section('title')
    Verifikasi Klinik
@endsection

@section('content')



    <style>
        .ui-datepicker {
            position: relative;
            z-index: 100000;
        }

        .sign-opt {
          width: 200px;
        }

        .sign-opt:hover{
          background-color: #dedcdc;
        }

        .my-custom-popup-class {
          padding-top: 2.5rem !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="">
                    <div class="template-demo">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                                        Beranda</a>
                                </li>

                                <li class="breadcrumb-item">
                                    <a href="{{ url('/elits-permohonan-uji-klinik-2') }}">Permohonan Uji Klinik
                                        Management</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <span>Verifikasi</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit tanggal cetak verifikasi -->
    <div class="modal fade" id="editTanggalCetakVerifikasi" tabindex="-1" role="dialog"
        aria-labelledby="editTanggalCetakVerifikasiLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTanggalCetakVerifikasiLabel">Tanggal Cetak Verifikasi</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="agendaForm"
                    action="{{ route('elits-permohonan-uji-klinik-2.print_verifikasi', $item->id_permohonan_uji_klinik) }}"
                    method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="tanggal-cetak-verifikasi">Tanggal</label>
                            <input type="date" class="form-control datetime" id="tanggal-cetak-verifikasi"
                                name="tanggal_cetak_verifikasi">
                        </div>
                        <div class="form-group">
                          <label for="signOption">Metode Tanda Tangan</label>
                          <select class="form-control" name="signOption" id="signOption">
                            <option value="0">Tanda Tangan Manual</option>
                            <option value="1">Tanda Tangan Elektronik</option>
                          </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cetak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Input NIK dan Password -->
    <div class="modal fade" id="inputNikAndPasword" tabindex="-1" aria-labelledby="inputNikAndPassword"
         aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputNikAndPassword">Input NIK dan Password BSRE</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="form-group">
                <label for="nikPetugas">NIK</label>
                <input type="text" class="form-control" name="nik" id="nikPetugas" placeholder="Nomor Induk Kependudukan" required>
              </div>
              <div class="form-group mt-2">
                <label for="passwordPetugas">Password</label>
                <input type="text" class="form-control" name="password" id="passwordPetugas" placeholder="Password" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="submitNikAndPassword()">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal Input NIK dan Password -->
    <div class="modal fade" id="inputNikAndPasword" tabindex="-1" aria-labelledby="inputNikAndPassword"
         aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputNikAndPassword">Input NIK dan Password BSRE</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
              <div class="form-group">
                <label for="nikPetugas">NIK</label>
                <input type="text" class="form-control" name="nik" id="nikPetugas" placeholder="Nomor Induk Kependudukan" required>
              </div>
              <div class="form-group mt-2">
                <label for="passwordPetugas">Password</label>
                <input type="text" class="form-control" name="password" id="passwordPetugas" placeholder="Password" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" onclick="submitNikAndPassword()">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="signOptionModal" tabindex="-1" aria-labelledby="signOptionTitle" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="signOptionTitle">Pilih metode tanda tangan</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="d-flex mx-auto m-2 justify-content-around">
            <a href="{{ route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', [$item->id_permohonan_uji_klinik]) }}?signoption=0"
               target="_blank">
                <button class="btn text-center m-2 p-2 sign-opt">
                  <img src="{{ asset('assets/admin/images/sign-icon.png') }}" width="80" height="80">
                  <h5 class="mt-2">Tanda Tangan Manual</h5>
                </button>
            </a>
            <a href="{{ route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', [$item->id_permohonan_uji_klinik]) }}?signoption=1"
               target="_blank">
                <button class="btn text-center m-2 p-2 sign-opt">
                  <img src="{{ asset('assets/admin/images/logo/logo-bsre.png') }}" width="80" height="80">
                  <h5 class="mt-2">Tanda Tangan Elektronik</h5>
                </button>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-12">
                    <H4 class="d-inline-block  float-left margin-top">Verifikasi</H4>
                    <button type="button" class="btn btn-outline-success btn-rounded float-right" data-toggle="modal" data-target="#signOptionModal">
                      <i class="fa fa-print"></i> Cetak Hasil
                    </button>
                    <button type="button" class="btn btn-outline-success btn-rounded float-right mr-2" data-toggle="modal"
                        data-target="#editTanggalCetakVerifikasi">
                        <i class="fa fa-print"></i> Cetak Verifikasi
                    </button>

                </div>
            </div>
        </div>
        @if (session('error-bsre'))
          <div class="col-12">
            <div class="alert alert-danger">
              {{ session('error-bsre') }}
            </div>
          </div>
        @endif
        @if(session('error-laporan'))
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                title: "Gagal Melakukan Tanda Tangan Elektronik",
                text: "Terjadi kesalahan saat melakukan tanda tangan elektronik. Silakan coba lagi.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Coba Lagi",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                  Swal.showLoading();
                  return new Promise((resolve) => {
                    setTimeout(() => {
                      resolve();
                      window.location.href = "{{ route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', [$item->id_permohonan_uji_klinik]) }}?signoption=1";
                    }, 1000);
                  });
                },
                customClass: {
                  popup: 'my-custom-popup-class'
                }
              });
            });
          </script>
        @endif
        @if(session('error-verifikasi'))
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              Swal.fire({
                title: "Gagal Melakukan Tanda Tangan Elektronik",
                text: "Terjadi kesalahan saat melakukan tanda tangan elektronik. Silakan coba lagi.",
                icon: "warning",
                customClass: {
                  popup: 'my-custom-popup-class'
                }
              });
            });
          </script>
        @endif
      <div class="card-body">
            <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="d-inline-block card-subtitle text-muted  margin-top"
                            style="margin-top:.2rem; background:blue;color:white !important;padding:5px;border-radius:5px;">
                            Laboratorium Klinik
                        </h6>
                        @if ($item->is_prolanis_gula == 1)
                            <h6 class="d-inline-block card-subtitle text-muted  margin-top"
                                style="margin-top:.2rem; background:rgb(255, 30, 0);color:white !important;padding:5px;border-radius:5px;">
                                Prolanis Gula
                            </h6>
                        @endif
                        @if ($item->is_prolanis_urine == 1)
                            <h6 class="d-inline-block card-subtitle text-muted  margin-top"
                                style="margin-top:.2rem; background:rgb(255, 174, 0);color:white !important;padding:5px;border-radius:5px;">
                                Prolanis Urine
                            </h6>
                        @endif
                        @if ($item->is_haji == 1)
                            <h6 class="d-inline-block card-subtitle text-muted  margin-top"
                                style="margin-top:.2rem; background:green;color:white !important;padding:5px;border-radius:5px;">
                                Haji
                            </h6>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="250px">No. Register</th>
                                    <td>{{ $item->noregister_permohonan_uji_klinik }}</td>

                                    <input type="hidden" name="permohonan_uji_klinik" id="permohonan_uji_klinik"
                                        value="{{ $item->id_permohonan_uji_klinik }}" readonly>
                                </tr>

                                <tr>
                                    <th width="250px">No. Rekam Medis</th>
                                    <td>
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $item->pasien->tgllahir_pasien)->format('dmY') . str_pad((int) $item->pasien->no_rekammedis_pasien, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Tgl. Register</th>
                                    <td>{{ \Carbon\Carbon::parse($tgl_register)->locale('id')->isoFormat('D MMMM YYYY') }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Nama Pasien</th>
                                    <td>{{ $item->pasien->nama_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Umur/Jenis Kelamin</th>
                                    <td>
                                        {{ $item->umurtahun_pasien_permohonan_uji_klinik . ' tahun ' . $item->umurbulan_pasien_permohonan_uji_klinik . ' bulan ' . $item->umurhari_pasien_permohonan_uji_klinik . ' hari' }}
                                        / {{ $item->pasien->gender_pasien == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>
                @if (session('status'))
                    <div class="col-12">
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    </div>
                @endif
                @if (session('error'))
                    <div class="col-12">
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                @if ($errors->has('error'))
                    <div class="alert alert-danger">
                        <strong>Error:</strong> {{ $errors->first('error') }}
                    </div>
                @endif
                <div class="col-md-6">
                    <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col" class="border border-primary">Jenis Kegiatan Lab Klinik</th>
                            <th scope="col" class="border border-primary">Tanggal Mulai / Jam</th>
                            <th scope="col" class="border border-primary">Tanggal Selesai / Jam</th>
                            <th scope="col" class="border border-primary">Nama Petugas</th>
                            <th scope="col" class="text-center border border-primary">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr id="registrasi">
                            <th scope="row">Pendaftaran / Registrasi</th>
                            @if (isset($listVerifications[1]))
                                <td>{{ $listVerifications[1]->start_date }}</td>
                                <td>{{ $listVerifications[1]->stop_date }}</td>
                                <td>{{ $listVerifications[1]->nama_petugas }}</td>
                                <td class="text-center">
                                    <svg id="toggle-registrasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                        width="48" height="48" viewBox="0 0 48 48">
                                        <path fill="#c8e6c9"
                                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                        </path>
                                        <path fill="#4caf50"
                                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                        </path>
                                    </svg>
                                </td>
                            @endif
                        </tr>
                        <tr id="registrasi-update" style="display: none;">
                            <th scope="row">Pendaftaran / Registrasi</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formRegistrasi">
                                @csrf
                                <input type="number" value="{{ 1 }}" name="verification_step" hidden>
                                <input type="text" value="{{ $listVerifications[1]->nama_petugas }}"
                                    name="nama_petugas" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[1]->start_date) ? $listVerifications[1]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[1]->stop_date) ? $listVerifications[1]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    <select name="nama_petugas" id="namaPetugasRegistrasi" required>
                                        @foreach (['Umi Sulistiyana', 'Liyana'] as $nama_petugas)
                                            <option value="{{ $nama_petugas }}"
                                                {{ isset($listVerifications[1]->nama_petugas) && $listVerifications[1]->nama_petugas == $nama_petugas
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $nama_petugas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasRegistrasi').value, 'formRegistrasi')">Verifikasi</button>
                                </td>
                            </form>
                        </tr>
                        <tr id="sample">
                            <th scope="row">Pengambilan Sample</th>

                            @if (isset($listVerifications[6]))
                                <td>{{ $listVerifications[6]->start_date }}</td>
                                <td>{{ $listVerifications[6]->stop_date }}</td>
                                <td>{{ $listVerifications[6]->nama_petugas }}</td>
                                @if ($listVerifications[6]->is_done == 0)
                                    <form
                                        action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                        method="post" class="formSample1">
                                        @csrf
                                        <input type="number" value="{{ 6 }}" name="verification_step"
                                            hidden>
                                        <input type="text" value="{{ $listVerifications[6]->start_date }}"
                                            name="start_date" hidden>
                                        <input type="text" value="{{ $listVerifications[6]->stop_date }}"
                                            name="stop_date" hidden>
                                        <input type="text" id="namaPetugasSample1" value="{{ $listVerifications[6]->nama_petugas }}"
                                            name="nama_petugas" hidden>
                                        <td class="text-center"><button type="submit"
                                                class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasSample1').value, 'formSample1')">Verifikasi</button>
                                    </form>
                                    <button type="button" class="btn btn-primary" disabled>Input</button>
                                @else
                                    <td class="text-center">
                                        <svg id="toggle-sample" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                            width="48" height="48" viewBox="0 0 48 48">
                                            <path fill="#c8e6c9"
                                                d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                            </path>
                                            <path fill="#4caf50"
                                                d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                            </path>
                                        </svg>
                                    </td>
                                @endif
                            @else
                                <form
                                    action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                    method="post" class="formPengambilanSampel">
                                    @csrf
                                    <input type="number" value="{{ 6 }}" name="verification_step" hidden>
                                    <td><input type="datetime-local" class="form-control" name="start_date"
                                            id="start_date_sample" required></td>
                                    <td><input type="datetime-local" class="form-control" name="stop_date"
                                            id="stop_date_sample" required></td>
                                    <td>
                                        <select name="nama_petugas" id="namaPetugasPengambilanSampel" required>
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[5]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center"><button type="submit"
                                            class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasPengambilanSampel').value, 'formPengambilanSampel')">Verifikasi</button>
                                </form>
                                <a
                                    href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-sample', $item->id_permohonan_uji_klinik) }}"><button
                                        type="button" class="btn btn-primary">Input</button></a>
                                </td>
                            @endif
                        </tr>
                        <tr id="sample-update" style="display:none;">
                            <th scope="row">Pengambilan Sample</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formPengambilanSampelUpdate">
                                @csrf
                                <input type="number" value="{{ 6 }}" name="verification_step" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[6]->start_date) ? $listVerifications[6]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[6]->stop_date) ? $listVerifications[6]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    <select name="nama_petugas" id="namaPetugasPengambilanSampelUpdate" required>
                                        @php
                                            $list_name_petugas = explode(', ', $verificationActivity[5]->klinik);
                                        @endphp
                                        @foreach ($list_name_petugas as $nama_petugas)
                                            <option value="{{ $nama_petugas }}"
                                                {{ isset($listVerifications[6]->nama_petugas) && $listVerifications[6]->nama_petugas == $nama_petugas
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $nama_petugas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center"><button type="submit"
                                        class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasPengambilanSampelUpdate').value, 'formPengambilanSampelUpdate')">Verifikasi</button>
                            </form>
                            <a
                                href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-sample', $item->id_permohonan_uji_klinik) }}"><button
                                    type="button" class="btn btn-primary">Input</button></a>
                            </td>
                        </tr>
                        <tr id="analitik">
                            <th scope="row">Pemeriksaan / Analitik</th>
                            @if (isset($listVerifications[2]))
                                <td>{{ $listVerifications[2]->start_date }}</td>
                                <td>{{ $listVerifications[2]->stop_date }}</td>
                                <td>{{ $listVerifications[2]->nama_petugas }}</td>
                                <td class="text-center">
                                    <svg id="toggle-analitik" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                        width="48" height="48" viewBox="0 0 48 48">
                                        <path fill="#c8e6c9"
                                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                        </path>
                                        <path fill="#4caf50"
                                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                        </path>
                                    </svg>
                                </td>
                            @else
                                <form
                                    action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                    method="post" class="formAnalitik">
                                    @csrf
                                    <input type="number" value="{{ 2 }}" name="verification_step" hidden>
                                    <td><input type="datetime-local" class="form-control" name="start_date"
                                            id="start_date_pemeriksaan" required></td>
                                    <td><input type="datetime-local" class="form-control" name="stop_date"
                                            id="stop_date_pemeriksaan" required></td>
                                    <td>
                                        <select name="nama_petugas" id="namaPetugasAnalitik" required>
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[1]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        @if (isset($listVerifications[6]))
                                            @if ($listVerifications[6]->is_done == 1)
                                                <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasAnalitik').value, 'formAnalitik')">Verifikasi</button>
                                            @else
                                                <button type="submit" class="btn btn-success"
                                                    disabled>Verifikasi</button>
                                            @endif
                                        @else
                                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                        @endif
                                    </td>
                                </form>
                            @endif
                        </tr>
                        <tr id="analitik-update" style="display: none;">
                            <th scope="row">Pemeriksaan / Analitik</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formAnalitikUpdate">
                                @csrf
                                <input type="number" value="{{ 2 }}" name="verification_step" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[2]->start_date) ? $listVerifications[2]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[2]->stop_date) ? $listVerifications[2]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    <select name="nama_petugas" id="namaPetugasAnalitikUpdate" required>
                                        @php
                                            $list_name_petugas = explode(', ', $verificationActivity[1]->klinik);
                                        @endphp
                                        @foreach ($list_name_petugas as $nama_petugas)
                                            <option value="{{ $nama_petugas }}"
                                                {{ isset($listVerifications[2]->nama_petugas) && $listVerifications[2]->nama_petugas == $nama_petugas
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $nama_petugas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    @if (isset($listVerifications[6]))
                                        @if ($listVerifications[6]->is_done == 1)
                                            <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasAnalitikUpdate').value, 'formAnalitikUpdate')">Verifikasi</button>
                                        @else
                                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                        @endif
                                    @else
                                        <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                    @endif
                                </td>
                            </form>
                        </tr>
                        <tr id="hasil-px">
                            <th scope="row">Input / Output Hasil Px</th>
                            @if (isset($listVerifications[3]))
                                <td>{{ $listVerifications[3]->start_date }}</td>
                                <td>{{ $listVerifications[3]->stop_date }}</td>
                                <td>
                                    @if ($listVerifications[3]->nama_petugas == '' || $listVerifications[3]->nama_petugas == null)
                                        <select name="nama_petugas" id="nama_petugas_select" required
                                            onchange="updateNamaPetugas()">
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[2]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $listVerifications[3]->nama_petugas }}
                                    @endif
                                </td>
                                @if ($listVerifications[3]->is_done == 0)
                                    <form
                                        action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                        method="post" class="formHasil">
                                        @csrf
                                        <input type="number" value="{{ 3 }}" name="verification_step"
                                            hidden>
                                        <input type="text" value="{{ $listVerifications[3]->start_date }}"
                                            name="start_date" hidden>
                                        <input type="text" value="{{ $listVerifications[3]->stop_date }}"
                                            name="stop_date" hidden>
                                        <input type="text" id="nama_petugas_input"
                                            value="{{ $listVerifications[3]->nama_petugas }}" name="nama_petugas" hidden>
                                        <td class="text-center"><button type="submit"
                                                class="btn btn-success" onclick="checkNikAndPassword('{{ $listVerifications[3]->nama_petugas }}', 'formHasil')">Verifikasi</button>
                                    </form>
                                    <button type="button" class="btn btn-primary" disabled>Input</button>
                                @else
                                    <td class="text-center">
                                        <svg id="toggle-hasil-px" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                            width="48" height="48" viewBox="0 0 48 48">
                                            <path fill="#c8e6c9"
                                                d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                            </path>
                                            <path fill="#4caf50"
                                                d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                            </path>
                                        </svg>
                                    </td>
                                @endif
                            @else
                                <form
                                    action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                    method="post" class="formHasilPx">
                                    @csrf
                                    <input type="number" value="{{ 3 }}" name="verification_step" hidden>
                                    <td><input type="datetime-local" class="form-control" name="start_date"
                                            id="start_date_input" required></td>
                                    <td><input type="datetime-local" class="form-control" name="stop_date"
                                            id="stop_date_input" required></td>
                                    <td>
                                        <select name="nama_petugas" id="namaPetugasHasilPx" required>
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[2]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        @if (isset($listVerifications[2]))
                                            @if ($listVerifications[2]->is_done == 1)
                                                <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasHasilPx').value, 'formHasilPx')">Verifikasi</button>
                                </form>
                                <a
                                    href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                    <button type="button" class="btn btn-primary">Input</button>
                                </a>
                            @else
                                <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                </form>
                                <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                            @endif
                        </tr>
                        <tr id="hasil-px-update" style="display: none;">
                            <th scope="row">Input / Output Hasil Px</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formHasilPxUpdate">
                                @csrf
                                <input type="number" value="{{ 3 }}" name="verification_step" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[3]->start_date) ? $listVerifications[3]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[3]->stop_date) ? $listVerifications[3]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    <select name="nama_petugas" id="namaPetugasHasilPxUpdate" required>
                                        @php
                                            $list_name_petugas = explode(', ', $verificationActivity[2]->klinik);
                                        @endphp
                                        @foreach ($list_name_petugas as $nama_petugas)
                                            <option value="{{ $nama_petugas }}"
                                                {{ isset($listVerifications[3]->nama_petugas) && $listVerifications[3]->nama_petugas == $nama_petugas
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $nama_petugas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    @if (isset($listVerifications[2]))
                                        @if ($listVerifications[2]->is_done == 1)
                                            <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasHasilPxUpdate').value, 'formHasilPxUpdate')">Verifikasi</button>
                            </form>
                            <a
                                href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                <button type="button" class="btn btn-primary">Input</button>
                            </a>
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                        </tr>
                        <tr id="verifikasi">
                            <th scope="row">Verifikasi</th>
                            @if (isset($listVerifications[4]))
                                <td>{{ $listVerifications[4]->start_date }}</td>
                                <td>{{ $listVerifications[4]->stop_date }}</td>
                                <td>
                                    @if ($listVerifications[4]->nama_petugas == '' || $listVerifications[4]->nama_petugas == null)
                                        <select name="nama_petugas" id="nama_petugas_select" required
                                            onchange="updateNamaPetugas()">
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[3]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        {{ $listVerifications[4]->nama_petugas }}
                                    @endif
                                </td>
                                @if ($listVerifications[4]->is_done == 0)
                                    <form
                                        action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                        method="post" class="formVerifikasi">
                                        @csrf
                                        <input type="number" value="{{ 4 }}" name="verification_step"
                                            hidden>
                                        <input type="text" value="{{ $listVerifications[4]->start_date }}"
                                            name="start_date" hidden>
                                        <input type="text" value="{{ $listVerifications[4]->stop_date }}"
                                            name="stop_date" hidden>
                                        <input type="text" id="nama_petugas_input"
                                            value="{{ $listVerifications[4]->nama_petugas }}" name="nama_petugas" hidden>
                                        <td class="text-center"><button type="submit"
                                                class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('nama_petugas_input').value, 'formVerifikasi')">Verifikasi</button>
                                    </form>
                                    <button type="button" class="btn btn-primary" disabled>Input</button>
                                @else
                                    <td class="text-center">
                                        <svg id="toggle-verifikasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                            width="48" height="48" viewBox="0 0 48 48">
                                            <path fill="#c8e6c9"
                                                d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                            </path>
                                            <path fill="#4caf50"
                                                d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                            </path>
                                        </svg>
                                    </td>
                                @endif
                            @else
                                <form
                                    action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                    method="post" class="formVerifikasi2">
                                    @csrf
                                    <input type="number" value="{{ 4 }}" name="verification_step" hidden>
                                    <td><input type="datetime-local" class="form-control" name="start_date"
                                            id="start_date_verifikasi" required></td>
                                    <td><input type="datetime-local" class="form-control" name="stop_date"
                                            id="stop_date_verifikasi" required></td>
                                    <td>
                                        <select name="nama_petugas" id="namaPetugasVerifikasi" required>
                                            @php
                                                $list_name_petugas = explode(', ', $verificationActivity[3]->klinik);
                                            @endphp
                                            @foreach ($list_name_petugas as $nama_petugas)
                                                <option value="{{ $nama_petugas }}">{{ $nama_petugas }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        @if (isset($listVerifications[3]))
                                            @if ($listVerifications[3]->is_done == 1)
                                                <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasVerifikasi').value, 'formVerifikasi2')">Verifikasi</button>
                                </form>
                                <a
                                    href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                    <button type="button" class="btn btn-primary">Input</button>
                                </a>
                            @else
                                <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                </form>
                                <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                            @endif
                        </tr>
                        <tr id="verifikasi-update" style="display: none;">
                            <th scope="row">Verifikasi</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formVerifikasi2Update">
                                @csrf
                                <input type="number" value="{{ 4 }}" name="verification_step" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[4]->start_date) ? $listVerifications[4]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[4]->stop_date) ? $listVerifications[4]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    <select name="nama_petugas" id="namaPetugasVerifikasiUpdate" required>
                                        @php
                                            $list_name_petugas = explode(', ', $verificationActivity[3]->klinik);
                                        @endphp
                                        @foreach ($list_name_petugas as $nama_petugas)
                                            <option value="{{ $nama_petugas }}"
                                                {{ isset($listVerifications[4]->nama_petugas) && $listVerifications[4]->nama_petugas == $nama_petugas
                                                    ? 'selected'
                                                    : '' }}>
                                                {{ $nama_petugas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="text-center">
                                    @if (isset($listVerifications[3]))
                                        @if ($listVerifications[3]->is_done == 1)
                                            <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasVerifikasiUpdate').value, 'formVerifikasi2Update')">Verifikasi</button>
                            </form>
                            <a
                                href="{{ route('elits-permohonan-uji-klinik-2.create-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                <button type="button" class="btn btn-primary">Input</button>
                            </a>
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                        </tr>
                        <tr id="validasi">
                            <th scope="row">Validasi</th>
                            @if (isset($listVerifications[5]))
                                <td>{{ $listVerifications[5]->start_date }}</td>
                                <td>{{ $listVerifications[5]->stop_date }}</td>
                                <td>dr. Muharyati</td>
                                <td class="text-center">
                                    <svg id="toggle-validasi" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                        width="48" height="48" viewBox="0 0 48 48">
                                        <path fill="#c8e6c9"
                                            d="M44,24c0,11.045-8.955,20-20,20S4,35.045,4,24S12.955,4,24,4S44,12.955,44,24z">
                                        </path>
                                        <path fill="#4caf50"
                                            d="M34.586,14.586l-13.57,13.586l-5.602-5.586l-2.828,2.828l8.434,8.414l16.395-16.414L34.586,14.586z">
                                        </path>
                                    </svg>
                                </td>
                            @else
                                <form
                                    action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                    method="post" class="formValidasi">
                                    @csrf
                                    <input type="number" value="{{ 5 }}" name="verification_step" hidden>
                                    <td><input type="datetime-local" class="form-control" name="start_date"
                                            id="start_date_validasi" required></td>
                                    <td><input type="datetime-local" class="form-control" name="stop_date"
                                            id="stop_date_validasi" required></td>
                                    <td>
                                        @php
                                            $day = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tgl_register)
                                                ->dayName;

                                            if ($day == 'Selasa' || $day == "Jum'at") {
                                                # code...
                                                // dd($day);
                                            }

                                        @endphp
                                        <select name="nama_petugas" id="namaPetugasValidasi" required>
                                            <option value="dr. DINI NURANI KUSUMASTUTI. Sp.PK"
                                                @if ($day == 'Selasa' || $day == "Jum'at") selected @endif>Penanggung jawab Lab.
                                                Klinik
                                            </option>
                                            <option value="dr. Muharyati"
                                                @if ($day != 'Selasa' && $day != "Jum'at") selected @endif>Diotorisasi
                                                oleh dr.
                                                Muharyati</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        @if (isset($listVerifications[4]))
                                            @if ($listVerifications[4]->is_done == 1)
                                                <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasValidasi').value, 'formValidasi')">Verifikasi</button>
                                </form>
                                <a
                                    href="{{ route('elits-permohonan-uji-klinik-2.disabled-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                    <button type="button" class="btn btn-primary">Input</button>
                                </a>
                            @else
                                <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                                </form>
                                <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                            @endif
                        </tr>
                        <tr id="validasi-update" style="display: none;">
                            <th scope="row">Validasi</th>
                            <form
                                action="{{ route('elits-permohonan-uji-klinik-2.verification-analytic', [$item->id_permohonan_uji_klinik]) }}"
                                method="post" class="formValidasiUpdate">
                                @csrf
                                <input type="number" value="{{ 5 }}" name="verification_step" hidden>
                                <td><input type="datetime-local" class="form-control" name="start_date"
                                        value="{{ isset($listVerifications[5]->start_date) ? $listVerifications[5]->start_date : '' }}"
                                        required></td>
                                <td><input type="datetime-local" class="form-control" name="stop_date"
                                        value="{{ isset($listVerifications[5]->stop_date) ? $listVerifications[5]->stop_date : '' }}"
                                        required></td>
                                <td>
                                    @php
                                        $day = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tgl_register)->dayName;

                                        if ($day == 'Selasa' || $day == "Jum'at") {
                                            # code...
                                            // dd($day);
                                        }

                                  @endphp
                                  <select name="nama_petugas" id="namaPetugasValidasiUpdate" required>
                                    <option value="dr. DINI NURANI KUSUMASTUTI. Sp.PK"
                                            @if ($day == 'Selasa' || $day == "Jum'at") selected @endif>Penanggung jawab Lab.
                                            Klinik
                                        </option>
                                        <option value="dr. Muharyati" @if ($day != 'Selasa' && $day != "Jum'at") selected @endif>
                                            Diotorisasi
                                            oleh dr.
                                            Muharyati</option>
                                    </select>
                                            Klinik
                                        </option>
                                        <option value="dr. Muharyati" @if ($day != 'Selasa' && $day != "Jum'at") selected @endif>
                                            Diotorisasi
                                            oleh dr.
                                            Muharyati</option>
                                    </select>
                                </td>
                                <td class="text-center">
                                    @if (isset($listVerifications[4]))
                                        @if ($listVerifications[4]->is_done == 1)
                                            <button type="submit" class="btn btn-success" onclick="checkNikAndPassword(document.getElementById('namaPetugasValidasiUpdate').value, 'formValidasiUpdate')">Verifikasi</button>
                            </form>
                            <a
                                href="{{ route('elits-permohonan-uji-klinik-2.disabled-permohonan-uji-analis2', $item->id_permohonan_uji_klinik) }}">
                                <button type="button" class="btn btn-primary">Input</button>
                            </a>
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                        @else
                            <button type="submit" class="btn btn-success" disabled>Verifikasi</button>
                            </form>
                            <button type="button" class="btn btn-primary" disabled>Input</button>
                            @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).scrollTop($(document).height());

        document.addEventListener("DOMContentLoaded", function() {
            let pendaftaranStop, pemeriksaanStart, pemeriksaanStop, inputStart, inputStop, verifikasiStart,
                verifikasiStop, validasiStart, validasiStop;

            @if (isset($listVerifications[1]))
                pendaftaranStop = new Date("{{ $listVerifications[1]->stop_date }}");
            @else
                pendaftaranStop = new Date();
            @endif

            // 1. Sample
            @if (isset($listVerifications[6]))
                sampleStart = new Date("{{ $listVerifications[6]->start_date }}");
                sampleStop = new Date("{{ $listVerifications[6]->stop_date }}");
            @else
                sampleStart = new Date(pendaftaranStop);
                sampleStart.setHours(sampleStart.getHours()); //  Pendaftaran
                // sampleStop = new Date(sampleStart.getTime() + 5 * 60000); // +1 jam
                sampleStop = new Date(sampleStart);
                sampleStop.setHours(sampleStart.getHours() + 1);
            @endif

            // 2. Pemeriksaan
            @if (isset($listVerifications[2]))
                pemeriksaanStart = new Date("{{ $listVerifications[2]->start_date }}");
                pemeriksaanStop = new Date("{{ $listVerifications[2]->stop_date }}");
            @else
                pemeriksaanStart = new Date(sampleStop);
                // pemeriksaanStart.setDate(pemeriksaanStart.getDate()); // Pendaftaran Stop
                pemeriksaanStop = new Date(pemeriksaanStart);
                // console.log(pemeriksaanStop);
                pemeriksaanStop = new Date(pemeriksaanStop.getTime() + 1 *
                    3600000); // +2 hari dari Pemeriksaan Start
            @endif

            // 3. Input / Output
            @if (isset($listVerifications[3]))
                inputStart = new Date("{{ $listVerifications[3]->start_date }}");
                inputStop = new Date("{{ $listVerifications[3]->stop_date }}");
            @else
                inputStart = new Date(pemeriksaanStop);
                inputStart.setHours(inputStart.getHours()); // Pemeriksaan Stop
                inputStop = new Date(inputStart.getTime() + 5 * 60000); // +1 jam dari Input Start
            @endif

            // 4. Verifikasi
            @if (isset($listVerifications[4]))
                verifikasiStart = new Date("{{ $listVerifications[4]->start_date }}");
                verifikasiStop = new Date("{{ $listVerifications[4]->stop_date }}");
            @else
                verifikasiStart = new Date(inputStop);
                verifikasiStart.setHours(verifikasiStart.getHours()); // Input Stop
                verifikasiStop = new Date(verifikasiStart.getTime() + 5 * 60000); // +5 menit dari Verifikasi Start
            @endif

            // 5. Validasi
            @if (isset($listVerifications[5]))
                validasiStart = new Date("{{ $listVerifications[5]->start_date }}");
                validasiStop = new Date("{{ $listVerifications[5]->stop_date }}");
            @else
                validasiStart = new Date(verifikasiStop);
                validasiStop = new Date(validasiStart.getTime() + 5 * 60000); // +5 menit dari Validasi Start
            @endif

            // if (document.querySelector('#start_date_sample')) {
            //     document.querySelector('#start_date_sample').value = formatDate(sampleStart);
            // }
            // if (document.querySelector('#stop_date_sample')) {
            //     document.querySelector('#stop_date_sample').value = formatDate(sampleStop);
            // }


            if (document.querySelector('#start_date_sample')) {
                // document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const start_date_sample = flatpickr("#start_date_sample", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                start_date_sample.setDate(formatDate(sampleStart), true); //

                $('#start_date_sample').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            if (document.querySelector('#stop_date_sample')) {
                // document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const stop_date_sample = flatpickr("#stop_date_sample", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                stop_date_sample.setDate(formatDate(sampleStop), true); //

                $('#stop_date_sample').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            // Populate the date fields in the HTML
            if (document.querySelector('#start_date_pemeriksaan')) {
                // document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const start_date_pemeriksaan = flatpickr("#start_date_pemeriksaan", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                start_date_pemeriksaan.setDate(formatDate(sampleStop), true); //

                $('#start_date_pemeriksaan').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            if (document.querySelector('#stop_date_pemeriksaan')) {
                // document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const stop_date_pemeriksaan = flatpickr("#stop_date_pemeriksaan", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                stop_date_pemeriksaan.setDate(formatDate(pemeriksaanStop), true); //

                $('#stop_date_pemeriksaan').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }
            // if (document.querySelector('#stop_date_pemeriksaan')) {
            //     document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStop);
            // }

            if (document.querySelector('#start_date_input')) {
                // document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const start_date_input = flatpickr("#start_date_input", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                start_date_input.setDate(formatDate(inputStart), true); //

                $('#start_date_input').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            // if (document.querySelector('#start_date_input')) {
            //     document.querySelector('#start_date_input').value = formatDate(inputStart);
            // }

            if (document.querySelector('#stop_date_input')) {
                // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const stop_date_input = flatpickr("#stop_date_input", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                stop_date_input.setDate(formatDate(inputStop), true); //

                $('#stop_date_input').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }
            // if (document.querySelector('#stop_date_input')) {
            //     document.querySelector('#stop_date_input').value = formatDate(inputStop);
            // }

            if (document.querySelector('#start_date_verifikasi')) {
                // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const start_date_verifikasi = flatpickr("#start_date_verifikasi", {
                    // Opsi lain jika diperlukan
                    enableTime: true,
                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                start_date_verifikasi.setDate(formatDate(verifikasiStart), true); //

                $('#start_date_verifikasi').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            // if (document.querySelector('#start_date_verifikasi')) {
            //     document.querySelector('#start_date_verifikasi').value = formatDate(verifikasiStart);
            // }

            if (document.querySelector('#stop_date_verifikasi')) {
                // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const stop_date_verifikasi = flatpickr("#stop_date_verifikasi", {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    locale: "id", // Setting locale to Indonesian
                    enableTime: true,
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                stop_date_verifikasi.setDate(formatDate(verifikasiStop), true); //

                $('#stop_date_verifikasi').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }
            // if (document.querySelector('#stop_date_verifikasi')) {
            //     document.querySelector('#stop_date_verifikasi').value = formatDate(verifikasiStop);
            // }

            if (document.querySelector('#start_date_validasi')) {
                // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const start_date_validasi = flatpickr("#start_date_validasi", {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    enableTime: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });

                start_date_validasi.setDate(formatDate(validasiStart), true); //

                $('#start_date_validasi').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            // if (document.querySelector('#start_date_validasi')) {
            //     document.querySelector('#start_date_validasi').value = formatDate(validasiStart);
            // }

            if (document.querySelector('#stop_date_validasi')) {
                // document.querySelector('#stop_date_input').value = formatDate(pemeriksaanStart);

                // flatpickr(".datetime", {
                //     enableTime: true,
                //     dateFormat: "d/m/Y H:i", // 24-hour format
                //     time_24hr: true
                // });
                const stop_date_validasi = flatpickr("#stop_date_validasi", {
                    // Opsi lain jika diperlukan

                    allowInput: true,
                    enableTime: true,
                    locale: "id", // Setting locale to Indonesian
                    dateFormat: "d/m/Y H:i", // 24-hour format
                    time_24hr: true
                });



                stop_date_validasi.setDate(formatDate(validasiStop), true); //

                $('#stop_date_validasi').inputmask("datetime", {
                    placeholder: "dd/mm/yyyy hh:mm",

                });
            }

            // if (document.querySelector('#start_date_pemeriksaan')) {
            //     document.querySelector('#start_date_pemeriksaan').value = formatDate(pemeriksaanStart);
            // }
            // if (document.querySelector('#stop_date_pemeriksaan')) {
            //     document.querySelector('#stop_date_pemeriksaan').value = formatDate(pemeriksaanStop);
            // }

            // if (document.querySelector('#start_date_input')) {
            //     document.querySelector('#start_date_input').value = formatDate(inputStart);
            // }
            // if (document.querySelector('#stop_date_input')) {
            //     document.querySelector('#stop_date_input').value = formatDate(inputStop);
            // }

            // if (document.querySelector('#start_date_verifikasi')) {
            //     document.querySelector('#start_date_verifikasi').value = formatDate(verifikasiStart);
            // }
            // if (document.querySelector('#stop_date_verifikasi')) {
            //     document.querySelector('#stop_date_verifikasi').value = formatDate(verifikasiStop);
            // }

            // if (document.querySelector('#start_date_validasi')) {
            //     document.querySelector('#start_date_validasi').value = formatDate(validasiStart);
            // }
            // if (document.querySelector('#stop_date_validasi')) {
            //     document.querySelector('#stop_date_validasi').value = formatDate(validasiStop);
            // }

            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');
                let hours = String(date.getHours()).padStart(2, '0');
                let minutes = String(date.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            if (document.getElementById('toggle-registrasi')) {
                document.getElementById('toggle-registrasi').addEventListener('click', function() {
                    var registrasiRow = document.getElementById('registrasi');
                    var registrasiUpdateRow = document.getElementById('registrasi-update');

                    if (registrasiRow.style.display === 'none') {
                        registrasiRow.style.display = '';
                        registrasiUpdateRow.style.display = 'none';
                    } else {
                        registrasiRow.style.display = 'none';
                        registrasiUpdateRow.style.display = '';
                    }


                    var registrasiUpdateRowStart = $('#registrasi-update [name="start_date"]').val();

                    flatpickr('#registrasi-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const register_update_start = flatpickr('#registrasi-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    register_update_start.setDate(formatDate(new Date(registrasiUpdateRowStart)), true); //

                    $('#registrasi-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var registrasiUpdateRowStop = $('#registrasi-update [name="stop_date"]').val();

                    flatpickr('#registrasi-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const register_update_stop = flatpickr('#registrasi-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    register_update_stop.setDate(formatDate(new Date(registrasiUpdateRowStop)), true); //

                    $('#registrasi-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });
            }

            if (document.getElementById('toggle-sample')) {

                document.getElementById('toggle-sample').addEventListener('click', function() {
                    var sampleRow = document.getElementById('sample');
                    var sampleUpdateRow = document.getElementById('sample-update');

                    if (sampleRow.style.display === 'none') {
                        sampleRow.style.display = '';
                        sampleUpdateRow.style.display = 'none';
                    } else {
                        sampleRow.style.display = 'none';
                        sampleUpdateRow.style.display = '';
                    }
                    var sampleUpdateRowStart = $('#sample-update [name="start_date"]').val();

                    flatpickr('#sample-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const sample_update_start = flatpickr('#sample-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    sample_update_start.setDate(formatDate(new Date(sampleUpdateRowStart)), true); //

                    $('#sample-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var sampleUpdateRowStop = $('#sample-update [name="stop_date"]').val();

                    flatpickr('#sample-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const sample_update_stop = flatpickr('#sample-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    sample_update_stop.setDate(formatDate(new Date(sampleUpdateRowStop)), true); //

                    $('#sample-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });

            }

            if (document.getElementById('toggle-analitik')) {

                document.getElementById('toggle-analitik').addEventListener('click', function() {
                    var analitikRow = document.getElementById('analitik');
                    var analitikUpdateRow = document.getElementById('analitik-update');

                    if (analitikRow.style.display === 'none') {
                        analitikRow.style.display = '';
                        analitikUpdateRow.style.display = 'none';
                    } else {
                        analitikRow.style.display = 'none';
                        analitikUpdateRow.style.display = '';
                    }

                    var analitikUpdateRowStart = $('#analitik-update [name="start_date"]').val();

                    flatpickr('#analitik-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const analitik_update_start = flatpickr('#analitik-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    analitik_update_start.setDate(formatDate(new Date(analitikUpdateRowStart)), true); //

                    $('#analitik-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var analitikUpdateRowStop = $('#analitik-update [name="stop_date"]').val();

                    flatpickr('#analitik-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const analitik_update_stop = flatpickr('#analitik-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    analitik_update_stop.setDate(formatDate(new Date(analitikUpdateRowStop)), true); //

                    $('#analitik-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });
            }

            if (document.getElementById('toggle-hasil-px')) {

                document.getElementById('toggle-hasil-px').addEventListener('click', function() {
                    var bacaHasilRow = document.getElementById('hasil-px');
                    var bacaHasilUpdateRow = document.getElementById('hasil-px-update');

                    if (bacaHasilRow.style.display === 'none') {
                        bacaHasilRow.style.display = '';
                        bacaHasilUpdateRow.style.display = 'none';
                    } else {
                        bacaHasilRow.style.display = 'none';
                        bacaHasilUpdateRow.style.display = '';
                    }
                    var bacaHasilUpdateRowStart = $('#hasil-px-update [name="start_date"]').val();

                    flatpickr('#hasil-px-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const bacaHasil_update_start = flatpickr('#hasil-px-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    bacaHasil_update_start.setDate(formatDate(new Date(bacaHasilUpdateRowStart)), true); //

                    $('#hasil-px-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var bacaHasilUpdateRowStop = $('#hasil-px-update [name="stop_date"]').val();

                    flatpickr('#hasil-px-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const bacaHasil_update_stop = flatpickr('#hasil-px-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    bacaHasil_update_stop.setDate(formatDate(new Date(bacaHasilUpdateRowStop)), true); //

                    $('#hasil-px-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });

            }


            if (document.getElementById('toggle-verifikasi')) {

                document.getElementById('toggle-verifikasi').addEventListener('click', function() {
                    var verifikasiRow = document.getElementById('verifikasi');
                    var verifikasiUpdateRow = document.getElementById('verifikasi-update');

                    if (verifikasiRow.style.display === 'none') {
                        verifikasiRow.style.display = '';
                        verifikasiUpdateRow.style.display = 'none';
                    } else {
                        verifikasiRow.style.display = 'none';
                        verifikasiUpdateRow.style.display = '';
                    }

                    var verifikasiUpdateRowStart = $('#verifikasi-update [name="start_date"]').val();

                    flatpickr('#verifikasi-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const verifikasi_update_start = flatpickr('#verifikasi-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    verifikasi_update_start.setDate(formatDate(new Date(verifikasiUpdateRowStart)),
                        true); //

                    $('#verifikasi-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var verifikasiUpdateRowStop = $('#verifikasi-update [name="stop_date"]').val();

                    flatpickr('#verifikasi-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const verifikasi_update_stop = flatpickr('#verifikasi-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    verifikasi_update_stop.setDate(formatDate(new Date(verifikasiUpdateRowStop)), true); //

                    $('#verifikasi-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });

            }

            if (document.getElementById('toggle-validasi')) {

                document.getElementById('toggle-validasi').addEventListener('click', function() {
                    var validasiRow = document.getElementById('validasi');
                    var validasiUpdateRow = document.getElementById('validasi-update');

                    if (validasiRow.style.display === 'none') {
                        validasiRow.style.display = '';
                        validasiUpdateRow.style.display = 'none';
                    } else {
                        validasiRow.style.display = 'none';
                        validasiUpdateRow.style.display = '';
                    }

                    var validasiUpdateRowStart = $('#validasi-update [name="start_date"]').val();

                    flatpickr('#validasi-update [name="start_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const validasi_update_start = flatpickr('#validasi-update [name="start_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    validasi_update_start.setDate(formatDate(new Date(validasiUpdateRowStart)), true); //

                    $('#validasi-update [name="start_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });

                    var validasiUpdateRowStop = $('#validasi-update [name="stop_date"]').val();

                    flatpickr('#validasi-update [name="stop_date"]', {
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });
                    const validasi_update_stop = flatpickr('#validasi-update [name="stop_date"]', {
                        // Opsi lain jika diperlukan

                        allowInput: true,
                        locale: "id", // Setting locale to Indonesian
                        enableTime: true,
                        dateFormat: "d/m/Y H:i", // 24-hour format
                        time_24hr: true
                    });


                    validasi_update_stop.setDate(formatDate(new Date(validasiUpdateRowStop)), true); //

                    $('#validasi-update [name="stop_date"]').inputmask("datetime", {
                        placeholder: "dd/mm/yyyy hh:mm",

                    });
                });
            }

            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');
                let hours = String(date.getHours()).padStart(2, '0');
                let minutes = String(date.getMinutes()).padStart(2, '0');
                return `${day}/${month}/${year} ${hours}:${minutes}`;
            }
        });
    </script>
    <script>
        $(document).ready(function() {

            const printVerifikasiDate = flatpickr('#tanggal-cetak-verifikasi', {
                allowInput: true,
                locale: "id",
                enableTime: false,
                dateFormat: "d/m/Y",
            });

            var printVerifikasiUpdateDate = $('#tanggal-cetak-verifikasi').val();

            printVerifikasiDate.setDate(formatDate(new Date(printVerifikasiUpdateDate)), true);

            $('#tanggal-cetak-verifikasi').inputmask("date", {
                placeholder: "dd/mm/yyyy",
            });

            function formatDate(date) {
                let year = date.getFullYear();
                let month = String(date.getMonth() + 1).padStart(2, '0');
                let day = String(date.getDate()).padStart(2, '0');
                return `${day}/${month}/${year}`;
            }
        });
    </script>

    <script>
        // Update input tersembunyi saat halaman dimuat
        window.onload = function() {
            updateNamaPetugas();
        };

        function updateNamaPetugas() {
            // Ambil nilai dari dropdown
            var selectedName = document.getElementById("nama_petugas_select").value;
            // Isi input tersembunyi dengan nama yang dipilih
            document.getElementById("nama_petugas_input").value = selectedName;
        }
    </script>
    <script>
      let namaPetugasValue = null;
      function checkNikAndPassword(namaPetugas, className) {
        namaPetugasValue = namaPetugas;
        event.preventDefault();
        $.ajax({
          url: "{{ url('elits-samples/check-petugas') }}/" + encodeURIComponent(namaPetugas),
          type: "GET",
          success: function(response) {
            if (response === "true") {
              const form = document.querySelector(`.${className}`);
              form.submit();
            } else {
              $('#inputNikAndPasword').modal('show');
            }
          },
          error: function() {
            swal({
              title: "Failed!",
              text: "An error occurred. Please try again.",
              icon: "error",
            });
          }
        });
      }

      function submitNikAndPassword()
      {
        event.preventDefault();

        if (namaPetugasValue != null){
          const formData = {
            nik: document.getElementById("nikPetugas").value,
            password: document.getElementById("passwordPetugas").value,
            _token: '{{ csrf_token() }}'
          };

          $.ajax({
            url : "{{ url('elits-samples/update-petugas') }}/" + encodeURIComponent(namaPetugasValue),
            type: "PUT",
            data: formData,
            success: function (response) {
              if (response === "true") {
                namaPetugasValue = null;
                $('#inputNikAndPasword').modal('hide');
                swal({
                  title: "Success!",
                  text: "NIK dan Password berhasil disimpan. Silahkan klik tombol verifikasi kembali!",
                  icon: "success",
                });
              } else {
                swal({
                  title: "Failed!",
                  text: "Failed to submit data. Please try again.",
                  icon: "error",
                });
              }
            },
            error: function() {
              swal({
                title: "Failed!",
                text: "An error occurred. Please try again.",
                icon: "error",
              });
            }
          });
        }
      }
    </script>
@endsection
