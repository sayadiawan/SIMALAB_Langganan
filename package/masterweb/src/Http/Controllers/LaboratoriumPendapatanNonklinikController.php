<?php

namespace Smt\Masterweb\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smt\Masterweb\Models\Pasien;
use Smt\Masterweb\Models\Sample;

use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\Laboratorium;
use Smt\Masterweb\Models\PermohonanUji;
use Illuminate\Support\Facades\Validator;
use PDF;

class LaboratoriumPendapatanNonklinikController extends Controller
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
      $payment = $request->get('payment');
      if (!empty($request->get('start_date')) && !empty($request->get('end_date')) && !empty($request->get('name_lab'))) {
        $name_lab = $request->get('name_lab');
        $start_date = $request->get('start_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('start_date'))->format('Y-m-d') : null;
        $end_date = $request->get('end_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('end_date'))->format('Y-m-d') : null;


        $datas = Sample::join('tb_permohonan_uji', function ($join) {
          $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
            ->whereNull('tb_permohonan_uji.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->leftjoin('ms_customer', function ($join) {
              $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
                ->whereNull('tb_permohonan_uji.deleted_at')
                ->whereNull('ms_customer.deleted_at');
            });
        })
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
            $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
              ->whereNull('tb_sample_penerimaan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->leftjoin('tb_sample_penanganan', function ($join) {
            $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
              ->whereNull('tb_sample_penanganan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'tb_sample_penanganan.*',
            'tb_sample_penerimaan.*',
            'date_sending',
            'date_analitik_sample',
            'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
            'name_sample_type',
            'ms_laboratorium.id_laboratorium as id_laboratorium',
            'ms_laboratorium.nama_laboratorium as nama_laboratorium',
            'tb_samples.id_samples as id_samples',
            'tb_samples.codesample_samples as codesample_samples',
            'tb_samples.permohonan_uji_id as permohonan_uji_id',
            'ms_customer.id_customer as id_customer',
            'tb_samples.cost_samples as cost_samples',
            'ms_customer.name_customer as name_customer',
            'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
            'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
            'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
            'tb_permohonan_uji.customer_id as customer_id',
            'tb_permohonan_uji.total_harga as total_harga',
            // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
            'tb_permohonan_uji.created_at as created_at',
          );

        if (isset($payment)) {
          $datas = $datas->where('status_pembayaran', $payment);
        }
        $datas = $datas->where('id_laboratorium', $name_lab)
          ->whereDate('date_sending', '>=', $start_date)
          ->whereDate('date_sending', '<=', $end_date)
          ->orderBy('tb_samples.count_id', 'asc')
          ->distinct('id_laboratorium')
          ->get();
      } else if (!empty($request->get('name_lab')) && empty($request->get('start_date')) && empty($request->get('end_date'))) {

        $name_lab = $request->get('name_lab');
        $search = $request->get('search');
        $start_date =  Carbon::now()->format('Y-m-d');
        $end_date = Carbon::now()->format('Y-m-d');


        $datas = Sample::join('tb_permohonan_uji', function ($join) {
          $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
            ->whereNull('tb_permohonan_uji.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->leftjoin('ms_customer', function ($join) {
              $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
                ->whereNull('tb_permohonan_uji.deleted_at')
                ->whereNull('ms_customer.deleted_at');
            });
        })
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
            $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
              ->whereNull('tb_sample_penerimaan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->leftjoin('tb_sample_penanganan', function ($join) {
            $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
              ->whereNull('tb_sample_penanganan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'tb_sample_penanganan.*',
            'tb_sample_penerimaan.*',
            'date_sending',
            'date_analitik_sample',
            'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
            'name_sample_type',
            'tb_samples.cost_samples as cost_samples',
            'ms_laboratorium.id_laboratorium as id_laboratorium',
            'ms_laboratorium.nama_laboratorium as nama_laboratorium',
            'tb_samples.id_samples as id_samples',
            'tb_samples.codesample_samples as codesample_samples',
            'tb_samples.permohonan_uji_id as permohonan_uji_id',
            'ms_customer.id_customer as id_customer',
            'ms_customer.name_customer as name_customer',
            'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
            'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
            'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
            'tb_permohonan_uji.customer_id as customer_id',
            'tb_permohonan_uji.total_harga as total_harga',
            // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
            'tb_permohonan_uji.created_at as created_at',
          );
        if (isset($payment)) {
          $datas = $datas->where('status_pembayaran', $payment);
        }
        $datas = $datas->where('id_laboratorium', $name_lab)
          ->whereDate('date_sending', '>=', $start_date)
          ->whereDate('date_sending', '<=', $end_date)
          ->orderBy('tb_samples.count_id', 'asc')
          ->distinct('id_laboratorium')
          ->get();
      } else if (empty($request->get('name_lab')) && !empty($request->get('start_date')) && !empty($request->get('end_date'))) {
        $start_date = $request->get('start_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('start_date'))->format('Y-m-d') : null;
        $end_date = $request->get('end_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('end_date'))->format('Y-m-d') : null;

        $datas = Sample::join('tb_permohonan_uji', function ($join) {
          $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
            ->whereNull('tb_permohonan_uji.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->leftjoin('ms_customer', function ($join) {
              $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
                ->whereNull('tb_permohonan_uji.deleted_at')
                ->whereNull('ms_customer.deleted_at');
            });
        })
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
            $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
              ->whereNull('tb_sample_penerimaan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->leftjoin('tb_sample_penanganan', function ($join) {
            $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
              ->whereNull('tb_sample_penanganan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'tb_sample_penanganan.*',
            'tb_sample_penerimaan.*',
            'date_sending',
            'date_analitik_sample',
            'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
            'tb_samples.cost_samples as cost_samples',
            'name_sample_type',
            'ms_laboratorium.id_laboratorium as id_laboratorium',
            'ms_laboratorium.nama_laboratorium as nama_laboratorium',
            'tb_samples.id_samples as id_samples',
            'tb_samples.codesample_samples as codesample_samples',
            'tb_samples.permohonan_uji_id as permohonan_uji_id',
            'ms_customer.id_customer as id_customer',
            'ms_customer.name_customer as name_customer',
            'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
            'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
            'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
            'tb_permohonan_uji.customer_id as customer_id',
            'tb_permohonan_uji.total_harga as total_harga',
            // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
            'tb_permohonan_uji.created_at as created_at',
          );
        if (isset($payment)) {
          $datas = $datas->where('status_pembayaran', $payment);
        }
        $datas = $datas->whereDate('date_sending', '>=', $start_date)
          ->whereDate('date_sending', '<=', $end_date)
          ->orderBy('tb_samples.count_id', 'asc')
          ->distinct('id_laboratorium')
          ->get();

      } else {


        $start_date = $request->get('start_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('start_date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
        $end_date = $request->get('end_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('end_date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');


        $datas = Sample::join('tb_permohonan_uji', function ($join) {
          $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
            ->whereNull('tb_permohonan_uji.deleted_at')
            ->whereNull('tb_samples.deleted_at')
            ->leftjoin('ms_customer', function ($join) {
              $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
                ->whereNull('tb_permohonan_uji.deleted_at')
                ->whereNull('ms_customer.deleted_at');
            });
        })
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
            $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
              ->whereNull('tb_sample_penerimaan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->leftjoin('tb_sample_penanganan', function ($join) {
            $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
              ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
              ->whereNull('tb_sample_penanganan.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'tb_sample_penanganan.*',
            'tb_sample_penerimaan.*',
            'date_sending',
            'date_analitik_sample',
            'name_sample_type',
            'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
            'tb_samples.cost_samples as cost_samples',
            'ms_laboratorium.id_laboratorium as id_laboratorium',
            'ms_laboratorium.nama_laboratorium as nama_laboratorium',
            'tb_samples.id_samples as id_samples',
            'tb_samples.codesample_samples as codesample_samples',
            'tb_samples.permohonan_uji_id as permohonan_uji_id',
            'ms_customer.id_customer as id_customer',
            'ms_customer.name_customer as name_customer',
            'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
            'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
            'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
            'tb_permohonan_uji.customer_id as customer_id',
            'tb_permohonan_uji.total_harga as total_harga',
            // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
            'tb_permohonan_uji.created_at as created_at',
          );
        if (isset($payment)) {
          $datas = $datas->where('status_pembayaran', $payment);
        }
        $datas = $datas->distinct('id_laboratorium')
          ->whereDate('date_sending', '>=', $start_date)
          ->whereDate('date_sending', '<=', $end_date)
          ->orderBy('tb_samples.count_id', 'asc')
          ->get();
      }

      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          /* if ($request->get('nama_laboratorium') != null) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['nama_laboratorium']), Str::lower($request->get('nama_laboratorium')))) {
                return true;
              }

              return false;
            });
          } */

          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['code_permohonan_uji']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['name_customer']), Str::lower($request->get('search')))) {
                return true;
              }

              return false;
            });
          }
        })
        ->addColumn('codesample_samples', function ($data) {
          return $data->codesample_samples ?? '-';
        })
        ->addColumn('tgl_register', function ($data) {
          return $data->created_at != null ? Carbon::createFromFormat('Y-m-d H:i:s', $data->created_at)->isoFormat('D MMMM Y HH:mm') : '-';
        })
        ->addColumn('name_sample_type', function ($data) {
          if (isset($data->rectal_swab_price)) {
            $name_sample_type = $data->name_sample_type . " + Biaya Rectal Swab";
          } else {

            $name_sample_type = $data->name_sample_type;
          }
          return $name_sample_type;
        })
        ->addColumn('tgl_transaksi', function ($data) {
          return $data->date_sending != null ? Carbon::createFromFormat('Y-m-d H:i:s', $data->date_sending)->isoFormat('D MMMM Y HH:mm') : '-';
        })
        ->addColumn('cost_samples', function ($data) {
          if (isset($data->rectal_swab_price)) {
            return $data->cost_samples != null ? rupiah(((float)$data->cost_samples + (float)$data->rectal_swab_price)) : 'Rp. 0';
          } else {
            return $data->cost_samples != null ? rupiah($data->cost_samples) : 'Rp. 0';
          }
        })
        ->addColumn('name_customer', function ($data) {
          return $data->name_customer ?? '-';

          // return $data->permohonanuji->customer->name_customer ?? '-';

          // return $data->customer->name_customer ?? '-';
        })
        ->addColumn('nama_laboratorium', function ($data) {
          return $data->nama_laboratorium ?? '-';

          // return $data->samplemethod->laboratorium->nama_laboratorium ?? '-';

          // return $data->sample->samplemethod->laboratorium->nama_laboratorium ?? '-';
        })
        ->rawColumns(['codesample_samples', 'name_sample_type', 'tgl_register', 'tgl_transaksi', 'total_harga', 'name_customer', 'nama_laboratorium'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    $data_lab = Laboratorium::latest()->get();

    return view('masterweb::module.admin.laboratorium.report.report-pendapatan-nonklinik.list', compact('data_lab'));
  }

  public function getCountTotalPendapatan(Request $request)
  {
    $payment = $request->get('payment');
    if (!empty($request->name_lab) && !empty($request->start_date) && !empty($request->end_date)) {
      $name_lab = $request->post('name_lab');
      $start_date = $request->post('start_date') != null ? Carbon::createFromFormat('m/d/Y', $request->post('start_date'))->format('Y-m-d') : null;
      $end_date = $request->post('end_date') != null ? Carbon::createFromFormat('m/d/Y', $request->post('end_date'))->format('Y-m-d') : null;
      $search = $request->search;

      $datas = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'tb_sample_method.laboratorium_id as sample_method_laboratorium_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $datas = $datas->where('status_pembayaran', $payment);
      }
      $datas = $datas
        ->where('tb_sample_method.laboratorium_id', $name_lab)
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        })
        ->distinct('tb_sample_method.laboratorium_id')
        ->get();

      $count_pendapatan = $datas->sum('cost_samples');
      $count_sample = $datas->count();
    } else if (!empty($request->name_lab) && empty($request->start_date) && empty($request->end_date)) {
      $search = $request->search;
      $name_lab = $request->name_lab;
      $start_date =  Carbon::now()->format('Y-m-d');
      $end_date = Carbon::now()->format('Y-m-d');

      $datas = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'tb_samples.cost_samples as cost_samples',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $datas = $datas->where('status_pembayaran', $payment);
      }
      $datas = $datas->where('id_laboratorium', $name_lab)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        })
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->distinct('id_laboratorium')
        ->get();

      $count_pendapatan = $datas->sum('cost_samples');
      $count_sample = $datas->count();
    } else if (empty($request->name_lab) && (!empty($request->start_date) && !empty($request->end_date))) {
      $search = $request->search;
      $start_date = Carbon::createFromFormat('m/d/Y', $request->start_date)->format('Y-m-d');
      $end_date = Carbon::createFromFormat('m/d/Y', $request->end_date)->format('Y-m-d');

      $datas = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.cost_samples as cost_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $datas = $datas->where('status_pembayaran', $payment);
      }

      $datas = $datas
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        })
        ->distinct('id_laboratorium')
        ->get();

      $count_pendapatan = $datas->sum('cost_samples');
      $count_sample = $datas->count();
    } else {
      $search = $request->search;
      $start_date = $request->get('start_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('start_date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');
      $end_date = $request->get('end_date') != null ? Carbon::createFromFormat('m/d/Y', $request->get('end_date'))->format('Y-m-d') : Carbon::now()->format('Y-m-d');


      $datas = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );

      if (isset($payment)) {
        $datas = $datas->where('status_pembayaran', $payment);
      }
      $datas = $datas->where(function ($query) use ($search) {
        $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
          ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
      })
        ->distinct('id_laboratorium')
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->get();

      $count_pendapatan = $datas->sum('cost_samples');
      $count_rectal_swab = $datas->sum('rectal_swab_price');


      $count_pendapatan = $count_pendapatan + $count_rectal_swab;


      $count_sample = $datas->count();
    }

    $response = [
      'count_pendapatan' => $count_pendapatan,
      'count_sample' => $count_sample
    ];

    return response()->json($response);
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

  public function setPrintDataPeriodikNonklinik(Request $request)
  {
    $start_date = $request->start_date;
    $payment = $request->payment;
    $end_date = $request->end_date;
    $name_lab = $request->name_lab;
    $search = $request->search;

    $start_date_format = (isset($start_date) && $start_date != "Invalid date") ? Carbon::createFromFormat('Y-m-d', $start_date)->isoFormat('D MMMM Y') : Carbon::now()->isoFormat('D MMMM Y');
    $end_date_format = ((isset($end_date) && $end_date != "Invalid date") ? Carbon::createFromFormat('Y-m-d', $end_date)->isoFormat('D MMMM Y') : Carbon::now()->isoFormat('D MMMM Y'));
    if (!isset($start_date) || $start_date == "Invalid date") {
      $start_date =  Carbon::now()->format('Y-m-d');
    }
    if (!isset($end_date) || $end_date == "Invalid date") {
      $end_date = Carbon::now()->format('Y-m-d');
    }


    // dd($search);

    // kondisi filter
    if ($start_date && $end_date && $name_lab && $search) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.cost_samples as cost_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );

      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->where('id_laboratorium', $name_lab)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('tb_samples.count_id', 'asc')
        ->distinct('id_laboratorium')
        ->get();
    } else if ($start_date && $end_date && $name_lab) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->where('id_laboratorium', $name_lab)
        ->distinct('id_laboratorium')
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else if ($start_date && $end_date && $search) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        )
        ->whereDate('date_permohonan_uji', '>=', $start_date)
        ->whereDate('date_permohonan_uji', '<=', $end_date)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        });
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data
        ->distinct('id_laboratorium')
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else if ($start_date && $end_date) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->distinct('id_laboratorium')
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else if ($name_lab && $search) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',

        );

      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data->where('id_laboratorium', $name_lab)
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        })
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->distinct('id_laboratorium')
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else if ($name_lab) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data->where('id_laboratorium', $name_lab)
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->distinct('id_laboratorium')
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else if ($search) {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        )
        ->where(function ($query) use ($search) {
          $query->where('code_permohonan_uji', 'LIKE', '%' . $search . '%')
            ->orWhere('name_customer', 'LIKE', '%' . $search . '%');
        });
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data
        ->distinct('id_laboratorium')
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    } else {
      $data = Sample::join('tb_permohonan_uji', function ($join) {
        $join->on('tb_permohonan_uji.id_permohonan_uji', '=', 'tb_samples.permohonan_uji_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('tb_samples.deleted_at')
          ->leftjoin('ms_customer', function ($join) {
            $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
              ->whereNull('tb_permohonan_uji.deleted_at')
              ->whereNull('ms_customer.deleted_at');
          });
      })
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
          $join->on('tb_sample_penerimaan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penerimaan.sample_id')
            ->whereNull('tb_sample_penerimaan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->leftjoin('tb_sample_penanganan', function ($join) {
          $join->on('tb_sample_penanganan.laboratorium_id', '=', 'tb_sample_method.laboratorium_id')
            ->on('tb_samples.id_samples', '=', 'tb_sample_penanganan.sample_id')
            ->whereNull('tb_sample_penanganan.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->select(
          'tb_sample_penanganan.*',
          'tb_sample_penerimaan.*',
          'date_sending',
          'date_analitik_sample',
          'name_sample_type',
          'tb_samples.biaya_tindakan_rectal_swab as rectal_swab_price',
          'tb_samples.cost_samples as cost_samples',
          'ms_laboratorium.id_laboratorium as id_laboratorium',
          'ms_laboratorium.nama_laboratorium as nama_laboratorium',
          'tb_samples.id_samples as id_samples',
          'tb_samples.codesample_samples as codesample_samples',
          'tb_samples.permohonan_uji_id as permohonan_uji_id',
          'ms_customer.id_customer as id_customer',
          'ms_customer.name_customer as name_customer',
          'tb_permohonan_uji.id_permohonan_uji as id_permohonan_uji',
          'tb_permohonan_uji.code_permohonan_uji as code_permohonan_uji',
          'tb_permohonan_uji.date_permohonan_uji as date_permohonan_uji',
          'tb_permohonan_uji.customer_id as customer_id',
          'tb_permohonan_uji.total_harga as total_harga',
          // // 'tb_permohonan_uji.date_done_estimation as date_done_estimation',
          'tb_permohonan_uji.created_at as created_at',
        );
      if (isset($payment)) {
        $data = $data->where('status_pembayaran', $payment);
      }
      $data = $data
        ->distinct('id_laboratorium')
        ->whereDate('date_sending', '>=', $start_date)
        ->whereDate('date_sending', '<=', $end_date)
        ->orderBy('tb_samples.count_id', 'asc')
        ->get();
    }


    // dd($data);

    // get laboratorium
    $laboratorium = Laboratorium::where('id_laboratorium', $name_lab)->first();

    if ($laboratorium) {
      $nama_laboratorium = $laboratorium->nama_laboratorium;
    } else {
      $nama_laboratorium = 'Semua Laboratorium';
    }



    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.report.report-pendapatan-nonklinik.print-format.print-periodik', [
      'permohonan_uji_nonklinik' => $data,
      'start_date_format' => $start_date_format,
      'end_date_format' => $end_date_format,
      'nama_laboratorium' => $nama_laboratorium,
      'payment' => $payment
    ]);

    return $pdf->stream();

    /* return view('masterweb::module.admin.laboratorium.report.report-pendapatan-nonklinik.print-format.print-periodik', [
      'permohonan_uji_nonklinik' => $data,
      'start_date_format' => $start_date_format,
      'end_date_format' => $end_date_format,
      'nama_laboratorium' => $nama_laboratorium,
      'search' => $search
    ]); */
  }
}