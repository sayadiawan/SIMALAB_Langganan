<div style="float: left; max-width: 50%">
    <table>
      <tr>
        <td style="width: 170px">
          Nomor Agenda
        </td>
        <td>
          :
        </td>
        <td>
          {!! $data['no_agenda'] !!}
        </td>
      </tr>
      <tr>
        <td>
          Nomor Register
        </td>
        <td>
          :
        </td>
        <td>
          <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap">
            {{ $data['no_register'] }}
          </ol>
        </td>
      </tr>
      <tr>
        <td>
          Nama Pelanggan
        </td>
        <td>
          :
        </td>
        <td>
          <b>

                      <span>
                        @php
                          $namaPelanggan = $data['nama_pelanggan'];
                          if (str_contains($namaPelanggan, 'π')){
                            $namaPelanggan = str_replace('π', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $namaPelanggan);
                          }

                          if (str_contains($namaPelanggan, '&pi;')){
                            $namaPelanggan = str_replace('&pi;', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $namaPelanggan);
                          }
                        @endphp
                        {!! $namaPelanggan !!}
                      </span>

          </b>
        </td>
      </tr>
      <tr>
        <td>
          Alamat Register
        </td>
        <td>
          :
        </td>
        <td style="width: 380px;">
          {{ $data['alamat_pelanggan'] }}
        </td>
      </tr>

      {{-- Jenis Sampel --}}
      <tr>
        <td>
          Jenis Sampel
        </td>
        <td>
          :
        </td>
        <td>
          <b>{{ $data['jenis_sampel'] }}</b>
        </td>
      </tr>
      <tr style="vertical-align: top">
        <td>
          Jenis Sarana
        </td>
        <td>
          :
        </td>
        <td>
          <strong>
            @php
              $jenisSarana = null;
              try {
                $jenisSaranas = array_unique($data['jenis_sarana']);
                if (count($jenisSaranas) > 1){
                  $last = array_pop($jenisSaranas);
                  $jenisSarana = implode(', ', $jenisSaranas) . ' dan ' . $last;
                }else{
                  $jenisSarana = implode(",", $jenisSaranas);
                }

               }catch (Exception $e){
                $jenisSarana = '-';
               }
            @endphp
            {{ $jenisSarana }}
          </strong>
        </td>
      </tr>

      <tr>
        <td colspan="3">
          <br>
        </td>
      </tr>
      <tr>
        <td>
          Metode Pemeriksaan
        </td>
        <td>
          :
        </td>
        <td>
          <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap; font-weight: bold">
            @foreach ($data['metode_pemeriksaan'] as $metode)
                <li>{{ $metode }}</li>
            @endforeach
          </ol>
        </td>
      </tr>
      <tr>
        <td>
          Hasil Pemeriksaan
        </td>
        <td>
          :
        </td>
        <td></td>
      </tr>
    </table>
  </div>

  <div style="float: right; max-width: 44%">
    <table style="margin-left: auto">
      <tr>
        <td colspan="3">
          <br>
        </td>
      </tr>
      <tr>
        <td style="width: 150px">Petugas Sampling</td>
        <td>
          :
        </td>
        <td>
          {{ $data['petugas_sampling'] }}
        </td>
      </tr>
      <tr>
        <td>Tanggal Sampling</td>
        <td>
          :
        </td>
        <td>
          @php
              $tanggalsampling = $data['tanggal_sampling'];
              $tanggalsampling = array_unique($tanggalsampling);
          @endphp
          @if (count($tanggalsampling) > 2)
            <ul style="list-style: none; margin: 0; padding: 0;">
              @foreach ($tanggalsampling as $tanggal)
                <li>
                  {{ \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal)->isoFormat('D MMMM Y') }}
                  @if (!$loop->last)
                    <span>,</span>
                  @endif
                </li>
              @endforeach
            </ul>
          @elseif(count($tanggalsampling) == 2)
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', head($tanggalsampling ?? []))->isoFormat('D MMMM Y') }}
            <span> & </span>
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', last($tanggalsampling ?? []))->isoFormat('D MMMM Y') }}
          @else
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', head($tanggalsampling ?? []))->isoFormat('D MMMM Y') }}
          @endif
        </td>
      </tr>
      <tr style="vertical-align: top">
        <td>Parameter</td>
        <td>
          :
        </td>
        <td>
          <ul style="list-style: none; margin: 0; padding: 0;">
            {!! implode("<br>", $data['parameter']) !!}
          </ul>
        </td>
      </tr>

      {{-- Metode Pemeriksaan --}}
      <tr>
        <td></td>
        <td></td>
        <td></td>
      </tr>

      {{-- Hasil Pemeriksaan --}}
      <tr>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </div>

  <div class="clearfix" style="clear: both"></div>
