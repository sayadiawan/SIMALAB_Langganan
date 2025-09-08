@extends('masterweb::template.admin.layout')
@section('title')
  Tambah Permohonan Uji Klinik Parameter
@endsection


@section('content')
  {{--  --}}

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />


  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="template-demo">
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item">
                <a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                  Beranda</a>
              </li>

              <li class="breadcrumb-item">
                <a href="{{ url('/elits-permohonan-uji-klinik') }}">Permohonan Uji Klinik
                  Management</a>
              </li>

              <li class="breadcrumb-item">
                <a
                  href="{{ route('elits-permohonan-uji-klinik.permohonan-uji-klinik-parameter', $id_permohonan_uji_klinik) }}">Permohonan
                  Uji
                  Klinik Parameter
                  Management</a>
              </li>

              <li class="breadcrumb-item active" aria-current="page">
                <span>Create</span>
              </li>
            </ol>
          </nav>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h4>Tambah Permohonan Uji Klinik Parameter
      </h4>
    </div>

    <div class="card-body">
      <form
        action="{{ route('elits-permohonan-uji-klinik-2.store-permohonan-uji-klinik-parameter', $id_permohonan_uji_klinik) }}"
        method="POST" enctype="multipart/form-data" id="form">

        @csrf
        @method('POST')

        <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
{{--
        <div class="form-group">
          <label for="type_permohonan_uji_paket_klinik">Pilih Paket<span style="color: red">*</span></label>

          <select class="form-control" name="type_permohonan_uji_paket_klinik" id="type_permohonan_uji_paket_klinik">
            <option value="">-Pilih Paket-</option>
            <option value="C">Custom</option>
            <option value="P">Paket</option>
          </select>
        </div> --}}

        {{-- <div class="form-group" id="display-parameter-jenis-klinik">
          <label for="parameter_jenis_klinik"> Jenis Parameter<span style="color: red">*</span></label>
          <select class="form-control" name="parameter_jenis_klinik" id="parameter_jenis_klinik" style="width: 100%">
            <option value=""></option>
          </select>
        </div> --}}
        <div class="form-group">
          <label for="parameter_jenis_klinik">Pilih Jenis Parameter<span style="color: red">*</span></label>
          <select class="form-control" name="parameter_jenis_klinik" id="parameter_jenis_klinik" style="width: 100%;">
              <option value="">-Jenis Parameter-</option>
              @foreach($data_jenis as $jenis)
                  <option value="{{ $jenis->id_parameter_jenis_klinik }}">{{ $jenis->name_parameter_jenis_klinik }}</option>
              @endforeach
          </select>
        </div>

        <div class="card mb-3 mt-3" id="display-custom-detail-parameter" style="display: none">
          <div class="card-body">
            <div class="form-inline" id="content-custom-detail-parameter">
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="harga_permohonan_uji_paket_klinik"> Harga Total (Rp.)</label>

          <input type="text" class="form-control" name="harga_permohonan_uji_paket_klinik"
            id="harga_permohonan_uji_paket_klinik" readonly>
        </div>

        <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
        <button type="button" class="btn btn-light"
          onclick="document.location='{{ route('elits-permohonan-uji-klinik-2.permohonan-uji-klinik-parameter', $id_permohonan_uji_klinik) }}'">Kembali</button>
      </form>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

  <script>
    $(document).ready(function() {
      var CSRF_TOKEN = $('#csrf-token').val();
      $(document).ready(function() {
    // Inisialisasi select2 pada elemen dropdown
    $('#parameter_jenis_klinik').select2({
        placeholder: 'Pilih jenis parameter',
        allowClear: true
    });

    // Event handling untuk perubahan pilihan
    $('#parameter_jenis_klinik').change(function(e) {
        var id_parameter_jenis_klinik = $(this).val();
        // console.log(id_parameter_jenis_klinik);

        if (id_parameter_jenis_klinik) {
            // Tampilkan elemen lain atau lakukan aksi sesuai kebutuhan
            $('#display-parameter-jenis-klinik').show();

            $.ajax({
                type: "post",
                url: "{{ route('elits-permohonan-uji-klinik-2.get-parameter-custom-permohonan-uji-klinik-parameter') }}",
                data: {
                    _token: '{{ csrf_token() }}',
                    id_parameter_jenis_klinik: id_parameter_jenis_klinik,
                },
                dataType: "json",
                success: function(response) {
                  $('#display-custom-detail-parameter').show();
                    var html = '';

                    // Periksa apakah ada data yang tersedia
                    if (response.length > 0) {
                        html += '<div class="row">';
                        for (let i = 0; i < response.length; i++) {
                            html += '<div class="col-md-3">';
                            html += '<div class="form-check mr-5 justify-content-start">';
                            html += '<label class="form-check-label">';
                            html += '<input type="checkbox" class="form-check-input parameter-custom-klinik" name="parameter_custom_klinik[' + i + ']" id="parameter_custom_klinik_' + i + '" value="' + response[i].id + '" data-harga="' + response[i].harga + '" onclick="updateParameterCustom(this);" data-index="' + i + '">' + response[i].text;
                            html += '<i class="input-helper"></i></label>';
                            html += '</div>';
                            html += '</div>';
                        }
                        html += '</div>';
                    } else {
                        html += '<div class="row">';
                        html += '<div class="col-md-12">';
                        html += '<p>No pakets available.</p>';
                        html += '</div>';
                        html += '</div>';
                    }

                    // Tampilkan HTML di elemen yang dituju
                    $('#content-custom-detail-parameter').html(html);

                },
                error: function() {
                    swal("Error!", "Tidak berhasil mendapatkan data parameter!", "error");
                }
            });
        } else {
            // Sembunyikan elemen jika tidak ada jenis parameter yang dipilih
            $('#display-parameter-jenis-klinik').hide();
            $('#display-custom-detail-parameter').hide();
        }
    });
});


      // $('#type_permohonan_uji_paket_klinik').change(function(e) {
      //   if ($(this).val() == 'C') {
      //     $('#display-parameter-jenis-klinik').show();
      //     $("#parameter_jenis_klinik").select2({
      //       ajax: {
      //         url: "{{ route('getParameterJenisKlinik') }}",
      //         type: "post",
      //         dataType: 'json',
      //         delay: 250,
      //         data: function(params) {
      //           return {
      //             _token: CSRF_TOKEN,
      //             search: params.term // search term
      //           };
      //         },
      //         processResults: function(response) {
      //           return {
      //             results: response
      //           };
      //         },
      //         cache: true
      //       },
      //       allowClear: true,
      //       placeholder: 'Pilih jenis parameter'
      //     });

      //     $('#display-paket-detail-parameter').hide();
      //     // $('#display-custom-detail-parameter').show();
      //     $(this).closest('form').find("input").val("");

      //     $.ajax({
      //       type: "post",
      //       url: "{{ route('elits-permohonan-uji-klinik.get-parameter-custom-permohonan-uji-klinik-parameter') }}",
      //       data: {
      //         _token: CSRF_TOKEN,
      //         parameter_type: $('#parameter_jenis_klinik').val(),
      //         type_paket: $('#type_permohonan_uji_paket_klinik').val()
      //       },
      //       dataType: "json",
      //       success: function(response) {
      //         // console.log(response);

      //         $('#display-custom-detail-parameter').show();
      //         var html = '<div class="row">';

      //         for (let i = 0; i < response.length; i++) {
      //           html += '<div class="col-md-3">';
      //           html += '<div class="form-check mr-5 justify-content-start">';
      //           html += '<label class="form-check-label">';
      //           html +=
      //             '<input type="checkbox" class="form-check-input parameter-custom-klinik" name="parameter_custom_klinik[' +
      //             i +
      //             ']" id="parameter_custom_klinik_' +
      //             i + '" value="' + response[i].id + '" data-harga="' + response[i].harga +
      //             '" onclick="updateParameterCustom(this);" data-index="' + i + '">' + response[
      //               i].text;
      //           html += '<i class="input-helper"></i></label>';
      //           html += '</div>';
      //           html += '</div>';
      //         }

      //         html += '</div>';

      //         $('#content-custom-detail-parameter').html(html);
      //       },
      //       error: function() {
      //         swal("Error!", "Tidak berhasil mendapatkan data parameter!", "error");
      //       }
      //     });

      //     $('#parameter_jenis_klinik').change(function(e) {
      //       $.ajax({
      //         type: "post",
      //         url: "{{ route('elits-permohonan-uji-klinik.get-parameter-custom-permohonan-uji-klinik-parameter') }}",
      //         data: {
      //           _token: CSRF_TOKEN,
      //           parameter_type: $('#parameter_jenis_klinik').val(),
      //           type_paket: $('#type_permohonan_uji_paket_klinik').val()
      //         },
      //         dataType: "json",
      //         success: function(response) {
      //           console.log(response);

      //           $('#display-custom-detail-parameter').show();
      //           var html = '<div class="row">';

      //           for (let i = 0; i < response.length; i++) {
      //             html += '<div class="col-md-3">';
      //             html += '<div class="form-check mr-5 justify-content-start">';
      //             html += '<label class="form-check-label">';
      //             html +=
      //               '<input type="checkbox" class="form-check-input parameter-custom-klinik" name="parameter_custom_klinik[' +
      //               i +
      //               ']" id="parameter_custom_klinik_' +
      //               i + '" value="' + response[i].id + '" data-harga="' + response[i].harga +
      //               '" onclick="updateParameterCustom(this);" data-index="' + i + '">' + response[
      //                 i].text;
      //             html += '<i class="input-helper"></i></label>';
      //             html += '</div>';
      //             html += '</div>';
      //           }

      //           html += '</div>';

      //           $('#content-custom-detail-parameter').html(html);
      //         },
      //         error: function() {
      //           swal("Error!", "Tidak berhasil mendapatkan data parameter!", "error");
      //         }
      //       });
      //     })
      //   }

      //   if ($(this).val() == 'P') {
      //     $('#display-parameter-jenis-klinik').hide();
      //     $("#parameter_paket_klinik").val('').trigger('change');
      //     $('#display-paket-detail-parameter').show();
      //     $('#display-custom-detail-parameter').hide();

      //     $("#parameter_paket_klinik").select2({
      //       ajax: {
      //         url: "{{ route('elits-permohonan-uji-klinik.get-parameter-paket-permohonan-uji-klinik-parameter') }}",
      //         type: "post",
      //         dataType: 'json',
      //         delay: 250,
      //         data: function(params) {
      //           return {
      //             _token: CSRF_TOKEN,
      //             search: params.term // search term
      //           };
      //         },
      //         processResults: function(response) {
      //           /* return {
      //             results: response
      //           }; */

      //           return {
      //             results: $.map(response, function(obj) {
      //               return {
      //                 id: obj.id,
      //                 text: obj.text,
      //                 harga: obj.harga
      //               };
      //             })
      //           };
      //         },
      //         cache: true
      //       },
      //       allowClear: true,
      //       placeholder: 'Pilih parameter paket'
      //     }).on('select2:select', function(e) {
      //       var data = e.params.data;
      //       $(this).children('[value="' + data['id'] + '"]').attr({
      //         'data-harga': data["harga"], //dynamic value from data array
      //       });
      //     }).val(0).trigger('change');

      //     $('#parameter_paket_klinik').on('select2:select', function(e) {
      //       var data = e.params.data.harga;
      //       console.log(data);
      //       $('#harga_permohonan_uji_paket_klinik').val(data);
      //     });
      //   }
      // });
      // type_permohonan_uji_paket_klinik

      $('.btn-simpan').on('click', function() {
        $('#form').ajaxForm({
          headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
          },
          success: function(response) {
            if (response.status == true) {
              swal({
                  title: "Success!",
                  text: response.pesan,
                  icon: "success"
                })
                .then(function() {
                  document.location =
                    '/elits-permohonan-uji-klinik-2/permohonan-uji-klinik-parameter/' +
                    response
                    .id_permohonan_uji_klinik;
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
          error: function(xhr, status, error) {
            var err = eval("(" + xhr.responseText + ")");

            swal("Error!", err.Message, "error");
          }
        })
      })
    });

    function updateParameterCustom(obj) {
    var harga_paket_parameter = 0;

    // Periksa setiap checkbox dengan class 'parameter-custom-klinik'
    $('.parameter-custom-klinik').each(function(i, e) {
        if ($(this).is(':checked')) {
            // Tambahkan harga dari setiap checkbox yang dipilih
            harga_paket_parameter += parseInt($(this).attr('data-harga'));
        }
    });

    // Update nilai input dengan total harga yang telah dihitung
    $('#harga_permohonan_uji_paket_klinik').val(harga_paket_parameter);
    }

  </script>
@endsection
