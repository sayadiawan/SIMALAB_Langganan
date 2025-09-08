<html lang="">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>AirMinum-{!! $no_LHU !!}</title>
  <link rel="shortcut icon" href="">
  <link rel="stylesheet" href="dist/css/bootstrap.min.css">


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
      margin: 5px 30px;
    }

    body {
      font-size: 12px;
    }

    .page_break {
      page-break-before: always;
    }

    .table-container {
      flex: 2;
      margin-right: 10px;
    }
    .table-container table {
      width: 50%;
      border-collapse: collapse;
      font-size: 16px;
    }
    .information-table table {
      width: 100%;
      border-collapse: collapse;
    }
    .information-table td {
      vertical-align: top;
    }
    .information-table td:nth-child(1) {
      width: 200px;
      font-weight: bold;
    }
    .information-table td:nth-child(2) {
      width: 10px;
      text-align: center;
    }

    .tembusan ol {
      padding-left: 18px;
    }
  </style>
</head>

<body style="margin:50px 10px 50px 10px; padding: 0; font-size: 10pt">
<table width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td>
      <img src="{{ public_path('assets/admin/images/logo/kop_boyolali_updated.png') }}"
      width="100%">
    </td>
  </tr>
</table>

<div class="row batas" style="float: right; justify-content: center; width: 250px;">
  <div class="">
    <div class="justify-content-end" style="text-align: center;">
      KEPADA<br>
      Yth.

      @php
      $customer = str_replace(
          // Hanya mencari simbol 'Π'
          'π',
          '<span style="font-family: \'DejaVu Sans\', sans-serif;">π</span>', // Ganti dengan <span> yang sesuai
          $sample->permohonanuji->customer->name_customer,
      );

      $customer = str_replace(
          '&pi',
          '<span style="font-family: \'DejaVu Sans\', sans-serif;">π</span>', // Ganti dengan <span> yang sesuai
          $sample->permohonanuji->customer->name_customer,
      );

      // str_replace("
      // dd($customer);

  @endphp
  {!! $customer !!}


      <br>
      @if (isset($sample->address_customer) && $sample->address_customer !== '')
        @php
          $addresArray = explode(',', $sample->address_customer);
          $kabupaten = $addresArray[count($addresArray) - 1];

          unset($addresArray[count($addresArray) - 1]);

          $address = implode(',', $addresArray);

        @endphp
        d.a {{ $address }}<br>
        <div style="display: inline-block; vertical-align: top;">
          Di_ <span
            style="display: inline-block; padding-top: 24px;"><strong><u>{{ $kabupaten }}</u></strong></span>
        </div>
      @else
        Di <strong>{{ $sample->kecamatan_customer }}</strong>
      @endif
    </div>
  </div>
</div>
<div class="table-container information-table" >
  <table style="font-size: 10pt">
    <tr>
      <td>No. Agenda</td>
      <td>:</td>
      <td>{!! $no_LHU !!}</td>
    </tr>
    <tr>
      <td>No. Code Reg</td>
      <td>:</td>
      <td>{{ $sample->codesample_samples }}</td>
    </tr>
    <tr>
      <td>Perihal</td>
      <td>:</td>
      <td>
        @php
          $hasKimiaOrganik = $laboratoriummethods->contains(function ($laboratoriummethod) {
              return $laboratoriummethod->jenis_parameter_kimia === 'kimia organik' && $laboratoriummethod->hasil !== '-';
          });

          $hasKimiawi = $laboratoriummethods->contains(function ($laboratoriummethod) {
              return $laboratoriummethod->jenis_parameter_kimia === 'kimiawi' && $laboratoriummethod->hasil !== '-';
          });
        @endphp

        @if ($hasKimiaOrganik || $hasKimiawi)
          Hasil Pemeriksaan Secara Kimia
        @else
          Hasil Pemeriksaan Secara Fisika
        @endif
      </td>
    </tr>

    <tr>
      <td>Asal Contoh Air</td>
      <td>:</td>
      <td>
          @if ( $sample->is_pudam == 1)
            @if (isset($sample->location_samples))
                @php
                    $location = str_replace("\n", '<br>', $sample->location_samples);
                    $location = str_replace('<div id="simple-translate" class="simple-translate-system-theme">&nbsp;</div>', '', $location);
                    $location = str_replace('<p>', '', $location);
                    $location = str_replace('</p>', '', $location);

                @endphp


                {!! $location !!}
            @else
                {{ $sample->name_customer_pdam }}
            @endif

          @else
              @php
                  $location = str_replace("\n", '<br>', $sample->location_samples);

                  $location = str_replace('<p>', '', $location);
                  $location = str_replace('</p>', '', $location);

              @endphp


              {!! $location !!}
          @endif

      </td>
    </tr>
    <tr>
      <td>Tanggal diambil</td>
      <td>:</td>
      <td>{{ isset($sample->datesampling_samples) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->datesampling_samples)->isoFormat('D MMMM Y') : '-' }}</td>
    </tr>
    <tr>
      <td>Tanggal diterima</td>
      <td>:</td>
      <td>{{ isset($sample->date_sending) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->isoFormat('D MMMM Y'): '-' }}</td>
    </tr>
    <tr>
      <td>No. Lab</td>
      <td>:</td>
      <td>
        {{ isset($lab_num->lab_number) ? sprintf("%04d",(int)$lab_num->lab_number):""}}
      </td>
    </tr>
    <tr>
      <td>Bahan</td>
      <td>:</td>
      <td>{{ $sample->name_sample_type }}</td>
    </tr>
  </table>
