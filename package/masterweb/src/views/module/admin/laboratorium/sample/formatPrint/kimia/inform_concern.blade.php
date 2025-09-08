<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Inform Concern</title>
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

        @font-face {
            font-family: "source_sans_proregular";
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            font-size: 11px;
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
            margin: 0px 30px;
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

        @font-face {
            font-family: 'DejaVu Sans', sans-serif !important;
            src: local("Source Sans Pro"), url("fonts/sourcesans/sourcesanspro-regular-webfont.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            font-size: 11px;
        }


        .text-center {
            text-align: center;
        }

        .td-header {
            font-family: "Times New Roman", Times, serif !important;
            font-weight: bold;
            text-align: center;
        }

        .table-syarat td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 4px 2px 4px 2px;
            font-size: 13px;
        }

        .table-clear td {
            border: 0px;
            padding: 0px;
        }
    </style>
</head>

<body>

    <div style="padding-top: 40px;">
        <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated.png') }}" width="100%">

        <div style="padding: 0px 40px 0px 40px;">
            <table style="margin: 5px 0px 5px 0px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td
                        style="
            font-size: 13px;
            font-weight: bold;
            text-align: center;
          ">
                        FOLMULIR PERMINTAAN PEMERIKSAAN
                    </td>
                </tr>
            </table>

            <table style="font-size: 13px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="33%">No. REGISTRASI</td>
                    <td width="2%">:</td>
                    <td>{{ $sample->codesample_samples }}</td>
                </tr>
                <tr>
                    <td width="33%" style="vertical-align: top;">NAMA</td>
                    <td width="2%" style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">
                        {{ $sample->name_pelanggan ?? $sample->permohonanuji->customer->name_customer }}
                    </td>
                </tr>
                <tr>
                    <td width="33%" style="vertical-align: top;">ALAMAT</td>
                    <td width="2%" style="vertical-align: top;">:</td>
                    <td style="vertical-align: top;">{{ $sample->address_customer }}</td>
                </tr>
                <tr>
                    <td width="33%">JENIS SAMPLE</td>
                    <td width="2%">:</td>
                    @php

                        if (str_contains($sample->codesample_samples, '- F')) {
                            # code...
                            $is_fisika = true;
                        } else {
                            $is_fisika = false;
                        }

                        $foodType = '';
                        if ($sample->nama_jenis_makanan != '') {
                            $foodType = '(' . $sample->nama_jenis_makanan . ')';
                        }

                    @endphp

                    @if ($is_fisika)
                        <td>{{ $sample->name_sample_type }} Fisika {{ $foodType }}</td>
                    @else
                        <td>{{ $sample->name_sample_type }} Kimia {{ $foodType }}</td>
                    @endif
                </tr>
                <tr>
                    <td width="33%">TANGGAL SAMPLING</td>
                    <td width="2%">:</td>
                    <td>{{ $sample->datesampling_samples }}</td>
                </tr>
                <tr>
                    <td width="33%">TANGGAL PENERIMAAN</td>
                    <td width="2%">:</td>
                    <td>{{ $sample->date_sending }}</td>
                </tr>
                <tr>
                    <td width="33%">NO. TELP</td>
                    <td width="2%">:</td>
                    <td>{{ $sample->cp_customer }}</td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px; background-color: black;">

        <div style="padding: 0px 30px 0px 30px;">
            <table style="margin: 5px 0px 10px 0px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td
                        style="
            font-size: 13px;
            font-weight: bold;
            text-align: center;
          ">
                        <u>PARAMETER PEMERIKSAAN KIMIA / FISIKA</u>
                    </td>
                </tr>
            </table>

            <table style="font-size: 13px;" width="100%" cellspacing="10" cellpadding="0">
                <tr>
                    <td width="30%">
                        <b>I. FISIKA AIR</b>
                    </td>
                    <td width="30%">
                        <b>II. KIMIA AIR</b>
                    </td>
                    <td>
                        <b>III. KIMIA MAKANAN / MINUMAN</b>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Bau'])) {{ $fisika['Bau'] }} @endif />
                                </td>
                                <td>Bau</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Besi (Fe)'])) {{ $kimia['Besi (Fe)'] }} @endif />
                                </td>
                                <td>Besi</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Borax'])) {{ $kimia['Borax'] }} @endif />
                                </td>
                                <td>Boraks</td>
                            </tr>
                        </table>
                    </td>
                </tr>


                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Zat padat terlarut<br>(Total Dissolved Solid)']) || isset($fisika['Total Zat Padat Terlarut (TDS)'])) {{ $fisika['Zat padat terlarut<br>(Total Dissolved Solid)'] ?? $fisika['Total Zat Padat Terlarut (TDS)'] }} @endif />
                                </td>
                                <td>TDS</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Fluorida'])) {{ $kimia['Fluorida'] }} @endif />
                                </td>
                                <td>Fluorida</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Formalin'])) {{ $kimia['Formalin'] }} @endif />
                                </td>
                                <td>Formalin</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Kekeruhan'])) {{ $fisika['Kekeruhan'] }} @endif />
                                </td>
                                <td>Kekeruhan</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Kesadahan']) || isset($kimia['Kesadahan (CaCO3)'])) {{ $kimia['Kesadahan'] ?? $kimia['Kesadahan (CaCO3)'] }} @endif />
                                </td>
                                <td>Kesadahan</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Benzoat'])) {{ $kimia['Benzoat'] }} @endif />
                                </td>
                                <td>Benzoat</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Rasa'])) {{ $fisika['Rasa'] }} @endif />
                                </td>
                                <td>Rasa</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Chlorida'])) {{ $kimia['Chlorida'] }} @endif />
                                </td>
                                <td>Chlorida</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Balisilat'])) {{ $kimia['Balisilat'] }} @endif />
                                </td>
                                <td>Salisilat</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Suhu'])) {{ $fisika['Suhu'] }} @endif />
                                </td>
                                <td>Suhu</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Mangan (Mn)'])) {{ $kimia['Mangan (Mn)'] }} @endif />
                                </td>
                                <td>Mangan</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Pewarna'])) {{ $kimia['Pewarna'] }} @endif />
                                </td>
                                <td>Pewarna</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($fisika['Warna'])) {{ $fisika['Warna'] }} @endif />
                                </td>
                                <td>Warna</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Nitrat'])) {{ $kimia['Nitrat'] }} @endif />
                                </td>
                                <td>Nitrat</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Siklamat'])) {{ $kimia['Siklamat'] }} @endif />
                                </td>
                                <td>Siklamat</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox" />
                                </td>
                                <td>........................</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Nitrit'])) {{ $kimia['Nitrit'] }} @endif />
                                </td>
                                <td>Nitrit</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 16px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Sakarin'])) {{ $kimia['Sakarin'] }} @endif />
                                </td>
                                <td>Sakarin</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding-left: 10px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox" />
                                </td>
                                <td>........................</td>
                            </tr>
                        </table>
                    </td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Sianida'])) {{ $kimia['Sianida'] }} @endif />
                                </td>
                                <td>Sianida</td>
                            </tr>
                        </table>
                    </td>
                    @php
                        $not_in_list_kimia = array_keys($not_in_list_kimia);
                    @endphp
                    @if (count($not_in_list_kimia) > 0 && str_contains($sample->name_sample_type, 'Makanan'))
                        <td style="padding-left: 13px;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="30px">
                                        <input type="checkbox" class="checkbox" checked />
                                    </td>
                                    <td>{{ $not_in_list_kimia[0] }}</td>
                                </tr>
                            </table>
                        </td>
                    @else
                        <td style="padding-left: 16px;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="30px">
                                        <input type="checkbox" class="checkbox" />
                                    </td>
                                    <td>........................</td>
                                </tr>
                            </table>
                        </td>
                    @endif

                </tr>

                <tr>
                    <td></td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['pH'])) {{ $kimia['pH'] }} @endif />
                                </td>
                                <td>PH</td>
                            </tr>
                        </table>
                    </td>
                    @if (count($not_in_list_kimia) > 1 && str_contains($sample->name_sample_type, 'Makanan'))
                        <td style="padding-left: 13px;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="30px">
                                        <input type="checkbox" class="checkbox" checked />
                                    </td>
                                    <td>{{ $not_in_list_kimia[1] }}</td>
                                </tr>
                            </table>
                        </td>
                    @else
                        <td style="padding-left: 16px;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="30px">
                                        <input type="checkbox" class="checkbox" />
                                    </td>
                                    <td>........................</td>
                                </tr>
                            </table>
                        </td>
                    @endif
                </tr>

                <tr>
                    <td></td>
                    <td style="padding-left: 13px;">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td width="30px">
                                    <input type="checkbox" class="checkbox"
                                        @if (isset($kimia['Zat Organik'])) {{ $kimia['Zat Organik'] }} @endif />
                                </td>
                                <td>Zat Organik</td>
                            </tr>
                        </table>
                    </td>
                    @if (count($not_in_list_kimia) > 2 && str_contains($sample->name_sample_type, 'Makanan'))
                        <td style="padding-left: 13px;">
                            <table width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="30px">
                                        <input type="checkbox" class="checkbox" checked />
                                    </td>
                                    <td>{{ $not_in_list_kimia[2] }}</td>
                                </tr>
                            </table>
                        </td>
                    @else
                        <td></td>
                    @endif
                </tr>

                @if (count($not_in_list_kimia) > 0 && str_contains($sample->name_sample_type, 'Air'))

                    @foreach ($not_in_list_kimia as $item)
                        <tr>
                            <td></td>
                            <td style="padding-left: 13px;">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox" checked />
                                        </td>
                                        <td>{{ $item }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                @endif

                @if (count($not_in_list_kimia) > 0 && str_contains($sample->name_sample_type, 'Makanan'))

                    @for ($i = 2; $i < count($not_in_list_kimia); $i++)
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="padding-left: 13px;">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="30px">
                                            <input type="checkbox" class="checkbox" checked />
                                        </td>
                                        <td>{{ $not_in_list_kimia[$i] }}</td>
                                    </tr>
                                </table>
                            </td>

                        </tr>
                    @endfor
                @endif


            </table>

            <table style="margin: 10px 0px 15px 0px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td
                        style="
            font-size: 15px;
            font-weight: bold;
            text-align: center;
          ">
                        <u>PERNYATAAN PERSETUJUAN</u>
                    </td>
                </tr>
            </table>

            <table style="font-size: 13px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="text-align: justify;">
                        Dengan ini menyatakan bahwa SETUJU/TIDAK SETUJU terhadap sampel yang telah diserahkan
                        berupa AIR MINUM / AIR BERSIH / MAKANAN / MINUMAN / FASILITAS SANITASI kepada
                        Laboratorium Kesehatan Kabupaten Boyolali, dengan :
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td></td>
                                <td width="20%" style="text-align: center;">
                                    Pengirim
                                    <br>
                                    <br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        Hasil pemeriksaan selama 6 hari kerja terhitung dari sampel diterima petugas
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td></td>
                                <td width="20%" style="text-align: center;">
                                    <hr style="border-bottom: 0.5px solid;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table style="margin: 10px 0px 15px 0px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td
                        style="
            font-size: 15px;
            font-weight: bold;
            text-align: center;
          ">
                        <u>SYARAT KELAYAKAN SAMPEL</u>
                    </td>
                </tr>
            </table>

            <table style="font-size: 13px;" width="100%" cellspacing="0" cellpadding="1">
                <tr>
                    <td>1.</td>
                    <td>Sampel Pemeriksaan Kimia Air</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Wadah botol plastik / botol kaca bening bersih</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Volume minimal 500 ml</td>
                </tr>
                <tr>
                    <td>2.</td>
                    <td>Sampel Makanan</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Kemasan dari plastik / Wadah yang bersih, Berat minimal 100 gr</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Sampel tersendiri untuk pemeriksaan secara kimia</td>
                </tr>
            </table>

            <table style="font-size: 13px;" width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="65%">
                        <table class="table-syarat" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td style="text-align: center;">Spefikasi</td>
                                <td width="30%" style="text-align: center;">Layak</td>
                                <td width="30%" style="text-align: center;">Tidak Layak</td>
                            </tr>
                            <tr>
                                <td>Tempat / Kemasan</td>
                                <td> <input type="checkbox" class="checkbox"
                                        @if ($penerimaan_sample['kelayakan_tempat_kemasan'] == 'layak') {{ 'checked' }} @endif /></td>
                                <td> <input type="checkbox" class="checkbox"
                                        @if ($penerimaan_sample['kelayakan_tempat_kemasan'] == 'tidak layak') {{ 'checked' }} @endif /></td>
                            </tr>
                            <tr>
                                <td>Berat / Vol</td>
                                <td> <input type="checkbox" class="checkbox"
                                        @if ($penerimaan_sample['kelayakan_berat_vol'] == 'layak') {{ 'checked' }} @endif /></td>
                                <td> <input type="checkbox" class="checkbox"
                                        @if ($penerimaan_sample['kelayakan_berat_vol'] == 'tidak layak') {{ 'checked' }} @endif /></td>
                            </tr>
                        </table>
                    </td>
                    <td></td>
                    <td width="20%">
                        <table width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    Penerima / Petugas
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <hr style="border-bottom: 0.5px solid;">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            {{--    <table style="margin: 0px 0px 9px 0px;" width="100%" cellspacing="0" cellpadding="0"> --}}
            {{--      <tr> --}}
            {{--        <td style="font-size: 13px;"> --}}
            {{--          Hasil pengujian hanya berlaku contoh sampel yang diuji --}}
            {{--        </td> --}}
            {{--      </tr> --}}
            {{--    </table> --}}

            {{--    <table class="table-syarat" width="100%" cellspacing="0" cellpadding="0"> --}}
            {{--      <tr> --}}
            {{--        <td width="20%">Nomor Register</td> --}}
            {{--        <td></td> --}}
            {{--      </tr> --}}
            {{--      <tr> --}}
            {{--        <td>Pengambilan Hasil</td> --}}
            {{--        <td></td> --}}
            {{--      </tr> --}}
            {{--    </table> --}}
            {{--  </div> --}}
            {{-- </div> --}}

            {{-- <div style="padding: 80px 40px 0px 40px;"> --}}
            {{--  <table class="table-syarat" width="100%" cellspacing="0" cellpadding="0"> --}}
            {{--    <tr> --}}
            {{--      <td colspan="5"> --}}
            {{--        <table class="table-clear" width="100%" cellspacing="0" cellpadding="0"> --}}
            {{--          <tr> --}}
            {{--            <td width="5%"></td> --}}
            {{--            <td width="25%" style="border: 1px solid black;" class="text-center">LOGO</td> --}}
            {{--            <td class="text-center"> --}}
            {{--              <table width="100%" cellspacing="0" cellpadding="0"> --}}
            {{--                <tr> --}}
            {{--                  <td style=" --}}
            {{--                      text-align: center; --}}
            {{--                      font-weight: bold; --}}
            {{--                      font-size: 13px; --}}
            {{--                    "> --}}
            {{--                    PEMERINTAH KABUPATEN BOYOLALI --}}
            {{--                  </td> --}}
            {{--                </tr> --}}
            {{--                <tr> --}}
            {{--                  <td style=" --}}
            {{--                      text-align: center; --}}
            {{--                      font-weight: bold; --}}
            {{--                      font-size: 13px; --}}
            {{--                    "> --}}
            {{--                    DINAS KESEHATAN --}}
            {{--                  </td> --}}
            {{--                </tr> --}}
            {{--                <tr> --}}
            {{--                  <td style=" --}}
            {{--                      text-align: center; --}}
            {{--                      font-weight: bold; --}}
            {{--                      font-size: 13px; --}}
            {{--                    "> --}}
            {{--                    LABORATORIUM KESEHATAN --}}
            {{--                  </td> --}}
            {{--                </tr> --}}
            {{--                <tr> --}}
            {{--                  <td style=" --}}
            {{--                      text-align: center; --}}
            {{--                      font-size: 12px; --}}
            {{--                    "> --}}
            {{--                    Komplek Perkantoran Terpadu Kabupaten Boyolali --}}
            {{--                  </td> --}}
            {{--                </tr> --}}
            {{--                <tr> --}}
            {{--                  <td style=" --}}
            {{--                      text-align: center; --}}
            {{--                      font-size: 12px; --}}
            {{--                    "> --}}
            {{--                    Jalan Ahmad Yani No. 1, Siswodipuran, Boyolali 57311 --}}
            {{--                  </td> --}}
            {{--                </tr> --}}
            {{--              </table> --}}
            {{--            </td> --}}
            {{--            <td width="5%"></td> --}}
            {{--          </tr> --}}
            {{--        </table> --}}
            {{--      </td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td colspan="2"> --}}
            {{--        Hari/ Tanggal : --}}
            {{--      </td> --}}
            {{--      <td colspan="3"> --}}
            {{--        No. Reg : --}}
            {{--      </td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td class="text-center"> --}}
            {{--        Jenis Kegiatan --}}
            {{--        <br>Lab Kesmas --}}
            {{--      </td> --}}
            {{--      <td width="18%" class="text-center"> --}}
            {{--        Tgl Mulai<br>/Jam --}}
            {{--      </td> --}}
            {{--      <td width="18%" class="text-center"> --}}
            {{--        Tgl Selesai<br>/Jam --}}
            {{--      </td> --}}
            {{--      <td width="18%" class="text-center"> --}}
            {{--        Nama<br>Petugas --}}
            {{--      </td> --}}
            {{--      <td width="17%" class="text-center"> --}}
            {{--        TTD --}}
            {{--      </td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Pengambilan sampel --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Pendaftaran/Registrasi --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Pemeriksaan/Analitik --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Input/Output Hasil Px --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Verifikasi --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--    <tr> --}}
            {{--      <td> --}}
            {{--        Validasi --}}
            {{--      </td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--      <td></td> --}}
            {{--    </tr> --}}
            {{--  </table> --}}
        </div>

</body>

</html>
