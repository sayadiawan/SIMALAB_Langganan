<?php

namespace Smt\Masterweb\Http\Controllers;

use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\User;
use \Smt\Masterweb\Models\Method;
use \Smt\Masterweb\Models\Customer;
use \Smt\Masterweb\Models\Industry;
use \Smt\Masterweb\Models\Sample;
use \Smt\Masterweb\Models\SampleMethod;
use \Smt\Masterweb\Models\SampleResult;
use \Smt\Masterweb\Models\SampleType;
use \Smt\Masterweb\Models\Container;
use \Smt\Masterweb\Models\Packet;
use \Smt\Masterweb\Models\PermohonanUji;
use \Smt\Masterweb\Models\Laboratorium;
use \Smt\Masterweb\Models\LaboratoriumMethod;
use \Smt\Masterweb\Models\LaboratoriumProgress;
use \Smt\Masterweb\Models\PenerimaanSample;
use \Smt\Masterweb\Models\VerifikasiHasil;
use \Smt\Masterweb\Models\Unit;
use \Smt\Masterweb\Models\LHU;
use \Smt\Masterweb\Models\SampleResultDetail;


use SimpleSoftwareIO\QrCode\Facades\QrCode;

use PDF;
use DB;

use Mapper;


use Carbon\Carbon;
use Smt\Masterweb\Models\SampleTypeDetail;