</div>

<table width="100%" cellspacing="0" cellpadding="0" border="1" style="margin-top: 10px">
  <thead>
  <tr>
    <th width="5%" style="text-align: center">NO</th>
    <th width="10%" style="text-align: center">PARAMETER</th>
    <th width="5%" style="text-align: center">SATUAN</th>
    <th width="35%" style="text-align: center">KADAR MAKSIMUM <br>YANG DIPERBOLEHKAN</th>
    <th width="25%" style="text-align: center">METODE</th>
    <th width="20%">HASIL PEMERIKSAAN</th>
  </tr>
  </thead>

  @php
  // dd($fisikaCount);

  $fisikaCount = 0;
  $kimiaOrganikCount = 0;
  $kimiaCount = 0;
  foreach ($laboratoriummethods as $laboratoriummethod) {
      if (
          $laboratoriummethod->jenis_parameter_kimia == 'fisika' &&
          $laboratoriummethod->is_tambahan != 1 &&
          (isset($laboratoriummethod->metode))
      ) {
          $fisikaCount++;
      } elseif (
          $laboratoriummethod->jenis_parameter_kimia == 'kimiawi' &&
          (isset($laboratoriummethod->metode))
      ) {
          $kimiaCount++;
      } elseif (
          $laboratoriummethod->jenis_parameter_kimia == 'kimia organik' &&
          (isset($laboratoriummethod->metode))
      ) {
          $kimiaOrganikCount++;
      }
  }
