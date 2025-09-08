<?php

namespace Smt\Masterweb\Http\Controllers;

use PDF;
use Mapper;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\Unit;
use \Smt\Masterweb\Models\User;
use \Smt\Masterweb\Models\Method;
use \Smt\Masterweb\Models\Packet;
use \Smt\Masterweb\Models\Sample;
use Illuminate\Support\Facades\DB;
use \Smt\Masterweb\Models\Customer;
use \Smt\Masterweb\Models\Industry;
use \Smt\Masterweb\Models\Container;
use App\Http\Controllers\Controller;
use \Smt\Masterweb\Models\SampleType;
use \Smt\Masterweb\Models\Laboratorium;
use \Smt\Masterweb\Models\SampleMethod;
use \Smt\Masterweb\Models\SampleResult;
use \Smt\Masterweb\Models\PermohonanUji;


use Illuminate\Support\Facades\Validator;
use LDAP\Result;
use \Smt\Masterweb\Models\PenerimaanSample;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use \Smt\Masterweb\Models\LaboratoriumMethod;


use \Smt\Masterweb\Models\SampleResultDetail;
use \Smt\Masterweb\Models\LaboratoriumProgress;
use \Smt\Masterweb\Models\SampleAnalitikProgress;
use Smt\Masterweb\Models\SampleTypeDetail;

