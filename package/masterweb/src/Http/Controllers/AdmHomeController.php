<?php

namespace Smt\Masterweb\Http\Controllers;

use PDF;
use Mapper;
use Carbon\Carbon;
use Smt\Masterweb\Rules\Captcha;
use \Smt\Masterweb\Models\Sample;
use Illuminate\Support\Facades\DB;


use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\Laboratorium;
use \Smt\Masterweb\Models\PermohonanUji;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;




class AdmHomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $auth = Auth()->user();
    $dataTotal = Sample::query()->selectRaw('
        (SELECT COUNT(*) FROM tb_permohonan_uji WHERE deleted_at IS NULL) AS total_permohonan_uji,
        COUNT(*) AS total_sample,
        SUM(CASE WHEN date_analitik_sample IS NULL AND deleted_at IS NULL THEN 1 ELSE 0 END) AS total_berjalan,
        SUM(CASE WHEN date_analitik_sample IS NOT NULL AND deleted_at IS NULL THEN 1 ELSE 0 END) AS total_selesai
    ')
      ->whereNull('deleted_at')
      ->first();

    $total_permohonan_uji = $dataTotal->total_permohonan_uji;
    $total_sample = $dataTotal->total_sample;
    $total_berjalan = $dataTotal->total_berjalan;
    $total_selesai = $dataTotal->total_selesai;

    $dataPermohonanUjiKlinik = PermohonanUjiKlinik2::query()
      ->selectRaw('
        (SELECT COUNT(*) FROM tb_permohonan_uji_klinik_2 WHERE deleted_at IS NULL) AS total_permohonan,
        COUNT(DISTINCT pasien_permohonan_uji_klinik) AS total_pasien
    ')
      ->whereNull('deleted_at')
      ->first();


    $total_permohonan_uji_klinik = $dataPermohonanUjiKlinik->total_permohonan;
    $pasien_klinik = $dataPermohonanUjiKlinik->total_pasien;

    $pendapatan = PermohonanUji::query()
      ->select(DB::raw('COALESCE(SUM(total_harga), 0) as total_pendapatan'), DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'))
      ->whereBetween('created_at', [now()->subMonths(12), now()->addMonths(4)])
      ->where('status_pembayaran', 1)
      ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
      ->orderBy('bulan')
      ->get();

    $pendapatanKlinik = PermohonanUjiKlinik2::query()
      ->select(DB::raw('COALESCE(SUM(total_harga_permohonan_uji_klinik), 0) as total_pendapatan'), DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'))
      ->whereBetween('created_at', [now()->subMonths(12), now()->addMonths(4)])
      ->where('status_pembayaran', 1)
      ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
      ->orderBy('bulan')
      ->get();

    $bulans = [];
    $pendapatans = [];

    foreach ($pendapatan as $pd) {
      $found = false;

      foreach ($pendapatanKlinik as $pdk) {
        if ($pd->bulan == $pdk->bulan) {
          $bulans[] = $pd->bulan;
          $pendapatans[] = $pd->total_pendapatan + $pdk->total_pendapatan;
          $found = true;
          break;
        }
      }

      if (!$found) {
        $bulans[] = $pd->bulan;
        $pendapatans[] = $pd->total_pendapatan;
      }
    }

    foreach ($pendapatanKlinik as $pdk) {
      if (!in_array($pdk->bulan, $bulans)) {
        $bulans[] = $pdk->bulan;
        $pendapatans[] = $pdk->total_pendapatan;
      }
    }

    $samples = Sample::query()
      ->join('ms_sample_type', 'ms_sample_type.id_sample_type', '=', 'typesample_samples')
      ->select(DB::raw('COALESCE(COUNT(id_samples), 0) as total_sample'), DB::raw('ms_sample_type.name_sample_type as type'))
      ->groupBy('ms_sample_type.name_sample_type')
      ->get();

    $countSample = [];
    $sampleTypes = [];

    foreach ($samples as  $sample){
      $countSample[] = $sample->total_sample;
      $sampleTypes[] = $sample->type;
    }


    return view(
      'masterweb::module.admin.beranda',
      compact(
        "auth",
        'total_permohonan_uji',
        'total_sample',
        'total_berjalan',
        'total_selesai',
        'bulans',
        'pendapatans',
        'sampleTypes',
        'countSample',
        'total_permohonan_uji_klinik',
        'pasien_klinik',
      )
    );
  }
}
