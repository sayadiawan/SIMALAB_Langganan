<?php

namespace Smt\Masterweb\Http\Controllers;


use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Http\Request;
use Smt\Masterweb\Helpers\Smt;
use Smt\Masterweb\Models\Petugas;
use Smt\Masterweb\Models\VerificationActivity;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mapper;
use PDF;
use Ramsey\Uuid\Uuid;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Smt\Masterweb\Models\BakuMutu;
use Smt\Masterweb\Models\BakuMutuDetailParameterNonKlinik;
use Smt\Masterweb\Models\Container;
use Smt\Masterweb\Models\Customer;
use Smt\Masterweb\Models\JenisMakanan;
use Smt\Masterweb\Models\LabNum;
use Smt\Masterweb\Models\Laboratorium;
use Smt\Masterweb\Models\LaboratoriumMethod;
use Smt\Masterweb\Models\LaboratoriumProgress;
use Smt\Masterweb\Models\LHU;
use Smt\Masterweb\Models\Method;
use Smt\Masterweb\Models\Packet;
use Smt\Masterweb\Models\PenerimaanSample;
use Smt\Masterweb\Models\PengesahanHasil;
use Smt\Masterweb\Models\PermohonanUji;
use Smt\Masterweb\Models\Program;
use Smt\Masterweb\Models\Sample;
use Smt\Masterweb\Models\SampleAnalitikProgress;
use Smt\Masterweb\Models\SampleMethod;
use Smt\Masterweb\Models\SampleResult;
use Smt\Masterweb\Models\SampleResultDetail;
use Smt\Masterweb\Models\SampleType;
use Smt\Masterweb\Models\SampleTypeDetail;
use Smt\Masterweb\Models\StartNum;
use Smt\Masterweb\Models\Unit;
use Smt\Masterweb\Models\User;
use Smt\Masterweb\Models\VerificationActivitySample;
use Smt\Masterweb\Models\VerifikasiHasil;
use Yajra\Datatables\Datatables;

// use \Smt\Masterweb\Models\PenerimaanSample;


class LaboratoriumSampleManagement extends Controller
{
  protected array $sample_types = [
    'AMB' => 'Air Minum Baktereologi',
    'ABB' => 'Air Bersih Bakteorologi',
    'AMF' => 'Air Minum Fisika',
    'AMK' => 'Air Minum Kimia',
    'AM' => 'Air Minum',
    'ALB' => 'Air Limbah Bakteorologi',
    'ALT' => 'Alat Makan dan Usap Alat Makan',
    'AKK' => 'Alat Makan dan Usap Alat Makan'
  ];

  public function __construct()
  {
    $this->middleware('auth');
  }


