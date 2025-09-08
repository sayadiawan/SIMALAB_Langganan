<?php

namespace Smt\Masterweb\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smt\Masterweb\Models\Pasien;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;

class AdmPasienController extends Controller
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
    if (request()->ajax()) {
      $datas = Pasien::latest()->get();

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['nik_pasien']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['nama_pasien']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          $urlShow = '';
          $urlSunting = '';
          $urlHapus = '';

          if (getAction('read')) {
            $urlShow = '<a class="dropdown-item" href="' . route('elits-pasien.show', $data->id_pasien) . '">Detail</a>';
          }

          if (getAction('update')) {
            $urlSunting = '<a class="dropdown-item" href="' . route('elits-pasien.edit', $data->id_pasien) . '">Edit</a>';
          }

          if (getAction('delete')) {
            $urlHapus = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id_pasien . '" data-nama="' . $data->nama_pasien . '" title="Hapus">Hapus</a> ';
          }

          $buttonAksi = '
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Aksi
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              ' . $urlShow . '
              ' . $urlSunting . '
              ' . $urlHapus . '
            </div>
          </div>';

          return $buttonAksi;
        })
        ->rawColumns(['action'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    return view('masterweb::module.admin.pasien.list');
  }

  public function dataPasienDatatables(Request $request)
  {
    $datas = Pasien::latest()->get();

    return Datatables::of($datas)
      ->filter(function ($instance) use ($request) {
        if (!empty($request->get('search'))) {
          $instance->collection = $instance->collection->filter(function ($row) use ($request) {
            if (Str::contains(Str::lower($row['nik_pasien']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['nama_pasien']), Str::lower($request->get('search')))) {
              return true;
            }

            return false;
          });
        }
      })
      ->addColumn('action', function ($data) {
        $urlShow = '';
        $urlSunting = '';
        $urlHapus = '';

        if (getAction('read')) {
          $urlShow = '<a class="dropdown-item" href="' . route('elits-pasien.show', $data->id_pasien) . '">Detail</a>';
        }

        if (getAction('update')) {
          $urlSunting = '<a class="dropdown-item" href="' . route('elits-pasien.edit', $data->id_pasien) . '">Sunting</a>';
        }

        if (getAction('delete')) {
          $urlHapus = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id_pasien . '" data-nama="' . $data->nama_pasien . '" title="Hapus">Hapus</a> ';
        }

        $buttonAksi = '
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Aksi
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              ' . $urlShow . '
              ' . $urlSunting . '
              ' . $urlHapus . '
            </div>
          </div>';

        return $buttonAksi;
      })
      ->rawColumns(['action'])
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
    return view('masterweb::module.admin.pasien.add');
  }

  public function rules($request)
  {
    $rule = [
      'nama_pasien' => 'required|string|min:3|max:255',
      'tgllahir_pasien' => 'required',
      'phone_pasien' => 'required',
    ];

    $pesan = [
      'nama_pasien.required' => 'Nama pasien tidak boleh kosong!',
      'tgllahir_pasien.required' => 'Tanggal lahir pasien tidak boleh kosong!',
      'phone_pasien.required' => 'Nomor telepon tidak boleh kosong!',
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
      // check jika sudah ada parameter jenis dan stuan di baku mutu
      $check = Pasien::where('nik_pasien', $request->post('nik_pasien'))
        ->first();

      if ($check) {
        return response()->json(['status' => false, 'pesan' => "NIK pasien sudah pernah diinput, silahkan input NIK yang berbeda!"], 200);
      } else {
        DB::beginTransaction();

        try {
          // get no rekam medis
          $count = Pasien::max('nourut_pasien');

          if ($count < 1) {
            $set_count =  1;
          } else {
            $set_count = $count + 1;
          }

          $post = new Pasien();
          $post->nik_pasien = $request->post('nik_pasien');
          $post->nourut_pasien = $set_count;
          $post->no_rekammedis_pasien = $set_count;
          // $post->no_rekammedis_pasien = $request->no_rekammedis_pasien;
          $post->nama_pasien = $request->post('nama_pasien');
          $post->gender_pasien = $request->post('gender_pasien');
          $post->tgllahir_pasien = Carbon::createFromFormat('d/m/Y', $request->tgllahir_pasien)->format('Y-m-d');

          $post->alamat_pasien = $request->post('alamat_pasien');
          $post->phone_pasien = $request->post('phone_pasien');
          $post->divisi_instansi_pasien = $request->post('divisi_instansi_pasien');

          $simpan = $post->save();

          DB::commit();

          if ($simpan == true) {
            return response()->json(['status' => true, 'pesan' => "Data pasien berhasil disimpan!", 'id_pasien' => $post->id_pasien], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data pasien tidak berhasil disimpan!"], 200);
          }
        } catch (\Exception $e) {
          DB::rollback();

          return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
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
    $item = Pasien::findOrFail($id);

    return view('masterweb::module.admin.pasien.show', compact('item'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $item = Pasien::findOrFail($id);

    return view('masterweb::module.admin.pasien.edit', compact('item'));
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

      // check pasien
      $post = Pasien::find($id);

      // memastikan jika data yang ada di table sama dengan yang ada diinputan maka tidak ada validasi
      // validasi berjalan jika data yang ada di table tidak sama dengan yang ada diinputan
      if ($post->nik_pasien == $request->nik_pasien) {

        DB::beginTransaction();

        try {
          $post->nik_pasien = $request->post('nik_pasien');
          $post->nama_pasien = $request->post('nama_pasien');
          $post->no_rekammedis_pasien = $request->no_rekammedis_pasien;
          $post->gender_pasien = $request->post('gender_pasien');
          $post->tgllahir_pasien = Carbon::createFromFormat('d/m/Y', $request->tgllahir_pasien)->format('Y-m-d');

          // dipindahkan ke table permohonan uji klinik
          /* $post->umurtahun_pasien = $request->post('umurtahun_pasien');
          $post->umurbulan_pasien = $request->post('umurbulan_pasien');
          $post->umurhari_pasien = $request->post('umurhari_pasien'); */

          $post->alamat_pasien = $request->post('alamat_pasien');
          $post->phone_pasien = $request->post('phone_pasien');
          $post->divisi_instansi_pasien = $request->post('divisi_instansi_pasien');

          $simpan = $post->save();

          DB::commit();

          if ($request->permohonanujiklinik_id != null) {
            $urlBack = route('elits-permohonan-uji-klinik.edit', $request->permohonanujiklinik_id);

            // get age
            $birthDate = Carbon::parse($post->tgllahir_pasien)->diff(Carbon::now())->format('%y-%m-%d');
            $birthDateExplode = explode('-', $birthDate);

            // update permohonan uji klinik
            $post_puk = PermohonanUjiKlinik2::findOrFail($request->permohonanujiklinik_id);
            $post_puk->umurtahun_pasien_permohonan_uji_klinik = $birthDateExplode[0];
            $post_puk->umurbulan_pasien_permohonan_uji_klinik = $birthDateExplode[1];
            $post_puk->umurhari_pasien_permohonan_uji_klinik = $birthDateExplode[2];

            $post_puk->save();
          } else {
            $urlBack = '/elits-pasien';
          }

          if ($simpan == true) {
            return response()->json(['status' => true, 'pesan' => "Data pasien berhasil diubah!", 'url_back' => $urlBack], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data pasien tidak berhasil diubah!"], 200);
          }
        } catch (\Exception $e) {
          DB::rollback();

          return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
        }
      } else {
        if($request->nik_pasien!="-"){
          $check = Pasien::where('nik_pasien', $request->nik_pasien)
          ->first();
          if ($check) {
            return response()->json(['status' => false, 'pesan' => "NIK sudah pernah dibuat silahkan NIK yang berbeda!"], 200);
          } else {
            DB::beginTransaction();

            try {
              $post->nik_pasien = $request->post('nik_pasien');
              $post->nama_pasien = $request->post('nama_pasien');
              $post->gender_pasien = $request->post('gender_pasien');
              $post->tgllahir_pasien = Carbon::createFromFormat('d/m/Y', $request->tgllahir_pasien)->format('Y-m-d');

              // dipindahkan ke table permohonan uji klinik
              /* $post->umurtahun_pasien = $request->post('umurtahun_pasien');
              $post->umurbulan_pasien = $request->post('umurbulan_pasien');
              $post->umurhari_pasien = $request->post('umurhari_pasien'); */

              $post->alamat_pasien = $request->post('alamat_pasien');
              $post->phone_pasien = $request->post('phone_pasien');

              $simpan = $post->save();

              DB::commit();

              if ($request->permohonanujiklinik_id != null) {
                $urlBack = route('elits-permohonan-uji-klinik.edit', $request->permohonanujiklinik_id);

                // get age
                $birthDate = Carbon::parse($post->tgllahir_pasien)->diff(Carbon::now())->format('%y-%m-%d');
                $birthDateExplode = explode('-', $birthDate);

                // update permohonan uji klinik
                $post_puk = PermohonanUjiKlinik2::findOrFail($request->permohonanujiklinik_id);
                $post_puk->umurtahun_pasien_permohonan_uji_klinik = $birthDateExplode[0];
                $post_puk->umurbulan_pasien_permohonan_uji_klinik = $birthDateExplode[1];
                $post_puk->umurhari_pasien_permohonan_uji_klinik = $birthDateExplode[2];

                $post_puk->save();
              } else {
                $urlBack = '/elits-pasien';
              }

              if ($simpan = true) {
                return response()->json(['status' => true, 'pesan' => "Data pasien berhasil diubah!", 'url_back' => $urlBack], 200);
              } else {
                return response()->json(['status' => false, 'pesan' => "Data pasien tidak berhasil diubah!"], 200);
              }
            } catch (\Exception $e) {
              DB::rollback();

              return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
            }
          }
        }else{
          DB::beginTransaction();

            try {
              $post->nik_pasien = $request->post('nik_pasien');
              $post->nama_pasien = $request->post('nama_pasien');
              $post->gender_pasien = $request->post('gender_pasien');
              $post->tgllahir_pasien = Carbon::createFromFormat('d/m/Y', $request->tgllahir_pasien)->format('Y-m-d');

              // dipindahkan ke table permohonan uji klinik
              /* $post->umurtahun_pasien = $request->post('umurtahun_pasien');
              $post->umurbulan_pasien = $request->post('umurbulan_pasien');
              $post->umurhari_pasien = $request->post('umurhari_pasien'); */

              $post->alamat_pasien = $request->post('alamat_pasien');
              $post->phone_pasien = $request->post('phone_pasien');

              $simpan = $post->save();

              DB::commit();

              if ($request->permohonanujiklinik_id != null) {
                $urlBack = route('elits-permohonan-uji-klinik.edit', $request->permohonanujiklinik_id);

                // get age
                $birthDate = Carbon::parse($post->tgllahir_pasien)->diff(Carbon::now())->format('%y-%m-%d');
                $birthDateExplode = explode('-', $birthDate);

                // update permohonan uji klinik
                $post_puk = PermohonanUjiKlinik2::findOrFail($request->permohonanujiklinik_id);
                $post_puk->umurtahun_pasien_permohonan_uji_klinik = $birthDateExplode[0];
                $post_puk->umurbulan_pasien_permohonan_uji_klinik = $birthDateExplode[1];
                $post_puk->umurhari_pasien_permohonan_uji_klinik = $birthDateExplode[2];

                $post_puk->save();
              } else {
                $urlBack = '/elits-pasien';
              }

              if ($simpan = true) {
                return response()->json(['status' => true, 'pesan' => "Data pasien berhasil diubah!", 'url_back' => $urlBack], 200);
              } else {
                return response()->json(['status' => false, 'pesan' => "Data pasien tidak berhasil diubah!"], 200);
              }
            } catch (\Exception $e) {
              DB::rollback();

              return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!'], 200);
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
    $hapus = Pasien::where('id_pasien', $id)->delete();

    $hapus_permohonan_uji_klinik = PermohonanUjiKlinik2::where('pasien_permohonan_uji_klinik', $id)->delete();


    if ($hapus == true && $hapus_permohonan_uji_klinik) {
      return response()->json(['status' => true, 'pesan' => "Data pasien berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data pasien tidak berhasil dihapus!"], 200);
    }
  }

  public function getPasienBySelect(Request $request)
  {
    $search = $request->search;

    if (isset($request->search)) {
      $data = Pasien::orderby('nama_pasien', 'asc')
        ->select('id_pasien', 'nik_pasien', 'nama_pasien', 'tgllahir_pasien', 'no_rekammedis_pasien')
        ->where('nama_pasien', 'like', '%' . $search . '%')
        ->orwhere('nik_pasien', 'like', '%' . $search . '%')
        ->orWhere('tgllahir_pasien', 'like', '%' . $search . '%')
        ->orWhere('no_rekammedis_pasien', 'like', '%' . $search . '%')
        ->limit(10)
        ->get();
    } else {
      $data = Pasien::orderby('nama_pasien', 'asc')
        ->select('id_pasien', 'nik_pasien', 'nama_pasien', 'tgllahir_pasien', 'no_rekammedis_pasien')
        ->limit(10)
        ->get();
    }

    // dd($data);

    $response = array();
    foreach ($data as $item) {
      $response[] = array(
        "id" => $item->id_pasien,
        "text" => $item->nama_pasien . ' - ' . $item->nik_pasien . ' - ' . $item->no_rekammedis_pasien . ' - ' . '(' . $item->tgllahir_pasien . ')',
      );
    }

    return response()->json($response);
  }

  public function getPasienByID(Request $request)
  {
    $item = Pasien::findOrFail($request->pasien_id);

    $response = [
      'id_pasien_satu_sehat' => $item->id_pasien_satu_sehat,
      'nik_pasien' => $item->nik_pasien,
      'no_rekammedis_pasien' => str_pad((int)($item->no_rekammedis_pasien), 4, '0', STR_PAD_LEFT),
      'nama_pasien' => $item->nama_pasien,
      'gender_pasien' => $item->gender_pasien == "L" ? 'Laki-laki' : 'Perempuan',
      'tgllahir_pasien' => $item->tgllahir_pasien != null ? Carbon::createFromFormat('Y-m-d', $item->tgllahir_pasien)->isoFormat('dddd, D MMMM Y') : '',
      'tgllahir_pasien_normal' => $item->tgllahir_pasien != null ? Carbon::createFromFormat('Y-m-d', $item->tgllahir_pasien)->format('d/m/Y') : '',
      'alamat_pasien' => $item->alamat_pasien,
      'phone_pasien' => $item->phone_pasien,
      'divisi_instansi_pasien' => $item->divisi_instansi_pasien
    ];

    return response()->json($response);
  }
}