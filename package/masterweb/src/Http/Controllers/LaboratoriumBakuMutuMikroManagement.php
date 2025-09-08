<?php

namespace Smt\Masterweb\Http\Controllers;


use PDF;
use Mapper;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\Unit;
use \Smt\Masterweb\Models\User;
use Yajra\DataTables\DataTables;
use \Smt\Masterweb\Models\Method;
use \Smt\Masterweb\Models\Packet;

use \Smt\Masterweb\Models\Sample;
use \Smt\Masterweb\Models\Library;
use Illuminate\Support\Facades\DB;
use \Smt\Masterweb\Models\BakuMutu;
use \Smt\Masterweb\Models\Customer;
use \Smt\Masterweb\Models\Industry;
use \Smt\Masterweb\Models\Container;
use App\Http\Controllers\Controller;
use \Smt\Masterweb\Models\SampleType;

use \Smt\Masterweb\Models\JenisMakanan;

use \Smt\Masterweb\Models\Laboratorium;


use \Smt\Masterweb\Models\SampleMethod;

use \Smt\Masterweb\Models\PermohonanUji;
use Illuminate\Support\Facades\Validator;

use \Smt\Masterweb\Models\PenerimaanSample;


use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Smt\Masterweb\Models\BakuMutuDetailParameterKlinik;
use \Smt\Masterweb\Models\LaboratoriumMethod;
use \Smt\Masterweb\Models\LaboratoriumProgress;
use \Smt\Masterweb\Models\BakuMutuDetailParameterNonKlinik;