@endphp
  <tbody>
  @if($fisikaCount == 0 && $kimiaOrganikCount == 0 && $kimiaCount == 0)
    <tr>
      <td colspan="6" style="text-align: center; padding: 4px"><b>Belum melakukan input hasil</b></td>
    </tr>
  @endif
  @if (count($laboratoriummethods) > 0)
    @if($sample->only_fisika)
      @if($fisikaCount > 0)
        <tr>
          <th style="text-align: center">A.</th>
          <th style="text-align: left; padding-left: 2px;" colspan="5">FISIKA</th>
        </tr>
      @endif

      {{-- foreach data --}}
      @php
        $no_a = 1;
      @endphp
      @foreach ($laboratoriummethods as $laboratoriummethod)
        @if ($laboratoriummethod->jenis_parameter_kimia == 'fisika' && $laboratoriummethod->is_tambahan != 1  && (isset($laboratoriummethod->metode)))
            @php
              $hasil = cek_hasil_color(
                  isset($laboratoriummethod->hasil)
                      ? $laboratoriummethod->hasil
                      : (isset($laboratoriummethod->equal)
                          ? $laboratoriummethod->equal
                          : ''),
                  $laboratoriummethod->min,
                  $laboratoriummethod->max,
                  $laboratoriummethod->equal,
                  'result_output_method_' . $laboratoriummethod->method_id,
                  $laboratoriummethod->offset_baku_mutu,
              );

              $unit = $laboratoriummethod->shortname_unit;

              $unitAll = $laboratoriummethod->shortname_unit;

              if (isset($unit)) {
                  $unit = '';
                  if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {
                      $unit = $laboratoriummethod->shortname_unit;
                  }
              } else {
                  $unit = '';
              }
            @endphp

            @if ($hasil != '-')

            <tr>
              <td width="5%" style="text-align: center">{{ $no_a }}</td>
              <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>

              <td width='15%' style="text-align: center">{!! $unitAll !!}</td>
              <td width="20%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
              @if($laboratoriummethod->is_ready == 1)
                <td width="20%" style="text-align: left; padding-left: 2px;">{!! isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method !!}</td>
                <td width="20%" style="text-align: center">{!! $hasil !!}</td>
              @else
                <td colspan="2" style="text-align: center">Alat Dan Reagen tidak tersedia</td>
              @endif
            </tr>
            @endif
          . {{ $laboratoriummethod->params_method }}

          @php
            $no_a++;
          @endphp
        @endif
      @endforeach
    @else
      @if($fisikaCount > 0)
        <tr>
          <th style="text-align: center">A.</th>
          <th style="text-align: left; padding-left: 2px;" colspan="5">FISIKA</th>
        </tr>
      @endif

      {{-- foreach data --}}
      @php
        $no_a = 1;
      @endphp
      @foreach ($laboratoriummethods as $laboratoriummethod)
        @if ($laboratoriummethod->jenis_parameter_kimia == 'fisika' && $laboratoriummethod->is_tambahan != 1  && (isset($laboratoriummethod->metode)))
            @php
              $hasil = cek_hasil_color(
                  isset($laboratoriummethod->hasil)
                      ? $laboratoriummethod->hasil
                      : (isset($laboratoriummethod->equal)
                          ? $laboratoriummethod->equal
                          : ''),
                  $laboratoriummethod->min,
                  $laboratoriummethod->max,
                  $laboratoriummethod->equal,
                  'result_output_method_' . $laboratoriummethod->method_id,
                  $laboratoriummethod->offset_baku_mutu,
              );

              $unit = $laboratoriummethod->shortname_unit;

              $unitAll = $laboratoriummethod->shortname_unit;

              if (isset($unit)) {
                  $unit = '';
                  if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {
                      $unit = $laboratoriummethod->shortname_unit;
                  }
              } else {
                  $unit = '';
              }
            @endphp
            @if ($hasil != '-')

            <tr>
              <td width="5%" style="text-align: center">{{ $no_a }}</td>
              <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>

              <td width='15%' style="text-align: center">{!! $unitAll !!}</td>
              <td width="20%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
              @if($laboratoriummethod->is_ready == 1)
                <td width="20%" style="text-align: left; padding-left: 2px;">{!! isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method !!}</td>
                <td width="20%" style="text-align: center">{!! $hasil !!}</td>
              @else
                <td colspan="2" style="text-align: center">Alat Dan Reagen tidak tersedia</td>
              @endif
            </tr>
            @endif
          . {{ $laboratoriummethod->params_method }}

          @php
            $no_a++;
          @endphp
        @endif
      @endforeach

      @if ($hasKimiawi || $hasKimiaOrganik)
          <tr>
              @if ($fisikaCount > 0)
                  <th style="text-align: center">B.</th>
              @else
                  <th style="text-align: center">A.</th>
              @endif
              <th style="text-align: left; padding-left: 2px;" colspan="5">KIMIA</th>
          </tr>
      @endif
      @if ($kimiaCount > 0 and $kimiaOrganikCount > 0)
          <tr>
              <th></th>
              <th style="text-align: left; padding-left: 2px;" colspan="5">a. KIMIA AN - ORGANIK
              </th>
          </tr>
      @endif

      {{-- foreach B --}}
      @php
        $no_b = 0;
      @endphp
      @foreach ($laboratoriummethods as $laboratoriummethod)
        @if (
            $laboratoriummethod->jenis_parameter_kimia == 'kimiawi' && $laboratoriummethod->is_tambahan != 1 && (isset($laboratoriummethod->metode)))

            @php
              $hasil = cek_hasil_color(
                  isset($laboratoriummethod->hasil)
                      ? $laboratoriummethod->hasil
                      : (isset($laboratoriummethod->equal)
                          ? $laboratoriummethod->equal
                          : ''),
                  $laboratoriummethod->min,
                  $laboratoriummethod->max,
                  $laboratoriummethod->equal,
                  'result_output_method_' . $laboratoriummethod->method_id,
                  $laboratoriummethod->offset_baku_mutu,
              );

              $unit = $laboratoriummethod->shortname_unit;

              $unitAll = $laboratoriummethod->shortname_unit;

              if (isset($unit)) {
                  $unit = '';
                  if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {
                      $unit = $laboratoriummethod->shortname_unit;
                  }
              } else {
                  $unit = '';
              }
            @endphp

            @if ($hasil != '-')

            <tr>
              <td width="5%" style="text-align: center">{{ $no_b + 1 }}</td>
              <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>

              <td width='15%' style="text-align: center">{!! $unitAll !!}</td>
              <td width="20%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
              @if($laboratoriummethod->is_ready == 1)
                <td width="20%" style="text-align: left; padding-left: 2px;">{!! isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method !!}</td>
                <td width="20%" style="text-align: center">{!! $hasil !!}</td>
              @else
                <td colspan="2" style="text-align: center">Alat Dan Reagen tidak tersedia</td>
              @endif
            </tr>
            @endif
          . {{ $laboratoriummethod->params_method }}

          @php
            $no_b++;
          @endphp
        @endif
      @endforeach

      @if ($hasKimiaOrganik)
          @if ($kimiaOrganikCount > 0 and $kimiaCount > 0)
              <tr>
                  <th></th>
                  @if ($kimiaCount > 0)
                      <th style="text-align: left; padding-left: 2px;" colspan="5">b. KIMIA ORGANIK
                      </th>
                  @else
                      <th style="text-align: left; padding-left: 2px;" colspan="5">a. KIMIA ORGANIK
                      </th>
                  @endif
              </tr>
          @endif
      @endif


      {{-- foreach B --}}
      @php
        $no_c = 0;



      @endphp
      @foreach ($laboratoriummethods as $laboratoriummethod)

        @if (
            $laboratoriummethod->jenis_parameter_kimia == 'kimia organik' && $laboratoriummethod->is_tambahan != 1  && (isset($laboratoriummethod->metode)))
            @php
              $hasil = cek_hasil_color(
                  isset($laboratoriummethod->hasil)
                      ? $laboratoriummethod->hasil
                      : (isset($laboratoriummethod->equal)
                          ? $laboratoriummethod->equal
                          : ''),
                  $laboratoriummethod->min,
                  $laboratoriummethod->max,
                  $laboratoriummethod->equal,
                  'result_output_method_' . $laboratoriummethod->method_id,
                  $laboratoriummethod->offset_baku_mutu,
              );

              $unit = $laboratoriummethod->shortname_unit;

              $unitAll = $laboratoriummethod->shortname_unit;

              if (isset($unit)) {
                  $unit = '';
                  if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {
                      $unit = $laboratoriummethod->shortname_unit;
                  }
              } else {
                  $unit = '';
              }
            @endphp
          @if ($hasil != '-')

          <tr>
            <td width="5%" style="text-align: center">{{ $no_c + 1 }}</td>
            <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>

            <td width='15%' style="text-align: center">{!! $unitAll !!}</td>
            <td width="20%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
            @if($laboratoriummethod->is_ready == 1)
              <td width="20%" style="text-align: left; padding-left: 2px;">{!! isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method !!}</td>
              <td width="20%" style="text-align: center">{!! $hasil !!}</td>
            @else
              <td colspan="2" style="text-align: center">Alat Dan Reagen tidak tersedia</td>
            @endif
          </tr>
          @endif
          . {{ $laboratoriummethod->params_method }}

          @php
            $no_c++;
          @endphp
        @endif
      @endforeach



      @if ($laboratoriummethods_plus_count > 0)
        @if($fisikaCount > 0 && $kimiaOrganikCount > 0 && $kimiaCount > 0)
          <tr>
            <th style="text-align: center"></th>
            <th style="text-align: left" colspan="5">Parameter Tambahan</th>
          </tr>
        @endif

        {{-- foreach D --}}
        @php
          $no_tambahan = 1;
        @endphp
        @foreach ($laboratoriummethods as $laboratoriummethod)
          @if ($laboratoriummethod->is_tambahan == 1  && (isset($laboratoriummethod->metode)))
            <tr>
              <td width="5%" style="text-align: center">{{ $no_tambahan }}</td>
              <td width="20%" style="text-align: left">{!! $laboratoriummethod->name_report !!}</td>

              @php
                $hasil = cek_hasil_color(
                    isset($laboratoriummethod->hasil)
                        ? $laboratoriummethod->hasil
                        : (isset($laboratoriummethod->equal)
                            ? $laboratoriummethod->equal
                            : ''),
                    $laboratoriummethod->min,
                    $laboratoriummethod->max,
                    $laboratoriummethod->equal,
                    'result_output_method_' . $laboratoriummethod->method_id,
                    $laboratoriummethod->offset_baku_mutu,
                );

                $unit = $laboratoriummethod->shortname_unit;
                $unitAll = $laboratoriummethod->shortname_unit;
                if (isset($unit)) {
                    $unit = '';
                    if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {
                        $unit = $laboratoriummethod->shortname_unit;
                    }
                } else {
                    $unit = '';
                }
              @endphp
              <td width='15%' style="text-align: center">{!! $unitAll !!}</td>
              <td width="20%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>
              @if($laboratoriummethod->is_ready == 1)
                <td width="20%" style="text-align: left; padding-left: 2px;">{!! isset($laboratoriummethod->metode) ? $laboratoriummethod->metode : $laboratoriummethod->name_method !!}</td>
                <td width="20%" style="text-align: center">{!! $hasil !!}</td>
              @else
                <td colspan="2" style="text-align: center">Alat Dan Reagen tidak tersedia</td>
              @endif
            </tr>
            . {{ $laboratoriummethod->params_method }}

            @php
              $no_tambahan++;
            @endphp
          @endif
        @endforeach
      @endif
    @endif
  @endif
  </tbody>
