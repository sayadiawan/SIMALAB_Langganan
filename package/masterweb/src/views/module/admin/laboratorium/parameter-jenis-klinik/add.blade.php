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
                <li class="breadcrumb-item"><a href="{{ url('/elits-parameter-jenis-klinik') }}">Parameter Jenis
                    Klinik</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>create</span></li>
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
        action="{{ route('elits-parameter-jenis-klinik.store') }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="name_parameter_jenis_klinik">Nama Parameter Jenis</label>

          <input type="text" class="form-control" id="name_parameter_jenis_klinik" name="name_parameter_jenis_klinik"
            placeholder="Name parameter jenis klinik.." value="{{ old('name_parameter_jenis_klinik') }}" required>
        </div>

        <div class="form-group">
          <label for="code_parameter_jenis_klinik">Kode Parameter Jenis</label>

          <input type="text" class="form-control" id="code_parameter_jenis_klinik" name="code_parameter_jenis_klinik"
            placeholder="Kode parameter jenis klinik.." value="{{ old('code_parameter_jenis_klinik') }}" required>
          </select>
        </div>

        <div class="form-group">
          <label for="sort_parameter_jenis_klinik">Urutan Parameter Jenis</label>

          <input type="number" class="form-control" id="sort_parameter_jenis_klinik" name="sort_parameter_jenis_klinik"
            placeholder="Urutan parameter jenis klinik.." value="{{ old('sort_parameter_jenis_klinik') }}" required>
          </select>
        </div>

        <br>

      </form>
      <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
      <button type="button" onclick="document.location='{{ url('/elits-parameter-jenis-klinik') }}'"
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
                    document.location = '/elits-parameter-jenis-klinik';
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
