@extends('masterweb::template.admin.layout')

@section('title')
    Daftar Pengujian
@endsection

@section('content')
    <style>
        .dropdown-scroll-menu {
            /* background-color: lightblue !important; */
            height: 200px !important;
            width: auto !important;
            overflow-y: auto !important;
        }

        .pointer {
          cursor: pointer;
        }

        .my-custom-popup-class {
          padding-top: 2.5rem !important;
        }
    </style>

    <script>
        var role = "admin"
    </script>

    <script src="https://www.gstatic.com/firebasejs/3.2.1/firebase.js"></script>
    <script src="{{ asset('assets/admin/js/firebase-js/firebase/config.js') }}"></script>
    <script src="{{ asset('assets/admin/js/firebase-js/firebase/database.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji') }}">Permohonan Uji</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><span>Daftar Pengujian</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

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

    <div class="card">


        <div class="card-header">
            <h4>Daftar Pengujian</h4>

        </div>


        <div class="card-body">

            <div class="col-md-12">

                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="codesample_samples">Kode Permohonan Uji</label>
                            </div>
                            <div class="col-md-0">
                                <label for="codesample_samples">:</label>
                            </div>
                            <div class="col-md-8">
                                <label for="codesample_samples">{{ $permohonan_uji->code_permohonan_uji }}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="codesample_samples">Nama Perusahaan</label>
                            </div>
                            <div class="col-md-0">
                                <label for="codesample_samples">:</label>
                            </div>
                            <div class="col-md-8">
                                <label for="codesample_samples">{{ $permohonan_uji->name_customer }}</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label for="codesample_samples">Tanggal</label>
                            </div>
                            <div class="col-md-0">
                                <label for="codesample_samples">:</label>
                            </div>
                            <div class="col-md-8">
                                @php
                                    $date_permohonan_uji = \Carbon\Carbon::createFromFormat(
                                        'Y-m-d H:i:s',
                                        $permohonan_uji->date_permohonan_uji,
                                    )->format('d/m/Y');
                                @endphp
                                <label for="codesample_samples">{{ $date_permohonan_uji }}</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label for="codesample_samples">Catatan</label>
                            </div>
                            <div class="col-md-0">
                                <label for="codesample_samples">:</label>
                            </div>
                            <div class="col-md-8">
                                <label for="codesample_samples">{{ $permohonan_uji->catatan }}</label>
                            </div>
                        </div>


                    </li>
                </ul>
            </div>

            <div class="d-flex">
                <div class="mr-auto p-2">
                </div>

                @if (getAction('create'))
                    <div class="p-2">
                        <a href="{{ route('elits-samples.create', [Request::segment(2)]) }}">

                            <button type="button" class="btn btn-fw btn-info btn-icon-text">
                                Tambah Data <i class="fa fa-plus btn-icon-append"></i>
                            </button>
                        </a>
                    </div>
                @endif

                @if (isset($packet_prints))
                    @if (count($packet_prints) > 0)
                        <div class="p-2">
                            @if (count($packet_prints) == 1)
                                @php
                                    $packet_print_sample_type = \Smt\Masterweb\Models\Sample::where(
                                        'permohonan_uji_id',
                                        '=',
                                        Request::segment(2),
                                    )
                                        ->where('id_sample_type', $packet_prints[0]['id_sample_type'])
                                        ->where('jenis_makanan_id', $packet_prints[0]['jenis_makanan_id'])
                                        ->join('tb_sample_method', function ($join) {
                                            $join
                                                ->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                                                ->whereNull('tb_sample_method.deleted_at')
                                                ->whereNull('tb_samples.deleted_at')
                                                ->join('ms_laboratorium', function ($join) {
                                                    $join
                                                        ->on(
                                                            'ms_laboratorium.id_laboratorium',
                                                            '=',
                                                            'tb_sample_method.laboratorium_id',
                                                        )
                                                        ->whereNull('ms_laboratorium.deleted_at')
                                                        ->whereNull('tb_sample_method.deleted_at');
                                                });
                                        })
                                        ->leftjoin('tb_pengesahan_hasil', function ($join) {
                                            $join
                                                ->on(
                                                    'tb_pengesahan_hasil.id_pengesahan_hasil',
                                                    '=',
                                                    DB::raw('(SELECT id_pengesahan_hasil FROM
                                    tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND
                                    tb_pengesahan_hasil.deleted_at is NULL AND tb_samples.deleted_at is NULL LIMIT 1)'),
                                                )

                                                ->whereNull('tb_pengesahan_hasil.deleted_at')
                                                ->whereNull('tb_samples.deleted_at');
                                        })
                                        ->leftjoin('ms_jenis_makanan', function ($join) {
                                            $join
                                                ->on(
                                                    'ms_jenis_makanan.id_jenis_makanan',
                                                    '=',
                                                    'tb_samples.jenis_makanan_id',
                                                )
                                                ->whereNull('tb_samples.deleted_at')
                                                ->whereNull('ms_jenis_makanan.deleted_at');
                                        })
                                        ->leftjoin('ms_sample_type', function ($join) {
                                            $join
                                                ->on(
                                                    'ms_sample_type.id_sample_type',
                                                    '=',
                                                    'tb_samples.typesample_samples',
                                                )

                                                ->whereNull('ms_sample_type.deleted_at')
                                                ->whereNull('tb_samples.deleted_at');
                                        })
                                        ->select(
                                            'id_laboratorium',
                                            'ms_jenis_makanan.*',
                                            'tb_pengesahan_hasil.*',
                                            'typesample_samples',
                                            'ms_sample_type.id_sample_type',
                                            'ms_sample_type.name_sample_type',
                                            'tb_samples.jenis_makanan_id',
                                        )
                                        ->distinct(
                                            'id_laboratorium',
                                            'ms_sample_type.id_sample_type',
                                            'tb_samples.jenis_makanan_id',
                                        )
                                        ->where('id_laboratorium', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                                        ->whereNotNull('pengesahan_hasil_date')
                                        ->get();

                                @endphp

                                @if (count($packet_print_sample_type) > 0)
                                    <a data-href="{{ route('elits-release.print-mikro', [Request::segment(2), $packet_prints[0]->id_sample_type]) }}?jenis_makanan_id={{ $packet_prints[0]->jenis_makanan_id }}"
                                        target="__blank" data-toggle="modal" data-target="#signOptionModal">
                                        <button type="button" class="btn btn-fw btn-primary btn-icon-text">
                                            Mikro <i class="fa fa-print"></i>
                                        </button>
                                    </a>
                                @endif
                            @else
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Mikro <i class="fa fa-print"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-scroll-menu" aria-labelledby="dropdownMenuButton">
                                        @php
                                            $ketemu = false;
                                            $samplesType = [];
                                            $samplesAirBersihAndAirMinum = ["c7c770a9-6bd7-4e30-83fc-0e4cc6a01fe0", "65df8403-b29f-4645-a1ed-12d2aeff1fbd"];
                                        @endphp
                                        @foreach ($packet_prints as $packet_print)

                                            @php
                                                $samplesType[] = $packet_print->id_sample_type;
                                                $packet_print_sample_type = \Smt\Masterweb\Models\Sample::where(
                                                    'permohonan_uji_id',
                                                    '=',
                                                    Request::segment(2),
                                                )
                                                    ->where('id_sample_type', $packet_print->id_sample_type)
                                                    ->where('jenis_makanan_id', $packet_print->jenis_makanan_id)
                                                    ->join('tb_sample_method', function ($join) {
                                                        $join
                                                            ->on(
                                                                'tb_sample_method.sample_id',
                                                                '=',
                                                                'tb_samples.id_samples',
                                                            )
                                                            ->whereNull('tb_sample_method.deleted_at')
                                                            ->whereNull('tb_samples.deleted_at')
                                                            ->join('ms_laboratorium', function ($join) {
                                                                $join
                                                                    ->on(
                                                                        'ms_laboratorium.id_laboratorium',
                                                                        '=',
                                                                        'tb_sample_method.laboratorium_id',
                                                                    )
                                                                    ->whereNull('ms_laboratorium.deleted_at')
                                                                    ->whereNull('tb_sample_method.deleted_at');
                                                            });
                                                    })
                                                    ->leftjoin('tb_pengesahan_hasil', function ($join) {
                                                        $join
                                                            ->on(
                                                                'tb_pengesahan_hasil.id_pengesahan_hasil',
                                                                '=',
                                                                DB::raw('(SELECT id_pengesahan_hasil FROM
                                        tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND
                                        tb_pengesahan_hasil.deleted_at is NULL AND tb_samples.deleted_at is NULL LIMIT 1)'),
                                                            )
                                                            ->whereNull('tb_pengesahan_hasil.deleted_at')
                                                            ->whereNull('tb_samples.deleted_at');
                                                    })
                                                    ->leftjoin('ms_jenis_makanan', function ($join) {
                                                        $join
                                                            ->on(
                                                                'ms_jenis_makanan.id_jenis_makanan',
                                                                '=',
                                                                'tb_samples.jenis_makanan_id',
                                                            )
                                                            ->whereNull('tb_samples.deleted_at')
                                                            ->whereNull('ms_jenis_makanan.deleted_at');
                                                    })
                                                    ->leftjoin('ms_sample_type', function ($join) {
                                                        $join
                                                            ->on(
                                                                'ms_sample_type.id_sample_type',
                                                                '=',
                                                                'tb_samples.typesample_samples',
                                                            )

                                                            ->whereNull('ms_sample_type.deleted_at')
                                                            ->whereNull('tb_samples.deleted_at');
                                                    })
                                                    ->select(
                                                        'id_laboratorium',
                                                        'ms_jenis_makanan.*',
                                                        'tb_pengesahan_hasil.pengesahan_hasil_date',
                                                        'typesample_samples',
                                                        'ms_sample_type.id_sample_type',
                                                        'ms_sample_type.name_sample_type',
                                                        'tb_samples.jenis_makanan_id',
                                                    )
                                                    ->distinct(
                                                        'id_laboratorium',
                                                        'ms_sample_type.id_sample_type',
                                                        'tb_samples.jenis_makanan_id',
                                                    )
                                                    ->where('id_laboratorium', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                                                    ->whereNotNull('pengesahan_hasil_date')
                                                    ->get();
                                            @endphp
                                            {{-- @if (isset($packet_print->pengesahan_hasil_date)) --}}
                                            @if (count($packet_print_sample_type) > 0)
                                                @if ($packet_print->id_sample_type == 'd34b4a50-4560-4fce-96c3-046c7080a986' or $packet_print->id_sample_type == 'c7c770a9-6bd7-4e30-83fc-0e4cc6a01fe0' or $packet_print->id_sample_type == '65df8403-b29f-4645-a1ed-12d2aeff1fbd')
                                                    @if (!$ketemu)
                                                        <a class="dropdown-item pointer"
                                                            data-href="{{ route('elits-release.print-mikro', [Request::segment(2), $packet_print->id_sample_type]) }}"
                                                            target="__blank" data-toggle="modal" data-target="#signOptionModal">{{ isset($packet_print->name_sample_type) ? $packet_print->name_sample_type : $packet_print->name_sample_type }}</a>
                                                    @endif
                                                    @php
                                                        if (isset($packet_print->name_jenis_makanan)) {
                                                            $ketemu = true;
                                                        }
                                                    @endphp
                                                @endif
                                            @endif
                                            {{-- @endif --}}
                                        @endforeach
                                        @if(empty(array_diff($samplesType, $samplesAirBersihAndAirMinum)))
                                          <a class="dropdown-item pointer"
                                             data-href="{{ route('elits-release.print-mikro-gabungan', [Request::segment(2)]) }}"
                                             target="__blank" data-toggle="modal" data-target="#signOptionModal">Air Bersih dan Air Minum</a>
                                        @endif
                                    </div>
                                </div>
                            @endif


                        </div>
                    @endif
                @endif

                @if (count($pudam) > 0 || count($makmin) > 0)
                    <div class="p-2">
                        <a data-href="{{ url('/elits-release/print-kimia', [Request::segment(2)]) }}" target="__blank" data-toggle="modal" data-target="#signOptionModal">
                            <button type="button" class="btn btn-fw btn-primary btn-icon-text">
                                Kimia <i class="fa fa-print"></i>
                            </button>
                        </a>
                    </div>
                @endif

            </div>


            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}

                </div>
            @endif

            <div class="col-12">
                <table id="order-listing-sample" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Laboratorium</th>
                            <th>Jenis Sarana</th>
                            <th>Method</th>

                            <th>Nomor Sampel</th>
                            <th>Status Terakhir</th>
                            @if (getAction('update') || getAction('delete'))
                                <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{--    Modal Option SIGN--}}
    <div class="modal fade" id="signOptionModal" tabindex="-1" aria-labelledby="signOptionTitle" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="tambahAgendaModalLabel">Tambah Agenda Sebelum Cetak Hasil</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearListSamples()">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <form id="agendaForm" action=""
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

    <script>
        $(document).ready(function() {
            if ("{{ getAction('update') || getAction('delete') }}") {
                var table = $('#order-listing-sample').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    responsive: true,
                    ajax: {
                        url: "/elits-samples/" + "{{ $id }}",
                        type: "GET",
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
                            data: 'nama_laboratorium',
                            name: 'nama_laboratorium'
                        },
                        {
                            data: 'name_sample_type',
                            name: 'name_sample_type'
                        },
                        {
                            data: 'count_method',
                            name: 'count_method'
                        },

                        {
                            data: 'codesample_samples',
                            name: 'codesample_samples'
                        },
                        {
                            data: 'last_status',
                            name: 'last_status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });
            } else {
                var table = $('#order-listing-sample').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    responsive: true,
                    ajax: {
                        url: "/elits-samples/" + "{{ $id }}",
                        type: "GET",
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
                            data: 'nama_laboratorium',
                            name: 'nama_laboratorium'
                        },
                        {
                            data: 'name_sample_type',
                            name: 'name_sample_type'
                        },
                        {
                            data: 'count_method',
                            name: 'count_method'
                        },
                        {
                            data: 'jenis_makanan',
                            name: 'jenis_makanan'
                        },
                        {
                            data: 'codesample_samples',
                            name: 'codesample_samples'
                        },
                        {
                            data: 'last_status',
                            name: 'last_status'
                        }
                    ]
                });
            }

            // datatables responsive
            new $.fn.dataTable.FixedHeader(table);

            $('.dropdown-scroll-menu').mousewheel(function(e, delta) {
                this.scrollLeft -= (delta * 40);
                e.preventDefault();
            });

            $('#order-listing-sample').on('click', '.btn-hapus', function() {
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
                                url: '/elits-samples-destroy/' + kode,
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
                                                document.location = '/elits-samples/' +
                                                    "{{ $id }}";
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
                                    swal("ERROR", "System tidak dapat menghapus data!",
                                        "error");
                                }
                            });
                        } else {
                            swal("Cancelled", "Hapus data dibatalkan!", "error");
                        }
                    });
            });
        });
    </script>
    <script>
      $(document).ready(function () {
        $('#signOptionModal').on('show.bs.modal', function (event) {
          var link = $(event.relatedTarget).data('href');

          var idSampleType = link.split('/');

          var idPermohonanUji = idSampleType[idSampleType.length -2];
          var idSampleType = idSampleType[idSampleType.length -1];
          var idSampleType = idSampleType.split('?')[0];


          getSamples(idPermohonanUji, idSampleType, function (samples) {
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

      function getSamples(idPermohonanUji, idSampleType, callback) {
            const url = `/elits-samples/list-samples/${idPermohonanUji}/${idSampleType}`;

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
