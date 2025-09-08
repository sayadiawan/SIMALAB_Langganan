@extends('masterweb::template.admin.layout')
@section('title')
    Data Paket
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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-packet') }}">Data Paket</a></li>
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
            <!-- <form enctype="multipart/form-data" class="forms-sample" action="{{ route('elits-packet.store') }}"  method="POST"> -->
            @csrf
            <div class="form-group">
                <label for="name_packet">Nama Paket</label>
                <input type="text" class="form-control" id="name_packet" name="name_packet" placeholder="Name Paket"
                    required>
            </div>

            <div class="form-group">
                <label for="code_sampletype">Parameter</label>
                <select id="methodAttributes" name="methodAttributes" class="js-example-basic-multiple form-control"
                    style="display:none; width: 100%" multiple="multiple" required>
                </select>
            </div>

            <div class="form-group">
                <label for="sample_type_id">Jenis Sample</label>
                <select id="sample_type_id" name="sample_type_id" class="js-customer-basic-multiple js-states form-control"
                    style="display:none; width: 100%" required>
                    <option value="" selected disabled>Pilih Jenis Sampel</option>
                    @foreach ($sampletypes as $sampletype)
                        <option value="{{ $sampletype->id_sample_type }}">{{ $sampletype->name_sample_type }}</option>
                    @endforeach
                </select>
            </div>
            {{-- <div class="form-group jenis_makanan" style="display: none;">
        <label for="jenis_makanan_id">Jenis Makanan</label>
        <select id="jenis_makanan_id" name="jenis_makanan_id" class="js-customer-basic-multiple js-states form-control"
          style="display:none" required>
          <option value="" selected disabled>Pilih Jenis Makanan</option>
          @foreach ($all_jenis_makanan as $jenis_makanan)
            <option value="{{ $jenis_makanan->id_jenis_makanan }}">{{ $jenis_makanan->name_jenis_makanan }}</option>
          @endforeach
        </select>
      </div> --}}


            <div class="form-group">
                <label for="cost_samples">Harga Bahan</label>
                <div class="input-group">
                    <div class="input-group-append">
                        <span class="input-group-text">
                            Rp.
                        </span>
                    </div>
                    <input type="number" class="form-control" id="price_bahan_packet" name="price_bahan_packet"
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
                    <input type="number" class="form-control" id="price_sarana_packet" name="price_sarana_packet"
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
                    <input type="number" class="form-control" id="price_jasa_packet" name="price_jasa_packet"
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
                    <input type="number" class="form-control" id="price_total_packet" readonly name="price_total_packet"
                        type="number" placeholder="Isikan Harga" required>
                </div>
            </div>



            <br>


            <!-- <div class="form-group">
                              <label for="favicon_sampletype">Favicon Sample Type</label>
                              <input type="text" class="form-control" id="favicon_sampletype" name="favicon_sampletype" placeholder="Code Sample Type" required >
                          </div> -->


            <button type="submit" class="btn btn-primary mr-2" id="submit">Simpan</button>
            <button onclick="goBack()" class="btn btn-light">Kembali</button>
            <!-- </form> -->
        </div>
    </div>


    <script>
        function goBack() {
            window.history.back();
        }



        $("#submit").click(function() {
            var methodAttributes = $("#methodAttributes").val()
            var name_packet = $("#name_packet").val()
            var sample_type_id = $("#sample_type_id").val()
            var price_bahan_packet = $("#price_bahan_packet").val()
            var price_jasa_packet = $("#price_jasa_packet").val()
            var price_total_packet = $("#price_total_packet").val()
            var price_sarana_packet = $("#price_sarana_packet").val()

            var jenis_makanan_id = $("#jenis_makanan_id").val()


            let _token = "{{ csrf_token() }}"



            $.ajax({
                url: "{{ route('elits-packet.store') }}",
                type: "POST",
                data: {
                    name_packet: name_packet,
                    methodAttributes: methodAttributes,
                    sample_type_id: sample_type_id,
                    jenis_makanan_id: jenis_makanan_id,
                    price_bahan_packet: price_bahan_packet,
                    price_jasa_packet: price_jasa_packet,
                    price_sarana_packet: price_jasa_packet,
                    price_total_packet: price_sarana_packet,
                    _token: _token
                },
                success: function(response) {

                    var url = "{{ route('elits-packet.index') }}";
                    window.location.href = url;

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(XMLHttpRequest.responseJSON.message);
                }
            });


        });


        $(document).ready(function() {

            $.fn.select2.defaults.set("theme", "classic");
            // $('#jenis_makanan_id').select2({
            //   placeholder: "Pilih Jenis Makanan",
            //   allowClear: true
            // });
            $('#sample_type_id').select2({
                    placeholder: "Pilih Jenis Sampel",
                    allowClear: true
                })
                .on('change', function(e) {
                    var getID = $(this).select2('data');

                    // // console.log(getID[0].text); // That's the selected ID :)
                    // if (getID[0].text.includes("Makanan")) {
                    //   $(".jenis_makanan").css("display", "block")
                    // } else {
                    //   $(".jenis_makanan").css("display", "none")
                    // }
                });
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
        });

        $('#price_bahan_packet').keyup(function() {
            // console.log($(this).val())
            pricetotal()
        })
        $('#price_jasa_packet').keyup(function() {
            pricetotal()
        })
        $('#price_sarana_packet').keyup(function() {
            pricetotal()
        })

        function pricetotal() {
            var total = parseInt($('#price_bahan_packet').val()) + parseInt($('#price_jasa_packet').val()) + parseInt($(
                '#price_sarana_packet').val())
            $('#price_total_packet').val(total)
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
