<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>INFORMED CONSENT - {{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}</title>
    <link rel="shortcut icon" href="">
    <style>
        .starter-template {
            text-align: center;
        }

        table>tr>td {
            /* cell-padding: 5px !important; */
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
            font-size: 5px;
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
            margin: 20px 75px 20px 75px;
        }

        @font-face {
            font-family: "source_sans_proregular";
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            font-size: 11px;
        }

        body {
            font-family: Arial, Calibri, Candara, Segoe, Segoe UI, Optima, Arial, sans-serif;
            font-size: 11px;
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

        .border {
            border: 1.5px solid black;
        }

        .v-align-top {
            vertical-align: top;
        }

        .checkbox {
            height: 10px;
            position: relative;
            bottom: 5px;
        }

        .blue-header {
            background-color: #3a95b5;
            color: white;
            font-weight: bold;
            letter-spacing: 1px;
            padding-left: 4px;
            height: 10px;
        }

        .text-center {
            text-align: center;
        }

        .td-header {
            font-family: "Times New Roman", Times, serif !important;
            font-weight: bold;
            text-align: center;
        }

        .table-consent td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 4px 2px 4px 2px;
            font-size: 12.4px;
        }

        .table-clear td {
            border: 0px;
            padding: 0px;
        }
    </style>
</head>

<body>

    <div style="margin-top: 60px;padding: 0px 20px 0px 20px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="td-header">
                    <span style="font-size: 20px;">
                        <u>INFORMED CONSENT</u>
                    </span>
                </td>
            </tr>
        </table>

        <table style="margin: 5px 0px 5px 0px;" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td class="td-header">
                    <span style="font-size: 20px;">
                        (PERSETUJUAN TINDAKAN MEDIK)
                    </span>
                </td>
            </tr>
        </table>

        <table style="font-size: 18px;font-family: 'Times New Roman', Times, serif !important;" width="100%"
            cellspacing="0" cellpadding="0">
            <tr>
                <td>Saya yang bertandatangan di bawah ini :</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        @if ($item_permohonan_uji_klinik->nama_perwakilan_permohonan_uji_klinik)
                            @php
                                $umurPerwakilan = \Smt\Masterweb\Helpers\DateHelper::calcAge(
                                    $item_permohonan_uji_klinik->tanggal_lahir_perwakilan_permohonan_uji_klinik,
                                );
                            @endphp
                            <tr>
                                <td width="28%">Nama</td>
                                <td width="2%">:</td>
                                <td>{{ $item_permohonan_uji_klinik->nama_perwakilan_permohonan_uji_klinik ?? '......................................................' }}
                                </td>
                            </tr>
                            <tr>
                                <td>Umur/ Jenis Kelamin</td>
                                <td>:</td>


                                <td>{{ $umurPerwakilan . ' Tahun / ' . ($item_permohonan_uji_klinik->gender_perwakilan_permohonan_uji_klinik == 'L' ? 'Laki-laki' : 'Perempuan' ?? '......................................................') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td>{{ $item_permohonan_uji_klinik->alamat_perwakilan_permohonan_uji_klinik ?? '......................................................' }}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td width="28%">Nama</td>
                                <td width="2%">:</td>
                                <td>{{ $item_pasien->nama_pasien }}</td>
                            </tr>
                            <tr>
                                <td>Umur/ Jenis Kelamin</td>
                                <td>:</td>
                                <td>{{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik . ' Tahun / ' . ($item_pasien->gender_pasien == 'L' ? 'Laki-laki' : 'Perempuan') }}
                                </td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td>:</td>
                                <td>{{ $item_pasien->alamat_pasien }}</td>
                            </tr>
                        @endif

                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: justify;">
                    Menyatakan dengan sesungguhnya telah memberikan PERSETUJUAN untuk dilakukan
                    tindakan medis berupa pengambilan darah sebanyak 3cc/5cc/10cc di lengan tangan kanan/ kiri
                    terhadap diri saya sendiri*/ anak*/ Istri*/ Suami*/ Ayah*/ Ibu*/ Kakak*/ Adik*/ Cucu* saya
                    dengan identitas :
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="28%">Nama</td>
                            <td width="2%">:</td>
                            <td>{{ $item_pasien->nama_pasien }}</td>
                        </tr>
                        <tr>
                            <td>Umur/ Jenis Kelamin</td>
                            <td>:</td>
                            <td>{{ $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik . ' Tahun / ' . ($item_pasien->gender_pasien == 'L' ? 'Laki-laki' : 'Perempuan') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>:</td>
                            <td>{{ $item_pasien->alamat_pasien }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td style="text-align: justify;">
                    Yang tujuan, sifat dan perlunya tindakan medis tersebut di atas, serta resiko yang dapat
                    ditimbulkannya dan upaya mengatasinya telah cukup dijelaskan oleh tenaga medis dan telah
                    saya mengerti sepenuhnya.
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    Demikian persetujuan ini saya buat dengan penuh kesadaran dan tanpa paksaan.
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="50%">&nbsp;</td>
                            <td width="50%" class="text-center">
                                Boyolali,
                                {{ Carbon\Carbon::parse($item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->isoFormat('DD MMMM YYYY') }}
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                Petugas,
                            </td>
                            <td class="text-center">
                                Yang Membuat Pernyataan,
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                ({{ Smt\Masterweb\Models\VerificationActivitySample::where('is_klinik', $item_permohonan_uji_klinik->id_permohonan_uji_klinik)->where('id_verification_activity', 1)->first()->nama_petugas }})
                            </td>
                            <td class="text-center">
                                @if ($item_permohonan_uji_klinik->nama_perwakilan_permohonan_uji_klinik)
                                    ({{ $item_permohonan_uji_klinik->nama_perwakilan_permohonan_uji_klinik }})
                                @else
                                    ({{ $item_pasien->nama_pasien }})
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br><br>
    <br><br><br><br><br>

    <div style="padding-top: 20px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated.png') }}" width="100%">
                </td>
            </tr>
        </table>

        <table style="margin: 10px 0px 10px 0px;" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td
                    style="
          font-size: 13px;
          font-weight: bold;
          text-align: center;
        ">
                    <u>FORMULIR PERMINTAAN PEMERIKSAAN KLINIK</u>
                </td>
            </tr>
        </table>

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="40%" class="border">
                    <table width="100%" cellspacing="2" cellpadding="0">
                        <tr>
                            <td width="22%">
                                No. Lab
                            </td>
                            <td width="3%">:</td>
                            <td>
                                {{ $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik }}
                            </td>
                        </tr>
                        <tr>
                            <td>
                                Diagnosa
                            </td>
                            <td>:</td>
                            <td>
                                {{ $item_permohonan_uji_klinik->diagnosa_permohonan_uji_klinik }}
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                DOKTER PENGIRIM
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td></td>
                            <td>
                                ( {{ $item_permohonan_uji_klinik->nama_dokter_pengirim_permohonan_uji_klinik }} )
                            </td>
                            {{-- <td>
                (&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
              </td> --}}
                        </tr>

                        <tr>
                            <td>
                                No. HP
                            </td>
                            <td>:</td>
                            <td>
                                {{ $item_permohonan_uji_klinik->hp_dokter_pengirim_permohonan_uji_klinik }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="60%">
                    <table width="100%" cellspacing="2" cellpadding="0">
                        <tr>
                            <td width="27%" style="padding-left: 10px;">
                                Nama Lengkap
                                <br>(Sesuai KTP)
                            </td>
                            <td width="3%">:<br>&nbsp;</td>
                            <td class="border" style="padding-left: 2px;">
                                @php
                                    $nama_lengkap = $item_pasien->nama_pasien;
                                @endphp
                                {{ $nama_lengkap }}
                                @if (strlen($nama_lengkap) < 44)
                                    <br>&nbsp;
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Tanggal Lahir
                            </td>
                            <td>:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ Carbon\Carbon::parse($item_pasien->tgllahir_pasien)->isoFormat('DD MMMM YYYY') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Jenis Kelamin
                            </td>
                            <td>:</td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        @if ($item_pasien->gender_pasien == 'L')
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" checked />
                                            </td>
                                            <td width="40%">Laki-laki</td>
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Perempuan</td>
                                        @else
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Laki-laki</td>
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" checked />
                                            </td>
                                            <td width="40%">Perempuan</td>
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                No. Tep
                            </td>
                            <td>:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ $item_pasien->phone_pasien }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Alamat
                                <br>&nbsp;
                                <br>&nbsp;
                            </td>
                            <td>:<br>&nbsp;<br>&nbsp;</td>
                            <td class="border" style="padding-left: 2px;">
                                @php
                                    $alamat = $item_pasien->alamat_pasien;
                                @endphp
                                {{ $alamat }}
                                @for ($i = 1; $i < 3; $i++)
                                    @if (strlen($alamat) < $i * 44)
                                        <br>&nbsp;
                                    @endif
                                @endfor
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <br>
        <br>

        {{-- @foreach ($parameter_jenis_klinik as $val)
    <table width="100%" cellspacing="10" cellpadding="0">
      <tr>
          <td width="25%" class="blue-header">{{ $val->name_parameter_jenis_klinik }}</td>
      </tr>
        @foreach ($val->pakets as $paket)
          <tr>
            <td>
              <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                  <td width="30px">
                    <input type="checkbox" class="checkbox" />
                  </td>
                  <td  width="25%">{{ $paket->name_parameter_paket_klinik }}</td>
                </tr>
              </table>
            </td>
          </tr>
        @endforeach
    </table>
    @endforeach --}}
        {{-- <table width="100%" cellspacing="10" cellpadding="0">
      @php
          $totalJenis = count($parameter_jenis_klinik);
          $currentIndex = 0; // Untuk melacak indeks saat ini
      @endphp

      @while ($currentIndex < $totalJenis)
          <tr>
              @for ($i = 0; $i < 3; $i++) <!-- Ubah dari 3 ke 2 kolom -->
                  @if ($currentIndex < $totalJenis)
                      @php
                          $val = $parameter_jenis_klinik[$currentIndex];
                          $currentIndex++;
                      @endphp

                      <td width="50%" valign="top"> <!-- Ubah width dari 33% menjadi 50% -->
                          <table width="100%" cellspacing="0" cellpadding="0">
                              <tr>
                                  <td colspan="12" class="blue-header" style="height: 20px; font-size: 10px;"> <!-- Tinggi dan ukuran font -->
                                      {{ $val->name_parameter_jenis_klinik }}
                                  </td>
                              </tr>
                              @if ($val->pakets->isNotEmpty())
                                  @foreach ($val->pakets as $paket)
                                      @php
                                          $isChecked = $parameter_paket_klinik->where('id_jenis_parameter_klinik', $val->id_parameter_jenis_klinik)
                                              ->contains('id_parameter_paket_klinik', $paket->id_parameter_paket_klinik);
                                      @endphp
                                      <tr>
                                          <td width="20px" style="height: 25px">
                                              <input type="checkbox" class="checkbox" {{ $isChecked ? 'checked' : '' }} />
                                          </td>
                                          <td>{{ $paket->name_parameter_paket_klinik }}</td>
                                      </tr>
                                  @endforeach
                              @else
                                  <tr>
                                      <td colspan="2" style="height: 30px;">&nbsp;</td> <!-- Baris kosong untuk menyeimbangkan -->
                                  </tr>
                              @endif
                          </table>
                      </td>
                  @else
                      <td width="50%"></td> <!-- Kosong untuk menjaga struktur 2 kolom -->
                  @endif
              @endfor
          </tr>
      @endwhile
  </table> --}}




        <table width="100%" cellspacing="10" cellpadding="0">
            <tr>
                <td class="blue-header">HEMATOLOGI</td>
                <td width="25%" class="blue-header">IMUNOSEROLOGI</td>
                <td width="25%" class="blue-header">URINALISA</td>
            </tr>

            @php
                $is_checked_hemo = false;
                $is_checked_widal = false;
                $is_checked_urin_rutin = false;
                $is_checked_diferensial_count = false;
                $is_checked_hbsag = false;
                $is_checked_sedimen = false;
                $is_checked_led = false;
                $is_checked_dengue_ns1 = false;
                $is_checked_test_kehamilan = false;
                $is_checked_goldar = false;
                $is_checked_igg = false;
                $is_checked_mikro_alb = false;
                $is_checked_igm = false;
                $is_checked_narkoba = false;
                $is_checked_guldar_puasa = false;

                $is_checked_sgot = false;
                $is_checked_guldar_pp = false;
                $is_checked_sgpt = false;
                $is_checked_guldar_sewaktu = false;

                $is_checked_hba1c = false;

                $is_checked_koltot = false;
                $is_checked_ureum = false;
                $is_checked_kolhdl = false;
                $is_checked_kreatin = false;
                $is_checked_ldl = false;
                $is_checked_asam_urat = false;
                $is_checked_trigliserida = false;

                $is_checked_sputum_bta = false;
                foreach ($item_permohonan_uji_klinik->permohonanujipaketklinik as $key => $value) {
                    if (
                        $value->parameterpaketklinik->name_parameter_paket_klinik ==
                        'Hematologi Rutin (Hb, Ht, Leko, AT, Index Eri)'
                    ) {
                        # code...\
                        $is_checked_hemo = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Urinalisis Urin Rutin') {
                        # code...\
                        $is_checked_urin_rutin = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Widal') {
                        # code...\
                        $is_checked_widal = true;
                    }
                    if (
                        $value->parameterpaketklinik->name_parameter_paket_klinik ==
                        'Differensial Count (Hitung Jenis Lekosit)'
                    ) {
                        # code...\
                        $is_checked_diferensial_count = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'HBsAg Strip') {
                        # code...\
                        $is_checked_hbsag = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Sedimen') {
                        # code...\
                        $is_checked_sedimen = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Laju Endap Darah (LED)') {
                        # code...\
                        $is_checked_led = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Dengue NS1') {
                        # code...\
                        $is_checked_dengue_ns1 = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Test Kehamilan') {
                        # code...\
                        $is_checked_test_kehamilan = true;
                    }
                    if (
                        $value->parameterpaketklinik->name_parameter_paket_klinik == 'Golongan Darah A, B, O dan Rhesus'
                    ) {
                        # code...\
                        $is_checked_goldar = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'IgG Anti Dengue') {
                        # code...\
                        $is_checked_igg = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Mikro Albumin') {
                        # code...\
                        $is_checked_mikro_alb = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'IgM Anti Dengue') {
                        # code...\
                        $is_checked_igm = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Narkoba') {
                        # code...\
                        $is_checked_narkoba = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Gula Darah Puasa') {
                        # code...\
                        $is_checked_guldar_puasa = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'SGOT (AST)') {
                        # code...\
                        $is_checked_sgot = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Gula Darah 2 Jam PP') {
                        # code...\
                        $is_checked_guldar_pp = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'SGPT (ALT)') {
                        # code...\
                        $is_checked_sgpt = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Gula Darah Sewaktu') {
                        # code...\
                        $is_checked_guldar_sewaktu = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'HbA1C') {
                        # code...\
                        $is_checked_hba1c = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Kolesterol Total') {
                        # code...\
                        $is_checked_koltot = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Ureum') {
                        # code...\
                        $is_checked_ureum = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'HDL Kolesterol') {
                        # code...\
                        $is_checked_kolhdl = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Kreatin') {
                        # code...\
                        $is_checked_kreatin = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'LDL Kolesterol') {
                        # code...\
                        $is_checked_ldl = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Asam Urat') {
                        # code...\
                        $is_checked_asam_urat = true;
                    }

                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Trigliserida') {
                        # code...\
                        $is_checked_trigliserida = true;
                    }
                    if ($value->parameterpaketklinik->name_parameter_paket_klinik == 'Sputum BTA') {
                        # code...\
                        $is_checked_sputum_bta = true;
                    }

                    # code...
                }
            @endphp

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_hemo ? 'checked' : '' }} />
                            </td>
                            <td>Hematologi Rutin (Hb, Ht, Leko, AT, Index Eri)</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_widal ? 'checked' : '' }} />
                            </td>
                            <td>Widal</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_urin_rutin ? 'checked' : '' }} />
                            </td>
                            <td>Urine Rutin</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_diferensial_count ? 'checked' : '' }} />
                            </td>
                            <td>Differensial Count (Hitung Jenis Lekosit)</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_hbsag ? 'checked' : '' }} />
                            </td>
                            <td>HBsAg Strip</td>
                        </tr>
                    </table>
                </td>
                {{--                <td> --}}
                {{--                    <table width="100%" cellspacing="0" cellpadding="0"> --}}
                {{--                        <tr> --}}
                {{--                            <td width="30px"> --}}
                {{--                                <input type="checkbox" class="checkbox" {{ $is_checked_sedimen ? 'checked' : '' }} /> --}}
                {{--                            </td> --}}
                {{--                            <td>Sedimen</td> --}}
                {{--                        </tr> --}}
                {{--                    </table> --}}
                {{--                </td> --}}
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_test_kehamilan ? 'checked' : '' }} />
                            </td>
                            <td>Test Kehamilan</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_led ? 'checked' : '' }} />
                            </td>
                            <td>Laju Endap Darah (LED)</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_dengue_ns1 ? 'checked' : '' }} />
                            </td>
                            <td>Dengue NS1</td>
                        </tr>
                    </table>
                </td>
                {{--                <td> --}}
                {{--                    <table width="100%" cellspacing="0" cellpadding="0"> --}}
                {{--                        <tr> --}}
                {{--                            <td width="30px"> --}}
                {{--                                <input type="checkbox" class="checkbox" --}}
                {{--                                    {{ $is_checked_test_kehamilan ? 'checked' : '' }} /> --}}
                {{--                            </td> --}}
                {{--                            <td>Test Kehamilan</td> --}}
                {{--                        </tr> --}}
                {{--                    </table> --}}
                {{--                </td> --}}
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_mikro_alb ? 'checked' : '' }} />
                            </td>
                            <td>Mikro Albumin</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_goldar ? 'checked' : '' }} />
                            </td>
                            <td>Golongan Darah A, B, O dan Rhesus</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_igg ? 'checked' : '' }} />
                            </td>
                            <td>IgG Anti Dengue</td>
                        </tr>
                    </table>
                </td>
                {{--                <td> --}}
                {{--                    <table width="100%" cellspacing="0" cellpadding="0"> --}}
                {{--                        <tr> --}}
                {{--                            <td width="30px"> --}}
                {{--                                <input type="checkbox" class="checkbox" --}}
                {{--                                    {{ $is_checked_mikro_alb ? 'checked' : '' }} /> --}}
                {{--                            </td> --}}
                {{--                            <td>Mikro Albumin</td> --}}
                {{--                        </tr> --}}
                {{--                    </table> --}}
                {{--                </td> --}}
            </tr>
            <tr>
                <td>

                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_igm ? 'checked' : '' }} />
                            </td>
                            <td>IgM Anti Dengue</td>
                        </tr>
                    </table>
                </td>
                <td>

                </td>
            </tr>
        </table>

        <br>

        <table width="100%" cellspacing="10" cellpadding="0">
            <tr>
                <td class="blue-header">KIMIA KLINIK</td>
                <td width="25%" class="blue-header">LAIN-LAIN</td>
                <td width="25%" class="blue-header">MIKROBIOLOGI</td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <b>DIABETES</b>
                            </td>
                            <td>
                                <b>FAAL HATI</b>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" {{ $is_checked_narkoba ? 'checked' : '' }} />
                            </td>
                            <td>Narkoba</td>
                        </tr>
                    </table>
                </td>
                <td>

                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ $is_checked_sputum_bta ? 'checked' : '' }} />
                            </td>
                            <td>Sputum BTA</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox"
                                                class="checkbox"{{ $is_checked_guldar_puasa ? 'checked' : '' }} />
                                        </td>
                                        <td>Gula Darah Puasa *</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_sgot ? 'checked' : '' }} />
                                        </td>
                                        <td>SGOT (AST)</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" />
                            </td>
                            <td>........................</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_guldar_pp ? 'checked' : '' }} />
                                        </td>
                                        <td>Gula Darah 2 Jam PP</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_sgpt ? 'checked' : '' }} />
                                        </td>
                                        <td>SGPT (ALT)</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" />
                            </td>
                            <td>........................</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_guldar_sewaktu ? 'checked' : '' }} />
                                        </td>
                                        <td>Gula Darah Sewaktu</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox" />
                            </td>
                            <td>........................</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>


                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_hba1c ? 'checked' : '' }} />
                                        </td>
                                        <td>HbA1C</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td class="blue-header">SPESIMEN</td>
                <td style="padding-left: 4px;">

                </td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <b>LEMAK</b>
                            </td>
                            <td>
                                <b>GINJAL</b>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ str_contains($item_permohonan_uji_klinik->jenis_spesimen, 'Darah Beku') ? 'checked' : '' }} />
                            </td>
                            <td>Darah Beku</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_koltot ? 'checked' : '' }} />
                                        </td>
                                        <td>Kolesterol Total</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_ureum ? 'checked' : '' }} />
                                        </td>
                                        <td>Ureum</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ str_contains($item_permohonan_uji_klinik->jenis_spesimen, 'NaF') ? 'checked' : '' }} />
                            </td>
                            <td>NaF</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_kolhdl ? 'checked' : '' }} />
                                        </td>
                                        <td>HDL Kolesterol</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_kreatin ? 'checked' : '' }} />
                                        </td>
                                        <td>Kreatin</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ str_contains($item_permohonan_uji_klinik->jenis_spesimen, 'EDTA') ? 'checked' : '' }} />
                            </td>
                            <td>EDTA</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_ldl ? 'checked' : '' }} />
                                        </td>
                                        <td>LDL Kolesterol</td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_asam_urat ? 'checked' : '' }} />
                                        </td>
                                        <td>Asam Urat *</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="30px">
                                <input type="checkbox" class="checkbox"
                                    {{ str_contains($item_permohonan_uji_klinik->jenis_spesimen, 'Urine') ? 'checked' : '' }} />
                            </td>
                            <td>Urine</td>
                        </tr>
                    </table>
                </td>
                <td></td>
            </tr>

            <tr>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="60%">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox"
                                                {{ $is_checked_trigliserida ? 'checked' : '' }} />
                                        </td>
                                        <td>Trigliserida</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="26px">
                                <b>(*)</b>
                            </td>
                            <td>
                                <b>Puasa 8-12 Jam</b>
                            </td>
                        </tr>
                    </table>
                </td>
                <td>


                </td>
            </tr>
        </table>

        <br>
        <br>
        <br>

        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td width="30%">
                    <table width="100%" cellspacing="2" cellpadding="0">
                        <tr>
                            <td class="border">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="text-align: center">Catatan</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="
                      padding-left: 2px;
                      padding-right: 2px;
                      padding-top: 5px;
                      font-size: 9px;
                    ">
                                            @php
                                                $catatan = $item_permohonan_uji_klinik->catatan_permohonan_uji_klinik;
                                            @endphp
                                            {{ $catatan }}
                                            @for ($i = 1; $i < 6; $i++)
                                                @if (strlen($catatan) < $i * 37)
                                                    <br>&nbsp;
                                                @endif
                                            @endfor
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td class="border">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="text-align: center">Riwayat Penggunaan Obat</td>
                                    </tr>
                                    <tr>
                                        <td
                                            style="
                      padding-left: 2px;
                      padding-right: 2px;
                      padding-top: 5px;
                      font-size: 9px;
                    ">
                                            @php
                                                $riwayat_obat =
                                                    $item_permohonan_uji_klinik->riwayat_obat_permohonan_uji_klinik;
                                            @endphp
                                            {{ $riwayat_obat }}
                                            @for ($i = 1; $i < 5; $i++)
                                                @if (strlen($riwayat_obat) < $i * 37)
                                                    <br>&nbsp;
                                                @endif
                                            @endfor
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width="70%">
                    <table width="100%" cellspacing="2" cellpadding="0">
                        <tr>
                            <td width="32%" style="padding-left: 10px;">
                                Tanggal Sampling
                            </td>
                            <td width="3%">:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ Carbon\Carbon::parse($item_permohonan_uji_klinik->tgl_sampling_permohonan_uji_klinik)->isoFormat('DD MMMM YYYY') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Jam Sampling
                            </td>
                            <td>:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ Carbon\Carbon::parse($item_permohonan_uji_klinik->jam_sampling_permohonan_uji_klinik)->format('H:i') }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Plebotomist
                            </td>
                            <td>:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ $item_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Vena Lengan Kanan / Kiri
                            </td>
                            <td>:</td>
                            <td>
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        @if ($item_permohonan_uji_klinik->vena_permohonan_uji_klinik == 'R')
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" checked />
                                            </td>
                                            <td width="40%">Kanan</td>
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Kiri</td>
                                        @elseif ($item_permohonan_uji_klinik->vena_permohonan_uji_klinik == 'L')
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Kanan</td>
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" checked />
                                            </td>
                                            <td width="40%">Kiri</td>
                                        @else
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Kanan</td>
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox" />
                                            </td>
                                            <td width="40%">Kiri</td>
                                        @endif
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Lokasi Lain
                            </td>
                            <td>:</td>
                            <td class="border" style="padding-left: 2px;">
                                {{ $item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Paraf
                                <br>&nbsp;
                            </td>
                            <td>:<br>&nbsp;</td>
                            <td class="border" style="padding-left: 2px;">
                                <br>&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-left: 10px;">
                                Penanganan Hasil
                                <br>&nbsp;
                            </td>
                            <td>:<br>&nbsp;</td>
                            <td>
                                <table width="100%" cellspacing="2" cellpadding="0">
                                    {{-- <tr>
                    @if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == 'Diambil Pasien')
                      <td width="10%">
                        <input type="checkbox" class="checkbox" checked />
                      </td>
                      <td width="40%">Diambil Pasien</td>
                    @else
                      <td width="10%">
                        <input type="checkbox" class="checkbox" />
                      </td>
                      <td width="40%">Diambil Pasien</td>
                    @endif
                    @if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == 'Dikirim ke Pasien')
                      <td width="10%">
                        <input type="checkbox" class="checkbox" checked />
                      </td>
                      <td width="40%">Dikirim ke Pasien</td>
                    @else
                      <td width="10%">
                        <input type="checkbox" class="checkbox" />
                      </td>
                      <td width="40%">Dikirim ke Pasien</td>
                    @endif
                  </tr>
                  <tr>
                    @if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == 'Diambil Dokter')
                      <td width="10%">
                        <input type="checkbox" class="checkbox" checked />
                      </td>
                      <td width="40%">Diambil Dokter</td>
                    @else
                      <td width="10%">
                        <input type="checkbox" class="checkbox" />
                      </td>
                      <td width="40%">Diambil Dokter</td>
                    @endif
                    @if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == 'Via Internet')
                      <td width="10%">
                        <input type="checkbox" class="checkbox" checked />
                      </td>
                      <td width="40%">Via Internet</td>
                    @else
                      <td width="10%">
                        <input type="checkbox" class="checkbox" />
                      </td>
                      <td width="40%">Via Internet</td>
                    @endif
                  </tr> --}}
                                    @php
                                        $options = [
                                            'Diambil Pasien',
                                            'Dikirim ke Pasien',
                                            'Diambil Dokter',
                                            'Via Internet',
                                        ];

                                        // Bagi menjadi dua bagian
                                        $firstRowOptions = array_slice($options, 0, 2); // Ambil dua opsi pertama
                                        $secondRowOptions = array_slice($options, 2, 2); // Ambil dua opsi berikutnya
                                    @endphp

                                    <tr>
                                        @foreach ($firstRowOptions as $option)
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox"
                                                    {{ $item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == $option ? 'checked' : '' }} />
                                            </td>
                                            <td width="40%">{{ $option }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($secondRowOptions as $option)
                                            <td width="10%">
                                                <input type="checkbox" class="checkbox"
                                                    {{ $item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik == $option ? 'checked' : '' }} />
                                            </td>
                                            <td width="40%">{{ $option }}</td>
                                        @endforeach
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>


    </div>
    {{-- <div style="padding-top: 20px;">
        <table style="margin-top: 70px;" class="table-consent" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="5">
                    <table class="table-clear" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <img src="{{ public_path('assets/admin/images/logo/kop_perusahaan.png') }}"
                                    width="640px">
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Hari/ Tanggal :
                </td>
                <td colspan="3">
                    No. Reg :
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    Jenis kegiatan Lab PK
                </td>
                <td width="16%" class="text-center">
                    Jam Mulai
                </td>
                <td width="16%" class="text-center">
                    Jam Selesai
                </td>
                <td width="18%" class="text-center">
                    Nama Petugas
                </td>
                <td width="17%" class="text-center">
                    TTD
                </td>
            </tr>
            <tr>
                <td>
                    Pendaftaran/Registrasi
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Pengambilan sampel
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Pemeriksaan/Analitik
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Input/Output Hasil Px
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Verifikasi
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    Validasi
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div> --}}
</body>

</html>
