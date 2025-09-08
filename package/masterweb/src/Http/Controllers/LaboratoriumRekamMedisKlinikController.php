<?php

namespace Smt\Masterweb\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smt\Masterweb\Models\Pasien;
use Yajra\DataTables\DataTables;

use Illuminate\Support\Facades\DB;
use Smt\Masterweb\Models\BakuMutu;
use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\PermohonanUji;
use Illuminate\Support\Facades\Validator;
use Smt\Masterweb\Models\PermohonanUjiKlinik;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;
use Smt\Masterweb\Models\ParameterJenisKlinik;
use Smt\Masterweb\Models\ParameterSatuanKlinik;
use Smt\Masterweb\Models\PermohonanUjiPaketKlinik;
use Smt\Masterweb\Models\PermohonanUjiAnalisKlinik;
use Smt\Masterweb\Models\PermohonanUjiParameterKlinik;
use Smt\Masterweb\Models\BakuMutuDetailParameterKlinik;
use Smt\Masterweb\Models\PermohonanUjiSubParameterKlinik;

class LaboratoriumRekamMedisKlinikController extends Controller
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
      $datas = PermohonanUjiKlinik2::with('pasien')
        ->select(DB::raw("pasien_permohonan_uji_klinik, count(*) as count"))
        ->groupBy('pasien_permohonan_uji_klinik')
        ->latest()
        ->get();


      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['pasien']['nama_pasien']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['pasien']['nik_pasien']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          $urlShow = '';
          $urlHapus = '';

          if (getAction('read')) {
            $urlShow = '<a class="dropdown-item" href="' . route('elits-rekam-medis-show', $data->pasien->id_pasien) . '">Detail</a>';
          }

          if (getAction('delete')) {
            $urlHapus = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->pasien->id_pasien . '" data-nama="' . $data->pasien->nama_pasien . '" title="Hapus">Hapus</a> ';
          }

          $buttonAksi = '
          <div class="btn-group" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              Aksi
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
              ' . $urlShow . '
              ' . $urlHapus . '
            </div>
          </div>';

          return $buttonAksi;
        })
        ->rawColumns(['action'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    return view('masterweb::module.admin.laboratorium.rekam-medis-klinik.list');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('masterweb::module.admin.laboratorium.rekam-medis-klinik.add');
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $id)
  {
    if (request()->ajax()) {
      $datas = PermohonanUjiKlinik2::where('pasien_permohonan_uji_klinik', $id)->latest()
        ->get();

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['no_register']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['no_pasien']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('action', function ($data) {
          $urlShow = '';
          $urlHapus = '';

          if (getAction('read')) {
            $urlShow = '<a class="dropdown-item" href="' . url('elits-rekam-medis-detail-hasil', $data->id_permohonan_uji_klinik) . '">Lihat Hasil</a>';
          }

          if (getAction('delete')) {
            $urlHapus = '<a class="dropdown-item btn-hapus" href="javascript:void(0)" data-id="' . $data->id_permohonan_uji_klinik . '" data-nama="' . $data->noregister_permohonan_uji_klinik . '" title="Hapus">Hapus</a> ';
          }

          $buttonAksi = '<div class="dropdown show m-1">
                    <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Aksi
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        ' . $urlShow . '

                        ' . $urlHapus . '
                    </div>
                </div>';

          // BUTTON PRINT
          $printHasilKlinik = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Hasil Klinik">Print Hasil Klinik</a> ';

          $data_permohonan_uji_parameter_klinik_rapid_antibody = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $data->id_permohonan_uji_klinik)
            ->whereHas('parametersatuanklinik', function ($query) {
              return $query->orderBy('name_parameter_satuan_klinik', 'asc')
                ->where('jenis_pemeriksaan_parameter_satuan_klinik', '2')
                ->whereNull('deleted_at');
            })
            ->get();

          if (count($data_permohonan_uji_parameter_klinik_rapid_antibody) > 0) {
            $printHasilRapidAntibody = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-rapid-antibody', $data->id_permohonan_uji_klinik) . '" title="Print Hasil Rapid Antibody" target="__blank">Print Hasil Rapid Antibody</a> ';
          } else {
            $printHasilRapidAntibody = '';
          }

          $data_permohonan_uji_parameter_klinik_rapid_antigen = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $data->id_permohonan_uji_klinik)
            ->whereHas('parametersatuanklinik', function ($query) {
              return $query->orderBy('name_parameter_satuan_klinik', 'asc')
                ->where('jenis_pemeriksaan_parameter_satuan_klinik', '3')
                ->whereNull('deleted_at');
            })
            ->get();

          if (count($data_permohonan_uji_parameter_klinik_rapid_antigen) > 0) {
            $printHasilRapidAntigen = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-rapid-antigen', $data->id_permohonan_uji_klinik) . '" title="Print Hasil Rapid Antigen" target="__blank">Print Hasil Rapid Antigen</a> ';
          } else {
            $printHasilRapidAntigen = '';
          }

          $data_permohonan_uji_parameter_klinik_pcr = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $data->id_permohonan_uji_klinik)
            ->whereHas('parametersatuanklinik', function ($query) {
              return $query->orderBy('name_parameter_satuan_klinik', 'asc')
                ->where('jenis_pemeriksaan_parameter_satuan_klinik', '1')
                ->whereNull('deleted_at');
            })
            ->get();

          if (count($data_permohonan_uji_parameter_klinik_pcr) > 0) {
            $printHasilPcr = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil-pcr', $data->id_permohonan_uji_klinik) . '" title="Print Hasil PCR" target="__blank">Print Hasil PCR</a> ';
          } else {
            $printHasilPcr = '';
          }

          $printHasilQrcode = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-qrcode', $data->id_permohonan_uji_klinik) . '" title="Print QR Code" target="__blank">Print QR Code</a> ';

          $buttonPrint = '<div class="dropdown show m-1">
                            <a class="btn btn-fw btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLinkPrint" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Print
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                ' . $printHasilKlinik . '

                                ' . $printHasilRapidAntibody . '

                                ' . $printHasilRapidAntigen . '

                                ' . $printHasilPcr . '

                                ' . $printHasilQrcode . '
                            </div>
                        </div>';

          return $buttonAksi . ' ' . $buttonPrint;
        })
        ->addColumn('tgl_register', function ($data) {
          return isset($data->tglregister_permohonan_uji_klinik) ? Carbon::createFromFormat('Y-m-d', $data->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y') : '';
        })
        ->addColumn('umur_pasien', function ($data) {
          return $data->umurtahun_pasien_permohonan_uji_klinik . 'tahun ' . $data->umurbulan_pasien_permohonan_uji_klinik . 'bulan ' . $data->umurhari_pasien_permohonan_uji_klinik . 'hari';
        })
        ->rawColumns(['action'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    $item = Pasien::findOrFail($id);

    return view('masterweb::module.admin.laboratorium.rekam-medis-klinik.show', compact('item'));
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
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
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    // $data = PermohonanUjiKlinik2::find($id);
    $hapus = PermohonanUjiKlinik2::where('id_permohonan_uji_klinik', $id)->delete();

    $detail_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)->get();

    if ($detail_paket_klinik) {
      PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)->delete();
    }

    $detail_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)->get();

    if ($detail_parameter_klinik) {
      PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)->delete();
    }

    $detail_analis_klinik = PermohonanUjiAnalisKlinik::where('permohonan_uji_klinik_id', $id)->first();

    if ($detail_analis_klinik) {
      PermohonanUjiAnalisKlinik::where('permohonan_uji_klinik_id', $id)->delete();
    }

    if ($hapus == true) {
      return response()->json(['status' => true, 'pesan' => "Data riwayat rekam medis berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data riwayat rekam medis tidak berhasil dihapus!"], 200);
    }
  }

  // detail lihat hasil
  public function show_detail_hasil(Request $request, $id)
  {
    $item = PermohonanUjiKlinik2::find($id);

    // data parameter
    $data_parameter_jenis = ParameterJenisKlinik::whereNull('deleted_at')->orderBy('name_parameter_jenis_klinik', 'asc')->get();
    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)
      ->whereNull('deleted_at')
      ->get();
    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    // data analis
    if ($item->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    if ($item->spesimen_darah_permohonan_uji_klinik !== null) {
      $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_darah_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_spesimen_darah = '-';
    }

    if ($item->spesimen_urine_permohonan_uji_klinik !== null) {
      $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_urine_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_spesimen_urine = '-';
    }

    // non sedimen
    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)
      ->whereNull('deleted_at')
      ->get();


    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)
      ->orderBy('jenis_parameter_klinik_id', 'ASC')
      ->orderBy('sorting_parameter_satuan', 'ASC')
      ->get();

    $data_jenis_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)
      ->groupBy('jenis_parameter_klinik_id')
      ->orderBy('sort_jenis_klinik', 'ASC')
      ->get();

    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id);




    if (count($data_permohonan_uji_satuan_klinik) > 0) {
      foreach ($data_jenis_parameter_klinik as $key_jenis_parameter_klinik => $value_jenis_parameter_klinik) {
        # code...
        $parameter_jenis_klinik = [];
        $parameter_jenis_klinik['name_parameter_jenis_klinik'] = $value_jenis_parameter_klinik->jenisparameterklinik->name_parameter_jenis_klinik;
        $parameter_jenis_klinik['id_parameter_jenis_klinik'] = $value_jenis_parameter_klinik->jenisparameterklinik->id_parameter_jenis_klinik;
        $parameter_jenis_klinik['item_permohonan_parameter_satuan'] = [];

        foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {

          if ($value_satuan->jenis_parameter_klinik_id == $value_jenis_parameter_klinik->jenisparameterklinik->id_parameter_jenis_klinik) {

            $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
            $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;


            $item_permohonan_parameter_satuan = [];
            $item_permohonan_parameter_satuan['id_permohonan_uji_parameter_klinik'] = $value_satuan->id_permohonan_uji_parameter_klinik;
            $item_permohonan_parameter_satuan['permohonan_uji_klinik'] = $value_satuan->permohonan_uji_klinik;
            $item_permohonan_parameter_satuan['permohonan_uji_paket_klinik'] = $value_satuan->permohonan_uji_paket_klinik;
            $item_permohonan_parameter_satuan['parameter_paket_klinik'] = $value_satuan->parameter_paket_klinik;
            $item_permohonan_parameter_satuan['parameter_satuan_klinik'] = $value_satuan->parameter_satuan_klinik;
            $item_permohonan_parameter_satuan['nama_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->name_parameter_satuan_klinik ?? null;
            $item_permohonan_parameter_satuan['harga_permohonan_uji_parameter_klinik'] = $value_satuan->harga_permohonan_uji_parameter_klinik;
            $item_permohonan_parameter_satuan['hasil_permohonan_uji_parameter_klinik'] = $value_satuan->hasil_permohonan_uji_parameter_klinik;
            $item_permohonan_parameter_satuan['flag_permohonan_uji_parameter_klinik'] = $value_satuan->flag_permohonan_uji_parameter_klinik;
            $item_permohonan_parameter_satuan['keterangan_permohonan_uji_parameter_klinik'] = $value_satuan->keterangan_permohonan_uji_parameter_klinik;

            $id_parameter_jenis_klinik = $value_satuan->jenis_parameter_klinik_id;


            $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
              ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
              ->get();



            $item_parameter_by_baku_mutu = null;
            if (count($data_parameter_by_baku_mutu) > 0) {

              $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
                ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                ->where('is_khusus_baku_mutu', '0')
                ->first();

              // cek dulu apakah ada data dengan data khusus sperti general atau specific

              if ($check_parameter_by_baku_mutu) {
                $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
              } else {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
                  ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '1')
                  ->where('gender_baku_mutu', $pasien_gender)
                  ->where(function ($query) use ($pasien_umur) {
                    $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                      ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                  })
                  ->first();
                if (!isset($item_parameter_by_baku_mutu)) {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
                    ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                    ->where('is_khusus_baku_mutu', '1')
                    ->where('gender_baku_mutu', $pasien_gender)
                    ->first();
                }
              }
            }




            // jika tidak ada satuan di permohonan uji satuan maka ambil value dari baku mutu
            // jika ada nilai satuan di permohonan uji satuan maka ambil nilai dari tabel itu sendiri
            if ($value_satuan->satuan_permohonan_uji_parameter_klinik != null) {
              $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $value_satuan->satuan_permohonan_uji_parameter_klinik;
              $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $value_satuan->unit->name_unit ?? null;
            } else if (isset($item_parameter_by_baku_mutu->unit_id)) {
              $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit_id;
              $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit->name_unit ?? null;
            } else {
              $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = null;
              $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = null;
            }

            $item_permohonan_parameter_satuan['min'] = $item_parameter_by_baku_mutu->min ?? null;
            $item_permohonan_parameter_satuan['max'] = $item_parameter_by_baku_mutu->max ?? null;
            $item_permohonan_parameter_satuan['equal'] = $item_parameter_by_baku_mutu->equal ?? null;
            $item_permohonan_parameter_satuan['nilai_baku_mutu'] = $item_parameter_by_baku_mutu->nilai_baku_mutu ?? null;


            // menyiapkan data array untuk mapping jika data parameter memiliki subsatuan
            $item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'] = [];


            $data_permohonan_uji_subsatuan_klinik = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $value_satuan->id_permohonan_uji_parameter_klinik)
              ->get();

            if (count($data_permohonan_uji_subsatuan_klinik)) {
              foreach ($data_permohonan_uji_subsatuan_klinik as $key_subsatuan => $value_subsatuan) {
                $item_permohonan_parameter_subsatuan = [];
                $item_permohonan_parameter_subsatuan['id_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->id_permohonan_uji_sub_parameter_klinik;
                $item_permohonan_parameter_subsatuan['permohonan_uji_parameter_klinik_id'] = $value_subsatuan->permohonan_uji_parameter_klinik_id;
                $item_permohonan_parameter_subsatuan['parameter_sub_satuan_klinik_id'] = $value_subsatuan->parameter_sub_satuan_klinik_id;
                $item_permohonan_parameter_subsatuan['nama_parameter_sub_satuan_klinik_id'] = $value_subsatuan->parametersubsatuanklinik->name_parameter_sub_satuan_klinik ?? null;
                $item_permohonan_parameter_subsatuan['hasil_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->hasil_permohonan_uji_sub_parameter_klinik;
                $item_permohonan_parameter_subsatuan['flag_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->flag_permohonan_uji_sub_parameter_klinik;
                // $item_permohonan_parameter_subsatuan['baku_mutu_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik;
                $item_permohonan_parameter_subsatuan['keterangan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->keterangan_permohonan_uji_sub_parameter_klinik;

                // cek data baku mutu jika memiliki data subsatuan dengan nilai rujukan
                if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
                  $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
                    ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_subsatuan->parameter_sub_satuan_klinik_id)
                    ->first();

                  if ($item_parameter_subsatuan_by_baku_mutu) {
                    $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
                    $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
                    $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
                    $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik;
                  } else {
                    $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
                    $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
                    $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
                    $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
                  }
                } else {
                  $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
                  $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
                  $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
                  $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
                }

                if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;

                  $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->unit->name_unit ?? null;
                } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;

                  $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit->name_unit ?? null;
                } else {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;

                  $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = null;
                }

                // cek dulu punya satuan di subparameter atau tidak
                // jika tidak ada maka ambil ke baku mutu
                // else null
                /* if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;
                } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
                } else {
                  $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;
                }
    
                $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
                $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
                $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
                $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik; */
                $item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'] = $item_permohonan_parameter_subsatuan;
                // array_push($arr_permohonan_parameter['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
              }
            }


            array_push($parameter_jenis_klinik['item_permohonan_parameter_satuan'], $item_permohonan_parameter_satuan);
            // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
            // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
          }
        }
        array_push($arr_permohonan_parameter, $parameter_jenis_klinik);
      }
    }

    $parameterCustom = DB::table('tb_permohonan_uji_paket_klinik')->where('tb_permohonan_uji_paket_klinik.permohonan_uji_klinik', '=', $id)
      ->join('tb_permohonan_uji_klinik', function ($join) {
        $join->on('tb_permohonan_uji_klinik.id_permohonan_uji_klinik', '=', 'tb_permohonan_uji_paket_klinik.permohonan_uji_klinik')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at')
          ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
      })
      ->join('tb_permohonan_uji_parameter_klinik', function ($join) {
        $join->on('tb_permohonan_uji_paket_klinik.id_permohonan_uji_paket_klinik', '=', 'tb_permohonan_uji_parameter_klinik.permohonan_uji_paket_klinik')
          ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at')
          ->whereNull('tb_permohonan_uji_parameter_klinik.deleted_at');
      })
      ->join('ms_parameter_satuan_klinik', function ($join) {
        $join->on('ms_parameter_satuan_klinik.id_parameter_satuan_klinik', '=', 'tb_permohonan_uji_parameter_klinik.parameter_satuan_klinik')
          ->whereNull('ms_parameter_satuan_klinik.deleted_at')
          ->whereNull('tb_permohonan_uji_parameter_klinik.deleted_at');
      })
      ->where('tb_permohonan_uji_paket_klinik.type_permohonan_uji_paket_klinik', "C")
      ->select(
        'ms_parameter_satuan_klinik.name_parameter_satuan_klinik',
        'ms_parameter_satuan_klinik.id_parameter_satuan_klinik',
        'tb_permohonan_uji_parameter_klinik.parameter_satuan_klinik',
        'tb_permohonan_uji_parameter_klinik.harga_permohonan_uji_parameter_klinik',
        DB::raw('count(*) as count_method')
      )
      ->groupBy(
        'ms_parameter_satuan_klinik.name_parameter_satuan_klinik',
        'ms_parameter_satuan_klinik.id_parameter_satuan_klinik',
        'tb_permohonan_uji_parameter_klinik.parameter_satuan_klinik',
        'tb_permohonan_uji_parameter_klinik.harga_permohonan_uji_parameter_klinik'
      )
      ->get();

    $value_items = array();

    $parameterPaket = DB::table('tb_permohonan_uji_paket_klinik')->where('tb_permohonan_uji_paket_klinik.permohonan_uji_klinik', '=', $id)
      ->join('tb_permohonan_uji_klinik', function ($join) {
        $join->on('tb_permohonan_uji_klinik.id_permohonan_uji_klinik', '=', 'tb_permohonan_uji_paket_klinik.permohonan_uji_klinik')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at')
          ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
      })
      ->join('ms_parameter_paket_klinik', function ($join) {
        $join->on('ms_parameter_paket_klinik.id_parameter_paket_klinik', '=', 'tb_permohonan_uji_paket_klinik.parameter_paket_klinik')
          ->whereNull('ms_parameter_paket_klinik.deleted_at')
          ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
      })
      ->where('tb_permohonan_uji_paket_klinik.type_permohonan_uji_paket_klinik', "P")
      ->select(
        'ms_parameter_paket_klinik.name_parameter_paket_klinik',
        'tb_permohonan_uji_paket_klinik.parameter_paket_klinik',
        'tb_permohonan_uji_paket_klinik.harga_permohonan_uji_paket_klinik',
        DB::raw('count(tb_permohonan_uji_paket_klinik.parameter_paket_klinik) as count_method')
      )
      ->groupBy(
        'ms_parameter_paket_klinik.name_parameter_paket_klinik',
        'tb_permohonan_uji_paket_klinik.parameter_paket_klinik',
        'tb_permohonan_uji_paket_klinik.harga_permohonan_uji_paket_klinik'
      )
      ->get();

    foreach ($parameterPaket as $valuePaket) {
      $nilaiPermohonanUjiKlinik["name_item"] = $valuePaket->name_parameter_paket_klinik;
      $nilaiPermohonanUjiKlinik["count_item"] = $valuePaket->count_method;
      $nilaiPermohonanUjiKlinik["price_item"] = $valuePaket->harga_permohonan_uji_paket_klinik;
      $nilaiPermohonanUjiKlinik["total"] = $valuePaket->harga_permohonan_uji_paket_klinik * $valuePaket->count_method;

      array_push($value_items, $nilaiPermohonanUjiKlinik);
    }

    foreach ($parameterCustom as $valueCustom) {
      $nilaiPermohonanUjiKlinik["name_item"] = $valueCustom->name_parameter_satuan_klinik;
      $nilaiPermohonanUjiKlinik["count_item"] = $valueCustom->count_method;
      $nilaiPermohonanUjiKlinik["price_item"] = $valueCustom->harga_permohonan_uji_parameter_klinik;
      $nilaiPermohonanUjiKlinik["total"] = $valueCustom->harga_permohonan_uji_parameter_klinik * $valueCustom->count_method;

      array_push($value_items, $nilaiPermohonanUjiKlinik);
    }

    return view('masterweb::module.admin.laboratorium.rekam-medis-klinik.lihat-hasil', [
      'item' => $item,
      'data_parameter_jenis' => $data_parameter_jenis,
      'data_parameter_satuan' => $data_parameter_satuan,
      'data_permohonan_uji_paket_klinik' => $data_permohonan_uji_paket_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'tgl_spesimen_darah' => $tgl_spesimen_darah,
      'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_detail_uji_paket' => $data_permohonan_uji_paket_klinik,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
      'value_items' => $value_items
    ]);
  }
}