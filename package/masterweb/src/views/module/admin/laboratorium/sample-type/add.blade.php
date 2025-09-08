@extends('masterweb::template.admin.layout')
@section('title')
  Add Data Jenis Sarana
@endsection

@section('content')
  <div class="row">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="">
          <div class="template-demo">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item">
                  <a href="{{ url('/home') }}"><i class="fa fa-home menu-icon mr-1"></i>
                    Beranda</a>
                </li>

                <li class="breadcrumb-item">
                  <a href="{{ url('/elits-sampletypes') }}">Data Jenis Sarana</a>
                </li>

                <li class="breadcrumb-item active" aria-current="page">
                  <span>create</span>
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
      <form enctype="multipart/form-data" class="forms-sample" id="form"
        action="{{ route('elits-sampletypes.store') }}" method="POST">

        @csrf

        <div class="form-group">
          <label for="name_sampletype">Nama Jenis Sarana<span style="color: red">*</span></label>
          <input type="text" class="form-control" id="name_sampletype" name="name_sampletype"
            placeholder="Name Sample Type">
        </div>

        <div class="form-group">
          <label for="code_sampletype">Parameter Wajib<span style="color: red">*</span></label>
          <select id="methodAttributes" name="methodAttributes[]" class="form-control" style="display:none; width: 100%"
            multiple="multiple">
          </select>
        </div>



        <div class="form-group">
          <label for="code_sampletype">Parameter Tambahan</label>
          <select id="methodPlusAttributes" name="methodPlusAttributes[]" class="form-control"
            style="display:none; width: 100%" multiple="multiple"></select>
        </div>
      </form>
      <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
      <button type="button" onclick="goBack()" class="btn btn-light">Kembali</button>
    </div>
  </div>


  <script>
    function goBack() {
      window.history.back();
    }

    $(document).ready(function() {

      $.fn.select2.defaults.set("theme", "classic");

      $('#methodAttributes').select2({
        placeholder: "Pilih Metode",
        allowClear: true,
        ajax: {
          url: "{{ url('/api/method/') }}",
          method: "post",
          dataType: 'json',

          params: { // extra parameters that will be passed to ajax
            contentType: "application/json;",
          },
          data: function(term) {
            return {
              term: term.term || '',
              page: term.page || 1
            };
          },
          cache: true
        }
      });

      $('#methodPlusAttributes').select2({
        placeholder: "Pilih Metode",
        allowClear: true,
        ajax: {
          url: "{{ url('/api/method/') }}",
          method: "post",
          dataType: 'json',

          params: { // extra parameters that will be passed to ajax
            contentType: "application/json;",
          },
          data: function(term) {
            return {
              term: term.term || '',
              page: term.page || 1
            };
          },
          cache: true
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
                  document.location = '/elits-sampletypes';
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
