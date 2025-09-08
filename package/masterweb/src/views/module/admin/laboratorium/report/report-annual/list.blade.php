@extends('masterweb::template.admin.layout')

@section('title')
  Laporan Tahunan Pemeriksaan
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
                <li class="breadcrumb-item active"><a href="{{ url('/report-annual') }}">Laporan Tahunan Pemeriksaan</a>
                </li>
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
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row mb-4">
            <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
            <div class="col-md-2">
              <label for="year" class="bold">Tahun</label>
              <select class="smt-select2 form-control w-100" id="year">
                @for ($i = 1990; $i <= date('Y') + 1; $i++)
                  <option value="{{ $i }}" {{ isSelected($i, date('Y')) }}>{{ $i }}</option>
                @endfor
              </select>
            </div>

            <div class="col-md-3">
              <label for="SampleType" class="bold">Jenis Sarana</label>
              <select class="select-sampletype form-control w-100">
              </select>
            </div>

          </div>
        </div>
      </div>

      <div class="row mb-4">
        <div class="col-md-12">
          <div class="pt-4 text-right">
            <a href="#" class="btn btn-light btn-fw btn-print-report-annual" target="__blank"> <i
                class="fas fa-print"></i>
              Cetak</a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          @if ($message = Session::get('warning'))
            <div class="alert alert-warning alert-block">
              <button type="button" class="close" data-dismiss="alert">×</button>
              <strong>{{ $message }}</strong>
            </div>
          @endif
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="table-responsive">
            <table id='empTable' class="table">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal Pemeriksaan</th>
                  <th>Nama Sample/Merk</th>
                  <th>Nama Customer/Pasien</th>
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
        ajax: {
          url: "{{ route('report-annual.data-report-annual') }}",
          type: "GET",
          data: function(d) {
            d.year = getYear(),
              d.sampletype = getSampleType()
          }
        },
        columns: [{
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
          },
          {
            data: 'tgl_pemeriksaan',
            name: 'tgl_pemeriksaan'
          },
          {
            data: 'codesample_samples',
            name: 'codesample_samples'
          },
          {
            data: 'customer',
            name: 'customer'
          }
        ]
      });

      table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
      });

      function getYear() {
        var year = $('#year').val();
        return year;
      }

      function getSampleType() {
        var sampletype = $('.select-sampletype').val();
        return sampletype;
      }

      $('#year').change(function() {
        table.ajax.reload(null, false);

        setUrlPrintReport()
      })

      $('.select-program').change(function() {
        table.ajax.reload(null, false);

        setUrlPrintReport()
      })
      $('.select-sampletype').change(function() {
        table.ajax.reload(null, false);

        setUrlPrintReport()
      })

      setUrlPrintReport()

      function setUrlPrintReport() {
        var year = $('#year').val();
        var sampletype = $('.select-sampletype').val();

        if (year !== "") {
          var val_url = "{{ url('report-annual/print-report-annual-maatweb?year=') }}" + year;

          if (sampletype !== "") {
            val_url = val_url + "&sampletype=" + sampletype;
          }
          $('.btn-print-report-annual').show();
          $('.btn-print-report-annual').attr('href', val_url);

        } else {
          var val_url = '#';

          $('.btn-print-report-annual').hide();
          $('.btn-print-report-annual').attr('href', val_url);
        }
      }



      var CSRF_TOKEN = $('#csrf-token').val();


      $(".select-sampletype").select2({
        ajax: {
          url: "{{ route('getSampleType') }}",
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
          cache: true
        },
        placeholder: 'Pilih Jenis Sarana',
        allowClear: true
      });
    });
  </script>
@endsection
