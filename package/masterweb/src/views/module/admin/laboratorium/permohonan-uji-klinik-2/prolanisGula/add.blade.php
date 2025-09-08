@extends('masterweb::template.admin.layout')
@section('title')
  Permohonan Uji Klinik Prolanis Gula
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
                <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji-klinik-2/prolanis-gula') }}">Permohonan Uji Klinik Prolanis
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
        action="{{ route('elits-permohonan-uji-klinik-2.store-prolanis-gula') }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="nama_prolanis_gula">Nama Prolanis Gula</label>

          <input type="text" class="form-control" id="nama_prolanis_gula" name="nama_prolanis_gula"
            placeholder="Masukkan nama prolanis gula.." value="{{ old('nama_prolanis_gula') }}">
        </div>

        <div class="form-group">
          <label for="tgl_prolanis_gula">Tanggal Prolanis Gula</label>

          <input type="date" class="form-control" id="tgl_prolanis_gula" name="tgl_prolanis_gula"
            placeholder="Masukkan tangal prolanis gula.." value="{{ old('tgl_prolanis_gula') }}">
        </div>

        <div class="form-group">
          <label for="kuota_prolanis_gula">Kuota Prolanis Gula</label>

          <input type="number" class="form-control" id="kuota_prolanis_gula" name="kuota_prolanis_gula"
            placeholder="Masukkan kuota prolanis gula.." value="{{ old('kuota_prolanis_gula') }}">
        </div>

        <br>

        <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
        <button type="button" onclick="document.location='{{ url('/elits-permohonan-uji-klinik-2/prolanis-gula') }}'"
          class="btn btn-light">Kembali</button>
      </form>
    </div>
  </div>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

  <script>
    function goBack() {
      window.history.back();
    }

    $(function() {
      var CSRF_TOKEN = "{{ csrf_token() }}";

      $(document).ready(function() {
          $('.select2-multiple').select2({
              placeholder: "Pilih Paket..",
              allowClear: true,
              theme: "bootstrap4",
          });
      });

      $('.btn-simpan').on('click', function() {
        $('#form').ajaxForm({
          success: function(response) {
            if (response.status == true) {
              swal({
                  title: "Success!",
                  text: response.pesan,
                  icon: "success"
                })
                .then(function() {
                  document.location = '/elits-permohonan-uji-klinik-2/prolanis-gula';
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

    $(document).ready(function() {
        // Mendapatkan tanggal saat ini
        var today = new Date();

        // Format tanggal menjadi YYYY-MM-DD
        var day = ('0' + today.getDate()).slice(-2);
        var month = ('0' + (today.getMonth() + 1)).slice(-2); // Bulan dimulai dari 0
        var year = today.getFullYear();

        // Gabungkan dalam format yang sesuai untuk input[type="date"]
        var formattedDate = year + '-' + month + '-' + day;

        // Set nilai input ke tanggal saat ini
        $('#tgl_prolanis_gula').val(formattedDate);
    });
  </script>
@endsection
