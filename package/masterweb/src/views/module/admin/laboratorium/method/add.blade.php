@extends('masterweb::template.admin.layout')
@section('title')
  Method Management
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
                <li class="breadcrumb-item"><a href="{{ url('/elits-methods') }}">Method Management</a></li>
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
      <form enctype="multipart/form-data" class="forms-sample" action="{{ route('elits-methods.store') }}" method="POST">
        @csrf
        <div class="form-group">
          <label for="params_method">Nama Parameter</label>
          <input type="text" class="form-control" id="params_method" name="params_method" placeholder="Parameter"
            required>
        </div>

        {{-- <div class="form-group">
                <label for="name_report_method">Nama Parameter di Laporan</label>
                <input type="text" class="form-control" id="name_report_method" name="name_report_method"  placeholder="Masukkan Nama Parameter di Laporan (jika berbeda)">
            </div> --}}

        <div class="form-group">
          <label for="name_method">Metode</label>
          <input type="text" class="form-control" id="name_method" name="name_method" placeholder="Metode" required>
        </div>

        <!-- <div class="form-group">
                                        <label for="name_method">Satuan</label>
                                        <select id="unitAttributes" name="unit" class="js-example-basic-multiple"  >

                                            @foreach ($units as $unit)
  <option value="{{ $unit->id_unit }}">{{ $unit->shortname_unit }}</option>
  @endforeach
                                            <option value="-" selected>-</option>
                                        </select>
                                    </div> -->

        <!-- <div class="form-group">
                                        <label for="kadar_diperbolehkan_method">Kadar yang diperbolehkan</label>
                                        <input type="text" class="form-control" id="kadar_diperbolehkan_method" name="kadar_diperbolehkan_method" placeholder="Kadar yang diperbolehkan" required >
                                    </div>  -->



        <div class="form-group">
          <label for="code_sampletype">Laboratorium</label>
          <select id="laboratoriumAttributes" name="laboratoriumAttributes[]"
            class="js-example-basic-multiple form-control" style="width: 100%" multiple="multiple" required>

            @foreach ($all_laboratorium as $laboratorium)
              <option value="{{ $laboratorium->id_laboratorium }}">{{ $laboratorium->nama_laboratorium }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label for="id_pdam_method">Apakah merupakan bagian PDAM?</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="1" name="id_pdam_method" id="id_pdam_method">
            <label class="form-check-label" for="flexRadioDefault1">
              Ya
            </label>
          </div>

          <div class="form-check">
            <input class="form-check-input" type="radio" value="0" name="id_pdam_method" id="id_pdam_method"
              checked>
            <label class="form-check-label" for="id_pdam_method">
              Tidak
            </label>
          </div>
        </div>


        <div class="form-group">
          <label for="name_method">Berhubungan dengan Kesehatan (Jika Kimia, Jika Tidak Abaikan)</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="1" name="berhubungan_kesehatan"
              id="berhubungan_kesehatan">
            <label class="form-check-label" for="flexRadioDefault1">
              Berhubungan dengan Kesehatan
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="0" name="berhubungan_kesehatan"
              id="berhubungan_kesehatan" checked>
            <label class="form-check-label" for="berhubungan_kesehatan">
              Tidak Berhubungan dengan Kesehatan
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="" name="berhubungan_kesehatan"
              id="berhubungan_kesehatan" checked>
            <label class="form-check-label" for="berhubungan_kesehatan">
              Mikrobiologi
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="name_method">Jenis Parameter</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="kimia organik" name="jenis_parameter_kimia"
              id="jenis_parameter_kimia">
            <label class="form-check-label" for="jenis_parameter_kimia">
              Kimia an organik
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="kimiawi" name="jenis_parameter_kimia"
              id="jenis_parameter_kimia" checked>
            <label class="form-check-label" for="jenis_parameter_kimia">
              Kimiawi
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="fisika" name="jenis_parameter_kimia"
              id="jenis_parameter_kimia" checked>
            <label class="form-check-label" for="jenis_parameter_kimia">
              Parameter Fisik
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="" name="jenis_parameter_kimia"
              id="jenis_parameter_kimia" checked>
            <label class="form-check-label" for="jenis_parameter_kimia">
              Mikrobiologi
            </label>
          </div>
        </div>


        <div class="form-group">
          <label>Alat dan Reagen</label>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="1" name="is_ready" id="is_ready_1" checked>
            <label class="form-check-label" for="is_ready_1">
              Tersedia
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" value="0" name="is_ready" id="is_ready_0">
            <label class="form-check-label" for="is_ready_0">
              Belum Tersedia
            </label>
          </div>
        </div>

        <div class="form-group">
          <label for="cost_samples">Harga Bahan</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                Rp.
              </span>
            </div>
            <input type="number" class="form-control" id="price_bahan" name="price_bahan" value="0"
              type="number" placeholder="Isikan Harga" required>
          </div>
        </div>

        <div class="form-group">
          <label for="cost_samples">Harga Sarana</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                Rp.
              </span>
            </div>
            <input type="number" class="form-control" id="price_sarana" name="price_sarana" value="0"
              type="number" placeholder="Isikan Harga" required>
          </div>
        </div>

        <div class="form-group">
          <label for="cost_samples">Harga Jasa</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                Rp.
              </span>
            </div>
            <input type="number" class="form-control" id="price_jasa" name="price_jasa" value="0"
              type="number" placeholder="Isikan Harga" required>
          </div>
        </div>

        <div class="form-group">
          <label for="cost_samples">Harga Total</label>
          <div class="input-group">
            <div class="input-group-append">
              <span class="input-group-text">
                Rp.
              </span>
            </div>
            <input type="number" class="form-control" id="price_total_method" readonly name="price_total_method"
              value="0" type="number" placeholder="Isikan Harga" required>
          </div>
        </div>


        {{-- <div class="form-group">
                <label for="module_method">Module</label>
                <input type="text" class="form-control" id="module_method" name="module_method" placeholder="Module" required >
            </div>

            <div class="form-group">
                <label for="model_method">Model</label>
                <input type="text" class="form-control" id="model_method" name="model_method" placeholder="Model" required >
            </div> --}}


        <button type="submit" class="btn btn-primary mr-2">Simpan</button>
        <button class="btn btn-light" onclick="goBack()">Kembali</button>
      </form>
    </div>
  </div>


  <script>
    function goBack() {
      window.history.back();
    }

    $(document).ready(function() {
      $.fn.select2.defaults.set("theme", "classic");
      $('#laboratoriumAttributes').select2({
        placeholder: "Pilih Laboratorium"
      });
      $('#unitAttributes').select2({
        placeholder: "Pilih Unit"
      });
    })

    $('#price_bahan').keyup(function() {
      // console.log($(this).val())
      pricetotal()
    })
    $('#price_jasa').keyup(function() {
      pricetotal()
    })
    $('#price_sarana').keyup(function() {
      pricetotal()
    })

    function pricetotal() {
      var total = parseInt($('#price_bahan').val()) + parseInt($('#price_jasa').val()) + parseInt($('#price_sarana')
        .val())
      $('#price_total_method').val(total)
    }
    // function myFunction() {

    //     var kode= document.getElementById("kode-user").value;
    //     var name= document.getElementById("name").value;
    //     var username= document.getElementById("username").value;
    //     var email= document.getElementById("email").value;

    //     var x = document.getElementById("level").selectedIndex;
    //     var level=document.getElementsByTagName("option")[x].value;



    //     if(level=="09405c01-092e-4eb7-a1d7-b511c74f6cda"){

    //         firebase.database().ref('users/'+kode).set({
    //             username: username,
    //             name: name,
    //             email: email,
    //             role:"user"
    //         }).then(function() {
    //             // window.location.href = "./dashboard"


    //         }).catch(function(error) {
    //             // An error happened.
    //         });


    //     }



    // }
  </script>
@endsection