class LaboratoriumVerifikasiHasilManagement extends Controller
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
  public function index($id, $idlab)
  {

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
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    }





    foreach ($laboratoriummethods as $key => $laboratoriummethod) {
      # code...
      $laboratoriummethods[$key]->detail = array();
      $laboratoriummethods[$key]->detail = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('sampletype_id', '=', $sampletype_id)
        ->where('sample_id', '=',  $id)->get();
    }





    $units = Unit::all();

    $containers = Container::where('id_container', '!=', '0')->get();


    return view('masterweb::module.admin.laboratorium.penerimaan-sample.penerimaan', compact('sample', 'laboratoriummethods', 'containers', 'units'));
  }






  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create($id, $idlab)
  {
    //get auth user



    $user = Auth()->user();
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
          'tb_sample_result.metode',
          'tb_sample_result.keterangan',
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
          'tb_sample_result.keterangan',
          'tb_sample_result.metode',
          'tb_sample_result.hasil',
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    }

     //pengurutan order ist
     $sample_type_details = SampleTypeDetail::where('sample_type_id', $sampletype_id)->orderBy('orderlist_sample_type_detail','ASC')->get();




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





    foreach ($laboratoriummethods as $key => $laboratoriummethod) {
      # code...
      $laboratoriummethods[$key]->detail = array();
      $laboratoriummethods[$key]->detail = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('sampletype_id', '=', $sampletype_id)
        ->where('sample_id', '=',  $id)->get();
    }



    $verifikasi_hasil = VerifikasiHasil::where('laboratorium_id', $idlab)
      ->where('sample_id', $id)->first();


    return view('masterweb::module.admin.laboratorium.verifikasi-hasil.verifikasi_hasil', compact('verifikasi_hasil', 'user', 'sample', 'laboratoriummethods'));
    //get all menu public
  }


  function convertToRoman($integer)
  {
    // Convert the integer into an integer (just to make sure)
    $integer = intval($integer);
    $result = '';

    // Create a lookup array that contains all of the Roman numerals.
    $lookup = array(
      'M' => 1000,
      'CM' => 900,
      'D' => 500,
      'CD' => 400,
      'C' => 100,
      'XC' => 90,
      'L' => 50,
      'XL' => 40,
      'X' => 10,
      'IX' => 9,
      'V' => 5,
      'IV' => 4,
      'I' => 1
    );

    foreach ($lookup as $roman => $value) {
      // Determine the number of matches
      $matches = intval($integer / $value);

      // Add the same number of characters to the string
      $result .= str_repeat($roman, $matches);

      // Set the integer to be the remainder of the integer and the value
      $integer = $integer % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
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
    $user = Auth()->user();

    $verifikasi_hasil = VerifikasiHasil::where('laboratorium_id', $idlabs)
      ->where('sample_id', $id)->first();

    if (isset($verifikasi_hasil)) {
      $verifikasi_hasil->verifikasi_hasil_date = Carbon::createFromFormat('d/m/Y', $data["verifikasi_hasil"])->format('Y-m-d H:i:s');

      $verifikasi_hasil->save();

      $sample = Sample::where('tb_samples.id_samples', '=', $id)
        ->where('ms_laboratorium.id_laboratorium', '=', $idlabs)
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

        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlabs)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlabs, $id) {
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
              })->leftjoin('tb_sample_result', function ($join) use ($id, $idlabs) {
                $join->where('tb_sample_result.laboratorium_id', '=', $idlabs)
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
        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlabs)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id, $idlabs, $id) {
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
              ->leftjoin('tb_sample_result', function ($join) use ($id, $idlabs) {
                $join->where('tb_sample_result.laboratorium_id', '=', $idlabs)
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
        ->where("laboratorium_id", $idlabs)->delete();
      foreach ($laboratoriummethods as $laboratoriummethod) {


        $sampleresult                   = new SampleResult;
        $uuid4                          = Uuid::uuid4();
        $sampleresult->id_sample_result = $uuid4->toString();
        $sampleresult->method_id        = $laboratoriummethod->method_id;
        $sampleresult->sample_id        = $id;
        $sampleresult->laboratorium_id  = $idlabs;
        if (isset($data["status_" . $laboratoriummethod->method_id])) {
          $sampleresult->offset_baku_mutu = $data["offset_baku_mutu_" . $laboratoriummethod->method_id];

          $sampleresult->hasil            = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $laboratoriummethod->method_id]));
          $sampleresult->metode = $data["metode_" . $laboratoriummethod->method_id];
          $sampleresult->keterangan = $data["keterangan_" . $laboratoriummethod->method_id];
        } else {
          $sampleresult->keterangan = $data["keterangan_" . $laboratoriummethod->method_id];
          $sampleresult->hasil            = "-";
        }
        $sampleresult->save();


        $sampleresultdetails = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('sampletype_id', '=', $sampletype_id)
          ->where('sample_id', '=',  $id)->get();

        foreach ($sampleresultdetails as $key => $sampleresultdetail) {
          # code...
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
    } else {

      $verifikasi_hasil = new VerifikasiHasil;
      //uuid
      $uuid4 = Uuid::uuid4();

      $verifikasi_hasil->id_verifikasi_hasil = $uuid4->toString();
      $verifikasi_hasil->sample_id = $id;
      $verifikasi_hasil->laboratorium_id = $idlabs;
      $verifikasi_hasil->verifikasi_hasil_date = Carbon::createFromFormat('d/m/Y', $data["verifikasi_hasil"])->format('Y-m-d H:i:s');

      $verifikasi_hasil->save();

      $sample = Sample::where('tb_samples.id_samples', '=', $id)
        ->where('ms_laboratorium.id_laboratorium', '=', $idlabs)
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

        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlabs)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id) {
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
              });
          })

          ->leftjoin('tb_sample_result', function ($join) use ($id, $idlabs) {
            $join->where('tb_sample_result.laboratorium_id', '=', $idlabs)
              ->where('tb_sample_result.method_id', '=', 'ms_method.id_method')
              ->where('tb_sample_result.sample_id', '=', $id)
              ->whereNull('tb_sample_result.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
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
        $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlabs)
          ->where('tb_sample_method.sample_id', '=', $id)
          ->orderBy('ms_method.jenis_parameter_kimia')
          ->join('ms_method', function ($join)   use ($sampletype_id) {
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
              });
          })

          ->leftjoin('tb_sample_result', function ($join) use ($id, $idlabs) {
            $join->where('tb_sample_result.laboratorium_id', '=', $idlabs)
              ->where('tb_sample_result.method_id', '=', 'ms_method.id_method')
              ->where('tb_sample_result.sample_id', '=', $id)
              ->whereNull('tb_sample_result.deleted_at')
              ->whereNull('tb_sample_method.deleted_at');
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
        ->where("laboratorium_id", $idlabs)->delete();
      foreach ($laboratoriummethods as $laboratoriummethod) {


        $sampleresult                   = new SampleResult;
        $uuid4                          = Uuid::uuid4();
        $sampleresult->id_sample_result = $uuid4->toString();
        $sampleresult->method_id        = $laboratoriummethod->method_id;
        $sampleresult->sample_id        = $id;
        $sampleresult->laboratorium_id  = $idlabs;
        if (isset($data["status_" . $laboratoriummethod->method_id])) {
          $sampleresult->offset_baku_mutu = $data["offset_baku_mutu_" . $laboratoriummethod->method_id];

          $sampleresult->hasil            = rubahNilaikeHtml(str_replace(',', '.', $data["result_method_" . $laboratoriummethod->method_id]));
          $sampleresult->metode = $data["metode_" . $laboratoriummethod->method_id];
          $sampleresult->keterangan = $data["keterangan_" . $laboratoriummethod->method_id];
        } else {
          $sampleresult->keterangan = $data["keterangan_" . $laboratoriummethod->method_id];
          $sampleresult->hasil            = "-";
        }
        $sampleresult->save();


        $sampleresultdetails = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('sampletype_id', '=', $sampletype_id)
          ->where('sample_id', '=',  $id)->get();

        foreach ($sampleresultdetails as $key => $sampleresultdetail) {
          # code...
          $sampleresultdetail_edit = SampleResultDetail::findOrFail($sampleresultdetail->id_sample_result_detail);
          if (isset($data["status_" . $sampleresultdetail->id_sample_result_detail])) {

            $sampleresultdetail_edit->hasil = rubahNilaikeHtml(str_replace(",", ".", $data["result_method_" . $sampleresultdetail->id_sample_result_detail]));
          } else {
            $sampleresultdetail_edit->hasil = "-";
          }
          $sampleresultdetail_edit->offset_baku_mutu = $data["offset_baku_mutu_" . $sampleresultdetail->id_sample_result_detail];

          $sampleresultdetail_edit->save();
        }
      }

      $no_LHU = LHU::where('sample_id', '=', $id)->where('lab_id', '=', $idlabs)->first();

      if (!isset($no_LHU)) {
        $no_LHU = new LHU;
        //uuid
        $uuid4 = Uuid::uuid4();

        $no_LHU_urutan = LHU::max('nomer_urut_LHU');


        // $no_LHU->id_lhu = $uuid4->toString();
        $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
        $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));
        $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');
        $no_LHU->sample_id = $id;
        $no_LHU->lab_id = $idlabs;
        $no_LHU->save();
      }
    }
    // dd($data);
    if (isset($sample->location_samples)){
      $sample->location_samples = $data['lokasi_pengambilan'];
    }else{
      $sample->address_location_pdam = $data['lokasi_pengambilan'];
    }

    $sample->save();



    // $sample = Sample::where('id_samples',$id)->first();



    return redirect()->route('elits-samples.verification-2', [$id, $idlabs])->with(['status' => 'Pelaporan Hasil berhasil di input']);
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
}
