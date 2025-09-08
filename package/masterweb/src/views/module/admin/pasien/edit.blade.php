@extends('masterweb::template.admin.layout')
@section('title')
  Pasien Management - Edit
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
                <li class="breadcrumb-item"><a href="{{ url('/elits-pasien') }}">Pasien Management</a></li>
                <li class="breadcrumb-item active" aria-current="page"><span>edit</span></li>
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
        action="{{ route('elits-pasien.update', $item->id_pasien) }}" method="POST">

        @csrf
        @method('PUT')

        <input type="hidden" name="permohonanujiklinik_id" id="permohonanujiklinik_id"
          value="{{ Request::query('permohonanujiklinik') }}" readonly>

        <div class="form-group">
          <label for="parameter_satuan_klinik">NIK Pasien<span style="color: red">*</span></label>

          <input type="text" class="form-control" id="nik_pasien" name="nik_pasien" placeholder="NIK pasien.."
            value="{{ $item->nik_pasien ?? old('nik_pasien') }}">
        </div>

        <div class="form-group">
          <label for="name_parameter_paket_klinik">Nama Pasien<span style="color: red">*</span></label>

          <input type="text" class="form-control" id="nama_pasien" name="nama_pasien" placeholder="Nama pasien.."
            value="{{ $item->nama_pasien ?? old('nama_pasien') }}">
        </div>

        <div class="form-group">
          <label for="no_rekammedis_pasien">Nomor Rekam Medis</label>

          <input type="text" class="form-control no_rekammedis_pasien" name="no_rekammedis_pasien"
            id="no_rekammedis_pasien" placeholder="Nomor rekam medis"
            value="{{ $item->no_rekammedis_pasien ?? old('no_rekammedis_pasien') }}">
        </div>

        <div class="form-group">
          <label for="divisi_instansi_pasien">Divisi/Instansi</label>

          <input type="text" class="form-control divisi_instansi_pasien" name="divisi_instansi_pasien"
            id="divisi_instansi_pasien" placeholder="Divisi/instansi"
            value="{{ $item->divisi_instansi_pasien ?? old('divisi_instansi_pasien') }}">
        </div>

        <div class="form-group">
          <label for="name_parameter_paket_klinik">Jenis Kelamin Pasien</label>

          <div class="form-check">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="gender_pasien" id="gender_pasien1" value="L"
                {{ $item->gender_pasien == 'L' ? 'checked' : '' }}>
              Laki-laki
              <i class="input-helper"></i></label>
          </div>

          <div class="form-check">
            <label class="form-check-label">
              <input type="radio" class="form-check-input" name="gender_pasien" id="gender_pasien2" value="P"
                {{ $item->gender_pasien == 'P' ? 'checked' : '' }}>
              Perempuan
              <i class="input-helper"></i></label>
          </div>
        </div>

        <div class="form-group">
          <label for="datelab_samples">Tanggal Lahir Pasien<span style="color: red">*</span></label>
          <div class="input-group ">
            <input type="text" class="form-control date_birth" name="tgllahir_pasien" id="tgllahir_pasien"
              placeholder="dd/mm/yyyy" readonly
              value="{{ $item->tgllahir_pasien != null ? \Carbon\Carbon::createFromFormat('Y-m-d', $item->tgllahir_pasien)->format('d/m/Y') : '' }}">
          </div>
        </div>

        <div class="form-group">
          <label for="harga_parameter_paket_klinik">Nomor Telepon<span style="color: red">*</span></label>

          <input type="text" class="form-control" id="phone_pasien" name="phone_pasien"
            placeholder="Nomor telepon pasien.." value="{{ $item->phone_pasien ?? old('phone_pasien') }}">
        </div>

        <div class="form-group">
          <label for="name_parameter_paket_klinik">Alamat Pasien</label>

          <textarea class="form-control" name="alamat_pasien" id="alamat_pasien" cols="30" placeholder="Alamat pasien.."
            rows="10">{{ $item->alamat_pasien ?? old('alamat_pasien') }}</textarea>
        </div>

        <br>

      </form>
      <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
      {{-- <button type="button" onclick="document.location='{{ url('/elits-pasien') }}'"
        class="btn btn-light">Kembali</button> --}}
      <button type="button" onclick="history.back();" class="btn btn-light">Kembali</button>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
    integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous"></script>

  <script>
    function goBack() {
      window.history.back();
    }

    $(document).ready(function() {
      $(".date_birth").datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
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
                  document.location = response.url_back;
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
    });
  </script>
@endsection
