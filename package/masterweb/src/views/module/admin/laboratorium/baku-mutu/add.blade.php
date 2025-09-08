@extends('masterweb::template.admin.layout')

@section('title')
    Baku Mutu Manager
@endsection

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="">
                    <div class="template-demo">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb breadcrumb-custom bg-inverse-primary">
                                <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i
                                            class="fa fa-home menu-icon mr-1"></i>
                                        Beranda</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/elits-baku-mutu-' . $lab_link) }}"> Baku Mutu
                                        Lab.{{ $lab }}</a></li>
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
            <form enctype="multipart/form-data" class="forms-sample"
                action="{{ route('elits-baku-mutu-' . $lab_link . '.store') }}" method="POST" id="form">
                @csrf

                <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />

                <div class="form-group">
                    <label for="name_customer">Jenis Sampel</label>

                    <select name="sampletype_id" class="form-control" id="sampletype_id">
                        <option value="" selected disabled>Pilih Jenis Sampel</option>
                        @foreach ($sample_types as $sample_type)
                            <option value="{{ $sample_type->id_sample_type }}">{{ $sample_type->name_sample_type }}</option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group">
                    <label for="name_customer">Parameter</label>

                    <select name="method_id" class="form-control" id="method_id" style="width: 100%">
                        <option value="" selected disabled>Pilih Parameter</option>
                        @foreach ($methods as $method)
                            <option value="{{ $method->id_method }}">{{ $method->params_method }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="name_report">Nama Parameter di Laporan</label>
                    <input type="text" class="form-control" id="name_report" name="name_report"
                        placeholder="Nama Parameter di Laporan">
                </div>



                {{-- <div class="form-group jenis_makanan" style="display: none; ">
                    <label for="jenis_makanan_id">Jenis Makanan</label>

                    <select id="jenis_makanan_id" name="jenis_makanan_id"
                        class="js-customer-basic-multiple js-states form-control" style="display:none; width: 100%">
                        <option value="" selected disabled>Pilih Jenis Makanan</option>
                        @foreach ($all_jenis_makanan as $jenis_makanan)
                            <option value="{{ $jenis_makanan->id_jenis_makanan }}">{{ $jenis_makanan->name_jenis_makanan }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}



                <div class="form-group">
                    <label for="name_customer">Acuan Baku Mutu</label>
                    <select name="library_id" class="form-control" id="library_id" style="width: 100%">
                        <option value="" selected disabled>Pilih Acuan Baku Mutu</option>
                        @foreach ($libraries as $library)
                            <option value="{{ $library->id_library }}">{{ $library->title_library }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="name_method">Satuan</label>
                    <select id="unitAttributes" name="unit_id" class="js-example-basic-multiple">
                        <option value="" selected disabled>Pilih Satuan</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id_unit }}">{!! $unit->shortname_unit !!}</option>
                        @endforeach
                        <option value="-" selected>-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="name_method">Punya Sub Baku Mutu :</label>
                    <input type="radio" id="html" name="is_sub" value="true"> Ya<br>
                    <input type="radio" id="css" name="is_sub" value="false" checked> Tidak<br>
                </div>

                <div class="no_sub" style="display: none;">



                    <div class="form-group">
                        <label for="max">Kadar Max Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapat koma,
                                maka
                                menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                        <input type="text" class="form-control" id="max" name="max_no_sub"
                            placeholder="Kadar Max Baku Mutu">
                    </div>

                    <div class="form-group">
                        <label for="min">Kadar Min Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapat koma,
                                maka
                                menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                        <input type="text" class="form-control" id="min" name="min_no_sub"
                            placeholder="Kadar Min Baku Mutu">
                    </div>

                    <div class="form-group">
                        <label for="equal">Nilai Harus Sama Dengan <br><b>(Apabila nilai baku mutu bukan berupa range
                                minimal maksimal misal (Negatif atau Positif) maka isi disini, apabila tidak maka
                                kosongi)</b></label>
                        <input type="text" class="form-control" id="equal" name="equal_no_sub"
                            placeholder="Nilai Harus Sama Dengan">
                    </div>

                    <div class="form-group">
                        <label for="nilai_baku_mutu">Nilai Baku Mutu di Laporan</label>
                        <textarea class="form-control" id="nilai_baku_mutu" name="nilai_baku_mutu_no_sub" placeholder="Nilai Baku Mutu"></textarea>
                    </div>
                </div>

                <div class="sub" style="display: none;">
                    <div class="form-group sub_baku_mutu" id="sub_baku_mutu_1">
                        <div class="card">
                            <div class="card-body">
                                <button type="button" class="close" onclick="minus(1)" id="minus_1"
                                    data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h5 class="card-title">
                                    <center>Sub Baku Mutu 1</center>
                                </h5>
                                <div class="form-group">
                                    <label for="min">Nama Sub Baku</label>
                                    <input type="text" class="form-control" id="name_subbakumutu"
                                        name="name_subbakumutu[0]" placeholder="Nama Sub Baku Mutu">
                                </div>
                                <div class="form-group">
                                    <label for="min">Kadar Min Baku Mutu <br><b>(Masukkan berupa angka dan apabila
                                            terdapan
                                            koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                                    <input type="text" class="form-control" id="min" name="min[0]"
                                        placeholder="Kadar Min Baku Mutu">
                                </div>

                                <div class="form-group">
                                    <label for="max">Kadar Max Baku Mutu <br><b>(Masukkan berupa angka dan apabila
                                            terdapat
                                            koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                                    <input type="text" class="form-control" id="max" name="max[0]"
                                        placeholder="Kadar Max Baku Mutu">
                                </div>

                                <div class="form-group">
                                    <label for="equal">Nilai Harus Sama Dengan <br><b>(Apabila nilai baku mutu bukan
                                            berupa
                                            range minimal maksimal misal (Negatif atau Positif) maka isi disini, apabila
                                            tidak maka kosongi)</b></label>
                                    <input type="text" class="form-control" id="equal" name="equal[0]"
                                        placeholder="Nilai Harus Sama Dengan">
                                </div>

                                <div class="form-group">
                                    <label for="nilai_baku_mutu">Nilai Baku Mutu di Laporan</label>
                                    <input type="text" class="form-control" id="nilai_baku_mutu"
                                        name="nilai_baku_mutu[0]" placeholder="Nilai Baku Mutu">
                                </div>
                                <button type="button" id="tambah" class="tambah btn btn-primary btn-lg btn-block"><i
                                        class="fas fa-plus"></i> Sub Baku Mutu</button>

                            </div>

                        </div>
                    </div>

                </div>

            </form>
            <button type="submit" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
            <button onclick="goBack()" class="btn btn-light" type="button">Kembali</button>
        </div>
    </div>
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $.fn.select2.defaults.set("theme", "classic");

            // $('#jenis_makanan_id').select2({
            //   placeholder: "Pilih Jenis Makanan",
            //   allowClear: true
            // });

            $('#sampletype_id').select2({
                    placeholder: "Pilih Jenis Sampel",
                    allowClear: true
                })
                .on('change', function(e) {
                    var getID = $(this).select2('data');

                    // console.log(getID[0].text); // That's the selected ID :)
                    // if (getID[0].text.includes("Makanan")) {
                    //   $(".jenis_makanan").css("display", "block")
                    // } else {
                    //   $(".jenis_makanan").css("display", "none")
                    // }
                });

            $('#method_id').select2({
                placeholder: "Pilih Parameter"
            });

            $('#library_id').select2({
                placeholder: "Pilih Acuan Baku Mutu"
            });

            $('#unitAttributes').select2({
                placeholder: "Pilih Unit",
                theme: 'bootstrap4'
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

                                    var link = "{{ $lab_link }}";
                                    // console.log(link);
                                    if (link == "kimia") {
                                        document.location =
                                            "{{ route('elits-baku-mutu-kimia.index') }}";
                                    } else {
                                        document.location =
                                            "{{ route('elits-baku-mutu-mikro.index') }}";
                                    }
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
                        }
                    },
                    error: function() {
                        swal("Error!", "System gagal menyimpan!", "error");
                    }
                })
            })
        })

        $('#method_id').change(function() {
            var data = $("#method_id option:selected").text();
            $('#name_report').val(data)
        });

        $('#method_id').change(function() {
            var data = $("#method_id option:selected").text();
            $('#name_report').val(data)
        });

        $(".no_sub").css("display", "block")
        $(".sub").css("display", "none")

        $(':radio[name="is_sub"]').change(function() {
            var is_sub = $(this).filter(':checked').val();

            if (is_sub == "true") {
                $(".no_sub").css("display", "none")
                $(".sub").css("display", "block")
            } else {
                $(".no_sub").css("display", "block")
                $(".sub").css("display", "none")
            }
            console.log(is_sub)
        });

        $(':radio[name="is_sub"]').filter('[value="false"]').attr('checked', true);

        var no = 1;

        function minus(no) {
            var count = $(".sub .sub_baku_mutu").children().length;
            // console.log(count)
            if (count > 1) {
                $('#sub_baku_mutu_' + no).remove();
                sorting()
                if (no == count) {
                    $('#sub_baku_mutu_' + (count - 1) + ' .card-body').append(
                        '<button type="button" id="tambah" class="tambah btn btn-primary btn-lg btn-block"><i class="fas fa-plus"></i> Sub Baku Mutu</button>'
                    )
                    $("#tambah").click(function() {
                        tambah(no + 1)
                        sorting()
                    });
                }
            }


        }

        function tambah(no) {
            $('#tambah').remove();

            var new_field = $(`<div class="form-group sub_baku_mutu" id="sub_baku_mutu_` + no + `">
                    <div class="card">
                        <div class="card-body">
                            <button type="button" onclick="minus(` + no + `)" class="close" id="minus_` + no + `" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h5 class="card-title"><center>Sub Baku Mutu ` + no +
                `</center></h5>
                            <div class="form-group">
                                <label for="min">Nama Sub Baku</label>
                                <input type="text" class="form-control" id="name_subbakumutu" name="name_subbakumutu[` +
                (no - 1) + `]" placeholder="Nama Sub Baku Mutu"  >
                            </div>
                            <div class="form-group">
                                <label for="min">Kadar Min Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapan koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                                <input type="text" class="form-control" id="min" name="min[` + (no - 1) + `]" placeholder="Kadar Min Baku Mutu"  >
                            </div>

                            <div class="form-group">
                                <label for="max">Kadar Max Baku Mutu <br><b>(Masukkan berupa angka dan apabila terdapat koma, maka menggunakan . (titik), apabila tidak ada kosongi)</b></label>
                                <input type="text" class="form-control" id="max" name="max[` + (no - 1) + `]" placeholder="Kadar Max Baku Mutu"  >
                            </div>

                            <div class="form-group">
                                <label for="equal">Nilai Harus Sama Dengan <br><b>(Apabila nilai baku mutu bukan berupa range minimal maksimal misal (Negatif atau Positif) maka isi disini, apabila tidak maka kosongi)</b></label>
                                <input type="text" class="form-control" id="equal" name="equal[` + (no - 1) + `]" placeholder="Nilai Harus Sama Dengan"  >
                            </div>

                            <div class="form-group">
                                <label for="nilai_baku_mutu">Nilai Baku Mutu di Laporan</label>
                                <input type="text" class="form-control" id="nilai_baku_mutu" name="nilai_baku_mutu[` +
                (no - 1) + `]" placeholder="Nilai Baku Mutu"  >
                            </div><button type="button" id="tambah" class="tambah btn btn-primary btn-lg btn-block"><i class="fas fa-plus"></i> Sub Baku Mutu</button>
                            </div>
                            </div>
                            </div>`);

            $(".sub").append(new_field);
            // $("#minus_"+(no+1)).click(function() {
            //     minus(no+1)
            //     sorting()
            // });

            $("#tambah").click(function() {
                tambah(no + 1)
                sorting()
            });
            sorting()

        }

        $("#minus_" + no).click(function() {
            sorting()
        });

        function sorting() {

            $(".sub .sub_baku_mutu").each(function(i, element) {
                // $(element).find('.card-title');
                // console.log( $(element).find('.card-title'))
                $(element).find('.card-title').html("<center>Sub Baku " + (i + 1) + "</center>");
                $(element).find('.close').prop("id", "minus_" + (i + 1));
                $(element).find('.close').attr("onclick", "minus(" + (i + 1) + ")");
                $(element).prop("id", "sub_baku_mutu_" + (i + 1));
                $(element).find('#min').prop("name", "min[" + (i) + "]");
                $(element).find('#max').prop("name", "max[" + (i) + "]");
                $(element).find('#equal').prop("name", "equal[" + (i) + "]");
                $(element).find('#nilai_baku_mutu').prop("name", "nilai_baku_mutu[" + (i) + "]");
                // $("#minus_"+(i+1)).click(function() {
                //     minus(i+1)
                //     sorting()
                // });


                // $('.card-title').text();
            });
        }

        $("#tambah").click(function() {
            tambah(no + 1)
        });

        function goBack() {
            window.history.back();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
@endsection