  /**
   * Retrieves the number of lab records for the given lab key in the current year.
   *
   * @param string $lab_key The lab key to filter records by.
   * @return int The number of lab records for the given lab key in the current year.
   */
  public function getLabNumByLabKey($lab_key,$id,$is_makanan=false): int
  {

    // dd($id);

    $start_num= StartNum::join('ms_laboratorium', function ($join) {
      $join->on('ms_laboratorium.kode_laboratorium', '=', 'ms_start_number.code_lab_start_number')
        ->whereNull('ms_laboratorium.deleted_at')
        ->whereNull('ms_start_number.deleted_at');
    })->where('id_laboratorium',$lab_key)->first();



    $permohonan_uji = PermohonanUji::findOrfail($id);


    if ($is_makanan) {
      # code...
      $start_num= StartNum::where('code_lab_start_number','MAK-MIN')->first();

      // dd($start_num);




      $lab_num =
      LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
        // ->where('lab_id', $lab_key)
        ->where('is_makanan',1)
        ->where('lab_id', $lab_key)
        ->where('tb_lab_num.created_at','<=',$permohonan_uji->created_at)
        // ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
        //     $query->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
        //     ->where('is_makanan',1)
        //     ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
        // })
        ->where('year_lab_num', '=', date('Y'))
        // ->tb_samples.created_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
        ->count();


      if ($lab_num>0) {
        # code...
        // dd($lab_num);


        return LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
        ->where('created_at','<',$permohonan_uji->created_at)
        ->where('is_makanan',1)
        ->where('lab_id', $lab_key)
        ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
          $query->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
          ->where('is_makanan',1)
          ->where('lab_id', $lab_key)
          ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
      })
        ->max('lab_number');

      }else{
        return LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
        ->where('is_makanan',1)
        ->where('lab_id', $lab_key)
          ->count() +$start_num->count_start_number;

      }


    }else{

      $lab_num=LabNum::where('lab_id', $lab_key)
      // ->where(function($query) use ($permohonan_uji,$lab_key) {

      //   $query

      //   // ->whereDay('tb_lab_num.created_at', '<=',(int) date('d'));
      //   // ->where(DB::raw('HOUR(tb_lab_num.created_at)'), '<=', date('H'));
      // })

      ->where(DB::raw('YEAR(tb_lab_num.created_at)'), '=', (int)date('Y'))
      ->whereMonth('tb_lab_num.created_at', '>=', (int) date('M'))
      // ->where(DB::raw('MOUNT(tb_lab_num.created_at)'), '<=', date('M'))

    //   ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
    //     $query->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
    //     ->where('lab_id', $lab_key)
    //     ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
    // })

    // ->where('year_lab_num', '=', date('Y'))
    // ->where(DB::raw('YEAR(tb_lab_num.created_at)'), '=', date('Y'))
      ->count();




      // dd(  $lab_num);

    //   dd(LabNum::where('lab_id', $lab_key)
    //   ->where('created_at','<=',$permohonan_uji->created_at)
    //   ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
    //     $query
    //     ->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
    //     ->where('lab_id', $lab_key)
    //     ->where(DB::raw('YEAR(tb_lab_num.created_at)'), '=', date('Y'));
    // })
    // ->where(DB::raw('YEAR(tb_lab_num.created_at)'), '=', date('Y'))
    // ->first());

      // dd($lab_num);
      if ($lab_num>0  ) {
        # code...
        // dd($lab_num);

        $last_lab_num_permohonan_uji=LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))

          ->where('year_lab_num', '=', date('Y'))

          // ->where('created_at','<',$permohonan_uji->created_at)
          ->where('permohonan_uji_id',$permohonan_uji->id_permohonan_uji)
          ->where('lab_id', $lab_key)
        //   ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
        //     $query
        //     // ->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
        //     ->where('lab_id', $lab_key)
        //     ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
        // })
          ->max('lab_number');

        $last_lab_num = LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))

          ->where('year_lab_num', '=', date('Y'))

          // ->where('created_at','<',$permohonan_uji->created_at)
          ->where('lab_id', $lab_key)
          ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
            $query
            // ->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
            ->where('lab_id', $lab_key)
            ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
        })
          ->max('lab_number');


          // if ($last_lab_num_permohonan_uji) {
          //  re
          // }




      //   return LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))

      //   ->where('year_lab_num', '=', date('Y'))

      //   ->where('created_at','<',$permohonan_uji->created_at)
      //   ->where('lab_id', $lab_key)
      //   ->orWhere(function($query) use ($permohonan_uji,$lab_key) {
      //     $query->where('permohonan_uji_id','<=',$permohonan_uji->id_permohonan_uji)
      //     ->where('lab_id', $lab_key)
      //     ->where(DB::raw('YEAR(created_at)'), '=', date('Y'));
      // })
      //   ->max('lab_number');

        // if ($lab_key=="3416ca19-6c69-4e5f-a004-ae8275de7644") {
        //   # code...
        //   dd($last_lab_num);
        // }

        return $last_lab_num;

      }else{


        if (date('Y')==$start_num->year_start_number) {
          # code...
          return LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
          ->where('lab_id', $lab_key)
          ->where('year_lab_num', '=', date('Y'))
          ->count() +$start_num->count_start_number;
        }else{
          return 0;
        }


      }
    }


  }

  public function getCodeSample($count)
  {

    $code_number = str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);
    $code_type = '...';

    $code_datetime = now();
    $code_year = $code_datetime->format('Y');

    $code = implode('/', [$code_number, $code_type, $code_year]);
    return $code;
  }

  public function getNewNumberSequence(string $lab_key ,$id,$is_makanan = false)
  {
    return $this->getLabNumByLabKey($lab_key,$id,$is_makanan);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request, $id)
  {
    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', $id)->join('ms_customer', function ($join) {
      $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
        ->whereNull('ms_customer.deleted_at')
        ->whereNull('tb_permohonan_uji.deleted_at');
    })->first();

    $check_mikro = Sample::where('permohonan_uji_id', '=', $id)
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
      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('id_laboratorium')
      ->distinct('id_laboratorium')
      ->where('id_laboratorium', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->get();


    $packet_prints = Sample::where('permohonan_uji_id', '=', $id)
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
      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('ms_jenis_makanan', function ($join) {
        $join->on('ms_jenis_makanan.id_jenis_makanan', '=', 'tb_samples.jenis_makanan_id')
          ->whereNull('tb_samples.deleted_at')
          ->whereNull('ms_jenis_makanan.deleted_at');
      })
      ->leftjoin('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')

          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('id_laboratorium', 'ms_jenis_makanan.*', 'typesample_samples', 'ms_sample_type.id_sample_type', 'ms_sample_type.name_sample_type', 'tb_samples.jenis_makanan_id')
      ->distinct('id_laboratorium', 'ms_sample_type.id_sample_type', 'tb_samples.jenis_makanan_id')
      ->where('id_laboratorium', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->get();


    if (request()->ajax()) {
      $datas = Sample::where('permohonan_uji_id', '=', $id)
        ->leftjoin('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->leftjoin('ms_laboratorium', function ($join) {
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
        ->leftjoin('tb_sample_penerimaan', function ($join) {
          $join->on('tb_sample_penerimaan.id_sample_penerimaan', '=', DB::raw('(SELECT id_sample_penerimaan FROM tb_sample_penerimaan WHERE tb_sample_penerimaan.sample_id = tb_samples.id_samples AND tb_sample_penerimaan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        ->leftjoin('ms_jenis_makanan', function ($join) {
          $join->on('ms_jenis_makanan.id_jenis_makanan', '=', 'tb_samples.jenis_makanan_id')
            ->whereNull('tb_samples.deleted_at')
            ->whereNull('ms_jenis_makanan.deleted_at');
        })

        ->leftjoin('tb_sample_penanganan', function ($join) use ($id) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pelaporan_hasil', function ($join) {
          $join->on('tb_pelaporan_hasil.id_pelaporan_hasil', '=', DB::raw('(SELECT id_pelaporan_hasil FROM tb_pelaporan_hasil WHERE tb_pelaporan_hasil.sample_id = tb_samples.id_samples AND tb_pelaporan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            ->whereNull('tb_pelaporan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_verifikasi_hasil', function ($join) {
          $join->on('tb_verifikasi_hasil.id_verifikasi_hasil', '=', DB::raw('(SELECT id_verifikasi_hasil FROM tb_verifikasi_hasil WHERE tb_verifikasi_hasil.sample_id = tb_samples.id_samples AND tb_verifikasi_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            ->whereNull('tb_verifikasi_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.id_sample_penanganan',
          'tb_sample_penanganan.penyimpanan_sample',
          'tb_sample_penanganan.date_checking',
          'tb_sample_penanganan.date_done_estimation_labs',
          'tb_sample_penanganan.destroyed_sample_date',
          'tb_sample_penanganan.penanganan_sample_date',
          'ms_jenis_makanan.id_jenis_makanan',
          'ms_jenis_makanan.name_jenis_makanan',
          'ms_jenis_makanan.olahan_jenis_makanan',
          'ms_jenis_makanan.price_jenis_makanan',
          'tb_pengesahan_hasil.id_pengesahan_hasil',
          'tb_pengesahan_hasil.pengesahan_hasil_date',
          'tb_pelaporan_hasil.id_pelaporan_hasil',
          'tb_pelaporan_hasil.pelaporan_hasil_date',
          'tb_verifikasi_hasil.id_verifikasi_hasil',
          'tb_verifikasi_hasil.verifikasi_hasil_date',
          'tb_sample_penerimaan.id_sample_penerimaan',
          'tb_sample_penerimaan.wadah_id',
          'tb_sample_penerimaan.wadah_sampel_other',
          'tb_sample_penerimaan.pengawet',
          'tb_sample_penerimaan.pengawet_other',
          'tb_sample_penerimaan.volume',
          'tb_sample_penerimaan.unit_id',
          'tb_sample_penerimaan.kondisi_sample',
          'tb_sample_penerimaan.validation_sample',
          'tb_sample_penerimaan.penerimaan_sample_date',
          'id_laboratorium',
          'nama_laboratorium',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'codesample_samples',
          'id_samples',
          'tb_samples.created_at'
        )
        ->distinct('id_laboratorium')
        ->orderBy(  'codesample_samples')
        ->latest()
        ->get();

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['nama_laboratorium']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['last_status']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['name_sample_type']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['codesample_samples']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['last_status']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('jenis_makanan', function ($data) {
          return isset($data->name_jenis_makanan) ? $data->name_jenis_makanan : '';
        })
        ->addColumn("last_status", function ($data) {
          $verificationActivities = VerificationActivitySample::query()->where('id_sample', '=', $data->id_samples)->join('ms_verification_activities', 'tb_verification_activity_samples.id_verification_activity', '=', 'ms_verification_activities.id')->get();
          $label_status = '';
          $step = [];
          if ($verificationActivities->isNotEmpty()) {
            foreach ($verificationActivities as $verificationActivity) {
              $step[$verificationActivity->id_verification_activity] = $verificationActivity;
            }

            if (!empty($step)) {
              $max = max(array_keys($step));
              switch ($max) {
                case 1:
                  $label_status = '<label class="badge badge-primary badge-pill w-75">' . $step[$max]->name . '</label>';
                  break;
                case 2:
                  $label_status = '<label class="badge badge-warning badge-pill w-75">' . $step[$max]->name . '</label>';
                  break;
                case 3:
                  $label_status = '<label class="badge badge-danger badge-pill w-75">' . $step[$max]->name . '</label>';
                  break;
                case 4:
                  $label_status = '<label class="badge badge-info badge-pill w-75">' . $step[$max]->name . '</label>';
                  break;
                case 5:
                  $label_status = '<label class="badge badge-success badge-pill w-75">' . $step[$max]->name . '</label>';
                  break;
                default:
                  break;
              }
            }
          }



          return $label_status;
        })
        ->addColumn("count_method", function ($data) {

          $prefixMethod = $sampleMethod = '';

          foreach ($data->samplemethod as $mytable) {

            // if($mytable)
            if ($data->id_laboratorium == $mytable['laboratorium_id']) {
              $sampleMethod .= $prefixMethod . $mytable['method']['params_method'];
              $prefixMethod = '<br><br>';
            }
          }

          return $sampleMethod;
        })
        ->addColumn('action', function ($data) use ($id) {
          $editButton = '';
          $deleteButton = '';
          $printButton = '';
          $informConcernButton = '';
          $verifikasiButton2 = '';
          $duplicate = '<a href="' . route('elits-samples.store-duplicate', [$data->id_samples ?? 0, $data->id_laboratorium ?? 0]) . '" class="dropdown-item" title="Duplicate">Duplicate</a> ';

          if (getAction('update') || getAction('delete')) {
            if (getAction('update')) {
              $editButton = '<a href="' . route('elits-samples.edit', [$data->id_samples]) . '" class="dropdown-item" title="Edit">Edit</a> ';
            }

            if (getSpesialAction(request()->segments(1), 'verification-sampel', '')) {
              $verifikasiButton2 = '<a href="' . route('elits-samples.verification-2', [$data->id_samples, $data->id_laboratorium]) . '" class="dropdown-item" title="Verifikasi">Verifikasi</a> ';
            }

            if (isset($data->id_laboratorium)) {
              if ($data->id_laboratorium !== 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5') {

                $printButton = '<a data-href="' . route('elits-release.printLHU', [$data->id_samples, $data->id_laboratorium]) . '" class="dropdown-item pointer" title="Print" data-toggle="modal" data-target="#signOptionModal">Print</a> ';
              }
              $informConcernButton = '<a href="' . route('elits-release.print-inform-concern', [$data->id_samples, $data->id_laboratorium]) . '" class="dropdown-item" title="Inform Concern">Print Informed Consent</a> ';
            } else {
              $printButton = '<a data-href="' . route('elits-release.printLHU', [$data->id_samples, 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5']) . '" class="dropdown-item pointer" title="Print" data-toggle="modal" data-target="#signOptionModal">Print</a> ';
            }

            if (getAction('delete')) {
              $deleteButton = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_samples  . '" data-nama="' . $data->codesample_samples . '" title="Hapus">Hapus</a> ';
            }
          }

          $button = '<div class="dropdown show m-1">
                              <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Aksi
                              </a>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                  ' . $printButton . '

                                  ' . $informConcernButton . '

                                  ' . $verifikasiButton2 . '

                                  ' . $editButton . '

                                  ' . $duplicate . '

                                  ' . $deleteButton . '
                              </div>
                          </div>';

          return $button;
        })
        ->rawColumns(['jenis_makanan', 'last_status', 'action', 'count_method'])
        ->addIndexColumn() //increment
        ->make(true);
    }


    $pudam = Sample::where('permohonan_uji_id', '=', $id)
      ->where('kode_laboratorium', 'KIM')
      ->distinct('typesample_samples')
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
      ->join('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM
            tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND
            tb_pengesahan_hasil.deleted_at is NULL AND tb_samples.deleted_at is NULL LIMIT 1)'))
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('tb_baku_mutu', function ($join) {
        $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
          ->on('tb_baku_mutu.sampletype_id', '=', 'tb_samples.typesample_samples')
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('ms_unit as unit_baku_mutu', function ($join) {
        $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
          ->whereNull('unit_baku_mutu.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->where('name_sample_type', 'PUDAM SISA CHLOR + FISIKA')
      ->select('ms_sample_type.name_sample_type')
      ->get();




    $makmin = Sample::where('permohonan_uji_id', '=', $id)
      ->where('kode_laboratorium', 'KIM')
      ->distinct('typesample_samples')
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
      ->join('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM
            tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND
            tb_pengesahan_hasil.deleted_at is NULL AND tb_samples.deleted_at is NULL LIMIT 1)'))
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('tb_baku_mutu', function ($join) {
        $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
          ->on('tb_baku_mutu.sampletype_id', '=', 'tb_samples.typesample_samples')
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('ms_unit as unit_baku_mutu', function ($join) {
        $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
          ->whereNull('unit_baku_mutu.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->where('name_sample_type', 'Makanan/Minuman/Lainnya')
      ->select('ms_sample_type.name_sample_type')
      ->get();

    // $pudam->add($makmin);
    // dd($pudam);
    // if()
    // dd(count($pudam[0]));


    return view('masterweb::module.admin.laboratorium.sample.list', compact('makmin', 'permohonan_uji', 'check_mikro', 'packet_prints', 'pudam', 'id'));
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

  public function getSamplePagination(Request $request)
  {


    $auth = Auth()->user();


    $draw = $request->get('draw');
    $start = $request->get("start");

    $rowperpage = $request->get("length"); // Rows display per page

    $columnIndex_arr = $request->get('order');
    $columnName_arr = $request->get('columns');
    $order_arr = $request->get('order');
    $search_arr = $request->get('search');

    $columnIndex = $columnIndex_arr[0]['column']; // Column index

    if ($columnIndex != 0) {
      $columnName = $columnName_arr[$columnIndex]['data']; // Column name
      $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    } else {
      $columnName = 'created_at';
      $columnSortOrder = 'desc';
    }


    $searchValue = $search_arr['value']; // Search value

    // Total records


    if ($auth->level == "0e6da765-0f3a-4471-9e1d-6af257e60a70") {
      $totalRecords = Sample::select('count(*) as allcount')
        ->where('tb_samples.status', '=', '0')
        ->count();

      $totalRecordswithFilter = Sample::select('count(*) as allcount')
        ->where('tb_samples.status', '=', '0')
        ->where('codesample_samples', 'like', '%' . $searchValue . '%')
        ->count();

      // Fetch records
      $samples = Sample::orderBy($columnName, $columnSortOrder)
        ->where('tb_samples.codesample_samples', 'like', '%' . $searchValue . '%')
        ->where('tb_samples.status', '=', '0')
        ->select('tb_samples.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    } else {
      $totalRecords = Sample::select('count(*) as allcount')->count();
      $totalRecordswithFilter = Sample::select('count(*) as allcount')->where('codesample_samples', 'like', '%' . $searchValue . '%')->count();

      // Fetch records
      $samples = Sample::orderBy($columnName, $columnSortOrder)
        ->where('tb_samples.codesample_samples', 'like', '%' . $searchValue . '%')
        ->select('tb_samples.*')

        ->skip($start)
        ->take($rowperpage)
        ->get();
    }


    $data_arr = array();

    $no = $start + 1;

    foreach ($samples as $samples) {
      $id_samples = $samples->id_samples;
      $user_samples = $samples->user_samples;
      $customer_samples = $samples->customer_samples;
      $customer_samples = Customer::where("id_customer", $customer_samples)->first();
      $qr = QrCode::size(100)->generate($samples->codesample_samples);
      $codesample_samples = $samples->codesample_samples . '<br><br>' . $qr;

      $datelab_samples = $samples->datelab_samples;
      $datelab_samples = Carbon::createFromFormat('Y-m-d H:i:s', $datelab_samples)->format('d/m/Y');




      if ($samples->status == '0') {
        $status =
          '<a href="/elits-deligations/' . $samples->id_samples . '">
                        <button type="button" class="btn btn-outline-warning">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            Proses Delegation
                        </button>
                    </a>';
      } else if ($samples->status == '1') {
        $status =
          '<a href="/elits-samples/analys/' . $samples->id_samples . '">
                    <button type="button" class="btn btn-outline-primary">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        Proses Analisa
                    </button>
                </a>';
      } else if ($samples->status == '2') {
        $status =
          '<a href="/elits-samples/analys/' . $samples->id_samples . '">
                    <button type="button" class="btn btn-outline-light">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        Proses Invoice
                    </button>
                </a>';
      } else {
        $status =
          '<a href="/elits-release/printLHU/' . $samples->id_samples . '">
                    <button type="button" class="btn btn-outline-success">
                        <i class="fa fa-users" aria-hidden="true"></i>
                        Rilis Sample
                    </button>
                </a>';
      }


      $data_arr[] = array(
        "number" => $no,
        "codesample_samples" => $codesample_samples,
        "id_samples" => $id_samples,
        "user_samples" => $user_samples,
        "customer_samples" => $customer_samples->name_customer,
        "status" => $status,
        "datelab_samples" => $datelab_samples,
      );
      $no++;
    }




    $response = array(
      "draw" => intval($draw),
      "iTotalRecords" => $totalRecords,
      "iTotalDisplayRecords" => $totalRecordswithFilter,
      "aaData" => $data_arr
    );

    echo json_encode($response);
    exit;

    // return view('masterweb::module.admin.laboratorium.sample.list',compact('user','samples'));


  }

  /**
   * Get the sample types code and their corresponding descriptions.
   *
   * @return array
   */
  public function getSampleTypes(): array
  {
    $sample_types = [
      'AMB' => 'Air Minum Baktereologi',
      'ABB' => 'Air Bersih Bakteorologi',
      'AMF' => 'Air Minum Fisika',
      'AMK' => 'Air Minum Kimia',
      'ALB' => 'Air Limbah Bakteorologi',
      'ALT' => 'Alat Makan dan Usap Alat Makan',
      'AKK' => 'Alat Makan dan Usap Alat Makan'
    ];

    return $sample_types;
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */

  function numberToRomanRepresentation($number)
  {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
      foreach ($map as $roman => $int) {
        if ($number >= $int) {
          $number -= $int;
          $returnValue .= $roman;
          break;
        }
      }
    }
    return $returnValue;
  }

  public function create($id, $id_lab = null)
  {
    //get auth user
    $user = Auth()->user();
    if ($id_lab == null) {

      // $count = Sample::count();
      if (Sample::where(DB::raw('YEAR(date_sending)'), '=', date('Y'))->count() == 0) {
        $count = 0;
      } else {
        $count = Sample::where(DB::raw('YEAR(date_sending)'), '=', date('Y'))->orderBy('count_id', 'DESC', 'DESC')->first()->count_id;
      }



      $users = User::all();
      $packets = Packet::where('id_packet', '!=', '0')->orderBy('created_at')->get();
      $all_jenis_makanan = JenisMakanan::all();

      $laboratoriums = Laboratorium::where('kode_laboratorium', '!=', 'KLI')->get();
      $programs = Program::orderBy('created_at')->get();

      $permohonan_uji = PermohonanUji::find($id);


      $data_methods = array();
      $code_samples = array();
      $lab_keys = array();

      foreach ($laboratoriums as $laboratorium) {
        $code_samples[strtolower($laboratorium->nama_laboratorium)] = $this->getCodeSample($this->getLabNumByLabKey($laboratorium->id_laboratorium,$id));
        $lab_keys[strtolower($laboratorium->nama_laboratorium)] = $laboratorium->id_laboratorium;

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
          })
          ->get();
        foreach ($laboratoriummethods as $laboratoriummethod) {
          //    print_r($laboratoriummethod->params_method);
          array_push(
            $data_methods[$i]->method,
            (object) array(
              'name_method' => $laboratoriummethod->params_method,
              'id_method' => $laboratoriummethod->id_method,
              'price_method' => $laboratoriummethod->price_total_method
            )
          );
        }

        $i++;
      }


      // dd($data_methods);







      // K/ tanggal/0001
      $containers = Container::where('id_container', '!=', '0')->get();
      $sampletypes = SampleType::orderBy('created_at')->get();



      $code_karanganyar = 'NK/' . date("Ymd", time()) . '/' . str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);

      $code_number = str_pad((int)($count + ($i + 1)), 4, '0', STR_PAD_LEFT);
      // $sample_types = $this->getSampleTypes();

      $code_type = '...';

      $code_datetime = now();
      $code_year = $code_datetime->format('Y');
      $code_month = $this->numberToRomanRepresentation($code_datetime->format('m'));

      $code = implode('/', [$code_number, $code_type, $code_month, $code_year]);



      $units = Unit::all();
      return view('masterweb::module.admin.laboratorium.sample.add', compact('permohonan_uji','id','programs', 'all_jenis_makanan', 'units', 'user', 'data_methods', 'containers', 'packets', 'sampletypes', 'code', 'code_samples', 'users', 'lab_keys'));
    } else {
      return abort(404);
    }
    //get all menu public
  }

  public function analys($id, $id_method = null)
  {


    $auth = Auth()->user();

    $samples = Sample::where("id_samples", $id)->first();


    $customer = Customer::where("id_customer", $samples->customer_samples)->first();


    if ($auth->level == "3382abf2-8518-42f9-91e1-096f25da8ae8") {
      $method = SampleMethod::join('ms_method', 'ms_method.id_method', '=', 'ms_samples_method.id_method')
        ->join('tb_delegation', function ($join) {
          $join->on('tb_delegation.id_method', '=', 'ms_samples_method.id_method');
          $join->on('tb_delegation.id_samples', '=', 'ms_samples_method.id_samples');
        })
        ->where("ms_samples_method.id_samples", $id)
        ->where('tb_delegation.id_delegation', '=', $auth->id)
        ->where('ms_method.deleted_at', '=', null)
        ->where('tb_delegation.deleted_at', '=', null)
        ->select('ms_samples_method.*', 'ms_method.*', 'tb_delegation.*')
        ->get();
    } else {
      $method = SampleMethod::where("id_samples", $id)
        ->leftJoin('ms_method', 'ms_method.id_method', '=', 'ms_samples_method.id_method')
        ->where('ms_method.deleted_at', '=', null)
        ->get();
    }



    return view('masterweb::module.admin.laboratorium.sample.analys', compact('samples', 'method', 'customer', 'id_method'));
  }

  public function verification($id, $idlab = null)
  {
    if ($idlab == null) {
      abort(404);
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
        ->leftjoin('tb_sample_penerimaan', function ($join) {
          $join->on('tb_sample_penerimaan.id_sample_penerimaan', '=', DB::raw('(SELECT id_sample_penerimaan FROM tb_sample_penerimaan WHERE tb_sample_penerimaan.sample_id = tb_samples.id_samples AND tb_sample_penerimaan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) use ($idlab) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples  AND tb_sample_penanganan.laboratorium_id ="' . $idlab . '" AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pelaporan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pelaporan_hasil.id_pelaporan_hasil', '=', DB::raw('(SELECT id_pelaporan_hasil FROM tb_pelaporan_hasil WHERE tb_pelaporan_hasil.sample_id = tb_samples.id_samples AND tb_pelaporan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pelaporan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            // $join->on('tb_pelaporan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            // ->on('tb_samples.id_samples', '=', 'tb_pelaporan_hasil.sample_id')
            ->whereNull('tb_pelaporan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pengetikan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pengetikan_hasil.id_pengetikan_hasil', '=', DB::raw('(SELECT id_pengetikan_hasil FROM tb_pengetikan_hasil WHERE tb_pengetikan_hasil.sample_id = tb_samples.id_samples AND tb_pengetikan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pengetikan_hasil.deleted_at  is NULL AND  tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->on('tb_samples.id_samples', '=', 'tb_pengetikan_hasil.sample_id')
            ->whereNull('tb_pengetikan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_verifikasi_hasil', function ($join) use ($idlab) {
          $join->on('tb_verifikasi_hasil.id_verifikasi_hasil', '=', DB::raw('(SELECT id_verifikasi_hasil FROM tb_verifikasi_hasil WHERE tb_verifikasi_hasil.sample_id = tb_samples.id_samples AND tb_verifikasi_hasil.laboratorium_id ="' . $idlab . '" AND tb_verifikasi_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_verifikasi_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pengesahan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->first();


      $year_now = Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->format('Y');


      $first_year = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
        // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
        ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now)
        ->limit(1)
        ->orderBy('tb_samples.created_at', 'asc')
        ->first();

      if ((int)$sample->count_id == (int) $first_year->count_id) {
        $previewssample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now - 1)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'desc')
          ->first();
      } else {
        $previewssample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          ->where('tb_samples.count_id', '<', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', $year_now)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'desc')
          ->first();
      }


      $last_year = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
        // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
        ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now)
        ->limit(1)
        ->orderBy('tb_samples.created_at', 'desc')
        ->first();




      // dd($previewssample->date_sending);
      // dd(Carbon::createFromFormat('Y-m-d H:i:s', $previewssample->date_sending)->format('Y'));
      if ((int)$sample->count_id == (int)$last_year->count_id) {

        $nextsample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now + 1)
          // ->where('tb_samples.count_id', '>', (int)$sample->count_id)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'asc')
          ->first();
      } else {
        $nextsample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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

          ->where('tb_samples.count_id', '>', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', $year_now)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'asc')
          ->first();
      }



      // dd($previewssample);

      // dd($sample);

      $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
        ->where('sample_id', '=', $id)
        ->orderBy('ms_method.created_at')
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })
        ->get();

      $laboratoriumprogress = LaboratoriumProgress::where('laboratorium_id', $idlab)->orderBy('order_sort', 'asc')->get();

      $laboratoriumsampleanalitikprogress = SampleAnalitikProgress::where('tb_sample_analitik_progress.laboratorium_id', $idlab)
        ->where('tb_sample_analitik_progress.sample_id', $id)
        ->orderBy('tb_laboratorium_progress.order_sort', 'asc')
        ->join('tb_laboratorium_progress', function ($join) {
          $join->on('tb_laboratorium_progress.id_laboratorium_progress', '=', 'tb_sample_analitik_progress.laboratorium_progress_id')
            ->on('tb_laboratorium_progress.laboratorium_id', '=', 'tb_sample_analitik_progress.laboratorium_id')
            ->whereNull('tb_laboratorium_progress.deleted_at')
            ->whereNull('tb_sample_analitik_progress.deleted_at');
        })
        ->get();

      // dd($laboratoriumsampleanalitikprogress);

      // dd($laboratoriumsampleanalitikprogress);
      $penerimaan_sample = PenerimaanSample::where('laboratorium_id', $idlab)->first();
      // dd($sample);

      $lab_num = LabNum::where('sample_id', $sample->id_samples)
        ->where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->where('sample_type_id', $sample->typesample_samples)
        ->first();


      return view('masterweb::module.admin.laboratorium.sample.verification', compact(
        'sample',
        'previewssample',
        'nextsample',
        'laboratoriummethods',
        'laboratoriumprogress',
        'penerimaan_sample',
        'laboratoriumsampleanalitikprogress',
        'lab_num'
      ));
    }
  }

  public function verification2($id, $idlab = null)
  {
    if ($idlab == null) {
      abort(404);
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
        ->leftjoin('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penerimaan', function ($join) {
          $join->on('tb_sample_penerimaan.id_sample_penerimaan', '=', DB::raw('(SELECT id_sample_penerimaan FROM tb_sample_penerimaan WHERE tb_sample_penerimaan.sample_id = tb_samples.id_samples AND tb_sample_penerimaan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) use ($idlab) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples  AND tb_sample_penanganan.laboratorium_id ="' . $idlab . '" AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pelaporan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pelaporan_hasil.id_pelaporan_hasil', '=', DB::raw('(SELECT id_pelaporan_hasil FROM tb_pelaporan_hasil WHERE tb_pelaporan_hasil.sample_id = tb_samples.id_samples AND tb_pelaporan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pelaporan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
            // $join->on('tb_pelaporan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            // ->on('tb_samples.id_samples', '=', 'tb_pelaporan_hasil.sample_id')
            ->whereNull('tb_pelaporan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pengetikan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pengetikan_hasil.id_pengetikan_hasil', '=', DB::raw('(SELECT id_pengetikan_hasil FROM tb_pengetikan_hasil WHERE tb_pengetikan_hasil.sample_id = tb_samples.id_samples AND tb_pengetikan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pengetikan_hasil.deleted_at  is NULL AND  tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->on('tb_samples.id_samples', '=', 'tb_pengetikan_hasil.sample_id')
            ->whereNull('tb_pengetikan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_verifikasi_hasil', function ($join) use ($idlab) {
          $join->on('tb_verifikasi_hasil.id_verifikasi_hasil', '=', DB::raw('(SELECT id_verifikasi_hasil FROM tb_verifikasi_hasil WHERE tb_verifikasi_hasil.sample_id = tb_samples.id_samples AND tb_verifikasi_hasil.laboratorium_id ="' . $idlab . '" AND tb_verifikasi_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_verifikasi_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_pengesahan_hasil', function ($join) use ($idlab) {
          $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.laboratorium_id ="' . $idlab . '" AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'tb_pengesahan_hasil.*', 'tb_pengesahan_hasil.*', 'tb_verifikasi_hasil.*', 'tb_pengetikan_hasil.*', 'tb_pelaporan_hasil.*', 'tb_sample_penanganan.*', 'tb_sample_penerimaan.*', 'ms_sample_type.name_sample_type', 'ms_sample_type.code_sample_type', 'ms_sample_type.typesample_type', 'ms_laboratorium.*')
        ->first();

      // dd($sample);


      $year_now = Carbon::createFromFormat('Y-m-d H:i:s', $sample->date_sending)->format('Y');


      $first_year = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
        // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
        ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now)
        ->limit(1)
        ->orderBy('tb_samples.created_at', 'asc')
        ->first();

      if ((int)$sample->count_id == (int) $first_year->count_id) {
        $previewssample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now - 1)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'desc')
          ->first();
      } else {
        $previewssample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          ->where('tb_samples.count_id', '<', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', $year_now)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'desc')
          ->first();
      }


      $last_year = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
        // ->where('tb_samples.count_id', '<', (int)$sample->count_id)
        ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now)
        ->limit(1)
        ->orderBy('tb_samples.created_at', 'desc')
        ->first();




      // dd($previewssample->date_sending);
      // dd(Carbon::createFromFormat('Y-m-d H:i:s', $previewssample->date_sending)->format('Y'));
      if ((int)$sample->count_id == (int)$last_year->count_id) {

        $nextsample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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
          ->where(DB::raw('YEAR(date_sending)'), '=', (int)$year_now + 1)
          // ->where('tb_samples.count_id', '>', (int)$sample->count_id)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'asc')
          ->first();
      } else {
        $nextsample = Sample::where('ms_laboratorium.id_laboratorium', '=', $idlab)
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

          ->where('tb_samples.count_id', '>', (int)$sample->count_id)
          ->where(DB::raw('YEAR(date_sending)'), '=', $year_now)
          ->limit(1)
          ->orderBy('tb_samples.created_at', 'asc')
          ->first();
      }



      // dd($previewssample);

      // dd($sample);

      $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
        ->where('sample_id', '=', $id)
        ->orderBy('ms_method.created_at')
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })
        ->get();

      $laboratoriumprogress = LaboratoriumProgress::where('laboratorium_id', $idlab)->orderBy('order_sort', 'asc')->get();

      $laboratoriumsampleanalitikprogress = SampleAnalitikProgress::where('tb_sample_analitik_progress.laboratorium_id', $idlab)
        ->where('tb_sample_analitik_progress.sample_id', $id)
        ->orderBy('tb_laboratorium_progress.order_sort', 'asc')
        ->join('tb_laboratorium_progress', function ($join) {
          $join->on('tb_laboratorium_progress.id_laboratorium_progress', '=', 'tb_sample_analitik_progress.laboratorium_progress_id')
            ->on('tb_laboratorium_progress.laboratorium_id', '=', 'tb_sample_analitik_progress.laboratorium_id')
            ->whereNull('tb_laboratorium_progress.deleted_at')
            ->whereNull('tb_sample_analitik_progress.deleted_at');
        })
        ->get();

      // dd($laboratoriumsampleanalitikprogress);

      // dd($laboratoriumsampleanalitikprogress);
      $penerimaan_sample = PenerimaanSample::where('laboratorium_id', $idlab)->first();
      // dd($sample);

      $lab_num = LabNum::where('sample_id', $sample->id_samples)
        ->where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->where('sample_type_id', $sample->typesample_samples)
        ->first();


      $permohonanUji = PermohonanUji::query()->where('id_permohonan_uji', '=', $sample->permohonan_uji_id)->first();

      $verificationActivitySamples = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->get();
      $listVerifications = [];
      foreach ($verificationActivitySamples as  $verificationActivitySample) {
        $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
      }

      $verificationActivity = VerificationActivity::all();
      // dd(  $verificationActivity);


      $labAnalitikProgres = null;
      foreach ($laboratoriumprogress as $labProgres) {
        if ($labProgres->id_laboratorium_progress == "bc2850f5-4ec4-450f-a727-2b1428c861d9" or $labProgres->id_laboratorium_progress == "57f7f491-288c-4c48-afa8-a9669bb62180") {
          $labAnalitikProgres = $labProgres;
        }
      }

      $verifikasiHasil = VerifikasiHasil::query()->where('sample_id', '=', $sample->id_samples)->first();
      $pengesahanHasil = PengesahanHasil::query()->where('sample_id', '=', $sample->id_samples)->first();

      return view('masterweb::module.admin.laboratorium.sample.verification-2', compact(
        'sample',
        'previewssample',
        'nextsample',
        'verificationActivity',
        'laboratoriummethods',
        'laboratoriumprogress',
        'penerimaan_sample',
        'laboratoriumsampleanalitikprogress',
        'lab_num',
        'permohonanUji',
        'listVerifications',
        'labAnalitikProgres',
        'verifikasiHasil',
        'pengesahanHasil',
      ));
    }
  }

  /**
   * Fungsi untuk mengecek nik atau password petugas di verifikasi
   * return string true jika nik dan password tidak null atau string kosong
   *  **/
  public function checkNikAndPassword($namaPetugas): string
  {
    $petugas = Petugas::query()->where('nama', '=', $namaPetugas)->first();

    if (isset($petugas)){
      if ($petugas->nik != null and $petugas->nik != "" and $petugas->password != null and $petugas->password != ""){
        return "true";
      }
    }

    return "false";
  }

  /**
   * fungsi untuk menyimpan nik dan password petugas di tabel ms_petugas
   */
  public function saveNikAndPassword(Request $request, $namaPetugas)
  {
    $request->validate([
      'nik' => 'required',
      'password' => 'required'
    ]);

    $petugas = Petugas::query()->where('nama', '=', $namaPetugas)->first();

    if (isset($petugas)) {
      $petugas->nik = $request->input('nik');
      $petugas->password = $request->input('password');

      if ($petugas->save()){
        return "true";
      }
    }else{
      $newPetugas = new Petugas();
      $newPetugas->nama = $namaPetugas;
      $newPetugas->nik = $request->input('nik');
      $newPetugas->password = $request->input('password');

      if ($newPetugas->save()){
        return "true";
      }
    }

    return "false";
  }


  public function verificationAnalytic(Request $request, $id_sample)
  {

    $request->validate([
      'verification_step' => 'required',
      'start_date' => 'required',
      'stop_date' => 'required',
      'nama_petugas' => 'required'
    ]);


    // updated if exist
    $verificationActivitySampleUpdated = VerificationActivitySample::query()->where('id_sample', '=', $id_sample)->where('id_verification_activity', '=', $request->get('verification_step'))->first();
    // dd($verificationActivitySampleUpdated);
    if (isset($verificationActivitySampleUpdated)) {
      $start_date = Carbon::createFromFormat('d/m/Y H:i', $request->get('start_date'))->format('Y-m-d H:i:s');
      $stop_date = Carbon::createFromFormat('d/m/Y H:i', $request->get('stop_date'))->format('Y-m-d H:i:s');
      $verificationActivitySampleUpdated->start_date =  $start_date;
      $verificationActivitySampleUpdated->stop_date =  $stop_date;
      $verificationActivitySampleUpdated->nama_petugas = $request->get('nama_petugas');

      $verificationActivitySampleUpdated->save();
      return redirect()->back();
    }

    $verificationActivitySample = new VerificationActivitySample();
    $verificationActivitySample->id = Uuid::uuid4()->toString();
    $verificationActivitySample->id_sample = $id_sample;
    $verificationActivitySample->id_verification_activity = $request->get('verification_step');


    $start_date = Carbon::createFromFormat('d/m/Y H:i', $request->get('start_date'))->format('Y-m-d H:i:s');
    $stop_date = Carbon::createFromFormat('d/m/Y H:i', $request->get('stop_date'))->format('Y-m-d H:i:s');

    // dd( $start_date);

    $verificationActivitySample->start_date =  $start_date;
    $verificationActivitySample->stop_date = $stop_date;
    $verificationActivitySample->nama_petugas = $request->get('nama_petugas');

    $verificationActivitySample->save();

    return redirect()->back();
  }

  public function getIdSample($idSampleType)
  {
    $user = Auth()->user();
    $level = $user->getlevel;

    if ($level->level == "elits-dev" || $level->level == "LAB") {

      $samplecount = Sample::join('ms_sample_type', function ($join) use ($idSampleType) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')


          ->where('ms_sample_type.typesample_type', '=', $idSampleType);
      })->max('tb_samples.codenumber_samples');
      $samplecount = $samplecount + 1;
    } else {
      return response()->json([
        'success' => 'false',
        'errors'  => "Cannot Access",
      ], 400);
    }

    return response()->json(array('success' => true, 'samplecount' =>  $samplecount));
  }

  public function rules($request)
  {
    $rule = [
      'jenis_sampel' => 'required',
      // 'program_samples' => 'required',
      // 'wadah' => 'required',
      // 'pengawet' => 'required',
      // 'volume' => 'required',
      // 'unit' => 'required',
      // 'kondisi_sample' => 'required',
      // 'validation_sample' => 'required',
      // 'kelayakan_tempat_kemasan' => 'required',
      // 'kelayakan_berat_vol' => 'required',
    ];

    $pesan = [
      'jenis_sampel.required' => 'Jenis sample tidak boleh kosong!',
      // 'wadah.required' => 'Wadah tidak boleh kosong!',
      // 'pengawet.required' => 'Pengawet tidak boleh kosong!',
      // 'volume.required' => 'Volume tidak boleh kosong!',
      // 'unit.required' => 'Unit tidak boleh kosong!',
      // 'kondisi_sample.required' => 'Kondisi sample tidak boleh kosong!',
      // 'validation_sample.required' => 'Validasi sample tidak boleh kosong!',
      // 'kelayakan_tempat_kemasan.required' => 'Validasi tempat/kemasan tidak boleh kosong!',
      // 'kelayakan_berat_vol.required' => 'Kelayakan berat/volume sample tidak boleh kosong!',
    ];

    return Validator::make($request, $rule, $pesan);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */



  public function store(Request $request, $id)
  {
    $validator = $this->rules($request->all());
    $is_success = true;


    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();



      $codeKimiaMikro = Uuid::uuid4();

      $paketAirBersih = 'a0067d2a-6193-4225-9210-be6569d88a6c';
      $paketAirMinum = '04d4e517-73c0-4fab-809c-5ba0bac730d7';

      // try {
      $laboratoriums = Laboratorium::where('kode_laboratorium', '!=', 'KLI')->get();

      # Code Samples
      $code_sample_kimia = $request->get('code_sample_kimia');
      $code_sample_mikro = $request->get('code_sample_mikro');
      $code_samples = [
        'kimia' => $code_sample_kimia,
        'mikro' => $code_sample_mikro
      ];

      # Parse Parameters.
      $data = $request->all();
      $array_method = [];
      $lab = [];



      if (isset($data["method"])) {
        for ($i = 0; $i < count($data["method"]); $i++) {
          $method = [];
          $methodlab = explode("_", $data["method"][$i]);
          $method["method"] = $methodlab[0];
          $method["lab"] = $methodlab[1];
          $method["price_method"] = $methodlab[2];

          array_push($array_method, $method);
          array_push($lab, $methodlab[1]);
        }
      }

      usort($array_method, function ($a, $b) {
        return strcmp($a["lab"], $b["lab"]);
      });
      $lab = array_unique($lab);
      sort($lab);


      $laboratoriumId= $lab[0];



      # Remove Unused Lab by Params Lab Id.
      if (count($lab) < 2) {
        $current_lab = head($lab);
        $current_laboratorium = $laboratoriums->where('id_laboratorium', $current_lab)->first();
        $current_lab_name = strtolower($current_laboratorium->nama_laboratorium);

        if ($current_lab_name == 'mikrobiologi') $current_lab_name = 'mikro';

        // Example: ["kimia" => uuid4, "mikro" => uuid4]


        foreach ($code_samples as $loop_lab_name => $loop_lab_key) {
          // dd($code_samples[$loop_lab_name]);
          if ($loop_lab_name != $current_lab_name) {
            unset($code_samples[$loop_lab_name]);
          }
        }
      }


      $created_at = Carbon::now()->format('Y-m-d H:i:s');



      do {
        # Initialization
        $current_lab = head($lab);

        $current_laboratorium = $laboratoriums->where('id_laboratorium', $current_lab)->first();
        $current_lab_name = strtolower($current_laboratorium->nama_laboratorium);
        // dd($current_lab_name);
        if ($current_lab_name == 'mikrobiologi') $current_lab_name = 'mikro';
        $current_sample_code = $code_samples[$current_lab_name];

        $current_sample_urutan = (int) head(explode("/", $current_sample_code));


        $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();


        // dd( $current_sample_urutan);

        $sample = new Sample;
        $sample->permohonan_uji_id = $id;

        $sample->created_at = $created_at;

        if ($request->post('jenis_sample_uji_usap') !== null){
          $sample->jenis_sample_uji_usap = $request->post('jenis_sample_uji_usap');
        }


        $sample->codesample_samples = $current_sample_code;

        $sample->name_pelanggan = $request->post('name_pelanggan');

        $sample->is_pudam  = $request->post('is_pudam')?1:0;
        $sample->name_customer_pdam = $request->post('name_customer_pdam');
        $sample->address_location_pdam  = $request->post('address_location_pdam');

        $packet = $request->post('packet');


        if (isset($packet)) {
          # code...

          if (count($packet)>1 ) {
            $is_lab=false;
            # code...

            if( $current_laboratorium->kode_laboratorium=="KIM"){
              $id_packet="";
              $cost=0;
              foreach ($packet as $key => $item_packet) {

                if ($item_packet == $paketAirMinum or $item_packet == $paketAirBersih){
                  $sample->codeKimiaMikro = $codeKimiaMikro;
                }
                # code...
                $packet_obj = Packet::find($item_packet);

                if (  str_contains($packet_obj->name_packet,'Fisika') || str_contains($packet_obj->name_packet,'Kimia')) {
                  # code...
                  $is_lab=true;
                  $id_packet=$item_packet;
                  $cost=$packet_obj->price_total_packet;
                }else{
                  foreach ($packet_obj->packet_detail as $packet_detail){
                    foreach ($packet_detail->method->laboratorium_method as $laboratorium_method){


                      if( $current_laboratorium->id_laboratorium==$laboratorium_method->laboratorium_id){
                        $is_lab=true;
                        $id_packet=$item_packet;
                        $cost=$packet_obj->price_total_packet;
                      }
                      // else{
                      //   dd($current_laboratorium);
                      // }
                    }
                  }
                }
              }


              if ( $is_lab) {
                # code...
                $sample->packet_id = $id_packet;
                $sample->cost_samples =  $cost;
              }else{
                $count=0;
                foreach ($array_method as $method) {


                  if ($method["lab"] == $current_laboratorium->id_laboratorium) {

                    $count= $count+$method["price_method"];
                  }
                }
                $sample->cost_samples =$count;
              }


            }else if( $current_laboratorium->kode_laboratorium=="MBI"){
              $id_packet="";
              $cost=0;
              foreach ($packet as $key => $item_packet) {
                # code...

                if ($item_packet == $paketAirMinum or $item_packet == $paketAirBersih){
                  $sample->codeKimiaMikro = $codeKimiaMikro;
                }

                $packet_obj = Packet::find($item_packet);

                if (  str_contains($packet_obj->name_packet,'Bakteriologis')) {
                  # code...
                  $is_lab=true;
                  $id_packet=$item_packet;
                  $cost=$packet_obj->price_total_packet;
                }else{
                  foreach ($packet_obj->packet_detail as $packet_detail){
                    foreach ($packet_detail->method->laboratorium_method as $laboratorium_method){


                      if( $current_laboratorium->id_laboratorium==$laboratorium_method->laboratorium_id){
                        $is_lab=true;
                        $id_packet=$item_packet;
                        $cost=$packet_obj->price_total_packet;
                      }
                      // else{
                      //   dd($current_laboratorium);
                      // }
                    }
                  }
                }
              }


              if ( $is_lab) {
                # code...
                $sample->packet_id = $id_packet;
                $sample->cost_samples =  $cost;
              }else{
                $count=0;
                foreach ($array_method as $method) {


                  if ($method["lab"] == $current_laboratorium->id_laboratorium) {

                    $count= $count+$method["price_method"];
                  }
                }
                $sample->cost_samples =$count;
              }
            }


          }else if (count($packet)==1 ) {

              $sample->packet_id = $packet[0];
              if ($packet[0] == $paketAirMinum or $packet[0] == $paketAirBersih){
                $sample->codeKimiaMikro = $codeKimiaMikro;
              }
              $sample->cost_samples = $request->post('cost_samples');

              // $packet = $request->post('packet');



              // foreach ($packet as $packet_id){
              $is_lab=false;
              $sample->packet_id = null;
                $packet_model = Packet::find($packet[0]);
                // dd($packet_model->packet_detail);
                foreach ($packet_model->packet_detail as $packet_detail){
                  foreach ($packet_detail->method->laboratorium_method as $laboratorium_method){


                    if( $current_laboratorium->id_laboratorium==$laboratorium_method->laboratorium_id){
                      $is_lab=true;
                    }
                    // else{
                    //   dd($current_laboratorium);
                    // }
                  }
                }


                if ( $is_lab) {
                  # code...
                  $sample->packet_id = $packet[0];
                  $sample->cost_samples =$packet_model->price_total_packet;
                }else{
                  $count=0;
                  foreach ($array_method as $method) {


                    if ($method["lab"] == $current_laboratorium->id_laboratorium) {

                      $count= $count+$method["price_method"];
                    }
                  }
                  $sample->cost_samples =$count;
                }


              // }


          }else{
            $sample->cost_samples = $request->post('cost_samples');
          }
        }else{
          $sample->cost_samples = $request->post('cost_samples');
        }





        # Jenis Sampel
        $sample->typesample_samples = $request->post('jenis_sampel');
        if (isset($request->gender_samples)) {
          $sample->gender_samples = $request->post('gender_samples');
        }

        # Umur
        if (isset($request->umur_samples)) {
          $sample->umur_samples = $request->post('umur_samples');
        }

        # Jenis Makanan
        $jenis_makanan_id = $request->post('jenis_makanan_id');
        if (isset($jenis_makanan_id)) {
          $sample->jenis_makanan_id = $request->post('jenis_makanan_id');
        }

        $jenis_makanan =$request->post('jenis_makanan_minuman');
        // if ($request->post('jenis_sampel') == "d34b4a50-4560-4fce-96c3-046c7080a986"){
          if (isset($jenis_makanan) ){
            $sample->nama_jenis_makanan = $request->post('jenis_makanan_minuman');
          }
        // }

        # Sample Urutan
        // if (Sample::where(DB::raw('YEAR(date_sending)'), '=', date('Y'))->count() == 0) {
        //   $sample_urutan = 0;
        // } else {
        //   $sample_urutan = Sample::where(DB::raw('YEAR(date_sending)'), '=', date('Y'))->orderBy('count_id', 'DESC')->first()->count_id;
        // }

        $sample->name_send_sample = $request->post('name_send_sample');
        $sample->code_sample_customer = $request->post('code_sample_customer');
        $sample->count_id =  $current_sample_urutan;
        $sample->program_samples = $request->post('program_samples');


        # Jenis Sarana
        if (isset($request->jenis_sarana)) {
          $sample->jenis_sarana_names = $request->jenis_sarana;
        }

        $datesampling_samples = Carbon::createFromFormat('d/m/Y H:i', $request->post('datesampling_samples'))->format('Y-m-d H:i:s');
        $date_sending = Carbon::createFromFormat('d/m/Y H:i', $request->post('date_sending'))->format('Y-m-d H:i:s');
        $sample->datesampling_samples = $datesampling_samples;
        $sample->date_sending = $date_sending;
        $sample->note_samples = $request->post('note');
        $sample->titik_pengambilan = $request->post('titik_pengambilan');
        $simpan_sample = $sample->save();



        # Lab Num
        if (isset($data["method"])) {
          foreach ($lab as $lab_key) {
            if ($current_lab == $lab_key) {
              # code...


                $start_num= StartNum::join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.kode_laboratorium', '=', 'ms_start_number.code_lab_start_number')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('ms_start_number.deleted_at');
                })->where('id_laboratorium',$lab_key)->first();

                $sample->typesample_samples = $request->post('jenis_sampel');

                $sample_type=SampleType::find($sample->typesample_samples);
                if (str_contains($sample_type->name_sample_type,"Makanan/Minuman/Lainnya")) {

                  $start_num= StartNum::where('code_lab_start_number','MAK-MIN')->first();

                  # code...
                  $lab_num_urutan =
                  LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
                  // ->where('lab_id', $lab_key)
                  ->where('is_makanan',1)
                  ->where('year_lab_num', '=', date('Y'))
                  // ->where('tb_lab_num.created_at','<=',$permohonan_uji->created_at)
                  // ->tb_samples.created_at >= DATE_FORMAT(NOW() ,'%Y-%m-01')
                  ->count();

                  if ($lab_num_urutan>0) {
                    # code...
                    $lab_num_urutan =
                    LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
                    ->where('is_makanan',1)
                    ->where('year_lab_num', '=', date('Y'))
                    // ->where('tb_lab_num.created_at','<=',$permohonan_uji->created_at)
                    ->max('lab_number');
                    $lab_num_urutan=$lab_num_urutan +1;
                  }else{
                    if (date('Y')==$start_num->year_start_number) {
                      # code...
                      $lab_num_urutan=$lab_num_urutan +($start_num->count_start_number+1);
                    }else{
                      $lab_num_urutan=1;
                    }


                  }

                  $lab_num = new LabNum;
                  $lab_num->sample_id = $sample->id_samples;
                  $lab_num->sample_type_id = $sample->typesample_samples;
                  $lab_num->lab_id = $lab_key;
                  $lab_num->is_makanan = 1;
                  $lab_num->mount_lab_num = Carbon::now()->format('m');
                  $lab_num->year_lab_num = Carbon::now()->format('Y');
                  $lab_num->permohonan_uji_id = $sample->permohonan_uji_id;
                  $lab_num->lab_number = $lab_num_urutan;
                  $lab_num->save();


                }else{

                  if (count($permohonan_uji->samples)>0) {
                    # code...

                    $samples= $permohonan_uji->samples
                              ->sortByDesc('created_at');
                     $lab_num= LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
                              ->where('lab_id', $lab_key)
                              ->where('year_lab_num', '=', date('Y'))
                              ->where('tb_lab_num.created_at','<=',$samples[0]->created_at);

                  }else{
                    $lab_num= LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
                    ->where('lab_id', $lab_key)
                    ->where('year_lab_num', '=', date('Y'))
                    ->where('tb_lab_num.created_at','<=',$permohonan_uji->created_at);


                  }
                  $lab_num_urutan =
                  $lab_num
                  ->count();

                  if ($lab_num_urutan>0) {
                    # code...



                      $lab_num_urutan =
                      $lab_num
                      ->max('lab_number');




                    $lab_num_urutan=$lab_num_urutan +1;
                  }else{
                    if (date('Y')==$start_num->year_start_number) {
                      # code...
                      $lab_num_urutan=$lab_num_urutan +$start_num->count_start_number;
                    }else{
                      $lab_num_urutan=1;
                    }


                  }


                  $lab_num = new LabNum;
                  $lab_num->sample_id = $sample->id_samples;
                  $lab_num->sample_type_id = $sample->typesample_samples;
                  $lab_num->lab_id = $lab_key;
                  $lab_num->mount_lab_num = Carbon::now()->format('m');
                  $lab_num->year_lab_num = Carbon::now()->format('Y');
                  $lab_num->permohonan_uji_id = $sample->permohonan_uji_id;
                  $lab_num->lab_number = $lab_num_urutan;
                  $lab_num->save();
                }




              foreach ($array_method as $method) {
                if ($method["lab"] == $lab_key) {
                  $saplemmethod = new SampleMethod;
                  $saplemmethod->sample_id = $sample->id_samples;
                  $saplemmethod->method_id = $method["method"];
                  $saplemmethod->price_method = (int)$method["price_method"];
                  $saplemmethod->laboratorium_id = $method["lab"];

                  $baku_mutu = BakuMutu::where('method_id', '=', $method["method"])
                    ->where('lab_id', '=', $method["lab"])
                    ->where('sampletype_id', '=', $sample->typesample_samples)->first();

                  if ($baku_mutu && $baku_mutu->is_sub == "1") {
                    $saplemmethod->is_sub = 1;
                    $bakuMutuDetailParameterNonKliniks = BakuMutuDetailParameterNonKlinik::where('method_id', '=', $method["method"])
                      ->where('sampletype_id', '=', $sample->typesample_samples)
                      ->where('baku_mutu_id', '=',  $baku_mutu->id_baku_mutu)->get();

                    foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {
                      $sample_result_detail = new SampleResultDetail;
                      // $sample_result_detail->id_sample_result_detail = Uuid::uuid4();
                      $sample_result_detail->sample_id = $sample->id_samples;
                      $sample_result_detail->method_id = $bakuMutuDetailParameterNonKlinik->method_id;
                      $sample_result_detail->sampletype_id = $bakuMutuDetailParameterNonKlinik->sampletype_id;
                      $sample_result_detail->lab_id  = $bakuMutuDetailParameterNonKlinik->lab_id;
                      $sample_result_detail->name_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik;
                      $sample_result_detail->min_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik;
                      $sample_result_detail->max_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik;
                      $sample_result_detail->equal_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik;
                      $sample_result_detail->nilai_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik;
                      $sample_result_detail->save();
                    }
                  }

                  $saplemmethod->save();
                }
              }
            }
          }


        }


        # [Update] Permohonan Uji
        $total_cost = Sample::where('permohonan_uji_id', '=', $id)->sum('cost_samples');
        $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();
        $permohonan_uji->total_harga = $total_cost;
        $permohonan_uji->total_harga = $total_cost;
        $permohonan_uji->save();

        #sortng number



        # Penerimaan Sample
        $penerimaan_sample = new PenerimaanSample;
        $penerimaan_sample->sample_id =  $sample->id_samples;
        $penerimaan_sample->penerimaan_sample_date = $date_sending;
        $penerimaan_sample->kelayakan_tempat_kemasan  = $request->post('kelayakan_tempat_kemasan');
        $penerimaan_sample->kelayakan_berat_vol  = $request->post('kelayakan_berat_vol');
        $simpan_penerimaan_sample = $penerimaan_sample->save();

        # Verivication
        if ($simpan_sample == true && $simpan_penerimaan_sample == true) {
          $verificationActivitySample = new VerificationActivitySample();
          $verificationActivitySample->id = Uuid::uuid4()->toString();
          $verificationActivitySample->id_verification_activity = 1;
          $verificationActivitySample->id_sample = $sample->id_samples;
          $verificationActivitySample->start_date = $date_sending;
          $verificationActivitySample->stop_date = Carbon::createFromFormat('d/m/Y H:i', $request->get('date_sending_stop'))->format('Y-m-d H:i:s');
          $verificationActivitySample->nama_petugas = $permohonan_uji->petugas_penerima;
          $verificationActivitySample->is_done = true;
          $verificationActivitySample->save();
        } else {
          $is_success = false;
        }





        array_shift($code_samples);
        array_shift($lab);

        // foreach ($lab as $lab_key) {


          $this->sortingNumber($lab_key,null);
        // }



        // if (isset($data["method"])) {

        // }


      } while (count($code_samples) > 0 && $simpan_penerimaan_sample == true && $simpan_sample == true);

      DB::commit();

      if ($is_success) {
        $this->sortingNumberAll();
        return response()->json(['status' => true, 'pesan' => "Data sample berhasil disimpan!", 'url_redirect' => route('elits-samples.index', $id)], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data sample tidak berhasil disimpan!"], 200);
      }
      // } catch (\Exception $e) {
      //   DB::rollback();

      //   return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      // }
    }
  }


  public function storeSampleDuplicate(Request $request, $data, $id_lab)
  {
    $sample = Sample::query()->find($data);

    $codesSamples = Sample::query()->where('permohonan_uji_id', '=', $sample->permohonan_uji_id)->get();


    DB::beginTransaction();
    // try {

      // dd($sample->packet_id);


      $packet_id=$sample->packet_id;
      if (isset($packet_id)) {
        // dd($sample);
        $samples = Sample::where('permohonan_uji_id',$sample->permohonan_uji_id)->where('created_at',$sample->created_at)->get();
        // dd($samples );

        # code...
        $created_at = Carbon::now()->format('Y-m-d H:i:s');


        foreach ($samples as $index => $sample) {



          $labNum = LabNum::query()->where('sample_id', '=', $sample->id_samples)->get();


          $sampleMethod = SampleMethod::query()->where('sample_id', '=', $sample->id_samples)->get();

          // dd($labNum);


          // print($sampleMethod);
          $penerimaanSamples = PenerimaanSample::query()->where('sample_id', '=', $sample->id_samples)->get();

          $arrayCode = [];
          foreach ($codesSamples as $cds) {
            $codes = explode('/', $cds->codesample_samples);
            array_push($arrayCode, $codes[0]);
          }

          $code = explode('/', $sample->codesample_samples);
          // $number = max($arrayCode);
          // $number = (int)$number + 1;

          // $number_copy= $number;
          // dd($number_copy);

          $sample_type = SampleType::find($sample->typesample_samples);


          $number =$this->getNewNumberSequence( $labNum[0]->lab_id,$sample->permohonan_uji_id,(str_contains($sample_type->name_sample_type,"Makanan/Minuman/Lainnya")));

          $number = (int)$number + 1;
          $number_copy=$number;

          $number = str_pad($number, 4, "0", STR_PAD_LEFT);
          $code[0] = $number;
          $code = implode('/', $code);


          // dd($code);


          $duplicateSample = new Sample();

          $duplicateSample->count_id = $sample->count_id;
          $duplicateSample->packet_id = $sample->packet_id;
          $duplicateSample->created_at = $created_at;
          $duplicateSample->is_pudam  = $request->post('is_pudam')?1:0;
          $duplicateSample->name_customer_pdam = $request->post('name_customer_pdam');
          $duplicateSample->address_location_pdam  = $request->post('address_location_pdam');

          // $created_at
          $duplicateSample->jenis_makanan_id = $sample->jenis_makanan_id;
          $duplicateSample->permohonan_uji_id = $sample->permohonan_uji_id;
          $duplicateSample->codesample_samples = $code;
          $duplicateSample->typesample_samples = $sample->typesample_samples;
          $duplicateSample->jenis_sarana_names = $sample->jenis_sarana_names;
          $duplicateSample->gender_samples = $sample->gender_samples;
          $duplicateSample->umur_samples = $sample->umur_samples;
          $duplicateSample->program_samples = $sample->program_samples;
          $duplicateSample->titik_pengambilan = $sample->titik_pengambilan;
          $duplicateSample->wadah_samples = $sample->wadah_samples;
          $duplicateSample->wadah_samples_others = $sample->wadah_samples_others;
          $duplicateSample->unit_samples = $sample->unit_samples;
          $duplicateSample->location_samples = $sample->location_samples;
          $duplicateSample->cost_samples = $sample->cost_samples;
          $duplicateSample->biaya_tindakan_rectal_swab = $sample->biaya_tindakan_rectal_swab;
          $duplicateSample->note_samples = $sample->note_samples;
          $duplicateSample->status = $sample->status;
          $duplicateSample->name_send_sample = $sample->name_send_sample;
          $duplicateSample->code_sample_customer = $sample->code_sample_customer;
          $duplicateSample->date_sending = $sample->date_sending;
          $duplicateSample->datesampling_samples = $sample->datesampling_samples;
          $duplicateSample->date_done_estimation = $sample->date_done_estimation;
          $duplicateSample->date_penerimaan_sample = $sample->date_penerimaan_sample;
          $duplicateSample->date_penanganan_sample = $sample->date_penanganan_sample;
          $duplicateSample->date_analitik_sample = $sample->date_analitik_sample;
          $duplicateSample->pelaporan_hasil_sample = $sample->pelaporan_hasil_sample;
          $duplicateSample->pengetikan_hasil_sample = $sample->pengetikan_hasil_sample;
          $duplicateSample->is_lengkap = $sample->is_lengkap;
          $duplicateSample->date_delegation = $sample->date_delegation;
          $duplicateSample->date_analition = $sample->date_analition;
          $duplicateSample->date_invoice = $sample->date_invoice;
          $duplicateSample->sample_type_group = $sample->sample_type_group;

          $duplicateSample->save();




          foreach ($labNum as $lab) {



            if (!str_contains($sample_type->name_sample_type,"Makanan/Minuman/Lainnya")) {
              # code...
              // $this->sortingNumber($lab->lab_id,$number_copy);

              $newLab = new LabNum();

              $newLab->sample_id = $duplicateSample->id_samples;
              $newLab->sample_type_id = $lab->sample_type_id;
              $newLab->permohonan_uji_id = $lab->permohonan_uji_id;
              $newLab->lab_id = $lab->lab_id;
              $newLab->lab_number = $number_copy;
              $newLab->lab_string = $lab->lab_string;
              $newLab->mount_lab_num = $lab->mount_lab_num;
              $newLab->year_lab_num = $lab->year_lab_num;

              $newLab->save();
            }else{
              // $this->sortingNumber($lab->lab_id,null);

              $newLab = new LabNum();



              $newLab->sample_id = $duplicateSample->id_samples;
              $newLab->sample_type_id = $lab->sample_type_id;
              $newLab->permohonan_uji_id = $lab->permohonan_uji_id;
              $newLab->lab_id = $lab->lab_id;
              $newLab->is_makanan = 1;
              $newLab->lab_number = $number_copy;
              $newLab->lab_string = $lab->lab_string;
              $newLab->mount_lab_num = $lab->mount_lab_num;
              $newLab->year_lab_num = $lab->year_lab_num;

              $newLab->save();
            }


            // $start_num= StartNum::join('ms_laboratorium', function ($join) {
            //   $join->on('ms_laboratorium.kode_laboratorium', '=', 'ms_start_number.code_lab_start_number')
            //     ->whereNull('ms_laboratorium.deleted_at')
            //     ->whereNull('ms_start_number.deleted_at');
            // })->where('id_laboratorium',$lab->lab_id)->first();


            // $lab_num_urutan =
            // LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
            // ->where('lab_id', $lab->lab_id)
            // ->count();
            // if ($lab_num_urutan>0) {
            //   # code...
            //   $lab_num_urutan =
            //   LabNum::where(DB::raw('YEAR(created_at)'), '=', date('Y'))
            //   ->where('lab_id', $lab->lab_id)
            //   ->max('lab_number');
            //   $lab_num_urutan=$lab_num_urutan +1;
            // }else{
            //   $lab_num_urutan=$lab_num_urutan +$start_num->count_start_number;

            // }


          }


          foreach ($sampleMethod as $smplMethod) {
            // if ($smplMethod->laboratorium_id == $id_lab) {
              $newSampleMethod = new SampleMethod();

              $newSampleMethod->sample_id = $duplicateSample->id_samples;
              $newSampleMethod->method_id = $smplMethod->method_id;
              $newSampleMethod->laboratorium_id = $smplMethod->laboratorium_id;
              $newSampleMethod->lab_num_id = $smplMethod->lab_num_id;
              $newSampleMethod->is_sub = $smplMethod->is_sub;
              $newSampleMethod->price_method = $smplMethod->price_method;

              $newSampleMethod->save();
            // }
          }

          foreach ($penerimaanSamples as $penerimaanSample) {
            if ($penerimaanSample->sample_id == $sample->id_samples) {
              $duplicatePenerimaanSample = new PenerimaanSample();

              $duplicatePenerimaanSample->sample_id = $duplicateSample->id_samples;
              $duplicatePenerimaanSample->laboratorium_id = $penerimaanSample->laboratorium_id;
              $duplicatePenerimaanSample->wadah_id = $penerimaanSample->wadah_id;
              $duplicatePenerimaanSample->wadah_sampel_other = $penerimaanSample->wadah_sampel_other;
              $duplicatePenerimaanSample->pengawet = $penerimaanSample->pengawet;
              $duplicatePenerimaanSample->pengawet_other = $penerimaanSample->pengawet_other;
              $duplicatePenerimaanSample->volume = $penerimaanSample->volume;
              $duplicatePenerimaanSample->unit_id = $penerimaanSample->unit_id;
              $duplicatePenerimaanSample->kondisi_sample = $penerimaanSample->kondisi_sample;
              $duplicatePenerimaanSample->validation_sample = $penerimaanSample->validation_sample;
              $duplicatePenerimaanSample->penerimaan_sample_date = $penerimaanSample->penerimaan_sample_date;
              $duplicatePenerimaanSample->kelayakan_tempat_kemasan = $penerimaanSample->kelayakan_tempat_kemasan;
              $duplicatePenerimaanSample->kelayakan_berat_vol = $penerimaanSample->kelayakan_berat_vol;

              $duplicatePenerimaanSample->save();
            }
          }

          $verifSample = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->first();
          $dateStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $verifSample->stop_date)->format('Y-m-d H:i:s');

          $verificationActivitySample = new VerificationActivitySample();
          $verificationActivitySample->id = Uuid::uuid4()->toString();
          $verificationActivitySample->id_sample = $duplicateSample->id_samples;
          $verificationActivitySample->id_verification_activity = 1;
          $verificationActivitySample->start_date = $dateStartDate;
          $verificationActivitySample->stop_date = Carbon::createFromFormat('Y-m-d H:i:s', $dateStartDate)->add(10, 'minutes')->format('Y-m-d H:i:s');
          $verificationActivitySample->nama_petugas = $verifSample->nama_petugas;
          $verificationActivitySample->is_done = true;

          $verificationActivitySample->save();
          # code...
        }
      }else{

        $samples = Sample::where('permohonan_uji_id',$sample->permohonan_uji_id)->where('created_at',$sample->created_at)->get();
        // dd($samples );

        # code...
        $created_at = Carbon::now()->format('Y-m-d H:i:s');


        foreach ($samples as $index => $sample) {


          $labNum = LabNum::query()->where('sample_id', '=', $sample->id_samples)->get();

          $sampleMethod = SampleMethod::query()->where('sample_id', '=', $sample->id_samples)->get();

          // dd($labNum);


          // print($sampleMethod);
          $penerimaanSamples = PenerimaanSample::query()->where('sample_id', '=', $sample->id_samples)->get();

          $arrayCode = [];
          foreach ($codesSamples as $cds) {
            $codes = explode('/', $cds->codesample_samples);
            array_push($arrayCode, $codes[0]);
          }

          $code = explode('/', $sample->codesample_samples);


          $sample_type = SampleType::find($sample->typesample_samples);



          $number =$this->getNewNumberSequence( $labNum[0]->lab_id,$sample->permohonan_uji_id,(str_contains($sample_type->name_sample_type,"Makanan/Minuman/Lainnya")));

          $number = (int)$number + 1;
          $number_copy=$number;

          $number = str_pad($number, 4, "0", STR_PAD_LEFT);
          $code[0] = $number;
          $code = implode('/', $code);



          // try {
          $duplicateSample = new Sample();

          $duplicateSample->count_id = $sample->count_id;
          // $duplicateSample->packet_id = $sample->packet_id;

          $duplicateSample->name_pelanggan =  $sample->name_pelanggan;
          $duplicateSample->jenis_makanan_id = $sample->jenis_makanan_id;
          $duplicateSample->permohonan_uji_id = $sample->permohonan_uji_id;
          $duplicateSample->codesample_samples = $code;
          $duplicateSample->typesample_samples = $sample->typesample_samples;
          $duplicateSample->jenis_sarana_names = $sample->jenis_sarana_names;
          $duplicateSample->gender_samples = $sample->gender_samples;
          $duplicateSample->umur_samples = $sample->umur_samples;
          $duplicateSample->program_samples = $sample->program_samples;
          $duplicateSample->titik_pengambilan = $sample->titik_pengambilan;
          $duplicateSample->wadah_samples = $sample->wadah_samples;
          $duplicateSample->wadah_samples_others = $sample->wadah_samples_others;
          $duplicateSample->unit_samples = $sample->unit_samples;
          $duplicateSample->location_samples = $sample->location_samples;
          $duplicateSample->cost_samples = $sample->cost_samples;
          $duplicateSample->biaya_tindakan_rectal_swab = $sample->biaya_tindakan_rectal_swab;
          $duplicateSample->note_samples = $sample->note_samples;
          $duplicateSample->status = $sample->status;
          $duplicateSample->name_send_sample = $sample->name_send_sample;
          $duplicateSample->code_sample_customer = $sample->code_sample_customer;
          $duplicateSample->date_sending = $sample->date_sending;
          $duplicateSample->datesampling_samples = $sample->datesampling_samples;
          $duplicateSample->date_done_estimation = $sample->date_done_estimation;
          $duplicateSample->date_penerimaan_sample = $sample->date_penerimaan_sample;
          $duplicateSample->date_penanganan_sample = $sample->date_penanganan_sample;
          $duplicateSample->date_analitik_sample = $sample->date_analitik_sample;
          $duplicateSample->pelaporan_hasil_sample = $sample->pelaporan_hasil_sample;
          $duplicateSample->pengetikan_hasil_sample = $sample->pengetikan_hasil_sample;
          $duplicateSample->is_lengkap = $sample->is_lengkap;
          $duplicateSample->date_delegation = $sample->date_delegation;
          $duplicateSample->date_analition = $sample->date_analition;
          $duplicateSample->date_invoice = $sample->date_invoice;
          $duplicateSample->sample_type_group = $sample->sample_type_group;


          $cek = $duplicateSample->save();
          // dd( $duplicateSample->id_samples);

          foreach ($labNum as $lab) {




            if (!str_contains($sample_type->name_sample_type,"Makanan/Minuman/Lainnya")) {
              # code...
              // $this->sortingNumber($lab->lab_id,$number_copy);

              $newLab = new LabNum();

              $newLab->sample_id = $duplicateSample->id_samples;
              $newLab->sample_type_id = $lab->sample_type_id;
              $newLab->permohonan_uji_id = $lab->permohonan_uji_id;
              $newLab->lab_id = $lab->lab_id;
              $newLab->lab_number = $number_copy;
              $newLab->lab_string = $lab->lab_string;
              $newLab->mount_lab_num = $lab->mount_lab_num;
              $newLab->year_lab_num = $lab->year_lab_num;

              $newLab->save();
            }else{
              // $this->sortingNumber($lab->lab_id,null);

              $newLab = new LabNum();



              $newLab->sample_id = $duplicateSample->id_samples;
              $newLab->sample_type_id = $lab->sample_type_id;
              $newLab->permohonan_uji_id = $lab->permohonan_uji_id;
              $newLab->lab_id = $lab->lab_id;
              $newLab->is_makanan = 1;
              $newLab->lab_number = $number_copy;
              $newLab->lab_string = $lab->lab_string;
              $newLab->mount_lab_num = $lab->mount_lab_num;
              $newLab->year_lab_num = $lab->year_lab_num;

              $newLab=$newLab->save();
            }


          }




          foreach ($sampleMethod as $smplMethod) {
            if ($smplMethod->laboratorium_id == $lab->lab_id) {
              $newSampleMethod = new SampleMethod();

              $newSampleMethod->sample_id = $duplicateSample->id_samples;
              $newSampleMethod->method_id = $smplMethod->method_id;
              $newSampleMethod->laboratorium_id = $smplMethod->laboratorium_id;
              $newSampleMethod->lab_num_id = $smplMethod->lab_num_id;
              $newSampleMethod->is_sub = $smplMethod->is_sub;
              $newSampleMethod->price_method = $smplMethod->price_method;

              $newSampleMethod->save();
            }
          }



          foreach ($penerimaanSamples as $penerimaanSample) {
            if ($penerimaanSample->sample_id == $sample->id_samples) {
              $duplicatePenerimaanSample = new PenerimaanSample();

              $duplicatePenerimaanSample->sample_id = $duplicateSample->id_samples;
              $duplicatePenerimaanSample->laboratorium_id = $penerimaanSample->laboratorium_id;
              $duplicatePenerimaanSample->wadah_id = $penerimaanSample->wadah_id;
              $duplicatePenerimaanSample->wadah_sampel_other = $penerimaanSample->wadah_sampel_other;
              $duplicatePenerimaanSample->pengawet = $penerimaanSample->pengawet;
              $duplicatePenerimaanSample->pengawet_other = $penerimaanSample->pengawet_other;
              $duplicatePenerimaanSample->volume = $penerimaanSample->volume;
              $duplicatePenerimaanSample->unit_id = $penerimaanSample->unit_id;
              $duplicatePenerimaanSample->kondisi_sample = $penerimaanSample->kondisi_sample;
              $duplicatePenerimaanSample->validation_sample = $penerimaanSample->validation_sample;
              $duplicatePenerimaanSample->penerimaan_sample_date = $penerimaanSample->penerimaan_sample_date;
              $duplicatePenerimaanSample->kelayakan_tempat_kemasan = $penerimaanSample->kelayakan_tempat_kemasan;
              $duplicatePenerimaanSample->kelayakan_berat_vol = $penerimaanSample->kelayakan_berat_vol;

              $duplicatePenerimaanSample->save();
            }
          }

          $verifSample = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->first();
          $dateStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $verifSample->stop_date)->format('Y-m-d H:i:s');

          $verificationActivitySample = new VerificationActivitySample();
          $verificationActivitySample->id = Uuid::uuid4()->toString();
          $verificationActivitySample->id_sample = $duplicateSample->id_samples;
          $verificationActivitySample->id_verification_activity = 1;
          $verificationActivitySample->start_date = $dateStartDate;
          $verificationActivitySample->stop_date = Carbon::createFromFormat('Y-m-d H:i:s', $dateStartDate)->add(10, 'minutes')->format('Y-m-d H:i:s');
          $verificationActivitySample->nama_petugas = $verifSample->nama_petugas;
          $verificationActivitySample->is_done = true;

          $verificationActivitySample->save();
        }

      }




      // dd();
      DB::commit();
    // } catch (\Exception $exception) {
    //   DB::rollBack();
    //   return redirect()->back()->with('message', 'Duplicate sample failed');
    // }

    return redirect()->back()->with('message', 'Duplicate sample successfully');
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
  public function edit($id, $id_lab = null)
  {
    $auth = Auth()->user();

    $user = Auth()->user();
    if ($id_lab == null) {



      $sample = Sample::where('tb_samples.id_samples', '=', $id)
        // ->join('tb_sample_method', function ($join) {
        //     $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
        //         ->whereNull('tb_sample_method.deleted_at')
        //         ->whereNull('tb_samples.deleted_at')
        //         ->join('ms_laboratorium', function ($join) {
        //             $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
        //                 ->whereNull('ms_laboratorium.deleted_at')
        //                 ->whereNull('tb_sample_method.deleted_at');
        //         });
        // })
        // ->join('ms_sample_type', function ($join) {
        //     $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
        //         ->whereNull('ms_sample_type.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_sample_penerimaan', function ($join) {
        //     $join->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
        //         ->whereNull('tb_sample_penerimaan.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_sample_penanganan', function ($join) {
        //     $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
        //         ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
        //         ->whereNull('tb_sample_penanganan.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_pelaporan_hasil', function ($join) {
        //     $join->on('tb_pelaporan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
        //         ->on('tb_samples.id_samples', '=', 'tb_pelaporan_hasil.sample_id')
        //         ->whereNull('tb_pelaporan_hasil.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_pengetikan_hasil', function ($join) {
        //     $join->on('tb_pengetikan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
        //         ->on('tb_samples.id_samples', '=', 'tb_pengetikan_hasil.sample_id')
        //         ->whereNull('tb_pengetikan_hasil.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_verifikasi_hasil', function ($join) {
        //     $join->on('tb_verifikasi_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
        //         ->on('tb_samples.id_samples', '=', 'tb_verifikasi_hasil.sample_id')
        //         ->whereNull('tb_verifikasi_hasil.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        // ->leftjoin('tb_pengesahan_hasil', function ($join) {
        //     $join->on('tb_pengesahan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
        //         ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
        //         ->whereNull('tb_pengesahan_hasil.deleted_at')
        //         ->whereNull('tb_samples.deleted_at');
        // })
        ->first();
      $sample2 = null;
      if (isset($sample->codeKimiaMikro)){
        $sample2 = Sample::query()->where('id_samples', '!=', $sample->id_samples)->where('codeKimiaMikro', '=', $sample->codeKimiaMikro)->first();
      }

        $permohonan_uji = PermohonanUji::find($sample->permohonanuji->id_permohonan_uji);

        // dd(  $permohonan_uji);


      $penerimaan_sample = PenerimaanSample::where('sample_id', '=', $id)->first();

      $methods = SampleMethod::where('sample_id', '=', $id)
        ->orderBy('ms_method.created_at')
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })
        ->get();

      if (isset($sample2)){
        $methods = SampleMethod::where('sample_id', '=', $id)
          ->orWhere('sample_id', '=', $sample2->id_samples)
          ->orderBy('ms_method.created_at')
          ->join('ms_method', function ($join) {
            $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
              ->whereNull('tb_sample_method.deleted_at')
              ->whereNull('ms_method.deleted_at');
          })
          ->get();
      }

      // "method_id" => "a39d7537-6b81-4a8b-800d-62aba1cb0dbb"
      // "laboratorium_id" => "3416ca19-6c69-4e5f-a004-ae8275de7644"


      $count = Sample::count();


      $users = User::all();

      $packets = Packet::where('id_packet', '!=', '0')->orderBy('created_at')->get();

      $all_jenis_makanan = JenisMakanan::all();

      $laboratoriums = Laboratorium::where('kode_laboratorium', '!=', 'KLI')->get();
      $programs = Program::orderBy('created_at')->get();

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
              'price_method' => $laboratoriummethod->price_total_method
            )
          );
        }

        $i++;
      }


      // dd($data_methods);







      // K/ tanggal/0001
      $containers = Container::where('id_container', '!=', '0')->get();
      $sampletypes = SampleType::orderBy('created_at')->get();

      $code = 'NK/' . date("Ymd", time()) . '/' . str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);

      $units = Unit::all();
      return view('masterweb::module.admin.laboratorium.sample.edit-2', compact('permohonan_uji','penerimaan_sample', 'methods', 'sample', 'programs', 'units', 'user', 'data_methods', 'containers', 'packets', 'sampletypes', 'code', 'users', 'all_jenis_makanan'));
    } else {
    }


    // return view('masterweb::module.admin.laboratorium.customer.edit', compact('customer', 'auth', 'categories', 'id'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

   public function updateTitik($id, Request $request)
  {


    $permohonan_uji = PermohonanUji::findOrFail($id);

    foreach ($permohonan_uji->samples as $one_sample){

      if ($one_sample->titik_pengambilan !="" || $one_sample->titik_pengambilan !=" " ) {
        # code...
        $samples = Sample::where('permohonan_uji_id',$one_sample->permohonan_uji_id)->where('created_at',$one_sample->created_at)->get();
        foreach ($samples as $index => $sample) {
          $sample->titik_pengambilan=$one_sample->titik_pengambilan;
          $sample->save();
        }
      }

    }


    return redirect()->route('elits-samples.index', [$id])->with(['status' => 'Sampel berhasil diupdate titik']);

  }

  public function update($id, Request $request)
  {
    $validator = $this->rules($request->all());

    // dd($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $data = $request->all();


        if (isset($request->changeSampleType)){
          $this->updateSampleAndParameter($id, $request);
        }



        $sample = Sample::where('id_samples', $id)->first();
        // $samples = Sample::where('permohonan_uji_id',$sample->permohonan_uji_id)->where('created_at',$sample->created_at)->get();

        // $sample->id_samples = $uuid4->toString();
        // $sample->permohonan_uji_id = $id;

        // foreach ($samples as $index => $sample) {

          // $packet = $request->post('packet');

          $id_sample=$sample->id_samples;

          // if (isset($packet) || $packet != null) {
          //   $sample->packet_id = $packet;
          // } else {
          //   $sample->packet_id = null;
          // }



          // $karanganyar_code = 'NK/' .  Carbon::createFromFormat('d/m/Y H:i', $request->post('date_sending'))->format('Ymd') . '/' . str_pad((int)$sample->count_id, 4, '0', STR_PAD_LEFT);

          // $code_number = str_pad((int)$sample->count_id, 4, '0', STR_PAD_LEFT);

          // $code_type = isset($this->sample_types[$data['jenis_sampel']])
          //   ? $this->sample_types[$data['jenis_sampel']]
          //   : '...';

          // $code_datetime = Carbon::createFromFormat('d/m/Y H:i', $request->post('date_sending'));
          // $code_year = $code_datetime->format('Y');
          // $code_month = $code_datetime->format('m');

          // $boyolali_code = implode('/', [$code_number, $code_type, $code_year, $code_month]);

          // $code = $request->post('code_sample', $boyolali_code);



          // $sample->codesample_samples = $code;


          if (isset($request->name_pelanggan)) {
            # code...
            $sample->name_pelanggan =  $request->name_pelanggan;
          }

          $sample->codesample_samples = $request->code_sample;


          if (isset($request->ispacket)) {
            $ispacket = $request->post('ispacket');
          }

          if (isset($packet)) {
            $sample->is_lengkap = 1;
          } else {
            $sample->is_lengkap = 0;
          }

          if (isset($request->jenis_sampel)) {
            $sample->typesample_samples = $request->post('jenis_sampel');
          }

          if (isset($request->gender_samples) || $request->gender_samples != null) {
            $sample->gender_samples = $request->post('gender_samples');
          } else {
            $sample->gender_samples = null;
          }


          if (isset($request->umur_samples) || $request->umur_samples != null) {
            $sample->umur_samples = $request->post('umur_samples');
          } else {
            $sample->umur_samples = null;
          }

          $sample->name_send_sample = $request->post('name_send_sample');
          $sample->code_sample_customer = $request->post('code_sample_customer');
          // $sample->count_id = $sample_urutan + 1;
          if (isset($request->cost_samples)) {
            $sample->cost_samples = $request->post('cost_samples');
          }

          $jenis_makanan_id = $request->post('jenis_makanan_id');

          if (isset($jenis_makanan_id) || $jenis_makanan_id != null) {
            $sample->jenis_makanan_id = $request->post('jenis_makanan_id');
          } else {
            $sample->jenis_makanan_id = null;
          }

          $sample->is_pudam  = $request->post('is_pudam')? 1:0;
          $sample->name_customer_pdam = $request->post('name_customer_pdam');
          $sample->address_location_pdam  = $request->post('address_location_pdam');

          if(isset($sample->codeKimiaMikro)){
            $sample2 = Sample::where('codeKimiaMikro', '=', $sample->codeKimiaMikro)->where('id_samples', '!=', $sample->id_samples)->first();
            $sample2->address_location_pdam = $request->post('address_location_pdam');
            $sample2->save();
          }

          $datesampling_samples = Carbon::createFromFormat('d/m/Y H:i', $request->post('datesampling_samples'))->format('Y-m-d H:i:s');
          $date_sending = Carbon::createFromFormat('d/m/Y H:i', $request->post('date_sending'))->format('Y-m-d H:i:s');
          // // $date_done_estimation = Carbon::createFromFormat('d/m/Y', $request->post('date_done'))->format('Y-m-d H:i:s');
          $sample->datesampling_samples = $datesampling_samples;
          $sample->date_sending = $date_sending;
          // $sample->date_done_estimation=$date_done_estimation;
          $sample->note_samples = $request->post('note');
          $sample->titik_pengambilan = $request->post('titik_pengambilan');

          $simpan_sample = $sample->save();

          // $methodsrequest = [];

          // if (isset($data["method"])) {
          //   for ($i = 0; $i < count($data["method"]); $i++) {
          //     $methodlab = explode("_", $data["method"][$i]);
          //     array_push($methodsrequest, $methodlab[0]);
          //   }

          //   $sample_methods = SampleMethod::whereIn('method_id', $methodsrequest)
          //     ->where('sample_id', $id_sample)
          //     ->select('method_id')
          //     ->get();

          //   $methodsnow = [];
          //   foreach ($sample_methods as $sample_method) {
          //     array_push($methodsnow, $sample_method->method_id);
          //   }

          //   $results = array_diff($methodsrequest, $methodsnow);

          //   foreach ($results as $key => $result) {
          //     // print_r($data["method"][$key]);
          //     $methodlab = explode("_", $data["method"][$key]);
          //     // array_push($methodsrequest,$methodlab[0]);
          //     $method = new SampleMethod;



          //     // $method->id_sample_method = Uuid::uuid4();
          //     $method->sample_id = $sample->id_samples;
          //     $method->method_id = $methodlab[0];
          //     $method->laboratorium_id = $methodlab[1];
          //     $method->price_method = $methodlab[2];

          //     $method->save();
          //     $baku_mutu = BakuMutu::where('method_id', '=',  $methodlab[0])
          //       ->where('lab_id', '=', $methodlab[1])
          //       ->where('sampletype_id', '=', $sample->typesample_samples)->first();




          //     if ($baku_mutu->is_sub == "1") {
          //       $method->is_sub = 1;
          //       $bakuMutuDetailParameterNonKliniks = BakuMutuDetailParameterNonKlinik::where('method_id', '=',  $methodlab[0])
          //         ->where('sampletype_id', '=', $sample->typesample_samples)
          //         ->where('baku_mutu_id', '=',  $baku_mutu->id_baku_mutu)->get();

          //       $sample_result_details = SampleResultDetail::where('method_id', '=',  $methodlab[0])
          //         ->where('sampletype_id', '=', $sample->typesample_samples)
          //         ->where('sample_id', '=', $sample->id_samples)
          //         ->where('lab_id', '=', $methodlab[1])
          //         ->get();

          //       if (count($sample_result_details) == 0) {
          //         foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {

          //           $sample_result_detail = new SampleResultDetail;
          //           // $sample_result_detail->id_sample_result_detail = Uuid::uuid4();
          //           $sample_result_detail->sample_id = $sample->id_samples;
          //           $sample_result_detail->method_id = $bakuMutuDetailParameterNonKlinik->method_id;
          //           $sample_result_detail->sampletype_id = $bakuMutuDetailParameterNonKlinik->sampletype_id;
          //           $sample_result_detail->lab_id  = $bakuMutuDetailParameterNonKlinik->lab_id;
          //           $sample_result_detail->name_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->min_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->max_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->equal_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->nilai_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->save();
          //         }
          //       } else {
          //         $sample_result_details = SampleResultDetail::where('method_id', '=',  $methodlab[0])
          //           ->where('sampletype_id', '=', $sample->typesample_samples)
          //           ->where('sample_id', '=', $sample->id_samples)
          //           ->where('lab_id', '=', $methodlab[1])->delete();
          //         foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {

          //           $sample_result_detail = new SampleResultDetail;
          //           // $sample_result_detail->id_sample_result_detail = Uuid::uuid4();
          //           $sample_result_detail->sample_id = $sample->id_samples;
          //           $sample_result_detail->method_id = $bakuMutuDetailParameterNonKlinik->method_id;
          //           $sample_result_detail->sampletype_id = $bakuMutuDetailParameterNonKlinik->sampletype_id;
          //           $sample_result_detail->lab_id  = $bakuMutuDetailParameterNonKlinik->lab_id;
          //           $sample_result_detail->name_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->min_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->max_sample_result_detail  = $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->equal_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->nilai_sample_result_detail   = $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik;
          //           $sample_result_detail->save();
          //         }
          //       }
          //     }
          //   }


          //   // dd( $results);



          //   $sample_method = SampleMethod::whereNotIn('method_id', $methodsrequest)
          //     ->where('sample_id', $id_sample)
          //     ->delete();
          // }

          $penerimaan_sample = PenerimaanSample::where('sample_id', $id_sample)->first();

          // $penerimaan_sample->wadah_id = $request->post('wadah');
          // $wadah_samples = $request->post('wadah_samples');

          // if (isset($wadah_samples)) {
          //   $penerimaan_sample->wadah_sampel_other = $wadah_samples;
          // } else {
          //   $penerimaan_sample->wadah_sampel_other = null;
          // }

          // $penerimaan_sample->pengawet = $request->post('pengawet');

          // $pengawet_others_sample = $request->post('pengawet_others_sample');
          // if (isset($pengawet_others_sample)) {
          //   $penerimaan_sample->pengawet_other = $pengawet_others_sample;
          // } else {
          //   $penerimaan_sample->pengawet_other = null;
          // }
          // $penerimaan_sample->unit_id = $request->post('unit');
          // $penerimaan_sample->volume = $request->post('volume');
          // $penerimaan_sample->kondisi_sample = $request->post('kondisi_sample');
          // $penerimaan_sample->validation_sample  = $request->post('validation_sample');

          $penerimaan_sample->kelayakan_tempat_kemasan  = $request->post('kelayakan_tempat_kemasan');
          $penerimaan_sample->kelayakan_berat_vol  = $request->post('kelayakan_berat_vol');

          $simpan_penerimaan_sample = $penerimaan_sample->save();
        // }

        $lab_num=LabNum::where('sample_id', $id_sample)->first();
        $labnum= explode("/",  $request->code_sample);

        $lab_num->lab_number=(int)$labnum[0];

        $this->sortingNumberKesmasByCode();

        $lab_num->save();

        DB::commit();

        if ($simpan_sample == true && $simpan_penerimaan_sample == true) {
          $this->sortingNumberAll();
          return response()->json(['status' => true, 'pesan' => "Data sample berhasil disimpan!", 'url_redirect' => route('elits-samples.index', $sample->permohonan_uji_id), 'redirect' => route('elits-samples.index', $sample->permohonan_uji_id)], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data sample tidak berhasil disimpan!"], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  private function updateSampleAndParameter($id, Request $request)
  {
    $sample = Sample::where('id_samples', $id)->first();

    $paketAirBersih = 'a0067d2a-6193-4225-9210-be6569d88a6c';
    $paketAirMinum = '04d4e517-73c0-4fab-809c-5ba0bac730d7';

    $packet = $request->post('packet');

    $methods = $request->get('method');

    $distinctMethod = [];

    foreach ($methods as $method){
      $parts = explode('_', $method);

      if (count($parts) > 1) {
        $groupKey = $parts[1];
        $distinctMethod[$groupKey][] = $method;
      }
    }

    $sample2 = null;

    $isPaket = $request->is_paket;

    //  untuk menghandle jika bukan paket
    if (!isset($isPaket)){
      $sample->packet_id = null;

      if (count($distinctMethod) > 1){
        // Gagal jika terdapat 2 lab (kimia dan mikro) hanya boleh salah satu
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }

      try {
        $method = array_first($distinctMethod);
        $sample->codesample_samples = $request->post('code_sample');
        $this->storeUpdateSampleAndParameter($sample, $method, $request);
        return response()->json(['status' => true, 'pesan' => "Data sample berhasil disimpan!", 'url_redirect' => route('elits-samples.index', $sample->permohonan_uji_id), 'redirect' => route('elits-samples.index', $sample->permohonan_uji_id)], 200);
      }catch (\Exception $e){
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }
    }

    // cek paket apakah paket air bersih atau peket air minum

    if ($packet == $paketAirMinum or $packet == $paketAirBersih){
      // jika sebelum edit paketnya paket air bersih atau paket air minum maka ambil sampel 2
      if ($sample->packet_id == $paketAirMinum or $sample->packet_id == $paketAirBersih){
        $codeKimiaMikro = $sample->codeKimiaMikro;
        if (isset($codeKimiaMikro) and $codeKimiaMikro != ""){
          $sample2 = Sample::where('codeKimiaMikro', '=', $codeKimiaMikro)->where('id_samples','!=', $id)->first();
        }else{
          throw new \Exception("Data sample tidak berhasil disimpan!");
        }
      }else{
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }
    }else{
      if ($sample->packet_id == $paketAirMinum or $sample->packet_id == $paketAirBersih){
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }

      if (count($distinctMethod) > 1){
        // Gagal jika terdapat 2 lab (kimia dan mikro) hanya boleh salah satu
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }
    }

    if (isset($sample2)){
      $lab = SampleMethod::query()->where('sample_id', '=', $sample->id_samples)->first();

      $labId = $lab->laboratorium_id;
      $method = $distinctMethod[$labId];
      $sample->codesample_samples = $request->post('code_sample');

      $this->storeUpdateSampleAndParameter($sample, $method, $request);

      $lab = SampleMethod::query()->where('sample_id', '=', $sample2->id_samples)->first();
      $method = $distinctMethod[$lab->laboratorium_id];

      $this->storeUpdateSampleAndParameter($sample2, $method, $request);

      return response()->json(['status' => true, 'pesan' => "Data sample berhasil disimpan!", 'url_redirect' => route('elits-samples.index', $sample->permohonan_uji_id), 'redirect' => route('elits-samples.index', $sample->permohonan_uji_id)], 200);

    }else{
      $lab = SampleMethod::query()->where('sample_id', '=', $sample->id_samples)->first();

      $labId = $lab->laboratorium_id;
      try {
        $method = $distinctMethod[$labId];
        $sample->codesample_samples = $request->post('code_sample');

        $this->storeUpdateSampleAndParameter($sample, $method, $request);

        return response()->json(['status' => true, 'pesan' => "Data sample berhasil disimpan!", 'url_redirect' => route('elits-samples.index', $sample->permohonan_uji_id), 'redirect' => route('elits-samples.index', $sample->permohonan_uji_id)], 200);
      }catch (\Exception $exception){
        // Tidak bisa ganti dari kimia ke mikro atau sebaliknya karena nomor sample
        throw new \Exception("Data sample tidak berhasil disimpan!");
      }
    }

  }

  private function storeUpdateSampleAndParameter(Sample $sample, $methods, Request $request)
  {
    $id_sample= $sample->id_samples;


    if (isset($request->name_pelanggan)) {
      $sample->name_pelanggan =  $request->name_pelanggan;
    }

    $packet = $request->post('packet');

    if (isset($packet)){
      $sample->packet_id = $packet;
    }


    if (isset($request->jenis_sampel)) {
      $sample->typesample_samples = $request->post('jenis_sampel');
    }

    if (isset($request->gender_samples) || $request->gender_samples != null) {
      $sample->gender_samples = $request->post('gender_samples');
    } else {
      $sample->gender_samples = null;
    }


    if (isset($request->umur_samples) || $request->umur_samples != null) {
      $sample->umur_samples = $request->post('umur_samples');
    } else {
      $sample->umur_samples = null;
    }

    $sample->name_send_sample = $request->post('name_send_sample');
    $sample->code_sample_customer = $request->post('code_sample_customer');

    if (isset($request->cost_samples)) {
      $sample->cost_samples = $request->post('cost_samples');
    }

    $jenis_makanan_id = $request->post('jenis_makanan_id');

    if (isset($jenis_makanan_id) || $jenis_makanan_id != null) {
      $sample->jenis_makanan_id = $request->post('jenis_makanan_id');
    } else {
      $sample->jenis_makanan_id = null;
    }

    $sample->is_pudam  = $request->post('is_pudam')? 1:0;
    $sample->name_customer_pdam = $request->post('name_customer_pdam');
    $sample->address_location_pdam  = $request->post('address_location_pdam');

    $datesampling_samples = Carbon::createFromFormat('d/m/Y H:i', $request->post('datesampling_samples'))->format('Y-m-d H:i:s');
    $date_sending = Carbon::createFromFormat('d/m/Y H:i', $request->post('date_sending'))->format('Y-m-d H:i:s');
    // // $date_done_estimation = Carbon::createFromFormat('d/m/Y', $request->post('date_done'))->format('Y-m-d H:i:s');
    $sample->datesampling_samples = $datesampling_samples;
    $sample->date_sending = $date_sending;
    // $sample->date_done_estimation=$date_done_estimation;
    $sample->note_samples = $request->post('note');
    $sample->titik_pengambilan = $request->post('titik_pengambilan');

    $simpan_sample = $sample->save();


    //delete method sebelumnya

    SampleMethod::query()->where('sample_id', $id_sample)->delete();

    LabNum::query()->where('sample_id', $id_sample)->delete();
    // insert method baru


    if (isset($methods)) {
      foreach ($methods as $method) {
        $methodLab = explode("_", $method);


        $newMethod = new SampleMethod();
        $newMethod->sample_id = $sample->id_samples;
        $newMethod->method_id = $methodLab[0];
        $newMethod->laboratorium_id = $methodLab[1];
        $newMethod->price_method = $methodLab[2];

        $newMethod->save();

        $baku_mutu = BakuMutu::where('method_id', '=', $methodLab[0])
          ->where('lab_id', '=', $methodLab[1])
          ->where('sampletype_id', '=', $sample->typesample_samples)->first();

        if ($baku_mutu->is_sub == "1") {
          $newMethod->is_sub = 1;
          $bakuMutuDetailParameterNonKliniks = BakuMutuDetailParameterNonKlinik::where('method_id', '=', $methodLab[0])
            ->where('sampletype_id', '=', $sample->typesample_samples)
            ->where('baku_mutu_id', '=', $baku_mutu->id_baku_mutu)->get();

          $sample_result_details = SampleResultDetail::where('method_id', '=', $methodLab[0])
            ->where('sampletype_id', '=', $sample->typesample_samples)
            ->where('sample_id', '=', $sample->id_samples)
            ->where('lab_id', '=', $methodLab[1])
            ->get();

          if (count($sample_result_details) == 0) {
            foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {

              $sample_result_detail = new SampleResultDetail;
              // $sample_result_detail->id_sample_result_detail = Uuid::uuid4();
              $sample_result_detail->sample_id = $sample->id_samples;
              $sample_result_detail->method_id = $bakuMutuDetailParameterNonKlinik->method_id;
              $sample_result_detail->sampletype_id = $bakuMutuDetailParameterNonKlinik->sampletype_id;
              $sample_result_detail->lab_id = $bakuMutuDetailParameterNonKlinik->lab_id;
              $sample_result_detail->name_sample_result_detail = $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->min_sample_result_detail = $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->max_sample_result_detail = $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->equal_sample_result_detail = $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->nilai_sample_result_detail = $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->save();
            }
          } else {
            SampleResultDetail::where('method_id', '=', $methodLab[0])
              ->where('sampletype_id', '=', $sample->typesample_samples)
              ->where('sample_id', '=', $sample->id_samples)
              ->where('lab_id', '=', $methodLab[1])->delete();

            foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {

              $sample_result_detail = new SampleResultDetail;
              $sample_result_detail->sample_id = $sample->id_samples;
              $sample_result_detail->method_id = $bakuMutuDetailParameterNonKlinik->method_id;
              $sample_result_detail->sampletype_id = $bakuMutuDetailParameterNonKlinik->sampletype_id;
              $sample_result_detail->lab_id = $bakuMutuDetailParameterNonKlinik->lab_id;
              $sample_result_detail->name_sample_result_detail = $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->min_sample_result_detail = $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->max_sample_result_detail = $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->equal_sample_result_detail = $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->nilai_sample_result_detail = $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik;
              $sample_result_detail->save();
            }
          }


        }

        $lab_num = LabNum::where('sample_id', $sample->id_samples);
        $lab_num->delete();

        $newLab = new LabNum();

        $newLab->sample_id = $sample->id_samples;
        $newLab->sample_type_id = $sample->typesample_samples;
        $newLab->permohonan_uji_id = $sample->permohonan_uji_id;
        $newLab->lab_id = $methodLab[1];
        if (isset($sample->jenis_makanan_id)){
          $newLab->is_makanan = 1;
        }else{
          $newLab->is_makanan = 0;
        }
        $newLab->lab_number = $sample->count_id;

        $codeSample = explode('/', $sample->codesample_samples);

        $newLab->mount_lab_num = $codeSample[count($codeSample) - 2];
        $newLab->year_lab_num = end($codeSample);

        $newLab->save();
      }
    }

    $penerimaan_sample = PenerimaanSample::where('sample_id', $id_sample)->first();

    $penerimaan_sample->kelayakan_tempat_kemasan = $request->post('kelayakan_tempat_kemasan');
    $penerimaan_sample->kelayakan_berat_vol = $request->post('kelayakan_berat_vol');

    $penerimaan_sample->save();
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $sample = Sample::where('id_samples', $id);
    $sample->delete();

    $lab_num = LabNum::where('sample_id', $id);
    $lab_num->delete();




    return redirect()->route('elits-samples.index', [$sample->permohonan_uji_id])->with(['status' => 'Sampel berhasil dihapus']);
  }

  public function sortingNumberAll(){

    $laboratorium = Laboratorium::where('kode_laboratorium',"!=","KLI")->get();
    foreach ($laboratorium as $labor){
      $this->sortingNumber($labor->id_laboratorium,null);
    }
    return redirect()->route('elits-permohonan-uji.index')->with(['status' => 'Sampel berhasil diurutkan']);

  }

  public function sortingNumberBylabAndPlusCount($labId, $plusCount)
  {
    $this->sortingNumber($labId, $plusCount);
    dd("Success");
  }

  public function sortingNumberKesmasByCode(){
    DB::statement("
    UPDATE tb_lab_num
    JOIN tb_samples ON tb_lab_num.sample_id = tb_samples.id_samples
    SET tb_lab_num.lab_number = SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1),
    tb_samples.count_id = SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1)
    WHERE YEAR(tb_lab_num.created_at) = YEAR(CURDATE());
    ");
    // return redirect()->route('elits-permohonan-uji.index')->with(['status' => 'Sampel berhasil diurutkan']);

  }

  public function sortingNumberKesmasByCodeAll(){
    $this->sortingNumberKesmasByCode();
    return redirect()->route('elits-permohonan-uji.index')->with(['status' => 'Sampel berhasil diurutkan']);

  }

  public function sortingNumber($lab_id, $plus_number=0){


    // $data = Sample::orderBy(DB::raw("CAST(SUBSTRING_INDEX(codesample_samples, '/', 1) AS UNSIGNED)"))
    // // ->where
    // ->leftjoin('tb_lab_num', function ($join) {
    //   $join->on('tb_lab_num.sample_id', '=', DB::raw('(SELECT sample_id FROM tb_lab_num WHERE tb_lab_num.sample_id = tb_samples.id_samples AND tb_lab_num.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

    //     // ->limit(1)
    //     ->whereNull('tb_lab_num.deleted_at')
    //     ->whereNull('tb_samples.deleted_at');
    // })
    // ->where('tb_lab_num.lab_id', $lab_id)
    // ->get();

    // $lab_id = 123; // Example lab_id

    DB::statement("
        DELETE tb_lab_num
      FROM tb_lab_num
      JOIN (
          SELECT id_lab_num
          FROM (
              SELECT id_lab_num,
                    ROW_NUMBER() OVER (PARTITION BY sample_id, permohonan_uji_id, deleted_at ORDER BY id_lab_num) AS rn
              FROM tb_lab_num
              WHERE YEAR(created_at) = YEAR(CURDATE())
          ) AS subquery
          WHERE rn > 1
      ) AS duplicates
      ON tb_lab_num.id_lab_num = duplicates.id_lab_num;
    ");

    if (isset($plus_number)) {
      # code...

        $start_num=$plus_number;
        DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");


        DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)) + " . (int)$start_num . " AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                        ) AS suffix

                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                    AND tb_lab_num.lab_number >= " . (int)$start_num . "
                    AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND YEAR(tb_samples.created_at) = ".date('Y')."

                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.is_makanan = 0
                          AND tb_lab_num.deleted_at IS NULL
                          AND tb_samples.deleted_at IS NULL

                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);

              // Step 2: Update the main table based on the OrderedSamples table
              DB::statement("
                  UPDATE tb_samples
                  JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
                  SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix),
                      tb_samples.count_id = LPAD(OrderedSamples.row_num, 4, '0')
                  WHERE YEAR(tb_samples.created_at) = ".date('Y').";

              ");
              DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

              DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (
                            ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                        ) + " . (int)$start_num . " AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)



                        ) AS suffix
                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                    AND tb_lab_num.lab_number >= " . (int)$start_num . "
                     AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                    AND YEAR(tb_samples.created_at) = ".date('Y')."


                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.is_makanan = 0
                          AND tb_lab_num.deleted_at IS NULL
                          AND tb_samples.deleted_at IS NULL


                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);
          DB::statement("
              UPDATE tb_lab_num
              JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
              SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0')
           WHERE YEAR(tb_lab_num.created_at) = ".date('Y').";
              ");


              //   $data=  DB::select("

              //         SELECT
              //       DISTINCT tb_samples.id_samples,
              //         tb_samples.codesample_samples,
              //               ROW_NUMBER() OVER (
              //                   ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
              //               ) AS row_num
              //         FROM tb_samples
              //         LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
              //         WHERE tb_lab_num.lab_id = :lab_id
              //           AND  tb_lab_num.lab_number >= '". $start_num."'
              //           AND tb_lab_num.deleted_at IS NULL
              //           AND tb_samples.deleted_at IS NULL
              //           AND tb_lab_num.sample_id = (
              //               SELECT sample_id FROM tb_lab_num
              //               WHERE tb_lab_num.sample_id = tb_samples.id_samples
              //                 AND tb_lab_num.deleted_at IS NULL
              //                 AND tb_samples.deleted_at IS NULL
              //               LIMIT 1
              //           )

              // ", ['lab_id' => $lab_id]);

              // dd($data);
    }else{

      $start_num= StartNum::join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.kode_laboratorium', '=', 'ms_start_number.code_lab_start_number')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('ms_start_number.deleted_at');
      })->where('id_laboratorium',$lab_id)->first();





      if (date('Y')==$start_num->year_start_number) {
        # code...
          $start_num=$start_num->count_start_number;
              // Step 1: Drop the temporary table if it exists
          DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

          // Step 2: Create the OrderedSamples temporary table
          DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (
                            ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                        ) + " . (int)$start_num . " AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                        ) AS suffix
                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                    AND YEAR(tb_samples.created_at) = ".date('Y')."
                    AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                      AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.deleted_at IS NULL
                            AND tb_lab_num.is_makanan = 0

                          AND tb_samples.deleted_at IS NULL
                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);



          DB::statement("
              UPDATE tb_samples
              JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
              SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix)
              WHERE YEAR(tb_samples.created_at) = ".date('Y').";

          ");


          // Step 1: Drop the temporary table if it exists
          DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

          // Step 2: Create the OrderedSamples temporary table
          DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (
                            ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                        ) + " . (int)$start_num . " AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                        ) AS suffix
                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                      AND YEAR(tb_samples.created_at) = ".date('Y')."
                    AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                      AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.is_makanan = 0

                          AND tb_lab_num.deleted_at IS NULL
                          AND tb_samples.deleted_at IS NULL
                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);

          // Step 3: Update tb_lab_num using OrderedSamples
          DB::statement("
              UPDATE tb_lab_num
              JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
              SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0')
                  WHERE YEAR(tb_lab_num.created_at) = ".date('Y').";
          ");
      }else{

        DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

          // Step 2: Create the OrderedSamples temporary table
          DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (
                            ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                        )  AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                        ) AS suffix
                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                    AND YEAR(tb_samples.created_at) = ".date('Y')."
                    AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                      AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.deleted_at IS NULL
                            AND tb_lab_num.is_makanan = 0

                          AND tb_samples.deleted_at IS NULL
                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);



          DB::statement("
              UPDATE tb_samples
              JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
              SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix);

          ");


          // Step 1: Drop the temporary table if it exists
          DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

          // Step 2: Create the OrderedSamples temporary table
          DB::statement("
              CREATE TEMPORARY TABLE OrderedSamples AS (
                  SELECT DISTINCT tb_samples.id_samples,
                        ROW_NUMBER() OVER (
                            ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                        )  AS row_num,
                        SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                        ) AS suffix
                  FROM tb_samples
                  LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
                  WHERE tb_lab_num.lab_id = :lab_id
                    AND tb_lab_num.deleted_at IS NULL
                    AND tb_samples.deleted_at IS NULL
                      AND YEAR(tb_samples.created_at) = ".date('Y')."
                    AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                      AND tb_lab_num.is_makanan = 0
                    AND tb_lab_num.sample_id = (
                        SELECT sample_id FROM tb_lab_num
                        WHERE tb_lab_num.sample_id = tb_samples.id_samples
                          AND tb_lab_num.is_makanan = 0

                          AND tb_lab_num.deleted_at IS NULL
                          AND tb_samples.deleted_at IS NULL
                        LIMIT 1
                    )
              )
          ", ['lab_id' => $lab_id]);

          // Step 3: Update tb_lab_num using OrderedSamples
          DB::statement("
              UPDATE tb_lab_num
              JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
              SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0');
          ");
      }



    }

    $start_num_kim= StartNum::where('code_lab_start_number',"MAK-MIN")->where('code_lab_makanan', "3416ca19-6c69-4e5f-a004-ae8275de7644")->first();
    $start_num_mbi= StartNum::where('code_lab_start_number',"MAK-MIN")->where('code_lab_makanan', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5")->first();

    // dd($start_num->count_start_number);

    $start_num_kim=$start_num_kim->count_start_number;
    $start_num_mbi=$start_num_mbi->count_start_number;




    // Step 1: Drop the temporary table if it exists
    DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

    // Step 2: Create the OrderedSamples temporary table
    DB::statement("
        CREATE TEMPORARY TABLE OrderedSamples AS (
            SELECT DISTINCT tb_samples.id_samples,
                  ROW_NUMBER() OVER (
                      ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                  ) + " . (int)$start_num_kim . " AS row_num,
                  SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                  ) AS suffix
            FROM tb_samples
            LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
            WHERE tb_lab_num.lab_id = :lab_id
            AND  tb_lab_num.deleted_at IS NULL
              AND tb_samples.deleted_at IS NULL
                   AND YEAR(tb_samples.created_at) = ".date('Y')."
                AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                AND tb_lab_num.is_makanan = 1
              AND tb_lab_num.sample_id = (
                  SELECT sample_id FROM tb_lab_num
                  WHERE tb_lab_num.sample_id = tb_samples.id_samples
                    AND tb_lab_num.deleted_at IS NULL
                      AND tb_lab_num.is_makanan = 1

                    AND tb_samples.deleted_at IS NULL
                  LIMIT 1
              )
        )
    ", ['lab_id' => "3416ca19-6c69-4e5f-a004-ae8275de7644"]);



    DB::statement("
        UPDATE tb_samples
        JOIN OrderedSamples ON tb_samples.id_samples = OrderedSamples.id_samples
        SET tb_samples.codesample_samples = CONCAT(LPAD(OrderedSamples.row_num, 4, '0'), OrderedSamples.suffix);
    ");


    // Step 1: Drop the temporary table if it exists
    DB::statement("DROP TEMPORARY TABLE IF EXISTS OrderedSamples");

    // Step 2: Create the OrderedSamples temporary table
    DB::statement("
        CREATE TEMPORARY TABLE OrderedSamples AS (
            SELECT DISTINCT tb_samples.id_samples,
                  ROW_NUMBER() OVER (
                      ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)
                  ) + " . (int)$start_num_mbi . " AS row_num,
                  SUBSTRING(tb_samples.codesample_samples, LOCATE('/', tb_samples.codesample_samples)

                  ) AS suffix
            FROM tb_samples

            LEFT JOIN tb_lab_num ON tb_lab_num.sample_id = tb_samples.id_samples
            WHERE tb_lab_num.lab_id = :lab_id
             AND tb_lab_num.deleted_at IS NULL
              AND tb_samples.deleted_at IS NULL
                AND YEAR(tb_samples.created_at) = ".date('Y')."
                AND YEAR(tb_lab_num.created_at) = ".date('Y')."
                AND tb_lab_num.is_makanan = 1
              AND tb_lab_num.sample_id = (
                  SELECT sample_id FROM tb_lab_num
                  WHERE tb_lab_num.sample_id = tb_samples.id_samples
                    AND tb_lab_num.is_makanan = 1
                    AND tb_lab_num.deleted_at IS NULL

                    AND tb_samples.deleted_at IS NULL
                  LIMIT 1
              )
        )
     ", ['lab_id' => "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5"]);


    // Step 3: Update tb_lab_num using OrderedSamples
    DB::statement("
        UPDATE tb_lab_num
        JOIN OrderedSamples ON tb_lab_num.sample_id = OrderedSamples.id_samples
        SET tb_lab_num.lab_number = LPAD(OrderedSamples.row_num, 4, '0');
    ");
  }

  public function sample_destroy($id)
  {
    $sample = Sample::where('id_samples', $id)->first();
    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=',  $sample->permohonan_uji_id)->first();

    $permohonan_uji->total_harga = (float)$permohonan_uji->total_harga - (float)$sample->cost_samples;

    $lab_num = LabNum::where('sample_id', $id);
    // ->first();
    // dd($lab_num->lab_id);
    $lab_id=$lab_num->first()->lab_id;

    $lab_num->delete();



    // // dd($permohonan_uji->total_harga);
    $permohonan_uji->save();

    if ($sample) {
      $hapus = $sample->delete();
    }
    $this->sortingNumber($lab_id,null);



    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data sample berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data sample tidak berhasil dihapus!"], 200);
    }
  }

  public function printMikro(Request $request, $id_permohonan_uji, $sample_type_id = null, $packet_id = null)
  {
    $jenis_makanan_id = $request->jenis_makanan_id;

    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', $id_permohonan_uji)
      ->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('ms_customer.deleted_at')
          ->whereNull('tb_permohonan_uji.deleted_at');
      })
      ->first();

    // dd( $permohonan_uji);

    $sample_type = SampleType::findOrFail($sample_type_id);

    $is_landing = false;
    $lab_num_max = LabNum::where('tb_lab_num.permohonan_uji_id', $id_permohonan_uji)
      ->join('tb_samples', function ($join) {
        $join->on('tb_lab_num.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_samples.deleted_at')
          ->whereNull('tb_lab_num.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_lab_num.lab_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_lab_num.deleted_at');
      })

      ->where('kode_laboratorium', "MBI")
      ->where('sample_type_id', $sample_type_id)
      ->orderBy('lab_number', 'desc')
      ->first();

    $lab_num_min = LabNum::where('tb_lab_num.permohonan_uji_id', $id_permohonan_uji)
      ->join('tb_samples', function ($join) {
        $join->on('tb_lab_num.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_samples.deleted_at')
          ->whereNull('tb_lab_num.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_lab_num.lab_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_lab_num.deleted_at');
      })
      ->where('kode_laboratorium', "MBI")
      ->where('sample_type_id', $sample_type_id)
      ->orderBy('lab_number', 'asc')
      ->first();

    if (isset($lab_num_max->lab_number) && isset($lab_num_min->lab_number)) {
      if ((int)$lab_num_max->lab_number == (int)$lab_num_min->lab_number) {
        $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number);
      } else {
        $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number) . "-" . sprintf("%04d", (int)$lab_num_max->lab_number);
      }

      ///mendapatkan format belakang strip
      $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->where('tb_samples.typesample_samples', $sample_type_id)
        // ->where('tb_samples.packet_id', $packet_id)
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.created_at', 'asc')
        ->first();

      // dd(strstr($sample->codesample_samples, '/'));

      $lab_string = $lab_num . strstr($sample->codesample_samples, '/');
    } else {
      $lab_string = null;
      $lab_num = null;
    }

    $lab_string = $lab_string;





    // $lab_num=$lab_num_min."-".$lab_num_max;




    // $lab_num = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
    // ->where('sample_type_id', $sample_type_id)
    // ->get();

    // $sort_lab_num = array_column((array)$lab_num, 'lab_number');
    // $lab_num_min = min($sort_lab_num);
    // $lab_num_max = max($sort_lab_num);



    $sample_type = SampleType::where('id_sample_type', $sample_type_id)->first();
    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_samples.*', 'ms_sample_type.*')
            ->distinct('tb_samples.id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            ->get();
        } else {
          $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_samples.*', 'ms_sample_type.*')
            ->distinct('tb_samples.id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            ->get();
        }
      } else {

        $packet_id = null;

        if (isset($packet_id)) {
          $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_samples.*', 'ms_sample_type.*')
            ->distinct('tb_samples.id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            ->get();
        } else {
          $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_samples.*', 'ms_sample_type.*')
            ->distinct('tb_samples.id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            ->get();
        }
      }
    } else {
      $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    }

    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {

          $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_method', function ($join) use ($sample_type_id, $jenis_makanan_id) {
              $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                ->whereNull('ms_method.deleted_at')
                ->whereNull('tb_sample_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $jenis_makanan_id) {
                  $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                    ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                    ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                    ->whereNull('tb_baku_mutu.deleted_at')
                    ->whereNull('ms_method.deleted_at');
                })
                ->join('ms_unit as unit_baku_mutu', function ($join) {
                  $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                    ->whereNull('unit_baku_mutu.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })


            ->select('ms_method.*', 'unit_baku_mutu.*', 'tb_baku_mutu.*')
            ->distinct('ms_method.id_method')
            ->get();
        } else {

          $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            // ->where('tb_baku_mutu.jenis_makanan_id',NULL)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_method', function ($join)  use ($sample_type_id, $jenis_makanan_id) {
              $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                ->whereNull('ms_method.deleted_at')
                ->whereNull('tb_sample_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $jenis_makanan_id) {
                  $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                    ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                    ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                    ->whereNull('tb_baku_mutu.deleted_at')
                    ->whereNull('ms_method.deleted_at');
                })
                ->join('ms_unit as unit_baku_mutu', function ($join) {
                  $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                    ->whereNull('unit_baku_mutu.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })


            ->select('ms_method.*', 'tb_baku_mutu.*', 'unit_baku_mutu.*')
            ->distinct('ms_method.id_method')
            ->get();
        }
      } else {

        if ($sample_type_id == "d34b4a50-4560-4fce-96c3-046c7080a986") {

          if (isset($packet_id)) {


            $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->where('tb_samples.packet_id', $packet_id)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('ms_method.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })


              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at');
              })
              ->select('ms_method.*', 'unit_baku_mutu.*', 'tb_baku_mutu.*')
              ->distinct('ms_method.id_method')
              ->get();


            // dd($method_all);
          } else {

            // $where_in = [];
            // foreach ($all_samples as $sample_one) {
            //   # code...
            //   array_push($where_in, $sample_one->jenis_makanan_id);
            // }

            $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              // ->whereIn('tb_baku_mutu.jenis_makanan_id', $where_in)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('ms_method.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                      // ->where('tb_baku_mutu.jenis_makanan_id', '=', $where_in)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })


              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at');
              })

              ->select(
                'ms_method.*',
                'unit_baku_mutu.*',
                'tb_baku_mutu.*'
              )
              ->distinct('ms_method.id_method')
              ->get();
          }
        } else {

          if (isset($packet_id)) {

            $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->where('tb_samples.packet_id', $packet_id)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('ms_method.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })


              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at');
              })
              ->select('ms_method.*', 'unit_baku_mutu.*', 'tb_baku_mutu.*')
              ->distinct('ms_method.id_method')
              ->get();
          } else {

            $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              // ->where('tb_baku_mutu.jenis_makanan_id',NULL)
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
                  ->whereNull('ms_method.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })


              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_sample_method.deleted_at');
              })

              ->select(
                'ms_method.*',
                'unit_baku_mutu.*',
                'tb_baku_mutu.*'
              )
              ->distinct('ms_method.id_method')
              ->get();
          }
        }
      }

      //pengurutan order ist
      $sample_type_details = SampleTypeDetail::where('sample_type_id', $sample_type_id)->orderBy('orderlist_sample_type_detail')->get();



      $method_all_temp = [];


      foreach ($sample_type_details as $sample_type_detail) {
        # code...
        foreach ($method_all as $method) {
          # code...


          // print("& ".$method->id_method." ".$sample_type_detail->method_id);
          if ($method->id_method == $sample_type_detail->method_id) {
            $method_all_temp[] = $method;
          }
        }
      }

      if ($method_all_temp != []) {
        # code...
        $method_all = $method_all_temp;
      }

      // dd( $method_all_temp);


    } else {
      $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)

        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('ms_method.deleted_at')
            ->whereNull('tb_sample_method.deleted_at')
            ->join('tb_baku_mutu', function ($join) {
              $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                // ->where('tb_baku_mutu.sampletype_id', '=',$sample_type_id)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->join('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            });
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        // ->join('tb_baku_mutu', function ($join) {
        //   $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
        //     ->where('tb_baku_mutu.sampletype_id', '=','tb_samples.typesample_samples')
        //     ->where('tb_baku_mutu.jenis_makanan_id', '=','tb_samples.jenis_makanan_id')
        //     ->whereNull('tb_baku_mutu.deleted_at')
        //     ->whereNull('ms_method.deleted_at');
        // })


        ->select('ms_method.*', 'unit_baku_mutu.*', 'tb_baku_mutu.*')
        ->distinct('ms_method.id_method')
        ->get();
    }

    // dd($method_all);






    $table = [];


    if ($sample_type_id != "6cbc3684-6166-4723-b5ba-827fb8727097") {
      foreach ($all_samples as $sample) {
        $sample_one = [];
        $sample_one["sample_type"] = $sample;
        $sample_one["result"] = [];
        foreach ($method_all as $method) {


          if (isset($jenis_makanan_id)) {
            $id_method = $method->id_method;
            $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->where('tb_sample_result.method_id', $method->id_method)
              ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_samples', function ($join) {
                $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                  ->whereNull('tb_samples.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_sample_method', function ($join) use ($id_method) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->where('tb_sample_method.method_id', $id_method)
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })
              ->leftjoin('tb_sample_analitik_progress', function ($join) {
                $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                  ->whereNull('tb_sample_analitik_progress.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at')
                  ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                  ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
              })
              ->join('ms_method', function ($join) use ($id_method, $sample_type_id, $jenis_makanan_id) {
                $join->where('ms_method.id_method', '=', $id_method)
                  ->whereNull('tb_sample_result.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $id_method, $jenis_makanan_id) {
                    $join
                      ->where('tb_baku_mutu.method_id', '=', $id_method)
                      ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                      ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_unit as unit_baku_mutu', function ($join) {
                    $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                      ->whereNull('unit_baku_mutu.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })

              ->orderBy('tb_sample_result.created_at', 'asc')
              ->first();
          } else {

            $id_method = $method->id_method;
            $jenis_makanan_id_sample = $sample->jenis_makanan_id;


            if ($sample_type_id == "d34b4a50-4560-4fce-96c3-046c7080a986") {
              $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
                ->where('ms_laboratorium.kode_laboratorium', 'MBI')
                ->where('tb_samples.typesample_samples', $sample_type_id)
                ->where('tb_sample_result.method_id', $method->id_method)
                ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id_sample)
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at');
                })
                ->join('tb_samples', function ($join) {
                  $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                    ->whereNull('tb_samples.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at');
                })
                ->join('tb_sample_method', function ($join) use ($id_method) {
                  $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                    ->where('tb_sample_method.method_id', $id_method)
                    ->whereNull('tb_sample_method.deleted_at')
                    ->whereNull('tb_samples.deleted_at');
                })
                ->leftjoin('tb_sample_analitik_progress', function ($join) {
                  $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                    ->whereNull('tb_sample_analitik_progress.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at')
                    ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                    ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
                })
                ->join('ms_method', function ($join) use ($id_method, $sample_type_id, $jenis_makanan_id_sample) {
                  $join->where('ms_method.id_method', '=', $id_method)
                    ->whereNull('tb_sample_result.deleted_at')
                    ->whereNull('ms_method.deleted_at')
                    ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $id_method, $jenis_makanan_id_sample) {
                      $join
                        ->where('tb_baku_mutu.method_id', '=', $id_method)
                        ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id_sample)
                        ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                        ->whereNull('tb_baku_mutu.deleted_at')
                        ->whereNull('ms_method.deleted_at');
                    })
                    ->join('ms_unit as unit_baku_mutu', function ($join) {
                      $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                        ->whereNull('unit_baku_mutu.deleted_at')
                        ->whereNull('tb_baku_mutu.deleted_at');
                    });
                })

                ->orderBy('tb_sample_result.created_at', 'asc')
                ->first();
            } else {

              $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
                ->where('ms_laboratorium.kode_laboratorium', 'MBI')
                ->where('tb_samples.typesample_samples', $sample_type_id)

                ->where('tb_sample_result.method_id', $method->id_method)
                ->join('ms_laboratorium', function ($join) {
                  $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                    ->whereNull('ms_laboratorium.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at');
                })
                ->join('tb_samples', function ($join) {
                  $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                    ->whereNull('tb_samples.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at');
                })
                ->join('tb_sample_method', function ($join) use ($id_method) {
                  $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                    ->where('tb_sample_method.method_id', $id_method)
                    ->whereNull('tb_sample_method.deleted_at')
                    ->whereNull('tb_samples.deleted_at');
                })
                ->leftjoin('tb_sample_analitik_progress', function ($join) {
                  $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                    ->whereNull('tb_sample_analitik_progress.deleted_at')
                    ->whereNull('tb_sample_result.deleted_at')
                    ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                    ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
                })
                ->join('ms_method', function ($join) use ($id_method, $sample_type_id) {
                  $join->where('ms_method.id_method', '=', $id_method)
                    ->whereNull('tb_sample_result.deleted_at')
                    ->whereNull('ms_method.deleted_at')
                    ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $id_method) {
                      $join
                        ->where('tb_baku_mutu.method_id', '=', $id_method)
                        ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                        ->whereNull('tb_baku_mutu.deleted_at');
                    })
                    ->join('ms_unit as unit_baku_mutu', function ($join) {
                      $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                        ->whereNull('unit_baku_mutu.deleted_at')
                        ->whereNull('tb_baku_mutu.deleted_at');
                    });
                })

                ->orderBy('tb_sample_result.created_at', 'asc')
                ->first();
            }
          }

          // print_r($result_one);






          $sample_result = [];
          $sample_result["hasil"] = isset($result_one->hasil) ? $result_one->hasil : "-";

          $sample_result["min"] = isset($result_one->min) ? $result_one->min : (isset($method->min) ? $method->min : null);
          $sample_result["max"] = isset($result_one->max) ? $result_one->max : (isset($method->max) ? $method->max : null);

          $sample_result["method_id"] = $method->id_method;

          $sample_result["offset_baku_mutu"] = isset($result_one->offset_baku_mutu) ? $result_one->offset_baku_mutu : (isset($method->offset_baku_mutu) ? $method->offset_baku_mutu : null);
          $sample_result["equal"] = isset($result_one->equal) ? $result_one->equal : (isset($method->equal) ? $method->equal : null);
          $sample_result["name_report"] = isset($result_one->name_report) ? $result_one->name_report : null;
          $sample_result["nilai_baku_mutu"] = isset($result_one->nilai_baku_mutu) ? $result_one->nilai_baku_mutu : (isset($method->nilai_baku_mutu) ? $method->nilai_baku_mutu : null);
          $sample_result["satuan_bakumutu"] = isset($result_one->unit_id) ? $result_one->name_unit : (isset($method->name_unit) ? $method->name_unit : null);
          $sample_result["keterangan"] = $result_one->keterangan ?? null;

          $sample_result["perlakuan_usap_tangan_sample_analitik_progress"] = isset($result_one->perlakuan_usap_tangan_sample_analitik_progress) ? $result_one->perlakuan_usap_tangan_sample_analitik_progress : null;

          array_push($sample_one["result"], $sample_result);
        }

        array_push($table, $sample_one);
      }
    } else {

      foreach ($method_all as $method) {

        $sample_one = [];
        $sample_one["method"] = $method;
        $sample_one["result"] = [];
        foreach ($all_samples as $sample) {


          $id_method = $method->id_method;

          $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)

            ->where('tb_sample_result.method_id', $method->id_method)
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_samples', function ($join) {
              $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_samples.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_sample_method', function ($join) use ($id_method) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->where('tb_sample_method.method_id', $id_method)
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_analitik_progress', function ($join) {
              $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_sample_analitik_progress.deleted_at')
                ->whereNull('tb_sample_result.deleted_at')
                ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
            })
            ->join('ms_method', function ($join) use ($id_method, $sample_type_id) {
              $join->where('ms_method.id_method', '=', $id_method)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $id_method) {
                  $join
                    ->where('tb_baku_mutu.method_id', '=', $id_method)
                    ->where('tb_baku_mutu.sampletype_id', '=', $sample_type_id)
                    ->whereNull('tb_baku_mutu.deleted_at');
                })
                ->join('ms_unit as unit_baku_mutu', function ($join) {
                  $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                    ->whereNull('unit_baku_mutu.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })

            ->orderBy('tb_sample_result.created_at', 'asc')
            ->first();

          if (isset($result_one)) {
            $sample_result = [];
            $sample_result["hasil"] = isset($result_one->hasil) ? $result_one->hasil : "-";
            $sample_result["min"] = isset($result_one->min) ? $result_one->min : null;
            $sample_result["max"] = isset($result_one->max) ? $result_one->max : null;
            $sample_result["method_id"] = $method->id_method;
            $sample_result["offset_baku_mutu"] = isset($result_one->offset_baku_mutu) ? $result_one->offset_baku_mutu : null;
            $sample_result["equal"] = isset($result_one->equal) ? $result_one->equal : null;
            $sample_result["name_report"] = isset($result_one->name_report) ? $result_one->name_report : null;
            $sample_result["nilai_baku_mutu"] = isset($result_one->nilai_baku_mutu) ? $result_one->nilai_baku_mutu : null;
            $sample_result["satuan_bakumutu"] = isset($result_one->unit_id) ? $result_one->name_unit : null;
            $sample_result["perlakuan_usap_tangan_sample_analitik_progress"] = isset($result_one->perlakuan_usap_tangan_sample_analitik_progress) ? $result_one->perlakuan_usap_tangan_sample_analitik_progress : null;
            $sample_result["sample"] = $sample;
            $sample_result["keterangan"] = $result_one->keterangan ?? null;
            array_push($sample_one["result"], $sample_result);
          }
        }

        // print_r($result_one);

        array_push($table, $sample_one);
      }
    }

    // dd($table);





    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
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
            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftJoin('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->on('tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples', 'permohonan_uji_id', 'name_sample_type')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            ->take(1)
            ->first();
        } else {
          $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
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
            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftJoin('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->on('tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples', 'permohonan_uji_id', 'name_sample_type')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            ->take(1)
            ->first();
        }
      } else {
        if (isset($packet_id)) {
          $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
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
            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftJoin('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->on('tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples', 'permohonan_uji_id', 'name_sample_type')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            ->take(1)
            ->first();
        } else {
          $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('tb_samples.typesample_samples', $sample_type_id)
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
            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftJoin('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->on('tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples', 'permohonan_uji_id', 'name_sample_type')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            ->take(1)
            ->first();
        }
      }
    } else {
      $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
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
        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftJoin('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->on('tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples', 'permohonan_uji_id', 'name_sample_type', 'tb_lab_num.*')
        ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
        ->take(1)
        ->first();
    }

    $sample_ids = [];
    foreach ($table as $mytable) {
      $sample_ids[] = data_get($mytable, 'sample_type.id_samples');
    }


    $lab_nums = LabNum::whereIn('sample_id', $sample_ids)->whereNull('deleted_at')->distinct()->pluck('lab_number', 'sample_id')->all();

    // dd($lab_nums, $sample_ids, $table);


    $pengesahan_hasil = PengesahanHasil::where('sample_id', '=', $sample->id_samples)->where('laboratorium_id', '=', $sample->laboratorium_id)->first();

    $agenda = $request->input('agenda');
    // dd($agenda);

    $no_LHU = LHU::where('sample_id', '=', $sample->id_samples)->where('lab_id', '=', $sample->laboratorium_id)->first();

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::where(DB::raw('YEAR(created_at)'), '=', date('Y'))->max('nomer_urut_LHU');
      // $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));
      $no_LHU->nomer_LHU = '400.7/A.' . $no_LHU->nomer_urut_LHU . '/4.2.26/' . Carbon::now()->format('Y');
      $no_LHU->sample_id = $sample->id_samples;
      $no_LHU->lab_id = $sample->id_laboratorium;
      $no_LHU->save();

      $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
      $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');

      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '400.7/' . $agenda . '/4.2.26/' . $year
        : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . $year;

    } else {
      if (isset($pengesahan_hasil)) {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('Y');
      } else {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');
      }

      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '400.7/' . $agenda . '/4.2.26/' . $year
        : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . $year;


    }

    $laboratorium = Laboratorium::findOrFail($sample->id_laboratorium);

    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      } else {
        if (isset($packet_id)) {
          $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      }
    } else {
      $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
        // ->take(1)
        ->firstOrFail();
    }

    $checking_min = $checking_min->date_checking;



    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {

          $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->first();
        } else {
          $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->first();
        }
      } else {

        if (isset($packet_id)) {

          $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->first();
        } else {
          $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->first();
        }
      }
    } else {

      $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
        // ->take(1)
        ->first();
    }

    // dd($checking_min);

    $done_max = $done_max->date_done_estimation_labs;

    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {

          $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      } else {
        if (isset($packet_id)) {

          $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      }
    } else {
      $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
        // ->take(1)
        ->firstOrFail();
    }


    $diambil_min = $diambil_min->datesampling_samples;

    // dd($diambil_min);
    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      } else {

        if (isset($packet_id)) {
          $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      }
    } else {
      $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
        // ->take(1)
        ->firstOrFail();
    }

    $diambil_max = $diambil_max->datesampling_samples;


    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $number_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $number_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      } else {

        if (isset($packet_id)) {
          $number_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'desc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $number_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'desc')
            // ->take(1)
            ->firstOrFail();
        }
      }
    } else {
      $number_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_samples.count_id', 'desc')
        // ->take(1)
        ->firstOrFail();
    }


    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $number_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $number_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            // ->take(1)
            ->firstOrFail();
        }
      } else {

        if (isset($packet_id)) {
          $number_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            // ->take(1)
            ->firstOrFail();
        } else {
          $number_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_penanganan', function ($join) {
              $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

                // ->limit(1)
                ->whereNull('tb_sample_penanganan.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_method.deleted_at');
            })

            ->leftjoin('tb_pengesahan_hasil', function ($join) {
              $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
                ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
                ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->whereNull('tb_pengesahan_hasil.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
            ->orderBy('tb_samples.count_id', 'asc')
            // ->take(1)
            ->firstOrFail();
        }
      }
    } else {
      $number_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            // ->limit(1)
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->leftjoin('tb_pengesahan_hasil', function ($join) {
          $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
            ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
            ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
            ->whereNull('tb_pengesahan_hasil.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
        ->orderBy('tb_samples.count_id', 'asc')
        // ->take(1)
        ->firstOrFail();
    }

    if (isset($sample_type_id)) {
      if (isset($jenis_makanan_id)) {
        if (isset($packet_id)) {
          $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
            // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
            // ->orderBy('ms_method.created_at')
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.packet_id', $packet_id)
            ->join('ms_method', function ($join)  use ($jenis_makanan_id, $sample_type_id) {
              $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                ->whereNull('tb_laboratorium_method.deleted_at')
                ->whereNull('ms_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($jenis_makanan_id, $sample_type_id) {
                  $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                    ->where('tb_baku_mutu.jenis_makanan_id', $jenis_makanan_id)
                    ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                    ->whereNull('tb_baku_mutu.deleted_at')
                    ->whereNull('ms_method.deleted_at');
                })
                ->join('ms_library', function ($join) {
                  $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                    ->whereNull('ms_library.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })
            ->leftjoin('tb_sample_result', function ($join) {
              $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                ->whereNull('tb_laboratorium_method.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_samples', function ($join) {
              $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                ->whereNull('tb_samples.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                // ->where('ms_laboratorium.kode_laboratorium', 'KIM')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_laboratorium_method.deleted_at');
            })
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            ->distinct('ms_library.id_library')
            ->select('ms_library.*')
            ->get();
        } else {
          $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
            // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
            // ->orderBy('ms_method.created_at')
            ->where('ms_laboratorium.kode_laboratorium', 'MBI')
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->where('tb_samples.typesample_samples', $sample_type_id)
            ->join('ms_method', function ($join) use ($jenis_makanan_id, $sample_type_id) {
              $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                ->whereNull('tb_laboratorium_method.deleted_at')
                ->whereNull('ms_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($jenis_makanan_id, $sample_type_id) {
                  $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                    ->where('tb_baku_mutu.jenis_makanan_id', $jenis_makanan_id)
                    ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                    ->whereNull('tb_baku_mutu.deleted_at')
                    ->whereNull('ms_method.deleted_at');
                })
                ->join('ms_library', function ($join) {
                  $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                    ->whereNull('ms_library.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })

            ->leftjoin('tb_sample_result', function ($join) {
              $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                ->whereNull('tb_laboratorium_method.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_samples', function ($join) {
              $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                ->whereNull('tb_samples.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                ->where('ms_laboratorium.kode_laboratorium', 'MBI')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_laboratorium_method.deleted_at');
            })
            ->join('tb_sample_method', function ($join) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })

            ->distinct('ms_library.id_library')
            ->select('ms_library.*')
            ->get();
        }
      } else {
        if ($sample_type_id == "d34b4a50-4560-4fce-96c3-046c7080a986") {
          if (isset($packet_id)) {
            $where_in = [];
            foreach ($all_samples as $sample_one) {
              # code...
              array_push($where_in, $sample_one->jenis_makanan_id);
            }
            $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
              // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
              // ->orderBy('ms_method.created_at')
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->where('tb_samples.packet_id', $packet_id)
              ->whereIn('tb_samples.jenis_makanan_id', $where_in)
              ->join('ms_method', function ($join) use ($sample_type_id, $where_in) {
                $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $where_in) {
                    $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                      ->whereIn('tb_baku_mutu.jenis_makanan_id', $where_in)
                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_library', function ($join) {
                    $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                      ->whereNull('ms_library.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })

              ->leftjoin('tb_sample_result', function ($join) {
                $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                  ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_samples', function ($join) {
                $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                  ->whereNull('tb_samples.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                  // ->where('ms_laboratorium.kode_laboratorium', 'KIM')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_laboratorium_method.deleted_at');
              })
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })

              ->distinct('ms_library.id_library')
              ->select('ms_library.*')
              ->get();
          } else {
            $where_in = [];
            foreach ($all_samples as $sample_one) {
              # code...
              array_push($where_in, $sample_one->jenis_makanan_id);
            }


            $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
              // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
              // ->orderBy('ms_method.created_at')
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->whereIn('tb_samples.jenis_makanan_id', $where_in)
              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->where('ms_laboratorium.kode_laboratorium', 'MBI')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_laboratorium_method.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id, $where_in) {
                $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id, $where_in) {
                    $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                      ->whereIn('tb_baku_mutu.jenis_makanan_id', $where_in)
                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_library', function ($join) {
                    $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                      ->whereNull('ms_library.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })
              ->leftjoin('tb_sample_result', function ($join) {
                $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                  ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_samples', function ($join) {
                $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                  ->whereNull('tb_samples.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })

              ->distinct('ms_library.id_library')
              ->select('ms_library.*')
              ->get();
            // dd($all_acuan_baku_mutu);
          }
        } else {
          if (isset($packet_id)) {

            $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
              // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
              // ->orderBy('ms_method.created_at')
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->where('tb_samples.packet_id', $packet_id)
              // ->whereIn('tb_samples.jenis_makanan_id')
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_library', function ($join) {
                    $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                      ->whereNull('ms_library.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })

              ->leftjoin('tb_sample_result', function ($join) {
                $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                  ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_samples', function ($join) {
                $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                  ->whereNull('tb_samples.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                  // ->where('ms_laboratorium.kode_laboratorium', 'KIM')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_laboratorium_method.deleted_at');
              })
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })

              ->distinct('ms_library.id_library')
              ->select('ms_library.*')
              ->get();
          } else {



            $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
              // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
              // ->orderBy('ms_method.created_at')
              ->where('ms_laboratorium.kode_laboratorium', 'MBI')
              ->where('tb_samples.typesample_samples', $sample_type_id)
              ->join('ms_laboratorium', function ($join) {
                $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->where('ms_laboratorium.kode_laboratorium', 'MBI')
                  ->whereNull('ms_laboratorium.deleted_at')
                  ->whereNull('tb_laboratorium_method.deleted_at');
              })
              ->join('ms_method', function ($join) use ($sample_type_id) {
                $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('ms_method.deleted_at')
                  ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
                    $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)

                      ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                      ->whereNull('tb_baku_mutu.deleted_at')
                      ->whereNull('ms_method.deleted_at');
                  })
                  ->join('ms_library', function ($join) {
                    $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                      ->whereNull('ms_library.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  });
              })
              ->leftjoin('tb_sample_result', function ($join) {
                $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
                  ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
                  ->whereNull('tb_laboratorium_method.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_samples', function ($join) {
                $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

                  ->whereNull('tb_samples.deleted_at')
                  ->whereNull('tb_sample_result.deleted_at');
              })
              ->join('tb_sample_method', function ($join) {
                $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                  ->whereNull('tb_sample_method.deleted_at')
                  ->whereNull('tb_samples.deleted_at');
              })

              ->distinct('ms_library.id_library')
              ->select('ms_library.*')
              ->get();
            // dd($all_acuan_baku_mutu);
          }
        }
      }
    } else {
      $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
        // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
        // ->orderBy('ms_method.created_at')
        ->where('ms_laboratorium.kode_laboratorium', 'MBI')
        ->where('tb_samples.typesample_samples', $sample_type_id)
        ->join('ms_method', function ($join) use ($sample_type_id) {
          $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_method.deleted_at')
            ->join('tb_baku_mutu', function ($join) use ($sample_type_id) {
              $join->where('tb_baku_mutu.sampletype_id', $sample_type_id)
                ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->join('ms_library', function ($join) {
              $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                ->whereNull('ms_library.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            });
        })

        ->leftjoin('tb_sample_result', function ($join) {
          $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
            ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('tb_sample_result.deleted_at');
        })
        ->join('tb_samples', function ($join) {
          $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

            ->whereNull('tb_samples.deleted_at')
            ->whereNull('tb_sample_result.deleted_at');
        })
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
            // ->where('ms_laboratorium.kode_laboratorium', 'KIM')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_laboratorium_method.deleted_at');
        })

        ->distinct('ms_library.id_library')
        ->select('ms_library.*')
        ->get();
    }


    $samplePrint = $request->printSamples;

    if(isset($samplePrint)){

      if(count($samplePrint) > count($table)){
        $agenda = $request->input('agenda');

        $no_agenda = !empty($agenda) ? '400.7/' . $agenda . '/4.2.26/' . 2025 : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . 2025;
        $idPermohonanUji = $id_permohonan_uji;

        $permohonan_uji = PermohonanUji::query()->where('id_permohonan_uji', $idPermohonanUji)->first();
        $customer= $permohonan_uji->customer;

        $samples = Sample::query()->where('permohonan_uji_id', $idPermohonanUji)
                    ->whereHas('samplemethod', function ($query) {
                        $query->where('laboratorium_id', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5");
                    })
                    ->with(['sampleresult.method', 'labnum', 'sampleresult.method.bakumutu', 'sampleresult.method.bakumutu.unit'])
                    ->orderBy('count_id', 'asc')
                    ->get();


        $samplePrint = $request->printSamples;

        if(isset($samplePrint)){
          if(count($samples) > count($samplePrint)){
            $samples = Sample::query()->where('permohonan_uji_id', $idPermohonanUji)
                    ->whereIn('id_samples', $samplePrint)
                    ->whereHas('samplemethod', function ($query) {
                        $query->where('laboratorium_id', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5");
                    })
                    ->with(['sampleresult.method', 'labnum', 'sampleresult.method.bakumutu', 'sampleresult.method.bakumutu.unit'])
                    ->orderBy('count_id', 'asc')
                    ->get();
          }
        }

        $listVerifications =
        $jenis_sarana = [];
        $tanggal_sampling = [];
        $labNums = [];
        $metode_pemeriksaan = [];
        $params = [];
        $methods = [];
        $results = [];
        $bakumutu = [];

        foreach($samples as $sample){
          $jenis_sarana[] = $sample->jenis_sarana_names;
          $tanggal_sampling[] = Carbon::parse($sample->datesampling_samples)->toDateString();
          $labNums[] = $sample->labnum[0]->lab_number;

          $result_item = [
            'labnum' => $sample->count_id,
            'lokasi' => $sample->location_samples ?? $sample->name_pelanggan,
            'jam_sampling' => date("H:i", strtotime($sample->datesampling_samples)),
            'hasil' => [],
          ];

          foreach($sample->sampleresult as $sampleresult){
            $metode_pemeriksaan[] = $sampleresult->metode;
            $result_item['hasil'][] = [
              $sampleresult->method_id => [
                'hasil' => $sampleresult->hasil,
                'metode' => $sampleresult->metode,
                'keterangan' => $sampleresult->keterangan,
                'offset_baku_mutu' => $sampleresult->offset_baku_mutu,
                'bakumutu' => null
              ]
            ];

            foreach($sampleresult->method as $method)
            {
              $bakumutu[$method->id_method] = $method->bakumutu;
              if ($method->params_method == "E-Coli (Membran Filter)") {
                $params[$method->id_method] = "Escherichia coli";
              } elseif ($method->params_method == "Total Coliform (Membrane Filter)") {
                  $params[$method->id_method] = "Total Coliform";
              } else {
                  $params[$method->id_method] = $method->params_method;
              }
            }
          }

          $results[] = $result_item;
        }

        if(count($labNums) == 1){
          $no_register = "0".$labNums[0]."/AB-AM- Bact/2025";
        }else{
          $no_register = "0".min($labNums)." - "."0".max($labNums)."/AB-AM- Bact/2025";
        }

        $verificationActivitySamples = VerificationActivitySample::query()->where('id_sample', '=', $samples[0]->id_samples)->get();
        $listVerifications = [];
        foreach ($verificationActivitySamples as  $verificationActivitySample) {
          $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
        }

        $tembusan = Sample::where('id_samples', $samples[0]->id_samples)->get('tembusan')->first()->tembusan;

        $signOption = $request->signOption;


        arsort($params);
        if(isset($signOption) and $signOption == 0){
          $data = [
            "no_agenda" => $no_agenda,
            "no_register" => $no_register,
            "nama_pelanggan" => $customer->name_customer,
            "alamat_pelanggan" => $customer->address_customer,
            "jenis_sampel" => "Air Bersih dan Air Minum",
            "jenis_sarana" => $jenis_sarana,
            "metode_pemeriksaan" => array_unique($metode_pemeriksaan),
            "petugas_sampling" => $permohonan_uji->name_sampling,
            "tanggal_sampling" => $tanggal_sampling,
            "parameter" => array_unique($params),
            "results" => $samples
          ];

          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih_air_minum', compact('data', 'listVerifications', 'tembusan', 'signOption'));
          return $pdf->stream();
        }else{


          $url = env("APP_URL");
          if (env("APP_ENV") == "local"){
            $url .= ":8000";
          }
          $url .= "/elits-signature/progress/".$sample->sample_id."/0";

          $result = Builder::create()
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(600)
            ->margin(2)
            ->build();

          $qrBase64 = base64_encode($result->getString());


          $data = [
            "no_agenda" => $no_agenda,
            "no_register" => $no_register,
            "nama_pelanggan" => $customer->name_customer,
            "alamat_pelanggan" => $customer->address_customer,
            "jenis_sampel" => "Air Bersih dan Air Minum",
            "jenis_sarana" => $jenis_sarana,
            "metode_pemeriksaan" => array_unique($metode_pemeriksaan),
            "petugas_sampling" => $permohonan_uji->name_sampling,
            "tanggal_sampling" => $tanggal_sampling,
            "parameter" => array_unique($params),
            "results" => $samples,
            "qr_code" => ""
          ];

          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih_air_minum', compact('data', 'listVerifications', 'tembusan', 'signOption', 'qrBase64'));

          $dataKepalaLab = Petugas::query()->where('nik', '=', '3309094611720002')->where('nama', '=', 'dr. Muharyati')->get(['nik', 'password'])->first();

          if (!isset($agenda)){
            return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap input agenda!');
          }

          if (count($listVerifications) < 5){
            return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
          }

          if (isset($dataKepalaLab->nik) and isset($dataKepalaLab->password)){

            $dataPetugasBSRE[] = [
              'nik' => $dataKepalaLab->nik,
              'passPhrase' => $dataKepalaLab->password,
              'tampilan' => 'invisible',
              'reason' => "hasil12121",
              'location' => "boyolali",
              'text' => "hasil22323"
            ];

            $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

            if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
              $data =  base64_encode($signBSRE["data"]["file"]);

              return view('masterweb::module.admin.laboratorium.sample.blob',
                compact('data')
              );
            }elseif ($signBSRE['status'] == 500){
              return redirect()->back()->with('error-laporan', 'errors');
            }else{
              return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
            }

          }else{
            return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
          }
        }
      }


      $labnumspartial = [];
      if(count($table) > count($samplePrint)){
        $filteredTable = array_filter($table, function($item) use ($samplePrint){
          return in_array($item['sample_type']->id_samples, $samplePrint);
        });

        $all_samples = $all_samples->filter(function ($sample) use ($samplePrint){
          return in_array($sample->id_samples, $samplePrint);
        });

        $table = $filteredTable;
        $lab_string = '/' . explode('/', $lab_string, 2)[1];

        foreach($table as $tbl){
          $labnumspartial[] = $tbl['sample_type']->count_id;
        }

        $labnumspartial = array_map(function($num) {
          return str_pad($num, 4, '0', STR_PAD_LEFT);
        }, $labnumspartial);

        if(count($labnumspartial) == 1){
          $labnumpartial = implode(',', $labnumspartial);
          $lab_string = $labnumpartial. $lab_string;
        }else{
          $maxlabnum = max($labnumspartial);
          $minlabnum = min($labnumspartial);

          $lab_string = $minlabnum.'-'.$maxlabnum. $lab_string;
        }
      }
    }

    $tembusan = Sample::where('id_samples', $sample->id_samples)->get('tembusan')->first()->tembusan;

    $sample_type = SampleType::findOrFail($sample_type_id);

    # [Baca Hasil] Ambil keterangan untuk metode pemeriksaan.
    $metode_pemeriksaan = [];

    foreach($table as $smp){

      if(isset($smp["sample_type"])){
        $metode = SampleResult::where('laboratorium_id', $sample->laboratorium_id)
          ->where('sample_id', $smp["sample_type"]->id_samples)
          ->whereNull('deleted_at')
          ->pluck('metode')->unique()->toArray();

        $metode_pemeriksaan = array_unique(array_merge($metode_pemeriksaan, $metode));

      }
    }

    // # Verifikasi 2
    $verificationActivitySamples = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->get();
    $listVerifications = [];
    foreach ($verificationActivitySamples as  $verificationActivitySample) {
      $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
    }

    // # Tanggal Pengambilan s.d.
    $tanggal_pengambilan = [];
    foreach ($table as $row) {
      $tanggal = data_get($row, 'sample_type.datesampling_samples');
      $tanggals = explode(' ', $tanggal);
      $tanggal_pengambilan[] = head($tanggals);
    }

    // dd($table);
    sort($tanggal_pengambilan);
    $tanggal_pengambilan = array_unique($tanggal_pengambilan);
    // dd($tanggal_pengambilan);

    // # Semi Hard Code total coliform.
    // foreach ($method_all as $index => $method) {
    //   if (\Illuminate\Support\Str::slug($method->name_report) === 'total-coliform') {
    //     $method_all->forget($index);
    //     $method_all->prepend($method);
    //     break;
    //   }
    // }

    // 1. Pendaftaran / Registrasi
    // 2. Pemeriksaan / Analitik
    // 3. Input / Output Hasil Px
    // 4. Verifikasi
    // 5. Validasi

    // dd($listVerifications);
    $url = env("APP_URL");
    if (env("APP_ENV") == "local"){
      $url .= ":8000";
    }
    $url .= "/elits-signature/progress/".$sample->sample_id."/0";

    $result = Builder::create()
      ->data($url)
      ->encoding(new Encoding('UTF-8'))
      ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
      ->size(600)
      ->margin(2)
      ->build();

    $qrBase64 = base64_encode($result->getString());

    $signOption = $request->signOption;
    if (isset($signOption) and $signOption == 0){
      $payloads = compact(
        'all_acuan_baku_mutu',
        'number_min',
        'number_max',
        'permohonan_uji',
        'table',
        'sample',
        'tembusan',
        'checking_min',
        'done_max',
        'diambil_min',
        'diambil_max',
        'laboratorium',
        'no_LHU',
        'all_samples',
        'lab_num',
        'lab_string',
        'method_all',
        'pengesahan_hasil',
        'lab_nums',
        'listVerifications',
        'metode_pemeriksaan',
        'tanggal_pengambilan',
        'agenda',
        'signOption'
      );

      switch (Str::lower(str_replace(' ', '', $sample_type->name_sample_type))) {
        case Str::lower(str_replace(' ', '', "Air Minum")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_minum', $payloads);
          return $pdf->stream();
        }

        case Str::lower(str_replace(' ', '', "Makanan/Minuman/Lainnya")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.alt_makan_minum', $payloads);
          return $pdf->stream();
        }

        case Str::lower(str_replace(' ', '', "Uji Usap")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.uji_usap', $payloads);
          return $pdf->stream();
        }

        case Str::lower(str_replace(' ', '', "Air Bersih")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih', $payloads);
          return $pdf->stream();
        }

        case Str::lower(str_replace(' ', '', "Air Limbah")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_limbah', $payloads);
          return $pdf->stream();
        }

        case Str::lower(str_replace(' ', '', "Kualitas Udara")): {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.kualitas_udara', $payloads);
          return $pdf->stream();
        }

        default: {
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.mikro', $payloads);
          return $pdf->stream();
        }
      }
    }

    $payloads = compact(
      'all_acuan_baku_mutu',
      'number_min',
      'number_max',
      'permohonan_uji',
      'table',
      'sample',
      'tembusan',
      'checking_min',
      'done_max',
      'diambil_min',
      'diambil_max',
      'laboratorium',
      'no_LHU',
      'all_samples',
      'lab_num',
      'lab_string',
      'method_all',
      'pengesahan_hasil',
      'lab_nums',
      'listVerifications',
      'metode_pemeriksaan',
      'tanggal_pengambilan',
      'agenda',
      'qrBase64'
    );

    switch (Str::lower(str_replace(' ', '', $sample_type->name_sample_type))) {
      case Str::lower(str_replace(' ', '', "Air Minum")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_minum', $payloads);
          break;

      case Str::lower(str_replace(' ', '', "Makanan/Minuman/Lainnya")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.alt_makan_minum', $payloads);
          break;

      case Str::lower(str_replace(' ', '', "Uji Usap")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.uji_usap', $payloads);
          break;

      case Str::lower(str_replace(' ', '', "Air Bersih")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih', $payloads);
          break;

      case Str::lower(str_replace(' ', '', "Air Limbah")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_limbah', $payloads);
          break;

      case Str::lower(str_replace(' ', '', "Kualitas Udara")):
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.kualitas_udara', $payloads);
          break;

      default:
          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.mikro', $payloads);
          break;
    }

    $dataKepalaLab = Petugas::query()->where('nik', '=', '3309094611720002')->where('nama', '=', 'dr. Muharyati')->get(['nik', 'password'])->first();

    if (!isset($agenda)){
      return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap input agenda!');
    }

    if (count($listVerifications) < 5){
      return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
    }

    if (isset($dataKepalaLab->nik) and isset($dataKepalaLab->password)){

      $dataPetugasBSRE[] = [
        'nik' => $dataKepalaLab->nik,
        'passPhrase' => $dataKepalaLab->password,
        'tampilan' => 'invisible',
        'reason' => "hasil12121",
        'location' => "boyolali",
        'text' => "hasil22323"
      ];

      $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

      if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
        $data =  base64_encode($signBSRE["data"]["file"]);

        return view('masterweb::module.admin.laboratorium.sample.blob',
          compact('data')
        );
      }elseif ($signBSRE['status'] == 500){
        return redirect()->back()->with('error-laporan', 'errors');
      }else{
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
      }

    }else{
      return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
    }

  }

  public function printKimia(Request $request,$id_permohonan_uji)
  {
    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', $id_permohonan_uji)
      ->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('ms_customer.deleted_at')
          ->whereNull('tb_permohonan_uji.deleted_at');
      })->first();

    $makmin = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->where('kode_laboratorium', 'KIM')
      ->distinct('typesample_samples')
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
          ->on('tb_baku_mutu.jenis_makanan_id', '=', 'tb_samples.jenis_makanan_id')
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('ms_unit as unit_baku_mutu', function ($join) {
        $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
          ->whereNull('unit_baku_mutu.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->where('name_sample_type', 'Makanan/Minuman/Lainnya')
      ->select('ms_sample_type.name_sample_type')
      ->get();






    // dd($method_all);

    $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_samples.permohonan_uji_id', '=', $id_permohonan_uji)
      // ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
      // ->orderBy('ms_method.created_at')

      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })

      ->leftjoin('tb_sample_result', function ($join) {
        $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
          ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('tb_sample_result.deleted_at');
      })
      ->join('tb_samples', function ($join) {
        $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

          ->whereNull('tb_samples.deleted_at')
          ->whereNull('tb_sample_result.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
          ->where('ms_laboratorium.kode_laboratorium', 'KIM')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_laboratorium_method.deleted_at');
      })
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('tb_baku_mutu', function ($join) {
        $join->on('tb_samples.typesample_samples', 'tb_baku_mutu.sampletype_id')
          ->on('tb_baku_mutu.method_id', '=', 'tb_sample_method.method_id')

          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })

      ->join('ms_library', function ($join) {
        $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
          ->whereNull('ms_library.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->distinct('ms_library.id_library')
      ->select('ms_library.*')
      ->get();

    // dd($all_acuan_baku_mutu);


    if (count($makmin) > 0) {
      $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->where('name_sample_type', 'Makanan/Minuman/Lainnya')
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*', 'tb_sample_penanganan.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'ASC')
        ->get();

      $sample_type = SampleType::where('name_sample_type', 'Makanan/Minuman/Lainnya')->first();

      $lab_num_max = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
        ->orderBy('lab_number', 'asc')
        ->first();

      $lab_num_min = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
        ->orderBy('lab_number', 'desc')
        ->first();
      if (isset($lab_num_max->lab_number) && isset($lab_num_min->lab_number)) {
        if ((int)$lab_num_max->lab_number == (int)$lab_num_min->lab_number) {
          $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number);
        } else {
          $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number) . "-" . sprintf("%04d", (int)$lab_num_max->lab_number);
        }
        if (isset($lab_num_max->lab_string) && $lab_num_max->lab_string != "") {
          $lab_string = $lab_num_max->lab_string;
        } else {
          $lab_string = $lab_num . '/Mak-KIM/' . getRomawi((int)$lab_num_max->mount_lab_num) . '/' . (int)$lab_num_max->year_lab_num;


          $lab_num_max = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
            ->orderBy('lab_number', 'asc')
            ->get();


          foreach ($lab_num_max as $item) {
            # code...
            $lab_num = LabNum::findOrFail($item->id_lab_num);
            $lab_num->lab_string = $lab_string;
            $lab_num->save();
          }
        }
      } else {
        $lab_string = null;
        $lab_num = null;
      }
      $lab_string = $lab_num;
    } else {
      $all_samples = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->orderBy('tb_samples.count_id', 'ASC')
        ->where('name_sample_type', 'PUDAM SISA CHLOR + FISIKA')
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*', 'tb_sample_penanganan.*')
        ->distinct('tb_samples.id_samples')
        ->get();
      $sample_type = SampleType::where('name_sample_type', 'PUDAM SISA CHLOR + FISIKA')->first();

      $lab_num_max = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
        ->orderBy('lab_number', 'asc')
        ->first();

      $lab_num_min = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
        ->orderBy('lab_number', 'desc')
        ->first();
      if (isset($lab_num_max->lab_number) && isset($lab_num_min->lab_number)) {
        if ((int)$lab_num_max->lab_number == (int)$lab_num_min->lab_number) {
          $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number);
        } else {
          $lab_num = sprintf("%04d", (int)$lab_num_min->lab_number) . "-" . sprintf("%04d", (int)$lab_num_max->lab_number);
        }
        if (isset($lab_num_max->lab_string) && $lab_num_max->lab_string != "") {
          $lab_string = $lab_num_max->lab_string;
        } else {
          $lab_string = $lab_num . '/Mak-KIM/' . getRomawi((int)$lab_num_max->mount_lab_num) . '/' . (int)$lab_num_max->year_lab_num;


          $lab_num_max = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
            ->orderBy('lab_number', 'asc')
            ->get();


          foreach ($lab_num_max as $item) {
            # code...
            $lab_num = LabNum::findOrFail($item->id_lab_num);
            $lab_num->lab_string = $lab_string;
            $lab_num->save();
          }
          $lab_num = sprintf("%04d", (int)$lab_num->lab_number);
        }
      } else {
        $lab_string = null;
        $lab_num = null;
      }
    }

    if (count($makmin) > 0) {
      // $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      //   ->where('ms_laboratorium.kode_laboratorium', 'KIM')
      //   ->where('tb_samples.typesample_samples', 'd34b4a50-4560-4fce-96c3-046c7080a986')
      //   ->join('tb_sample_method', function ($join) {
      //     $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
      //       ->whereNull('tb_sample_method.deleted_at')
      //       ->whereNull('tb_samples.deleted_at');
      //   })
      //   ->join('ms_method', function ($join) {
      //     $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
      //       ->whereNull('ms_method.deleted_at')
      //       ->whereNull('tb_sample_method.deleted_at');
      //   })
      //   ->join('tb_baku_mutu', function ($join) {
      //     $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
      //       ->on('tb_baku_mutu.sampletype_id', '=', 'tb_samples.typesample_samples')
      //       ->whereNull('tb_baku_mutu.deleted_at')
      //       ->whereNull('ms_method.deleted_at');
      //   })
      //   ->join('ms_unit as unit_baku_mutu', function ($join) {
      //     $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
      //       ->whereNull('unit_baku_mutu.deleted_at')
      //       ->whereNull('tb_baku_mutu.deleted_at');
      //   })
      //   ->join('ms_laboratorium', function ($join) {
      //     $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
      //       ->whereNull('ms_laboratorium.deleted_at')
      //       ->whereNull('tb_sample_method.deleted_at');
      //   })
      //   ->select('ms_method.*')
      //   ->distinct('ms_method.id_method')
      //   ->get();
      $where_in = [];
      foreach ($all_samples as $sample_one) {
        # code...
        array_push($where_in, $sample_one->jenis_makanan_id);
      }
      $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', 'd34b4a50-4560-4fce-96c3-046c7080a986')
        ->whereIn('tb_baku_mutu.jenis_makanan_id', $where_in)
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_method', function ($join) use ($where_in) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('ms_method.deleted_at')
            ->whereNull('tb_sample_method.deleted_at')
            ->join('tb_baku_mutu', function ($join) use ($where_in) {
              $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->where('tb_baku_mutu.sampletype_id', '=', 'd34b4a50-4560-4fce-96c3-046c7080a986')
                ->where('tb_baku_mutu.jenis_makanan_id', '=', $where_in)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->join('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            });
        })


        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->select(
          'ms_method.*',
          'unit_baku_mutu.*',
          'tb_baku_mutu.*'
        )
        ->distinct('ms_method.id_method')
        ->get();
    } else {
      $method_all = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', '0e9a1091-b248-44e9-921b-5b4773109603')
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
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })

        ->join('tb_baku_mutu', function ($join) {
          $join->on('tb_baku_mutu.method_id', '=', 'tb_sample_method.method_id')
            ->where('tb_baku_mutu.sampletype_id', '=', "0e9a1091-b248-44e9-921b-5b4773109603")
            ->whereNull('tb_baku_mutu.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })
        ->join('ms_unit as unit_baku_mutu', function ($join) {
          $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
            ->whereNull('unit_baku_mutu.deleted_at')
            ->whereNull('tb_baku_mutu.deleted_at');
        })
        ->select('ms_method.*', 'unit_baku_mutu.*', 'tb_baku_mutu.*')
        ->distinct('ms_method.id_method')
        ->get();
    }



    $lab_string = $lab_num;


    if (count($makmin) > 0) {

      $all_samples_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', 'd34b4a50-4560-4fce-96c3-046c7080a986')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'DESC')
        ->first();

      if (isset($all_samples_max)) {
        $all_samples_max = $all_samples_max->codesample_samples;
      }


      $all_samples_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', 'd34b4a50-4560-4fce-96c3-046c7080a986')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'ASC')
        ->first();

      if (isset($all_samples_min)) {
        $all_samples_min = $all_samples_min->codesample_samples;
      }
    } else {


      $all_samples_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', '0e9a1091-b248-44e9-921b-5b4773109603')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'DESC')
        ->first();

      if (isset($all_samples_max)) {
        $all_samples_max = $all_samples_max->codesample_samples;
      }

      $all_samples_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
        ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        ->where('tb_samples.typesample_samples', '0e9a1091-b248-44e9-921b-5b4773109603')
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })

        ->join('ms_laboratorium', function ($join) {
          $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
            ->whereNull('ms_laboratorium.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
            ->whereNull('ms_sample_type.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select('tb_samples.*', 'ms_sample_type.*')
        ->distinct('tb_samples.id_samples')
        ->orderBy('tb_samples.count_id', 'ASC')
        ->first();

      if (isset($all_samples_min)) {
        $all_samples_min = $all_samples_min->codesample_samples;
      }
    }



    // dd($all_samples_max->codesample_samples." ".$all_samples_min->codesample_samples);

    $table = [];


    foreach ($all_samples as $sample) {
      $sample_one = [];
      $sample_one["sample_type"] = $sample;
      $sample_one["result"] = [];
      $jenis_makanan_id = $sample->jenis_makanan_id;
      $sample_one["jenis_makanan"] = JenisMakanan::where('id_jenis_makanan', $jenis_makanan_id)->first();

      foreach ($method_all as $method) {

        $sampletype = $sample->typesample_samples;

        $id_method = $method->id_method;
        if (isset($jenis_makanan_id)) {
          $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
            ->where('ms_laboratorium.kode_laboratorium', 'KIM')
            ->where('tb_samples.typesample_samples', $sampletype)
            ->where('tb_sample_result.method_id', $method->id_method)
            ->where('tb_samples.jenis_makanan_id', $jenis_makanan_id)
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_samples', function ($join) {
              $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_samples.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_sample_method', function ($join) use ($id_method) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->where('tb_sample_method.method_id', $id_method)
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_analitik_progress', function ($join) {
              $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_sample_analitik_progress.deleted_at')
                ->whereNull('tb_sample_result.deleted_at')
                ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
            })
            ->join('ms_method', function ($join) use ($id_method, $sampletype, $jenis_makanan_id) {
              $join->where('ms_method.id_method', '=', $id_method)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($sampletype, $id_method, $jenis_makanan_id) {
                  $join
                    ->where('tb_baku_mutu.method_id', '=', $id_method)
                    ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
                    ->where('tb_baku_mutu.sampletype_id', '=', $sampletype)
                    ->whereNull('tb_baku_mutu.deleted_at')
                    ->whereNull('ms_method.deleted_at');
                })
                ->join('ms_unit as unit_baku_mutu', function ($join) {
                  $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                    ->whereNull('unit_baku_mutu.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })

            ->orderBy('tb_sample_result.created_at', 'asc')
            ->first();
        } else {

          $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
            ->where('ms_laboratorium.kode_laboratorium', 'KIM')
            ->where('tb_samples.typesample_samples', $sampletype)
            ->where('tb_sample_result.method_id', $method->id_method)
            ->join('ms_laboratorium', function ($join) {
              $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
                ->whereNull('ms_laboratorium.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_samples', function ($join) {
              $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_samples.deleted_at')
                ->whereNull('tb_sample_result.deleted_at');
            })
            ->join('tb_sample_method', function ($join) use ($id_method) {
              $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
                ->where('tb_sample_method.method_id', $id_method)
                ->whereNull('tb_sample_method.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->leftjoin('tb_sample_analitik_progress', function ($join) {
              $join->on('tb_sample_analitik_progress.sample_id', '=', 'tb_sample_result.sample_id')
                ->whereNull('tb_sample_analitik_progress.deleted_at')
                ->whereNull('tb_sample_result.deleted_at')
                ->where('tb_sample_analitik_progress.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
                ->where('tb_sample_analitik_progress.laboratorium_progress_id', 'bc2850f5-4ec4-450f-a727-2b1428c861d9');
            })
            ->join('ms_method', function ($join) use ($id_method, $sampletype) {
              $join->where('ms_method.id_method', '=', $id_method)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at')
                ->join('tb_baku_mutu', function ($join) use ($sampletype, $id_method) {
                  $join
                    ->where('tb_baku_mutu.method_id', '=', $id_method)
                    ->where('tb_baku_mutu.sampletype_id', '=', $sampletype)
                    ->whereNull('tb_baku_mutu.deleted_at');
                })
                ->join('ms_unit as unit_baku_mutu', function ($join) {
                  $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                    ->whereNull('unit_baku_mutu.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                });
            })

            ->orderBy('tb_sample_result.created_at', 'asc')
            ->first();
        }
        // $id_method = $method->id_method;

        // $result_one = SampleResult::where('tb_sample_result.sample_id', '=', $sample->id_samples)
        //   ->where('ms_laboratorium.kode_laboratorium', 'KIM')
        //   ->where('tb_sample_result.method_id', $method->id_method)
        //   ->join('ms_laboratorium', function ($join) {
        //     $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_result.laboratorium_id')
        //       ->whereNull('ms_laboratorium.deleted_at')
        //       ->whereNull('tb_sample_result.deleted_at');
        //   })
        //   ->join('ms_method', function ($join) {
        //     $join->on('ms_method.id_method', '=', 'tb_sample_result.method_id')
        //       ->whereNull('tb_sample_result.deleted_at')
        //       ->whereNull('ms_method.deleted_at');
        //   })
        //   ->join('tb_samples', function ($join) {
        //     $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')
        //       ->whereNull('tb_samples.deleted_at')
        //       ->whereNull('tb_sample_result.deleted_at');
        //   })
        //   ->join('tb_sample_method', function ($join) use ($id_method) {
        //     $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
        //       ->where('tb_sample_method.method_id', $id_method)
        //       ->whereNull('tb_sample_method.deleted_at')
        //       ->whereNull('tb_samples.deleted_at');
        //   })
        //   ->join('tb_baku_mutu', function ($join) use ($sampletype, $id_method) {
        //     $join->where('tb_baku_mutu.method_id', '=', $id_method)
        //       ->where('tb_baku_mutu.sampletype_id', '=', $sampletype)
        //       ->whereNull('tb_baku_mutu.deleted_at')
        //       ->whereNull('ms_method.deleted_at');
        //   })
        //   ->join('ms_unit as unit_baku_mutu', function ($join) {
        //     $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
        //       ->whereNull('unit_baku_mutu.deleted_at')
        //       ->whereNull('tb_baku_mutu.deleted_at');
        //   })

        //   ->first();

        $sample_result = [];
        $sample_result["hasil"] = isset($result_one->hasil) ? $result_one->hasil : "-";
        $sample_result["min"] = isset($result_one->min) ? $result_one->min : null;
        $sample_result["max"] = isset($result_one->max) ? $result_one->max : null;

        $sample_result["equal"] = isset($result_one->equal) ? $result_one->equal : null;
        array_push($sample_one["result"],  $sample_result);
        // array_push($sample_one["min"], isset($result_one->min) ? $result_one->min : "-");
        // array_push($sample_one["max"], isset($result_one->max) ? $result_one->max : "-");
      }
      array_push($table, $sample_one);
    }





    if (count($makmin) > 0) {
      $sample_type_id = 'd34b4a50-4560-4fce-96c3-046c7080a986';
    } else {
      $sample_type_id = '0e9a1091-b248-44e9-921b-5b4773109603';
    }
    $diambil_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->orderBy('datesampling_samples', 'ASC')
      ->where('kode_laboratorium', 'KIM')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      // ->whereNotNull('penanganan_sample_date')
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })

      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
      // ->take(1)
      ->firstOrFail();

    $diambil_min = $diambil_min->datesampling_samples;

    // dd($diambil_min);

    $diambil_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->orderBy('datesampling_samples', 'DESC')
      ->where('kode_laboratorium', 'KIM')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      // ->whereNotNull('penanganan_sample_date')
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })

      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
      // ->take(1)
      ->firstOrFail();

    $diambil_max = $diambil_max->datesampling_samples;

    $checking_min = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->orderBy('date_checking', 'ASC')
      ->where('kode_laboratorium', 'KIM')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      ->whereNotNull('penanganan_sample_date')
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })

      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
      // ->take(1)
      ->firstOrFail();

    $checking_min = $checking_min->date_checking;

    $done_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->orderBy('date_done_estimation_labs', 'DESC')
      ->where('kode_laboratorium', 'KIM')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      ->whereNotNull('penanganan_sample_date')
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })

      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
          ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
          ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_pengesahan_hasil.created_at', 'desc')
      // ->take(1)
      ->first();


    $date_verif_max = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)

      ->where('kode_laboratorium', 'KIM')
      ->whereNotNull('verifikasi_hasil_date')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_verifikasi_hasil', function ($join) {
        $join->on('tb_verifikasi_hasil.id_verifikasi_hasil', '=', DB::raw('(SELECT id_verifikasi_hasil FROM tb_verifikasi_hasil WHERE tb_verifikasi_hasil.sample_id = tb_samples.id_samples AND tb_verifikasi_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_verifikasi_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })

      // ->leftjoin('tb_verifikasi_hasil', function ($join) {
      //   $join->on('tb_verifikasi_hasil.sample_id', '=', 'tb_samples.id_samples')
      //     ->on('tb_samples.id_samples', '=', 'tb_verifikasi_hasil.sample_id')
      //     ->where('tb_verifikasi_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      //     ->whereNull('tb_pengesahan_hasil.deleted_at')
      //     ->whereNull('tb_samples.deleted_at');
      // })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_verifikasi_hasil.verifikasi_hasil_date', 'desc')
      // ->take(1)
      ->first();

    // dd($date_verif_max);
    if (isset($date_verif_max->verifikasi_hasil_date)) {
      $date_verif_max = $date_verif_max->verifikasi_hasil_date;
    } else {
    }



    // dd($checking_min);

    $done_max = $done_max->date_done_estimation_labs;


    $sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->where('kode_laboratorium', 'KIM')
      ->whereNotNull('penanganan_sample_date')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))


          // ->limit(1)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')

          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      // ->leftjoin('tb_pengesahan_hasil', function ($join) {
      //   $join->on('tb_pengesahan_hasil.sample_id', '=', 'tb_samples.id_samples')
      //     ->on('tb_samples.id_samples', '=', 'tb_pengesahan_hasil.sample_id')
      //     ->where('tb_pengesahan_hasil.laboratorium_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      //     ->whereNull('tb_pengesahan_hasil.deleted_at')
      //     ->whereNull('tb_samples.deleted_at');
      // })

      // ->select('tb_pengesahan_hasil.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'codesample_samples', 'id_samples')
      ->orderBy('tb_pengesahan_hasil.pengesahan_hasil_date', 'desc')
      // ->take(1)
      ->firstOrFail();


    $pengesahan_hasil = PengesahanHasil::where('sample_id', '=', $sample->id_samples)->where('laboratorium_id', '=', $sample->laboratorium_id)->first();

    $no_LHU = LHU::where('sample_id', '=', $sample->id_samples)->where('lab_id', '=', $sample->laboratorium_id)->first();

    $agenda = $request->input('agenda');
    // dd($agenda);

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();



      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));
      $no_LHU->nomer_LHU = '445.9/A.' . $no_LHU->nomer_urut_LHU . '/4.2.26/' . $romawi_bulan . '/' . Carbon::now()->format('Y');
      $no_LHU->sample_id = $sample->id_samples;
      $no_LHU->lab_id = $sample->id_laboratorium;
      $no_LHU->save();




      $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
      $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');

      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '445.9/' . $agenda . '/4.2.26/' . $mount . '/' . $year
        : '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;

    } else {
      if (isset($pengesahan_hasil)) {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('Y');
      } else {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');
      }
      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '445.9/' . $agenda . '/4.2.26/' . $mount . '/' . $year
        : '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
    }

    $item_sample = Sample::where('permohonan_uji_id', '=', $id_permohonan_uji)
      ->where('ms_laboratorium.kode_laboratorium', 'KIM')
      ->where('tb_samples.typesample_samples',  $sample_type_id)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_sample_method.laboratorium_id')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('tb_samples.*', 'ms_sample_type.*')
      ->distinct('tb_samples.id_samples')
      ->first();

    $lab_num_max = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
      ->orderBy('lab_number', 'asc')
      ->take(1);

    $lab_num_min = LabNum::where('permohonan_uji_id', $id_permohonan_uji)
      ->orderBy('lab_number', 'desc')
      ->take(1);

    $laboratorium = Laboratorium::where('id_laboratorium', $sample->id_laboratorium)->first();


    //dd($year);


    if (count($makmin) > 0) {



      $pdf = PDF::loadView("masterweb::module.admin.laboratorium.sample.formatPrint.kimia.makmin", compact(
        'permohonan_uji',
        'all_samples_max',
        'all_samples_min',
        'table',
        'sample',
        'laboratorium',
        'no_LHU',
        'method_all',
        'diambil_min',
        'diambil_max',
        'checking_min',
        'done_max',
        'pengesahan_hasil',
        'all_acuan_baku_mutu',
        'date_verif_max',
        'lab_num',
        'lab_string',
      ));

      return $pdf->stream();
    } else {
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.pudam', compact(
        'permohonan_uji',
        'all_samples_max',
        'all_samples_min',
        'table',
        'sample',
        'laboratorium',
        'no_LHU',
        'method_all',
        'diambil_min',
        'diambil_max',
        'checking_min',
        'done_max',
        'pengesahan_hasil',
        'all_acuan_baku_mutu',
        'date_verif_max',
        'lab_num',
        'lab_string',
      ));

      return $pdf->stream();
    }

    // testing for print of view
    // return view('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.pudam', compact('permohonan_uji', 'all_samples_max', 'all_samples_min', 'table', 'sample', 'laboratorium', 'no_LHU', 'method_all', 'diambil_min', 'diambil_max', 'checking_min', 'done_max', 'pengesahan_hasil', 'all_acuan_baku_mutu', 'date_verif_max'));
  }

  public function printLHU(Request $request, $id, $idlab, $ischlor = null)
  {
    //ini_set('max_execution_time', 3600);
    $sample = Sample::where('id_samples', '=', $id)
      ->join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('ms_customer.deleted_at')
          ->whereNull('tb_permohonan_uji.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) use ($idlab) {
        $join
          ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
          ->where('tb_sample_penanganan.laboratorium_id', '=', $idlab)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();
    // dd($sample);mount

    $tembusans = $sample->tembusan;

    $pengesahan_hasil = PengesahanHasil::where('sample_id', '=', $id)->where('laboratorium_id', '=', $idlab)->first();

    $no_LHU = LHU::where('sample_id', '=', $id)->where('lab_id', '=', $idlab)->first();

    $agenda = $request->input('agenda');
    // dd($agenda);

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));
      $no_LHU->nomer_LHU = '400.7/A.' . $no_LHU->nomer_urut_LHU . '/4.2.26/' . Carbon::now()->format('Y');
      $no_LHU->sample_id = $id;
      $no_LHU->lab_id = $idlab;
      $no_LHU->save();


      $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
      $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');

      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '400.7/' . $agenda . '/4.2.26/' . $year
        : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . $year;
    } else {
      if (isset($pengesahan_hasil)) {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('Y');
      } else {
        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');
      }

      // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      $no_LHU = !empty($agenda)
        ? '400.7/' . $agenda . '/4.2.26/' . $year
        : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . $year;
    }

    // dd($sample);

    $verifikasi = VerifikasiHasil::where('sample_id', '=', $id)->where('laboratorium_id', '=', $idlab)->first();
    $laboratorium = Laboratorium::findOrFail($idlab);

    $sampletype_id = $sample->id_sample_type;

    $sampletype_id = $sample->id_sample_type;
    $jenis_makanan_id = $sample->jenis_makanan_id;
    if (isset($jenis_makanan_id)) {



      $laboratoriummethods_sampletypes = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
        ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
        // ->orderBy('ms_method.created_at')
        ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail')
        ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlab) {
          $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
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
            // ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
            //   $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
            //     ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
            //     ->whereNull('tb_baku_mutu.deleted_at');
            // })

            ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            })->leftjoin('tb_sample_result', function ($join) use ($idlab) {
              $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')

                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at');
            });
        })
        ->leftjoin('ms_sample_type_detail', function ($join) use ($id) {
          $join->on('ms_sample_type_detail.method_id', '=', 'tb_laboratorium_method.method_id')
            ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_sample_type_detail.deleted_at');
        })


        ->select(
          'tb_baku_mutu.*',
          'ms_method.*',
          'unit_baku_mutu.*',
          // 'tb_sample_result.hasil',
          // 'ms_sample_type_detail.is_tambahan',
          'tb_laboratorium_method.*',
        )

        // ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
        //   $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
        //     ->whereNull('tb_baku_mutu.deleted_at')
        //     ->whereNull('ms_method.deleted_at');
        // })
        // ->join('ms_unit as unit_baku_mutu', function ($join) {
        //   $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
        //     ->whereNull('unit_baku_mutu.deleted_at')
        //     ->whereNull('tb_baku_mutu.deleted_at');
        // })
        ->distinct('ms_method.id_method')
        // ->select('tb_baku_mutu.*', 'ms_method.*', 'tb_laboratorium_method.*', 'unit_baku_mutu.*')
        ->get();
    } else {
      $laboratoriummethods_sampletypes = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
        ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
        ->where('ms_sample_type_detail.sample_type_id', '=', $sampletype_id)

        // ->orderBy('ms_method.created_at')
        ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail')
        // ->orderBy('ms_method.jenis_parameter_kimia')
        ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
          $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
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
            // ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
            //   $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
            //     ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
            //     ->whereNull('tb_baku_mutu.deleted_at');
            // })
            ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
              $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                ->where('tb_sample_result.sample_id', '=', $id)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })

            ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            });
        })
        ->leftjoin('ms_sample_type_detail', function ($join) {
          $join->on('ms_sample_type_detail.method_id', '=', 'tb_laboratorium_method.method_id')

            ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_sample_type_detail.deleted_at');
        })


        ->select(
          'tb_baku_mutu.*',
          'ms_method.*',
          'unit_baku_mutu.*',
          // 'tb_sample_result.hasil',
          // 'ms_sample_type_detail.is_tambahan',
          'tb_laboratorium_method.*',
        )

        // ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
        //   $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
        //     ->whereNull('tb_baku_mutu.deleted_at')
        //     ->whereNull('ms_method.deleted_at');
        // })
        // ->join('ms_unit as unit_baku_mutu', function ($join) {
        //   $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
        //     ->whereNull('unit_baku_mutu.deleted_at')
        //     ->whereNull('tb_baku_mutu.deleted_at');
        // })
        ->distinct('ms_method.id_method')
        // ->distinct('tb_laboratorium_method.laboratorium_id')
        // ->select('tb_baku_mutu.*', 'ms_method.*', 'tb_laboratorium_method.*', 'unit_baku_mutu.*')
        ->get();
      // dd($laboratoriummethods_sampletypes);

    }
    $laboratoriummethods = $laboratoriummethods_sampletypes;

    if (isset($jenis_makanan_id)) {

      // dd($jenis_makanan_id);

      $laboratoriummethodsResult = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
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
          'tb_sample_result.*',
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    } else {
      $laboratoriummethodsResult = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
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
          'tb_sample_result.*',
          'tb_sample_result.offset_baku_mutu'
        )
        ->distinct('ms_method.id_method')
        ->get();
    }

    // dd($laboratoriummethods);

    // $laboratoriummethods = SampleTypeDetail::where('ms_sample_type_detail.sample_type_id', '=', $sampletype_id)
    //   ->where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
    //   ->join('ms_method', function ($join) {
    //     $join->on('ms_method.id_method', '=', 'ms_sample_type_detail.method_id')
    //       ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
    //       ->whereNull('ms_sample_type_detail.deleted_at')
    //       ->whereNull('ms_method.deleted_at');
    //   })
    //   ->join('tb_laboratorium_method', function ($join) {
    //     $join->on('tb_laboratorium_method.method_id', '=', 'ms_sample_type_detail.method_id')
    //       ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
    //       ->whereNull('ms_sample_type_detail.deleted_at')
    //       ->whereNull('tb_laboratorium_method.deleted_at');
    //   })
    //   ->leftjoin('tb_sample_result', function ($join) use ($id) {
    //     $join->on('tb_sample_result.method_id', '=', 'ms_sample_type_detail.method_id')
    //       ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
    //       ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
    //       ->where('tb_sample_result.sample_id', '=', $id)
    //       ->whereNull('ms_sample_type_detail.deleted_at')
    //       ->whereNull('tb_sample_result.deleted_at');
    //   })
    //   ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
    //     $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
    //       ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
    //       ->whereNull('tb_baku_mutu.deleted_at')
    //       ->whereNull('ms_method.deleted_at');
    //   })
    //   ->join('ms_unit as unit_baku_mutu', function ($join) {
    //     $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
    //       ->whereNull('unit_baku_mutu.deleted_at')
    //       ->whereNull('tb_baku_mutu.deleted_at');
    //   })
    //   ->get();
    // $sampletype_id = $sample->id_sample_type;
    // $jenis_makanan_id = $sample->jenis_makanan_id;
    // if (isset($jenis_makanan_id)) {

    //   $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
    //     ->where('tb_sample_method.sample_id', '=', $id)
    //     ->orderBy('ms_method.jenis_parameter_kimia')
    //     ->join('ms_method', function ($join)   use ($sampletype_id, $jenis_makanan_id, $idlab, $id) {
    //       $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
    //         ->whereNull('tb_sample_method.deleted_at')
    //         ->whereNull('ms_method.deleted_at')
    //         ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $jenis_makanan_id) {
    //           $join
    //             // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
    //             //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
    //             //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
    //             //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
    //             //     LIMIT 1)'))
    //             ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
    //             ->where('tb_baku_mutu.jenis_makanan_id', '=', $jenis_makanan_id)
    //             ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
    //             ->whereNull('tb_baku_mutu.deleted_at')
    //             ->whereNull('ms_method.deleted_at');
    //         })
    //         // ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
    //         //   $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
    //         //     ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
    //         //     ->whereNull('tb_baku_mutu.deleted_at');
    //         // })

    //         ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
    //           $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
    //             ->whereNull('unit_baku_mutu.deleted_at')
    //             ->whereNull('tb_baku_mutu.deleted_at');
    //         })->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
    //           $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
    //             ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
    //             ->where('tb_sample_result.sample_id', '=', $id)
    //             ->whereNull('tb_sample_result.deleted_at')
    //             ->whereNull('ms_method.deleted_at');
    //         });
    //     })



    //     ->select(
    //       'tb_baku_mutu.*',
    //       'ms_method.*',
    //       'tb_sample_method.*',
    //       'unit_baku_mutu.*',
    //       'tb_sample_result.hasil',
    //       'tb_sample_result.offset_baku_mutu'
    //     )
    //     ->get();
    // } else {
    //   $laboratoriummethods = SampleMethod::where('tb_sample_method.laboratorium_id', '=', $idlab)
    //     ->where('tb_sample_method.sample_id', '=', $id)
    //     ->orderBy('ms_method.jenis_parameter_kimia')
    //     ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
    //       $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
    //         ->whereNull('tb_sample_method.deleted_at')
    //         ->whereNull('ms_method.deleted_at')
    //         ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id, $idlab) {
    //           $join
    //             // ->on('tb_baku_mutu.method_id', '=', DB::raw('(SELECT id_method FROM
    //             //     ms_method WHERE tb_baku_mutu.method_id = ms_method.id_method AND
    //             //     tb_baku_mutu.deleted_at is NULL AND ms_method.deleted_at is NULL
    //             //     AND tb_baku_mutu.sampletype_id =  "'.$sampletype_id.'"
    //             //     LIMIT 1)'))
    //             ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
    //             ->where('tb_baku_mutu.lab_id', '=', $idlab)
    //             ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
    //             ->whereNull('tb_baku_mutu.deleted_at')
    //             ->whereNull('ms_method.deleted_at');
    //         })
    //         // ->leftjoin('tb_baku_mutu_detail_parameter_non_klinik', function ($join) {
    //         //   $join->on('tb_baku_mutu_detail_parameter_non_klinik.baku_mutu_id', '=', 'tb_baku_mutu.id_baku_mutu')
    //         //     ->whereNull('tb_baku_mutu_detail_parameter_non_klinik.deleted_at')
    //         //     ->whereNull('tb_baku_mutu.deleted_at');
    //         // })

    //         ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
    //           $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
    //             ->whereNull('unit_baku_mutu.deleted_at')
    //             ->whereNull('tb_baku_mutu.deleted_at');
    //         })
    //         ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
    //           $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
    //             ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
    //             ->where('tb_sample_result.sample_id', '=', $id)
    //             ->whereNull('tb_sample_result.deleted_at')
    //             ->whereNull('ms_method.deleted_at');
    //         });
    //     })




    //     ->select(
    //       'tb_baku_mutu.*',
    //       'ms_method.*',
    //       'tb_sample_method.*',
    //       'unit_baku_mutu.*',
    //       'tb_sample_result.hasil',
    //       'tb_sample_result.offset_baku_mutu'
    //     )
    //     ->distinct('ms_method.id_method')
    //     ->get();
    // }

    $sample_type_details = SampleTypeDetail::where('sample_type_id', $sampletype_id)->orderBy('orderlist_sample_type_detail')->get();



    $method_all_temp = [];


    foreach ($sample_type_details as $sample_type_detail) {
      # code...
      foreach ($laboratoriummethods as $method) {
        # code...


        // print("& ".$method->id_method." ".$sample_type_detail->method_id);
        if ($method->id_method == $sample_type_detail->method_id) {
          // $method_all_temp[] = $method;

          array_push($method_all_temp,$method);
        }
      }
    }

    if ($method_all_temp != []) {
      # code...
      // $laboratoriummethods = $method_all_temp;

      $laboratoriummethods = collect($method_all_temp)->unique();

    }





    foreach ($laboratoriummethods as $key => $laboratoriummethod) {
      # code...sample_type_details
      $laboratoriummethods[$key]->detail = array();


      $laboratoriummethods[$key]->is_tambahan = SampleTypeDetail::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('method_id', '=', $laboratoriummethod->id_method)
        ->where('ms_sample_type_detail.sample_type_id', $sampletype_id)
        ->firstOrFail()->is_tambahan;



      $laboratoriummethods[$key]->detail = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('sampletype_id', '=', $sampletype_id)
        ->where('sample_id', '=',  $id)->get();

      $sample_result = SampleResult::where('method_id', '=', $laboratoriummethod->id_method)
        ->where('laboratorium_id', '=', $idlab)
        ->where('sample_id', '=',  $id)->first();


      $laboratoriummethods[$key]->hasil = isset($sample_result->hasil) ? $sample_result->hasil : "-";
      $laboratoriummethods[$key]->keterangan = isset($sample_result->keterangan) ? $sample_result->keterangan : "-";
      $laboratoriummethods[$key]->metode = isset($sample_result->metode) ? $sample_result->metode : null;

      // dd($sample_result);

      if (count($laboratoriummethods[$key]->detail) == 0) {

        $bakuMutuDetailParameterNonKliniks = BakuMutuDetailParameterNonKlinik::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('sampletype_id', '=', $sampletype_id)
          ->where('baku_mutu_id', '=',  $laboratoriummethod->id_baku_mutu)->get();

        if (count($bakuMutuDetailParameterNonKliniks) > 0) {
          // dd($bakuMutuDetailParameterNonKliniks);
          $all_array = [];
          foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {
            # code...
            $array = [];
            $array = [
              "id_sample_result_detail" =>    $bakuMutuDetailParameterNonKlinik->id_baku_mutu_detail_parameter_non_klinik,
              "method_id" => $laboratoriummethod->id_method,
              "sample_id" => $laboratoriummethod->sample_id,
              "is_tambahan" => $laboratoriummethod->is_tambahan,
              "sampletype_id" => $sampletype_id,
              "lab_id" =>  $laboratoriummethod->lab_id,
              "name_sample_result_detail" =>    $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik,
              "min_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik,
              "max_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik,
              "equal_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik,
              "nilai_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik,
              "hasil" => "-",
              "offset_baku_mutu" => "false",
              "created_at" => $bakuMutuDetailParameterNonKlinik->created_at,
              "updated_at" => $bakuMutuDetailParameterNonKlinik->updated_at,
              "deleted_at" => $bakuMutuDetailParameterNonKlinik->deleted_at
            ];
            array_push($all_array, $array);
          }
          $laboratoriummethods[$key]->detail = $all_array;
        }
      }
      //else {
      //   if ($laboratoriummethods_sampletypes[$key]->name_report == "pH") {
      //     dd("");
      //     # code...
      //   }
      // }
    }






    $all_acuan_baku_mutu = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
      ->where('tb_sample_result.sample_id', '=', $id)
      ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
      // ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) use ($sampletype_id) {
        $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('ms_method.deleted_at')
          ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
            $join->where('tb_baku_mutu.sampletype_id', $sampletype_id)
              ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')

              ->whereNull('tb_baku_mutu.deleted_at')
              ->whereNull('ms_method.deleted_at');
          })

          ->join('ms_library', function ($join) {
            $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
              ->whereNull('ms_library.deleted_at')
              ->whereNull('tb_baku_mutu.deleted_at');
          });
      })
      ->leftjoin('tb_sample_result', function ($join) use ($id) {
        $join->on('tb_sample_result.method_id', '=', 'tb_laboratorium_method.method_id')
          ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('tb_sample_result.deleted_at');
      })
      ->join('tb_samples', function ($join) {
        $join->on('tb_samples.id_samples', '=', 'tb_sample_result.sample_id')

          ->whereNull('tb_samples.deleted_at')
          ->whereNull('tb_sample_result.deleted_at');
      })
      ->join('ms_laboratorium', function ($join) {
        $join->on('ms_laboratorium.id_laboratorium', '=', 'tb_laboratorium_method.laboratorium_id')
          // ->where('ms_laboratorium.kode_laboratorium', 'KIM')
          ->whereNull('ms_laboratorium.deleted_at')
          ->whereNull('tb_laboratorium_method.deleted_at');
      })
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })

      ->distinct('tb_laboratorium_method.laboratorium_id')
      ->select('ms_library.*')
      ->get();


    /* $data_method = Method::select(DB::raw('DISTINCT jenis_parameter_kimia, berhubungan_kesehatan, id_method'))
      ->orderBy('params_method')
      ->whereNull('deleted_at')
      ->get(); */

    $data_method = Method::select('jenis_parameter_kimia', 'berhubungan_kesehatan', 'id_method')
      ->distinct('jenis_parameter_kimia')
      // ->orderBy('params_method')
      ->whereNull('deleted_at')
      ->get();

    $laboratoriummethods_plus_count = SampleTypeDetail::where('ms_sample_type_detail.sample_type_id', '=', $sampletype_id)
      ->where('ms_sample_type_detail.is_tambahan', '=', 1)
      ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
      ->where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'ms_sample_type_detail.method_id')
          ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
          ->whereNull('ms_sample_type_detail.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('tb_laboratorium_method', function ($join) {
        $join->on('tb_laboratorium_method.method_id', '=', 'ms_sample_type_detail.method_id')
          ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
          ->whereNull('ms_sample_type_detail.deleted_at')
          ->whereNull('tb_laboratorium_method.deleted_at');
      })
      ->leftjoin('tb_sample_result', function ($join) use ($id) {
        $join->on('tb_sample_result.method_id', '=', 'ms_sample_type_detail.method_id')
          ->on('tb_sample_result.laboratorium_id', '=', 'tb_laboratorium_method.laboratorium_id')
          ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
          ->where('tb_sample_result.sample_id', '=', $id)

          ->whereNull('ms_sample_type_detail.deleted_at')
          ->whereNull('tb_sample_result.deleted_at');
      })
      ->where(function ($query) {
        $query->whereNotNull('tb_sample_result.hasil')
          ->where('tb_sample_result.hasil', '!=', '-');
      })
      ->join('tb_baku_mutu', function ($join) use ($sampletype_id) {
        $join->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
          ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->join('ms_unit as unit_baku_mutu', function ($join) {
        $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
          ->whereNull('unit_baku_mutu.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->count();

    // dd($laboratoriummethods_plus_count);



    $lab_num = LabNum::where('sample_id', $sample->id_samples)
      ->where('permohonan_uji_id', $sample->permohonan_uji_id)
      ->where('sample_type_id', $sample->typesample_samples)
      ->first();

    $folder = '';

    //    $mount = (int)Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('m');
    //    if (($mount > 4 && $year == 2023) || $year > 2023) {
    //      $folder = '52023';
    //    } else {
    //      $folder = '2021_42023';
    //    }
    $validation = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->where('id_verification_activity', '=', 5)->first();
    $analytic = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->where('id_verification_activity', '=', 2)->first();

    //    // Makanan / Minuman
    //    if ($sample->id_sample_type == "d34b4a50-4560-4fce-96c3-046c7080a986"){
    //      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.makmin', compact(
    //        'pengesahan_hasil',
    //        'no_LHU',
    //        'sample',
    //        'verifikasi',
    //        'lab_num',
    //        'laboratoriummethods',
    //        'tembusans',
    //        'validation',
    //        'analytic'
    //      ));
    //
    //      return $pdf->stream();
    //    }

    $laboratoriummethodsUnits = $laboratoriummethods;

//    $laboratoriummethods = collect($laboratoriummethods)
//      ->filter(function ($item) {
//        return $item->hasil !== null;
//      })
//      ->sortBy('name_report');
    // $laboratoriummethods = $laboratoriummethodsResult;


    $kimiaOrganikCount = collect($laboratoriummethods)->filter(function ($item) {
      return $item->jenis_parameter_kimia === 'kimia organik';
    })->count();

    $kimiaCount = collect($laboratoriummethods)->filter(function ($item) {
      return $item->jenis_parameter_kimia === 'kimiawi';
    })->count();

    $fisikaCount = collect($laboratoriummethods)->filter(function ($item) {
      return $item->jenis_parameter_kimia === 'fisika';
    })->count();

    $url = env("APP_URL");
    if (env("APP_ENV") == "local"){
      $url .= ":8000";
    }
    $url .= "/elits-signature/progress/".$id."/0";

    $result = Builder::create()
      ->data($url)
      ->encoding(new Encoding('UTF-8'))
      ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
      ->size(600)
      ->margin(2)
      ->build();

    $qrBase64 = base64_encode($result->getString());


    $signOption = $request->signOption ?? 0;

    if (isset($request->signoption) and $request->signoption == "1"){
      $signOption = 1;
    }

    if ($signOption == 0){
      if ($laboratorium->kode_laboratorium == "KIM" && $sample->name_sample_type == "Air Minum") {
        $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.air_minum', compact(
          'laboratoriummethods_sampletypes',
          'pengesahan_hasil',
          'laboratoriummethods_plus_count',
          'no_LHU',
          'sample',
          'verifikasi',
          'lab_num',
          'laboratorium',
          'laboratoriummethods',
          'all_acuan_baku_mutu',
          'data_method',
          'tembusans',
          'validation',
          'kimiaCount',
          'fisikaCount',
          'kimiaOrganikCount',
          'laboratoriummethodsUnits',
          'signOption'
        ));

      } elseif ($laboratorium->kode_laboratorium == "KIM" && $sample->name_sample_type == "Air Bersih") {
        $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.air_bersih', compact(
          'laboratoriummethods_sampletypes',
          'pengesahan_hasil',
          'laboratoriummethods_plus_count',
          'no_LHU',
          'sample',
          'lab_num',
          'verifikasi',
          'laboratorium',
          'laboratoriummethods',
          'all_acuan_baku_mutu',
          'tembusans',
          'validation',
          'kimiaCount',
          'fisikaCount',
          'kimiaOrganikCount',
          'laboratoriummethodsUnits',
          'signOption'
        ));

      } elseif ($laboratorium->kode_laboratorium == "KIM" && $ischlor != null) {
        $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.printLHU', compact(
          'laboratoriummethods_sampletypes',
          'pengesahan_hasil',
          'no_LHU',
          'sample',
          'laboratoriummethods_plus_count',
          'verifikasi',
          'lab_num',
          'laboratorium',
          'laboratoriummethods',
          'all_acuan_baku_mutu',
          'tembusans',
          'validation',
          'kimiaCount',
          'fisikaCount',
          'kimiaOrganikCount',
          'laboratoriummethodsUnits',
          'signOption'
        ));

      } else {
        $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.printLHU', compact(
          'laboratoriummethods_sampletypes',
          'pengesahan_hasil',
          'no_LHU',
          'sample',
          'lab_num',
          'laboratoriummethods_plus_count',
          'verifikasi',
          'laboratorium',
          'laboratoriummethods',
          'all_acuan_baku_mutu',
          'tembusans',
          'validation',
          'kimiaCount',
          'fisikaCount',
          'kimiaOrganikCount',
          'laboratoriummethodsUnits',
          'signOption'
        ));

      }

      return $pdf->stream();
    }

    $dataKepalaLab = Petugas::query()->where('nik', '=', '3309094611720002')->where('nama', '=', 'dr. Muharyati')->get(['nik', 'password'])->first();

    $verificationActivity = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->get();

    if ($laboratorium->kode_laboratorium == "KIM" && $sample->name_sample_type == "Air Minum") {
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.air_minum', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'laboratoriummethods_plus_count',
        'no_LHU',
        'sample',
        'verifikasi',
        'lab_num',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu',
        'data_method',
        'tembusans',
        'validation',
        'kimiaCount',
        'fisikaCount',
        'kimiaOrganikCount',
        'laboratoriummethodsUnits',
        'qrBase64'
      ));

    } elseif ($laboratorium->kode_laboratorium == "KIM" && $sample->name_sample_type == "Air Bersih") {
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.air_bersih', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'laboratoriummethods_plus_count',
        'no_LHU',
        'sample',
        'lab_num',
        'verifikasi',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu',
        'tembusans',
        'validation',
        'kimiaCount',
        'fisikaCount',
        'kimiaOrganikCount',
        'laboratoriummethodsUnits',
        'qrBase64'
      ));

    } elseif ($laboratorium->kode_laboratorium == "KIM" && $ischlor != null) {
      /* dd('KIM && null');

      return view('masterweb::module.admin.laboratorium.sample.printLHU', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'no_LHU',
        'sample',
        'laboratoriummethods_plus_count',
        'verifikasi',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu'
      )); */

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.printLHU', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'no_LHU',
        'sample',
        'laboratoriummethods_plus_count',
        'verifikasi',
        'lab_num',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu',
        'tembusans',
        'validation',
        'kimiaCount',
        'fisikaCount',
        'kimiaOrganikCount',
        'laboratoriummethodsUnits',
        'qrBase64'
      ));

    } else {
      /* dd('else');

      return view('masterweb::module.admin.laboratorium.sample.printLHU', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'no_LHU',
        'sample',
        'laboratoriummethods_plus_count',
        'verifikasi',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu'
      )); */

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.printLHU', compact(
        'laboratoriummethods_sampletypes',
        'pengesahan_hasil',
        'no_LHU',
        'sample',
        'lab_num',
        'laboratoriummethods_plus_count',
        'verifikasi',
        'laboratorium',
        'laboratoriummethods',
        'all_acuan_baku_mutu',
        'tembusans',
        'validation',
        'kimiaCount',
        'fisikaCount',
        'kimiaOrganikCount',
        'laboratoriummethodsUnits',
        'qrBase64'
      ));

    }

    if (!isset($agenda)){
      return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap input agenda!');
    }

    if (count($verificationActivity) < 5){
      return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
    }

    if (isset($dataKepalaLab->nik) and isset($dataKepalaLab->password) and isset($verifikasi)){

      $dataPetugasBSRE[] = [
        'nik' => $dataKepalaLab->nik,
        'passPhrase' => $dataKepalaLab->password,
        'tampilan' => 'invisible',
        'reason' => "hasil12121",
        'location' => "boyolali",
        'text' => "hasil22323"
      ];

      $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

      if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
        $data =  base64_encode($signBSRE["data"]["file"]);

        return view('masterweb::module.admin.laboratorium.sample.blob',
          compact('data')
        );
      }elseif ($signBSRE['status'] == 500){
        return redirect()->back()->with('error-laporan', 'errors');
      }else{
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
      }

    }else{
      return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
    }

  }

  public function printAllMakanMinum(Request $request, $idPermohonanUji)
  {

    $samples = Sample::query()->where('permohonan_uji_id', '=', $idPermohonanUji)->where('name_sample_type', '=', 'Makanan/Minuman/Lainnya')
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('ms_customer.deleted_at')
          ->whereNull('tb_permohonan_uji.deleted_at');
      })
      ->get();

    $labnums = [];
    $idlab = '3416ca19-6c69-4e5f-a004-ae8275de7644';
    $laboratoriummethodsArray = [];
    $no_lhus = [];
    $param_methods = [];
    $validasi = [];
    $tembusans = [];
    foreach ($samples as $sample) {
      $sampletype_id = $sample->id_sample_type;
      $id = $sample->id_samples;
      $lab_num = LabNum::where('sample_id', $sample->id_samples)
        ->where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->where('sample_type_id', $sample->typesample_samples)
        ->first();

      $laboratoriummethods = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', $idlab)
        ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
        ->where('ms_sample_type_detail.sample_type_id', '=', $sampletype_id)

        // ->orderBy('ms_method.created_at')
        ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail')
        // ->orderBy('ms_method.jenis_parameter_kimia')
        ->join('ms_method', function ($join)   use ($sampletype_id, $idlab, $id) {
          $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_method.deleted_at')
            ->leftjoin('tb_baku_mutu', function ($join) use ($sampletype_id) {
              $join
                ->on('tb_baku_mutu.method_id', '=', 'ms_method.id_method')
                ->where('tb_baku_mutu.sampletype_id', '=', $sampletype_id)
                ->whereNull('tb_baku_mutu.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })
            ->leftjoin('tb_sample_result', function ($join) use ($id, $idlab) {
              $join->where('tb_sample_result.laboratorium_id', '=', $idlab)
                ->on('tb_sample_result.method_id', '=', 'ms_method.id_method')
                ->where('tb_sample_result.sample_id', '=', $id)
                ->whereNull('tb_sample_result.deleted_at')
                ->whereNull('ms_method.deleted_at');
            })

            ->leftjoin('ms_unit as unit_baku_mutu', function ($join) {
              $join->on('unit_baku_mutu.id_unit', '=', 'tb_baku_mutu.unit_id')
                ->whereNull('unit_baku_mutu.deleted_at')
                ->whereNull('tb_baku_mutu.deleted_at');
            });
        })
        ->leftjoin('ms_sample_type_detail', function ($join) {
          $join->on('ms_sample_type_detail.method_id', '=', 'tb_laboratorium_method.method_id')

            ->orderBy('ms_sample_type_detail.orderlist_sample_type_detail', 'asc')
            ->whereNull('tb_laboratorium_method.deleted_at')
            ->whereNull('ms_sample_type_detail.deleted_at');
        })


        ->select(
          'tb_baku_mutu.*',
          'ms_method.*',
          'unit_baku_mutu.*',
          'tb_laboratorium_method.*',
        )
        ->distinct('ms_method.id_method')
        ->get();

      foreach ($laboratoriummethods as $key => $laboratoriummethod) {
        array_push($param_methods, $laboratoriummethod->params_method);
        # code...
        $laboratoriummethods[$key]->detail = array();


        $laboratoriummethods[$key]->is_tambahan = SampleTypeDetail::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('method_id', '=', $laboratoriummethod->id_method)
          ->where('ms_sample_type_detail.sample_type_id', $sampletype_id)
          ->firstOrFail()->is_tambahan;



        $laboratoriummethods[$key]->detail = SampleResultDetail::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('sampletype_id', '=', $sampletype_id)
          ->where('sample_id', '=',  $id)->get();

        $sample_result = SampleResult::where('method_id', '=', $laboratoriummethod->id_method)
          ->where('laboratorium_id', '=', $idlab)
          ->where('sample_id', '=',  $id)->first();


        $laboratoriummethods[$key]->hasil = isset($sample_result->hasil) ? $sample_result->hasil : "-";
        $laboratoriummethods[$key]->keterangan = isset($sample_result->keterangan) ? $sample_result->keterangan : "-";
        $laboratoriummethods[$key]->metode = isset($sample_result->metode) ? $sample_result->metode : null;
        // dd($sample_result);

        if (count($laboratoriummethods[$key]->detail) == 0) {

          $bakuMutuDetailParameterNonKliniks = BakuMutuDetailParameterNonKlinik::where('method_id', '=', $laboratoriummethod->id_method)
            ->where('sampletype_id', '=', $sampletype_id)
            ->where('baku_mutu_id', '=',  $laboratoriummethod->id_baku_mutu)->get();

          if (count($bakuMutuDetailParameterNonKliniks) > 0) {
            // dd($bakuMutuDetailParameterNonKliniks);
            $all_array = [];
            foreach ($bakuMutuDetailParameterNonKliniks as $bakuMutuDetailParameterNonKlinik) {
              # code...
              $array = [];
              $array = [
                "id_sample_result_detail" =>    $bakuMutuDetailParameterNonKlinik->id_baku_mutu_detail_parameter_non_klinik,
                "method_id" => $laboratoriummethod->id_method,
                "sample_id" => $laboratoriummethod->sample_id,
                "is_tambahan" => $laboratoriummethod->is_tambahan,
                "sampletype_id" => $sampletype_id,
                "lab_id" =>  $laboratoriummethod->lab_id,
                "name_sample_result_detail" =>    $bakuMutuDetailParameterNonKlinik->name_baku_mutu_detail_parameter_non_klinik,
                "min_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->min_baku_mutu_detail_parameter_non_klinik,
                "max_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->max_baku_mutu_detail_parameter_non_klinik,
                "equal_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->equal_baku_mutu_detail_parameter_non_klinik,
                "nilai_sample_result_detail" => $bakuMutuDetailParameterNonKlinik->nilai_baku_mutu_detail_parameter_non_klinik,
                "hasil" => "-",
                "offset_baku_mutu" => "false",
                "created_at" => $bakuMutuDetailParameterNonKlinik->created_at,
                "updated_at" => $bakuMutuDetailParameterNonKlinik->updated_at,
                "deleted_at" => $bakuMutuDetailParameterNonKlinik->deleted_at
              ];
              array_push($all_array, $array);
            }
            $laboratoriummethods[$key]->detail = $all_array;
          }
        }
      }


      $no_LHU = LHU::where('sample_id', '=', $id)->where('lab_id', '=', $idlab)->first();

      $agenda = $request->input('agenda');
      // dd($agenda);

      if (!isset($no_LHU)) {
        $no_LHU = new LHU;
        //uuid
        $uuid4 = Uuid::uuid4();

        $no_LHU_urutan = LHU::max('nomer_urut_LHU');
        $no_LHU->id_lhu = $uuid4->toString();
        $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
        $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));
        $no_LHU->nomer_LHU = '445.9/A.' . $no_LHU->nomer_urut_LHU . '/4.2.26/' . $romawi_bulan . '/' . Carbon::now()->format('Y');
        $no_LHU->sample_id = $id;
        $no_LHU->lab_id = $idlab;
        $no_LHU->save();


        $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
        $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');

        // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
        $no_LHU = !empty($agenda)
          ? '445.9/' . $agenda . '/4.2.26/' . $mount . '/' . $year
          : '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      } else {
        if (isset($pengesahan_hasil)) {
          $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('m'));
          $year = Carbon::createFromFormat('Y-m-d H:i:s', $pengesahan_hasil->pengesahan_hasil_date)->format('Y');
        } else {
          $mount = $this->convertToRoman(Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('m'));
          $year = Carbon::createFromFormat('Y-m-d H:i:s', $no_LHU->created_at)->format('Y');
        }

        // $no_LHU = '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
        $no_LHU = !empty($agenda)
          ? '445.9/' . $agenda . '/4.2.26/' . $mount . '/' . $year
          : '<span style="padding-right:40px">445.9/</span>/4.2.26/' . $mount . '/' . $year;
      }

      $analytic = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->where('id_verification_activity', '=', 2)->first();
      $valid = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->where('id_verification_activity', '=', 5)->first();

      $arr = [];

      $labSamples = [
        "jenis_sarana" => $sample->jenis_sarana_names,
        "nama_jenis_makanan" => $sample->nama_jenis_makanan,
        "lab_num" => $lab_num->lab_number,
        "date_sending" => $sample->date_sending,
        "date_analytic" => isset($analytic) ? $analytic->stop_date : null
      ];

      array_push($arr, $laboratoriummethods);
      array_push($arr, $labSamples);

      array_push($labnums, $lab_num);
      array_push($laboratoriummethodsArray, $arr);
      array_push($no_lhus, $no_LHU);
      array_push($tembusans, $sample->tembusan);
      if (isset($valid)) {
        array_push($validasi, $valid);
      }
    }

    $no_LHU = $no_lhus[0];
    $param_methods = array_unique($param_methods);

    if (count($validasi) == count($samples)) {
      $validasi = $validasi[count($samples) - 1];
    } else {
      $validasi = null;
    }

    $tembusans = $tembusans[count($samples) - 1];

    if (isset($request->signOption) and $request->signOption == 1){
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.makmin', compact(
        'no_LHU',
        'samples',
        'labnums',
        'laboratoriummethodsArray',
        'param_methods',
        'tembusans',
        'validasi'
      ));


      $datas = [
        [
          'nik' => 3309094611720002,
          'passPhrase' => "@Muharyati123",
          'tampilan' => 'invisible',
          'reason' => "hasil12121",
          'location' => "boyolali",
          'text' => "hasil22323"
        ]
      ];

      $signBSRE = Smt::signBSRE($pdf, $datas);

      if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
        $data =  base64_encode($signBSRE["data"]["file"]);

        return view('masterweb::module.admin.laboratorium.sample.blob',
          compact('data')
        );
      }elseif ($signBSRE['status'] == 500){
        return redirect()->back()->with('error-laporan', 'errors');
      }
    }

    $signOption = 0;

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.makmin', compact(
      'no_LHU',
      'samples',
      'labnums',
      'laboratoriummethodsArray',
      'param_methods',
      'tembusans',
      'validasi',
      'signOption'
    ));

    return $pdf->stream();
  }

  public function print_verifikasi($id, $idlab = null, Request $request)
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
      ->leftjoin('tb_sample_penerimaan', function ($join) {
        $join->on('tb_sample_penerimaan.id_sample_penerimaan', '=', DB::raw('(SELECT id_sample_penerimaan FROM tb_sample_penerimaan WHERE tb_sample_penerimaan.sample_id = tb_samples.id_samples AND tb_sample_penerimaan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

          ->whereNull('tb_sample_penerimaan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) {
        $join->on('tb_sample_penanganan.id_sample_penanganan', '=', DB::raw('(SELECT id_sample_penanganan FROM tb_sample_penanganan WHERE tb_sample_penanganan.sample_id = tb_samples.id_samples AND tb_sample_penanganan.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_pelaporan_hasil', function ($join) {
        $join->on('tb_pelaporan_hasil.id_pelaporan_hasil', '=', DB::raw('(SELECT id_pelaporan_hasil FROM tb_pelaporan_hasil WHERE tb_pelaporan_hasil.sample_id = tb_samples.id_samples AND tb_pelaporan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
          // $join->on('tb_pelaporan_hasil.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
          // ->on('tb_samples.id_samples', '=', 'tb_pelaporan_hasil.sample_id')
          ->whereNull('tb_pelaporan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_pengetikan_hasil', function ($join) {
        $join->on('tb_pengetikan_hasil.id_pengetikan_hasil', '=', DB::raw('(SELECT id_pengetikan_hasil FROM tb_pengetikan_hasil WHERE tb_pengetikan_hasil.sample_id = tb_samples.id_samples AND tb_pengetikan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

          // ->on('tb_samples.id_samples', '=', 'tb_pengetikan_hasil.sample_id')
          ->whereNull('tb_pengetikan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_verifikasi_hasil', function ($join) {
        $join->on('tb_verifikasi_hasil.id_verifikasi_hasil', '=', DB::raw('(SELECT id_verifikasi_hasil FROM tb_verifikasi_hasil WHERE tb_verifikasi_hasil.sample_id = tb_samples.id_samples AND tb_verifikasi_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

          ->whereNull('tb_verifikasi_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('tb_pengesahan_hasil', function ($join) {
        $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))

          ->whereNull('tb_pengesahan_hasil.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    //    // dd($sample);
    //
    //
    //    $laboratoriummethods = SampleMethod::where('laboratorium_id', '=', $idlab)
    //      ->where('sample_id', '=', $id)
    //      ->orderBy('ms_method.created_at')
    //      ->join('ms_method', function ($join) {
    //        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
    //          ->whereNull('tb_sample_method.deleted_at')
    //          ->whereNull('ms_method.deleted_at');
    //      })
    //      ->get();
    //
    //    $laboratoriumprogress = LaboratoriumProgress::where('laboratorium_id', $idlab)->orderBy('order_sort', 'asc')->get();
    //    $laboratoriumsampleanalitikprogress = SampleAnalitikProgress::where('tb_sample_analitik_progress.laboratorium_id', $idlab)
    //      ->where('tb_sample_analitik_progress.sample_id', $id)
    //      ->orderBy('tb_laboratorium_progress.order_sort', 'asc')
    //      ->join('tb_laboratorium_progress', function ($join) {
    //        $join->on('tb_laboratorium_progress.id_laboratorium_progress', '=', 'tb_sample_analitik_progress.laboratorium_progress_id')
    //          ->on('tb_laboratorium_progress.laboratorium_id', '=', 'tb_sample_analitik_progress.laboratorium_id')
    //          ->whereNull('tb_laboratorium_progress.deleted_at')
    //          ->whereNull('tb_sample_analitik_progress.deleted_at');
    //      })
    //      ->get();
    //
    //    // dd($laboratoriumsampleanalitikprogress);
    //
    //    // dd($laboratoriumsampleanalitikprogress);
    //    $penerimaan_sample = PenerimaanSample::where('sample_id', $id)
    //      ->join('ms_container', function ($join) {
    //        $join->on('ms_container.id_container', '=', 'tb_sample_penerimaan.wadah_id')
    //          ->whereNull('ms_container.deleted_at')
    //          ->whereNull('tb_sample_penerimaan.deleted_at');
    //      })
    //      ->join('ms_unit', function ($join) {
    //        $join->on('ms_unit.id_unit', '=', 'tb_sample_penerimaan.unit_id')
    //          ->whereNull('ms_unit.deleted_at')
    //          ->whereNull('tb_sample_penerimaan.deleted_at');
    //      })
    //      ->first();
    //
    //    $penanganan_sample = PenangananSample::where('sample_id', $id)->where('laboratorium_id', $idlab)->first();
    //
    //
    //    // dd($penanganan_sample);
    //
    //
    //    // dd($sample);

    $verificationActivitySamples = VerificationActivitySample::query()->where('id_sample', '=', $sample->id_samples)->get();
    $listVerifications = [];

    $url = env("APP_URL");
    if (env("APP_ENV") == "local"){
      $url .= ":8000";
    }
    $url .= "/elits-signature/progress/".$id."/0";

    $result = Builder::create()
      ->data($url)
      ->encoding(new Encoding('UTF-8'))
      ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
      ->size(600)
      ->margin(2)
      ->build();

    $qrBase64 = base64_encode($result->getString());

    if (isset($request->tanggal_cetak_verifikasi)){
      $datePrintVerification = \DateTime::createFromFormat('d/m/Y' , $request->tanggal_cetak_verifikasi)->format('Y-m-d');
    }else{
      $datePrintVerification = Carbon::now()->toDateString();
    }

    if (isset($request->signOption) and $request->signOption == 0){
      foreach ($verificationActivitySamples as  $verificationActivitySample){
        $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
      }

      $signOption = 0;

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.print_verifikasi', compact('listVerifications', 'sample', 'datePrintVerification', 'signOption'));

      return $pdf->stream();
    }

    $dataPetugasBSRE = [];
    foreach ($verificationActivitySamples as  $verificationActivitySample) {
      $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;

      $petugas = Petugas::query()->where('nama', '=', $verificationActivitySample->nama_petugas)->get()->first();
      if (isset($petugas)){
        $dataPetugasBSRE[] = [
          'nik' => $petugas->nik,
          'passPhrase' => $petugas->password,
          'tampilan' => 'invisible',
          'reason' => "hasil12121",
          'location' => "boyolali",
          'text' => "hasil22323"
        ];
      }
    }


    if (count($listVerifications) == 5 and count($dataPetugasBSRE) == 5){

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.print_verifikasi', compact('listVerifications', 'sample', 'datePrintVerification', 'qrBase64'));

      $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

      if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
        $data =  base64_encode($signBSRE["data"]["file"]);

        return view('masterweb::module.admin.laboratorium.sample.blob',
          compact('data')
        );
      }elseif ($signBSRE['status'] == 500){
        return redirect()->back()->with('error-verifikasi', 'errors');
      }else{
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
      }
    }else{
      if (count($listVerifications) < 5){
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
      }

      if (count($dataPetugasBSRE) < 5){
        return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
      }
    }
  }

  public function printInformConcern($id, $idlab, $ischlor = null)
  {
    $sample = Sample::where('id_samples', '=', $id)
      ->join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('ms_customer.deleted_at')
          ->whereNull('tb_permohonan_uji.deleted_at');
      })
      ->leftjoin('tb_sample_penanganan', function ($join) use ($idlab) {
        $join
          ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
          ->where('tb_sample_penanganan.laboratorium_id', '=', $idlab)
          ->whereNull('tb_sample_penanganan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->first();

    $laboratorium = Laboratorium::findOrFail($idlab);

    $sampletype_id = $sample->id_sample_type;
    $jenis_makanan_id = $sample->jenis_makanan_id;
    $kimia = [];
    $fisika = [];
    $kimiaMakanan = [];

    $penerimaan_sample = PenerimaanSample::where('sample_id', '=', $id)->firstOrFail();

    if (isset($jenis_makanan_id)) {
      $laboratoriummethods_sampletypes = SampleMethod::where('laboratorium_id', '=', $idlab)
      ->where('sample_id', '=', $id)
      ->orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->get();

      foreach ($laboratoriummethods_sampletypes as $laboratoriumMethod) {
        $kimiaMakanan[$laboratoriumMethod->params_method] = "checked";
      }
    } else {



        $laboratoriummethods_sampletypes = SampleMethod::where('laboratorium_id', '=', $idlab)
        ->where('sample_id', '=', $id)
        ->orderBy('ms_method.created_at')
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('tb_sample_method.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })
        ->get();



      foreach ($laboratoriummethods_sampletypes as $laboratoriumMethod) {
        if ($laboratoriumMethod->jenis_parameter_kimia == "kimiawi" || $laboratoriumMethod->jenis_parameter_kimia == "kimia organik") {
          $kimia[$laboratoriumMethod->params_method] = "checked";
        } else {
          $fisika[$laboratoriumMethod->params_method] = "checked";
        }
      }
    }

    // dd($laboratoriummethods_sampletypes);


    $list_kimia_wajib=[
      "Besi (Fe)" => "checked",
      "Fluorida"  => "checked",
      "Kesadahan (CaCO3)" => "checked",
      "Chlorida"  => "checked",
      "Mangan (Mn)"    => "checked",
      "Nitrat"    => "checked",
      "Nitrit"    => "checked",
      "Sianida"   => "checked",
      "pH"        => "checked",
      "Zat Organik"=> "checked",
      "Boraks"    => "checked",
      "Formalin"  => "checked",
      "Benzoat"   => "checked",
      "Salisilat" => "checked",
      "Pewarna"   => "checked",
      "Siklamat"  => "checked",
      "Sakarin"   => "checked",
      "Besi"      => "checked",
      "Chlorida"  => "checked",
      "pH"        => "checked",
      "Boraks"    => "checked",
      "Formalin"   => "checked"];


    $not_in_list_kimia = array_diff_key($kimia, $list_kimia_wajib);
    // dd($not_in_list_kimia);



    if ($laboratorium->kode_laboratorium == "MBI") {
      $airBersih = '';
      $airMinum  = '';
      $airLimbah = '';
      $bakteri   = '';
      $kuman     = '';
      if (strpos(strtolower($sample->name_sample_type), 'air bersih') !== false) {
        $airBersih = 'checked';
      } elseif (strpos(strtolower($sample->name_sample_type), 'air minum') !== false) {
        $airMinum = 'checked';
      } elseif (strpos(strtolower($sample->name_sample_type), 'air limbah') !== false) {
        $airLimbah = 'checked';
      } elseif (strpos(strtolower($sample->name_sample_type), 'bakteri') !== false) {
        $bakteri = 'checked';
      }elseif (strpos(strtolower($sample->name_sample_type), 'makanan/minuman/lainnya') !== false) {
        $bakteri = 'checked';
      } else {
        $kuman = 'checked';
      }
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.inform_concern', compact('sample', 'airMinum', 'airBersih', 'airLimbah', 'bakteri', 'kuman', 'penerimaan_sample','not_in_list_kimia'));
      return $pdf->stream();
    }

    //dd($fisika, $kimia, $kimiaMakanan);

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.kimia.inform_concern', compact('sample', 'kimia', 'fisika', 'kimiaMakanan', 'penerimaan_sample','not_in_list_kimia'));
    return $pdf->stream();
  }

  public function updateNamaPengambil(Request $request, $id)
  {
    //dd($id);
    $request->validate([
        'name_send_sample' => 'required',
    ]);

    $sample = Sample::findOrFail($id);
    $sample->name_send_sample = $request->name_send_sample;

    $permohonan_uji = PermohonanUji::findOrFail($sample->permohonanuji->id_permohonan_uji);
    $permohonan_uji->name_sampling = $request->name_send_sample;
    $permohonan_uji->save();

    return redirect()->back()->with('status', 'Nama pengambil berhasil diperbarui.');
  }

  public function getSamplesByPermohonanUjiAndSampleType($idPermohonanUji, $idSampleType)
  {
    $samples = Sample::query();
    if($idPermohonanUji != "print-mikro-air-bersih-air-minum"){
      $samples = $samples->where('permohonan_uji_id', '=', $idPermohonanUji)->where('typesample_samples', '=', $idSampleType);
    }else{
      $samples = $samples->where('permohonan_uji_id', '=', $idSampleType);
    }

    $samples = $samples
      ->join('tb_sample_method', 'tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
      ->join('ms_sample_type', 'tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
      ->where('tb_sample_method.laboratorium_id', '=', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5') // Lab Mikro
      ->select('id_samples', 'location_samples', 'name_pelanggan', 'count_id', 'name_sample_type')
      ->distinct('id_samples')
      ->orderBy('count_id', 'asc')
      ->get();

    return $samples;
  }

  public function getSamplesMikroBySampleId($idSample, $labId)
  {
    $sample = Sample::query()->where('id_samples', $idSample)->select('permohonan_uji_id', 'typesample_samples')->first();
    $sampleType = $sample->typesample_samples;
    $sampleAB = null;
    $sampleAM = null;
    if ($sampleType == "c7c770a9-6bd7-4e30-83fc-0e4cc6a01fe0"){
      $sampleAB = $sampleType;
      $sampleAM = "65df8403-b29f-4645-a1ed-12d2aeff1fbd";
    }
    if ($sampleType == "65df8403-b29f-4645-a1ed-12d2aeff1fbd"){
      $sampleAM = $sampleType;
      $sampleAB = "c7c770a9-6bd7-4e30-83fc-0e4cc6a01fe0";
    }

    if (isset($sampleAM) and $sampleAB){
      $samples = Sample::where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->join('tb_sample_method', 'tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
        ->join('ms_sample_type', 'tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
        ->where('tb_sample_method.laboratorium_id', '=', $labId)
        ->where(function ($query) use ($sampleAB, $sampleAM) {
          $query->where('tb_samples.typesample_samples', $sampleAB)
            ->orWhere('tb_samples.typesample_samples', $sampleAM);
        })
        ->select('id_samples', 'location_samples', 'name_pelanggan', 'count_id', 'name_sample_type')
        ->distinct('id_samples')
        ->orderBy('count_id', 'asc')
        ->get();
    }elseif (isset($sampleAM)){
      $samples = Sample::where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->join('tb_sample_method', 'tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
        ->join('ms_sample_type', 'tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
        ->where('tb_sample_method.laboratorium_id', '=', $labId)
        ->where('tb_samples.typesample_samples', '=', $sampleType)
        ->select('id_samples', 'location_samples', 'name_pelanggan', 'count_id', 'name_sample_type')
        ->distinct('id_samples')
        ->orderBy('count_id', 'asc')
        ->get();
    }elseif (isset($sampleAB)){
      $samples = Sample::where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->join('tb_sample_method', 'tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
        ->join('ms_sample_type', 'tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
        ->where('tb_sample_method.laboratorium_id', '=', $labId)
        ->where('tb_samples.typesample_samples', '=', $sampleType)
        ->select('id_samples', 'location_samples', 'name_pelanggan', 'count_id', 'name_sample_type')
        ->distinct('id_samples')
        ->orderBy('count_id', 'asc')
        ->get();
    }else{
      $samples = Sample::where('permohonan_uji_id', $sample->permohonan_uji_id)
        ->join('tb_sample_method', 'tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
        ->join('ms_sample_type', 'tb_samples.typesample_samples', '=', 'ms_sample_type.id_sample_type')
        ->where('tb_sample_method.laboratorium_id', '=', $labId)
        ->where('tb_samples.typesample_samples', '=', $sampleType)
        ->select('id_samples', 'location_samples', 'name_pelanggan', 'count_id', 'name_sample_type')
        ->distinct('id_samples')
        ->orderBy('count_id', 'asc')
        ->get();
    }

      return $samples;
  }

  public function printMikroGabungan(Request $request, $idPermohonanUji)
  {

    $agenda = $request->input('agenda');
    $no_agenda = !empty($agenda) ? '400.7/' . $agenda . '/4.2.26/' . 2025 : '<span style="padding-right:40px">400.7/</span>/4.2.26/' . 2025;


    $permohonan_uji = PermohonanUji::query()->where('id_permohonan_uji', $idPermohonanUji)->first();
    $customer= $permohonan_uji->customer;

    $samples = Sample::query()->where('permohonan_uji_id', $idPermohonanUji)
                ->whereHas('samplemethod', function ($query) {
                    $query->where('laboratorium_id', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5");
                })
                ->with(['sampleresult.method', 'labnum', 'sampleresult.method.bakumutu', 'sampleresult.method.bakumutu.unit'])
                ->orderBy('count_id', 'asc')
                ->get();


    $samplePrint = $request->printSamples;

    if(isset($samplePrint)){
      if(count($samples) > count($samplePrint)){
        $samples = Sample::query()->where('permohonan_uji_id', $idPermohonanUji)
                ->whereIn('id_samples', $samplePrint)
                ->whereHas('samplemethod', function ($query) {
                    $query->where('laboratorium_id', "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5");
                })
                ->with(['sampleresult.method', 'labnum', 'sampleresult.method.bakumutu', 'sampleresult.method.bakumutu.unit'])
                ->orderBy('count_id', 'asc')
                ->get();
      }
    }

    $listVerifications =
    $jenis_sarana = [];
    $tanggal_sampling = [];
    $labNums = [];
    $metode_pemeriksaan = [];
    $params = [];
    $methods = [];
    $results = [];
    $bakumutu = [];

    foreach($samples as $sample){
      $jenis_sarana[] = $sample->jenis_sarana_names;
      $tanggal_sampling[] = Carbon::parse($sample->datesampling_samples)->toDateString();
      $labNums[] = $sample->labnum[0]->lab_number;

      $result_item = [
        'labnum' => $sample->count_id,
        'lokasi' => $sample->location_samples ?? $sample->name_pelanggan,
        'jam_sampling' => date("H:i", strtotime($sample->datesampling_samples)),
        'hasil' => [],
      ];

      foreach($sample->sampleresult as $sampleresult){
        $metode_pemeriksaan[] = $sampleresult->metode;
        $result_item['hasil'][] = [
          $sampleresult->method_id => [
            'hasil' => $sampleresult->hasil,
            'metode' => $sampleresult->metode,
            'keterangan' => $sampleresult->keterangan,
            'offset_baku_mutu' => $sampleresult->offset_baku_mutu,
            'bakumutu' => null
          ]
        ];

        foreach($sampleresult->method as $method)
        {
          $bakumutu[$method->id_method] = $method->bakumutu;
          if ($method->params_method == "E-Coli (Membran Filter)") {
            $params[$method->id_method] = "Escherichia coli";
          } elseif ($method->params_method == "Total Coliform (Membrane Filter)") {
              $params[$method->id_method] = "Total Coliform";
          } else {
              $params[$method->id_method] = $method->params_method;
          }
        }
      }

      $results[] = $result_item;
    }

    $jenis_sarana = array_unique(array_filter(array_map('trim', $jenis_sarana)));

    if(count($labNums) == 1){
      $no_register = "0".$labNums[0]."/AB-AM- Bact/2025";
    }else{
      $no_register = "0".min($labNums)." - "."0".max($labNums)."/AB-AM- Bact/2025";
    }

    $verificationActivitySamples = VerificationActivitySample::query()->where('id_sample', '=', $samples[0]->id_samples)->get();
    $listVerifications = [];
    foreach ($verificationActivitySamples as  $verificationActivitySample) {
      $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
    }

    $tembusan = Sample::where('id_samples', $samples[0]->id_samples)->get('tembusan')->first()->tembusan;

    $signOption = $request->signOption;


    arsort($params);
    if(isset($signOption) and $signOption == 0){
      $data = [
        "no_agenda" => $no_agenda,
        "no_register" => $no_register,
        "nama_pelanggan" => $customer->name_customer,
        "alamat_pelanggan" => $customer->address_customer,
        "jenis_sampel" => "Air Bersih dan Air Minum",
        "jenis_sarana" => $jenis_sarana,
        "metode_pemeriksaan" => array_unique($metode_pemeriksaan),
        "petugas_sampling" => $permohonan_uji->name_sampling,
        "tanggal_sampling" => $tanggal_sampling,
        "parameter" => array_unique($params),
        "results" => $samples
      ];

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih_air_minum', compact('data', 'listVerifications', 'tembusan', 'signOption'));
      return $pdf->stream();
    }else{


      $url = env("APP_URL");
      if (env("APP_ENV") == "local"){
        $url .= ":8000";
      }
      $url .= "/elits-signature/progress/".$sample->sample_id."/0";

      $result = Builder::create()
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(600)
        ->margin(2)
        ->build();

      $qrBase64 = base64_encode($result->getString());


      $data = [
        "no_agenda" => $no_agenda,
        "no_register" => $no_register,
        "nama_pelanggan" => $customer->name_customer,
        "alamat_pelanggan" => $customer->address_customer,
        "jenis_sampel" => "Air Bersih dan Air Minum",
        "jenis_sarana" => $jenis_sarana,
        "metode_pemeriksaan" => array_unique($metode_pemeriksaan),
        "petugas_sampling" => $permohonan_uji->name_sampling,
        "tanggal_sampling" => $tanggal_sampling,
        "parameter" => array_unique($params),
        "results" => $samples,
        "qr_code" => ""
      ];

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.sample.formatPrint.mikro.air_bersih_air_minum', compact('data', 'listVerifications', 'tembusan', 'signOption', 'qrBase64'));

      $dataKepalaLab = Petugas::query()->where('nik', '=', '3309094611720002')->where('nama', '=', 'dr. Muharyati')->get(['nik', 'password'])->first();

      if (!isset($agenda)){
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap input agenda!');
      }

      if (count($listVerifications) < 5){
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
      }

      if (isset($dataKepalaLab->nik) and isset($dataKepalaLab->password)){

        $dataPetugasBSRE[] = [
          'nik' => $dataKepalaLab->nik,
          'passPhrase' => $dataKepalaLab->password,
          'tampilan' => 'invisible',
          'reason' => "hasil12121",
          'location' => "boyolali",
          'text' => "hasil22323"
        ];

        $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

        if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
          $data =  base64_encode($signBSRE["data"]["file"]);

          return view('masterweb::module.admin.laboratorium.sample.blob',
            compact('data')
          );
        }elseif ($signBSRE['status'] == 500){
          return redirect()->back()->with('error-laporan', 'errors');
        }else{
          return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
        }

      }else{
        return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
      }
    }
  }
}
