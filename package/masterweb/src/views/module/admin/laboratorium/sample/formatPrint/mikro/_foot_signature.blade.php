<div style="max-width: 360px; text-align: center; margin-left: auto">
  <style>
    .tembusan ol {
      padding-left: 20px;
    }
  </style>

  Boyolali,
  @if(data_get($listVerifications, '5.stop_date') !== null)
    {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', data_get($listVerifications, '5.stop_date'))->isoFormat('D
    MMMM Y') }}<br>
  @else
    <br>
  @endif
  Kepala Laboratorium Kesehatan<br>
  Kabupaten Boyolali
  @if(isset($signOption) and $signOption == 0)
    <br>
  @endif
  @if(data_get($listVerifications, '5.stop_date') !== null)
    <br>
    @php
      $petugas = "dr. Muharyati";
      $nip = "NIP. 19721106 200212 2 001";
    @endphp
    @if(isset($signOption) and $signOption == 0)
      <br>
      <strong><u>dr. Muharyati </u></strong><br>
      Pembina <br>
      NIP. 19721106 200212 2 001
    @else
      @include("masterweb::module.admin.laboratorium.template.TTD_BSRE")
    @endif
  @else
    <br>
    <br>
    <strong><u>dr. Muharyati </u></strong><br>
    Pembina <br>
    NIP. 19721106 200212 2 001
  @endif
</div>
<br>

<div class="tembusan" style="line-height: 1.2; margin-top: -15px;">
  <div style="margin-bottom: -10px;"><strong><u> Tembusan dikirim Kepada Yth. : </u></strong></div>
  {!! $tembusan !!}
</div>
<table class="pemeriksa-validator" style="page-break-before: auto; width: 100%; line-height: 0.8; margin-top: -10px;">
  <tr>
    <td width="10%">
      Pemeriksa
    </td>
    <td width="1%"> :</td>
    <td>
      @if(data_get($listVerifications, '2.nama_petugas') !== null)
        {{ data_get($listVerifications, '2.nama_petugas') }}
      @endif
    </td>
    <td rowspan="3" class="justify-content-end">

    </td>
  </tr>
  <tr>
    <td>
      Verifikator
    </td>
    <td> :</td>
    <td>
      @if(data_get($listVerifications, '4.nama_petugas') !== null)
        {{ data_get($listVerifications, '4.nama_petugas') }}
      @endif
    </td>
  </tr>
  <tr>
    <td></td>
    <td></td>
    <td></td>
  </tr>
</table>

