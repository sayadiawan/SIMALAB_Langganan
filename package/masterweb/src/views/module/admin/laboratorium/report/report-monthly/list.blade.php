@extends('masterweb::template.admin.layout')

@section('title')
  Laporan Bulanan Pemeriksaan
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
                <li class="breadcrumb-item active"><a href="{{ url('/report-monthly') }}">Laporan Bulanan Pemeriksaan</a>
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
          {{-- <div id="datepicker-popup" class="input-group date datepicker">
                <input type="text" class="form-control">
                <span class="input-group-addon input-group-append border-left">
                    <span class="far fa-calendar input-group-text"></span>
                </span>
            </div> --}}
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="form-group row mb-4">
            <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />

            <div class="col-md-2">
              <label for="month" class="bold">Bulan</label>
              <select class="smt-select2 form-control w-100" id="month">
                @for ($i = 1; $i <= 12; $i++)
                  <option value="{{ sprintf('%02d', $i) }}" {{ isSelected($i, date('m')) }}>{{ fbulan($i) }}
                  </option>
                @endfor
              </select>
            </div>
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
            <a href="#" class="btn btn-light btn-fw btn-print-report-monthly" target="__blank"> <i
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
        responsive: true,
        ajax: {
          url: "{{ route('report-monthly.data-report-monthly') }}",
          type: "GET",
          data: function(d) {
            d.month = getMonth();
            d.year = getYear();
            d.sampletype = getSampleType();
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

      // datatables responsive
      new $.fn.dataTable.FixedHeader(table);

      table.on('draw', function() {
        $('[data-toggle="tooltip"]').tooltip();
      });

      function getMonth() {
        var month = $('#month').val();
        return month;
      }

      function getYear() {
        var year = $('#year').val();
        return year;
      }

      function getSampleType() {
        var sampletype = $('.select-sampletype').val();
        return sampletype;
      }

      $('#month').change(function() {
        table.ajax.reload(null, false);

        setUrlPrintReport()
      })

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
        var month = $('#month').val();
        var year = $('#year').val();
        var sampletype = $('.select-sampletype').val();




        if (month !== "" && year !== "") {

          /* var val_url = "{{ url('report-monthly/print-report-monthly?month=') }}" + month + '&year=' + year +
            '&program=' + program; */

          /* var val_url = "{{ url('report-monthly/print-report-monthly-excel?month=') }}" + month + '&year=' + year +
            '&program=' + program; */
          var val_url = "{{ url('report-monthly/print-report-monthly-maatweb?month=') }}" + month + '&year=' + year;

          if (sampletype !== "") {
            val_url = val_url + "&sampletype=" + sampletype;
          }
          $('.btn-print-report-monthly').show();
          $('.btn-print-report-monthly').attr('href', val_url);


        } else {
          var val_url = '#';

          $('.btn-print-report-monthly').hide();
          $('.btn-print-report-monthly').attr('href', val_url);
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
