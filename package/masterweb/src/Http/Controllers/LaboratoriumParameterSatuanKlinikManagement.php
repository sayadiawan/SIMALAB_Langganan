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
use Smt\Masterweb\Models\ParameterSatuanKlinik;
use Smt\Masterweb\Models\ParameterSubSatuanKlinik;

class LaboratoriumParameterSatuanKlinikManagement extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function rules($request)
  {
    $rule = [
      'parameter_jenis_klinik' => 'required',
      'name_parameter_satuan_klinik' => 'required|max:256',
      // 'metode_parameter_satuan_klinik' => 'required|max:256',
      'jenis_pemeriksaan_parameter_satuan_klinik' => 'required',
      'harga_satuan_parameter_satuan_klinik' => 'required|numeric'
    ];

    $pesan = [
      'parameter_jenis_klinik.required' => 'Parameter jenis klinik tidak boleh kosong!',
      'name_parameter_satuan_klinik.required' => 'Nama parameter satuan klinik tidak boleh kosong!',
      // 'metode_parameter_satuan_klinik.required' => 'Metode parameter satuan klinik tidak boleh kosong!',
      'jenis_pemeriksaan_parameter_satuan_klinik.required' => 'Jenis parameter pemeriksaan klinik tidak boleh kosong!',
      'harga_satuan_parameter_satuan_klinik.required' => 'Harga parameter satuan klinik tidak boleh kosong!',
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
    // #1
    /* $user = Auth()->user();
        $level = $user->getlevel->level;

        if ($level == "elits-dev" || $level == "admin") {
            $data = ParameterSatuanKlinik::orderBy('created_at', 'desc')
                ->whereHas('parameterjenisklinik', function ($query) {
                    return $query->whereNull('deleted_at');
                })
                ->get();

            return view('masterweb::module.admin.laboratorium.parameter-satuan-klinik.list', compact('data'));
        } else {
            return abort(404);
        } */

    // #2
    $data = ParameterSatuanKlinik::orderBy('created_at', 'desc')
      ->whereHas('parameterjenisklinik', function ($query) {
        return $query->whereNull('deleted_at');
      })
      ->get();

    return view('masterweb::module.admin.laboratorium.parameter-satuan-klinik.list', compact('data'));
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

    return view('masterweb::module.admin.laboratorium.parameter-satuan-klinik.add');
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

    // dd($request->all());
    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      // dd($request->name_parameter_satuan_klinik);
      // check apakah ada kesamaan data parameter jenis dan satuan yang akan diinput lagi
      $check = ParameterSatuanKlinik::where('name_parameter_satuan_klinik', $request->post('name_parameter_satuan_klinik'))
        ->where('parameter_jenis_klinik', $request->post('parameter_jenis_klinik'))
        ->first();

      if (isset($check)) {
        if ($check->name_parameter_satuan_klinik == $request->post('name_parameter_satuan_klinik')) {
          return response()->json(['status' => false, 'pesan' => "Nama parameter satuan pada jenis tersebut sudah pernah dibuat, silahkan coba dengan nama yang berbeda atau jenisnya!"], 200);
        } else {
          DB::beginTransaction();

          try {
            $post = new ParameterSatuanKlinik();
            $post->parameter_jenis_klinik = $request->post('parameter_jenis_klinik');
            $post->name_parameter_satuan_klinik = $request->name_parameter_satuan_klinik;
            $post->metode_parameter_satuan_klinik = $request->metode_parameter_satuan_klinik;
            $post->loinc_parameter_satuan_klinik = $request->loinc_parameter_satuan_klinik;
            $post->ket_default_parameter_satuan_klinik = rubahNilaikeHtml($request->ket_default_parameter_satuan_klinik);
            $post->jenis_pemeriksaan_parameter_satuan_klinik = $request->post('jenis_pemeriksaan_parameter_satuan_klinik');
            $post->is_sub_parameter_satuan_klinik = $request->post('is_sub_parameter_satuan_klinik');
            $post->harga_satuan_parameter_satuan_klinik = $request->post('harga_satuan_parameter_satuan_klinik');
            $post->sort_parameter_satuan_klinik = $request->post('sort_parameter_satuan_klinik');

            $simpan = $post->save();

            if (($request->is_sub_parameter_satuan_klinik == 1) && isset($request->name_parameter_sub_satuan_klinik)) {
              $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

              for ($i = 1; $i <= $count_sub_parameter; $i++) {
                $post_sub = new ParameterSubSatuanKlinik();
                $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
                $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

                $simpan_sub = $post_sub->save();
              }
            }

            DB::commit();

            if ($simpan == true) {
              return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil disimpan!"], 200);
            } else {
              return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil disimpan!"], 200);
            }
          } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'pesan' => $e], 200);
          }
        }
      } else {
        DB::beginTransaction();

        try {
          $post = new ParameterSatuanKlinik();
          $post->parameter_jenis_klinik = $request->post('parameter_jenis_klinik');
          $post->name_parameter_satuan_klinik = $request->name_parameter_satuan_klinik;
          $post->metode_parameter_satuan_klinik = $request->metode_parameter_satuan_klinik;
          $post->loinc_parameter_satuan_klinik = $request->loinc_parameter_satuan_klinik;
          $post->ket_default_parameter_satuan_klinik = $request->ket_default_parameter_satuan_klinik;
          $post->jenis_pemeriksaan_parameter_satuan_klinik = $request->post('jenis_pemeriksaan_parameter_satuan_klinik');
          $post->is_sub_parameter_satuan_klinik = $request->post('is_sub_parameter_satuan_klinik');
          $post->harga_satuan_parameter_satuan_klinik = $request->post('harga_satuan_parameter_satuan_klinik');
          $post->sort_parameter_satuan_klinik = $request->post('sort_parameter_satuan_klinik');

          $simpan = $post->save();

          if (($request->is_sub_parameter_satuan_klinik == 1) && isset($request->name_parameter_sub_satuan_klinik)) {
            $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

            for ($i = 1; $i <= $count_sub_parameter; $i++) {
              $post_sub = new ParameterSubSatuanKlinik();
              $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
              $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

              $simpan_sub = $post_sub->save();
            }
          }

          DB::commit();

          if ($simpan == true) {
            return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil disimpan!"], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil disimpan!"], 200);
          }
        } catch (\Exception $e) {
          DB::rollback();

          return response()->json(['status' => false, 'pesan' => $e], 200);
        }
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
    $user = Auth()->user();
    $level = $user->getlevel->level;
    $item = ParameterSatuanKlinik::find($id);

    $data_subitem = [];

    if ($item->is_sub_parameter_satuan_klinik == '1') {
      $data_subitem = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)
        ->whereNull('deleted_at')
        ->get();
    }

    return view('masterweb::module.admin.laboratorium.parameter-satuan-klinik.show', compact('item', 'data_subitem'));
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
    $item = ParameterSatuanKlinik::find($id);

    $data_subitem = [];

    if ($item->is_sub_parameter_satuan_klinik == '1') {
      $data_subitem = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)
        ->whereNull('deleted_at')
        ->get();
    }

    $existing_sorts = ParameterSatuanKlinik::with('parameterjenisklinik')->orderBy('sort_parameter_satuan_klinik')
    // ->pluck('name_parameter_satuan_klinik', 'sort_parameter_satuan_klinik');
    ->get();
    // dd($existing_sorts);

    return view('masterweb::module.admin.laboratorium.parameter-satuan-klinik.edit', compact('item', 'data_subitem', 'existing_sorts'));
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
      // dd($request);
      $post = ParameterSatuanKlinik::find($id);

      // pastikan antara data yang sudah disimpan sama dengan data yang diupdate
      // jika sama maka tidak ada validasi
      // jika berbeda maka akan ada validasi lanjutan untuk mengecek apakah data jenis dan satuan berbeda atau sama
      if ($post->parameter_jenis_klinik == $request->post('parameter_jenis_klinik') && $post->name_parameter_satuan_klinik == $request->post('name_parameter_satuan_klinik')) {
        DB::beginTransaction();

        try {
          $post->parameter_jenis_klinik = $request->post('parameter_jenis_klinik');
          $post->name_parameter_satuan_klinik = $request->post('name_parameter_satuan_klinik');
          $post->metode_parameter_satuan_klinik = $request->post('metode_parameter_satuan_klinik');
          $post->ket_default_parameter_satuan_klinik = rubahNilaikeHtml($request->ket_default_parameter_satuan_klinik);
          $post->loinc_parameter_satuan_klinik = $request->loinc_parameter_satuan_klinik;
          $post->jenis_pemeriksaan_parameter_satuan_klinik = $request->post('jenis_pemeriksaan_parameter_satuan_klinik');
          $post->is_sub_parameter_satuan_klinik = $request->post('is_sub_parameter_satuan_klinik');
          $post->harga_satuan_parameter_satuan_klinik = $request->post('harga_satuan_parameter_satuan_klinik');

          // Simpan nomor urut yang lama
          $old_sort = $post->sort_parameter_satuan_klinik;

          // DD($old_sort);

          // Ambil nomor urut setelah yang dipilih di dropdown
          $after_sort = $request->post('after_sort_parameter_satuan_klinik');

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
              ParameterSatuanKlinik::where('sort_parameter_satuan_klinik', '>=', $new_sort)
                  ->increment('sort_parameter_satuan_klinik');
          } else {
              // Jika nomor urut diubah
              if ($old_sort != $new_sort) {
                  if ($new_sort > $old_sort) {
                      // Jika pindah ke bawah, kurangi urutan item antara old_sort dan new_sort
                      ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$old_sort + 1, $new_sort])
                          ->decrement('sort_parameter_satuan_klinik');
                  } else {
                      // Jika pindah ke atas, tambahkan urutan item antara new_sort dan old_sort
                      ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$new_sort, $old_sort - 1])
                          ->increment('sort_parameter_satuan_klinik');
                  }
              }
          }

          // Update urutan item yang sedang diedit
          $post->sort_parameter_satuan_klinik = $new_sort;

          $simpan = $post->save();

          if ($request->is_sub_parameter_satuan_klinik == 1  && isset($request->name_parameter_sub_satuan_klinik)) {
            $check_subitem = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->get();

            if (count($check_subitem) > 0) {
              ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->delete();

              $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

              for ($i = 1; $i <= $count_sub_parameter; $i++) {
                $post_sub = new ParameterSubSatuanKlinik();
                $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
                $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

                $post_sub->save();
              }
            } else {
              $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

              for ($i = 1; $i <= $count_sub_parameter; $i++) {
                $post_sub = new ParameterSubSatuanKlinik();
                $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
                $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

                $post_sub->save();
              }
            }
          } else {
            $check_subitem = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->get();

            if (count($check_subitem) > 0) {
              ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->delete();
            }
          }

          DB::commit();

          if ($simpan == true) {
            return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil diubah!"], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil diubah!"], 200);
          }
        } catch (\Exception $e) {
          DB::rollback();

          return response()->json(['status' => false, 'pesan' => $e], 200);
        }
      } else {
        $check = ParameterSatuanKlinik::where('name_parameter_satuan_klinik', '=', $request->post('name_parameter_satuan_klinik'))
          ->where('parameter_jenis_klinik', $request->post('parameter_jenis_klinik'))
          ->first();

        if (isset($check)) {
          if ($check->name_parameter_satuan_klinik == $request->post('name_parameter_satuan_klinik')) {
            return response()->json(['status' => false, 'pesan' => "Nama parameter satuan pada jenis tersebut sudah pernah dibuat, silahkan coba dengan nama yang berbeda atau jenisnya!"], 200);
          } else {
            DB::beginTransaction();

            try {
              $post->parameter_jenis_klinik = $request->post('parameter_jenis_klinik');
              $post->name_parameter_satuan_klinik = $request->post('name_parameter_satuan_klinik');
              $post->metode_parameter_satuan_klinik = $request->post('metode_parameter_satuan_klinik');
              $post->ket_default_parameter_satuan_klinik = $request->ket_default_parameter_satuan_klinik;
              $post->loinc_parameter_satuan_klinik = $request->loinc_parameter_satuan_klinik;
              $post->jenis_pemeriksaan_parameter_satuan_klinik = $request->post('jenis_pemeriksaan_parameter_satuan_klinik');
              $post->is_sub_parameter_satuan_klinik = $request->post('is_sub_parameter_satuan_klinik');
              $post->harga_satuan_parameter_satuan_klinik = $request->post('harga_satuan_parameter_satuan_klinik');

              // Simpan nomor urut yang lama
              $old_sort = $post->sort_parameter_satuan_klinik;

              // Ambil nomor urut setelah yang dipilih di dropdown
              $after_sort = $request->post('after_sort_parameter_satuan_klinik');

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
                  ParameterSatuanKlinik::where('sort_parameter_satuan_klinik', '>=', $new_sort)
                      ->increment('sort_parameter_satuan_klinik');
              } else {
                  // Jika nomor urut diubah
                  if ($old_sort != $new_sort) {
                      if ($new_sort > $old_sort) {
                          // Jika pindah ke bawah, kurangi urutan item antara old_sort dan new_sort
                          ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$old_sort + 1, $new_sort])
                              ->decrement('sort_parameter_satuan_klinik');
                      } else {
                          // Jika pindah ke atas, tambahkan urutan item antara new_sort dan old_sort
                          ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$new_sort, $old_sort - 1])
                              ->increment('sort_parameter_satuan_klinik');
                      }
                  }
              }

              // Update urutan item yang sedang diedit
              $post->sort_parameter_satuan_klinik = $new_sort;

              $simpan = $post->save();

              if (($request->is_sub_parameter_satuan_klinik == 1) && isset($request->name_parameter_sub_satuan_klinik)) {
                $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

                for ($i = 1; $i <= $count_sub_parameter; $i++) {
                  $post_sub = new ParameterSubSatuanKlinik();
                  $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
                  $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

                  $simpan_sub = $post_sub->save();
                }
              }

              DB::commit();

              if ($simpan == true) {
                return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil disimpan!"], 200);
              } else {
                return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil disimpan!"], 200);
              }
            } catch (\Exception $e) {
              DB::rollback();

              return response()->json(['status' => false, 'pesan' => $e], 200);
            }
          }
        } else {
          DB::beginTransaction();

          try {
            $post->parameter_jenis_klinik = $request->post('parameter_jenis_klinik');
            $post->name_parameter_satuan_klinik = $request->post('name_parameter_satuan_klinik');
            $post->metode_parameter_satuan_klinik = $request->post('metode_parameter_satuan_klinik');
            $post->ket_default_parameter_satuan_klinik = $request->ket_default_parameter_satuan_klinik;
            $post->loinc_parameter_satuan_klinik = $request->loinc_parameter_satuan_klinik;
            $post->jenis_pemeriksaan_parameter_satuan_klinik = $request->post('jenis_pemeriksaan_parameter_satuan_klinik');
            $post->is_sub_parameter_satuan_klinik = $request->post('is_sub_parameter_satuan_klinik');
            $post->harga_satuan_parameter_satuan_klinik = $request->post('harga_satuan_parameter_satuan_klinik');

            // Simpan nomor urut yang lama
            $old_sort = $post->sort_parameter_satuan_klinik;

            // Ambil nomor urut setelah yang dipilih di dropdown
            $after_sort = $request->post('after_sort_parameter_satuan_klinik');

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
              ParameterSatuanKlinik::where('sort_parameter_satuan_klinik', '>=', $new_sort)
              ->increment('sort_parameter_satuan_klinik');
            } else {
              // Jika nomor urut diubah
              if ($old_sort != $new_sort) {
                if ($new_sort > $old_sort) {
                  // Jika pindah ke bawah, kurangi urutan item antara old_sort dan new_sort
                  ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$old_sort + 1, $new_sort])
                  ->decrement('sort_parameter_satuan_klinik');
                } else {
                  // Jika pindah ke atas, tambahkan urutan item antara new_sort dan old_sort
                  ParameterSatuanKlinik::whereBetween('sort_parameter_satuan_klinik', [$new_sort, $old_sort - 1])
                  ->increment('sort_parameter_satuan_klinik');
                    }
                  }
                }

            // Update urutan item yang sedang diedit
            $post->sort_parameter_satuan_klinik = $new_sort;

            $simpan = $post->save();

            if (($request->is_sub_parameter_satuan_klinik == 1) && isset($request->name_parameter_sub_satuan_klinik)) {
              $count_sub_parameter = count($request->name_parameter_sub_satuan_klinik);

              for ($i = 1; $i <= $count_sub_parameter; $i++) {
                $post_sub = new ParameterSubSatuanKlinik();
                $post_sub->parameter_satuan_klinik = $post->id_parameter_satuan_klinik;
                $post_sub->name_parameter_sub_satuan_klinik = $request->name_parameter_sub_satuan_klinik[$i];

                $simpan_sub = $post_sub->save();
              }
            }

            DB::commit();

            if ($simpan == true) {
              return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil disimpan!"], 200);
            } else {
              return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil disimpan!"], 200);
            }
          } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['status' => false, 'pesan' => $e], 200);
          }
        }
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
    $hapus = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $id)->delete();

    $check_subitem = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->get();

    if (count($check_subitem) > 0) {
      ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $id)->delete();
    }

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data parameter satuan berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data parameter satuan tidak berhasil dihapus!"], 200);
    }
  }

  public function getParameterSatuanKlinik(Request $request)
  {
    $search = $request->search;

    if (isset($request->param)) {
      /* $data = ParameterSatuanKlinik::orderby('name_parameter_satuan_klinik', 'asc')->select('id_parameter_satuan_klinik', 'name_parameter_satuan_klinik')->where('parameter_jenis_klinik', $request->param)->limit(20)->get(); */

      if ($search == '' || $search == null) {
        $data = ParameterSatuanKlinik::orderby('name_parameter_satuan_klinik', 'asc')
          ->select('id_parameter_satuan_klinik', 'name_parameter_satuan_klinik')
          ->where('parameter_jenis_klinik', $request->param)
          ->limit(20)
          ->get();
      } else {
        $data = ParameterSatuanKlinik::orderby('name_parameter_satuan_klinik', 'asc')
          ->select('id_parameter_satuan_klinik', 'name_parameter_satuan_klinik')
          ->where('parameter_jenis_klinik', $request->param)
          ->where('name_parameter_satuan_klinik', 'like', '%' . $search . '%')
          ->limit(20)
          ->get();
      }
    } else {
      if ($search == '' || $search == null) {
        $data = ParameterSatuanKlinik::orderby('name_parameter_satuan_klinik', 'asc')->select('id_parameter_satuan_klinik', 'name_parameter_satuan_klinik')->limit(20)->get();
      } else {
        $data = ParameterSatuanKlinik::orderby('name_parameter_satuan_klinik', 'asc')->select('id_parameter_satuan_klinik', 'name_parameter_satuan_klinik')->where('name_parameter_satuan_klinik', 'like', '%' . $search . '%')->limit(20)->get();
      }
    }

    $response = array();
    foreach ($data as $item) {
      $response[] = array(
        "id" => $item->id_parameter_satuan_klinik,
        "text" => $item->name_parameter_satuan_klinik
      );
    }

    return response()->json($response);
  }
}