class LaboratoriumAnalitikSampleManagement extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function persiapan_reagen($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.persiapan-reagen.persiapan-reagen', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function persiapan_reagen_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();
    if (!isset($sampleanalitikprogress)) {

      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["persiapan_reagen_date"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["persiapan_reagen_date"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }

    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);
  }

  public function inkubasi($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.inkubasi.inkubasi', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function inkubasi_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();
    if (!isset($sampleanalitikprogress)) {

      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["inkubasi_date"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["inkubasi_date"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }
    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);
    // }else{
    //     return redirect()->route('elits-inkubasi.verification',[$id,$idlab]);

    // }


  }

  public function preparasi($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.preparasi.preparasi', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function preparasi_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();
    if (!isset($sampleanalitikprogress)) {
      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["preparasi"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["preparasi"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }
    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);
  }

  public function pemeriksaan_alat($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.pemeriksaan-alat.pemeriksaan-alat', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function pemeriksaan_alat_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();


    // $validated = $request->validate([
    //     'pemeriksaan_alat' => ['required']
    // ]);

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();
    if (!isset($sampleanalitikprogress)) {
      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pemeriksaan_alat"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pemeriksaan_alat"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }
    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);


    // dd("");

    // if($data["pemeriksaan_alat"]=="ya"){
    //     $sampleanalitikprogress =new SampleAnalitikProgress;
    //     $uuid4 = Uuid::uuid4();
    //     $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
    //     $sampleanalitikprogress->laboratorium_progress_id  = $progress;
    //     $sampleanalitikprogress->laboratorium_id = $idlab;
    //     $sampleanalitikprogress->sample_id = $id;
    //     $sampleanalitikprogress->date_done =Carbon::now()->format('Y-m-d H:i:s');
    //     $sampleanalitikprogress->save();
    //     return redirect()->route('elits-samples.verification',[$id,$idlab])->with(['status'=>'Pemeriksaan Alat berhasil di input']);

    // }else{
    //     return redirect()->route('elits-pemeriksaan-alat.verification',[$id,$idlab]);

    // }


  }

  public function pemeriksaan($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.pemeriksaan.pemeriksaan', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function pemeriksaan_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();

    if (!isset($sampleanalitikprogress)) {
      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pemeriksaan"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pemeriksaan"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }
    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);
  }

  public function pipetase($id, $idlab)
  {

    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })->get();
    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();




    return view('masterweb::module.admin.laboratorium.analitik.pipetase.pipetase', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }

  public function pipetase_store(Request $request, $id, $idlab, $progress)
  {
    $data = $request->all();

    $sampleanalitikprogress = SampleAnalitikProgress::where('laboratorium_progress_id', '=', $progress)
      ->where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)->first();

    if (!isset($sampleanalitikprogress)) {
      $sampleanalitikprogress = new SampleAnalitikProgress;
      $uuid4 = Uuid::uuid4();
      $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
      $sampleanalitikprogress->laboratorium_progress_id  = $progress;
      $sampleanalitikprogress->laboratorium_id = $idlab;
      $sampleanalitikprogress->sample_id = $id;
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pipetase"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    } else {
      $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pipetase"])->format('Y-m-d H:i:s');
      $sampleanalitikprogress->save();
    }
    return redirect()->route('elits-samples.verification', [$id, $idlab])->with(['status' => 'Sampel berhasil di input']);
  }

  public function baca_hasil($id, $idlab, $progress)
  {
    Carbon::setLocale('id');
    $sample = Sample::where('tb_samples.id_samples', '=', $id)
      ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->join('ms_laboratorium', function ($join) {
            $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
              ->whereNull('ms_laboratorium.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
          });
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();
    $sampletype_id = $sample->id_sample_type;

    $jenis_makanan_id = $sample->jenis_makanan_id;
    if (isset($jenis_makanan_id)) {

      // dd($jenis_makanan_id);

      $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
        ->where('tb_sample_method.sample_id', '=', $id)
        // ->orderBy('ms_method.jenis_parameter_kimia')
        ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlab, $id) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at')
            ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $jenis_makanan_id) {
              $join
                // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                //     LIMIT 1)'))
                ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
              $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })

            ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
              $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                ->where('tb_sample_result.sample_id', '=', $id)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at');
            });
        })



        ->select(
          'tb_baku_mutu.*',
          'ms_method.*',
          'tb_sample_method.*',
          'unit_baku_mutu.*',
          'tb_sample_result.hasil',
          'tb_sample_result.keterangan',
          'tb_sample_result.metode',
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    } else {
      $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
        ->where('tb_sample_method.sample_id', '=', $id)
        ->orderBy('ms_method.jenis_parameter_kimia')
        ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at')
            ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $idlab) {
              $join
                // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                //     LIMIT 1)'))
                ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->where('tb_baku_mutu.lab_id', '=', $idlab)
                ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
              $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })

            ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })
            ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
              $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                ->where('tb_sample_result.sample_id', '=', $id)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at');
            });
        })




        ->select(
          'tb_baku_mutu.*',
          'ms_method.*',
          'tb_sample_method.*',
          'unit_baku_mutu.*',
          'tb_sample_result.hasil',
          'tb_sample_result.keterangan',
          'tb_sample_result.metode',
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    }

    //pengurutan order ist
    $sample_type_details = SampleTypeDetail::where('sample_type_id', $sampletype_id)->orderBy('orderlist_sample_type_detail')->get();



    $method_all_temp = [];


    foreach ($sample_type_details as $sample_type_detail) {
      # code...
      foreach ($laboratoriummethods as $method) {
        # code...


        // print("& ".$method->id_method." ".$sample_type_detail->method_id);
        if ($method->id_method == $sample_type_detail->method_id) {
          $method_all_temp[] = $method;
        }
      }
    }
    if ($method_all_temp != [] and count($laboratoriummethods) == count($method_all_temp)) {
      # code...
      $laboratoriummethods = $method_all_temp;
    }

    $lab = Laboratorium::where('id_laboratorium', '=', $idlab)->first();

    foreach ($laboratoriummethods as $key => $laboratoriummethod) {
      # code...
      $laboratoriummethods[$key]->detail = array();

      $laboratoriummethods[$key]->detail = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('sampletype_id', '=', $sampletype_id)
        ->where('sample_id', '=',  $id)->get();
    }

    // dd($laboratoriummethods);
    $jenis_sarana_options = $this->get_jenis_sarana_options($sample);







    $units = Unit::all();


    $containers = Container::where('id_container', '!=', '0')->get();

    // $containers= Container::where('id_container','!=','0')->get();

    $sampleanalitikprogress = SampleAnalitikProgress::where("laboratorium_progress_id", $progress)
      ->where('laboratorium_id', $idlab)
      ->where('sample_id', $id)->first();

    // dd($sampleanalitikprogress);


    return view('masterweb::module.admin.laboratorium.analitik.baca-hasil.baca-hasil', compact('sampleanalitikprogress', 'sample', 'laboratoriummethods', 'containers', 'units', 'lab', 'jenis_sarana_options'));
  }

  public function baca_hasil_save(Request $request, $id, $idlab, $progress)
  {
    DB::beginTransaction();

    try {
      $simpan_baca_hasil = false;
      $data = $request->all();

      $sample = Sample::where('tb_samples.id_samples', '=', $id)
        ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            });
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->first();

      $sampletype_id = $sample->id_sample_type;

      $jenis_makanan_id = $sample->jenis_makanan_id;
      if (isset($jenis_makanan_id)) {

        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlab, $id) {
            $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
              ->whereNull('tb_sample_method.deleted_at')
              ->whereNull('ms_method.deleted_at')
              ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $jenis_makanan_id) {
                $join
                  // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                  //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                  //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                  //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                  //     LIMIT 1)'))
                  ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                  ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                  ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                  ->whereNull('tb_baku_mutu.deleted_at')
                  ->whereNull('ms_method.deleted_at');
              })
              ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
                $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                  ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                  ->whereNull('tb_baku_mutu.deleted_at');
              })

              ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
                $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                  ->whereNull('unit_baku_mutu.deleted_at')
                  ->whereNull('tb_baku_mutu.deleted_at');
              })->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
                $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                  ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                  ->where('tb_sample_result.sample_id', '=', $id)
                  ->whereNull('tb_sample_result.deleted_at')
                  ->whereNull('ms_method.deleted_at');
              });
          })



          ->select(
            'tb_baku_mutu.*',
            'ms_method.*',
            'tb_sample_method.*',
            'unit_baku_mutu.*',
            'tb_sample_result.hasil',
            'tb_sample_result.offset_baku_mutu'
          )
          ->get();
      } else {
        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
            $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
              ->whereNull('tb_sample_method.deleted_at')
              ->whereNull('ms_method.deleted_at')
              ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id) {
                $join
                  // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                  //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                  //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                  //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                  //     LIMIT 1)'))
                  ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                  ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                  ->whereNull('tb_baku_mutu.deleted_at')
                  ->whereNull('ms_method.deleted_at');
              })
              ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
                $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                  ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                  ->whereNull('tb_baku_mutu.deleted_at');
              })

              ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
                $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                  ->whereNull('unit_baku_mutu.deleted_at')
                  ->whereNull('tb_baku_mutu.deleted_at');
              })
              ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
                $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                  ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                  ->where('tb_sample_result.sample_id', '=', $id)
                  ->whereNull('tb_sample_result.deleted_at')
                  ->whereNull('ms_method.deleted_at');
              });
          })




          ->select(
            'tb_baku_mutu.*',
            'ms_method.*',
            'tb_sample_method.*',
            'unit_baku_mutu.*',
            'tb_sample_result.hasil',
            'tb_sample_result.offset_baku_mutu'
          )
          ->get();
      }

      SampleResult::where("sample_id", $id)
        ->where("laboratorium_id", $idlab)->delete();


      foreach ($laboratoriummethods as $laboratoriummethod) {
        $sampleresult                   = new SampleResult;
        $uuid4                          = Uuid::uuid4();
        // $sampleresult->id_sample_result = $uuid4->toString();
        $sampleresult->method_id        = $laboratoriummethod->method_id;
        $sampleresult->sample_id        = $id;
        $sampleresult->laboratorium_id  = $idlab;
        if (isset($data["status_" . $laboratoriummethod->method_id])) {
          $sampleresult->offset_baku_mutu = $data["offset_baku_mutu_" . $laboratoriummethod->method_id];

          $sampleresult->hasil            = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $laboratoriummethod->method_id]));
          $sampleresult->metode           = $data["metode_" . $laboratoriummethod->method_id];
          $sampleresult->keterangan       = $data["keterangan_" . $laboratoriummethod->method_id];
        } else {
          $sampleresult->keterangan       = $data["keterangan_" . $laboratoriummethod->method_id];
          $sampleresult->hasil            = "-";
        }

        $simpan_baca_hasil = $sampleresult->save();

        $sampleresultdetails = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('sampletype_id', '=', $sampletype_id)
          ->where('sample_id', '=',  $id)->get();

        foreach ($sampleresultdetails as $key => $sampleresultdetail) {
          $sampleresultdetail_edit = SampleResultDetail::findOrFail($sampleresultdetail->id_sample_result_detail);
          if (isset($data["status_" . $sampleresultdetail->id_sample_result_detail])) {

            $sampleresultdetail_edit->hasil = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $sampleresultdetail->id_sample_result_detail]));
          } else {
            $sampleresultdetail_edit->hasil            = "-";
          }
          $sampleresultdetail_edit->offset_baku_mutu = $data["offset_baku_mutu_" . $sampleresultdetail->id_sample_result_detail];
          $sampleresultdetail_edit->save();
        }
      }

      // simpan ke SampleAnalitikProgress
      $sampleanalitikprogress = SampleAnalitikProgress::where("laboratorium_progress_id", $progress)
        ->where('laboratorium_id', $idlab)
        ->where('sample_id', $id)->first();

      if (isset($sampleanalitikprogress)) {
        $sampleanalitikprogress = SampleAnalitikProgress::where("laboratorium_progress_id", $progress)
          ->where('laboratorium_id', $idlab)
          ->where('sample_id', $id)->first();


        // $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pengujian"])->format('Y-m-d H:i:s');
        if (isset($request->perlakuan_usap_tangan_sample_analitik_progress) || $request->perlakuan_usap_tangan_sample_analitik_progress != null) {
          $sampleanalitikprogress->perlakuan_usap_tangan_sample_analitik_progress = $request->perlakuan_usap_tangan_sample_analitik_progress;
        }

        $sampleanalitikprogress->save();
      } else {
        $sampleanalitikprogress = new SampleAnalitikProgress;
        $sampleanalitikprogress->laboratorium_progress_id  = $progress;
        $sampleanalitikprogress->laboratorium_id = $idlab;
        $sampleanalitikprogress->sample_id = $id;
        // $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pengujian"])->format('Y-m-d H:i:s');

        if (isset($request->perlakuan_usap_tangan_sample_analitik_progress) || $request->perlakuan_usap_tangan_sample_analitik_progress != null) {
          $sampleanalitikprogress->perlakuan_usap_tangan_sample_analitik_progress = $request->perlakuan_usap_tangan_sample_analitik_progress;
        }

        $sampleanalitikprogress->save();
      }

      $sample->location_samples = $request->get('lokasi_pengambilan');
      $sample->jenis_sarana_names = $request->get('jenis_sarana');
      $sample->save();

      DB::commit();

      if ($simpan_baca_hasil == true) {
        return response()->json(['status' => true, 'pesan' => "Data baca hasil berhasil disimpan!", 'url_redirect' => route('elits-baca-hasil.index', [$id, $idlab, $progress])], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data baca hasil tidak berhasil disimpan!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
    }
  }

  public function rules_hasil_store($request)
  {
    $rule = [
      'baca_hasil' => 'required'
    ];

    $pesan = [
      'baca_hasil.required' => 'Baca hasil wajib dichecklist!'
    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function baca_hasil_store(Request $request, $id, $idlab, $progress)
  {
    $validator = $this->rules_hasil_store($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $simpan_baca_hasil = false;
        $data = $request->all();



        $sampleresults = SampleResult::where('laboratorium_id', '=', $idlab)
          ->where('sample_id', '=', $id)->count();
        // dd($sampleresults);

        $sampleanalitikprogress = SampleAnalitikProgress::where("laboratorium_progress_id", $progress)
          ->where('laboratorium_id', $idlab)
          ->where('sample_id', $id)->first();

        // dd($sampleanalitikprogress);



        if (isset($sampleanalitikprogress)) {
          $sample = Sample::where('tb_samples.id_samples', '=', $id)
            ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_method.deleted_at');
                });
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->first();

          $sampletype_id = $sample->id_sample_type;
          $jenis_makanan_id = $sample->jenis_makanan_id;
          if (isset($jenis_makanan_id)) {

            $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
              ->where('tb_sample_method.sample_id', '=', $id)
              ->orderBy('ms_method.jenis_parameter_kimia')
              ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlab, $id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $jenis_makanan_id) {
                    $join
                      // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                      //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                      //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                      //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                      //     LIMIT 1)'))
                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                      ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
                    $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                      ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  })

                  ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  })->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
                    $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                      ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                      ->where('tb_sample_result.sample_id', '=', $id)
                      ->whereNull('tb_sample_result.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  });
              })




              ->select(
                'tb_baku_mutu.*',
                'ms_method.*',
                'tb_sample_method.*',
                'unit_baku_mutu.*',
                'tb_sample_result.hasil',
                'tb_sample_result.offset_baku_mutu'
              )
              ->get();
          } else {
            $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
              ->where('tb_sample_method.sample_id', '=', $id)
              ->orderBy('ms_method.jenis_parameter_kimia')
              ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id) {
                    $join
                      // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
                      //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
                      //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
                      //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
                      //     LIMIT 1)'))
                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
                    $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
                      ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  })

                  ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  })
                  ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
                    $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                      ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                      ->where('tb_sample_result.sample_id', '=', $id)
                      ->whereNull('tb_sample_result.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  });
              })




              ->select(
                'tb_baku_mutu.*',
                'ms_method.*',
                'tb_sample_method.*',
                'unit_baku_mutu.*',
                'tb_sample_result.hasil',
                'tb_sample_result.offset_baku_mutu'
              )
              ->get();
          }


          SampleResult::where("sample_id", $id)
            ->where("laboratorium_id", $idlab)->delete();

          foreach ($laboratoriummethods as $laboratoriummethod) {
            $sampleresult                   = new SampleResult;
            $uuid4                          = Uuid::uuid4();
            // $sampleresult->id_sample_result = $uuid4->toString();
            $sampleresult->method_id        = $laboratoriummethod->method_id;
            $sampleresult->sample_id        = $id;
            $sampleresult->laboratorium_id  = $idlab;

            if (isset($data["status_" . $laboratoriummethod->method_id])) {
              $sampleresult->offset_baku_mutu = $data["offset_baku_mutu_" . $laboratoriummethod->method_id];

              $sampleresult->hasil            = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $laboratoriummethod->method_id]));

              $sampleresult->metode           = $data["metode_" . $laboratoriummethod->method_id];
              $sampleresult->keterangan       = $data["keterangan_" . $laboratoriummethod->method_id];
            } else {
              $sampleresult->keterangan       = $data["keterangan_" . $laboratoriummethod->method_id];
              $sampleresult->hasil            = "-";
            }

            $simpan_baca_hasil = $sampleresult->save();

            $sampleresultdetails = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
              ->where('sampletype_id', '=', $sampletype_id)
              ->where('sample_id', '=',  $id)->get();

            foreach ($sampleresultdetails as $key => $sampleresultdetail) {
              $sampleresultdetail_edit = SampleResultDetail::findOrFail($sampleresultdetail->id_sample_result_detail);
              if (isset($data["status_" . $sampleresultdetail->id_sample_result_detail])) {

                $sampleresultdetail_edit->hasil = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $sampleresultdetail->id_sample_result_detail]));
              } else {
                $sampleresultdetail_edit->hasil            = "-";
              }
              $sampleresultdetail_edit->offset_baku_mutu = $data["offset_baku_mutu_" . $sampleresultdetail->id_sample_result_detail];
              $sampleresultdetail_edit->save();
            }
          }


          $sampleanalitikprogress = SampleAnalitikProgress::where("laboratorium_progress_id", $progress)
            ->where('laboratorium_id', $idlab)
            ->where('sample_id', $id)->first();


          //          $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pengujian"])->format('Y-m-d H:i:s');
          if (isset($request->perlakuan_usap_tangan_sample_analitik_progress) || $request->perlakuan_usap_tangan_sample_analitik_progress != null) {
            $sampleanalitikprogress->perlakuan_usap_tangan_sample_analitik_progress = $request->perlakuan_usap_tangan_sample_analitik_progress;
          }

          $sampleanalitikprogress->save();

          $analys = Sample::where('tb_samples.id_samples', '=', $id)
            ->where('ms_laboratorium.id_laboratorium', '!=', $idlab)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_method.deleted_at');
                });
            })
            ->leftjoin('tb_sample_analitik_progress', function ($join) {
              $join->on('tb_sample_analitik_progress.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
                ->on('tb_sample_analitik_progress.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_analitik_progress.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('tb_laboratorium_progress', function ($join) {
                  $join->on('tb_laboratorium_progress.id_laboratorium_progress', '=', 'tb_sample_analitik_progress.laboratorium_progress_id')
                    ->whereNull('tb_laboratorium_progress.deleted_at')
                    ->whereNull('tb_sample_analitik_progress.deleted_at')
                    ->where('tb_laboratorium_progress.link', '=', 'baca-hasil');
                });
            })
            ->get();

          $is_all_done = true;

          foreach ($analys as $analy) {
            if (!isset($analy->date_done) && ($analy->date_done != NULL)) {
              $is_all_done = false;
            }
          }

          if ($is_all_done) {
            $sample = Sample::where('tb_samples.id_samples', '=', $id)
              ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at')
                  ->join('ms_laboratorium', function ($join) {
                    $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                      ->whereNull('ms_laboratorium.deleted_at')
                      ->whereNull('tb_sample_method.deleted_at');
                  });
              })
              ->first();

            $sample->date_analitik_sample = Carbon::now()->format('Y-m-d H:i:s');
            $sample->location_samples = $request->post('lokasi_pengambilan');
            $sample->tembusan = $request->post('tembusan');
            $sample->only_fisika = $request->post('only_fisika');
            $sample->save();
          }
        } else {
          $sample = Sample::where('tb_samples.id_samples', '=', $id)
            ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_method.deleted_at');
                });
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->first();

          $sampletype_id = $sample->id_sample_type;

          $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
            ->where('sample_id', '=', $id)
            ->orderBy('ms_method.created_at')
            ->join('ms_method', function ($join) {
              $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
              $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })->join('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })
            ->select('tb_baku_mutu.*', 'ms_method.*', 'tb_sample_method.*', 'unit_baku_mutu.*')
            ->get();

          SampleResult::where("sample_id", $id)
            ->where("laboratorium_id", $idlab)->delete();



          foreach ($laboratoriummethods as $laboratoriummethod) {
            $sampleresult                   = new SampleResult;
            $uuid4                          = Uuid::uuid4();
            // $sampleresult->id_sample_result = $uuid4->toString();
            $sampleresult->method_id        = $laboratoriummethod->method_id;
            $sampleresult->sample_id        = $id;
            $sampleresult->laboratorium_id  = $idlab;
            if (isset($data["status_" . $laboratoriummethod->method_id])) {
              $sampleresult->offset_baku_mutu = $data["offset_baku_mutu_" . $laboratoriummethod->method_id];

              $sampleresult->hasil            = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $laboratoriummethod->method_id]));

              $sampleresult->metode           = $data["metode_" . $laboratoriummethod->method_id];
              $sampleresult->keterangan       = $data["keterangan_" . $laboratoriummethod->method_id];
            } else {
              $sampleresult->keterangan = $data["keterangan_" . $laboratoriummethod->method_id];
              $sampleresult->hasil            = "-";
            }

            $simpan_baca_hasil = $sampleresult->save();

            $sampleresultdetails = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
              ->where('sampletype_id', '=', $sampletype_id)
              ->where('sample_id', '=',  $id)->get();

            foreach ($sampleresultdetails as $key => $sampleresultdetail) {
              $sampleresultdetail_edit = SampleResultDetail::findOrFail($sampleresultdetail->id_sample_result_detail);
              if (isset($data["status_" . $sampleresultdetail->id_sample_result_detail])) {

                $sampleresultdetail_edit->hasil = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $sampleresultdetail->id_sample_result_detail]));
              } else {
                $sampleresultdetail_edit->hasil            = "-";
              }
              $sampleresultdetail_edit->offset_baku_mutu = $data["offset_baku_mutu_" . $sampleresultdetail->id_sample_result_detail];
              $sampleresultdetail_edit->save();
            }
          }



          $sampleanalitikprogress = new SampleAnalitikProgress;
          $uuid4 = Uuid::uuid4();
          // $sampleanalitikprogress->id_sample_analitik_progress = $uuid4->toString();
          $sampleanalitikprogress->laboratorium_progress_id  = $progress;
          $sampleanalitikprogress->laboratorium_id = $idlab;
          $sampleanalitikprogress->sample_id = $id;
          //          $sampleanalitikprogress->date_done = Carbon::createFromFormat('d/m/Y', $data["pengujian"])->format('Y-m-d H:i:s');

          if (isset($request->perlakuan_usap_tangan_sample_analitik_progress) || $request->perlakuan_usap_tangan_sample_analitik_progress != null) {
            $sampleanalitikprogress->perlakuan_usap_tangan_sample_analitik_progress = $request->perlakuan_usap_tangan_sample_analitik_progress;
          }

          $sampleanalitikprogress->save();

          $analys = Sample::where('tb_samples.id_samples', '=', $id)
            ->where('ms_laboratorium.id_laboratorium', '!=', $idlab)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_method.deleted_at');
                });
            })
            ->leftjoin('tb_sample_analitik_progress', function ($join) {
              $join->on('tb_sample_analitik_progress.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
                ->on('tb_sample_analitik_progress.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_analitik_progress.deleted_at')
                ->whereNull('tb_samples.deleted_at')
                ->join('tb_laboratorium_progress', function ($join) {
                  $join->on('tb_laboratorium_progress.id_laboratorium_progress', '=', 'tb_sample_analitik_progress.laboratorium_progress_id')
                    ->whereNull('tb_laboratorium_progress.deleted_at')
                    ->whereNull('tb_sample_analitik_progress.deleted_at')
                    ->where('tb_laboratorium_progress.link', '=', 'baca-hasil');
                });
            })
            ->get();

          $is_all_done = true;

          foreach ($analys as $analy) {
            if (!isset($analy->date_done) && ($analy->date_done != NULL)) {
              $is_all_done = false;
            }
          }

          if ($is_all_done) {
            $sample = Sample::where('tb_samples.id_samples', '=', $id)
              ->where('ms_laboratorium.id_laboratorium', '=', $idlab)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at')
                  ->join('ms_laboratorium', function ($join) {
                    $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                      ->whereNull('ms_laboratorium.deleted_at')
                      ->whereNull('tb_sample_method.deleted_at');
                  });
              })
              ->first();

            $sample->date_analitik_sample = Carbon::now()->format('Y-m-d H:i:s');
            $sample->location_samples = $request->post('lokasi_pengambilan');
            $sample->jenis_sarana_names = $request->post('jenis_sarana');
            $sample->tembusan = $request->post('tembusan');
            $sample->only_fisika = $request->post('only_fisika');
            $sample->save();
          }
        }

        $sample->refresh();
        $sample->location_samples = $request->get('lokasi_pengambilan');
        $sample->jenis_sarana_names = $request->get('jenis_sarana');
        $sample->save();

        DB::commit();

        if ($simpan_baca_hasil == true) {
          return response()->json(['status' => true, 'pesan' => "Data baca hasil berhasil disimpan!", 'url_redirect' => route('elits-samples.verification-2', [$id, $idlab])], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data baca hasil tidak berhasil disimpan!"], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($id)
  {
    //get auth user




    $user = Auth()->user();
    $count = Sample::count();
    $users = User::all();

    $packets = Packet::where('id_packet', '!=', '0')->orderBy('created_at')->get();

    $laboratoriums = Laboratorium::all();

    $data_methods = array();

    foreach ($laboratoriums as $laboratorium) {
      array_push(
        $data_methods,
        (object) array(
          'name' => $laboratorium->nama_laboratorium,
          'id_lab' => $laboratorium->id_laboratorium,
          'method' => array()
        )
      );
    }


    $i = 0;
    foreach ($data_methods as $data_method) {
      $laboratoriummethods = LaboratoriumMethod::where('laboratorium_id', '=', $data_method->id_lab)
        ->orderBy('ms_method.created_at')
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })->get();
      foreach ($laboratoriummethods as $laboratoriummethod) {
        //    print_r($laboratoriummethod->params_method);
        array_push(
          $data_methods[$i]->method,
          (object) array(
            'name_method' => $laboratoriummethod->params_method,
            'id_method' => $laboratoriummethod->id_method,
            'price_method' => $laboratoriummethod->price_method
          )
        );
      }

      $i++;
    }







    $containers = Container::where('id_container', '!=', '0')->get();
    $sampletypes = SampleType::orderBy('created_at')
      ->get();

    $code = 'S.KRNGY-' . date("Ymd", time()) . '-0' . ($count + 1);





    return view('masterweb::module.admin.laboratorium.sample.add', compact('user', 'data_methods', 'containers', 'packets', 'sampletypes', 'code', 'users'));
    //get all menu public
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request, $id, $idlabs)
  {
    $data = $request->all();


    // print_r($data);
    $validated = $request->validate([
      'wadah' => ['required', 'max:255'],
      'pengawet' => ['required'],
      'volume' => ['required'],
      'unit' => ['required'],
      'kondisi_sample' => ['required'],
      'validation_sample' => ['required']
    ]);

    // dd($data);


    $user = Auth()->user();
    $penerimaan_sample = new PenerimaanSample;
    //uuid
    $uuid4 = Uuid::uuid4();

    $penerimaan_sample->id_sample_penerimaan = $uuid4->toString();
    $penerimaan_sample->sample_id = $id;
    $penerimaan_sample->laboratorium_id = $idlabs;
    $penerimaan_sample->wadah_id = $request->post('wadah');
    $wadah_samples = $request->post('wadah_samples');
    if (isset($wadah_samples)) {
      $penerimaan_sample->wadah_sampel_other = $wadah_samples;
    }
    $penerimaan_sample->pengawet = $request->post('pengawet');
    $penerimaan_sample->unit_id = $request->post('unit');
    $penerimaan_sample->volume = $request->post('volume');
    $penerimaan_sample->kondisi_sample = $request->post('kondisi_sample');
    $penerimaan_sample->validation_sample  = $request->post('validation_sample');
    $penerimaan_sample->save();

    $sample = Sample::where('id_samples', $id)->first();



    return redirect()->route('elits-samples.verification-2', [$id, $idlabs])->with(['status' => 'Penerimaan berhasil di input']);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //


  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $auth = Auth()->user();

    $customer = Customer::where('id_customer', $id)->first();

    $categories = Industry::all();


    return view('masterweb::module.admin.laboratorium.customer.edit', compact('customer', 'auth', 'categories', 'id'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update($id, Request $request)
  {


    // print_r($data);



    $data = $request->all();
    $customer = Customer::find($id);
    $customer->name_customer = $request->post('name_customer');
    $customer->address_customer = $request->post('address_customer');
    $customer->email_customer = $request->post('email_customer');
    $customer->category_customer = $request->post('category_customer');
    $customer->cp_customer = $request->post('cp_customer');
    $customer->save();

    return redirect()->route('elits-customers.index')->with(['status' => 'Customer succesfully updated']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $customer = Customer::findOrFail($id);
    $customer->delete();
    return redirect()->route('elits-customers.index')->with('status', 'Data berhasil dihapus');
  }

  /**
   * Retrieves the options for the jenis sarana.
   *
   * This function is responsible for retrieving the options for the jenis sarana.
   * It does not take any parameters and does not return any value.
   *
   * @return array|null
   */
  protected function get_jenis_sarana_options(Sample $sample)
  {
    $jenis_sample = $sample->name_sample_type;
    $options = [];
    $processed_options = [];

    switch ($jenis_sample) {
      case 'Air Minum':
        $options = [
          'PDAM',
          'DAM / DAMIU',
          'AMDK',
          'Sumur',
          'PAMSIMAS',
          'Lainnya',
        ];
        break;
      case 'Air Bersih':
        $options = [
          'Sumur Gali',
          'Sumur Dalam',
          'Sumur Bor',
          'Perpipaan',
          'Sumur Artesis',
          'Lainnya',
        ];
        break;
      case 'Air Limbah':
        $options = [
          'IPAL'
        ];
        break;
      case 'Makanan/Minuman/Lainnya':
        $options = [
          'Nasi',
          'Sayur',
          'Lauk',
          'Lainnya',
        ];
        break;
      default:
        return null;
    }

    foreach ($options as $index => $value) {
      $key = \Illuminate\Support\Str::slug($value);
      $processed_options[$index] = [
        'key' => $key,
        'value' => $value
      ];
    }

    return $processed_options;
  }
}
