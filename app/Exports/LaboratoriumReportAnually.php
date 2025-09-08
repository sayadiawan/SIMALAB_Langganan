<?php

namespace App\Exports;

use DB;
use Smt\Masterweb\Models\Sample;
use Smt\Masterweb\Models\Program;
use Illuminate\Contracts\View\View;
use Smt\Masterweb\Models\SampleType;
use Smt\Masterweb\Models\SampleMethod;
use Smt\Masterweb\Models\SampleResult;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaboratoriumReportAnually  implements FromView, ShouldAutoSize
{
  private $year;
  private $program;
  private $sampletype;


  public function __construct(int $year, $program, $sampletype)
  {
    $this->year  = $year;
    $this->program  = $program;
    $this->sampletype  = $sampletype;
  }

  public function view(): View
  {
    $tahun = $this->year;
    $program = $this->program;
    $sampletype = $this->sampletype;

    $item_program = Program::find($program);
    $item_sampletype = SampleType::find($sampletype);

    $data_sample = Sample::whereHas('pengesahanhasil', function ($query) {
      // return $query->whereNull('deleted_at')->whereNull('sample_id');
      return $query->whereNull('deleted_at')->whereNull('sample_id');
    })
      ->whereHas('permohonanuji', function ($query) {
        return $query->whereNull('deleted_at');
      })
      ->whereHas('samplemethod', function ($query) {
        return $query->whereNull('deleted_at');
      })
      ->orderBy('tb_sample_penanganan.penanganan_sample_date', 'asc')
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
          // ->limit(1)
          // ->orderBy('penanganan_sample_date', 'asc')
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      });

     $data_sample =  $data_sample->whereHas('permohonanuji', function ($query) use ($tahun) {
        return $query
      ->whereYear('tb_permohonan_uji.date_permohonan_uji', $tahun)
      ->whereNull('deleted_at');
      });

    if ($program != null) {

      $data_sample = $data_sample->where('program_samples', $program);
    }
    if ($sampletype != null) {

      $data_sample = $data_sample->where('typesample_samples', $sampletype);
    }
    $data_sample = $data_sample->get();

    // dd($data_sample);

    // PRINT ALL METHOD TO HORIZONTAL
    // $method_all = Sample::join('tb_sample_method', function ($join) {
    //     $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
    //       ->whereNull('tb_sample_method.deleted_at')
    //       ->whereNull('tb_samples.deleted_at');
    //   })
    //   ->join('ms_method', function ($join) {
    //     $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
    //       ->whereNull('ms_method.deleted_at')
    //       ->whereNull('tb_sample_method.deleted_at');
    //   })

    //   ->select('ms_method.*')
    //   ->distinct('ms_method.id_method')
    //   ->get();

    $method_all = Sample::leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
          // ->limit(1)
          // ->orderBy('penanganan_sample_date', 'asc')
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('ms_method.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->join('tb_baku_mutu', function ($join) {
        $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
          ->on('tb_baku_mutu.sampletype_id', '=', 'tb_samples.typesample_samples')
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('ms_unit as unit_baku_mutu', function ($join) {
        $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
          ->whereNull('unit_baku_mutu.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->select('ms_method.*', 'tb_baku_mutu.*', 'unit_baku_mutu.*')
      ->distinct('ms_method.id_method');

    $method_all =  $method_all->whereHas('permohonanuji', function ($query) use ($tahun) {
        return $query 
      ->whereYear('tb_permohonan_uji.date_permohonan_uji', $tahun)
      ->whereNull('deleted_at');
      });
    if ($program != null) {
      $method_all = $method_all->where('program_samples', $program);
    }
    if ($sampletype != null) {

      $method_all = $method_all->where('typesample_samples', $sampletype);
    }
    $method_all = $method_all->get();
    // dd($method_all);

    $data_sample_methods = Sample::orderBy('penanganan_sample_date', 'asc')
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
          // ->limit(1)
          // ->orderBy('penanganan_sample_date', 'asc')
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      });
     $data_sample_methods =  $data_sample_methods->whereHas('permohonanuji', function ($query) use ($tahun) {
        return $query 
      ->whereYear('tb_permohonan_uji.date_permohonan_uji', $tahun)
      ->whereNull('deleted_at');
      });
    if ($program != null) {
      $data_sample_methods = $data_sample_methods->where('program_samples', $program);
    }
    if ($sampletype != null) {

      $data_sample_methods = $data_sample_methods->where('typesample_samples', $sampletype);
    }
    $data_sample_methods = $data_sample_methods->get();


    $sampels = [];
    foreach ($data_sample_methods as $data_sample_method) {
      $sample = [];
      $sample["sample"] = $data_sample_method;
      $sample["method"] = [];
      foreach ($method_all as $method) {

        $result = SampleResult::where('method_id',  $method->id_method)
          ->where('sample_id', $data_sample_method->id_samples)
          /* ->whereHas('sample.pengesahanhasil', function ($query) {
                    return $query->whereNull('sample_id');
                }) */
          ->first();
        if (isset($result)) {
          if ($method->sampletype_id == $data_sample_method->typesample_samples) {
            array_push($sample["method"], $result->hasil);
          } else {
            array_push($sample["method"], '');
          }
          // $sample["method"][ $method->id_method]=$result->hasil;
        } else {
          array_push($sample["method"], '');
          // $sample["method"]="-";
        }
      }
      array_push($sampels, $sample);
      # code...
    }



    // dd($sampels);


    // dd($data_sample_method);

    // dd(is_object($data_sample));

    return view('masterweb::module.admin.laboratorium.report.report-annual.formatPrint.report-anually-maatweb', [
      'data_sample' => $data_sample,
      'tahun' => $tahun,
      'item_program' => $item_program,
      'item_sampletype' => $item_sampletype,
      'method_all' => $method_all,
      'data_sample_method' => $sampels
    ]);
  }
}