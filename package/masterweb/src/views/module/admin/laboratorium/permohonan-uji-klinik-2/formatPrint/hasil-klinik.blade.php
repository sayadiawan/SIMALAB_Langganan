<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Hasil Klinik - {{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</title>
    <link rel="shortcut icon" href="">
    @php

        // dd(count($value_items));
        // dd($arr_permohonan_parameter);

        $value_items = 0;
        foreach ($arr_permohonan_parameter as $item) {
            # code...

            foreach ($item['item_permohonan_parameter_satuan'] as $key => $parameter) {
                # code...
                $value_items++;
            }
            // dd($item);
        }

        if ($value_items < 3) {
            # code...
            $size_table = 9;
            $padding_table = 5  ;
        } elseif ($value_items >= 4 && $value_items <= 16) {
            # code...
            $size_table = 9;
        } else {
            $size_table = 8;
        }

    @endphp
    <style>
        .starter-template {
            text-align: center;
        }

        table {
            table-layout: fixed;
            width: 100%;
        }

        td,
        th {
            /* cell-padding: 5px !important; */
            word-wrap: break-word;
            /* This ensures text breaks into the next line */
            white-space: normal;
            /* Allows wrapping */
        }

        @media print {
            #cetak {
                display: none;
            }
        }

        .garis {
            border: 1px solid
        }

        .table2 {
            /* font-size: 5px; */
            text-align: center
        }

        .result {
            border-collapse: collapse;
        }

        .result td {
            border: 1px solid black;
            text-align: center;
        }

        @page {
            size: 794px 1248px;
            margin: 50px 20px 50px 20px;
        }

        @font-face {
            font-family: "source_sans_proregular";
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            /* font-size: 11px; */
        }

        body {
            font-family: Arial, Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
            font-size: 19px;
            text-align: justify;
            text-justify: inter-word;
        }

        .page_break {
            page-break-before: always;
        }

        .flex-container {
            display: flex !important;
            flex-wrap: nowrap !important;
        }

        .flex-container>div {
            width: 100px !important;
            margin: 10px !important;
        }

        th {
            margin-top: 2px;
            font-size: {{ $size_table }}pt !important;
            padding-top : {{ $padding_table ?? '' }}pt !important;
            padding-bottom : {{ $padding_table ?? '' }}pt !important;
        }

        td {
            margin-top: 2px;
            font-size: {{ $size_table }}pt !important;
            padding-top : {{ $padding_table ?? '' }}pt !important;
            padding-bottom : {{ $padding_table ?? '' }}pt !important;
        }


        .no-break {
            page-break-inside: avoid !important;
        }
    </style>
</head>


<body>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td><img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated.png') }}" width="100%">
            </td>
        </tr>
    </table>


    {{--
<table width="100%" cellspacing="0" cellpadding="0">
    <tr>
        <td>Disampaikan dengan hormat hasil laboratorium klinik kami adalah sebagai berikut:</td>
    </tr>
</table> --}}

    <br>

    <table width="100%" cellspacing="0" cellpadding="0" border="1">
        <tr>
            <td width="20%" style="  vertical-align: middle; padding: 3px;">
                Nama
            </td>
            <td width="80%" style="padding-left: 3px;">
                <strong>{{ $item_permohonan_uji_klinik->pasien->nama_pasien }}</strong>
            </td>
            <td width="20%" style="  vertical-align: middle; padding: 3px;">
                No Lab
            </td>
            <td width="80%" style="padding: 3px;">
                {!! $no_LHU !!}
            </td>

        </tr>
        <tr>
            <td width="30%" style="  vertical-align: middle; padding: 3px;">
                Tanggal Lahir / Umur
            </td>
            <td style="padding: 3px;">
                {{ isset($item_permohonan_uji_klinik->pasien->tgllahir_pasien)
                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $item_permohonan_uji_klinik->pasien->tgllahir_pasien)->isoFormat(
                        'D MMMM Y',
                    )
                    : '' }}
                / {{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik ?? '-' }} Tahun
                {{ $item_permohonan_uji_klinik->umurbulan_pasien_permohonan_uji_klinik ?? '-' }} Bulan
                {{ $item_permohonan_uji_klinik->umurhari_pasien_permohonan_uji_klinik ?? '-' }} Hari
            </td>
            <td width="30%" style="  vertical-align: middle; padding: 3px;">
                Tanggal Register
            </td>
            <td style="padding: 3px;">
                {{ isset($item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)
                    ? \Carbon\Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik,
                    )->isoFormat('D MMMM Y')
                    : '-' }}
            </td>
        </tr>
        <tr>
            <td width="30%" style="  vertical-align: middle; padding: 3px;">
                Alamat
            </td>
            <td style="padding-left: 3px;">
                {{ $item_permohonan_uji_klinik->pasien->alamat_pasien ?? '-' }}
            </td>
            <td width="30%" style="  vertical-align: middle; padding: 3px;">
                Dokter
            </td>
            <td style="padding: 3px;">
                dr. Dini Nurani Kusumastuti, Sp. PK
            </td>
        </tr>
    </table>



    <br>

    <table cellspacing="0" cellpadding="0" border="1" style="margin-bottom: 0px;">
        <thead style="background-color: rgb(184, 246, 246)">
            <tr>
                <th style="width: 40%;">PEMERIKSAAN</th>
                <th style="width: 15%; text-align: center;  ">HASIL</th>
                <th style="width: 20%; text-align: center;  ">SATUAN</th>
                <th style="width: 15%; text-align: center;  ">NILAI<br>RUJUKAN</th>
                <th style="width: 20%; text-align: center;  ">METODE</th>
                <th style="width: 45%; text-align: center;  ">KETERANGAN</th>
            </tr>
        </thead>


        <tbody>
            @php
                $item_permohonan_parameter_satuan = 0; // Variabel untuk menyimpan total jumlah elemen

                // Misalkan $data adalah array yang diberikan
                foreach ($arr_permohonan_parameter as $item) {
                    // Pastikan item_permohonan_parameter_satuan ada dan adalah array
                    if (
                        isset($item['item_permohonan_parameter_satuan']) &&
                        is_array($item['item_permohonan_parameter_satuan'])
                    ) {
                        // Hitung elemen di dalam setiap array item_permohonan_parameter_satuan
                        $item_permohonan_parameter_satuan += count($item['item_permohonan_parameter_satuan']);
                    }
                }

                // Menampilkan total jumlah elemen
                // dd ($arr_permohonan_parameter);
                $count = 0;
            @endphp


            @foreach ($arr_permohonan_parameter as $key_parameter_jenis_klinik => $item_parameter_jenis_klinik)
                @php
                    $nama_param_jenis = str_replace(
                        '<br>',
                        '',
                        $item_parameter_jenis_klinik['name_parameter_jenis_klinik'],
                    );
                    $nama_param_jenis = html_entity_decode($item_parameter_jenis_klinik['name_parameter_jenis_klinik']);
                @endphp
                @if ($item_permohonan_uji_klinik->is_prolanis_gula == 1 || $item_permohonan_uji_klinik->is_prolanis_gula == 1)
                    @if (collect($item_parameter_jenis_klinik['item_permohonan_parameter_satuan'])->contains(function ($item_satuan_klinik) {
                            return $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== null &&
                                $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== '-';
                        }))
                        <tr>
                            <th colspan="6" style="text-align: left; padding-left: 3px;">
                                <strong>{!! $nama_param_jenis !!}</strong>
                            </th>
                        </tr>
                    @endif
                @else
                    <tr>
                        <th colspan="6" style="text-align: left; padding-left: 3px;">
                            <strong>{!! $nama_param_jenis !!}</strong>
                        </th>
                    </tr>
                @endif

                {{-- @for ($j = 0; $j < count($item_parameter_jenis_klinik['item_permohonan_parameter_satuan']) - 1; $j++)

                @endfor --}}

                @php
                    $count_parameter = 0;
                @endphp
                @foreach ($item_parameter_jenis_klinik['item_permohonan_parameter_satuan'] as $key_satuan_klinik => $item_satuan_klinik)
                    @if (isset($item_satuan_klinik['hasil_permohonan_uji_parameter_klinik']) &&
                            $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] != '-')
                        @if (count($item_satuan_klinik['data_permohonan_uji_subsatuan_klinik']) > 0)
                            <tr>
                                <td colspan="6" style="text-align: left; padding-left: 3px;">
                                    {{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}:
                                </td>
                            </tr>

                            {{-- melakukan mapping data permohonan uji parameter satuan yang memiliki permohonan uji parameter subsatuan --}}
                            @foreach ($item_satuan_klinik['data_permohonan_uji_subsatuan_klinik'] as $key_subsatuan_klinik => $item_subsatuan_klinik)
                              @if($item_subsatuan_klinik['hasil_permohonan_uji_sub_parameter_klinik'] !== null && $item_subsatuan_klinik['hasil_permohonan_uji_sub_parameter_klinik'] !== '-')
                                <tr>
                                    {{-- nama test --}}
                                    <td style="text-align: center">
                                        <p style="padding-left: 30px">
                                            {{ $item_subsatuan_klinik['nama_parameter_sub_satuan_klinik_id'] }} ~
                                        </p>
                                    </td>

                                    {{-- hasil --}}
                                    <td style="text-align: center">
                                        {{ $item_subsatuan_klinik['hasil_permohonan_uji_sub_parameter_klinik'] ?? '-' }}
                                    </td>

                                    {{-- flag --}}


                                    {{-- satuan --}}
                                    <td style="text-align: center">
                                        @if ($item_subsatuan_klinik['satuan_permohonan_uji_sub_parameter_klinik'] != null)
                                            {{ $item_subsatuan_klinik['nama_satuan_permohonan_uji_sub_parameter_klinik'] }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- nilai rujukan --}}
                                    <td style="text-align: center">
                                        {{ $item_subsatuan_klinik['nilai_baku_mutu_detail_parameter_klinik'] }}
                                    </td>

                                    {{-- metode --}}
                                    <td style="text-align: center">
                                        {{ $item_subsatuan_klinik['method_permohonan_uji_parameter_klinik'] }}
                                    </td>

                                    {{-- keterangan --}}
                                    <td style="text-align: center">
                                        {!! $item_subsatuan_klinik['keterangan_permohonan_uji_sub_parameter_klinik'] ?? '' !!}
                                    </td>
                                </tr>
                              @endif
                            @endforeach
                        @else
                            @if (
                                !(
                                    $count_parameter == count($item_parameter_jenis_klinik['item_permohonan_parameter_satuan']) - 1 &&
                                    $count == count($arr_permohonan_parameter) - 1
                                ))
                                @if ($item_satuan_klinik['nama_parameter_satuan_klinik'] == 'Rerata Glukosa Darah')
                                    {{-- @php
                                        dd($item_satuan_klinik);
                                    @endphp --}}
                                @endif
                                @if($item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== null && $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== '-')
                                  <tr style="">
                                      {{-- nama test --}}
                                      <td style="text-align: left; padding-left: 3px;">
                                          {{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}
                                      </td>


                                      {{-- hasil --}}
                                      <td style="text-align: center">
                                          {{ $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] ?? '-' }}
                                      </td>

                                      {{-- flag --}}

                                      {{-- satuan --}}
                                      {{-- kondisi jika data satuan pada permohonan uji parameter klinik belum terpilih dan suda terpilih --}}
                                      <td style="text-align: center">

                                          @if ($item_satuan_klinik['satuan_permohonan_uji_parameter_klinik'] != null)
                                              {!! $item_satuan_klinik['nama_satuan_permohonan_uji_parameter_klinik'] !!}
                                          @else
                                              -
                                          @endif

                                      </td>

                                      {{-- nilai rujukan --}}
                                      <td
                                          style="text-align: center; font-family: DejaVu Sans !important; font-size:8pt!important">
                                          {!! rubahNilaikeHtml($item_satuan_klinik['nilai_baku_mutu']) !!}
                                      </td>

                                      {{-- metode --}}
                                      <td style="text-align: center">
                                          {{ $item_satuan_klinik['method_permohonan_uji_parameter_klinik'] }}
                                      </td>


                                      {{-- keterangan --}}
                                      {{-- @if ($item_satuan_klinik['nama_parameter_satuan_klinik'] == 'Cholesterol HDL')

                                      @endif --}}
                                      <td
                                          style="text-align: left; font-family: DejaVu Sans !important; padding-left: 3px; font-size: 10px !important">
                                          @php
                                              // Ambil keterangan
                                              $keterangan =
                                                  $item_satuan_klinik['keterangan_permohonan_uji_parameter_klinik'] ?? '';

                                              // Hapus tag <br> dan konversi entitas HTML
                                              $keterangan = str_replace('<br>', '', $keterangan);
                                              $keterangan = html_entity_decode($keterangan); // Mengonversi entitas HTML
                                              $keterangan = str_replace('&nbsp;', ' ', $keterangan); // Mengganti &nbsp; dengan spasi

                                              // Trim untuk menghapus spasi di awal dan akhir
                                              $keterangan = trim($keterangan);
                                          @endphp

                                          @if ($keterangan !== strip_tags($keterangan) && !empty($keterangan))
                                              {{-- Jika string mengandung HTML --}}
                                              {!! rubahNilaikeHtml($keterangan) !!}
                                          @else
                                              {{-- Jika string tidak mengandung HTML, tampilkan dengan nl2br --}}
                                              {!! rubahNilaikeHtml(e($keterangan)) !!}
                                          @endif
                                      </td>
                                  </tr>
                                @endif
                            @endif
                        @endif
                    @endif
                    @php
                        $count_parameter++;
                    @endphp
                @endforeach
                @php
                    $count++;
                @endphp
            @endforeach



        </tbody>

    </table>

    <div class="no-break" style="page-break-inside: avoid;">
      @if($item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== null && $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] !== '-')
        <table cellspacing="0" cellpadding="0" border="1" style="margin-top: 0px;">
            <tr>
                {{-- nama test --}}
                <td style="width: 40%;text-align: left; padding-left: 3px;">
                    {{ $item_satuan_klinik['nama_parameter_satuan_klinik'] }}
                </td>


                {{-- hasil --}}
                <td style="width: 15%;text-align: center">
                    {{ $item_satuan_klinik['hasil_permohonan_uji_parameter_klinik'] }}
                </td>

                {{-- flag --}}

                {{-- satuan --}}
                {{-- kondisi jika data satuan pada permohonan uji parameter klinik belum terpilih dan suda terpilih --}}
                <td style="width: 20%;text-align: center">

                    @if ($item_satuan_klinik['satuan_permohonan_uji_parameter_klinik'] != null)
                        {!! $item_satuan_klinik['nama_satuan_permohonan_uji_parameter_klinik'] !!}
                    @else
                        -
                    @endif

                </td>

                {{-- nilai rujukan --}}
                <td
                    style="width: 15%; text-align: center; font-family: DejaVu Sans !important; font-size:8pt!important">
                    {!! rubahNilaikeHtml($item_satuan_klinik['nilai_baku_mutu']) !!}
                </td>

                {{-- metode --}}
                <td style="width: 20%;text-align: center">
                    {{ $item_satuan_klinik['method_permohonan_uji_parameter_klinik'] }}
                </td>


                {{-- keterangan --}}
                {{-- @if ($item_satuan_klinik['nama_parameter_satuan_klinik'] == 'Cholesterol HDL')

                @endif --}}
                <td
                    style="width: 45%;text-align: left; font-family: DejaVu Sans !important; padding-left: 3px; font-size: 10px !important">
                    @php
                        // Ambil keterangan
                        $keterangan = $item_satuan_klinik['keterangan_permohonan_uji_parameter_klinik'] ?? '';

                        // Hapus tag <br> dan konversi entitas HTML
                        $keterangan = str_replace('<br>', '', $keterangan);
                        $keterangan = html_entity_decode($keterangan); // Mengonversi entitas HTML
                        $keterangan = str_replace('&nbsp;', ' ', $keterangan); // Mengganti &nbsp; dengan spasi

                        // Trim untuk menghapus spasi di awal dan akhir
                        $keterangan = trim($keterangan);
                    @endphp

                    @if ($keterangan !== strip_tags($keterangan) && !empty($keterangan))
                        {{-- Jika string mengandung HTML --}}
                        {!! rubahNilaikeHtml($keterangan) !!}
                    @else
                        {{-- Jika string tidak mengandung HTML, tampilkan dengan nl2br --}}
                        {!! rubahNilaikeHtml(e($keterangan)) !!}
                    @endif
                </td>
            </tr>
        </table>
      @endif
        <br>

        <table class="no-break" width="100%" cellspacing="0" cellpadding="0" style="page-break-inside: avoid;">

            <tr>
                <td width="30%" style="text-align: center">
                </td>
                <td width="30%">

                </td>
                @php
                    $validasi = Smt\Masterweb\Models\VerificationActivitySample::where(
                        'is_klinik',
                        $item_permohonan_uji_klinik->id_permohonan_uji_klinik,
                    )
                        ->where('id_verification_activity', 5)
                        ->first();

                    if (isset($validasi)) {
                        # code...
                        $tanggal_validasi = $validasi->stop_date;
                        $nama_petugas_validasi = $validasi->nama_petugas;
                    } else {
                        $tanggal_validasi = null;
                        $nama_petugas_validasi = null;
                    }
                    // dd($tanggal_validasi);
                @endphp
                <td width="30%" style="text-align: center; font-size: 10pt !important">Boyolali,
                    {{ isset($tanggal_validasi)
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $tanggal_validasi)->isoFormat('D MMMM Y')
                        : '-' }}
                </td>
            </tr>
            @php
                $day = Carbon\Carbon::createFromFormat(
                    'Y-m-d H:i:s',
                    $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik,
                )->dayName;

            @endphp
            @if (isset($validasi) && isset($nama_petugas_validasi))
                @if (
                    $nama_petugas_validasi == 'dr. Muharyati' &&
                        ($day != 'Selasa' && $day != "Jum'at") &&
                        ($item_permohonan_uji_klinik->is_prolanis_gula != 1 && $item_permohonan_uji_klinik->is_prolanis_urine != 1))
                    <tr>
                        <td width="30%" style="text-align: center"></td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center; font-size: 10pt !important">di Otorisasi oleh</td>
                    </tr>
                    <tr>
                        <td width="30%" style="text-align: center">

                        </td>
                        <td width="30%">

                        </td>
                        @php
                            $petugas = 'dr. Muharyati';
                            $nip = 'NIP. 19721106 2002 12 2';
                        @endphp
                        @if (isset($signOption) && $signOption == 0)
                            <td width="30%" style="text-align: center">
                                <br>
                                <br>
                                {{ $petugas }} <br>
                                {{ $nip }}
                            </td>
                        @else
                            <td width="30%" style="text-align: center">@include('masterweb::module.admin.laboratorium.template.TTD_BSRE_KLINIK')</td>
                        @endif
                    </tr>
                @else
                    <tr>


                        <td width="30%" style="text-align: center;font-size: 10pt !important">Penanggungjawab Lab.
                            Klinik
                        </td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center;font-size: 10pt !important">Petugas</td>
                    </tr>
                    @if (isset($signOption) && $signOption == 0)
                        <tr>
                            <td width="30%" style="text-align: center">
                                <br>
                                <br>
                                <img src="{{ public_path('assets/admin/images/dr_dini_ttd.png') }}" width="130px" />
                                <br>
                                <br>
                            </td>
                            <td width="30%">

                            </td>
                            <td width="30%" style="text-align: center">
                                <br>




                                <br>

                            </td>
                        </tr>
                        <tr>
                            <td width="30%" style="text-align: center">

                                <span style="text-decoration: underline;">dr. DINI NURANI KUSUMASTUTI.
                                    Sp.PK</span> <br>
                                SIP.
                                503.5/0034/SIPD/4.14/I/2024
                            </td>
                            <td width="30%">
                            </td>
                            @php
                                $petugas = $nama_petugas_pemeriksa;
                                $nip = ' ';
                            @endphp
                            <td width="30%" style="text-align: center">
                                {{ $petugas }} <br>
                                {{ $nip }}
                            </td>
                        </tr>
                    @else
                        @php
                            $petugas = 'dr. DINI NURANI KUSUMASTUTI. Sp.PK';
                            $nip = 'SIP. 503.5/0034/SIPD/4.14/I/2024';
                        @endphp
                        <td width="40%">

                            @include('masterweb::module.admin.laboratorium.template.TTD_BSRE_KLINIK')
                        </td>
                        <td></td>
                        @php
                            $petugas = $nama_petugas_pemeriksa;
                            $nip = ' ';
                        @endphp
                        <td width="40%">
                            @include('masterweb::module.admin.laboratorium.template.TTD_BSRE_KLINIK')
                        </td>
                    @endif
                @endif
            @else
                @if ($day != 'Selasa' && $day != "Jum'at")
                    <tr>
                        <td width="30%" style="text-align: center"></td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center">di Otorisasi oleh</td>
                    </tr>
                    <tr>
                        <td width="30%" style="text-align: center">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center"></td>
                    </tr>
                    <tr>
                        <td width="30%" style="text-align: center"></td>
                        <td width="30%"></td>
                        <td width="30%" style="text-align: center"><u>dr. Muharyati</u> <br> NIP.
                            19721106 2002 12 2
                            001
                        </td>
                    </tr>
                @else
                    <tr>
                        <td width="30%" style="text-align: center">Penanggungjawab Lab. Klinik
                        </td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center">Petugas</td>
                    </tr>
                    <tr>
                        <td width="30%" style="text-align: center">
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                            <br>
                        </td>
                        <td width="30%">

                        </td>
                        <td width="30%" style="text-align: center"></td>
                    </tr>
                    <tr>
                        <td width="30%" style="text-align: center">

                            <span style="text-decoration: underline;">dr. DINI NURANI KUSUMASTUTI.
                                Sp.PK</span> <br> SIP.
                            503.5/0034/SIPD/4.14/I/2024
                        </td>
                        <td width="30%">

                        </td>
                        @php
                            $nama_petugas_pemeriksa = '...................';
                        @endphp
                        <td width="30%" style="text-align: center">{{ $nama_petugas_pemeriksa }}</td>
                    </tr>
                @endif

            @endif
        </table>
    </div>



</body>

</html>
