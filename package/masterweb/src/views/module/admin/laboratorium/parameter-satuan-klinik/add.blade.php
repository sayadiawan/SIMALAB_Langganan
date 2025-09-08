@extends('masterweb::template.admin.layout')
@section('title')
  Parameter Jenis Klinik
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
                <li class="breadcrumb-item"><a href="{{ url('/elits-parameter-satuan-klinik') }}">Parameter Jenis
                    Klinik</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>Create</span></li>
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
        action="{{ route('elits-parameter-satuan-klinik.store') }}" method="POST">

        @csrf

        <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
        <div class="form-group">
          <label for="parameter_jenis_klinik">Parameter Jenis Klinik</label>

          <select class="form-control" name="parameter_jenis_klinik" id="parameter_jenis_klinik">
            <option value=""></option>
          </select>
        </div>

        <div class="form-group">
          <label for="name_parameter_satuan_klinik">Nama Parameter Satuan</label>

          <input type="text" class="form-control" id="name_parameter_satuan_klinik" name="name_parameter_satuan_klinik"
            placeholder="Nama parameter satuan klinik.." value="{{ old('name_parameter_satuan_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="metode_parameter_satuan_klinik">Metode Parameter Satuan</label>

          <input type="text" class="form-control" id="metode_parameter_satuan_klinik" name="metode_parameter_satuan_klinik"
            placeholder="Metode parameter satuan klinik.." value="{{ old('metode_parameter_satuan_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="loinc_parameter_satuan_klinik">Loinc Parameter Satuan</label>

          <input type="text" class="form-control" id="loinc_parameter_satuan_klinik" name="loinc_parameter_satuan_klinik"
            placeholder="Loinc parameter satuan klinik.." value="{{ old('loinc_parameter_satuan_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="jenis_pemeriksaan_parameter_satuan_klinik">Jenis Pemeriksaan</label>

          <select class="form-control" name="jenis_pemeriksaan_parameter_satuan_klinik"
            id="jenis_pemeriksaan_parameter_satuan_klinik">

            @if (reference_sas('jenis_pemeriksaan_klinik'))
              @foreach (reference_sas('jenis_pemeriksaan_klinik') as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
              @endforeach
            @endif
          </select>
        </div>

        <div class="form-group">
          <label for="is_sub_parameter_satuan_klinik">Apakah memiliki sub parameter satuan?</label>
          <div class="form-check">
            <input class="form-check-input is_sub_parameter_satuan_klinik" type="radio" value="1"
              name="is_sub_parameter_satuan_klinik" id="is_sub_parameter_satuan_klinik_1">
            <label class="form-check-label" for="flexRadioDefault1">
              Ya
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input is_sub_parameter_satuan_klinik" type="radio" value="0"
              name="is_sub_parameter_satuan_klinik" id="is_sub_parameter_satuan_klinik_2" checked>
            <label class="form-check-label" for="is_sub_parameter_satuan_klinik">
              Tidak
            </label>
          </div>
        </div>

        <div class="sub-parameter-satuan" id="sub-parameter-satuan" style="display: none">
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
                  <tr id="row_1" class="tr_row">
                    <td style="width: 10%">
                      <h5>1</h5>
                    </td>

                    <td style="width: 70%">
                      <input type="text" class="form-control name_parameter_sub_satuan_klinik"
                        name="name_parameter_sub_satuan_klinik[1]" id="name_parameter_sub_satuan_klinik_1">
                    </td>

                    <td style="width: 20%">
                      <button type="button" class="btn btn-primary btn-add-row" data-row="1" onclick="addRow(1)">
                        <i class="fas fa-plus"></i>
                      </button>

                      <button type="button" class="btn btn-danger btn-remove-row" data-row="1" onclick="removeRow(1)">
                        <i class="fas fa-minus"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="sort_parameter_satuan_klinik">Urutan Parameter Satuan</label>

          <input type="number" class="form-control" id="sort_parameter_satuan_klinik"
            name="sort_parameter_satuan_klinik" placeholder="Urutan parameter satuan klinik.."
            value="{{ old('sort_parameter_satuan_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="harga_satuan_parameter_satuan_klinik">Harga Parameter Satuan (Rupiah)</label>

          <input type="number" class="form-control" id="harga_satuan_parameter_satuan_klinik"
            name="harga_satuan_parameter_satuan_klinik" placeholder="Harga parameter satuan klinik.."
            value="{{ old('harga_satuan_parameter_satuan_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="ket_default_parameter_satuan_klinik">Keterangan Default Parameter Satuan</label>

          <textarea type="text" class="form-control" id="ket_default_parameter_satuan_klinik" name="ket_default_parameter_satuan_klinik"
            placeholder="Keterangan default parameter satuan klinik.." style="height:120px" value="{{ old('ket_default_parameter_satuan_klinik') }}" required></textarea>
        </div>

        <br>

      </form>
      <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
      <button type="button" onclick="document.location='{{ url('/elits-parameter-satuan-klinik') }}'"
        class="btn btn-light">Kembali</button>
    </div>
  </div>

  {{-- <script src="https://cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script> --}}
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>
{{--
  <script>
    CKEDITOR.replace( 'ket_default_parameter_satuan_klinik' );
  </script> --}}

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
          },
          placeholder: 'Jenis Parameter',
          allowClear: true
        });

        $("#jenis_pemeriksaan_parameter_satuan_klinik").select2({
          placeholder: 'Jenis Pemeriksaan Parameter',
          allowClear: true
        });

        $('.sub-parameter-satuan').hide();

        $('.is_sub_parameter_satuan_klinik').change(function(e) {
          //logic jika parameter satuan memiliki sub
          if (($(this).val() == '0') && $('#is_sub_parameter_satuan_klinik_2').is(':checked')) {
            console.log('close');
            $('.sub-parameter-satuan').hide();
          }

          if (($(this).val() == '1') && $('#is_sub_parameter_satuan_klinik_1').is(':checked')) {
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
                    document.location = '/elits-parameter-satuan-klinik';
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
  </script>
@endsection
