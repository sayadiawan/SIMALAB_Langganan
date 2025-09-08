@extends('masterweb::template.admin.layout')

@section('title')
    Beranda
@endsection

@php
    $user = Auth()->user();
@endphp

@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    Data Sampel
                </div>

                <div class="p-2">
                    {{-- <button type="button" class="btn btn-primary mr-1" data-toggle="modal" data-target="#importExcel">
                    IMPORT EXCEL
                </button> --}}

                    <!-- Import Excel -->
                    <div class="modal fade" id="importExcel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <form method="post" action="/elits-excel/formImports" enctype="multipart/form-data">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Import Excel</h5>
                                    </div>
                                    <div class="modal-body">

                                        {{ csrf_field() }}

                                        <div class="form-group">

                                            <div class="row">

                                                <div class="col-md-12">
                                                    <label for="method">Pilih Method</label>
                                                    <select class="form-select form-control" id="method" name="method"
                                                        aria-label="Pilih Method">
                                                        @foreach ($methods as $method)
                                                            <option value="{{ $method->id_method }}">
                                                                {{ $method->params_method }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                            </div>
                                        </div>

                                        <label>Pilih file excel</label>
                                        <div class="form-group">

                                            <input type="file" name="file" required="required">
                                        </div>

                                    </div>
                                    <div id="test">
                                    </div>
                                    <div class="modal-footer ">
                                        <button type="button" id="download" class="btn btn-primary mr-auto">Download
                                            Format</button>


                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Import</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table id="empTable" class="table">
                            <thead>
                                <tr>
                                    <td>No.</td>
                                    <td>Pelanggan</td>
                                    <td>Nomor Sampel</td>
                                    <td>Jenis Sampel</td>
                                    <td>Status Pengajuan</td>
                                    <td>Tanggal Diterima</td>
                                    <td>Aksi</td>
                                </tr>
                            </thead>

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
            $("#download").click(function() {

                var id_method = $('#method').find(":selected").val()
                console.log(id_method)
                var url = "{{ route('elits-excel.downloadFormImports', ':id') }}";

                url = url.replace(":id", id_method);
                console.log(url);

                window.location = url;

            })

            var table = $('#empTable').DataTable({
                processing: true,
                serverSide: true,
                stateSave: true,
                responsive: true,
                ajax: {
                    url: "{{ route('elits-analys.getSamplePagination') }}",
                    type: "GET",
                    data: function(d) {
                        d.search = $('input[type="search"]').val()
                    }
                },
                columns: [{
                        data: 'nomer',
                    },
                    {
                        data: 'pelanggan',
                        name: 'pelanggan'
                    },
                    {
                        data: 'codesample_samples',
                        name: 'codesample_samples'
                    },
                    {
                        data: 'name_sample_type',
                        name: 'name_sample_type'
                    },
                    {
                        data: 'last_status',
                        name: 'last_status'
                    },

                    {
                        data: 'date_sending',
                        name: 'date_sending'
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
        })
    </script>
@endsection
