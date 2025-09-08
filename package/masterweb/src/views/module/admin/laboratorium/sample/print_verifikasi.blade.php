<html lang="">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>LHU.ELIT-2002006-AP </title>
    <link rel="shortcut icon" href="">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- CSS only -->
    <!-- Bootstrap CSS -->

    <style>
        .starter-template {
            text-align: center;
        }


        table>tr>td {
            cell-padding: 5px !important;
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

        .result th {
            border: 1px solid black;
            text-align: center;
        }

        @page {
            size: 794px 1248px;
            margin: 20px 20px 20px 20px;
        }

        body {
            font-size: 12px;
        }

        .page_break {
            page-break-before: always;
        }
    </style>
</head>

<body style="margin: 10px; padding: 0;">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td>
        <img src="{{ public_path('assets/admin/images/logo/kop_perusahaan.png') }}" width="730px">
      </td>
    </tr>
  </table>
  <table class="table table-bordered border border-dark">
    <tr>
      <td class="p-2" colspan="2"><b>Hari/Tanggal :</b> {{ \Smt\Masterweb\Helpers\DateHelper::formatDateIndo($datePrintVerification) }} </td>
      <td></td>
      <td class="p-2" colspan="2"><b>No.Reg :</b> {{ $sample->codesample_samples }}</td>
    </tr>
    <thead>
    <tr class="text-center">
      <th scope="col" class="border border-dark p-2" style="font-size: 12px;">Jenis Kegiatan Lab Kesmas</th>
      <th scope="col" class="border border-dark p-2" style="font-size: 12px;">Tanggal Mulai / Jam</th>
      <th scope="col" class="border border-dark p-2" style="font-size: 12px;">Tanggal Selesai / Jam</th>
      <th scope="col" class="border border-dark p-2" style="font-size: 12px;">Nama Petugas</th>
      <th scope="col" class="text-center border border-dark p-2" style="font-size: 12px;">TTD</th>
    </tr>
    </thead>
    <tbody class="border border-dark">
    <tr class="border border-dark">
      <th scope="row" class="w-25 p-2" style="text-align: left;">Pendaftaran / Registrasi</th>
      @if(isset($listVerifications[1]))
        <td class="border border-dark p-2">{{ $listVerifications[1]->start_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[1]->stop_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[1]->nama_petugas }}</td>
        @php
          $petugas = $listVerifications[1]->nama_petugas;
          $nip = "";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <td></td>
        @else
          <td class="border border-dark p-2">@include("masterweb::module.admin.laboratorium.template.TTD_BSRE_VERIF")</td>
        @endif
      @else
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
      @endif
    </tr>
    <tr class="border border-dark">
      <th scope="row" class="p-2" style="text-align: left;">Pemeriksaan / Analitik</th>
      @if(isset($listVerifications[2]))
        <td class="border border-dark p-2">{{ $listVerifications[2]->start_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[2]->stop_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[2]->nama_petugas }}</td>
        @php
          $petugas = $listVerifications[2]->nama_petugas;
          $nip = "";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <td></td>
        @else
          <td class="border border-dark p-2">@include("masterweb::module.admin.laboratorium.template.TTD_BSRE_VERIF")</td>
        @endif
      @else
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
      @endif
    </tr>
    <tr class="border border-dark">
      <th scope="row" class="p-2" style="text-align: left;">Input / Output Hasil Px</th>
      @if(isset($listVerifications[3]))
        <td class="border border-dark p-2">{{ $listVerifications[3]->start_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[3]->stop_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[3]->nama_petugas }}</td>
        @php
          $petugas = $listVerifications[3]->nama_petugas;
          $nip = "";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <td></td>
        @else
          <td class="border border-dark p-2">@include("masterweb::module.admin.laboratorium.template.TTD_BSRE_VERIF")</td>
        @endif
      @else
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
      @endif
    </tr>
    <tr class="border border-dark">
      <th scope="row" class="p-2" style="text-align: left;">Verifikasi</th>
      @if(isset($listVerifications[4]))
        <td class="border border-dark p-2">{{ $listVerifications[4]->start_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[4]->stop_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[4]->nama_petugas }}</td>
        @php
          $petugas = $listVerifications[4]->nama_petugas;
          $nip = "";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <td></td>
        @else
          <td class="border border-dark p-2">@include("masterweb::module.admin.laboratorium.template.TTD_BSRE_VERIF")</td>
        @endif
      @else
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
      @endif
    </tr>
    <tr class="border border-dark">
      <th scope="row" class="p-2" style="text-align: left;">Validasi</th>
      @if(isset($listVerifications[5]))
        <td class="border border-dark p-2">{{ $listVerifications[5]->start_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[5]->stop_date }}</td>
        <td class="border border-dark p-2">{{ $listVerifications[5]->nama_petugas }}</td>
        @php
          $petugas = $listVerifications[5]->nama_petugas;
          $nip = "";
        @endphp
        @if(isset($signOption) and $signOption == 0)
          <td></td>
        @else
          <td class="border border-dark p-2">@include("masterweb::module.admin.laboratorium.template.TTD_BSRE_VERIF")</td>
        @endif
      @else
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
        <td class="border border-dark"></td>
      @endif
    </tr>
    </tbody>
  </table>
  @if(isset($qrBase64))
    <img src="data:image/png;base64, {{ $qrBase64 }}" alt="QR Code" width="80" height="80">
  @endif
</body>

</html>
