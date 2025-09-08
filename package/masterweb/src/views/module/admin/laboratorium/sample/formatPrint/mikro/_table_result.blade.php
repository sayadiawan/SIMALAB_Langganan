<table class="result" width="98%" border="3" cellspacing="0" cellpadding="0">
    <tr>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black; border-right: 2px solid black;"
            rowspan="3">Nomor <br> Lab.</td>
        <td class="td-result" style="text-align: center; width: 40%; border-bottom: 2px solid black;" rowspan="3">LOKASI</td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black; padding-left: 3px !important; padding-right: 3px !important;" rowspan="3">Jam Sampling</td>
        <td class="td-result" style="text-align: center; width: 66%; border-bottom: 3px solid black;"
            colspan="{{ count($method_all) * 2 }}"><b>Parameter Wajib</b></td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black;" rowspan="3">Satuan</td>
        <td class="td-result" style="text-align: center; width: 4%; border-bottom: 2px solid black;" rowspan="3">Ket.</td>
    </tr>



    <tr>
        @foreach ($method_all as $method)
            <td style="text-align: center; width: 0%" colspan="2"><b>{!! $method->name_report !!}</b></td>
        @endforeach
    </tr>

    <tr align="center">
        @foreach (range(1, count($method_all)) as $num)
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


    @foreach ($table as $mytable)
        @php
            $loop_lab_num = data_get($lab_nums, data_get($mytable, 'sample_type.id_samples'));
        @endphp
        <tr style="border-bottom: 1px solid white">
            <td class="table_tr" style="vertical-align:center;text-align: center; border-right: 2px solid black;">

                {{ !empty($loop_lab_num) ? sprintf('%04d', (int) $loop_lab_num) : '' }}

                @if ($loop->last)
                    <br><br>
                @endif

            </td>
            <td class="table_tr">

                @if (isset($mytable['sample_type']->location_samples) && $mytable['sample_type']->location_samples != '')
                    @php

                        if ($mytable['sample_type']->is_pudam == 1) {
                            $location = str_replace('"""', '', $mytable['sample_type']->address_location_pdam);
                            $location = str_replace("\n", '<br>', $location);
                            $location = str_replace('<p>', '', $location);
                            $location = str_replace('</p>', '', $location);

                            if ($location == "") {
                                $location = $mytable['sample_type']->name_pelanggan;
                            }

                        }else{
                            $location = str_replace('"""', '', $mytable['sample_type']->location_samples);
                            $location = str_replace("\n", '<br>', $location);
                            $location = str_replace('<p>', '', $location);
                            $location = str_replace('</p>', '', $location);
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
                    @if ($mytable['sample_type']->is_pudam == 1)
                        @php

                            $location = str_replace('"""', '', $mytable['sample_type']->address_location_pdam);
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
                    @else
                        @php
                            $location = str_replace('"""', '', $mytable['sample_type']->location_samples);
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
                    data_get($mytable, 'sample_type.datesampling_samples'),
                )->isoFormat('HH:mm') }}

            </td>

            @foreach ($method_all as $method)
                @foreach ($mytable['result'] as $result)
                    @if ($method->id_method == $result['method_id'])
                        @php
                            // dd($result->method_id);
                            $hasil = cek_hasil_color_mikro(
                                isset($result['hasil'])
                                    ? $result['hasil']
                                    : (isset($result['equal'])
                                        ? $result['equal']
                                        : ''),
                                $result['min'] ?? null,
                                $result['max'] ?? null,
                                $result['equal'] ?? null,
                                'result_output_method_' . $result['method_id'] ?? null,
                                $result['offset_baku_mutu'] ?? null,
                            );

                        @endphp
                        <td class="table_tr" style="text-align: center; padding-left: 0 !important; padding-right: 0 !important;" title="$result->hasil">
                            {!! $hasil !!}</td>
                        <td class="table_tr" style="text-align: center;" title="$result->max">{!! data_get($result, 'nilai_baku_mutu') !!}
                        </td>
                    @endif
                @endforeach
            @endforeach

            <td class="table_tr" style="text-align: center; width: 90px !important; padding-left: 1px !important; padding-right: 1px !important;" title="$result->satuan_bakumutu">{!! $result['satuan_bakumutu'] !!}
            </td>
            <td class="table_tr" style="text-align: center; padding-left: 0 !important; padding-right: 0 !important;" width="75px" title="(static)">

              @foreach ($mytable['result'] as $result)
                <span style="color: black;">{{ $result['keterangan'] }}</span>
              @endforeach

            </td>
        </tr>
    @endforeach

</table>
