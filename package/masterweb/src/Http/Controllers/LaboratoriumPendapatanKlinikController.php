<?php

namespace Smt\Masterweb\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\PermohonanUjiKlinik;
use PDF;

class LaboratoriumPendapatanKlinikController extends Controller
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
    $count = PermohonanUjiKlinik2::get()->sum('total_harga_permohonan_uji_klinik');

    if (request()->ajax()) {
      // dd($request->start_date);
      if (!empty($request->get('start_date')) && !empty($request->get('end_date'))) {
        $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
        $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');

        $datas = PermohonanUjiKlinik2::with('pasien')->whereDate('tglregister_permohonan_uji_klinik', '>=', $start_date)
          ->whereDate('tglregister_permohonan_uji_klinik', '<=', $end_date)
          ->latest()
          ->get();
      } else {
        $datas = PermohonanUjiKlinik2::with('pasien')->latest()->get();
      }

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['noregister_permohonan_uji_klinik']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['nama_pasien']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('tgl_register', function ($data) {
          return $data->tglregister_permohonan_uji_klinik != null ? Carbon::createFromFormat('Y-m-d H:i:s', $data->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y') : '-';
        })
        ->addColumn('tgl_transaksi', function ($data) {
          return $data->created_at != null ? Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->isoFormat('D MMMM Y HH:mm') : '-';
        })
        ->addColumn('total_harga', function ($data) {
          return $data->total_harga_permohonan_uji_klinik != null ? rupiah($data->total_harga_permohonan_uji_klinik) : 'Rp. 0';
        })
        ->addColumn('nama_pasien', function ($data) {
          return $data->pasien->nama_pasien;
        })
        ->rawColumns(['tgl_register', 'tgl_transaksi', 'total_harga', 'nama_pasien'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    return view('masterweb::module.admin.laboratorium.report.report-pendapatan-klinik.list', compact('count'));
  }

  public function getCountTotalPendapatan(Request $request)
  {
    $search = $request->search;
    $start_date = $request->start_date ? Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d') : null;
    $end_date = $request->end_date ? Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d') : null;

    $query = PermohonanUjiKlinik2::whereNull('deleted_at');

    if ($start_date && $end_date) {
      $query->whereBetween('tglregister_permohonan_uji_klinik', [$start_date, $end_date]);
    }

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('noregister_permohonan_uji_klinik', 'LIKE', '%' . $search . '%')
          ->orWhereHas('pasien', function ($query) use ($search) {
            $query->where('nama_pasien', 'LIKE', '%' . $search . '%')
              ->whereNull('deleted_at');
          });
      });
    }

    $count = $query->sum('total_harga_permohonan_uji_klinik');

    return response()->json($count);
  }


  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
  }

  public function rules($request)
  {
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
  public function show($id)
  {
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
  }

  public function setPrintDataPeriodikKlinik(Request $request)
  {
    $start_date = $request->start_date;
    $end_date = $request->end_date;
    $search = $request->search;

    $start_date_format = isset($start_date) ? Carbon::createFromFormat('Y-m-d', $start_date)->isoFormat('D MMMM Y') : '';
    $end_date_format = isset($end_date) ? Carbon::createFromFormat('Y-m-d', $end_date)->isoFormat('D MMMM Y') : '';

    if ($start_date && $end_date && $search) {
      $data = PermohonanUjiKlinik::leftjoin('ms_pasien', function ($join) {
        $join->on('ms_pasien.id_pasien', '=', 'tb_permohonan_uji_klinik.pasien_permohonan_uji_klinik')
          ->whereNull('ms_pasien.deleted_at')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at');
      })
        ->select(
          'ms_pasien.nik_pasien as id_pasien',
          'ms_pasien.nik_pasien as nik_pasien',
          'ms_pasien.nama_pasien as nama_pasien',
          'ms_pasien.no_rekammedis_pasien as nopasien_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.id_permohonan_uji_klinik as id_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.noregister_permohonan_uji_klinik as noregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.tglregister_permohonan_uji_klinik as tglregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.created_at as created_at',
          'tb_permohonan_uji_klinik.total_harga_permohonan_uji_klinik as total_harga_permohonan_uji_klinik',
        )
        ->whereDate('tglregister_permohonan_uji_klinik', '>=', $start_date)
        ->whereDate('tglregister_permohonan_uji_klinik', '<=', $end_date)
        ->where(function ($query) use ($search) {
          $query->where('noregister_permohonan_uji_klinik', 'LIKE', '%' . $search . '%')
            ->orWhere('nama_pasien', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('created_at', 'asc')
        ->get();
    } else if ($start_date && $end_date) {
      $data = PermohonanUjiKlinik::leftjoin('ms_pasien', function ($join) {
        $join->on('ms_pasien.id_pasien', '=', 'tb_permohonan_uji_klinik.pasien_permohonan_uji_klinik')
          ->whereNull('ms_pasien.deleted_at')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at');
      })
        ->select(
          'ms_pasien.nik_pasien as id_pasien',
          'ms_pasien.nik_pasien as nik_pasien',
          'ms_pasien.nama_pasien as nama_pasien',
          'ms_pasien.no_rekammedis_pasien as nopasien_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.id_permohonan_uji_klinik as id_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.noregister_permohonan_uji_klinik as noregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.tglregister_permohonan_uji_klinik as tglregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.created_at as created_at',
          'tb_permohonan_uji_klinik.total_harga_permohonan_uji_klinik as total_harga_permohonan_uji_klinik',
        )
        ->whereDate('tglregister_permohonan_uji_klinik', '>=', $start_date)
        ->whereDate('tglregister_permohonan_uji_klinik', '<=', $end_date)
        ->orderBy('created_at', 'asc')
        ->get();
    } else if ($search) {
      $data = PermohonanUjiKlinik::leftjoin('ms_pasien', function ($join) {
        $join->on('ms_pasien.id_pasien', '=', 'tb_permohonan_uji_klinik.pasien_permohonan_uji_klinik')
          ->whereNull('ms_pasien.deleted_at')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at');
      })
        ->select(
          'ms_pasien.nik_pasien as id_pasien',
          'ms_pasien.nik_pasien as nik_pasien',
          'ms_pasien.nama_pasien as nama_pasien',
          'ms_pasien.no_rekammedis_pasien as nopasien_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.id_permohonan_uji_klinik as id_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.noregister_permohonan_uji_klinik as noregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.tglregister_permohonan_uji_klinik as tglregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.created_at as created_at',
          'tb_permohonan_uji_klinik.total_harga_permohonan_uji_klinik as total_harga_permohonan_uji_klinik',
        )
        ->where(function ($query) use ($search) {
          $query->where('noregister_permohonan_uji_klinik', 'LIKE', '%' . $search . '%')
            ->orWhere('nama_pasien', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('created_at', 'asc')
        ->get();
    } else {
      $data = PermohonanUjiKlinik::leftjoin('ms_pasien', function ($join) {
        $join->on('ms_pasien.id_pasien', '=', 'tb_permohonan_uji_klinik.pasien_permohonan_uji_klinik')
          ->whereNull('ms_pasien.deleted_at')
          ->whereNull('tb_permohonan_uji_klinik.deleted_at');
      })
        ->select(
          'ms_pasien.nik_pasien as id_pasien',
          'ms_pasien.nik_pasien as nik_pasien',
          'ms_pasien.nama_pasien as nama_pasien',
          'ms_pasien.no_rekammedis_pasien as nopasien_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.id_permohonan_uji_klinik as id_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.noregister_permohonan_uji_klinik as noregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.tglregister_permohonan_uji_klinik as tglregister_permohonan_uji_klinik',
          'tb_permohonan_uji_klinik.created_at as created_at',
          'tb_permohonan_uji_klinik.total_harga_permohonan_uji_klinik as total_harga_permohonan_uji_klinik',
        )
        ->orderBy('created_at', 'asc')
        ->get();
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.report.report-pendapatan-klinik.print-format.print-periodik', [
      'permohonan_uji_klinik' => $data,
      'start_date_format' => $start_date_format,
      'end_date_format' => $end_date_format
    ]);
    return $pdf->stream();

    /* return view('masterweb::module.admin.laboratorium.report.report-pendapatan-klinik.print-format.print-periodik', [
      'permohonan_uji_klinik' => $data,
      'start_date_format' => $start_date_format,
      'end_date_format' => $end_date_format
    ]); */
  }
}