</table>
<br>
<strong class="text-keterangan">KETERANGAN</strong> <br>
<span>Air Minum mengacu Permenkes No. 2 Tahun 2023 tentang Pelaksanaan Peraturan Pemerintah Nomor 66 tentang Kesehatan Lingkungan</span>

<div class="row batas" style="float: right; justify-content: center; margin-top: 20px;">
  <div class="col-md-12">
    <div class="justify-content-end" style="text-align: center;">
      @php
        $nullDate = ".................."
      @endphp
      Boyolali, {{ isset($validation) ? \Smt\Masterweb\Helpers\DateHelper::formatDateIndo($validation->stop_date) : $nullDate }} <br>
      Kepala Laboratorium Kesehatan<br>
      Kabupaten Boyolali<br>
      @if(isset($validation))
        <br>
        <br>
        @php
          $petugas = "dr. Muharyati";
          $nip = "NIP. 19721106 200212 2 001";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <br>
          <br>
          <strong><u>dr. Muharyati</u></strong><br>
          Pembina<br>
          NIP. 19721106 200212 2 001
        @else
          @include("masterweb::module.admin.laboratorium.template.TTD_BSRE")
        @endif
      @else
        <br>
        <br>
        <br>
        <br>
        <strong><u>dr. Muharyati</u></strong><br>
        Pembina<br>
        NIP. 19721106 200212 2 001
      @endif
    </div>
  </div>
</div>
<div>
  <div class="tembusan">
    <p><u>Tembusan dikirim Kepada Yth:</u></p>
    {!! $tembusans !!}
  </div>
  <table>
    <p>Catatan :</p>

    @php

      $uniqueLaboratoriummethods = $laboratoriummethodsUnits->unique(function ($item) {
          return $item->shortname_unit . ':' . $item->name_unit;
      });
      $unitDictionary = ['TCU' => 'True Color Unit', 'NTU' => 'Nephelometric Turbidity Unit'];
    @endphp
    @foreach ($uniqueLaboratoriummethods as $laboratoriummethod)
      @if($laboratoriummethod->shortname_unit != '-')
        <tr>
          <td>{!! $laboratoriummethod->shortname_unit !!}</td>
          <td>:</td>
          <td>{!! $laboratoriummethod->name_unit == 'TCU' || $laboratoriummethod->name_unit == 'NTU'
                      ? $unitDictionary[$laboratoriummethod->name_unit]
                      : $laboratoriummethod->name_unit !!}
          </td>
        </tr>
      @endif
    @endforeach
    @if((isset($no_b) and $no_b > 0) or (isset($no_c) and $no_c > 0))
      <tr>
        <td>SR</td>
        <td>:</td>
        <td>Sambungan Rumah</td>
      </tr>
    @endif
  </table>