class LaboratoriumBakuMutuMikroManagement extends Controller
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
  public function index(Request $request)
  {
    Carbon::setLocale('id');
    $data = BakuMutu::where('tb_baku_mutu.lab_id', '=', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->orderBy('tb_baku_mutu.created_at')
      ->leftjoin('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_baku_mutu.method_id')
          ->whereNull('ms_method.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->leftjoin('ms_library', function ($join) {
        $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
          ->whereNull('ms_library.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->leftjoin('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_baku_mutu.sampletype_id')
          ->whereNull('tb_baku_mutu.deleted_at')
          ->whereNull('ms_sample_type.deleted_at');
      })
      ->leftjoin('ms_jenis_makanan', function ($join) {
        $join->on('ms_jenis_makanan.id_jenis_makanan', '=', 'tb_baku_mutu.jenis_makanan_id')
          ->whereNull('ms_jenis_makanan.deleted_at')
          ->whereNull('tb_baku_mutu.deleted_at');
      })
      ->get();
    $lab_link = "mikro";
    $lab = "Mikro";

    $sample_types = SampleType::query()
      ->whereNull('deleted_at')
      ->orderBy('name_sample_type')
      ->get(['name_sample_type']);
    $sample_methods = Method::query()
      ->whereNull('deleted_at')
      ->orderBy('params_method')
      ->get(['params_method']);

    if (request()->ajax()) {
      $datas = BakuMutu::where('tb_baku_mutu.lab_id', '=', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
        ->orderBy('tb_baku_mutu.created_at')
        ->leftjoin('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_baku_mutu.method_id')
            ->whereNull('ms_method.deleted_at')
            ->whereNull('tb_baku_mutu.deleted_at');
        })
        ->leftjoin('ms_library', function ($join) {
          $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
            ->whereNull('ms_library.deleted_at')
            ->whereNull('tb_baku_mutu.deleted_at');
        })
        ->leftjoin('ms_sample_type', function ($join) {
          $join->on('ms_sample_type.id_sample_type', '=', 'tb_baku_mutu.sampletype_id')
            ->whereNull('tb_baku_mutu.deleted_at')
            ->whereNull('ms_sample_type.deleted_at');
        })
        ->leftjoin('ms_jenis_makanan', function ($join) {
          $join->on('ms_jenis_makanan.id_jenis_makanan', '=', 'tb_baku_mutu.jenis_makanan_id')
            ->whereNull('ms_jenis_makanan.deleted_at')
            ->whereNull('tb_baku_mutu.deleted_at');
        })
        ->get();

      return DataTables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['jenis_sample']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['parameter']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['acuan_bakumutu']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          if (getAction('update') || getAction('delete')) {
            if (getAction('update')) {
              $editButton = '<a href="' . route('elits-baku-mutu-mikro.edit', [$data->id_baku_mutu]) . '" class="dropdown-item"
              data-toggle="tooltip" data-custom-class="tooltip-info" data-placement="top"
              title="Edit Data">Edit</a>';
            } else {
              $editButton = '';
            }

            $deleteButton = '';
            if (getAction('delete')) {
              $deleteButton = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id_baku_mutu . '" data-nama="' . $data->name_sample_type . ' - ' . $data->params_method . '" title="Hapus">Hapus</a> ';
            }

            $buttonAksi = '<div class="dropdown show m-1">
                              <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLinkAksi" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Aksi
                              </a>

                              <div class="dropdown-menu dropdown-scroll-menu" aria-labelledby="dropdownMenuLink">
                                  ' . $editButton . '
                                  ' . $deleteButton . '
                              </div>
                          </div>';
          } else {
            $buttonAksi = '';
          }

          $buttonControl = $buttonAksi;

          return $buttonControl;
        })
        ->addColumn('jenis_sample', function ($data) {
          return $data->name_sample_type;
        })
        ->addColumn('parameter', function ($data) {
          return $data->params_method ?? '-';
        })

        ->addColumn('acuan_bakumutu', function ($data) {
          return $data->title_library;
        })
        ->addColumn('nilai_bakumutu', function ($data) {
          return $data->nilai_baku_mutu;
        })
        ->rawColumns([
          'action',
          'jenis_sample',
          'parameter',
          'acuan_bakumutu',
          'nilai_bakumutu'
        ])
        ->addIndexColumn() //increment
        ->make(true);
    }

    return view('masterweb::module.admin.laboratorium.baku-mutu.list_mikro', compact('lab', 'lab_link', 'data', 'sample_types', 'sample_methods'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //get auth user





    $user = Auth()->user();
    $methods = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->get();
    $id_lab = "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5";
    $sample_types = SampleType::orderBy('created_at')->get();

    $all_jenis_makanan = JenisMakanan::all();


    $libraries = Library::all();
    $units = Unit::all();
    $lab_link = "mikro";
    $lab = "Mikro";

    return view('masterweb::module.admin.laboratorium.baku-mutu.add', compact('all_jenis_makanan', 'units', 'libraries', 'sample_types', 'id_lab', 'methods', 'lab_link', 'lab'));
  }

  public function rules($request)
  {
    $rule = [
      'sampletype_id' => 'required',
      'method_id' => 'required'
    ];

    $pesan = [
      'sampletype_id.required' => 'Jenis sampel tidak boleh kosong!',
      'method_id.required' => 'Parameter tidak boleh kosong!'
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $data = $request->all();

        // validasi biar tidak ada data yang double
        $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
          ->where('sampletype_id', $data['sampletype_id'])
          ->where('method_id', $data['method_id'])
          ->first();

        if ($check != null) {
          return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
        }

        if ($data["is_sub"] == "false") {
          $user = Auth()->user();
          $baku_mutu = new BakuMutu;
          //uuid
          $uuid4 = Uuid::uuid4();

          // $baku_mutu->id_baku_mutu = $uuid4->toString();
          $baku_mutu->sampletype_id = $data['sampletype_id'];
          $baku_mutu->method_id = $data['method_id'];
          $baku_mutu->unit_id = $data['unit_id'];
          $baku_mutu->min = $data['min_no_sub'];
          $baku_mutu->max = $data['max_no_sub'];
          if (isset($data["equal_no_sub"])) {
            $baku_mutu->equal = rubahNilaikeHtml(str_replace(",", ".", $data["equal_no_sub"]));
          }
          $baku_mutu->name_report = $data['name_report'];
          $baku_mutu->library_id = $data['library_id'];
          // $baku_mutu->nilai_baku_mutu = $data['nilai_baku_mutu_no_sub'];
          if (isset($data["nilai_baku_mutu_no_sub"])) {
            $baku_mutu->nilai_baku_mutu = rubahNilaikeHtml(str_replace(",", ".", $data["nilai_baku_mutu_no_sub"]));
          }
          $baku_mutu->lab_id = 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5';
          // if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
          //   $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
          // } else {
          $baku_mutu->jenis_makanan_id = NULL;
          // }

          $simpan = $baku_mutu->save();
        } else {
          $user = Auth()->user();
          $baku_mutu = new BakuMutu;
          //uuid
          $uuid4 = Uuid::uuid4();

          // $baku_mutu->id_baku_mutu = $uuid4->toString();
          $baku_mutu->name_report = $data['name_report'];
          $baku_mutu->sampletype_id = $data['sampletype_id'];
          $baku_mutu->method_id = $data['method_id'];
          $baku_mutu->unit_id = $data['unit_id'];
          $baku_mutu->is_sub = 1;
          $baku_mutu->library_id = $data['library_id'];
          $baku_mutu->lab_id = 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5';
          // if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
          //   $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
          // } else {
          $baku_mutu->jenis_makanan_id = NULL;
          // }

          $simpan = $baku_mutu->save();

          foreach ($data['name_subbakumutu'] as $key => $name_subbakumutu) {
            if (isset($name_subbakumutu)) {
              $bakuMutudetailparameternonklinik = new BakuMutuDetailParameterNonKlinik;
              //uuid
              $uuid4 = Uuid::uuid4();

              // $bakuMutudetailparameternonklinik->id_baku_mutu_detail_parameter_non_klinik = $uuid4->toString();
              $bakuMutudetailparameternonklinik->baku_mutu_id = $baku_mutu->id_baku_mutu;
              $bakuMutudetailparameternonklinik->sampletype_id = $baku_mutu->sampletype_id;
              $bakuMutudetailparameternonklinik->method_id = $baku_mutu->method_id;
              $bakuMutudetailparameternonklinik->lab_id = 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5';
              $bakuMutudetailparameternonklinik->name_baku_mutu_detail_parameter_non_klinik   = $data['name_subbakumutu'][$key];
              if (isset($data['min'][$key]) && $data['min'][$key] != "") {
                $bakuMutudetailparameternonklinik->min_baku_mutu_detail_parameter_non_klinik   = $data['min'][$key];
              }
              if (isset($data['max'][$key]) && $data['max'][$key] != "") {
                $bakuMutudetailparameternonklinik->max_baku_mutu_detail_parameter_non_klinik  = $data['max'][$key];
              }
              if (isset($data['equal'][$key]) && $data['equal'][$key] != "") {
                if (isset($data["equal"])) {
                  $bakuMutudetailparameternonklinik->equal_baku_mutu_detail_parameter_non_klinik = rubahNilaikeHtml(str_replace(",", ".", $data['equal'][$key]));
                }
                // $bakuMutudetailparameternonklinik->equal_baku_mutu_detail_parameter_non_klinik  = $data['equal'][$key];
              }
              if (isset($data['nilai_baku_mutu'][$key]) && $data['nilai_baku_mutu'][$key] != "") {
                if (isset($data["nilai_baku_mutu"])) {
                  $bakuMutudetailparameternonklinik->nilai_baku_mutu_detail_parameter_non_klinik = rubahNilaikeHtml(str_replace(",", ".", $data['nilai_baku_mutu'][$key]));
                }
                // $bakuMutudetailparameternonklinik->nilai_baku_mutu_detail_parameter_non_klinik  = $data['nilai_baku_mutu'][$key];
              }
              $bakuMutudetailparameternonklinik->save();
            }
          }
        }

        DB::commit();

        if ($simpan == true) {
          return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil disimpan!"], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil disimpan!"], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
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

    $baku_mutu = BakuMutu::where('id_baku_mutu', $id)->first();
    $all_jenis_makanan = JenisMakanan::all();


    $bakuMutudetailparameternonkliniks = BakuMutuDetailParameterNonKlinik::where('baku_mutu_id', $id)
      ->where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->where('sampletype_id', $baku_mutu->sampletype_id)
      ->where('method_id', $baku_mutu->method_id)
      ->orWhere('baku_mutu_id', $baku_mutu->id_baku_mutu)
      ->orderBy('created_at')
      ->get();

    $methods = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->get();
    $id_lab = "d3bff0b4-622e-40b0-b10f-efa97a4e1bd5";
    $sample_types = SampleType::orderBy('created_at')->get();

    $libraries = Library::all();
    $units = Unit::all();
    $lab_link = "mikro";
    $lab = "Mikro";


    return view('masterweb::module.admin.laboratorium.baku-mutu.edit', compact('all_jenis_makanan', 'id', 'baku_mutu', 'units', 'libraries', 'sample_types', 'id_lab', 'methods', 'lab_link', 'lab', 'bakuMutudetailparameternonkliniks'));
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
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $data = $request->all();

        // validasi untuk mengetahui apakah adatanya beda atau sama
        $baku_mutu = BakuMutu::find($id);

        if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
          if (
            $baku_mutu->sampletype_id != $request->sampletype_id &&
            $baku_mutu->method_id != $request->method_id &&
            $baku_mutu->jenis_makanan_id != $request->jenis_makanan_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->where('jenis_makanan_id', $data['jenis_makanan_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          } else if (
            $baku_mutu->sampletype_id == $request->sampletype_id &&
            $baku_mutu->method_id != $request->method_id &&
            $baku_mutu->jenis_makanan_id != $request->jenis_makanan_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->where('jenis_makanan_id', $data['jenis_makanan_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu parameter dan jenis makanan sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          } else if (
            $baku_mutu->sampletype_id != $request->sampletype_id &&
            $baku_mutu->method_id == $request->method_id &&
            $baku_mutu->jenis_makanan_id != $request->jenis_makanan_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->where('jenis_makanan_id', $data['jenis_makanan_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu jenis sample dan jenis makanan sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          } else if (
            $baku_mutu->sampletype_id != $request->sampletype_id &&
            $baku_mutu->method_id != $request->method_id &&
            $baku_mutu->jenis_makanan_id == $request->jenis_makanan_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->where('jenis_makanan_id', $data['jenis_makanan_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu jenis sample dan parameter sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          }
        } else {
          if (
            $baku_mutu->sampletype_id != $request->sampletype_id &&
            $baku_mutu->method_id != $request->method_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          } else if (
            $baku_mutu->sampletype_id == $request->sampletype_id &&
            $baku_mutu->method_id != $request->method_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu parameter sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          } else if (
            $baku_mutu->sampletype_id != $request->sampletype_id &&
            $baku_mutu->method_id == $request->method_id
          ) {
            $check = BakuMutu::where('lab_id', 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5')
              ->where('sampletype_id', $data['sampletype_id'])
              ->where('method_id', $data['method_id'])
              ->first();

            if ($check != null) {
              return response()->json(['status' => false, 'pesan' => "Data baku mutu jenis sample sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
            } else {
              $baku_mutu->sampletype_id = $data['sampletype_id'];
              $baku_mutu->method_id = $data['method_id'];

              if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
                $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
              } else {
                $baku_mutu->jenis_makanan_id = NULL;
              }
            }
          }
        }

        if ($data["is_sub"] == "false") {
          $user = Auth()->user();
          // $baku_mutu = BakuMutu::find($id);

          $baku_mutu->name_report = $data['name_report'];
          $baku_mutu->unit_id = $data['unit_id'];
          $baku_mutu->min = $data['min_no_sub'];
          $baku_mutu->max = $data['max_no_sub'];
          if (isset($data["equal_no_sub"])) {
            $baku_mutu->equal = rubahNilaikeHtml(str_replace(",", ".", $data["equal_no_sub"]));
          }
          // $baku_mutu->equal = $data['equal_no_sub'];
          $baku_mutu->library_id = $data['library_id'];

          if (isset($data["nilai_baku_mutu_no_sub"])) {
            $baku_mutu->nilai_baku_mutu = rubahNilaikeHtml(str_replace(",", ".", $data["nilai_baku_mutu_no_sub"]));
          }

          $simpan = $baku_mutu->save();
        } else {
          $user = Auth()->user();
          // $baku_mutu = BakuMutu::find($id);
          //uuid
          $baku_mutu->name_report = $data['name_report'];
          $baku_mutu->unit_id = $data['unit_id'];
          $baku_mutu->min = NULL;
          $baku_mutu->max = NULL;
          $baku_mutu->is_sub = 1;
          $baku_mutu->equal = NULL;
          $baku_mutu->library_id = $data['library_id'];
          $baku_mutu->nilai_baku_mutu = NULL;

          $simpan = $baku_mutu->save();


          BakuMutuDetailParameterNonKlinik::where('baku_mutu_id', $baku_mutu->id_baku_mutu)->delete();
          foreach ($data['name_subbakumutu'] as $key => $name_subbakumutu) {
            # code...
            if (isset($name_subbakumutu)) {
              $bakuMutudetailparameternonklinik = new BakuMutuDetailParameterNonKlinik;
              //uuid
              $uuid4 = Uuid::uuid4();

              // $bakuMutudetailparameternonklinik->id_baku_mutu_detail_parameter_non_klinik = $uuid4->toString();
              $bakuMutudetailparameternonklinik->lab_id = 'd3bff0b4-622e-40b0-b10f-efa97a4e1bd5';
              $bakuMutudetailparameternonklinik->baku_mutu_id = $baku_mutu->id_baku_mutu;
              $bakuMutudetailparameternonklinik->sampletype_id = $baku_mutu->sampletype_id;
              $bakuMutudetailparameternonklinik->method_id = $baku_mutu->method_id;

              $bakuMutudetailparameternonklinik->name_baku_mutu_detail_parameter_non_klinik   = $data['name_subbakumutu'][$key];
              if (isset($data['min'][$key]) && $data['min'][$key] != "") {
                $bakuMutudetailparameternonklinik->min_baku_mutu_detail_parameter_non_klinik   = $data['min'][$key];
              }
              if (isset($data['max'][$key]) && $data['max'][$key] != "") {
                $bakuMutudetailparameternonklinik->max_baku_mutu_detail_parameter_non_klinik  = $data['max'][$key];
              }
              if (isset($data['equal'][$key]) && $data['equal'][$key] != "") {
                if (isset($data["equal"])) {
                  $bakuMutudetailparameternonklinik->equal_baku_mutu_detail_parameter_non_klinik = rubahNilaikeHtml(str_replace(",", ".", $data['equal'][$key]));
                }
                // $bakuMutudetailparameternonklinik->equal_baku_mutu_detail_parameter_non_klinik  = $data['equal'][$key];
              }
              if (isset($data['nilai_baku_mutu'][$key]) && $data['nilai_baku_mutu'][$key] != "") {
                if (isset($data["nilai_baku_mutu"])) {
                  $bakuMutudetailparameternonklinik->nilai_baku_mutu_detail_parameter_non_klinik = rubahNilaikeHtml(str_replace(",", ".", $data['nilai_baku_mutu'][$key]));
                }
                // $bakuMutudetailparameternonklinik->nilai_baku_mutu_detail_parameter_non_klinik  = $data['nilai_baku_mutu'][$key];
              }
              $bakuMutudetailparameternonklinik->save();
            }
          }
        }

        DB::commit();

        if ($simpan == true) {
          return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil diubah!"], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil diubah!"], 200);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
      }
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $hapus = BakuMutu::where('id_baku_mutu', $id)->delete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil dihapus!"], 200);
    }
  }

  public function resyncFormatBakuMutu(Request $request)
  {
    DB::beginTransaction();

    try {
      // terkadang ada data min max dan nilai bakumutunya yang berkoma
      $bakumutus = BakuMutu::orderBy('created_at', 'desc')
        ->get();

      if (count($bakumutus) > 0) {
        foreach ($bakumutus as $key => $bakumutu) {
          $post = BakuMutu::findOrFail($bakumutu->id_baku_mutu);

          if ($post->min != null) {
            if (strpos($post->min, ',') !== false) {
              $post->min = str_replace(",", ".", $post->min);
            }
          }

          if ($post->max != null) {
            if (strpos($post->max, ',') !== false) {
              $post->max = str_replace(",", ".", $post->max);
            }
          }

          if ($post->nilai_baku_mutu != null && ($post->min != null || $post->max != null)) {
            if (strpos($post->nilai_baku_mutu, ',') !== false) {
              $post->nilai_baku_mutu = str_replace(",", ".", $post->nilai_baku_mutu);
            }
          }

          $post->save();

          $data_bakumutu_detail_nonklinik = BakuMutuDetailParameterNonKlinik::where('baku_mutu_id', $bakumutu->id_baku_mutu)->get();

          if (count($data_bakumutu_detail_nonklinik) > 0) {
            foreach ($data_bakumutu_detail_nonklinik as $key => $value) {
              $post_detail = BakuMutuDetailParameterNonKlinik::findOrFail($value->id_baku_mutu_detail_parameter_non_klinik);

              if ($post_detail->min_baku_mutu_detail_parameter_non_klinik != null) {
                if (strpos($post_detail->min_baku_mutu_detail_parameter_non_klinik, ',') !== false) {
                  $post_detail->min_baku_mutu_detail_parameter_non_klinik = str_replace(",", ".", $post_detail->min_baku_mutu_detail_parameter_non_klinik);
                }
              }

              if ($post_detail->max_baku_mutu_detail_parameter_non_klinik != null) {
                if (strpos($post_detail->max_baku_mutu_detail_parameter_non_klinik, ',') !== false) {
                  $post_detail->max_baku_mutu_detail_parameter_non_klinik = str_replace(",", ".", $post_detail->max_baku_mutu_detail_parameter_non_klinik);
                }
              }

              if ($post_detail->nilai_baku_mutu_detail_parameter_non_klinik != null && ($post_detail->min_baku_mutu_detail_parameter_non_klinik != null || $post_detail->max_baku_mutu_detail_parameter_non_klinik != null)) {
                if (strpos($post_detail->nilai_baku_mutu_detail_parameter_non_klinik, ',') !== false) {
                  $post_detail->nilai_baku_mutu_detail_parameter_non_klinik = str_replace(",", ".", $post_detail->nilai_baku_mutu_detail_parameter_non_klinik);
                }
              }

              $post_detail->save();
            }
          }

          $data_bakumutu_detail_klinik = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $bakumutu->id_baku_mutu)->get();

          if (count($data_bakumutu_detail_klinik) > 0) {
            foreach ($data_bakumutu_detail_klinik as $key => $value) {
              $post_detail = BakuMutuDetailParameterKlinik::findOrFail($value->id_baku_mutu_detail_parameter_klinik);

              if ($post_detail->min_baku_mutu_detail_parameter_klinik != null) {
                if (strpos($post_detail->min_baku_mutu_detail_parameter_klinik, ',') !== false) {
                  $post_detail->min_baku_mutu_detail_parameter_klinik = str_replace(",", ".", $post_detail->min_baku_mutu_detail_parameter_klinik);
                }
              }

              if ($post_detail->max_baku_mutu_detail_parameter_klinik != null) {
                if (strpos($post_detail->max_baku_mutu_detail_parameter_klinik, ',') !== false) {
                  $post_detail->max_baku_mutu_detail_parameter_klinik = str_replace(",", ".", $post_detail->max_baku_mutu_detail_parameter_klinik);
                }
              }

              if ($post_detail->nilai_baku_mutu_detail_parameter_klinik != null && ($post_detail->min_baku_mutu_detail_parameter_klinik != null || $post_detail->max_baku_mutu_detail_parameter_klinik != null)) {
                if (strpos($post_detail->nilai_baku_mutu_detail_parameter_klinik, ',') !== false) {
                  $post_detail->nilai_baku_mutu_detail_parameter_klinik = str_replace(",", ".", $post_detail->nilai_baku_mutu_detail_parameter_klinik);
                }
              }

              $post_detail->save();
            }
          }
        }
      }

      DB::commit();

      return view('masterweb::response', [
        'code' => 200,
        'status' => true,
        'pesan' => 'Resynchronize tanda baca pada baku mutu terhadap format yang diinginkan berhasil diubah!',
      ]);
    } catch (\Exception $e) {
      DB::rollback();

      return view('masterweb::response', [
        'code' => 400,
        'status' => false,
        'pesan' => $e->getMessage(),
      ]);
    }
  }
}
