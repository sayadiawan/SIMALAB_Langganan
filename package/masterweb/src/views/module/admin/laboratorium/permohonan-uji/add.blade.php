@extends('masterweb::template.admin.layout')
@section('title')
    Permohonan Uji Management
@endsection


@section('content')
    <script>
        console.log("dsaasdadsadsads");
    </script>
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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji') }}">Permohonan Uji
                                        Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>create</span></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>Tambah Permohonan Uji</h4>

        </div>

        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <form action="{{ route('elits-permohonan-uji.store') }}" id="form" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="code_permohonan_uji"> No. Permohonan Uji:</label>
                        <div class="input-group date">
                            <input type="text" class="form-control" readonly name="code_permohonan_uji"
                                id="code_permohonan_uji" placeholder="Kode Permohonan Uji" value="{{ $code }}">

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="customer">
                            <label for="customer_samples">Nama pelanggan/perusahaan:</label>
                            <select id="customerAttributes" name="customer"
                                class="js-customer-basic-multiple js-states form-control" style="width: 100%">
                            </select>
                            <button type="button" class="not_found btn btn-link">Klik Disini Jika Pelanggan Baru</button>
                        </div>

                        <div class="card new_customer" style="display: none">

                            <div class="card-body">
                                <button type="button" class="close cancel_customer_new" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <div class="form-group ">
                                    <label for="code_permohonan_uji"> Masukkan Nama Pelanggan Baru:</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control" name="new_customer" id="new_customer"
                                            placeholder="Masukkan Nama Pelanggan Baru" value="">
                                    </div>
                                </div>

                                <div class="form-group">

                                    <label for="address_customer">Alamat</label>
                                    <textarea class="form-control" id="new_address_customer" name="new_address_customer" placeholder="Isikan Alamat"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <select name="new_kecamatan" class="form-control" id="new_kecamatan"
                                        onchange="CheckKecamatan(this)" style="width: 100%">
                                        <option value="" selected disabled>Pilih Kecamatan</option>
                                        <option value="BOYOLALI">BOYOLALI</option>
                                        <option value="Selo">Selo</option>
                                        <option value="Ampel">Ampel</option>
                                        <option value="Cepogo">Cepogo</option>
                                        <option value="Musuk">Musuk</option>
                                        <option value="Tamansari">Tamansari</option>
                                        <option value="Mojosongo">Mojosongo</option>
                                        <option value="Teras">Teras</option>
                                        <option value="Sawit">Sawit</option>
                                        <option value="Banyudono">Banyudono</option>
                                        <option value="Ngemplak">Ngemplak</option>
                                        <option value="Simo">Simo</option>
                                        <option value="Karanggede">Karanggede</option>
                                        <option value="Klego">Klego</option>
                                        <option value="Andong">Andong</option>
                                        <option value="Kemusu">Kemusu</option>
                                        <option value="Wonosegoro">Wonosegoro</option>
                                        <option value="Wonosamudro">Wonosamudro</option>
                                        <option value="Juwangi">Juwangi</option>
                                        <option value="Kabupaten Boyolali">Kabupaten Boyolali</option>
                                        <option value="Andong">Andong</option>
                                        <option value="Ampel">Ampel</option>
                                        <option value="Banyudono">Banyudono</option>
                                        <option value="Boyolali">Boyolali</option>
                                        <option value="Cepogo">Cepogo</option>
                                        <option value="Gladagsari">Gladagsari</option>
                                        <option value="Juwangi">Juwangi</option>
                                        <option value="Karanggede">Karanggede</option>
                                        <option value="Kemusu">Kemusu</option>
                                        <option value="Klego">Klego</option>
                                        <option value="Mojosongo">Mojosongo</option>
                                        <option value="Musuk">Musuk</option>
                                        <option value="Ngemplak">Ngemplak</option>
                                        <option value="Nogosari">Nogosari</option>
                                        <option value="Sambi">Sambi</option>
                                        <option value="Sawit">Sawit</option>
                                        <option value="Selo">Selo</option>
                                        <option value="Simo">Simo</option>
                                        <option value="Tamansari">Tamansari</option>
                                        <option value="Teras">Teras</option>
                                        <option value="Wonosegoro">Wonosegoro</option>
                                        <option value="Wonosamodro">Wonosamodro</option>
                                        <option value="0">Lainnya</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control mt-10" id="new_kecamatan_other"
                                        style='display:none;' id="new_kecamatan_other" name="new_kecamatan_other"
                                        placeholder="Isikan Kecamatan Lain">
                                </div>

                                <div class="form-group">
                                    <label for="new_email_customer">Email</label>
                                    <input type="text" class="form-control" id="new_email_customer"
                                        name="new_email_customer" placeholder="Isikan Email">
                                </div>



                                <div class="form-group">
                                    <label for="new_cp_customer">Contact Person</label>
                                    <textarea class="form-control" id="new_cp_customer" name="new_cp_customer" placeholder="Isikan Contact Person"></textarea>
                                </div>

                            </div>
                        </div>

                        <div class="card old_customer" style="display: none">

                            <div class="card-body">

                                <div class="form-group ">
                                    <label for="name_customer"> Nama Pelanggan :</label>
                                    <div class="input-group date">
                                        <span id="name_customer">
                                            <h5></h5>
                                        </span>

                                        {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label for="address_customer"> Alamat Pelanggan :</label>
                                    <div class="input-group date">
                                        <span id="address_customer">
                                            <h5></h5>
                                        </span>

                                        {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="kecamatan_customer"> Kecamatan Pelanggan :</label>
                                    <div class="input-group date">
                                        <span id="kecamatan_customer">
                                            <h5></h5>
                                        </span>

                                        {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <label for="email_customer"> Email Pelanggan :</label>
                                    <div class="input-group date">
                                        <span id="email_customer">
                                            <h5></h5>
                                        </span>

                                        {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                    </div>
                                </div>

                                <!-- <div class="form-group ">
                                                                                        <label for="category_customer"> Kategori Pelanggan :</label>
                                                                                        <div class="input-group date">
                                                                                            <span id="category_customer">
                                                                                                <h5></h5>
                                                                                            </span>

                                                                                            {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                                                                        </div>
                                                                                    </div> -->

                                <div class="form-group ">
                                    <label for="cp_customer"> Contact Person Pelanggan :</label>
                                    <div class="input-group date">
                                        <span id="cp_customer">
                                            <h5></h5>
                                        </span>

                                        {{-- <input type="text" class="form-control" name="new_customer" id="new_customer"
                    placeholder="Masukkan Nama Pelanggan Baru" value=""> --}}
                                    </div>
                                </div>
{{--                                <div class="form-group">--}}
{{--                                  <label for="address_customer">Alamat Hasil :</label>--}}
{{--                                  <textarea class="form-control" id="alamat_customer_edited" name="alamat_customer_edited" placeholder="Isikan Alamat"></textarea>--}}
{{--                                </div>--}}


                                {{-- <div class="form-group">

                <label for="address_customer">Alamat</label>
                <textarea class="form-control" id="new_address_customer" name="new_address_customer"
                  placeholder="Isikan Alamat"></textarea>
              </div>

              <div class="form-group">
                <label for="kecamatan">Kecamatan</label>
                <select name="new_kecamatan" class="form-control" id="new_kecamatan" onchange="CheckKecamatan(this)">
                  <option value="" selected disabled>Pilih Kecamatan</option>
                  <option value="Colomadu">Colomadu</option>
                  <option value="Gondangrejo">Gondangrejo</option>
                  <option value="Jaten">Jaten</option>
                  <option value="Jatipuro">Jatipuro</option>
                  <option value="Jatiyoso">Jatiyoso</option>
                  <option value="Jenawi">Jenawi</option>
                  <option value="Jumapolo">Jumapolo</option>
                  <option value="Jumantono">Jumantono</option>
                  <option value="BOYOLALI">BOYOLALI</option>
                  <option value="Karangpandan">Karangpandan</option>
                  <option value="Kebakkramat">Kebakkramat</option>
                  <option value="Kerjo">Kerjo</option>
                  <option value="Matesih">Matesih</option>
                  <option value="Ngargoyoso">Ngargoyoso</option>
                  <option value="Mojogedang">Mojogedang</option>
                  <option value="Tasikmadu">Tasikmadu</option>
                  <option value="Ngargoyoso">Ngargoyoso</option>
                  <option value="Tawangmangu">Tawangmangu</option>
                  <option value="0">Lainnya</option>
                </select>
              </div>

              <div class="form-group">
                <input type="text" class="form-control mt-10" id="new_kecamatan_other" style='display:none;'
                  id="new_kecamatan_other" name="new_kecamatan_other" placeholder="Isikan Kecamatan Lain">
              </div>

              <div class="form-group">
                <label for="new_email_customer">Email</label>
                <input type="text" class="form-control" id="new_email_customer" name="new_email_customer"
                  placeholder="Isikan Email">
              </div>

              <div class="form-group">
                <label for="new_cp_customer">Contact Person</label>
                <textarea class="form-control" id="new_cp_customer" name="new_cp_customer"
                  placeholder="Isikan Contact Person"></textarea>
              </div> --}}

                            </div>
                        </div>
                    </div>

                    <div class="form-group table-sample">
                        <label for="date_get_sample">Tanggal Masuk Pengajuan</label>
                        <div class="input-group date">
                            <input type="text" class="form-control date_get_sample datepicker date_get_sample"
                                name="date" id="date_get_sample" placeholder="Isikan Tanggal"
                                data-date-format="dd/mm/yyyy" required>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                                                                <label for="datelab_samples">Rencana Pengambilan Hasil</label>
                                                                <div class="input-group date">
                                                                    <input type="text" class="form-control datelab_samples " name="date_done"
                                                                        id="datelab_samples" placeholder="Isikan Rencana Pengambilan Hasil"
                                                                        data-date-format="dd/mm/yyyy" required>
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">
                                                                            <i class="fas fa-calendar-alt"></i>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div> -->

                    <div class="form-group">
                        <label for="pengirim_sample"> Nama Pengirim Sampel/ Pengambil Sampel (Muncul di Nota):</label>
                        <div class="input-group date">
                            <input type="text" class="form-control" name="pengirim_sample" id="pengirim_sample"
                                placeholder="Masukkan/ Nama Pengirim Sampel/ Pengambil Sampel (Muncul di Nota)">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name_sampling"> Petugas Sampling (Untuk Mikro):</label>
                        <div class="input-group date">
                            <input type="text" class="form-control" name="name_sampling" id="name_sampling"
                                placeholder="Masukkan Petugas Sampling">

                        </div>
                    </div>
                    {{-- <div class="form-group">
            <label for="location_permohonan_uji"> Lokasi Umum Sampel (Khusus Mikro dan Khusus MikMan
              (Kimia)):</label>
            <div class="input-group date">
              <input type="text" class="form-control" name="location_permohonan_uji" id="location_permohonan_uji"
                placeholder="Lokasi Umum Sampel (Khusus Mikro)">

            </div>
          </div> --}}
                    {{-- <div class="form-group">
            <label for="pdam_pengirim_sample"> Pengambil Sampel (Khusus Mikro, Khusus MikMan (Kimia) dan Kimia
              PUDAM):</label>
            <div class="input-group date">
              <input type="text" class="form-control" name="pdam_pengirim_sample" id="pdam_pengirim_sample"
                placeholder="Pengambil Sampel (Khusus Mikro dan Kimia PUDAM)">

            </div>
          </div> --}}

                    <div class="form-group">
                        <label for="petugas_penerima"> Petugas Penerima Sampel (Muncul di Permintaan Periksaan):</label>
                        <div class="input-group date">
                          <select class="form-control" name="petugas_penerima" id="petugas_penerima">
                            <option value="Umi Sulistiyana">Umi Sulistiyana</option>
                            <option value="Liyana">Liyana</option>
                          </select>
{{--                            <input type="text" class="form-control" value="{{ $user->name }}"--}}
{{--                                name="petugas_penerima" id="petugas_penerima"--}}
{{--                                placeholder="Masukkan Petugas Penerima Sampel (Muncul di Permintaan Periksaan)">--}}

                        </div>
                    </div>


                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Catatan</label>
                        <textarea class="form-control" name="catatan" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select class="form-control" name="metode_pembayaran">
                            <option disabled>Metode Pembayaran</option>
                            <option value="0">Cash</option>
                            <option value="1">Transfer</option>
                        </select>
                    </div>


                </form>
                <button type="submit" id="submitAll" class="btn btn-primary mr-2">Simpan</button>
                <button type="button" class="btn btn-light" onclick="window.history.back()">Kembali</button>

            </li>


        </ul>

    </div>
@endsection

@section('scripts')
    <script>
        $('#new_kecamatan').select2({
            allowclear: true,
            placeholder: 'Pilih Kecamatan'
        });

        function CheckKecamatan(val) {
            var element = document.getElementById('new_kecamatan_other');
            if (val.value == '0')
                element.style.display = 'block';
            else
                element.style.display = 'none';
        }




        $(document).ready(function() {
            $('#submitAll').click(function() {
                $('#form').ajaxSubmit({
                    success: function(response) {
                        $('#submitAll').prop("disabled", true);
                        if (response.status == true) {
                            swal({
                                    title: "Success!",
                                    text: response.pesan,
                                    icon: "success"
                                })
                                .then(function() {
                                    // console.log(link);

                                    var url = "{{ route('elits-samples.create', '#') }}"
                                    url = url.replace('#', response.id);
                                    document.location = url;

                                });
                        } else {

                            var pesan = "";
                            var data_pesan = response.pesan;
                            const wrapper = document.createElement('div');

                            if (typeof(data_pesan) == 'object') {
                                jQuery.each(data_pesan, function(key, value) {
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
                            $('#submitAll').prop("disabled", false);
                        }
                    },
                    error: function() {
                        swal("Error!", "System gagal menyimpan!", "error");
                        $('#submitAll').prop("disabled", false);
                    }
                })
            })
        })

        $('.not_found').click(function() {
            $('.new_customer').css('display', 'block');
            $('.old_customer').css('display', 'none');
            //    console.log("ssaddasdas")
            $("#customerAttributes").val('').trigger('change');
            $('.cancel_customer_new').click(function() {
                $("#customerAttributes").prop("disabled", false);
                $('.new_customer').css('display', 'none');
                $('.old_customer').css('display', 'none');
            })
            //    $("#customerAttributes").prop( "disabled", true );
        });
        $('.date_get_sample').datepicker('update', new Date());

        $("#new_customer").on('keyup', function() {
            // $("#pengirim_sample").val($("#new_customer").val());
            document.getElementById("pengirim_sample").value = $("#new_customer").val();
            console.log($("#new_customer").val());
        });

        $('.date_get_sample').datepicker({
            format: 'dd/mm/yyyy'
        }).on('change', function() {
            var jsDate = $('.date_get_sample').datepicker('getDate');

            // console.log(jsDate)


            var curr_date = jsDate.getDate();
            var curr_month = jsDate.getMonth() + 1; //Months are zero based
            var curr_year = jsDate.getFullYear();


            var date2 = new Date(curr_year + "/" + curr_month + "/" + curr_date);
            date2.setDate(date2.getDate() + 14);
            $('.datelab_samples').datepicker('update', date2);
        });





        $('.datelab_samples').datepicker({
            format: 'dd/mm/yyyy'
        })
        var date = new Date();
        date.setDate(date.getDate() + 14);
        $('.datelab_samples').datepicker('update', date);


        $.fn.select2.defaults.set("theme", "classic");

        $('.js-customer-basic-multiple').select2({
            placeholder: "Pilih Customer",
            allowClear: true,
            ajax: {
                url: "{{ url('/api/customer/') }}",
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
        }).on('change', function(e) {


            var getID = $(this).select2('data');
            if (getID[0] != "" && getID[0] != undefined) {

                // console.log(getID[0]['id']);
                var id = getID[0]['id'];


                var url = "{{ route('customer.detail', '#') }}"
                url = url.replace("#", id);
                // console.log(id)
                $.ajax({
                    url: url,
                    type: "GET",

                    success: function(response) {

                        $("#name_customer h5").text(response.name_customer);
                        $("#address_customer h5").text(response.address_customer);
                        $("#kecamatan_customer h5").text(response.kecamatan_customer);
                        $("#email_customer h5").text(response.email_customer);
                        $("#category_customer h5").text(response.name_industry);
                        $("#cp_customer h5").text(response.cp_customer);
                        // $("#alamat_customer_edited").text(response.address_customer);
                        $('.old_customer').css('display', 'block');
                        $('.new_customer').css('display', 'none');

                        document.getElementById("pengirim_sample").value = response.name_customer;
                    }
                })
            } else {
                $('.old_customer').css('display', 'none');
            }
        });
    </script>
@endsection
