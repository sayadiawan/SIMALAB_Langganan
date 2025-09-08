<?php

namespace Smt\Masterweb\Http\Controllers;

// use Validator;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\User;
use Illuminate\Validation\Rule;

use Yajra\Datatables\Datatables;
use \Smt\Masterweb\Models\Method;

// use DB;

use Illuminate\Support\Facades\DB;
use Smt\Masterweb\Models\BakuMutu;
use App\Http\Controllers\Controller;
use Facade\Ignition\DumpRecorder\Dump;
use Smt\Masterweb\Models\Laboratorium;
use Illuminate\Support\Facades\Validator;
use Smt\Masterweb\Models\ParameterSatuanKlinik;

use Smt\Masterweb\Models\PermohonanUjiParameterKlinik;
use Smt\Masterweb\Models\ParameterSubSatuanKlinik;
use Smt\Masterweb\Models\BakuMutuDetailParameterKlinik;

class LaboratoriumBakuMutuKlinikManagement extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function rules($request)
  {
    $rule = [
      'parameter_jenis_klinik_id' => 'required',
      'parameter_satuan_klinik_id' => 'required',
      'library_id' => 'required',
    ];

    $pesan = [
      'parameter_jenis_klinik_id.required' => 'Parameter jenis klinik tidak boleh kosong!',
      'parameter_satuan_klinik_id.required' => 'Parameter satuan klinik tidak boleh kosong!',
      'library_id.required' => 'Acuan baku mutu tidak boleh kosong!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // #1 attempt
    /* $user = Auth()->user();
    $level = $user->getlevel->level;

    if ($level == "elits-dev" || $level == "admin" || $level == "LAB") {
      $get_lab = Laboratorium::where('id_laboratorium', 'bbed2259-2826-4711-b0fc-abdad5aace22')
        ->orwhere('kode_laboratorium', 'KLI')
        ->first();

      $lab_link = strtolower($get_lab->nama_laboratorium);
      $lab = $get_lab->nama_laboratorium;

      return view('masterweb::module.admin.laboratorium.baku-mutu-klinik.list', compact('lab_link', 'lab'));
    } else {
      return abort(404);
    } */

    // #2 attempt
    $get_lab = Laboratorium::where('id_laboratorium', 'bbed2259-2826-4711-b0fc-abdad5aace22')
      ->orwhere('kode_laboratorium', 'KLI')
      ->first();

    $lab_link = strtolower($get_lab->nama_laboratorium);
    $lab = $get_lab->nama_laboratorium;

    return view('masterweb::module.admin.laboratorium.baku-mutu-klinik.list', compact('lab_link', 'lab'));
  }

  public function data_baku_mutu_klinik(Request $request)
  {
    $get_lab = Laboratorium::where('id_laboratorium', 'bbed2259-2826-4711-b0fc-abdad5aace22')
      ->orwhere('kode_laboratorium', 'KLI')
      ->first();

    $datas = BakuMutu::where('lab_id', $get_lab->id_laboratorium)
      ->orderBy('created_at', 'desc')
      ->whereHas('laboratorium', function ($query) {
        $query->whereNull('deleted_at');
      })
      ->get();

    return Datatables::of($datas)
      ->addColumn('action', function ($data) {
        $detailButton = '<a class="dropdown-item" href="' . route('elits-baku-mutu-klinik.show', $data->id_baku_mutu) . '" title="Detail">Detail</a> ';

        $editButton = '<a href="' . route('elits-baku-mutu-klinik.edit', $data->id_baku_mutu) . '" class="dropdown-item" title="Edit">Edit</a> ';

        if ($data->parametersatuanklinik->name_parameter_satuan_klinik) {
          $name_parameter_satuan = $data->parametersatuanklinik->name_parameter_satuan_klinik;
        } else {
          $name_parameter_satuan = '-';
        }


        $deleteButton = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_baku_mutu   . '" data-nama="' . $name_parameter_satuan . '" title="Hapus">Hapus</a> ';

        $button = '<div class="dropdown show m-1">
                            <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Aksi
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                ' . $detailButton . '

                                ' . $editButton . '

                                ' . $deleteButton . '
                            </div>
                        </div>';

        return $button;
      })
      ->addColumn('jenis_parameter', function ($data) {
        if ($data->parameterjenisklinik->name_parameter_jenis_klinik) {
          $jenis_parameter = $data->parameterjenisklinik->name_parameter_jenis_klinik;
        } else {
          $jenis_parameter = '-';
        }

        return $jenis_parameter;
      })
      ->addColumn('parameter_satuan', function ($data) {
        if ($data->parametersatuanklinik->name_parameter_satuan_klinik) {
          $parameter_satuan = $data->parametersatuanklinik->name_parameter_satuan_klinik;
        } else {
          $parameter_satuan = '-';
        }

        return $parameter_satuan;
      })
      ->addColumn('library', function ($data) {
        if ($data->library->title_library) {
          $library = $data->library->title_library;
        } else {
          $library = '-';
        }

        return $library;
      })
      ->addColumn('nilai_baku_mutu', function ($data) {
        if ($data->nilai_baku_mutu) {
          $nilai_baku_mutu = rubahNilaikeHtml($data->nilai_baku_mutu);
        } else {
          $nilai_baku_mutu = '-';
        }

        return $nilai_baku_mutu;
      })
      ->addColumn('is_khusus_baku_mutu', function ($data) {
        if ($data->is_khusus_baku_mutu == '0') {
          $status = "General";
        } else {
          $status = "Specific";
        }

        return $status;
      })
      ->addColumn('detail_data_khusus', function ($data) {
        if (($data->minimal_umur_baku_mutu != null || $data->maksimal_umur_baku_mutu != null) && $data->gender_baku_mutu != null) {
          if ($data->gender_baku_mutu == "L") {
            $gender = "Laki-laki";
          } else {
            $gender = "Perempuan";
          }

          $status = "Umur: " . $data->minimal_umur_baku_mutu . "-" . $data->maksimal_umur_baku_mutu . "<br>" . "Jenis kelamin: " . $gender;
        } else if (($data->minimal_umur_baku_mutu == null || $data->maksimal_umur_baku_mutu == null) && $data->gender_baku_mutu != null) {
          if ($data->gender_baku_mutu == "L") {
            $gender = "Laki-laki";
          } else {
            $gender = "Perempuan";
          }

          $status = "Jenis kelamin: " . $gender;
        } else {
          $status = "-";
        }

        return $status;
      })
      ->rawColumns(['action', 'jenis_parameter', 'parameter_satuan', 'library', 'nilai_baku_mutu', 'is_khusus_baku_mutu', 'detail_data_khusus'])
      ->addIndexColumn() //increment
      ->make(true);
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
    $level = $user->getlevel->level;

    $get_lab = Laboratorium::where('id_laboratorium', 'bbed2259-2826-4711-b0fc-abdad5aace22')
      ->orwhere('kode_laboratorium', 'KLI')
      ->first();

    $lab_link = strtolower($get_lab->nama_laboratorium);
    $lab = $get_lab->nama_laboratorium;


    return view('masterweb::module.admin.laboratorium.baku-mutu-klinik.add', compact('get_lab', 'lab_link', 'lab'));
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
      // check jika sudah ada parameter jenis dan stuan di baku mutu
      $check = BakuMutu::where('parameter_jenis_klinik_id', $request->post('parameter_jenis_klinik_id'))
        ->where('parameter_satuan_klinik_id', $request->post('parameter_satuan_klinik_id'))
        ->first();

      if ($check) {
        if ($check->is_khusus_baku_mutu == $request->is_khusus_baku_mutu) {
          if (isset($check->gender_baku_mutu)) {
            if ($check->gender_baku_mutu == $request->gender_baku_mutu) {
              return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data gender yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
            } else {
              return $this->storeCreateBakuMutu($request->all());
            }
          } else {
            return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data khusus yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
          }
        } else {
          return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data khusus yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
        }
      } else {
        return $this->storeCreateBakuMutu($request->all());
      }
    }
  }

  private function storeCreateBakuMutu($request)
  {
    DB::beginTransaction();

    try {
      $post = new BakuMutu();
      $post->library_id = $request['library_id'];
      $post->lab_id = $request['lab_id'];
      $post->parameter_jenis_klinik_id = $request['parameter_jenis_klinik_id'];
      $post->parameter_satuan_klinik_id = $request['parameter_satuan_klinik_id'];
      $post->is_sub_parameter_satuan_baku_mutu = $request['is_sub_parameter_satuan_baku_mutu'];
      $post->is_khusus_baku_mutu = $request['is_khusus_baku_mutu'];

      $id_parameter = $request['parameter_satuan_klinik_id'];

      if ($request['is_khusus_baku_mutu'] == "1") {
        $post->minimal_umur_baku_mutu = $request['minimal_umur_baku_mutu'];
        $post->maksimal_umur_baku_mutu = $request['maksimal_umur_baku_mutu'];
        $post->gender_baku_mutu = $request['gender_baku_mutu'];
      } else {
        $post->minimal_umur_baku_mutu = null;
        $post->maksimal_umur_baku_mutu = null;
        $post->gender_baku_mutu = null;
      }

      if ($request['is_sub_parameter_satuan_baku_mutu'] == 0) {
        $post->min = $request['min'];
        $post->max = $request['max'];
        $post->equal = $request['equal'];
        $post->nilai_baku_mutu = rubahNilaikeHtml($request['nilai_baku_mutu']);
        $post->unit_id = $request['unit_id'];

        $this->updateBakuMutuPermohonanUji($request, $id_parameter);
      }

      $simpan = $post->save();

      // jika dibaku mutu untuk parameter memiliki sub satuan
      if ($request['is_sub_parameter_satuan_baku_mutu'] == 1) {
        if ($request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik'] !== null) {
          $count_sub_parameter_satuan = count($request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik']);

          for ($i = 0; $i < $count_sub_parameter_satuan; $i++) {
            $post_sub_parameter = new BakuMutuDetailParameterKlinik();
            $post_sub_parameter->baku_mutu_id = $post['id_baku_mutu'];
            $post_sub_parameter->parameter_sub_satuan_baku_mutu_detail_parameter_klinik = $request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik'][$i];
            $post_sub_parameter->unit_id_baku_mutu_detail_parameter_klinik = $request['unit_id_baku_mutu_detail_parameter_klinik'][$i];
            $post_sub_parameter->min_baku_mutu_detail_parameter_klinik = $request['min_baku_mutu_detail_parameter_klinik'][$i];
            $post_sub_parameter->max_baku_mutu_detail_parameter_klinik = $request['max_baku_mutu_detail_parameter_klinik'][$i];
            $post_sub_parameter->equal_baku_mutu_detail_parameter_klinik = $request['equal_baku_mutu_detail_parameter_klinik'][$i];
            $post_sub_parameter->nilai_baku_mutu_detail_parameter_klinik = $request['nilai_baku_mutu_detail_parameter_klinik'][$i];

            $simpan_post_sub_parameter = $post_sub_parameter->save();
          }
        }
      }


      DB::commit();

      if ($simpan == true) {
        return response()->json(['status' => true, 'pesan' => "Data baku mutu klinik berhasil disimpan!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data baku mutu klinik tidak berhasil disimpan!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
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
    $item = BakuMutu::find($id);

    $get_lab = Laboratorium::find($item->lab_id);
    $lab_link = strtolower($get_lab->nama_laboratorium);
    $lab = $get_lab->nama_laboratorium;

    // cek apakah di baku mutu memiliki sub parameter satuan
    $parameter_sub_satuan_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $id)
      ->whereHas('parametersubsatuanklinik', function ($query) {
        $query->orderBy('created_at', 'asc')
          ->whereNull('deleted_at');
      })
      ->get();

    if (count($parameter_sub_satuan_baku_mutu) > 0) {
      $data_parameter_sub_satuan_baku_mutu = $parameter_sub_satuan_baku_mutu;
    } else {
      $data_parameter_sub_satuan_baku_mutu = [];
    }

    return view('masterweb::module.admin.laboratorium.baku-mutu-klinik.show', [
      'item' => $item,
      'lab_link' => $lab_link,
      'lab' => $lab,
      'data_parameter_sub_satuan_baku_mutu' => $data_parameter_sub_satuan_baku_mutu
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $item = BakuMutu::find($id);

    $get_lab = Laboratorium::find($item->lab_id);
    $lab_link = strtolower($get_lab->nama_laboratorium);
    $lab = $get_lab->nama_laboratorium;

    // cek apakah di baku mutu memiliki sub parameter satuan
    $parameter_sub_satuan_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $id)
      ->whereHas('parametersubsatuanklinik', function ($query) {
        $query->orderBy('created_at', 'asc')
          ->whereNull('deleted_at');
      })
      ->get();

    if (count($parameter_sub_satuan_baku_mutu) > 0) {
      $data_parameter_sub_satuan_baku_mutu = $parameter_sub_satuan_baku_mutu;
    } else {
      $data_parameter_sub_satuan_baku_mutu = [];
    }

    return view('masterweb::module.admin.laboratorium.baku-mutu-klinik.edit', [
      'item' => $item,
      'lab_link' => $lab_link,
      'lab' => $lab,
      'data_parameter_sub_satuan_baku_mutu' => $data_parameter_sub_satuan_baku_mutu
    ]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    $validator = $this->rules($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      // check parameter jenis dan satuan
      $post = BakuMutu::find($id);

      // cek dulu isian field mulai jenis, satuan, dan data khusus sama atau tidak
      if ($post->parameter_jenis_klinik_id == $request->parameter_jenis_klinik_id && $post->parameter_satuan_klinik_id == $request->parameter_satuan_klinik_id && $post->is_khusus_baku_mutu == $request->is_khusus_baku_mutu) {
        // ngecek lagi apakah punya detail gender atau tidak
        if (isset($request->gender_baku_mutu)) {
          if ($post->gender_baku_mutu == $request->gender_baku_mutu) {
            return $this->storeUpdateBakuMutu($request->all(), $id);
          } else {
            $check = BakuMutu::where('parameter_jenis_klinik_id', $request->parameter_jenis_klinik_id)
              ->where('parameter_satuan_klinik_id', $request->parameter_satuan_klinik_id)
              ->where('gender_baku_mutu', $request->gender_baku_mutu)
              ->first();

            if ($check) {
              return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data gender yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
            } else {
              return $this->storeUpdateBakuMutu($request->all(), $id);
            }
          }
        } else {
          return $this->storeUpdateBakuMutu($request->all(), $id);
        }
      } else {
        // else ini jika semua data diubah (mulai jenis, satuan, dan data khusus)
        $check = BakuMutu::where('parameter_jenis_klinik_id', $request->parameter_jenis_klinik_id)
          ->where('parameter_satuan_klinik_id', $request->parameter_satuan_klinik_id)
          ->where('is_khusus_baku_mutu', $request->is_khusus_baku_mutu)
          ->first();

        if ($check) {
          if ($check->gender_baku_mutu != null) {
            if ($check->gender_baku_mutu == $request->gender_baku_mutu) {
              return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data gender yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
            } else {
              return $this->storeUpdateBakuMutu($request->all(), $id);
            }
          } else {
            return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data gender yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
          }
        } else {
          /* $check = BakuMutu::where('parameter_jenis_klinik_id', $request->parameter_jenis_klinik_id)
            ->where('parameter_satuan_klinik_id', $request->parameter_satuan_klinik_id)
            ->first();

          if ($check) {
            return response()->json(['status' => false, 'pesan' => "Parameter sudah pernah dibuat di baku mutu dengan data khusus yang sama, silahkan pilih parameter jenis dan satuan yang berbeda!"], 200);
          } else {
            return $this->storeUpdateBakuMutu($request->all(), $id);
          } */

          return $this->storeUpdateBakuMutu($request->all(), $id);
        }
      }
    }
  }

  private function updateBakuMutuPermohonanUji($request, $id_parameter){
    $all_permohonanUjiParameterKlinik = PermohonanUjiParameterKlinik::where('parameter_satuan_klinik',$id_parameter)->whereYear('created_at', date('Y'))->get();

    foreach ($all_permohonanUjiParameterKlinik as $permohonanUjiParameterKlinik) {
      // $data_permohonan_uji_parameter_satuan = PermohonanUjiParameterSatuan::where('permohonan_uji_parameter_klinik_id', $permohonanUjiParameterKlinik->id_permohonan_uji_parameter_klinik
      if ($request['is_khusus_baku_mutu'] == "1") {
        $request['gender_baku_mutu'];

        if ($permohonanUjiParameterKlinik->permohonanujiklinik->pasien->gender_pasien == $request['gender_baku_mutu']) {
          $permohonanUjiParameterKlinik->baku_mutu_permohonan_uji_parameter_klinik =  rubahNilaikeHtml($request['nilai_baku_mutu']);
        }

      }else{
        $permohonanUjiParameterKlinik->baku_mutu_permohonan_uji_parameter_klinik =  rubahNilaikeHtml($request['nilai_baku_mutu']);
      }  
    }
  }

  private function storeUpdateBakuMutu($request, $id)
  {

    $post = BakuMutu::find($id);
    $post->library_id = $request['library_id'];
    $post->lab_id = $request['lab_id'];
    $post->parameter_jenis_klinik_id = $request['parameter_jenis_klinik_id'];
    $post->parameter_satuan_klinik_id = $request['parameter_satuan_klinik_id'];
    $post->unit_id = $request['unit_id'];
    $post->is_sub_parameter_satuan_baku_mutu = $request['is_sub_parameter_satuan_baku_mutu'];
    $post->is_khusus_baku_mutu = $request['is_khusus_baku_mutu'];
    $id_parameter = $request['parameter_satuan_klinik_id'];


    if ($request['is_khusus_baku_mutu'] == "1") {
      $post->minimal_umur_baku_mutu = $request['minimal_umur_baku_mutu'];
      $post->maksimal_umur_baku_mutu = $request['maksimal_umur_baku_mutu'];
      $post->gender_baku_mutu = $request['gender_baku_mutu'];
    } else {
      $post->minimal_umur_baku_mutu = null;
      $post->maksimal_umur_baku_mutu = null;
      $post->gender_baku_mutu = null;
    }

    // jika dibaku mutu untuk parameter memiliki sub satuan
    if ($request['is_sub_parameter_satuan_baku_mutu'] == 1) {
      if ($request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik'] != null) {
        $count_sub_parameter_satuan = count($request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik']);

        $check_delete = DB::table('tb_baku_mutu_detail_parameter_klinik')->where('baku_mutu_id', $id)->get();

        if (count($check_delete) > 0) {
          DB::table('tb_baku_mutu_detail_parameter_klinik')->where('baku_mutu_id', $id)->delete();
        }

        for ($i = 0; $i < $count_sub_parameter_satuan; $i++) {
          $post_sub_parameter = new BakuMutuDetailParameterKlinik();
          $post_sub_parameter->baku_mutu_id = $id;
          $post_sub_parameter->parameter_sub_satuan_baku_mutu_detail_parameter_klinik = $request['parameter_sub_satuan_baku_mutu_detail_parameter_klinik'][$i];
          $post_sub_parameter->unit_id_baku_mutu_detail_parameter_klinik = $request['unit_id_baku_mutu_detail_parameter_klinik'][$i];
          $post_sub_parameter->min_baku_mutu_detail_parameter_klinik = $request['min_baku_mutu_detail_parameter_klinik'][$i];
          $post_sub_parameter->max_baku_mutu_detail_parameter_klinik = $request['max_baku_mutu_detail_parameter_klinik'][$i];
          $post_sub_parameter->equal_baku_mutu_detail_parameter_klinik = $request['equal_baku_mutu_detail_parameter_klinik'][$i];
          $post_sub_parameter->nilai_baku_mutu_detail_parameter_klinik = rubahNilaikeHtml($request['nilai_baku_mutu_detail_parameter_klinik'][$i]);

          $simpan_post_sub_parameter = $post_sub_parameter->save();
        }

        $post->min = null;
        $post->max = null;
        $post->equal = null;
        $post->nilai_baku_mutu = null;
      }
    }

    if ($request['is_sub_parameter_satuan_baku_mutu'] == 0) {
      $post->min = $request['min'];
      $post->max = $request['max'];
      $post->equal = $request['equal'];
      $post->nilai_baku_mutu = rubahNilaikeHtml($request['nilai_baku_mutu']);

      $check_delete = DB::table('tb_baku_mutu_detail_parameter_klinik')->where('baku_mutu_id', $id)->get();


      $this->updateBakuMutuPermohonanUji($request, $id_parameter);

      if (count($check_delete) > 0) {
        DB::table('tb_baku_mutu_detail_parameter_klinik')->where('baku_mutu_id', $id)->delete();
      }
    }

    $simpan = $post->save();

    DB::commit();

    if ($simpan == true) {
      return response()->json(['status' => true, 'pesan' => "Data baku mutu klinik berhasil diubah!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data baku mutu klinik tidak berhasil diubah!"], 200);
    }

    /* DB::beginTransaction();

    try {

    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
    } */
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $hapus = BakuMutu::find($id)->delete();
    BakuMutuDetailParameterKlinik::where('baku_mutu_id',$id)->delete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil dihapus!"], 200);
    }
  }

  public function checkBakuMutuParameterKlinik(Request $request)
  {
    $response = array();
    $parameter_jenis = $request->parameter_jenis;
    $parameter_satuan = $request->parameter_satuan;

    $check = BakuMutu::where('parameter_jenis_klinik_id', $parameter_jenis)
      ->where('parameter_satuan_klinik_id', $parameter_satuan)
      ->first();

    if ($check !== null) {
      return response()->json(['status' => true, 'pesan' => "Maaf, Anda tidak bisa menambahkan lebih parameter yang sama di Baku Mutu Klinik!"], 200);
    }
  }

  public function getBakuMutuKlinik(Request $request)
  {
    $search = $request->search;

    if ($search == '') {
      $data = BakuMutu::orderby('name_report', 'asc')
        ->select('id_baku_mutu', 'name_report')
        ->limit(10)
        ->get();
    } else {
      $data = BakuMutu::orderby('name_report', 'asc')
        ->select('id_baku_mutu', 'name_report')
        ->where('name_report', 'like', '%' . $search . '%')
        ->limit(10)
        ->get();
    }

    $response = array();
    foreach ($data as $item) {
      $response[] = array(
        "id" => $item->id_baku_mutu,
        "text" => $item->name_report
      );
    }

    return response()->json($response);
  }

  public function checkBakuMutuSubParameterSatuan(Request $request)
  {
    $get_parameter_jenis = $request->parameter_jenis;
    $get_parameter_satuan = $request->parameter_satuan;

    /* $check_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $get_parameter_satuan)
            ->where('parameter_jenis_klinik', $get_parameter_jenis)
            ->get(); */

    $check_sub_parameter = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $get_parameter_satuan)
      ->orderBy('name_parameter_sub_satuan_klinik', 'asc')
      ->get();

    if (count($check_sub_parameter) > 0) {
      return response()->json(['status' => true, 'data_sub' => $check_sub_parameter], 200);
    } else {
      return response()->json(['status' => false, 'data_sub' => []], 200);
    }
  }
}