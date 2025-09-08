<?php

namespace Smt\Masterweb\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FormatHajiExport implements FromView
{
    protected $count;
    protected $nama_haji;
    protected $tgl_haji;
    protected $kuota;

    public function __construct($count, $nama_haji, $tgl_haji, $kuota)
    {
        $this->count = $count;
        $this->nama_haji = $nama_haji;
        $this->tgl_haji = $tgl_haji;
        $this->kuota = $kuota;
    }

    public function view(): View
    {
        $romanMonths = [
          1 => 'I',
          2 => 'II',
          3 => 'III',
          4 => 'IV',
          5 => 'V',
          6 => 'VI',
          7 => 'VII',
          8 => 'VIII',
          9 => 'IX',
          10 => 'X',
          11 => 'XI',
          12 => 'XII'
        ];

        $currentMonth = date('n'); // Mendapatkan bulan saat ini dalam format numerik (1-12)
        $romanMonth = $romanMonths[$currentMonth]; // Mendapatkan bulan dalam format Romawi

        $rows = array_fill(0, $this->kuota, []);
        return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.format_haji_excel', [
            'rows' => $rows,
            'nama_haji' => $this->nama_haji,
            'tgl_haji' => $this->tgl_haji,
            'count' => $this->count,
            'romanMonth' => $romanMonth,
        ]);
    }
}
