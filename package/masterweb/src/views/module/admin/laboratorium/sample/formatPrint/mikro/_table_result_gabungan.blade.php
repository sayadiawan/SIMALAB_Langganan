<table class="result" width="98%" border="3" cellspacing="0" cellpadding="0">
    <tr>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black; border-right: 2px solid black;"
            rowspan="3">Nomor <br> Lab.</td>
        <td class="td-result" style="text-align: center; width: 40%; border-bottom: 2px solid black;" rowspan="3">LOKASI</td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black; padding-left: 3px !important; padding-right: 3px !important;" rowspan="3">Jam Sampling</td>
        <td class="td-result" style="text-align: center; width: 66%; border-bottom: 3px solid black;"
            colspan="{{ count($data['parameter']) * 2 }}"><b>Parameter Wajib</b></td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black;" rowspan="3">Satuan</td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black;" rowspan="3">Ket.</td>
    </tr>



    <tr>
        @foreach ($data['parameter'] as $param)
            <td style="text-align: center; width: 0%" colspan="2"><b>{!! $param !!}</b></td>
        @endforeach
    </tr>

    <tr align="center">
        @foreach (range(1, count($data['parameter'])) as $num)
            <td style="border-bottom: 2px solid black; font-size: 12pt;"> <br>Hasil <br> Pemeriksaan <br><br></td>
            <td style="border-bottom: 2px solid black; font-size: 12pt;"> <br><b>Kadar Maksimum <br> yg
                    diperbolehkan</b> <br><br></td>
        @endforeach
    </tr>


    <style>
        .table_tr {
            vertical-align: center !important;
            vertical-align: center !important;
            padding-top: 12px !important;
            padding-left: 12px !important;
            padding-right: 12px !important;
            font-size: 12pt !important;

        }

        .td-result {
            font-size: 12pt !important;
        }
    </style>

    @foreach ($data['results'] as $result)
    @php
        $loop_lab_num = $result->count_id;
    @endphp
    <tr style="border-bottom: 1px solid white">
        <td class="table_tr" style="vertical-align:center;text-align: center; border-right: 2px solid black;">

            {{ !empty($loop_lab_num) ? sprintf('%04d', (int) $loop_lab_num) : '' }}

            @if ($loop->last)
                <br><br>
            @endif

        </td>
        <td class="table_tr">

                @if (isset($result->location_samples) && $result->location_samples != '')
                    @php

                        $location = str_replace('"""', '', $result->location_samples);
                        $location = str_replace("\n", '<br>', $location);
                        $location = str_replace('<p>', '', $location);
                        $location = str_replace('</p>', '', $location);

                        if (str_contains($location, 'π')){
                          $location = str_replace('π', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                        }

                        if (str_contains($location, '&pi;')){
                          $location = str_replace('&pi;', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                        }

                    @endphp
                    {!! $location !!}
                @else
                    @if ($result->is_pudam == 1)
                        @php

                            $location = str_replace('"""', '', $result->address_location_pdam);
                            $location = str_replace("\n", '<br>', $location);
                            $location = str_replace('<p>', '', $location);
                            $location = str_replace('</p>', '', $location);

                            if ($location == "") {
                                $location = $result->name_pelanggan;
                            }

                            if (str_contains($location, 'π')){
                              $location = str_replace('π', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                            }

                            if (str_contains($location, '&pi;')){
                              $location = str_replace('&pi;', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                            }

                        @endphp
                        {!! $location !!}
                    @else
                        @php
                            $location = str_replace('"""', '', $result->location_samples);
                            $location = str_replace("\n", '<br>', $location);
                            $location = str_replace('<p>', '', $location);
                            $location = str_replace('</p>', '', $location);

                            if ($location == "") {
                                $location = $mytable['sample_type']->name_pelanggan;
                            }

                            if (str_contains($location, 'π')){
                              $location = str_replace('π', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                            }

                            if (str_contains($location, '&pi;')){
                              $location = str_replace('&pi;', "<span style='font-family: \"DejaVu Sans\", sans-serif;'>π</span>", $location);
                            }

                        @endphp


                        {!! $location !!}
                    @endif
                @endif
        </td>
        <td class="table_tr" style="text-align: center;" width="20px"
            title="$mytable['sample_type']->datesampling_samples">
            {{ \Carbon\Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $result->datesampling_samples,
            )->isoFormat('HH:mm') }}
        </td>

        @foreach ($data['parameter'] as $key => $param)
                @foreach ($result->sampleresult as $sampleresult)
                    @if ($key == $sampleresult->method_id)
                        @php
                            $hasil = cek_hasil_color_mikro(
                                isset($sampleresult->hasil)
                                    ? $sampleresult->hasil
                                    : '',
                                $sampleresult->method[0]->bakumutu->min ?? null,
                                $sampleresult->method[0]->bakumutu->max ?? null,
                                $sampleresult->method[0]->bakumutu->equal ?? null,
                                'result_output_method_' . $sampleresult->method_id ?? null,
                                $sampleresult->offset_baku_mutu ?? null,
                            );

                            $nilaiBakuMutu = $sampleresult->method[0]->bakumutu->nilai_baku_mutu;
                        @endphp
                        <td class="table_tr" style="text-align: center; padding-left: 0 !important; padding-right: 0 !important;" title="$result->hasil">
                            {!! $hasil !!}</td>
                        <td class="table_tr" style="text-align: center;" title="$result->max">{!! $nilaiBakuMutu !!}
                        </td>
                    @endif
                @endforeach
            @endforeach


        <td class="table_tr" style="text-align: center; width: 90px !important; padding-left: 1px !important; padding-right: 1px !important;" title="$result->satuan_bakumutu">
            @php
                $satuans = [];
                foreach ($result->sampleresult as $sampleresult) {
                    foreach ($sampleresult->method as $method) {
                        $satuans[] = $method->bakumutu->unit->name_unit;
                    }
                }

                $satuans = array_unique($satuans);
            @endphp
            @foreach ($satuans as $satuan)
                <span style="color: black;">{!! $satuan !!}</span>
            @endforeach
        </td>
      <td class="table_tr" style="text-align: center; padding-left: 0 !important; padding-right: 0 !important;" width="75px" title="(static)">

          @foreach ($result->sampleresult as $sampleresult)
            <span style="color: black;">{{ $sampleresult->keterangan }}</span>
          @endforeach

        </td>
    </tr>
    @endforeach
</table>
