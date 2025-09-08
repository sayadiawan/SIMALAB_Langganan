@extends('masterweb::template.admin.layout')

@section('title')
  Permohonan Uji Klinik Prolanis
@endsection

@section('content')
  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="">
          <div class="template-demo">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i> Beranda</a></li>
                <li class="breadcrumb-item"><a href="">Laboratorium</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Permohonan Uji Klinik Prolanis</span></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>

  @if (session('success'))
      <div class="col-12">
          <div class="alert alert-success">
              {{ session('success') }}
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

  <div class="card">
    <div class="card-body">
      <div class="d-flex">
        <div class="mr-auto p-2"></div>
        <div class="p-2">
          <a href="{{ route('elits-permohonan-uji-klinik-2.create-prolanis') }}">
            <button type="button" class="btn btn-info btn-icon-text">
              Tambah Data
              <i class="fa fa-plus btn-icon-append"></i>
            </button>
          </a>
        </div>
      </div>

      <div class="row">
        @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
          </div>
        @endif

        <div class="col-12">
          <div class="table-responsive">
            <table id="order-listing" class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama Prolanis</th>
                  <th>Tanggal Prolanis</th>
                  <th>Kuota Prolanis</th>
                  <th>Status Prolanis</th>
                  <th style="text-align: center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @php
                  $no = 1;
                @endphp

                @foreach ($data as $item)
                  <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $item->nama_prolanis }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->tgl_prolanis)->isoFormat('DD MMMM YYYY') }}</td>
                    <td>{{ $item->kuota_prolanis }}</td>
                    <td>
                      @if($item->status_prolanis == 1)
                          <label class="badge badge-info badge-pill d-block" style="width: 130px">Sudah diunggah</label>
                          @if($item->is_prolanis_gula == 1)
                            <label class="badge badge-primary badge-pill d-block mt-1" style="width: 130px">Prolanis Gula</label>
                          @elseif($item->is_prolanis_urine == 1)
                            <label class="badge badge-success badge-pill d-block mt-1" style="width: 130px">Prolanis Urine</label>
                          @endif
                      @else
                          <label class="badge badge-warning badge-pill" style="width: 130px">Belum diunggah</label>
                      @endif
                    </td>
                    <td style="text-align: center">
                      <!-- Button Dropdown for Prolanis Gula -->
                      <div class="btn-group d-block mb-1">
                        <button type="button" class="btn btn-primary dropdown-toggle" style="width: 150px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @if ($item->status_prolanis == 1) disabled @endif>
                          Prolanis Gula
                        </button>
                        <div class="dropdown-menu bg-primary">
                          <a href="{{ route('elits-permohonan-uji-klinik-2.format-prolanis-gula', $item->id_permohonan_uji_klinik_prolanis) }}" class="dropdown-item bg-primary text-white">
                            <i class="fa fa-download"></i> Unduh Format Gula
                          </a>
                          <button type="button" class="dropdown-item bg-primary text-white" data-toggle="modal" data-target="#importGulaModal-{{ $item->id_permohonan_uji_klinik_prolanis }}">
                            <i class="fa fa-file-excel"></i> Unggah Data Gula
                          </button>
                        </div>
                      </div>

                      <!-- Button Dropdown for Prolanis Urine -->
                      <div class="btn-group d-block">
                        <button type="button" class="btn btn-success dropdown-toggle" style="width: 150px" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" @if ($item->status_prolanis == 1) disabled @endif>
                          Prolanis Urine
                        </button>
                        <div class="dropdown-menu bg-success">
                          <a href="{{ route('elits-permohonan-uji-klinik-2.format-prolanis-urine', $item->id_permohonan_uji_klinik_prolanis) }}" class="dropdown-item bg-success text-white">
                            <i class="fa fa-download"></i> Unduh Format Urine
                          </a>
                          <button type="button" class="dropdown-item bg-success text-white" data-toggle="modal" data-target="#importUrineModal-{{ $item->id_permohonan_uji_klinik_prolanis }}">
                            <i class="fa fa-file-excel"></i> Unggah Data Urine
                          </button>
                        </div>
                      </div>
                      <div class="btn-group d-block">
                         @if ($item->status_prolanis == 0)
                          <a href="#hapus" type="button" class="btn btn-danger mt-1 btn-hapus" style="width: 150px"
                              data-id="{{ $item->id_permohonan_uji_klinik_prolanis }}"
                              data-name="{{ $item->nama_prolanis }}">
                              <i class="fa fa-trash"></i> Hapus
                          </a>
                        @endif
                      </div>
                    </td>
                  </tr>

                  <!-- Modal Import Gula -->
                  <div class="modal fade" id="importGulaModal-{{ $item->id_permohonan_uji_klinik_prolanis }}" tabindex="-1" aria-labelledby="importGulaModalLabel-{{ $item->id_permohonan_uji_klinik_prolanis }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="importGulaModalLabel-{{ $item->id_permohonan_uji_klinik_prolanis }}">Import Data Prolanis Gula</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form action="{{ route('elits-permohonan-uji-klinik-2.importProlanisGula', $item->id_permohonan_uji_klinik_prolanis) }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <div class="modal-body">
                            <div class="mb-3">
                              <label for="fileGula" class="form-label">Pilih File Excel</label>
                              <input type="file" class="form-control" id="fileGula" name="file" required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                  <!-- Modal Import Urine -->
                  <div class="modal fade" id="importUrineModal-{{ $item->id_permohonan_uji_klinik_prolanis }}" tabindex="-1" aria-labelledby="importUrineModalLabel-{{ $item->id_permohonan_uji_klinik_prolanis }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="importUrineModalLabel-{{ $item->id_permohonan_uji_klinik_prolanis }}">Import Data Prolanis Urine</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <form action="{{ route('elits-permohonan-uji-klinik-2.importProlanisUrine', $item->id_permohonan_uji_klinik_prolanis) }}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <div class="modal-body">
                            <div class="mb-3">
                              <label for="fileUrine" class="form-label">Pilih File Excel</label>
                              <input type="file" class="form-control" id="fileUrine" name="file" required>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Import</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- SweetAlert JS -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    $(function() {
      $('#order-listing').on('click', '.btn-hapus', function() {
        var kode = $(this).data('id');
        var name = $(this).data('name');

        swal({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data : " + name,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              $.ajax({
                type: 'ajax',
                method: 'get',
                url: '/elits-permohonan-uji-klinik-2/destroy-prolanis/' + kode,
                async: true,
                dataType: 'json',
                success: function(response) {
                  if (response.status == true) {
                    swal({
                        title: "Success!",
                        text: response.pesan,
                        icon: "success"
                      })
                      .then(function() {
                        document.location = '/elits-permohonan-uji-klinik-2/prolanis';
                      });
                  } else {
                    swal("Hapus Data Gagal!", {
                      icon: "warning",
                      title: "Failed!",
                      text: response.pesan,
                    });
                  }
                },
                error: function() {
                  swal("ERROR", "System tidak dapat menghapus data!", "error");
                }
              });
            } else {
              swal("Cancelled", "Hapus data dibatalkan!", "error");
            }
          });
      });
    })
  </script>


@endsection
