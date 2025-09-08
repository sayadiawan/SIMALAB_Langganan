<?php

namespace Smt\Masterweb\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\User;

use \Smt\Masterweb\Models\Method;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\ParameterJenisKlinik;

use Illuminate\Validation\Rule;
use Kreait\Firebase\Http\Requests;

class LaboratoriumParameterJenisKlinikManagement extends Controller
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
  public function index()
  {
    // #1 attempt
    /* $user = Auth()->user();
    $level = $user->getlevel->level;

    if ($level == "elits-dev" || $level == "admin") {
      $data = ParameterJenisKlinik::orderBy('created_at', 'desc')->get();

      return view('masterweb::module.admin.laboratorium.parameter-jenis-klinik.list', compact('data'));
    } else {
      return abort(404);
    } */

    // #2 attempt
    $data = ParameterJenisKlinik::orderBy('created_at', 'desc')->get();

    return view('masterweb::module.admin.laboratorium.parameter-jenis-klinik.list', compact('data'));
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

    return view('masterweb::module.admin.laboratorium.parameter-jenis-klinik.add');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make(
      $request->all(),
      [
        'name_parameter_jenis_klinik' => 'required|max:256',
        'code_parameter_jenis_klinik' => 'required|max:6|unique:ms_parameter_jenis_klinik,code_parameter_jenis_klinik,NULL,id_parameter_jenis_klinik,deleted_at,NULL'
      ],
      [
        'name_parameter_jenis_klinik.required' => 'Nama parameter jenis klinik tidak boleh kosong!',
        'code_parameter_jenis_klinik.required' => 'Kode parameter jenis klinik tidak boleh kosong!',
        'code_parameter_jenis_klinik.max' => 'Kode parameter jenis klinik tidak boleh lebih dari 6 karakter!',
        'code_parameter_jenis_klinik.unique' => 'Kode parameter jenis klinik sudah tersedia, silahkan masukkan kode yang berbeda!',
      ]
    );

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post = new ParameterJenisKlinik();
        $post->name_parameter_jenis_klinik = $request->post('name_parameter_jenis_klinik');
        $post->code_parameter_jenis_klinik = $request->post('code_parameter_jenis_klinik');
        $post->sort_parameter_jenis_klinik = $request->post('sort_parameter_jenis_klinik');

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json(['status' => true, 'pesan' => "Data parameter jenis berhasil disimpan!"], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data parameter jenis tidak berhasil disimpan!"], 400);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e], 400);
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
    //get auth user

    $user = Auth()->user();
    $level = $user->getlevel->level;
    $item = ParameterJenisKlinik::find($id);

    $existing_sorts = ParameterJenisKlinik::orderBy('sort_parameter_jenis_klinik')
    ->pluck('name_parameter_jenis_klinik', 'sort_parameter_jenis_klinik');

    return view('masterweb::module.admin.laboratorium.parameter-jenis-klinik.edit', compact('item', 'existing_sorts'));
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
    $post = ParameterJenisKlinik::find($id);

    if ($request->code_parameter_jenis_klinik !== $post->code_parameter_jenis_klinik) {
      $validator = Validator::make(
        $request->all(),
        [
          'name_parameter_jenis_klinik' => 'required|max:256',
          'code_parameter_jenis_klinik' => 'required|max:6|unique:ms_parameter_jenis_klinik,code_parameter_jenis_klinik,NULL,id_parameter_jenis_klinik,deleted_at,NULL'
        ],
        [
          'name_parameter_jenis_klinik.required' => 'Nama parameter jenis klinik tidak boleh kosong!',
          'code_parameter_jenis_klinik.required' => 'Kode parameter jenis klinik tidak boleh kosong!',
          'code_parameter_jenis_klinik.max' => 'Kode parameter jenis klinik tidak boleh lebih dari 6 karakter!',
          'code_parameter_jenis_klinik.unique' => 'Kode parameter jenis klinik sudah tersedia, silahkan masukkan kode yang berbeda!',
        ]
      );
    } else {
      $validator = Validator::make(
        $request->all(),
        [
          'name_parameter_jenis_klinik' => 'required|max:256',
          'code_parameter_jenis_klinik' => 'required|max:6'
        ],
        [
          'name_parameter_jenis_klinik.required' => 'Nama parameter jenis klinik tidak boleh kosong!',
          'code_parameter_jenis_klinik.required' => 'Kode parameter jenis klinik tidak boleh kosong!',
          'code_parameter_jenis_klinik.max' => 'Kode parameter jenis klinik tidak boleh lebih dari 6 karakter!',
        ]
      );
    }

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      DB::beginTransaction();

      try {
        $post->name_parameter_jenis_klinik = $request->post('name_parameter_jenis_klinik');
        $post->code_parameter_jenis_klinik = $request->post('code_parameter_jenis_klinik');

        // Simpan nomor urut yang lama
        $old_sort = $post->sort_parameter_jenis_klinik;

        // DD($old_sort);

        // Ambil nomor urut setelah yang dipilih di dropdown
        $after_sort = $request->post('after_sort_parameter_jenis_klinik');

        // Tentukan nomor urut yang baru (after_sort + 1)
        if($after_sort != null){
          $new_sort = $after_sort ? ($after_sort + 1) : 1;
        }else{
          $new_sort = $old_sort;
        }

        // Cek jika item yang sedang diedit tidak memiliki nomor urut sebelumnya
        if (is_null($old_sort) || $old_sort == 0) {
            // Jika item tidak memiliki urutan sebelumnya, update semua item
            // dengan nomor urut lebih besar dari atau sama dengan new_sort
            ParameterJenisKlinik::where('sort_parameter_jenis_klinik', '>=', $new_sort)
                ->increment('sort_parameter_jenis_klinik');
        } else {
            // Jika nomor urut diubah
            if ($old_sort != $new_sort) {
                if ($new_sort > $old_sort) {
                    // Jika pindah ke bawah, kurangi urutan item antara old_sort dan new_sort
                    ParameterJenisKlinik::whereBetween('sort_parameter_jenis_klinik', [$old_sort + 1, $new_sort])
                        ->decrement('sort_parameter_jenis_klinik');
                } else {
                    // Jika pindah ke atas, tambahkan urutan item antara new_sort dan old_sort
                    ParameterJenisKlinik::whereBetween('sort_parameter_jenis_klinik', [$new_sort, $old_sort - 1])
                        ->increment('sort_parameter_jenis_klinik');
                }
            }
        }

        // Update urutan item yang sedang diedit
        $post->sort_parameter_jenis_klinik = $new_sort;

        $simpan = $post->save();

        DB::commit();

        if ($simpan == true) {
          return response()->json(['status' => true, 'pesan' => "Data parameter jenis berhasil diubah!"], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data parameter jenis tidak berhasil diubah!"], 400);
        }
      } catch (\Exception $e) {
        DB::rollback();

        return response()->json(['status' => false, 'pesan' => $e], 400);
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
    $hapus = ParameterJenisKlinik::where('id_parameter_jenis_klinik', $id)->delete();

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data parameter jenis berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data parameter jenis tidak berhasil dihapus!"], 400);
    }
  }

  public function getParameterJenisKlinik(Request $request)
  {
    $search = $request->search;

    if ($search == '') {
      $data = ParameterJenisKlinik::orderby('name_parameter_jenis_klinik', 'asc')->select('id_parameter_jenis_klinik', 'name_parameter_jenis_klinik', 'code_parameter_jenis_klinik')->limit(10)->get();
    } else {
      $data = ParameterJenisKlinik::orderby('name_parameter_jenis_klinik', 'asc')->select('id_parameter_jenis_klinik', 'name_parameter_jenis_klinik', 'code_parameter_jenis_klinik')->where('name_parameter_jenis_klinik', 'like', '%' . $search . '%')->limit(10)->get();
    }

    $response = array();
    foreach ($data as $item) {
      $response[] = array(
        "id" => $item->id_parameter_jenis_klinik,
        "text" => $item->name_parameter_jenis_klinik . ' - ' . $item->code_parameter_jenis_klinik
      );
    }

    return response()->json($response);
  }
}
