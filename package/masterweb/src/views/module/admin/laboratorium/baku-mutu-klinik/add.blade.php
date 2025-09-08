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
        action="{{ route('elits-baku-mutu-klinik.store') }}" method="POST" novalidate>

        @csrf

        <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
        <input type="hidden" class="form-control" name="lab_id" id="lab_id" value="{{ $get_lab->id_laboratorium }}"
          readonly>

        <div class="form-group">
          <label for="parameter_jenis_klinik_id">Parameter Jenis Klinik <span style="color: red">*</span></label>

          <select class="form-control" name="parameter_jenis_klinik_id" id="parameter_jenis_klinik_id">
            <option value=""></option>
          </select>
        </div>

        <div class="form-group display-parameter-satuan-klinik-id" style="display: none">
          <label for="parameter_satuan_klinik_id">Parameter Satuan Klinik <span style="color: red">*</span></label>

          <select class="form-control" name="parameter_satuan_klinik_id" id="parameter_satuan_klinik_id">
            <option value=""></option>
          </select>
        </div>

        <div class="form-group">
          <label for="unit_id">Satuan</label>

          <select class="form-control unit_id" name="unit_id" id="unit_id">
            <option value=""></option>
          </select>
        </div>

        <div class="form-group">
          <label for="library_id">Acuan Baku Mutu <span style="color: red">*</span></label>

          <select class="form-control" name="library_id" id="library_id">
            <option value=""></option>
          </select>
        </div>

        <div class="form-group">
          <label for="is_khusus_baku_mutu">Data Khusus </label>

          <select class="form-control" name="is_khusus_baku_mutu" id="is_khusus_baku_mutu">
            <option value="0">General</option>
            <option value="1">Specific</option>
          </select>
        </div>

        <div class="card display-data-khusus mb-3" style="display: none">
          <div class="card-body">

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="is_khusus">Umur Minimal</label>

                  <input type="number" class="form-control" name="minimal_umur_baku_mutu" id="minimal_umur_baku_mutu"
                    placeholder="Masukkan minimal umur">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="is_khusus">Umur Maksimal</label>

                  <input type="number" class="form-control" name="maksimal_umur_baku_mutu" id="maksimal_umur_baku_mutu"
                    placeholder="Masukkan maksimal umur">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label for="gender_baku_mutu">Jenis Kelamin <span style="color: red">*</span></label>

              <select class="form-control" name="gender_baku_mutu" id="gender_baku_mutu">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>
          </div>
        </div>

        <input type="hidden" class="form-control" name="is_sub_parameter_satuan_baku_mutu"
          id="is_sub_parameter_satuan_baku_mutu" value="0" readonly>

        <div class="parameter-satuan-non-sub">
          <div class="form-group">
            <label for="min">Kadar Min Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapan koma, maka
                menggunakan
                .
                (titik), apabila tidak ada kosongi)</b></label>
            <input type="text" class="form-control" id="min" name="min"
              placeholder="Kadar Min Baku Mutu">
          </div>

          <div class="form-group">
            <label for="max">Kadar Max Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapat koma, maka
                menggunakan
                .
                (titik), apabila tidak ada kosongi)</b></label>
            <input type="text" class="form-control" id="max" name="max"
              placeholder="Kadar Max Baku Mutu">
          </div>

          <div class="form-group">
            <label for="equal">Nilai Harus Sama Dengan <br><b>(Apabila nilai baku mutu bukan berupa range minimal
                maksimal
                misal (Negatif atau Positif) maka isi disini, apabila tidak maka kosongi)</b></label>
            <input type="text" class="form-control" id="equal" name="equal"
              placeholder="Nilai Harus Sama Dengan">
          </div>


          <div class="form-group">
            <label for="nilai_baku_mutu">Nilai Baku Mutu di Laporan <span style="color: red">*</span></label>
            <input type="text" class="form-control" id="nilai_baku_mutu" name="nilai_baku_mutu"
              placeholder="Nilai Baku Mutu" value="{{ old('harga_parameter_paket_klinik') }}">
          </div>
        </div>

        <div class="parameter-satuan-with-sub" style="display: none">
          <div class="parameter-satuan-with-sub-form"></div>
        </div>

        <br>

      </form>
      <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
      <button type="button" onclick="document.location='{{ url('/elits-baku-mutu-klinik') }}'"
        class="btn btn-light">Kembali</button>
    </div>
  </div>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

  <script>
    function goBack() {
      window.history.back();
    }

    $(document).ready(function() {
      $(function() {
        var CSRF_TOKEN = $('#csrf-token').val();

        $("#parameter_jenis_klinik_id").select2({
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
                results: $.map(response, function(obj) {
                  return {
                    id: obj.id,
                    text: obj.text
                  };
                })
              };
            },
            cache: true
          },
          placeholder: 'Pilih jenis parameter',
          allowClear: true
        });

        $('.parameter-satuan-non-sub').hide();

        $('#parameter_jenis_klinik_id').on('select2:unselecting', function(e) {
          if ($("#parameter_satuan_klinik_id").hasClass("select2-hidden-accessible")) {
            $("#parameter_satuan_klinik_id").val('').trigger('change');

            $('.display-parameter-satuan-klinik-id').hide();
            $('.parameter-satuan-non-sub').hide();
            $('.parameter-satuan-with-sub').hide();
          }
        });

        $('#parameter_jenis_klinik_id').on('select2:selecting', function(e) {
          $('.display-parameter-satuan-klinik-id').show();
          $("#parameter_satuan_klinik_id").val('').trigger('change');

          $('.parameter-satuan-non-sub').hide();
          $('.parameter-satuan-with-sub').hide();

          $("#parameter_satuan_klinik_id").select2({
            ajax: {
              url: "{{ route('getParameterSatuanKlinik') }}",
              type: "post",
              dataType: 'json',
              delay: 250,
              data: function(params) {
                return {
                  _token: CSRF_TOKEN,
                  search: params.term, // search term
                  param: $("#parameter_jenis_klinik_id").val()
                };
              },
              processResults: function(response) {
                return {
                  results: $.map(response, function(obj) {
                    return {
                      id: obj.id,
                      text: obj.text
                    };
                  })
                };
              },
              cache: true
            },
            placeholder: 'Pilih paramater satuan',
            allowClear: true,
          });
        });

        $("#library_id").select2({
          ajax: {
            url: "{{ route('getLibrary') }}",
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
                results: $.map(response, function(obj) {
                  return {
                    id: obj.id,
                    text: obj.text
                  };
                })
              };
            },
            cache: true
          },
          placeholder: 'Pilih library',
          allowClear: true
        });

        $(".unit_id").select2({
          ajax: {
            url: "{{ route('getDataUnitBySelect') }}",
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
                results: $.map(response, function(obj) {
                  return {
                    id: obj.id,
                    text: obj.text
                  };
                })
              };
            },
            cache: true
          },
          placeholder: 'Pilih satuan',
          allowClear: true
        });

        $('#is_khusus_baku_mutu').change(function(e) {
          e.preventDefault();

          if ($('#is_khusus_baku_mutu').val() == 1) {
            $('.display-data-khusus').show();
          } else {
            $('.display-data-khusus').hide();
          }
        });

        //   logic untuk mendapatkan parameter satuan yang memiliki sub dan non-sub
        $('#parameter_satuan_klinik_id').change(function() {

          var parameter_jenis = $('#parameter_jenis_klinik_id').val();
          var parameter_satuan = $('#parameter_satuan_klinik_id').val();

          if (parameter_satuan !== null && parameter_satuan !== '') {
            $.ajax({
              type: "POST",
              url: "{{ route('checkBakuMutuSubParameterSatuan') }}",
              data: {
                _token: CSRF_TOKEN,
                parameter_jenis: parameter_jenis,
                parameter_satuan: parameter_satuan,
              },
              dataType: "JSON",
              success: function(response) {
                if (response.status == true) {
                  $('.parameter-satuan-non-sub').hide();
                  $('.parameter-satuan-with-sub').show();

                  $('#is_sub_parameter_satuan_baku_mutu').val(1);

                  // forloop sub parameter satuan
                  var sub_parameter_len = response.data_sub.length;
                  var card_html = '';

                  for (let i = 0; i < sub_parameter_len; i++) {
                    card_html += '<hr>';

                    card_html += '<h6 class="card-title"><strong>' + response.data_sub[i]
                      .name_parameter_sub_satuan_klinik + '</strong></h6>';

                    card_html +=
                      '<input type="hidden" class="form-control" id="parameter_sub_satuan_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="parameter_sub_satuan_baku_mutu_detail_parameter_klinik[' + i +
                      ']" value="' + response.data_sub[i].id_parameter_sub_satuan_klinik +
                      '" readonly>' +
                      '</div>';

                    card_html += '<div class="form-group">' +
                      '<label for="unit_id_baku_mutu_detail_parameter_klinik">Satuan</label>' +
                      '<select class="form-control unit_id" id="unit_id_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="unit_id_baku_mutu_detail_parameter_klinik[' + i +
                      ']">' +
                      '<option value=""></option>' +
                      '</select>' +
                      '</div>';

                    card_html += '<div class="form-group">' +
                      '<label for="min">Kadar Min Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapan koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>' +
                      '<input type="text" class="form-control" id="min_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="min_baku_mutu_detail_parameter_klinik[' + i +
                      ']" placeholder="Kadar Min Baku Mutu">' +
                      '</div>';

                    card_html += '<div class="form-group">' +
                      '<label for="max">Kadar Max Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapat koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>' +
                      '<input type="text" class="form-control" id="max_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="max_baku_mutu_detail_parameter_klinik[' + i +
                      ']" placeholder="Kadar Max Baku Mutu">' +
                      '</div>';

                    card_html += '<div class="form-group">' +
                      '<label for="equal">Nilai Harus Sama Dengan <br><b>(Apabila nilai baku mutu bukan berupa range minimal maksimal misal (Negatif atau Positif) maka isi disini, apabila tidak maka kosongi)</b></label>' +
                      '<input type="text" class="form-control" id="equal_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="equal_baku_mutu_detail_parameter_klinik[' + i +
                      ']" placeholder="Nilai Harus Sama Dengan">' +
                      '</div>';

                    card_html += '<div class="form-group">' +
                      '<label for="nilai_baku_mutu">Nilai Baku Mutu di Laporan <span style="color: red">*</span></label>' +
                      '<input type="text" class="form-control" id="nilai_baku_mutu_detail_parameter_klinik_' +
                      i +
                      '" name="nilai_baku_mutu_detail_parameter_klinik[' + i +
                      ']" placeholder="Nilai Baku Mutu" value="{{ old('nilai_baku_mutu_detail_parameter_klinik') }}">' +
                      '</div>';
                  }

                  $('.parameter-satuan-with-sub .parameter-satuan-with-sub-form').html(card_html);

                  $(".unit_id").select2({
                    ajax: {
                      url: "{{ route('getDataUnitBySelect') }}",
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
                          results: $.map(response, function(obj) {
                            return {
                              id: obj.id,
                              text: obj.text
                            };
                          })
                        };
                      },
                      cache: true
                    },
                    placeholder: 'Pilih satuan',
                    allowClear: true
                  });
                }

                if (response.status == false) {
                  $('.parameter-satuan-non-sub').show();
                  $('.parameter-satuan-with-sub').hide();

                  $('#is_sub_parameter_satuan_baku_mutu').val(0);
                }
              },
              error: function() {
                swal("Error!", "System gagal medapatkan data!", "error");
              }
            });
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
                    document.location = '/elits-baku-mutu-klinik';
                  });
              } else {
                var pesan = "";
                var data_pesan = response.pesan;
                const wrapper = document.createElement('div');

                if (typeof(data_pesan) == 'object') {
                  jQuery.each(data_pesan, function(key, value) {
                    console.log(value);
                    pesan += value + '. <br>';
                    wrapper.innerHTML = pesan;
                  });

                  swal({
                    title: "Error!",
                    content: wrapper,
                    icon: "warning"
                  });
                } else {
                  swal({
                    title: "Error!",
                    text: response.pesan,
                    icon: "warning"
                  });
                }
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