</div>
</body>

</html>


{{--<html lang="">--}}

{{--<head>--}}
{{--    <meta charset="UTF-8">--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
{{--    <meta name="description" content="">--}}
{{--    <meta name="author" content="">--}}
{{--    <title>AirMinum-{!! $no_LHU !!}</title>--}}
{{--    <link rel="shortcut icon" href="">--}}
{{--    <link rel="stylesheet" href="dist/css/bootstrap.min.css">--}}
{{--    <style>--}}
{{--        .starter-template {--}}
{{--            text-align: center;--}}
{{--        }--}}


{{--        table>tr>td {--}}
{{--            /* cell-padding: 5px !important; */--}}
{{--        }--}}

{{--        @media print {--}}
{{--            #cetak {--}}
{{--                display: none;--}}
{{--            }--}}
{{--        }--}}

{{--        .garis {--}}
{{--            border: 1px solid--}}
{{--        }--}}

{{--        .table2 {--}}
{{--            font-size: 5px;--}}
{{--            text-align: center--}}
{{--        }--}}

{{--        .result {--}}
{{--            border-collapse: collapse;--}}
{{--        }--}}

{{--        .result td {--}}
{{--            border: 1px solid black;--}}
{{--            text-align: center;--}}
{{--        }--}}

{{--        @page {--}}
{{--            size: 794px 1248px;--}}
{{--            margin: 20px 20px 20px 50px;--}}
{{--        }--}}

{{--        body {--}}
{{--            font-size: 12px;--}}
{{--        }--}}

{{--        .page_break {--}}
{{--            page-break-before: always;--}}
{{--        }--}}

{{--        .table-container {--}}
{{--          flex: 2;--}}
{{--          margin-right: 10px;--}}
{{--        }--}}
{{--        .table-container table {--}}
{{--          width: 60%;--}}
{{--          border-collapse: collapse;--}}
{{--          font-size: 16px;--}}
{{--        }--}}
{{--        .information-table table {--}}
{{--          width: 100%;--}}
{{--          border-collapse: collapse;--}}
{{--        }--}}
{{--        .information-table td {--}}
{{--          vertical-align: top;--}}
{{--        }--}}
{{--        .information-table td:nth-child(1) {--}}
{{--          width: 200px;--}}
{{--          font-weight: bold;--}}
{{--        }--}}
{{--        .information-table td:nth-child(2) {--}}
{{--          width: 10px;--}}
{{--          text-align: center;--}}
{{--        }--}}
{{--    </style>--}}
{{--</head>--}}

{{--<body style="margin: 10px; padding: 0;">--}}
{{--      <table width="100%" cellspacing="0" cellpadding="0">--}}
{{--        <tr>--}}
{{--          <td>--}}
{{--            <img src="{{ public_path('assets/admin/images/logo/kop_perusahaan.png') }}" width="730px">--}}
{{--          </td>--}}
{{--        </tr>--}}
{{--      </table>--}}

{{--      <div class="row batas" style="float: right; justify-content: center;">--}}
{{--        <div class="">--}}
{{--          <div class="justify-content-end" style="text-align: center;">--}}
{{--            KEPADA<br>--}}
{{--            Yth. {{ $sample->name_customer }}<br>--}}
{{--            <br>--}}
{{--            d.a--}}
{{--            <br>--}}
{{--            Di&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--}}
{{--            <br>--}}
{{--            <strong>{{ $sample->kecamatan_customer }}</strong>--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div class="table-container">--}}
{{--        <table>--}}
{{--          <tr>--}}
{{--            <td>No. Agenda</td>--}}
{{--            <td>:</td>--}}
{{--            <td>{!! $no_LHU !!}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>No. Code Reg</td>--}}
{{--            <td>:</td>--}}
{{--            <td>{{ $sample->codesample_samples }}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>Perihal</td>--}}
{{--            <td>:</td>--}}
{{--            <td>Hasil Pemeriksaan {{ $laboratorium->nama_laboratorium }}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>Asal Contoh Air</td>--}}
{{--            <td>:</td>--}}
{{--            <td class="wrap-text">{{ $sample->location_samples }}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>Tanggal diambil</td>--}}
{{--            <td>:</td>--}}
{{--            <td>{{ isset($sample->datesampling_samples) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->datesampling_samples)->isoFormat('D MMMM Y') : '-' }}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>Tanggal diterima</td>--}}
{{--            <td>:</td>--}}
{{--            <td>{{ isset($sample->date_sending) ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->isoFormat('D MMMM Y'): '-' }}</td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>No. Lab</td>--}}
{{--            <td>:</td>--}}
{{--            <td>--}}
{{--              {{ isset($lab_num->lab_number) ? sprintf("%04d",(int)$lab_num->lab_number):""}}--}}
{{--            </td>--}}
{{--          </tr>--}}
{{--          <tr>--}}
{{--            <td>Bahan</td>--}}
{{--            <td>:</td>--}}
{{--            <td>{{ $sample->name_sample_type }}</td>--}}
{{--          </tr>--}}
{{--        </table>--}}
{{--      </div>--}}

{{--    <table width="100%" cellspacing="0" cellpadding="0" border="1" style="margin-top: 10px">--}}
{{--        <thead>--}}
{{--            <tr>--}}
{{--                <th width="5%" style="text-align: center">NO</th>--}}
{{--                <th width="30%" style="text-align: left">PARAMETER</th>--}}
{{--                <th width="15%" style="text-align: center">Satuan</th>--}}
{{--                <th width="25%" style="text-align: center">Kadar Maksimum Yang diperbolehkan </th>--}}
{{--                <th width="20%" style="text-align: center">KETERANGAN</th>--}}
{{--                <th width="25%" style="text-align: center">Hasil</th>--}}
{{--            </tr>--}}
{{--        </thead>--}}

{{--        <tbody>--}}
{{--            @if (count($laboratoriummethods) > 0)--}}
{{--                <tr>--}}
{{--                    <th style="text-align: center">I.</th>--}}
{{--                    <th style="text-align: left; padding-left: 2px;" colspan="4">Parameter yang berhubungan langsung dengan kesehatan--}}
{{--                    </th>--}}
{{--                </tr>--}}

{{--                <tr>--}}
{{--                    <th style="text-align: center">A.</th>--}}
{{--                    <th style="text-align: left; padding-left: 2px;" colspan="4">Kimia an organik</th>--}}
{{--                </tr>--}}

{{--                --}}{{-- foreach data --}}
{{--                @php--}}
{{--                    $no_a = 1;--}}
{{--                @endphp--}}
{{--                @foreach ($laboratoriummethods as $laboratoriummethod)--}}
{{--                    @if (--}}
{{--                        $laboratoriummethod->berhubungan_kesehatan == 1 &&--}}
{{--                            $laboratoriummethod->is_tambahan == 0 &&--}}
{{--                            $laboratoriummethod->jenis_parameter_kimia == 'kimia organik')--}}
{{--                        <tr>--}}
{{--                            <td width="5%" style="text-align: center">{{ $no_a }}</td>--}}
{{--                            <td width="30%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>--}}

{{--                            @php--}}
{{--                                $hasil = cek_hasil_color(--}}
{{--                                    isset($laboratoriummethod->hasil)--}}
{{--                                        ? $laboratoriummethod->hasil--}}
{{--                                        : (isset($laboratoriummethod->equal)--}}
{{--                                            ? $laboratoriummethod->equal--}}
{{--                                            : ''),--}}
{{--                                    $laboratoriummethod->min,--}}
{{--                                    $laboratoriummethod->max,--}}
{{--                                    $laboratoriummethod->equal,--}}
{{--                                    'result_output_method_' . $laboratoriummethod->method_id,--}}
{{--                                    $laboratoriummethod->offset_baku_mutu,--}}
{{--                                );--}}

{{--                                $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                $unitAll = $laboratoriummethod->shortname_unit;--}}
{{--                                if (isset($unit)) {--}}
{{--                                    $unit = '';--}}
{{--                                    if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {--}}
{{--                                        $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                    }--}}
{{--                                } else {--}}
{{--                                    $unit = '';--}}
{{--                                }--}}
{{--                            @endphp--}}
{{--                            <td width='15%' style="text-align: center">{!! $unitAll !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>--}}
{{--                            <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->keterangan !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $hasil !!}</td>--}}
{{--                        </tr>--}}
{{--                        . {{ $laboratoriummethod->params_method }}--}}

{{--                        @php--}}
{{--                            $no_a++;--}}
{{--                        @endphp--}}
{{--                    @endif--}}
{{--                @endforeach--}}

{{--                <tr>--}}
{{--                    <th style="text-align: center">II.</th>--}}
{{--                    <th style="text-align: left" colspan="4">Parameter yang tidak langsung berhubungan dengan--}}
{{--                        kesehatan</th>--}}
{{--                </tr>--}}

{{--                <tr>--}}
{{--                    <th style="text-align: center">B.</th>--}}
{{--                    <th style="text-align: left; padding-left: 2px;" colspan="4">Parameter Fisik</th>--}}
{{--                </tr>--}}

{{--                --}}{{-- foreach D --}}
{{--                @php--}}
{{--                    $no_d = 1;--}}
{{--                @endphp--}}
{{--                @foreach ($laboratoriummethods as $laboratoriummethod)--}}
{{--                    @if (--}}
{{--                        $laboratoriummethod->berhubungan_kesehatan == 0 &&--}}
{{--                            $laboratoriummethod->is_tambahan == 0 &&--}}
{{--                            $laboratoriummethod->jenis_parameter_kimia == 'fisika')--}}
{{--                        <tr>--}}
{{--                            <td width="5%" style="text-align: center">{{ $no_d }}</td>--}}
{{--                            <td width="30%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>--}}

{{--                            @php--}}
{{--                                $hasil = cek_hasil_color(--}}
{{--                                    isset($laboratoriummethod->hasil)--}}
{{--                                        ? $laboratoriummethod->hasil--}}
{{--                                        : (isset($laboratoriummethod->equal)--}}
{{--                                            ? $laboratoriummethod->equal--}}
{{--                                            : ''),--}}
{{--                                    $laboratoriummethod->min,--}}
{{--                                    $laboratoriummethod->max,--}}
{{--                                    $laboratoriummethod->equal,--}}
{{--                                    'result_output_method_' . $laboratoriummethod->method_id,--}}
{{--                                    $laboratoriummethod->offset_baku_mutu,--}}
{{--                                );--}}

{{--                                $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                $unitAll = $laboratoriummethod->shortname_unit;--}}
{{--                                if (isset($unit)) {--}}
{{--                                    $unit = '';--}}
{{--                                    if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {--}}
{{--                                        $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                    }--}}
{{--                                } else {--}}
{{--                                    $unit = '';--}}
{{--                                }--}}
{{--                            @endphp--}}
{{--                            <td width='15%' style="text-align: center">{!! $unitAll !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>--}}
{{--                            <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->keterangan !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $hasil !!}</td>--}}
{{--                        </tr>--}}
{{--                        . {{ $laboratoriummethod->params_method }}--}}

{{--                        @php--}}
{{--                            $no_d++;--}}
{{--                        @endphp--}}
{{--                    @endif--}}
{{--                @endforeach--}}

{{--                <tr>--}}
{{--                    <th style="text-align: center">C.</th>--}}
{{--                    <th style="text-align: left; padding-left: 2px;" colspan="4">Kimiawi</th>--}}
{{--                </tr>--}}

{{--                --}}{{-- foreach C --}}
{{--                @php--}}
{{--                    $no_c = 1;--}}
{{--                @endphp--}}
{{--                @foreach ($laboratoriummethods as $laboratoriummethod)--}}
{{--                    @if (--}}
{{--                        $laboratoriummethod->berhubungan_kesehatan == 0 &&--}}
{{--                            $laboratoriummethod->is_tambahan == 0 &&--}}
{{--                            $laboratoriummethod->jenis_parameter_kimia == 'kimiawi')--}}
{{--                        <tr>--}}
{{--                            <td width="5%" style="text-align: center">{{ $no_c }}</td>--}}
{{--                            <td width="30%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>--}}

{{--                            @php--}}
{{--                                $hasil = cek_hasil_color(--}}
{{--                                    isset($laboratoriummethod->hasil)--}}
{{--                                        ? $laboratoriummethod->hasil--}}
{{--                                        : (isset($laboratoriummethod->equal)--}}
{{--                                            ? $laboratoriummethod->equal--}}
{{--                                            : ''),--}}
{{--                                    $laboratoriummethod->min,--}}
{{--                                    $laboratoriummethod->max,--}}
{{--                                    $laboratoriummethod->equal,--}}
{{--                                    'result_output_method_' . $laboratoriummethod->method_id,--}}
{{--                                    $laboratoriummethod->offset_baku_mutu,--}}
{{--                                );--}}

{{--                                $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                $unitAll = $laboratoriummethod->shortname_unit;--}}
{{--                                if (isset($unit)) {--}}
{{--                                    $unit = '';--}}
{{--                                    if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {--}}
{{--                                        $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                    }--}}
{{--                                } else {--}}
{{--                                    $unit = '';--}}
{{--                                }--}}
{{--                            @endphp--}}
{{--                            <td width='15%' style="text-align: center">{!! $unitAll !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>--}}
{{--                            <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->keterangan !!}</td>--}}
{{--                            <td width="25%" style="text-align: center">{!! $hasil !!}</td>--}}
{{--                        </tr>--}}
{{--                        . {{ $laboratoriummethod->params_method }}--}}

{{--                        @php--}}
{{--                            $no_c++;--}}
{{--                        @endphp--}}
{{--                    @endif--}}
{{--                @endforeach--}}

{{--                @if ($laboratoriummethods_plus_count > 0)--}}
{{--                    <tr>--}}
{{--                        <th style="text-align: center">III.</th>--}}
{{--                        <th style="text-align: left; padding-left: 2px;" colspan="4">Parameter Tambahan</th>--}}
{{--                    </tr>--}}

{{--                    --}}{{-- foreach D --}}
{{--                    @php--}}
{{--                        $no_tambahan = 1;--}}
{{--                    @endphp--}}
{{--                    @foreach ($laboratoriummethods as $laboratoriummethod)--}}
{{--                        @if ($laboratoriummethod->is_tambahan == 1)--}}
{{--                            <tr>--}}
{{--                                <td width="5%" style="text-align: center">{{ $no_tambahan }}</td>--}}
{{--                                <td width="30%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->name_report !!}</td>--}}

{{--                                @php--}}
{{--                                    $hasil = cek_hasil_color(--}}
{{--                                        isset($laboratoriummethod->hasil)--}}
{{--                                            ? $laboratoriummethod->hasil--}}
{{--                                            : (isset($laboratoriummethod->equal)--}}
{{--                                                ? $laboratoriummethod->equal--}}
{{--                                                : ''),--}}
{{--                                        $laboratoriummethod->min,--}}
{{--                                        $laboratoriummethod->max,--}}
{{--                                        $laboratoriummethod->equal,--}}
{{--                                        'result_output_method_' . $laboratoriummethod->method_id,--}}
{{--                                        $laboratoriummethod->offset_baku_mutu,--}}
{{--                                    );--}}

{{--                                    $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                    $unitAll = $laboratoriummethod->shortname_unit;--}}
{{--                                    if (isset($unit)) {--}}
{{--                                        $unit = '';--}}
{{--                                        if (trim($laboratoriummethod->shortname_unit) != '-' && trim($hasil) != '-') {--}}
{{--                                            $unit = $laboratoriummethod->shortname_unit;--}}
{{--                                        }--}}
{{--                                    } else {--}}
{{--                                        $unit = '';--}}
{{--                                    }--}}
{{--                                @endphp--}}
{{--                                <td width='15%' style="text-align: center">{!! $unitAll !!}</td>--}}
{{--                                <td width="25%" style="text-align: center">{!! $laboratoriummethod->nilai_baku_mutu !!}</td>--}}
{{--                                <td width="20%" style="text-align: left; padding-left: 2px;">{!! $laboratoriummethod->keterangan !!}</td>--}}
{{--                                <td width="25%" style="text-align: center">{!! $hasil !!}</td>--}}
{{--                            </tr>--}}
{{--                            . {{ $laboratoriummethod->params_method }}--}}

{{--                            @php--}}
{{--                                $no_tambahan++;--}}
{{--                            @endphp--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                @endif--}}



{{--            @endif--}}
{{--        </tbody>--}}
{{--    </table>--}}
{{--      <strong class="text-keterangan">KETERANGAN</strong> <br>--}}
{{--      <span>{{ $sample->name_sample_type }} mengacu </span>--}}
{{--      @if (count($all_acuan_baku_mutu) > 0)--}}

{{--        @if (count($all_acuan_baku_mutu) > 1)--}}
{{--          @php--}}
{{--            $no = 1;--}}
{{--          @endphp--}}
{{--          @foreach ($all_acuan_baku_mutu as $acuan_baku_mutu)--}}
{{--            {{ $acuan_baku_mutu->title_library }}--}}

{{--            @php--}}
{{--              $no++;--}}
{{--            @endphp--}}
{{--          @endforeach--}}
{{--        @else--}}

{{--          {{ $all_acuan_baku_mutu[0]->title_library }}--}}

{{--        @endif--}}
{{--      @endif--}}

{{--      <div class="row batas" style="float: right; justify-content: center; margin-top: 20px;">--}}
{{--        <div class="col-md-12">--}}
{{--          <div class="justify-content-end" style="text-align: center;">--}}
{{--            @php--}}
{{--              $nullDate = ".................."--}}
{{--            @endphp--}}
{{--            Boyolali,{{ isset($pengesahan_hasil->pengesahan_hasil_date) ? \Smt\Masterweb\Helpers\DateHelper::formatDateIndo($pengesahan_hasil->pengesahan_hasil_date) : $nullDate }} <br>--}}
{{--            Kepala Laboratorium Kesehatan<br>--}}
{{--            Kabupaten Boyolali<br>--}}
{{--            @if (isset($verifikasi))--}}
{{--              --}}{{-- <img src="{{ asset('assets/admin/images/ttd.png') }}" width="100px" class="img-fluid">--}}
{{--  <div style="clear: right">--}}
{{--  </div> --}}
{{--              <br>--}}
{{--              <br>--}}
{{--              <br>--}}
{{--              <br>--}}
{{--            @else--}}
{{--              <br>--}}
{{--              <br>--}}
{{--              <br>--}}
{{--              <br>--}}
{{--            @endif--}}
{{--            <strong><u>dr. Muharyati</u></strong><br>--}}
{{--            Pembina<br>--}}
{{--            NIP. 19721106 200212 2 001--}}
{{--          </div>--}}
{{--        </div>--}}
{{--      </div>--}}
{{--      <div>--}}
{{--        <p><u>Tembusan dikirim Kepada Yth:</u></p>--}}
{{--        <ol>--}}
{{--          <li>Pertanggal</li>--}}
{{--        </ol>--}}
{{--        <p>Catatan :</p>--}}

{{--        <ul>--}}
{{--          @php--}}
{{--            $uniqueLaboratoriummethods = $laboratoriummethods->unique(function ($item) {--}}
{{--                return $item->shortname_unit . ':' . $item->name_unit;--}}
{{--            });--}}
{{--            $unitDictionary = array("TCU" => "True Color Unit", "NTU" =>"Nephelometric Turbidity Unit");--}}
{{--          @endphp--}}
{{--          @foreach($uniqueLaboratoriummethods as $laboratoriummethod)--}}

{{--            @if($laboratoriummethod->shortname_unit != '-')--}}
{{--              {!! $laboratoriummethod->shortname_unit  !!} :--}}
{{--              {!! ($laboratoriummethod->name_unit == "TCU" || $laboratoriummethod->name_unit == "NTU") ? $unitDictionary[$laboratoriummethod->name_unit] : $laboratoriummethod->name_unit !!} <br>--}}
{{--            @endif--}}
{{--          @endforeach--}}
{{--        </ul>--}}
{{--      </div>--}}

{{--</body>--}}

{{--</html>--}}
