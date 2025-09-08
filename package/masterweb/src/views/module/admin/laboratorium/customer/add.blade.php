@extends('masterweb::template.admin.layout')
@section('title')
Customer Management
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
                            <li class="breadcrumb-item"><a href="{{ url('/elits-customers') }}">Customer Management</a>
                            </li>
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
        <form enctype="multipart/form-data" class="forms-sample" action="{{ route('elits-customers.store') }}" method="POST">
            @csrf
            <div class="form-group">

                <label for="name_customer">Nama</label>
                <input type="text" class="form-control" id="name_customer" name="name_customer" placeholder="Isikan Nama" required>
            </div>

            <div class="form-group">

                <label for="address_customer">Alamat</label>
                <textarea class="form-control" id="address_customer" name="address_customer" placeholder="Isikan Alamat" required></textarea>
            </div>

                <div class="form-group">
                    <label for="kecamatan">Kecamatan</label>
                    <select name="kecamatan" class="form-control" id="kecamatan" onchange="CheckKecamatan(this)"
                        style="width: 100%">
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
                        <option value="0">Lainnya</option>
                    </select>
                </div>

            <div class="form-group">
                <input type="text" class="form-control mt-10" id="kecamatan_other" style='display:none;' id="kecamatan_other" name="kecamatan_other" placeholder="Isikan Kecamatan Lain">
            </div>

            <div class="form-group">
                <label for="email_customer">Email</label>
                <input type="text" class="form-control" id="email_customer" name="email_customer" placeholder="Isikan Email">
            </div>

            <div class="form-group">
                <label for="cp_customer">Contact Person</label>
                <textarea class="form-control" id="cp_customer" name="cp_customer" placeholder="Isikan Contact Person"></textarea>
            </div>


            <button type="submit" class="btn btn-primary mr-2">Simpan</button>
            <button onclick="goBack()" class="btn btn-light">Kembali</button>
        </form>
    </div>
</div>





<script>
    $('#kecamatan').select2({
        allowclear: true,
        placeholder: 'Pilih Kecamatan'
    });

    function CheckKecamatan(val) {
        var element = document.getElementById('kecamatan_other');


        if (val.value == '0')
            element.style.display = 'block';
        else

            element.style.display = 'none';
    }

    function goBack() {
        window.history.back();
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
