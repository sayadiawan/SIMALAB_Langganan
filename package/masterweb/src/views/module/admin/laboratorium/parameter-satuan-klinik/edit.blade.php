@extends('masterweb::template.admin.layout')
@section('title')
    Parameter Satuan Klinik
@endsection

@section('content')





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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-parameter-satuan-klinik') }}">Parameter
                                        Satuan
                                        Klinik</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>Edit</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card">
        <div class="card-body">
            <form enctype="multipart/form-data" class="forms-sample" id="form"
                action="{{ route('elits-parameter-satuan-klinik.update', $item->id_parameter_satuan_klinik) }}"
                method="POST">

                @csrf
                @method('PUT')

                <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
                <div class="form-group">
                    <label for="parameter_jenis_klinik">Parameter Jenis Klinik</label>

                    <select class="form-control" name="parameter_jenis_klinik" id="parameter_jenis_klinik">
                        <option value="{{ $item->parameter_jenis_klinik }}" selected>
                            {{ $item->parameterjenisklinik->name_parameter_jenis_klinik }}</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name_parameter_satuan_klinik">Nama Parameter Satuan</label>

                    <input type="text" class="form-control" id="name_parameter_satuan_klinik"
                        name="name_parameter_satuan_klinik" placeholder="Nama parameter satuan klinik.."
                        value="{{ $item->name_parameter_satuan_klinik ?? old('name_parameter_satuan_klinik') }}" required>
                </div>

                <div class="form-group">
                    <label for="metode_parameter_satuan_klinik">Metode Parameter Satuan</label>

                    <input type="text" class="form-control" id="metode_parameter_satuan_klinik"
                        name="metode_parameter_satuan_klinik" placeholder="Metode parameter satuan klinik.."
                        value="{{ $item->metode_parameter_satuan_klinik ?? '-' }}" required>
                </div>

                <div class="form-group">
                    <label for="loinc_parameter_satuan_klinik">Loinc Parameter Satuan</label>

                    <input type="text" class="form-control" id="loinc_parameter_satuan_klinik"
                        name="loinc_parameter_satuan_klinik" placeholder="Loinc parameter satuan klinik.."
                        value="{{ $item->loinc_parameter_satuan_klinik ?? old('loinc_parameter_satuan_klinik') }}" required>
                </div>

                <div class="form-group">
                    <label for="jenis_pemeriksaan_parameter_satuan_klinik">Jenis Pemeriksaan</label>

                    <select class="form-control" name="jenis_pemeriksaan_parameter_satuan_klinik"
                        id="jenis_pemeriksaan_parameter_satuan_klinik">

                        @if (reference_sas('jenis_pemeriksaan_klinik'))
                            @foreach (reference_sas('jenis_pemeriksaan_klinik') as $key => $value)
                                <option value="{{ $key }}"
                                    {{ $item->jenis_pemeriksaan_parameter_satuan_klinik == $key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                            @endforeach
                        @endif

                    </select>
                </div>

                <div class="form-group">
                    <label for="is_sub_parameter_satuan_klinik">Apakah memiliki sub parameter satuan?</label>
                    <div class="form-check">
                        <input class="form-check-input is_sub_parameter_satuan_klinik" type="radio" value="1"
                            name="is_sub_parameter_satuan_klinik" id="is_sub_parameter_satuan_klinik_1"
                            {!! $item->is_sub_parameter_satuan_klinik == '1' ? 'checked' : '' !!}>
                        <label class="form-check-label" for="flexRadioDefault1">
                            Ya
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input is_sub_parameter_satuan_klinik" type="radio" value="0"
                            name="is_sub_parameter_satuan_klinik" id="is_sub_parameter_satuan_klinik_2"
                            {!! $item->is_sub_parameter_satuan_klinik == '0' ? 'checked' : '' !!}>
                        <label class="form-check-label" for="is_sub_parameter_satuan_klinik">
                            Tidak
                        </label>
                    </div>
                </div>

                <div class="sub-parameter-satuan" id="sub-parameter-satuan" {!! $item->is_sub_parameter_satuan_klinik == '1' ? '' : 'style="display: none"' !!}>
                    <div class="row">
                        <div class="table-responsive">
                            <table id="table-sub-parameter-satuan" class="table">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">No</th>
                                        <th style="width: 70%">Sub Parameter</th>
                                        <th style="width: 20%">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @if (count($data_subitem) > 0)
                                        @php
                                            $no = 1;
                                        @endphp

                                        @foreach ($data_subitem as $key_ds => $item_ds)
                                            <tr id="row_{{ $no }}" class="tr_row">
                                                <td style="width: 10%">
                                                    <h5>{{ $no }}</h5>
                                                </td>

                                                <td style="width: 70%">
                                                    <input type="text"
                                                        class="form-control name_parameter_sub_satuan_klinik"
                                                        name="name_parameter_sub_satuan_klinik[{{ $no }}]"
                                                        id="name_parameter_sub_satuan_klinik_{{ $no }}"
                                                        value="{{ $item_ds->name_parameter_sub_satuan_klinik }}">
                                                </td>

                                                <td style="width: 20%">
                                                    <button type="button" class="btn btn-primary btn-add-row"
                                                        data-row="{{ $no }}"
                                                        onclick="addRow({{ $no }})">
                                                        <i class="fas fa-plus"></i>
                                                    </button>

                                                    <button type="button" class="btn btn-danger btn-remove-row"
                                                        data-row="{{ $no }}"
                                                        onclick="removeRow({{ $no }})">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </td>
                                            </tr>

                                            @php
                                                $no++;
                                            @endphp
                                        @endforeach
                                    @else
                                        <tr id="row_1" class="tr_row">
                                            <td style="width: 10%">
                                                <h5>1</h5>
                                            </td>

                                            <td style="width: 70%">
                                                <input type="text"
                                                    class="form-control name_parameter_sub_satuan_klinik"
                                                    name="name_parameter_sub_satuan_klinik[1]"
                                                    id="name_parameter_sub_satuan_klinik_1">
                                            </td>

                                            <td style="width: 20%">
                                                <button type="button" class="btn btn-primary btn-add-row" data-row="1"
                                                    onclick="addRow(1)">
                                                    <i class="fas fa-plus"></i>
                                                </button>

                                                <button type="button" class="btn btn-danger btn-remove-row"
                                                    data-row="1" onclick="removeRow(1)">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group">
                  <label for="sort_parameter_satuan_klinik">Urutan Parameter Satuan</label>

                  <input type="number" class="form-control" id="sort_parameter_satuan_klinik"
                      name="sort_parameter_satuan_klinik" placeholder="Urutan parameter satuan klinik.."
                      value="{{ $item->sort_parameter_satuan_klinik ?? old('sort_parameter_satuan_klinik') }}"
                      required>
                </div> --}}

                {{-- <div class="form-group">
                  <label for="sort_parameter_satuan_klinik">Urutan Parameter Satuan</label>
                  <p>Urutan setelah :</p>
                  <select class="form-control" id="after_sort_parameter_satuan_klinik" name="after_sort_parameter_satuan_klinik">
                      <option value="">Pilih posisi setelah urutan...</option>
                      @foreach ($existing_sorts as $sort)
                          <option value="{{ $sort->sort_parameter_satuan_klinik }}"
                              {{ ($sort->sort_parameter_satuan_klinik == $item->sort_parameter_satuan_klinik) ? 'selected' : '' }}>
                              {{ $sort->sort_parameter_satuan_klinik }} - {{ $sort->name_parameter_satuan_klinik }}
                          </option>
                      @endforeach
                  </select>

                  <input type="text" class="form-control mt-2" id="sort_parameter_satuan_klinik" name="sort_parameter_satuan_klinik" value="{{ $item->sort_parameter_satuan_klinik ?? old('sort_parameter_satuan_klinik') }}" readonly>
                </div> --}}

                <div class="form-group">
                    <label for="sort_parameter_satuan_klinik">Urutan Parameter Satuan</label>

                    <!-- Input Urutan Parameter Satuan Klinik -->
                    <input type="number" class="form-control" id="sort_parameter_satuan_klinik"
                        name="sort_parameter_satuan_klinik" placeholder="Urutan parameter satuan klinik.."
                        value="{{ $item->sort_parameter_satuan_klinik ?? old('sort_parameter_satuan_klinik') }}" required
                        readonly>

                    <!-- Tombol Pindah Urutan -->
                    <button class="btn btn-primary mt-2" type="button" id="btn-show-dropdown">Pindahkan Urutan</button>

                    <!-- Dropdown yang akan disembunyikan sampai tombol ditekan -->
                    <div id="dropdown-move-order" style="display: none; margin-top: 10px;">
                        <label for="after_sort_parameter_satuan_klinik">Pindahkan Setelah</label>
                        <select name="after_sort_parameter_satuan_klinik" id="after_sort_parameter_satuan_klinik"
                            class="selec2">
                            <option value="">-- Pilih Nomor Urut --</option>
                            @foreach ($existing_sorts as $sort)
                            {{-- Jangan tampilkan jika urutan adalah 0 --}}
                                @if ($sort->sort_parameter_satuan_klinik > 0)
                                <option value="{{ $sort->sort_parameter_satuan_klinik }}">
                                    Setelah urutan {{ $sort->sort_parameter_satuan_klinik }} - {{ $sort->name_parameter_satuan_klinik }} ({{ $sort->parameterjenisklinik->name_parameter_jenis_klinik }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label for="harga_satuan_parameter_satuan_klinik">Harga Parameter Satuan (Rupiah)</label>

                    <input type="number" class="form-control" id="harga_satuan_parameter_satuan_klinik"
                        name="harga_satuan_parameter_satuan_klinik" placeholder="Harga parameter satuan klinik.."
                        value="{{ $item->harga_satuan_parameter_satuan_klinik ?? old('harga_satuan_parameter_satuan_klinik') }}"
                        required>
                </div>

                <div class="form-group">
                    <label for="ket_default_parameter_satuan_klinik">Keterangan Default Parameter Satuan</label>

                    <textarea type="text" class="form-control" id="ket_default_parameter_satuan_klinik"
                        name="ket_default_parameter_satuan_klinik" placeholder="Metode parameter satuan klinik.." style="height:120px"
                        required>{!! rubahNilaikeForm($item->ket_default_parameter_satuan_klinik ?? old('ket_default_parameter_satuan_klinik')) !!}</textarea>
                </div>

                <br>

            </form>
            <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
            <button type="button" onclick="document.location='{{ url('/elits-parameter-satuan-klinik') }}'"
                class="btn btn-light">Kembali</button>
        </div>
    </div>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>

    <script>
        function goBack() {
            window.history.back();
        }

        function addRow(row) {
            var tableSubParameterSatuanLength = $("#table-sub-parameter-satuan tbody .tr_row").length;

            console.log('tableSubParameterSatuanLength ' + tableSubParameterSatuanLength);

            for (x = 0; x < tableSubParameterSatuanLength; x++) {
                var tr = $("#table-sub-parameter-satuan tbody tr")[x];
                var count = $(tr).attr('id');

                console.log(count);
                count = Number(count.substring(4));

                console.log(count);
            } // /for

            var count_table_tbody_tr = $("#table-sub-parameter-satuan tbody .tr_row").length;
            id_html = count + 1;

            var dom_html = `<tr id="row_${id_html}" class="tr_row">
                            <td style="width: 10%">
                                <h5>${id_html}</h5>
                            </td>

                            <td style="width: 70%">
                                <input type="text" class="form-control name_parameter_sub_satuan_klinik" name="name_parameter_sub_satuan_klinik[${id_html}]" id="name_parameter_sub_satuan_klinik_${id_html}">
                            </td>

                            <td style="width: 20%">
                                <button type="button" class="btn btn-primary btn-add-row" data-row="${id_html}" onclick="addRow(${id_html})">
                                    <i class="fas fa-plus"></i>
                                </button>

                                <button type="button" class="btn btn-danger btn-remove-row" data-row="${id_html}" onclick="removeRow(${id_html})">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </td>
                        </tr>`;

            // $(document.getElementById('main-bdy')).append(dom_html);

            if (count_table_tbody_tr >= 1) {
                $("#table-sub-parameter-satuan tbody .tr_row:last").after(dom_html);
            } else {
                $("#table-sub-parameter-satuan tbody").html(dom_html);
            }
        }

        function removeRow(row) {
            var count_table_tbody_tr = $("#table-sub-parameter-satuan tbody .tr_row").length;

            if (count_table_tbody_tr > 1) {
                $("#table-sub-parameter-satuan tbody .tr_row#row_" + row).remove();

                getSubAmount()
            }
        }
        $(document).ready(function() {
            $(function() {
                var CSRF_TOKEN = $('#csrf-token').val();

                $("#parameter_jenis_klinik").select2({
                    ajax: {
                        url: "{{ route('getParameterJenisKlinik') }}",
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
                    }
                });

                $('.is_sub_parameter_satuan_klinik').change(function(e) {
                    //logic jika parameter satuan memiliki sub
                    if (($(this).val() == '0') && $('#is_sub_parameter_satuan_klinik_2').is(
                            ':checked')) {
                        console.log('close');
                        $('.sub-parameter-satuan').hide();
                    }

                    if (($(this).val() == '1') && $('#is_sub_parameter_satuan_klinik_1').is(
                            ':checked')) {
                        console.log('open');
                        $('.sub-parameter-satuan').show();
                    }
                });

                $('.btn-simpan').on('click', function() {
                    $('#form').ajaxSubmit({
                        success: function(response) {
                            if (response.status == true) {
                                swal({
                                        title: "Success!",
                                        text: response.pesan,
                                        icon: "success"
                                    })
                                    .then(function() {
                                        document.location =
                                            '/elits-parameter-satuan-klinik';
                                    });
                            } else {
                                var pesan = "";

                                jQuery.each(response.pesan, function(key, value) {
                                    pesan += value + '. ';
                                });

                                swal({
                                    title: "Error!",
                                    text: pesan,
                                    icon: "warning"
                                });
                            }
                        },
                        error: function() {
                            swal("Error!", "System gagal menyimpan!", "error");
                        }
                    })
                })
            })
        })

        $(document).ready(function() {
            // Saat tombol "Pindahkan Setelah" ditekan, tampilkan dropdown
            $('#btn-show-dropdown').on('click', function() {
                $('#dropdown-move-order').toggle(); // Mengubah visibilitas dropdown
            });

            // Inisialisasi Select2 pada dropdown
            $('#after_sort_parameter_satuan_klinik').select2({
                placeholder: 'Pilih urutan setelah...',
                allowClear: true
            });
        });

        $(document).ready(function() {
            $('#after_sort_parameter_satuan_klinik').on('change', function() {
                const selectedSort = parseInt($(this).val());
                $('#sort_parameter_satuan_klinik').val(selectedSort + 1);
            });
        });
    </script>
@endsection
