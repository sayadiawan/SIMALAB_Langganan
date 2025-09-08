@extends('masterweb::template.admin.layout')

@section('title')
  Baku Mutu Lab.{{ $lab }} Management
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
                <li class="breadcrumb-item"><a href="{{ url('/elits-baku-mutu-' . $lab_link) }}"> Baku Mutu
                    Lab.{{ $lab }}</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>List</span></li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">


    <div class="card-body">
      <div class="d-flex">
        <div class="mr-auto p-2">
        </div>

        @if (getAction('create'))
          <div class="p-2">
            <a href="{{ route('elits-baku-mutu-klinik.create') }}">

              <button type="button" class="btn btn-info btn-icon-text" onclick="localStorage.clear();">
                Tambah Data
                <i class="fa fa-plus btn-icon-append"></i>
              </button>
            </a>
          </div>
        @endif

      </div>

      <div class="row">

        @if (session('status'))
          <div class="alert alert-success">
            {{ session('status') }}
          </div>
        @endif

        <div class="col-12">
          <div class="table-responsive">
            <table id='empTable' class="table" style="width:100%">
              <thead>
                <tr>
                  <th width="15">No</th>
                  <th>Jenis Parameter</th>
                  <th>Parameter Satuan</th>
                  <th>Acuan Baku Mutu</th>
                  <th>Nilai Baku Mutu</th>
                  <th>Status Data Khusus</th>
                  <th>Detail Data Khusus</th>
                  <th width="200">Actions</th>
                </tr>
              </thead>

              <tbody id="tabel-body">

              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection


@section('scripts')
  <script type="text/javascript">
    $(document).ready(function() {
      // DataTable
      var table = $('#empTable').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        responsive: true,
        ajax: {
          url: "{{ route('elits-baku-mutu-klinik.data-baku-mutu-klinik') }}",
          type: "GET"
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'jenis_parameter',
            name: 'jenis_parameter'
          },
          {
            data: 'parameter_satuan',
            name: 'parameter_satuan'
          },
          {
            data: 'library',
            name: 'library'
          },
          {
            data: 'nilai_baku_mutu',
            name: 'nilai_baku_mutu'
          },
          {
            data: 'is_khusus_baku_mutu',
            name: 'is_khusus_baku_mutu'
          },
          {
            data: 'detail_data_khusus',
            name: 'detail_data_khusus'
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
          }
        ]
      });

      // datatables responsive
      new $.fn.dataTable.FixedHeader(table);

      table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
      });

      $('#empTable').on('click', '.btn-hapus', function() {
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
                url: '/elits-baku-mutu-klinik-destroy/' + kode,
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
                        document.location = '/elits-baku-mutu-klinik';
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
