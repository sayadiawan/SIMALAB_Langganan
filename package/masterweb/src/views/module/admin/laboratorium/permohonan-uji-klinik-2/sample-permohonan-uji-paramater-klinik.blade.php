@extends('masterweb::template.admin.layout')
@section('title')
    Permohonan Uji Klinik
@endsection


@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"
        integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script> --}}
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="//cdn.ckeditor.com/4.22.1/basic/ckeditor.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
                                <li class="breadcrumb-item"><a href="{{ url('/elits-permohonan-uji-klinik-2') }}">Permohonan
                                        Uji Klinik
                                        Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><span>Sample Permohonan Uji
                                        Klinik</span>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form
        action="{{ route('elits-permohonan-uji-klinik-2.store-permohonan-uji-sample', $item_permohonan_uji_klinik->id_permohonan_uji_klinik) }}"
        method="POST" enctype="multipart/form-data" id="form">
        {{-- <form action=""> --}}

        @csrf
        @method('PUT')

        <input type="hidden" name="_token-select" id="csrf-token" value="{{ Session::token() }}" />
        <div class="card">
            <div class="card-header">
                <h4>Sample Permohonan Uji Paket Klinik
                </h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="250px">No. Register</th>
                                    <td>{{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. Rekam Medis</th>
                                    <td>
                                        {{ Carbon\Carbon::createFromFormat('Y-m-d', $item_permohonan_uji_klinik->pasien->tgllahir_pasien)->format('dmY') . str_pad((int) $item_permohonan_uji_klinik->pasien->no_rekammedis_pasien, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Tgl. Register</th>
                                    <td>{{ $tgl_register_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Nama Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->nama_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Usia</th>
                                    <td>
                                        {{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik .
                                            ' tahun ' .
                                            $item_permohonan_uji_klinik->umurbulan_pasien_permohonan_uji_klinik .
                                            ' bulan ' .
                                            $item_permohonan_uji_klinik->umurhari_pasien_permohonan_uji_klinik .
                                            ' hari' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Jenis Kelamin</th>
                                    <td>
                                        {{ $item_permohonan_uji_klinik->pasien->gender_pasien == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Alamat Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->alamat_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. Telepon</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->phone_pasien }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="250px">No. Pasien</th>
                                    <td>{{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">No. KTP</th>
                                    <td>{{ $item_permohonan_uji_klinik->pasien->nik_pasien }}</td>
                                </tr>

                                <tr>
                                    <th width="250px">Tanggal Lahir</th>
                                    <td>
                                        {{ isset($item_permohonan_uji_klinik->pasien->tgllahir_pasien)
                                            ? \Carbon\Carbon::createFromFormat('Y-m-d', $item_permohonan_uji_klinik->pasien->tgllahir_pasien)->isoFormat(
                                                'D MMMM Y',
                                            )
                                            : '' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th width="250px">Pengirim</th>
                                    <td>{{ $item_permohonan_uji_klinik->namapengirim_permohonan_uji_klinik }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 p-4">
                        <div class="form-group">
                            <label for="jenis_spesimen">JENIS SPESIMEN</label>
                            <select class="form-control" name="jenis_spesimen[]" id="jenis_spesimen" multiple="multiple">
                                <option value="-"
                                    {{ is_null($jenis_spesimen) || $jenis_spesimen == '-' ? 'selected' : '' }} selected>
                                </option>
                                @foreach (['-', 'Darah Beku', 'NaF', 'EDTA', 'Urine'] as $option)
                                    <option value="{{ $option }}"
                                        {{ $jenis_spesimen == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function() {
                                    $("#jenis_spesimen").select2({
                                        theme: 'bootstrap4',
                                        allowClear: true
                                    });

                                    // Check if there is a selected value
                                    let selectedValue = "{{ $jenis_spesimen ?? old('jenis_spesimen') }}";
                                    if (selectedValue) {
                                        $("#jenis_spesimen").val(selectedValue).trigger('change');
                                    } else {
                                        $("#jenis_spesimen").val(null).trigger('change');
                                    }
                                });
                            </script>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tgl_sampling_permohonan_uji_klinik">TGL. SAMPLING <span
                                            style="color: red">*</span></label>
                                    <input type="date" class="form-control" name="tgl_sampling_permohonan_uji_klinik"
                                        id="tgl_sampling_permohonan_uji_klinik" value="{{ $tgl_sampling }}" placeholder=""
                                        @if ($data_verifikasi_registrasi)  @endif>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jam_sampling_permohonan_uji_klinik">JAM SAMPLING <span
                                            style="color: red">*</span></label>
                                    <input type="time" class="form-control" name="jam_sampling_permohonan_uji_klinik"
                                        id="jam_sampling_permohonan_uji_klinik" value="{{ $jam_sampling }}" placeholder=""
                                        @if ($data_verifikasi_registrasi)  @endif>
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var tglSamplingInput = document.querySelector('#tgl_sampling_permohonan_uji_klinik');
                                var jamSamplingInput = document.querySelector('#jam_sampling_permohonan_uji_klinik');

                                if (!tglSamplingInput.value) {
                                    var today = new Date();
                                    var year = today.getFullYear();
                                    var month = ('0' + (today.getMonth() + 1)).slice(-2);
                                    var day = ('0' + today.getDate()).slice(-2);

                                    var formattedDate = `${year}-${month}-${day}`;
                                    tglSamplingInput.value = formattedDate;
                                }

                                if (!jamSamplingInput.value) {
                                    var now = new Date();
                                    var hours = ('0' + now.getHours()).slice(-2);
                                    var minutes = ('0' + now.getMinutes()).slice(-2);

                                    var formattedTime = `${hours}:${minutes}`;
                                    jamSamplingInput.value = formattedTime;
                                }
                            });
                        </script>


                        {{-- <div class="form-group">
                            <label for="plebotomist_permohonan_uji_klinik">PLEBOTOMIST</label>
                            <input type="text" class="form-control" name="plebotomist_permohonan_uji_klinik"
                                id="plebotomist_permohonan_uji_klinik" placeholder="Masukkan plebotomist"
                                value="{{ $plebotomist ? $plebotomist : old('plebotomist_permohonan_uji_klinik') }}">
                        </div> --}}

                        <div class="form-group">
                            <label for="plebotomist_permohonan_uji_klinik">PLEBOTOMIST<span
                                style="color: red">*</span></label>
                            <select class="form-control" name="plebotomist_permohonan_uji_klinik"
                                id="plebotomist_permohonan_uji_klinik" required>
                                {{-- <option value="" {{ is_null($plebotomist) ? 'selected' : '' }}></option> --}}
                                <option value="" disabled selected>Pilih Plebotomist</option>
                                @foreach ($optionPlebotomist as $option)
                                    <option value="{{ $option->name_satu_sehat_practitioner }}"
                                        {{ $plebotomist == $option->name_satu_sehat_practitioner ? 'selected' : '' }}>
                                        {{ $option->name_satu_sehat_practitioner }}
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function() {
                                    $("#plebotomist_permohonan_uji_klinik").select2({
                                        placeholder: 'Pilih plebotomist',
                                        theme: 'bootstrap4',
                                        allowClear: true
                                    });

                                    // Check if there is a selected value
                                    let selectedValue = "{{ $plebotomist ?? old('plebotomist_permohonan_uji_klinik') }}";
                                    if (selectedValue) {
                                        $("#plebotomist_permohonan_uji_klinik").val(selectedValue).trigger('change');
                                    } else {
                                        $("#plebotomist_permohonan_uji_klinik").val(null).trigger('change');
                                    }
                                });
                            </script>
                        </div>

                        <div class="form-group">
                            <label for="vena_permohonan_uji_klinik">VENA LENGAN KANAN/KIRI</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($vena == 'R')
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_1" value="R" checked>
                                        Kanan
                                        <i class="input-helper"></i>
                                    @else
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_1" value="R">
                                        Kanan
                                        <i class="input-helper"></i>
                                    @endif

                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($vena == 'L')
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_2" value="L" checked>
                                        Kiri
                                        <i class="input-helper"></i>
                                    @else
                                        <input type="radio" class="form-check-input" name="vena_permohonan_uji_klinik"
                                            id="vena_permohonan_uji_klinik_2" value="L">
                                        Kiri
                                        <i class="input-helper"></i>
                                    @endif

                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="lokasi_lain_permohonan_uji_klinik">LOKASI LAIN</label>
                            <input type="text" class="form-control" name="lokasi_lain_permohonan_uji_klinik"
                                id="lokasi_lain_permohonan_uji_klinik" placeholder="Masukkan lokasi lain"
                                value="{{ $lokasi_lain ? $lokasi_lain : old('lokasi_lain_permohonan_uji_klinik') }}">
                        </div>

                        <div class="form-group">
                            <label for="penanganan_hasil_permohonan_uji_klinik">PENANGANAN HASIL</label>
                            <select class="form-control" name="penanganan_hasil_permohonan_uji_klinik"
                                id="penanganan_hasil_permohonan_uji_klinik">
                                <option value="" {{ is_null($penanganan_hasil) ? 'selected' : '' }}></option>
                                @foreach (['Diambil Pasien', 'Diambil Dokter', 'Dikirim ke Pasien', 'Via Internet'] as $option)
                                    <option value="{{ $option }}"
                                        {{ $penanganan_hasil == $option ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                $(document).ready(function() {
                                    $("#penanganan_hasil_permohonan_uji_klinik").select2({
                                        placeholder: 'Pilih penanganan hasil',
                                        theme: 'bootstrap4',
                                        allowClear: true
                                    });

                                    // Check if there is a selected value
                                    let selectedValue = "{{ $penanganan_hasil ?? old('penanganan_hasil_permohonan_uji_klinik') }}";
                                    if (selectedValue) {
                                        $("#penanganan_hasil_permohonan_uji_klinik").val(selectedValue).trigger('change');
                                    } else {
                                        $("#penanganan_hasil_permohonan_uji_klinik").val(null).trigger('change');
                                    }
                                });
                            </script>
                        </div>

                        <div class="form-group">
                            <label for="catatan_permohonan_uji_klinik">CATATAN</label>
                            <textarea class="form-control" name="catatan_permohonan_uji_klinik" id="catatan_permohonan_uji_klinik"
                                rows="3" placeholder="Isikan catatan pasien" style="height: 100px">{{ $catatan ? $catatan : old('catatan_permohonan_uji_klinik') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="riwayat_obat_permohonan_uji_klinik">RIWAYAT PENGGUNAAN OBAT</label>
                            <textarea class="form-control" name="riwayat_obat_permohonan_uji_klinik" id="riwayat_obat_permohonan_uji_klinik"
                                rows="3" placeholder="Isikan riwayat penggunaan obat pasien" style="height: 100px">{{ $riwayat_obat ? $riwayat_obat : old('riwayat_obat_permohonan_uji_klinik') }}</textarea>
                            {{-- <script>
                              $(document).ready(function() {
                                  CKEDITOR.replace('riwayat_obat_permohonan_uji_klinik');
                              });
                          </script> --}}
                        </div>
                    </div>
                </div>

    </form>
    <button type="button" class="btn btn-primary mr-2 btn-simpan">Simpan</button>
    <button type="button" class="btn btn-light"
        onclick="document.location='{{ url('/elits-permohonan-uji-klinik-2/verification/' . $item_permohonan_uji_klinik->id_permohonan_uji_klinik) }}'">Kembali</button>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.3/moment.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"
        integrity="sha384-qlmct0AOBiA2VPZkMY3+2WqkHtIQ9lSdAsAn5RUJD/3vA5MKDgSGcdmIv4ycVxyn" crossorigin="anonymous">
    </script>



    <script>
        $(document).ready(function() {
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
                                    document.location =
                                        '/elits-permohonan-uji-klinik-2/verification/{{ $item_permohonan_uji_klinik->id_permohonan_uji_klinik }}';
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
            });
        })
    </script>
@endsection
