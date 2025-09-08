<?php

namespace Smt\Masterweb\Http\Controllers;

use DB;
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
use \Smt\Masterweb\Models\PenerimaanSample;

use SimpleSoftwareIO\QrCode\Facades\QrCode;


use \Smt\Masterweb\Models\LaboratoriumMethod;
use \Smt\Masterweb\Models\LaboratoriumProgress;
use \Smt\Masterweb\Models\BakuMutuDetailParameterNonKlinik;


class LaboratoriumBakuMutuKimiaManagement extends Controller
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

    $query = BakuMutu::where('tb_baku_mutu.lab_id', '=', '3416ca19-6c69-4e5f-a004-ae8275de7644')
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
      });

    if ($request->has('search') && !empty($request->get('search'))) {
      $query->where(function ($query) use ($request) {
        $search = $request->get('search');
        $query->where('ms_sample_type.name_sample_type', 'like', "%$search%")
          ->orWhere('ms_method.params_method', 'like', "%$search%");
      });
    }

    if ($request->has('jenis_sample') && !empty($request->get('jenis_sample'))) {
      $query->where('ms_sample_type.name_sample_type', $request->get('jenis_sample'));
    }

    $datas = $query->get();

    if ($request->ajax()) {
      return DataTables::of($datas)
        ->addColumn('action', function ($data) {
          $editButton = getAction('update') ? '<a href="' . route('elits-baku-mutu-kimia.edit', [$data->id_baku_mutu]) . '" class="dropdown-item" data-toggle="tooltip" data-custom-class="tooltip-info" data-placement="top" title="Edit Data">Edit</a>' : '';
          $deleteButton = getAction('delete') ? '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id_baku_mutu . '" data-nama="' . $data->name_sample_type . ' - ' . $data->params_method . '" title="Hapus">Hapus</a>' : '';
          $buttonAksi = $editButton . $deleteButton;
          return '<div class="dropdown show m-1"><a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLinkAksi" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Aksi</a><div class="dropdown-menu dropdown-scroll-menu" aria-labelledby="dropdownMenuLink">' . $buttonAksi . '</div></div>';
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
        ->rawColumns(['action', 'jenis_sample', 'parameter', 'acuan_bakumutu', 'nilai_bakumutu'])
        ->addIndexColumn()
        ->make(true);
    }

    $lab_link = "kimia";
    $lab = "Kimia";

    return view('masterweb::module.admin.laboratorium.baku-mutu.list', compact('lab_link', 'lab'));
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
    $methods = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', '3416ca19-6c69-4e5f-a004-ae8275de7644')

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
    $lab_link = "kimia";
    $lab = "Kimia";

    return view('masterweb::module.admin.laboratorium.baku-mutu.add', compact('units', 'all_jenis_makanan', 'libraries', 'sample_types', 'id_lab', 'methods', 'lab_link', 'lab'));
    //get all menu public
  }




  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();

    // $validator = $this->rules($request->all());

    // if ($validator->fails()) {
    //     return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    // } else {
    DB::beginTransaction();

    try {
      $validated = $request->validate([
        'sampletype_id' => ['required'],
        'method_id' => ['required'],
      ]);

      if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
        $check = BakuMutu::where('lab_id', '3416ca19-6c69-4e5f-a004-ae8275de7644')
          ->where('sampletype_id', $data['sampletype_id'])
          ->where('method_id', $data['method_id'])
          ->where('jenis_makanan_id', $data['jenis_makanan_id'])
          ->first();
      } else {
        $check = BakuMutu::where('lab_id', '3416ca19-6c69-4e5f-a004-ae8275de7644')
          ->where('sampletype_id', $data['sampletype_id'])
          ->where('method_id', $data['method_id'])
          ->first();
      }

      if ($check != null) {
        return response()->json(['status' => false, 'pesan' => "Data baku mutu kimia sudah pernah diinputkan. Untuk detailnya bisa dicari pada kolom pencarian!"], 200);
      }

      if ($data["is_sub"] == "false") {

        $user = Auth()->user();
        $baku_mutu = new BakuMutu;
        //uuid
        $uuid4 = Uuid::uuid4();

        $baku_mutu->id_baku_mutu = $uuid4->toString();
        $baku_mutu->name_report = $data['name_report'];
        $baku_mutu->sampletype_id = $data['sampletype_id'];
        $baku_mutu->method_id = $data['method_id'];
        $baku_mutu->unit_id = $data['unit_id'];
        $baku_mutu->min = $data['min_no_sub'];
        $baku_mutu->max = $data['max_no_sub'];
        $baku_mutu->is_sub = 0;
        if (isset($data["equal_no_sub"])) {
          $baku_mutu->equal = rubahNilaikeHtml(str_replace(",", ".", $data["equal_no_sub"]));
        }
        // $baku_mutu->equal = $data['equal_no_sub'];
        $baku_mutu->library_id = $data['library_id'];
        if (isset($data["nilai_baku_mutu_no_sub"])) {
          $baku_mutu->nilai_baku_mutu = rubahNilaikeHtml(str_replace(",", ".", $data["nilai_baku_mutu_no_sub"]));
        }
        if (isset($request->jenis_makanan_id) && $request->jenis_makanan_id != null) {
          $baku_mutu->jenis_makanan_id = $request->jenis_makanan_id;
        } else {
          $baku_mutu->jenis_makanan_id = NULL;
        }
        // $baku_mutu->nilai_baku_mutu = $data['nilai_baku_mutu_no_sub'];
        $baku_mutu->lab_id = '3416ca19-6c69-4e5f-a004-ae8275de7644';
        $baku_mutu->save();
      } else {
        $user = Auth()->user();
        $baku_mutu = new BakuMutu;
        //uuid
        $uuid4 = Uuid::uuid4();

        $baku_mutu->id_baku_mutu = $uuid4->toString();
        $baku_mutu->name_report = $data['name_report'];
        $baku_mutu->sampletype_id = $data['sampletype_id'];
        $baku_mutu->method_id = $data['method_id'];
        $baku_mutu->unit_id = $data['unit_id'];
        $baku_mutu->library_id = $data['library_id'];
        $baku_mutu->is_sub = 1;
        $baku_mutu->lab_id = '3416ca19-6c69-4e5f-a004-ae8275de7644';
        $baku_mutu->save();

        foreach ($data['name_subbakumutu'] as $key => $name_subbakumutu) {
          # code...
          if (isset($name_subbakumutu)) {
            $bakuMutudetailparameternonklinik = new BakuMutuDetailParameterNonKlinik;
            //uuid
            $uuid4 = Uuid::uuid4();

            $bakuMutudetailparameternonklinik->id_baku_mutu_detail_parameter_non_klinik = $uuid4->toString();
            $bakuMutudetailparameternonklinik->lab_id = '3416ca19-6c69-4e5f-a004-ae8275de7644';
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

      if ($baku_mutu == true) {
        return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil disimpan!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil disimpan!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
    }
    // }

    // $sample = Sample::where('id_samples',$id)->first();


    if ($baku_mutu == true) {
      return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil diubah!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil diubah!"], 200);
    }
    // return redirect()->route('elits-baku-mutu-kimia.index')->with(['status'=>'Baku Mutu berhasil diinput']);

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

    $bakuMutudetailparameternonkliniks = BakuMutuDetailParameterNonKlinik::where('baku_mutu_id', $id)
      ->where('lab_id', '3416ca19-6c69-4e5f-a004-ae8275de7644')
      ->where('sampletype_id', $baku_mutu->sampletype_id)
      ->where('method_id', $baku_mutu->method_id)
      ->orWhere('baku_mutu_id', $baku_mutu->id_baku_mutu)
      ->orderBy('created_at')
      ->get();
    $all_jenis_makanan = JenisMakanan::all();

    $methods = LaboratoriumMethod::where('tb_laboratorium_method.laboratorium_id', '=', '3416ca19-6c69-4e5f-a004-ae8275de7644')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_laboratorium_method.method_id')
          ->whereNull('tb_laboratorium_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->get();
    $id_lab = "3416ca19-6c69-4e5f-a004-ae8275de7644";
    $sample_types = SampleType::orderBy('created_at')->get();



    $libraries = Library::all();
    $units = Unit::all();
    $lab_link = "kimia";
    $lab = "Kimia";


    return view('masterweb::module.admin.laboratorium.baku-mutu.edit', compact('all_jenis_makanan', 'id', 'bakuMutudetailparameternonkliniks', 'baku_mutu', 'units', 'libraries', 'sample_types', 'id_lab', 'methods', 'lab_link', 'lab'));
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
    $data = $request->all();

    DB::beginTransaction();

    try {
      $validated = $request->validate([
        'sampletype_id' => ['required'],
        'method_id' => ['required'],
      ]);


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
        $baku_mutu->name_report = $data['name_report'];
        $baku_mutu->unit_id = $data['unit_id'];
        $baku_mutu->min = $data['min_no_sub'];
        $baku_mutu->max = $data['max_no_sub'];
        $baku_mutu->is_sub = 0;
        if (isset($data["equal_no_sub"])) {
          $baku_mutu->equal = rubahNilaikeHtml(str_replace(",", ".", $data["equal_no_sub"]));
        }
        $baku_mutu->library_id = $data['library_id'];
        if (isset($data["nilai_baku_mutu_no_sub"])) {
          $baku_mutu->nilai_baku_mutu = rubahNilaikeHtml(str_replace(",", ".", $data["nilai_baku_mutu_no_sub"]));
        }

        $baku_mutu->save();

        // $sample = Sample::where('id_samples',$id)->first();
      } else {
        $user = Auth()->user();
        $baku_mutu->name_report = $data['name_report'];
        $baku_mutu->unit_id = $data['unit_id'];
        $baku_mutu->min = NULL;
        $baku_mutu->max = NULL;
        $baku_mutu->is_sub = 1;
        $baku_mutu->equal = NULL;
        $baku_mutu->library_id = $data['library_id'];
        $baku_mutu->nilai_baku_mutu = NULL;
        $baku_mutu->save();


        BakuMutuDetailParameterNonKlinik::where('baku_mutu_id', $baku_mutu->id_baku_mutu)->delete();
        foreach ($data['name_subbakumutu'] as $key => $name_subbakumutu) {
          # code...
          if (isset($name_subbakumutu)) {
            $bakuMutudetailparameternonklinik = new BakuMutuDetailParameterNonKlinik;
            //uuid
            $uuid4 = Uuid::uuid4();

            $bakuMutudetailparameternonklinik->id_baku_mutu_detail_parameter_non_klinik = $uuid4->toString();
            $bakuMutudetailparameternonklinik->lab_id = '3416ca19-6c69-4e5f-a004-ae8275de7644';
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

      if ($baku_mutu == true) {
        return response()->json(['status' => true, 'pesan' => "Data baku mutu mikro berhasil disimpan!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data baku mutu mikro tidak berhasil disimpan!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => $e->getMessage()], 200);
    }

    // return redirect()->route('elits-baku-mutu-kimia.index')->with(['status'=>'Baku Mutu berhasil diupdate']);


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
      return response()->json(['status' => true, 'pesan' => "Data baku mutu kimia berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data baku mutu kimia tidak berhasil dihapus!"], 200);
    }
  }
}
