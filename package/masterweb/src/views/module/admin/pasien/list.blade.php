@extends('masterweb::template.admin.layout')

@section('title')
  Pasien Management
@endsection

@section('content')
  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="">
          <div class="template-demo">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                    Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/elits-pasien') }}">Laboraturium</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Pasien Management</span></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <div class="mb-2 float-right">
            <a href="{{ route('elits-pasien.create') }}">

              <button type="button" class="btn btn-info btn-icon-text">
                Tambah Data
                <i class="fa fa-plus btn-icon-append"></i>
              </button>
            </a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <table id="table-pasien" class="table" width="100%">
            <thead>
              <tr>
                <th>No</th>
                <th>NIK Pasien</th>
                <th>Nomor Rekam Medis</th>
                <th>Nama Pasien</th>
                <th>Nomor Telepon</th>
                <th>Actions</th>
              </tr>
            </thead>

            <tbody id="table-body">

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    $(document).ready(function() {
      var datatable = $('#table-pasien').DataTable({
        processing: true,
        serverSide: true,
        ordering: true,
        stateSave: true,
        responsive: true,
        ajax: {
          url: '{!! url()->current() !!}',
          type: 'GET',
          data: function(d) {
            d.search = $('input[type="search"]').val()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'nik_pasien',
            name: 'nik_pasien'
          },
          {
            data: 'no_rekammedis_pasien',
            name: 'no_rekammedis_pasien'
          },
          {
            data: 'nama_pasien',
            name: 'nama_pasien'
          },
          {
            data: 'phone_pasien',
            name: 'phone_pasien'
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            width: '15%'
          }
        ]
      })

      $('#table-pasien').on('click', '.btn-hapus', function() {
        var kode = $(this).data('id');
        var nama = $(this).data('nama');

        swal({
            title: "Apakah anda yakin?",
            text: "Untuk menghapus data : " + nama,
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
              $.ajax({
                type: 'ajax',
                method: 'get',
                url: '/elits-pasien-destroy/' + kode,
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
                        // document.location = '/elits-pasien';

                        datatable.ajax.reload();
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
    });
  </script>
@endsection
