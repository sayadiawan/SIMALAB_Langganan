<div style="float: left; max-width: 50%">
    <table>
      {{-- Nomor Agenda --}}
      <tr>
        <td style="width: 170px">
          Nomor Agenda
        </td>
        <td>
          :
        </td>
        <td>
          {!! $no_LHU !!}
        </td>
      </tr>

      {{-- Nomor Register --}}
      <tr>
        <td>
          Nomor Register
        </td>
        <td>
          :
        </td>
        <td>
          <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap">
            {{-- @foreach ($table as $mytable)
  <li> {{ data_get($mytable, 'sample_type.codesample_samples') }} </li>
  @endforeach --}}
            {{ $lab_string }}
          </ol>
        </td>
      </tr>

      {{-- Nama Pelanggan --}}
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
                          $namaPelanggan = $sample->permohonanuji->customer->name_customer;
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

      {{-- Alamat Register --}}
      <tr>
        <td>
          Alamat Register
        </td>
        <td>
          :
        </td>
        <td style="width: 380px;">
          @php
            $address = $sample->permohonanuji->customer->address_customer;
          @endphp
          {{ $address }}
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
          <b>
            @php
                $jenis_sample = [];
                foreach ($table as $tbl){
                  if ($tbl['sample_type']->jenis_sample_uji_usap != null){
                    $jenis_sample[] = $tbl['sample_type']->jenis_sample_uji_usap;
                  }else{
                    $jenis_sample[] = 'Alat Masak';
                  }
                }

                $jenis_sample = array_unique($jenis_sample);
                if (count($jenis_sample) == 0){
                  $jenis_sample[] = 'Alat Masak';
                }

                if (count($jenis_sample) > 1) {
                      $lasts = array_pop($jenis_sample);
                      $jenis_sample = implode(', ', $jenis_sample) . ' dan ' . $lasts;
                  } else {
                      $jenis_sample = $jenis_sample[0] ?? '';
                  }


            @endphp
            {{ $jenis_sample }}
          </b>
        </td>
      </tr>

      {{-- Jenis Sarana --}}
      <tr style="vertical-align: top">
        <td>
          Jenis Sarana
        </td>
        <td>
          :
        </td>
        <td>
          <strong>
            {{-- @php
  dd($all_samples);
  @endphp --}}

            {{-- @php
                dd($all_samples[0]['jenis_sarana_names']);
            @endphp --}}
            {{-- @php
              $jenisSarana = null;
              try {
                $jenisSarana = arrayToKomma($all_samples, 'jenis_sarana_names');
               }catch (Exception $e){
                $jenisSarana = '-';
               }
            @endphp --}}
            @php
              $saranas = [];
              $arraySarana = [];
              foreach ($table as $tbl){
                $saranas[] = $tbl['sample_type'];
              }

              foreach ($saranas as $sarana){
                $arraySarana[] = $sarana->jenis_sarana_names;
              }
              $arraySarana = array_unique(array_filter(array_map('trim', $arraySarana)));
              if (count($arraySarana) > 1) {
                  $last = array_pop($arraySarana);
                  $jenisSarana = implode(', ', $arraySarana) . ' dan ' . $last;
              } else {
                  $jenisSarana = $arraySarana[0] ?? '';
              }

            @endphp
            {{ $jenisSarana }}
          </strong>
        </td>
      </tr>

      <tr>
        <td colspan="3" style="padding-top: 2px;">
        </td>
      </tr>

      {{-- Metode Pemeriksaan --}}
      <tr>
        <td>
          Metode Pemeriksaan
        </td>
        <td>
          :
        </td>
        <td>
          <ol style="list-style: none; margin: 0; padding: 0; white-space:nowrap; font-weight: bold">
            @if ($method_all[0]->sampletype_id == '939ea9c5-f562-43d3-a7c2-75203c990a53')
              <li>ALT</li>
            @else
              @foreach ($metode_pemeriksaan as $metode)
                <li>{{ $metode }}</li>
              @endforeach
            @endif
          </ol>
        </td>
      </tr>

      {{-- Hasil Pemeriksaan --}}
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
      {{-- Nomor Agenda --}}
      <tr>
        <td colspan="3">
          <br>
        </td>
      </tr>

      {{-- Alamat Register --}}
      <tr>
        <td style="width: 150px">Petugas Sampling</td>
        <td>
          :
        </td>
        <td>
          {{ $sample->permohonanuji->name_sampling }}
        </td>
      </tr>

      {{-- Jenis Sampel --}}
      <tr>
        <td>Tanggal Sampling</td>
        <td>
          :
        </td>
        <td>
          @if (count($tanggal_pengambilan) > 2)
            <ul style="list-style: none; margin: 0; padding: 0;">
              @foreach ($tanggal_pengambilan as $tanggal)
                <li>
                  {{ \Carbon\Carbon::createFromFormat('Y-m-d', $tanggal)->isoFormat('D MMMM Y') }}
                  @if (!$loop->last)
                    <span>,</span>
                  @endif
                </li>
              @endforeach
            </ul>
          @elseif(count($tanggal_pengambilan) == 2)
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', head($tanggal_pengambilan ?? []))->isoFormat('D MMMM Y') }}
            <span> & </span>
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', last($tanggal_pengambilan ?? []))->isoFormat('D MMMM Y') }}
          @else
            {{ \Carbon\Carbon::createFromFormat('Y-m-d', head($tanggal_pengambilan ?? []))->isoFormat('D MMMM Y') }}
          @endif
        </td>
      </tr>

      {{-- Jenis Sarana --}}
      <tr style="vertical-align: top">
        <td>Parameter</td>
        <td>
          :
        </td>
        <td>
          <ul style="list-style: none; margin: 0; padding: 0;">
            @foreach ($method_all as $method)
              @if ($method->sampletype_id != 'ba2aa1af-02c7-4656-ad00-6e19d22f5eb2')
                <li> {{ $method->name_report }} </li>
              @endif
            @endforeach



            @if ($method->sampletype_id == 'ba2aa1af-02c7-4656-ad00-6e19d22f5eb2')
              <li> Total Coliform</li>
            @endif
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
