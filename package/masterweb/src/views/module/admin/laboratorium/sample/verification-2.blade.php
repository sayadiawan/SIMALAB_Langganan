@extends('masterweb::template.admin.layout')
@section('title')
    Verifikasi Sample
@endsection

@section('content')
    <style>
        .ui-datepicker {
            position: relative;
            z-index: 100000;
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

                                @if (getSpesialAction(Request::segment(1), 'is-analis', ''))
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/elits-analys') }}">
                                            Data Analisa</a>
                                    </li>
                                @else
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/elits-permohonan-uji') }}">
                                            Permohonan Uji</a>
                                    </li>

                                    <li class="breadcrumb-item">
                                        <a href="{{ url('/elits-samples', [$sample->permohonan_uji_id]) }}">
                                            Daftar Pengujian</a>
                                    </li>
                                @endif

                                <li class="breadcrumb-item active" aria-current="page"><span>Analys</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-12">
                    <H4 class="d-inline-block  float-left margin-top">Verifikasi Sampel</H4>

                    @if ($sample->kode_laboratorium == 'KIM')
                        @if ($sample->name_sample_type == 'Makanan/Minuman/Lainnya')
                            <button type="button" class="btn btn-outline-success btn-rounded float-right"
                                data-toggle="modal" data-target="#tambahAgendaKimiaModal">
                                <i class="fa fa-print"></i> Cetak Hasil
                            </button>
                        @else
                            <button type="button" class="btn btn-outline-success btn-rounded float-right"
                                data-toggle="modal" data-target="#tambahAgendaLHUModal">
                                <i class="fa fa-print"></i> Cetak Hasil
                            </button>
                        @endif
                    @elseif($sample->kode_laboratorium == 'MBI')
                        <button type="button" class="btn btn-outline-success btn-rounded float-right" data-toggle="modal"
                            data-target="#tambahAgendaMikroModal">
                            <i class="fa fa-print"></i> Cetak Hasil
                        </button>
                    @else
                        <button type="button" class="btn btn-outline-success btn-rounded float-right" data-toggle="modal"
                            data-target="#tambahAgendaMikroModal">
                            <i class="fa fa-print"></i> Cetak Hasil
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-success btn-rounded float-right mr-2" data-toggle="modal" data-target="#editTanggalCetakVerifikasi">
                      <i class="fa fa-print"></i> Cetak Verifikasi
                    </button>

                </div>
            </div>
        </div>

      @if (session('error-bsre'))
        <div class="col-12 mt-2">
          <div class="alert alert-danger">
            {{ session('error-bsre') }}
          </div>
        </div>
      @endif
      @if(session('error-laporan') or session('error-verifikasi'))
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
            <div class="content">
                <div class="container-fluid">
                    <div class="row">

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
                        <!-- utama -->

                        <div class="col-md-12">
                            <h6 class="d-inline-block card-subtitle text-muted  margin-top"
                                style="margin-top:.2rem; background:blue;color:white !important;padding:5px;border-radius:5px;">
                                Laboratorium {{ $sample->nama_laboratorium }}</h6>

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


                                @if ($sample->kode_laboratorium === 'MBI')
                                    <label for="lokasi_pengambilan"><b>Lokasi Sampel :</b></label>
                                @else
                                    <label for="lokasi_pengambilan"><b>Asal Contoh Air/ Lokasi
                                            Sampel :</b></label>
                                @endif

                                <br>



                                @if (isset($sample->location_samples) && $sample->location_samples != '')
                                    @if ($sample->kode_laboratorium === 'MBI')
                                        {!! $sample->location_samples !!}
                                    @else
                                        {!! $sample->location_samples !!}
                                    @endif
                                @else
                                    @php
                                        if ($sample->is_pudam) {
                                            if ($sample->kode_laboratorium === 'MBI') {
                                                $location =
                                                    $sample->address_location_pdam ?? old('address_location_pdam');
                                            } else {
                                                $location = $sample->name_customer_pdam ?? old('name_customer_pdam');
                                            }
                                        } else {
                                            $location = $sample->titik_pengambilan ?? old('titik_pengambilan');
                                        }
                                    @endphp
                                    @if ($sample->kode_laboratorium === 'MBI')
                                        {!! $location !!}
                                    @else
                                        {!! $location !!}
                                    @endif
                                @endif

                                <br>
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
                            @if ($sample->kode_laboratorium == 'KIM')
                                @include('masterweb::module.admin.laboratorium.sample.table-verification-kimia')
                            @elseif($sample->kode_laboratorium === 'MBI')
                                @include('masterweb::module.admin.laboratorium.sample.table-verification-mikro')
                            @endif
                        </div>
                    </div>

                    <!-- utama -->
                </div>
                <!-- /.row -->
            </div>
        </div>

    </div>

    <!-- Modal Edit Nama Pengambil-->
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

    <!-- Modal Tambah Agenda Kimia -->
    <div class="modal fade" id="tambahAgendaKimiaModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahAgendaKimiaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAgendaModalLabel">Tambah Agenda Sebelum Cetak Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="agendaForm" action="{{ route('elits-release.print-kimia-2', [$sample->permohonan_uji_id]) }}"
                    method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="agenda">Agenda</label>
                            <input type="text" class="form-control" id="agenda" name="agenda"
                                placeholder="Masukkan agenda">
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

    <!-- Modal Tambah Agenda LHU -->
    <div class="modal fade" id="tambahAgendaLHUModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahAgendaLHUModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAgendaModalLabel">Tambah Agenda Sebelum Cetak Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="agendaForm"
                    action="{{ route('elits-release.printLHU', [$sample->id_samples, $sample->id_laboratorium]) }}"
                    method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="agenda">Agenda</label>
                            <input type="text" class="form-control" id="agenda" name="agenda"
                                placeholder="Masukkan agenda">
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

    <!-- Modal Tambah Agenda Mikro -->
    <div class="modal fade" id="tambahAgendaMikroModal" tabindex="-1" role="dialog"
        aria-labelledby="tambahAgendaModalMikroLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAgendaModalLabel">Tambah Agenda Sebelum Cetak Hasil</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearListSamples()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="agendaForm"
                    action="{{ route('elits-release.print-mikro', [$sample->permohonan_uji_id, $sample->typesample_samples, $sample->packet_id]) }}"
                    method="GET">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="agenda">Agenda</label>
                            <input type="text" class="form-control" id="agenda" name="agenda"
                                placeholder="Masukkan agenda">
                        </div>
                        <div class="form-group">
                          <label for="signOption">Metode Tanda Tangan</label>
                          <select class="form-control" name="signOption" id="signOption">
                            <option value="0">Tanda Tangan Manual</option>
                            <option value="1">Tanda Tangan Elektronik</option>
                          </select>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label for="samples">List Sampel</label>
                                <div class="d-flex align-items-center">
                                    <label for="printAll" style="font-size: 11px; margin-right: 5px; margin-top: 8px;">Cetak Semua Hasil</label>
                                    <input type="checkbox" name="printall" id="printAll">
                                </div>
                            </div>
                            <ol id="listSamples" class="mt-2">

                            </ol>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Cetak</button>
                    </div>
                </form>
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
                action="{{ route('elits-release.print_verifikasi', [$sample->id_samples, $sample->id_laboratorium]) }}"
                method="GET">
            <div class="modal-body">
              <div class="form-group">
                <label for="tanggal-cetak-verifikasi">Tanggal</label>
                <input type="date" class="form-control datetime" id="tanggal-cetak-verifikasi" name="tanggal_cetak_verifikasi">
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
@endsection

@section('scripts')
    <script>
        $(document).scrollTop($(document).height());
    </script>
    <script>
      $(document).ready(function() {
        flatpickr('#tanggal-cetak-verifikasi', {
          enableTime: false,
          dateFormat: "d/m/Y"
        });

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
    <script>
        $(document).ready(function () {
          $('#tambahAgendaMikroModal').on('show.bs.modal', function (event) {

            var link = window.location.href;

            console.error(link);



            var idSamplelab = link.split('/');

            var idSample = idSamplelab[idSamplelab.length -2];
            var idLab = idSamplelab[idSamplelab.length -1];
            var idlab = idLab.split('?')[0];

            getSamples(idSample, idlab, function (samples) {
                if (samples) {
                    const listSamples = $('#listSamples')
                    samples.forEach(sample => {
                            var location = sample.location_samples !== null ? sample.location_samples : sample.name_pelanggan;

                            var tempDiv = document.createElement('div');
                            tempDiv.innerHTML = location;
                            location = tempDiv.textContent || tempDiv.innerText;

                            listSamples.append(`<li class="w-full d-flex justify-content-between" style="padding: 0px !important; height: 14px !important;">
                                <label style="font-size: 11px; margin-right: 5px;" >00${sample.count_id} - ${location}</label>
                                <input type="checkbox" name="printSamples[]" class="samplesCheckbox" value="${sample.id_samples}">
                            </li><hr>`)
                        });
                }
            })

            $('#agendaForm').attr('action', link);
          })

          $('#printAll').on('change', function() {
              var isChecked = $(this).prop('checked');
              $('#listSamples .samplesCheckbox').prop('checked', isChecked);
          });
        })

        function getSamples(idSample, idlab, callback) {
              const url = `/elits-samples/list-samples-by-id-sample/${idSample}/${idlab}`;

              $.ajax({
                  url: url,
                  method: 'GET',
                  dataType: 'json',
                  success: function(data) {
                      callback(data);
                  },
                  error: function(xhr, status, error) {
                      callback(null);
                  }
              });
          }

        function clearListSamples() {
          const listSamples = $('#listSamples');
          listSamples.empty();
        }

      </script>
@endsection
