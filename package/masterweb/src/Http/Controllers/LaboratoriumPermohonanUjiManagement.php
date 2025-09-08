<?php

namespace Smt\Masterweb\Http\Controllers;

use PDF;
use Mapper;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\User;
use \Smt\Masterweb\Models\Packet;
use \Smt\Masterweb\Models\Sample;
use Illuminate\Support\Facades\DB;
use \Smt\Masterweb\Models\Customer;
use \Smt\Masterweb\Models\Industry;
use \Smt\Masterweb\Models\Container;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use \Smt\Masterweb\Models\SampleType;
use \Smt\Masterweb\Models\PacketDetail;
use \Smt\Masterweb\Models\SampleMethod;
use \Smt\Masterweb\Models\PermohonanUji;
use \Smt\Masterweb\Models\SamplesMethod;
use \Smt\Masterweb\Models\MethodSampling;
use \Smt\Masterweb\Models\LabNum;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Smt\Masterweb\Models\VerificationActivitySample;
use Yajra\Datatables\Datatables;


class LaboratoriumPermohonanUjiManagement extends Controller
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
    // if (request()->ajax()) {
    //   if (Auth::user()->level == "0e6da765-0f3a-4471-9e1d-6af257e60a70") {
    //     $datas = PermohonanUji::where('tb_permohonan_uji.status', '=', '0')
    //       ->select('tb_permohonan_uji.*')
    //       ->orderBy('created_at', 'DESC')
    //       ->get();
    //   } else {
    //     $datas = PermohonanUji::select('tb_permohonan_uji.*')
    //       ->orderBy('created_at', 'DESC')
    //       ->get();
    //   }

    //   return Datatables::of($datas)
    //     ->filter(function ($instance) use ($request) {
    //       if (!empty($request->get('search'))) {
    //         $instance->collection = $instance->collection->filter(function ($row) use ($request) {
    //           if (Str::contains(Str::lower($row['code_permohonan_uji']), Str::lower($request->get('search')))) {
    //             return true;
    //           } else if (Str::contains(Str::lower($row['customer_permohonan_uji']), Str::lower($request->get('search')))) {
    //             return true;
    //           } else if (Str::contains(Str::lower($row['date_permohonan_uji']), Str::lower($request->get('search')))) {
    //             return true;
    //           } else if (Str::contains(Str::lower($row['status_pembayaran']), Str::lower($request->get('search')))) {
    //             return true;
    //           } else if (Str::contains(Str::lower($row['count_sample_type']), Str::lower($request->get('search')))) {
    //             return true;
    //           }

    //           return false;
    //         });
    //       }
    //     })
    //     ->addColumn('code_permohonan_uji', function ($data) {
    //       $qr = QrCode::size(100)->generate(route('scan.verification', [$data->id_permohonan_uji]));
    //       $code_permohonan_uji = $data->code_permohonan_uji . '<br><br>' . $qr;

    //       return $code_permohonan_uji;
    //     })
    //     ->addColumn('customer_permohonan_uji', function ($data) {
    //       return $data->customer->name_customer;
    //     })
    //     ->addColumn('date_permohonan_uji', function ($data) {
    //       return Carbon::createFromFormat('Y-m-d H:i:s', $data->date_permohonan_uji)->isoFormat('D MMMM Y');;
    //     })
    //     ->addColumn("status_pembayaran", function ($data) {
    //       if ($data->status_pembayaran == 0) {
    //         $get_sample_rectal = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
    //           ->where('typesample_samples', 'ab516530-aed0-481b-ab9c-86c8ccbcabb3')
    //           ->join('ms_sample_type', function ($join) {
    //             $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
    //               ->whereNull('ms_sample_type.deleted_at')
    //               ->whereNull('tb_samples.deleted_at');
    //           })
    //           ->select(
    //             'name_sample_type',
    //             'id_samples',
    //             'tb_samples.created_at'
    //           )
    //           ->distinct('typesample_samples')
    //           ->latest()
    //           ->get();

    //         if (count($get_sample_rectal) > 0) {
    //           $text = '
    //               <div class="form-group">
    //                   <p>Total permohonan uji: <b>' . rupiah($data->total_harga) . '</b></p>
    //                   <p>Total tambahan biaya tindakan Rectal Swab: <b>' . rupiah(20000) . '</b></p>
    //                   <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga + 20000) . '</b></p>
    //               </div>
    //           ';

    //           $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="20000" id="biaya-tindakan-rectal-swab" readonly>';
    //         } else {
    //           $text = '
    //               <div class="form-group">
    //                   <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga) . '</b></p>
    //               </div>
    //           ';

    //           $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="0" id="biaya-tindakan-rectal-swab" readonly>';
    //         }

    //         $status_pembayaran = '
    //         <button type="button" class="btn btn-outline-danger" data-toggle="modal" style="padding:0px 10px !important; width:100%;" data-target="#pembayaran_' . $data->id_permohonan_uji . '" >
    //                     Belum Terbayar
    //                 </button>

    //                 <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    //                     <div class="modal-dialog" role="document">
    //                         <div class="modal-content">
    //                             <div class="modal-header">
    //                                 <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
    //                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    //                                 <span aria-hidden="true">&times;</span>
    //                                 </button>
    //                             </div>
    //                             <form action="' . route('elits-permohonan-uji.payment', [$data->id_permohonan_uji]) . '" method="POST">

    //                                 <div class="modal-body">

    //                                     ' . $text . '

    //                                     <input type="hidden" name="_token" value="' . csrf_token() . '">

    //                                     <div class="form-group">
    //                                         <label for="recipient-name" class="col-form-label">TELAH DITERIMA DARI :</label>
    //                                         <input type="text" class="form-control" name="recipient-name" value="' . $data->customer->name_customer . '" id="recipient-name">

    //                                         ' . $input . '
    //                                     </div>

    //                                     <div class="form-group">
    //                                         <label for="message-text" class="col-form-label">ALAMAT :</label>
    //                                         <textarea class="form-control" name="address" id="message-text">' . $data->customer->address_customer . '</textarea>
    //                                     </div>

    //                                 </div>
    //                                 <div class="modal-footer">
    //                                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    //                                     <button type="submit" class="btn btn-success" >TERBAYAR</button>
    //                                 </div>
    //                             </form>
    //                         </div>
    //                     </div>
    //                 </div>
    //         ';
    //       } else {
    //         $get_sample_rectal = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
    //           ->where('typesample_samples', 'ab516530-aed0-481b-ab9c-86c8ccbcabb3')
    //           ->join('ms_sample_type', function ($join) {
    //             $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
    //               ->whereNull('ms_sample_type.deleted_at')
    //               ->whereNull('tb_samples.deleted_at');
    //           })
    //           ->select(
    //             'name_sample_type',
    //             'id_samples',
    //             'tb_samples.created_at'
    //           )
    //           ->distinct('typesample_samples')
    //           ->latest()
    //           ->get();

    //         if (count($get_sample_rectal) > 0) {
    //           $text = '
    //               <div class="form-group">
    //                   <p>Total permohonan uji: <b>' . rupiah($data->total_harga) . '</b></p>
    //                   <p>Total tambahan biaya tindakan Rectal Swab: <b>' . rupiah(20000) . '</b></p>
    //                   <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga + 20000) . '</b></p>
    //               </div>
    //           ';

    //           $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="20000" id="biaya-tindakan-rectal-swab" readonly>';
    //         } else {
    //           $text = '
    //               <div class="form-group">
    //                   <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga) . '</b></p>
    //               </div>
    //           ';

    //           $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="0" id="biaya-tindakan-rectal-swab" readonly>';
    //         }
    //         $status_pembayaran = '<button type="button" class="btn btn-outline-success" style="padding:0px 10px !important; width:100%;" data-toggle="modal" data-target="#pembayaran_' . $data->id_permohonan_uji . '">Terbayar</button>

    //         <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    //                     <div class="modal-dialog" role="document">
    //                         <div class="modal-content">
    //                             <div class="modal-header">
    //                                 <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
    //                                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    //                                 <span aria-hidden="true">&times;</span>
    //                                 </button>
    //                             </div>
    //                             <form action="' . route('elits-permohonan-uji.edit_payment', [$data->id_permohonan_uji]) . '" method="POST">

    //                                 <div class="modal-body">

    //                                     ' . $text . '

    //                                     <input type="hidden" name="_token" value="' . csrf_token() . '">

    //                                     <div class="form-group">
    //                                         <label for="recipient-name" class="col-form-label">TELAH DITERIMA DARI :</label>
    //                                         <input type="text" class="form-control" name="recipient-name" value="' . $data->nota_diterima_dari . '" id="recipient-name">

    //                                         ' . $input . '
    //                                     </div>

    //                                     <div class="form-group">
    //                                         <label for="message-text" class="col-form-label">ALAMAT :</label>
    //                                         <textarea class="form-control" name="address" id="message-text">' . $data->nota_address_from . '</textarea>
    //                                     </div>

    //                                 </div>
    //                                 <div class="modal-footer">
    //                                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    //                                     <button type="submit" class="btn btn-success" >TERBAYAR</button>
    //                                 </div>
    //                             </form>
    //                         </div>
    //                     </div>
    //                 </div>
    //         ';
    //       }

    //       $done_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
    //         ->join('tb_pengesahan_hasil', function ($join) {
    //           $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
    //             ->whereNull('tb_pengesahan_hasil.deleted_at')
    //             ->whereNull('tb_samples.deleted_at');
    //         })->count();
    //       $all_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)->count();

    //       if ($all_sample > 0) {
    //         if ($done_sample >= $all_sample) {
    //           $status_pembayaran = $status_pembayaran . '
    //           <br>
    //           <br>
    //           <button type="submit" class="btn btn-outline-success " style="padding:0px 10px !important;width:100%">Selesai</button>
    //           ';
    //         }
    //       }


    //       return $status_pembayaran;
    //     })
    //     ->addColumn('action', function ($data) {
    //       $readButton = '';
    //       $editButton = '';
    //       $deleteButton = '';

    //       if (getAction('read')) {
    //         $readButton = '<a href="' . route('elits-samples.index', [$data->id_permohonan_uji]) . '" class="dropdown-item" title="Lihat Daftar Sample">Lihat Daftar Sample</a> ';
    //       }

    //       if (getAction('update')) {
    //         $editButton = '<a href="' . route('elits-permohonan-uji.edit', [$data->id_permohonan_uji]) . '" class="dropdown-item" title="Edit">Edit</a> ';
    //       }

    //       if (getAction('delete')) {
    //         $deleteButton = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_permohonan_uji  . '" data-nama="' . $data->customer->name_customer . '" title="Hapus">Hapus</a> ';
    //       }

    //       $button = '<div class="dropdown show m-1">
    //                           <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    //                           Aksi
    //                           </a>

    //                           <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
    //                               ' . $readButton . '

    //                               ' . $editButton . '

    //                               ' . $deleteButton . '
    //                           </div>
    //                       </div>';

    //       return $button;
    //     })
    //     ->addColumn('cetak', function ($data) {
    //       $get_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
    //         ->join('ms_sample_type', function ($join) {
    //           $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
    //             ->whereNull('ms_sample_type.deleted_at')
    //             ->whereNull('tb_samples.deleted_at');
    //         })
    //         ->select(
    //           'name_sample_type',
    //           'id_samples',
    //           'tb_samples.created_at'
    //         )
    //         ->distinct('typesample_samples')
    //         ->latest()
    //         ->get();

    //       // generate button

    //       if (count($get_sample) > 0) {
    //         $cetakButton = '
    //               <div class="dropdown show">
    //                   <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownPrintLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    //                       <i class="fa fa-print" aria-hidden="true"></i> Cetak
    //                   </a>

    //                   <div class="dropdown-menu" aria-labelledby="dropdownPrintLink">

    //                       <a class="dropdown-item" href="' . route('elits-release.permintaan-pemeriksaan', [$data->id_permohonan_uji]) . '" target="__blank">Cetak Permintaan Pemeriksaan</a>
    //                       <a class="dropdown-item" href="' . route('elits-release.nota', [$data->id_permohonan_uji]) . '" target="__blank">Cetak Nota</a>

    //                   </div>
    //               </div>
    //         ';
    //       } else {
    //         $cetakButton = '
    //           <button type="button" class="btn btn-success" disabled>
    //             <i class="fa fa-print" aria-hidden="true"></i> Cetak
    //           </button>
    //         ';
    //       }

    //       return  $cetakButton;
    //     })
    //     ->addColumn('count_sample_type', function ($data) {
    //       $get_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
    //         ->join('ms_sample_type', function ($join) {
    //           $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
    //             ->whereNull('ms_sample_type.deleted_at')
    //             ->whereNull('tb_samples.deleted_at');
    //         })
    //         ->select(
    //           'name_sample_type',
    //           'tb_samples.typesample_samples'
    //         )
    //         ->distinct('typesample_samples')
    //         ->get();

    //       $prefixSampleType = $sampleType = '';

    //       foreach ($get_sample as $mytable) {
    //         $sampleType .= $prefixSampleType . $mytable['name_sample_type'];
    //         $prefixSampleType = '<br><br>';
    //       }

    //       return $sampleType;
    //     })
    //     ->rawColumns(['code_permohonan_uji', 'customer_permohonan_uji', 'date_permohonan_uji', 'status_pembayaran', 'action', 'cetak', 'count_sample_type'])
    //     ->addIndexColumn() //increment
    //     ->make(true);
    // }

    return view('masterweb::module.admin.laboratorium.permohonan-uji.pagination');
  }

  public function loadDataTableSearch($datas, $search)
  {
    $datas = $datas->where(function ($query) use ($search) {
      $query->where('tb_permohonan_uji.code_permohonan_uji', 'like', "%{$search}%")
        ->Orwhere('ms_customer.name_customer', 'like', "%{$search}%")
        ->Orwhere('tb_samples.codesample_samples', 'like', "%{$search}%")
        ->Orwhere('ms_sample_type.name_sample_type', 'like', "%{$search}%");
    });
    return $datas;
  }

  public function loadDataTablePermohonanUji($limit_val, $start, $search)
  {
    $totalDataRecord =  PermohonanUji::where('tb_permohonan_uji.status', '=', '0')
      ->leftjoin('tb_samples', function ($join) {
        $join->on('tb_samples.permohonan_uji_id', '=', 'tb_permohonan_uji.id_permohonan_uji')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->join('ms_sample_type', function ($join) {
            $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
              ->whereNull('ms_sample_type.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('tb_permohonan_uji.deleted_at')

          ->whereNull('ms_customer.deleted_at');
      })
      ->orderBy('tb_permohonan_uji.created_at', 'DESC')
      ->distinct('tb_permohonan_uji.id_permohonan_uji');

    $totalDataRecord = $this->loadDataTableSearch($totalDataRecord, $search);
    $totalDataRecord = $totalDataRecord->count();

    $totalFilteredRecord = $totalDataRecord;

    $datas =  PermohonanUji::where('tb_permohonan_uji.status', '=', '0')
      ->leftjoin('tb_samples', function ($join) {
        $join->on('tb_samples.permohonan_uji_id', '=', 'tb_permohonan_uji.id_permohonan_uji')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->join('ms_sample_type', function ($join) {
            $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
              ->whereNull('ms_sample_type.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftjoin('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('tb_permohonan_uji.deleted_at')

          ->whereNull('ms_customer.deleted_at');
      })
      ->select(

        'tb_permohonan_uji.*',
        // 'ms_sample_type.*',
        'ms_customer.*'
      )
      ->offset($start)
      ->limit($limit_val)
      ->orderBy('tb_permohonan_uji.created_at', 'DESC')
      ->distinct('tb_permohonan_uji.id_permohonan_uji');
    $datas = $this->loadDataTableSearch($datas, $search);
    $datas = $datas->get();

    $no = (int) $start + 1;
    $i = 0;
    foreach ($datas as $data) {
      $datas[$i]["nomer"] = $no;
      $no++;
      $i++;
    }
    $result["totalFilteredRecord"] = $totalFilteredRecord;
    $result["totalDataRecord"] = $totalDataRecord;
    $result["datas"] = $datas;

    return $result;
  }

  public function pagination(Request $request)
  {

    $limit_val = $request->input('length');
    $start = $request["start"];
    $search = $request["search"];
    $request["start"] = 0;

    $result = $this->loadDataTablePermohonanUji($limit_val, $start, $search);

    // $data_table = Datatables::of($datas)
    // ->addColumn('nomer', function ($data) {
    //   return $data['nomer'];
    // })
    // ->addColumn('name_device_production', function ($data) {
    //   return $data['name_device_production'];
    // })
    // ->addColumn('kode_device', function ($data) {
    //   return $data['kode_device'];
    // })
    $datas = $result["datas"];
    $data_table = Datatables::of($datas)
      ->filter(function ($instance) use ($request) {
        if (!empty($request->get('search'))) {
          $instance->collection = $instance->collection->filter(function ($row) use ($request) {
            if (Str::contains(Str::lower($row['code_permohonan_uji']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['customer_permohonan_uji']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['date_permohonan_uji']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['status_pembayaran']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['count_sample_type']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['num_samples']), Str::lower($request->get('search')))) {
              return true;
            }

            return false;
          });
        }
      })
      ->addColumn('pemeriksaan', function ($data) {
        $methodPackets = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
          ->leftjoin('ms_packet', function ($join) {
            $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
              ->whereNull('ms_packet.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->whereNotNull('ms_packet.id_packet')
          ->select('tb_samples.sample_type_group', 'ms_packet.id_packet', 'tb_samples.cost_samples', 'ms_packet.name_packet', 'ms_packet.price_total_packet', DB::raw('count(*) as count_method'))
          ->groupBy('tb_samples.sample_type_group', 'ms_packet.id_packet', 'ms_packet.name_packet', 'ms_packet.price_total_packet')
          ->orderBy('tb_samples.sample_type_group', 'DESC')
          ->get();

          // dd( $methodPackets );



        $count = 1;
        $sample_type_group = null;
        $i = 0;


        $methodNonPackets = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
        ->leftjoin('ms_packet', function ($join) {
          $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
            ->whereNull('ms_packet.deleted_at')
            ->whereNull('tb_samples.deleted_at');
        })
        ->join('tb_sample_method', function ($join) {
          $join->on('tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
            ->whereNull('tb_samples.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->join('ms_method', function ($join) {
          $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
            ->whereNull('ms_method.deleted_at')
            ->whereNull('tb_sample_method.deleted_at');
        })
        ->whereNull('ms_packet.id_packet')
        // ->orderBy('ms_packet.id_packet')
        ->select('ms_method.id_method', 'tb_sample_method.price_method', 'ms_method.params_method', 'ms_method.price_total_method', DB::raw('count(*) as count_method'))
        ->groupBy('ms_method.id_method', 'ms_method.params_method', 'ms_method.price_total_method')
        // ->lists('total','id_method')
        ->get();





      $value_items = array();
      $total = 0;


      $string_pemeriksaan ="";
      foreach ($methodNonPackets as $methodNonPacket) {
        // $methodNonPacket["name_item"] = $methodNonPacket["params_method"];
        $string_pemeriksaan= $string_pemeriksaan.$methodNonPacket["params_method"]."<br>";

        // $methodNonPacket["count_item"] = $methodNonPacket["count_method"];
        // $methodNonPacket["price_item"] = $methodNonPacket["price_total_method"];
        // $methodNonPacket["total"] = $methodNonPacket["price_total_method"] * $methodNonPacket["count_method"];
        // array_push($value_items, $methodNonPacket);
        // $total = $total + $methodNonPacket["total"];
      }


        foreach ($methodPackets as $methodPacket) {

          if ($i == 0) {
            $id_packet = $methodPacket['id_packet'];
          }
          if ($i == 0 && isset($methodPacket['sample_type_group'])) {
            $id_packet = $methodPacket['id_packet'];
            $methodPackets[$i]['count_method'] = 1;
          }
          if ($count = 1 && isset($methodPacket['sample_type_group'])) {
            $id_packet = $methodPacket['id_packet'];
            $methodPackets[$i]['count_method'] = 1;
          }

          if ($id_packet == $methodPacket['id_packet'] && isset($methodPacket['sample_type_group']) && $i != 0) {
            unset($methodPackets[$i]);
            $count++;
            $methodPackets[$i - 1]['count_method'] = $count;
          } else {
            $count = 1;
          }
          $i++;
        }

        // dd($methodPackets);



        foreach ($methodPackets as $methodPacket) {

          $methodPacket["name_item"] = $methodPacket["name_packet"];
          $methodPacket["count_item"] = $methodPacket["count_method"];

          if ($methodPacket['cost_samples'] == 0) {

            $methodPacket["price_item"] = $methodPacket["price_total_packet"];
            if ($methodPacket["price_total_packet"] == 0) {
              $packet_details = PacketDetail::where('packet_id', $methodPacket["id_packet"])
                ->join('ms_method', function ($join) {
                  $join->on('ms_method.id_method', '=', 'ms_packet_detail.method_id')
                    ->whereNull('ms_method.deleted_at')
                    ->whereNull('ms_packet_detail.deleted_at');
                })->get();
              $total = 0;
              foreach ($packet_details as $packet_detail) {
                # code...
                $total = $total + $packet_detail->price_total_method;
              }
              $methodPacket["price_item"] = $total;
            }
          } else {
            $methodPacket["price_item"] = $methodPacket['cost_samples'];
          }

          $methodPacket["total"] = $methodPacket["price_item"] * $methodPacket["count_method"];
          array_push($value_items, $methodPacket);

          $total = $total +  $methodPacket["total"];

          $id_packet = $methodPacket['id_packet'];
        }

      foreach ($methodPackets as $key => $value) {
        # code...
        $string_pemeriksaan= $string_pemeriksaan.$value->name_packet."<br>";
      }



        return $string_pemeriksaan;
      })
      ->addColumn('code_permohonan_uji', function ($data) {
        $qr = QrCode::size(100)->generate(route('scan.verification', [$data->id_permohonan_uji]));
        $code_permohonan_uji = $data->code_permohonan_uji . '<br><br>' . $qr;

        return $code_permohonan_uji;
      })
      ->addColumn('customer_permohonan_uji', function ($data) {
        return $data->customer->name_customer;
      })
      ->addColumn('num_samples', function ($data) {
        $get_sample = DB::select("
        SELECT * FROM tb_samples WHERE `permohonan_uji_id` = '".$data->id_permohonan_uji."'AND tb_samples.deleted_at IS NULL ORDER BY CAST(SUBSTRING_INDEX(tb_samples.codesample_samples, '/', 1) AS UNSIGNED)

        ");

        // dd($get_sample);



        // Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
        // ->orderBy('count_id','asc')
        //   // ->join('ms_sample_type', function ($join) {
        //   //   $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
        //   //     ->whereNull('ms_sample_type.deleted_at')
        //   //     ->whereNull('tb_samples.deleted_at');
        //   // })
        //   ->get();


        $prefixSample = $sample = '';

        foreach ($get_sample as $mytable) {
          $sample .= $prefixSample . $mytable->codesample_samples;
          $prefixSample = '<br><br>';
        }

        return $sample;
        // return Carbon::createFromFormat('Y-m-d H:i:s', $data->date_permohonan_uji)->isoFormat('D MMMM Y');;
      })
      ->addColumn("status_pembayaran", function ($data) {
        if ($data->status_pembayaran == 0) {
          $get_sample_rectal = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
            ->where('typesample_samples', 'ab516530-aed0-481b-ab9c-86c8ccbcabb3')
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select(
              'name_sample_type',
              'id_samples',
              'tb_samples.created_at'
            )
            ->distinct('typesample_samples')
            ->latest()
            ->get();

          $amountText = '';
          $inputDisabled = '';
          if ($data->status_pembayaran == 0){
            $amountText = '
                  <div class="form-group">
                      <p>Sudah terbayar: <b>' . rupiah($data->terbayar) . '</b></p>
                  </div>
            ';
            $placeholder = 'Kosongi jika terbayar lunas';
          }else{
            $inputDisabled = 'disabled';
            $placeholder = 'Lunas';
          }

          if (count($get_sample_rectal) > 0) {
            $text = '
                  <div class="form-group">
                      <p>Total permohonan uji: <b>' . rupiah($data->total_harga) . '</b></p>
                      <p>Total tambahan biaya tindakan Rectal Swab: <b>' . rupiah(20000) . '</b></p>
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga + 20000) . '</b></p>
                  </div>
              ';

            $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="20000" id="biaya-tindakan-rectal-swab" readonly>';
          } else {
            $text = '
                  <div class="form-group">
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga) . '</b></p>
                  </div>
              ';

            $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="0" id="biaya-tindakan-rectal-swab" readonly>';
          }
          $paymentMethod = $data->metode_pembayaran == 0 ? "Cash": "Transfer";

          $status_pembayaran = '
            <button type="button" class="btn btn-outline-danger" data-toggle="modal" style="padding:5px 10px !important; width:100%;" data-target="#pembayaran_' . $data->id_permohonan_uji . '" >
                        '.$paymentMethod.'
                    </button>

                    <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="' . route('elits-permohonan-uji.payment', [$data->id_permohonan_uji]) . '" method="POST">

                                    <div class="modal-body">

                                        ' . $text . '
                                        '. $amountText .'

                                        <input type="hidden" name="_token" value="' . csrf_token() . '">

                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">TELAH DITERIMA DARI :</label>
                                            <input type="text" class="form-control" name="recipient-name" value="' . $data->customer->name_customer . '" id="recipient-name">

                                            ' . $input . '
                                        </div>

                                        <div class="form-group">
                                            <label for="message-text" class="col-form-label">ALAMAT :</label>
                                            <textarea class="form-control" name="address" id="message-text">' . $data->customer->address_customer . '</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="amount" class="col-form-label">Terbayar :</label>
                                            <input type="number" class="form-control" name="amount" id="amount" placeholder="'.$placeholder.'"" '.$inputDisabled.'>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" '.$inputDisabled.'>Belum Lunas</button>
                                        <button type="submit" class="btn btn-success" >LUNAS</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            ';
        } else {
          $get_sample_rectal = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
            ->where('typesample_samples', 'ab516530-aed0-481b-ab9c-86c8ccbcabb3')
            ->join('ms_sample_type', function ($join) {
              $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
                ->whereNull('ms_sample_type.deleted_at')
                ->whereNull('tb_samples.deleted_at');
            })
            ->select(
              'name_sample_type',
              'id_samples',
              'tb_samples.created_at'
            )
            ->distinct('typesample_samples')
            ->latest()
            ->get();

          $amountText = '';
          $inputDisabled = '';
          if ($data->status_pembayaran == 0){
            $amountText = '
                  <div class="form-group">
                      <p>Sudah terbayar: <b>' . rupiah($data->terbayar) . '</b></p>
                  </div>
            ';
            $placeholder = 'Kosongi jika terbayar lunas';
          }else{
            $inputDisabled = 'disabled';
            $placeholder = 'LUNAS';
          }

          if (count($get_sample_rectal) > 0) {
            $text = '
                  <div class="form-group">
                      <p>Total permohonan uji: <b>' . rupiah($data->total_harga) . '</b></p>
                      <p>Total tambahan biaya tindakan Rectal Swab: <b>' . rupiah(20000) . '</b></p>
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga + 20000) . '</b></p>
                  </div>
              ';

            $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="20000" id="biaya-tindakan-rectal-swab" readonly>';
          } else {
            $text = '
                  <div class="form-group">
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga) . '</b></p>
                  </div>
              ';

            $input = '<input type="hidden" class="form-control" name="biaya_tindakan_rectal_swab" value="0" id="biaya-tindakan-rectal-swab" readonly>';
          }
          $status_pembayaran = '<button type="button" class="btn btn-outline-success" style="padding:5px 10px !important; width:100%;" data-toggle="modal" data-target="#pembayaran_' . $data->id_permohonan_uji . '">Terbayar</button>

            <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="' . route('elits-permohonan-uji.edit_payment', [$data->id_permohonan_uji]) . '" method="POST">

                                    <div class="modal-body">

                                        ' . $text . '
                                        '. $amountText .'

                                        <input type="hidden" name="_token" value="' . csrf_token() . '">

                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">TELAH DITERIMA DARI :</label>
                                            <input type="text" class="form-control" name="recipient-name" value="' . $data->nota_diterima_dari . '" id="recipient-name">

                                            ' . $input . '
                                        </div>

                                        <div class="form-group">
                                            <label for="message-text" class="col-form-label">ALAMAT :</label>
                                            <textarea class="form-control" name="address" id="message-text">' . $data->nota_address_from . '</textarea>
                                        </div>

                                        <div class="form-group">
                                            <label for="amount" class="col-form-label">Terbayar :</label>
                                            <input type="number" class="form-control" name="amount" id="amount" placeholder="'.$placeholder.'" '.$inputDisabled.'>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary" '.$inputDisabled.'>Belum Lunas</button>
                                        <button type="submit" class="btn btn-success" >LUNAS</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            ';
        }

//        $done_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
//          ->join('tb_pengesahan_hasil', function ($join) {
//            $join->on('tb_pengesahan_hasil.id_pengesahan_hasil', '=', DB::raw('(SELECT id_pengesahan_hasil FROM tb_pengesahan_hasil WHERE tb_pengesahan_hasil.sample_id = tb_samples.id_samples AND tb_pengesahan_hasil.deleted_at  is NULL AND tb_samples.deleted_at   is NULL  LIMIT 1)'))
//              ->whereNull('tb_pengesahan_hasil.deleted_at')
//              ->whereNull('tb_samples.deleted_at');
//          })->count();
        $validate_sample = Sample::query()
          ->where('permohonan_uji_id', '=', $data->id_permohonan_uji)
          ->join('tb_verification_activity_samples', function ($join) {
            $join->on('tb_verification_activity_samples.id_sample', '=', 'tb_samples.id_samples')
              ->where('tb_verification_activity_samples.id_verification_activity', '=', 5);
          })
          ->count();

        $all_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)->count();

        if ($all_sample > 0) {
          if ($validate_sample >= $all_sample) {
            $status_pembayaran = $status_pembayaran . '
              <br>
              <br>
              <button type="submit" class="btn btn-outline-success " style="padding:5px 10px !important;width:100%">Selesai</button>
              ';
          }
        }


        return $status_pembayaran;
      })
      ->addColumn('action', function ($data) {
        $readButton = '';
        $editButton = '';
        $deleteButton = '';

        if (getAction('read')) {
          $readButton = '<a href="' . route('elits-samples.index', [$data->id_permohonan_uji]) . '" class="dropdown-item" title="Lihat Daftar Sample">Lihat Daftar Sample</a> ';
        }

        if (getAction('update')) {
          $editButton = '<a href="' . route('elits-permohonan-uji.edit', [$data->id_permohonan_uji]) . '" class="dropdown-item" title="Edit">Edit</a> ';
        }

        if (getAction('delete')) {
          $deleteButton = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_permohonan_uji  . '" data-nama="' . $data->customer->name_customer . '" title="Hapus">Hapus</a> ';
        }

        $button = '<div class="dropdown show m-1">
                              <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                              Aksi
                              </a>

                              <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                  ' . $readButton . '

                                  ' . $editButton . '

                                  ' . $deleteButton . '
                              </div>
                          </div>';

        return $button;
      })
      ->addColumn('cetak', function ($data) {
        $get_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
          ->join('ms_sample_type', function ($join) {
            $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
              ->whereNull('ms_sample_type.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'name_sample_type',
            'id_samples',
            'tb_samples.created_at'
          )
          ->distinct('typesample_samples')
          ->latest()
          ->get();

        // generate button

        if (count($get_sample) > 0) {
          $cetakButton = '
                  <div class="dropdown show">
                      <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownPrintLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          <i class="fa fa-print" aria-hidden="true"></i> Cetak
                      </a>

                      <div class="dropdown-menu" aria-labelledby="dropdownPrintLink">


                          <a class="dropdown-item" href="' . route('elits-release.nota', [$data->id_permohonan_uji]) . '" target="__blank">Cetak Nota</a>

                      </div>
                  </div>
            ';
        } else {
          $cetakButton = '
              <button type="button" class="btn btn-success" disabled>
                <i class="fa fa-print" aria-hidden="true"></i> Cetak
              </button>
            ';
        }

        return  $cetakButton;
      })
      ->addColumn('count_sample_type', function ($data) {
        $get_sample = Sample::where('permohonan_uji_id', '=', $data->id_permohonan_uji)
          ->join('ms_sample_type', function ($join) {
            $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
              ->whereNull('ms_sample_type.deleted_at')
              ->whereNull('tb_samples.deleted_at');
          })
          ->select(
            'name_sample_type',
            'tb_samples.typesample_samples'
          )
          ->distinct('typesample_samples')
          ->get();

        $prefixSampleType = $sampleType = '';

        foreach ($get_sample as $mytable) {
          $sampleType .= $prefixSampleType . $mytable['name_sample_type'];
          $prefixSampleType = '<br><br>';
        }

        return $sampleType;
      })
      ->rawColumns(['code_permohonan_uji', 'customer_permohonan_uji','pemeriksaan', 'num_samples', 'status_pembayaran', 'action', 'cetak', 'count_sample_type'])
      ->setFilteredRecords($result["totalFilteredRecord"])
      ->setTotalRecords($result["totalDataRecord"])
      ->make(true);
    return $data_table;
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
    $count = PermohonanUji::where(DB::raw('YEAR(date_permohonan_uji)'), '=', date('Y'))->max('urutan_permohonan_uji');
    $users = User::all();
    $categories = Industry::all();

    $packets = Packet::where('id_packet', '!=', '0')->orderBy('created_at')->get();

    $containers = Container::where('id_container', '!=', '0')->get();
    $sampletypes = SampleType::orderBy('created_at')->get();

    // $code ='PU.NK/'.date("Ymd", time()).'/'.($count+1);
    $code = 'PU.NK/' . date("Ymd", time()) . '/' . str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);

    return view('masterweb::module.admin.laboratorium.permohonan-uji.add', compact('user', 'containers', 'packets', 'sampletypes', 'code', 'users', 'categories'));
    //get all menu public
  }




  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store2(Request $request)
  {
    $data = $request->all();

    // print_r($data);
    $validated = $request->validate([
      'code_permohonan_uji' => ['required'],
      'issend_samples' => ['required'],
      'customerAttributes' => ['required'],
      'name_personil' => ['required'],
      // 'date_done_estimation' => ['required'],
      'count_sample' => ['required'],
      'total_price_sample' => ['required'],
      'underpayment_sample' => ['required'],
      'down_payment_sample' => ['required'],
      'payment_samples' => ['required'],

    ]);

    $user = Auth()->user();
    $permohonan_uji = new PermohonanUji;

    $count = PermohonanUji::where(DB::raw('YEAR(date_permohonan_uji)'), '=', date('Y'))->max('urutan_permohonan_uji');

    $code = 'PU.NK/' . Carbon::createFromFormat('d/m/Y', $data["date"])->format('Ymd') . '/' . str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);

    //uuid
    $id_permohonan_uji = Uuid::uuid4()->toString();

    $permohonan_uji->id_permohonan_uji  = $id_permohonan_uji;
    $permohonan_uji->code_permohonan_uji = $code;
    $permohonan_uji->customer_id = $data["customerAttributes"];
    $permohonan_uji->name_personil = $data["name_personil"];

    if ($data["issend_samples"] != "on") {
      $permohonan_uji->pengambil_sample = 0;
      $permohonan_uji->date_get_sample = Carbon::createFromFormat('d/m/Y', $data["date_get_sample"])->format('Y-m-d H:i:s');
      $permohonan_uji->sample_condition = $data["condition_samples"];
      if ($data["condition_samples"] == '0') {
        $permohonan_uji->sample_condition_other = $data["condition_samples_others"];
      }
    } else {
      $permohonan_uji->pengambil_sample = 1;
      $permohonan_uji->date_sampling =  Carbon::createFromFormat('d/m/Y', $data["datesampling_samples"])->format('Y-m-d H:i:s');
      $permohonan_uji->packet_sampling = $data["packet_samples_sampling"];
      $permohonan_uji->status = 4;
    }
    $permohonan_uji->total_harga = $data["total_price_sample"];
    $permohonan_uji->uang_muka = $data["down_payment_sample"];
    $permohonan_uji->uang_muka = $data["down_payment_sample"];
    $permohonan_uji->sisa = $data["underpayment_sample"];
    // // $permohonan_uji->date_done_estimation = Carbon::createFromFormat('d/m/Y', $data["date_done_estimation"])->format('Y-m-d H:i:s');
    $permohonan_uji->pembayaran = $data["payment_samples"];
    if ($data["payment_samples"] == '0') {
      $permohonan_uji->pembayaran_lain = $data["payment_samples_others"];
    }

    $permohonan_uji->catatan = $data["note_sample"];

    if ($data["subcontract_samples"] == "true") {
      $permohonan_uji->subkontrak = 1;
      $permohonan_uji->name_subkontrak = $data["subcontract_name_samples"];
    } else {
      $permohonan_uji->subkontrak = 0;
    }



    // $permohonan_uji->date_done_estimation = $data["date_done_estimation"];

    $permohonan_uji->save();


    if ($data["issend_samples"] != "on") {


      foreach ($data["Allsamples"] as $sample_value) {
        $sample = new Sample;
        $uuid4 = Uuid::uuid4();


        // $datesampling_samples = Carbon::createFromFormat('d/m/Y', $sample_value['datesampling_samples'])->format('Y-m-d H:i:s');
        // $datelab_samples = Carbon::createFromFormat('d/m/Y', $sample_value['datesampling_samples'])->format('Y-m-d H:i:s');

        $sample->id_samples = $uuid4->toString();
        $sample->codesample_samples = $sample_value['code_samples'];
        $sample->code_sample_user = $sample_value['code_samples_user'];
        $sample->customer_samples =  $permohonan_uji->customer_id;
        $sample->permohonan_uji_id =  $permohonan_uji->id_permohonan_uji;
        $sample->issend_samples =   $permohonan_uji->pengambil_sample;
        $sample->packet_id = $sample_value['packet_samples'];
        $sample->cost_samples = $sample_value['cost_samples'];
        $sample->typesample_samples = $sample_value['typesample_samples'];
        $sample->wadah_samples = $sample_value['wadah_samples'];
        if ($sample->wadah_samples == '0') {
          $sample->wadah_samples_others = $sample_value['wadah_samples_others'];
        }
        $sample->weight_samples = $sample_value['weight_samples'];
        $sample->unit_samples = $sample_value['unitAttributes'];
        $sample->codenumber_samples = $sample_value['codenumber_samples'];
        $sample->typesample_samples = $sample_value['typesample_samples'];
        $sample->datelab_samples = $permohonan_uji->date_get_sample;
        $sample->save();


        if (isset($sample_value["packet_samples_others"]) && count($sample_value["packet_samples_others"]) > 0) {
          for ($i = 0; $i < count($sample_value["packet_samples_others"]); $i++) {
            $method = new SamplesMethod;
            $method->id = Uuid::uuid4();
            $method->id_samples = $sample->id_samples;
            $method->id_method = $sample_value["packet_samples_others"][$i];
            $method->save();
          }
        } else {
          // $packets = Packet::join('ms_packet_detail', function ($join) {
          //     $join->on('ms_packet_detail.packet_id', '=', 'ms_packet.id_packet')
          //     ->whereNull('ms_packet_detail.deleted_at')
          //     ->whereNull('ms_packet.deleted_at');
          // })
          // ->where('ms_packet.id_packet','=', $sample->packet_id )
          // ->get();

          $packet_samples_selected = $sample_value["packet_samples_selected"];
          foreach ($packet_samples_selected as $method_id) {
            $method = new SamplesMethod;
            $method->id = Uuid::uuid4();
            $method->id_samples = $sample->id_samples;
            $method->id_method = $method_id;
            $method->save();
          }
        }
      }
    } else {


      if ($data["packet_samples_sampling"] == '0') {
        foreach ($data["methodAttributes_sampling"] as $methodsampling_value) {
          $methodSampling = new MethodSampling;
          $uuid4 = Uuid::uuid4();
          $methodSampling->id_method_sampling = $uuid4;
          $methodSampling->permohonan_uji_id = $permohonan_uji->id_permohonan_uji;
          $methodSampling->method_id = $methodsampling_value;
          $methodSampling->save();
        }
      } else {
        foreach ($data["methodAttributes_sampling"] as $methodsampling_value) {
          $methodSampling = new MethodSampling;
          $uuid4 = Uuid::uuid4();
          $methodSampling->id_method_sampling = $uuid4;
          $methodSampling->permohonan_uji_id = $permohonan_uji->id_permohonan_uji;
          $methodSampling->method_id = $methodsampling_value;
          $methodSampling->save();
        }
      }
    }





    // $user = Auth()->user();
    // $sample = new Sample;
    // //uuid
    // $uuid4 = Uuid::uuid4();

    // $datesampling_samples = Carbon::createFromFormat('d/m/Y', $request->post('datesampling_samples'))->format('Y-m-d H:i:s');
    // $datelab_samples = Carbon::createFromFormat('d/m/Y', $request->post('datelab_samples'))->format('Y-m-d H:i:s');

    // $sample->id_samples = $uuid4->toString();
    // $sample->codesample_samples = $request->post('code_samples');
    // $sample->customer_samples = $request->post('customerAttributes');
    // if($request->post('packet_samples')!="0"){
    //     $sample->packet_id = $request->post('packet_samples');
    // }

    // if(!$request->post('issend_samples')=="on"){
    //     $sample->sender_samples = $request->post('sender_samples');
    //     $sample->issend_samples = 0;
    // }else{
    //     $sample->issend_samples = 1;
    // }
    // $sample->cost_samples = $request->post('cost_samples');
    // $sample->typesample_samples = $request->post('typesample_samples');
    // $sample->user_input_samples = $user->id;
    // $sample->wadah_samples = $request->post('wadah_samples');
    // if($sample->wadah_samples=='0'){
    //     $sample->wadah_samples_others = $request->post('wadah_samples_others');
    // }

    // $sample->weight_samples = $request->post('weight_samples');
    // $sample->unit_samples = $request->post('unitAttributes');
    // $sample->codenumber_samples = $request->post('codenumber_samples');
    // $sample->typesample_samples = $request->post('typesample_samples');
    // $sample->poinsample_lat_samples = $request->post('poinsample_lat_samples');
    // $sample->poinsample_long_samples = $request->post('poinsample_long_samples');
    // $sample->poinsample_samples = $request->post('poinsample_samples');
    // $sample->datesampling_samples = $datesampling_samples;
    // $sample->datelab_samples = $datelab_samples;

    //  $sample->save();


    //  if(isset ($data["packet_samples_others"]) && count($data["packet_samples_others"])>0){
    //     for($i = 0; $i < count($data["packet_samples_others"]);$i++ ) {
    //         $method = new SamplesMethod;
    //         $method->id= Uuid::uuid4();
    //         $method->id_samples=$sample->id_samples;
    //         $method->id_method=$data["packet_samples_others"][$i];
    //         $method->save();
    //     }
    // }else{
    //     $packets = Packet::join('ms_packet_detail', function ($join) {
    //         $join->on('ms_packet_detail.packet_id', '=', 'ms_packet.id_packet')
    //         ->whereNull('ms_packet_detail.deleted_at')
    //         ->whereNull('ms_packet.deleted_at');
    //     })
    //     ->where('ms_packet.id_packet','=', $sample->packet_id )
    //     ->get();
    //     foreach ($packets as $packet) {
    //         $method = new SamplesMethod;
    //         $method->id= Uuid::uuid4();
    //         $method->id_samples=$sample->id_samples;
    //         $method->id_method=$packet->method_id;
    //         $method->save();
    //     }
    // }


    return response()->json(
      [
        'success' => 'Ajax request submitted successfully',
        'data' => $data,
      ]
    );
    //return redirect()->route('user-client-management.index',[$request->get('client_id')])->with(['status'=>'User succesfully inserted','user'=>$user]);

  }

  public function store(Request $request)
  {
    $data = $request->all();



    $validated = $request->validate([
      'code_permohonan_uji' => ['required'],
    ]);

    $user = Auth()->user();
    $count = PermohonanUji::where(DB::raw('YEAR(date_permohonan_uji)'), '=', date('Y'))->max('urutan_permohonan_uji');

    $code = 'PU.NK/' . Carbon::createFromFormat('d/m/Y', $data["date"])->format('Ymd') . '/' . str_pad((int)($count + 1), 4, '0', STR_PAD_LEFT);

    if (isset($data["new_customer"])) {
      $customer = new Customer;
      //uuid
      $uuid4 = Uuid::uuid4();


      $customer->id_customer = $uuid4->toString();
      $customer->name_customer = $request->post('new_customer');
      $customer->address_customer = $request->post('new_address_customer');
      $customer->email_customer = $request->post('new_email_customer');
      // $customer->category_customer = $request->post('new_category_customer');
      $customer->cp_customer = $request->post('new_cp_customer');
      $kecamatan = $request->post('new_kecamatan');
      if ($kecamatan != '0') {
        $customer->kecamatan_customer = $request->post('new_kecamatan');
      } else {
        $customer->kecamatan_customer = $request->post('new_kecamatan_other');
      }
      $customer->save();
    }

    $permohonan_uji = new PermohonanUji;
    //uuid
    $id_permohonan_uji = Uuid::uuid4()->toString();

    $permohonan_uji_urutan = PermohonanUji::where(DB::raw('YEAR(date_permohonan_uji)'), '=', date('Y'))->max('urutan_permohonan_uji');

    $permohonan_uji->id_permohonan_uji  = $id_permohonan_uji;
    $permohonan_uji->code_permohonan_uji = $code;
    $permohonan_uji->pengirim_sample = $data["pengirim_sample"];
    $permohonan_uji->pdam_pengirim_sample = '';
    $permohonan_uji->petugas_penerima = $data["petugas_penerima"];
    $permohonan_uji->location_permohonan_uji = '';
    $permohonan_uji->urutan_permohonan_uji = $permohonan_uji_urutan + 1;
//    $permohonan_uji->address_customer_edited = $data["alamat_customer_edited"];
    // $permohonan_uji->date_done_estimation = Carbon::createFromFormat('d/m/Y', $data["date_done"])->format('Y-m-d H:i:s');

    //metode pembayaran
    $permohonan_uji->metode_pembayaran = $data["metode_pembayaran"];

    if (isset($data["customer"])) {
      $permohonan_uji->customer_id = $data["customer"];
    } else {
      $permohonan_uji->customer_id = $customer->id_customer;
    }
    $permohonan_uji->catatan = $data["catatan"];
    $permohonan_uji->name_sampling = $data["name_sampling"];
    $permohonan_uji->date_permohonan_uji = Carbon::createFromFormat('d/m/Y', $data["date"])->format('Y-m-d H:i:s');

    $save = $permohonan_uji->save();



    if ($save == true) {
      return response()->json(['status' => true, 'pesan' => "Data permohonan uji berhasil ditambahkan!", 'id' => $permohonan_uji->id_permohonan_uji], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data permohonan uji tidak berhasil ditambahkan!"], 400);
    }
    // return redirect()->route('elits-samples.create', [$permohonan_uji->id_permohonan_uji])->with(['status' => 'Permohonan Uji Berhasil di input']);
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
    $user = Auth()->user();
    $item_permohonan_uji = PermohonanUji::findOrFail($id);
    $users = User::all();

    $packets = Packet::where('id_packet', '!=', '0')->orderBy('created_at')->get();

    $containers = Container::where('id_container', '!=', '0')->get();
    $sampletypes = SampleType::orderBy('created_at')->get();


    return view('masterweb::module.admin.laboratorium.permohonan-uji.edit', [
      'user' => $user,
      'item_permohonan_uji' => $item_permohonan_uji,
      'users' => $users,
      'packets' => $packets,
      'containers' => $containers,
      'sampletypes' => $sampletypes
    ]);
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
    // dd($request);

    DB::beginTransaction();

    $post = PermohonanUji::findOrFail($id);
    // dd($request->post('date'));
    $code = 'PU.NK/' . Carbon::createFromFormat('d/m/Y', $request->post('date'))->format('Ymd') . '/' . str_pad((int)($post->urutan_permohonan_uji), 4, '0', STR_PAD_LEFT);

    $post->customer_id = $request->customer;

    $post->date_permohonan_uji = Carbon::createFromFormat('d/m/Y', $request->post('date'))->format('Y-m-d H:i:s');
    // $post->date_done_estimation = Carbon::createFromFormat('d/m/Y', $request->post('date_done'))->format('Y-m-d H:i:s');
    $post->code_permohonan_uji = $code;
    $post->pdam_pengirim_sample = $request->post("pdam_pengirim_sample");
    // $post->pengirim_sample = $request->post('pengirim_sample');
    $post->petugas_penerima = $request->post('petugas_penerima');
    $post->name_sampling = $request->post('name_sampling');
//    $post->address_customer_edited = $request->post('alamat_customer_edited');

    // $post->location_permohonan_uji = $request->post('location_permohonan_uji');
    $post->catatan = $request->post('catatan');

    try {
      $simpan = $post->save();

      DB::commit();

      if ($simpan == true) {
        return response()->json(['status' => true, 'pesan' => "Data permohonan uji berhasil diubah!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data permohonan uji tidak berhasil diubah!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => 'System gagal melakukan perubahan!'], 200);
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
    $data = PermohonanUji::findOrFail($id);
    $data->delete();
    $sample = Sample::where('permohonan_uji_id', $id);
    $sample->delete();



    // $all_sample = Sample::orderBy('count_id','ASC')->get();

    // dd($all_sample);

    // $i=1;
    // foreach ($all_sample as $sample) {
    //   # code...
    //   $sample->count_id=$i;
    //   $sample->count_id=$i;
    //   $sample->save();
    //   $i++;
    // }

    // $all_permohonan_uji = PermohonanUji::orderBy('urutan_permohonan_uji','ASC')->get();
    // $i=1;
    // foreach ($all_permohonan_uji as $permohonan_uji) {
    //   # code...
    //   $permohonan_uji->urutan_permohonan_uji=$i;
    //   $permohonan_uji->save();
    //   $i++;
    // }

    $lab_num = LabNum::where('permohonan_uji_id', $id);


    $lab_num_temp = LabNum::where('permohonan_uji_id', $id)->select('lab_id')->distinct('lab_id')->get();
    $lab_num->delete();

    foreach ($lab_num_temp as $lab){
      // sortingNumber($lab->lab_id, $lab->lab_number);
    }


    if ($data == true) {
      return response()->json(['status' => true, 'pesan' => "Data permohonan uji berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data permohonan uji tidak berhasil dihapus!"], 200);
    }
  }

  public function print($id)
  {
  }

  public function edit_payment($id, Request $request)
  {
    $data = $request->all();
    $user = Auth()->user();

    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();
    $permohonan_uji->nota_diterima_dari = $data['recipient-name'];
    $permohonan_uji->nota_address_from = $data['address'];

    $status = '';
    if (isset($request->amount)){
      $permohonan_uji->terbayar += $request->amount;
      $permohonan_uji->status_pembayaran = '0';
      $status = 'Pembayaran berhasil disimpan';
    }else{
      $permohonan_uji->status_pembayaran = '1';
      $status = 'Pembayaran Selesai';
    }

    $permohonan_uji->save();
    return redirect()->route('elits-permohonan-uji.index')->with(['status' => $status]);
  }

  public function payment($id, Request $request)
  {
    $data = $request->all();
    $user = Auth()->user();
    $max_nota = (int)PermohonanUji::where(DB::raw('YEAR(date_permohonan_uji)'), '=', date('Y'))->max('nomor_nota');

    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();
    $permohonan_uji->nomor_nota = $max_nota + 1;
    $permohonan_uji->nota_diterima_dari = $data['recipient-name'];
    $permohonan_uji->nota_petugas_penerima = $user->id;
    $permohonan_uji->nota_address_from = $data['address'];

    if ($request->biaya_tindakan_rectal_swab != 0) {
      $permohonan_uji->biaya_tindakan_rectal_swab = $request->biaya_tindakan_rectal_swab;
    } else {
      $permohonan_uji->biaya_tindakan_rectal_swab = null;
    }

    $status = '';
    if (isset($request->amount)){
      $permohonan_uji->terbayar += $request->amount;
      $permohonan_uji->status_pembayaran = '0';
      $status = 'Pembayaran berhasil disimpan';
    }else{
      $permohonan_uji->status_pembayaran = '1';
      $status = 'Pembayaran Selesai';
    }
    $permohonan_uji->save();

    return redirect()->route('elits-permohonan-uji.index')->with(['status' => $status]);
    //get auth user

    //get all menu public
  }

  public function analys($id, $id_method = null)
  {


    $auth = Auth()->user();

    $samples = Sample::where("id_samples", $id)->first();
    $customer = Customer::where("id_customer", $samples->customer_samples)->first();


    if ($auth->level == "3382abf2-8518-42f9-91e1-096f25da8ae8") {
      $method = SamplesMethod::join('ms_method', 'ms_method.id_method', '=', 'ms_samples_method.id_method')
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
      $method = SamplesMethod::where("id_samples", $id)
        ->leftJoin('ms_method', 'ms_method.id_method', '=', 'ms_samples_method.id_method')
        ->where('ms_method.deleted_at', '=', null)
        ->get();
    }



    return view('masterweb::module.admin.laboratorium.permohonan-uji.analys', compact('samples', 'method', 'customer', 'id_method'));
  }

  public function setPersiapanSample($id)
  {
    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();
    $permohonan_uji->status = 1;
    $permohonan_uji->date_sampling_prepare = Carbon::now()->format('Y-m-d H:i:s');
    $permohonan_uji->save();

    return redirect()->route('elits-permohonan-uji.index')->with(['status' => 'Proses Persiapan Sampel selesai']);
  }

  public function setSampling($id)
  {
    $permohonan_uji = PermohonanUji::where('id_permohonan_uji', '=', $id)->first();
    $permohonan_uji->status = 2;
    $permohonan_uji->date_sampling_done = Carbon::now()->format('Y-m-d H:i:s');
    $permohonan_uji->save();

    return redirect()->route('elits-permohonan-uji.index')->with(['status' => 'Proses Persiapan Sampel selesai']);
  }

  public function getPacketDetail($id)
  {
    if (isset($id)) {
      $packet_sample_detail = PacketDetail::where('packet_id', $id)
        ->join('ms_method', function ($join) {
          $join->on('ms_packet_detail.method_id', '=', 'ms_method.id_method')
            ->whereNull('ms_packet_detail.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })->get();
    } else {
      $packet_sample_detail = null;
    }

    return response()->json(array('success' => true, 'packet_sample_detail' => $packet_sample_detail));

    # code...
  }

  public function getIdSample($id)
  {
    $user = Auth()->user();
    $level = $user->getlevel;


    $packet_sample = SampleType::where('id_sample_type', $id)->join('ms_packet', function ($join) {
      $join->on('ms_sample_type.packet_id', '=', 'ms_packet.id_packet')
        ->whereNull('ms_sample_type.deleted_at')
        ->whereNull('ms_packet.deleted_at');
    })
      ->first();
    if (isset($packet_sample->id_packet)) {
      $packet_sample_detail = PacketDetail::where('packet_id', $packet_sample->id_packet)
        ->join('ms_method', function ($join) {
          $join->on('ms_packet_detail.method_id', '=', 'ms_method.id_method')
            ->whereNull('ms_packet_detail.deleted_at')
            ->whereNull('ms_method.deleted_at');
        })->get();
    } else {
      $packet_sample_detail = null;
    }
    // dd($packet_sample_detail);



    if ($level->level == "elits-dev" || $level->level == "LAB") {

      $samplecount = Sample::max('tb_samples.codenumber_samples');
      $samplecount = $samplecount + 1;
    } else {
      return response()->json([
        'success' => 'false',
        'errors'  => "Cannot Access",
      ], 400);
    }

    return response()->json(array('success' => true, 'samplecount' =>  $samplecount, 'packet_sample' => $packet_sample, 'packet_sample_detail' => $packet_sample_detail));
  }

  public function printLHU($id)
  {
    // dd("ygygyu");
    $sample = Sample::findOrFail($id);

    $customer = Customer::findOrFail($sample->customer_samples);
    $category = Industry::findOrFail($customer->category_customer);
    return view('masterweb::module.admin.laboratorium.permohonan-uji.printLHU', compact('sample', 'customer', 'category'));
  }

  public function printPermohonanUjiBlangko()
  {
    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.blangko', []);

    return $pdf->stream();
  }

  public function nota($id)
  {
    //    dd("ygygyu");
    $user = Auth()->user();
    $permohonan_uji = PermohonanUji::where("id_permohonan_uji", $id)
      ->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('ms_customer.deleted_at');
      })
      ->select('tb_permohonan_uji.*', 'ms_customer.*', 'tb_permohonan_uji.created_at as dibuat')
      ->first();




//    $methodNonPackets = Sample::where('permohonan_uji_id', '=', $id)
//      ->leftjoin('ms_packet', function ($join) {
//        $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
//          ->whereNull('ms_packet.deleted_at')
//          ->whereNull('tb_samples.deleted_at');
//      })
//      ->join('tb_sample_method', function ($join) {
//        $join->on('tb_samples.id_samples', '=', 'tb_sample_method.sample_id')
//          ->whereNull('tb_samples.deleted_at')
//          ->whereNull('tb_sample_method.deleted_at');
//      })
//      ->join('ms_method', function ($join) {
//        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
//          ->whereNull('ms_method.deleted_at')
//          ->whereNull('tb_sample_method.deleted_at');
//      })
//      ->whereNull('ms_packet.id_packet')
//      // ->orderBy('ms_packet.id_packet')
//      ->select('ms_method.id_method', 'tb_sample_method.price_method', 'ms_method.params_method', 'ms_method.price_total_method', DB::raw('count(*) as count_method'))
//      ->groupBy('ms_method.id_method', 'ms_method.params_method', 'ms_method.price_total_method')
//      // ->lists('total','id_method')
//      ->get();





    $value_items = array();
    $total = 0;
//    foreach ($methodNonPackets as $methodNonPacket) {
//      $methodNonPacket["name_item"] = $methodNonPacket["params_method"];
//      $methodNonPacket["count_item"] = $methodNonPacket["count_method"];
//      $methodNonPacket["price_item"] = $methodNonPacket["price_total_method"];
//      $methodNonPacket["total"] = $methodNonPacket["price_total_method"] * $methodNonPacket["count_method"];
//      array_push($value_items, $methodNonPacket);
//      $total = $total + $methodNonPacket["total"];
//    }


//    $methodPackets = Sample::where('permohonan_uji_id', '=', $id)
//    ->leftjoin('ms_packet', function ($join) {
//        $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
//            ->whereNull('ms_packet.deleted_at')
//            ->whereNull('tb_samples.deleted_at');
//    })
//    ->leftJoin('tb_sample_method', 'tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
//    ->whereNotNull('ms_packet.id_packet')
//    ->select(
//        'tb_samples.sample_type_group',
//        'tb_samples.created_at',
//        'ms_packet.id_packet',
//        'tb_samples.cost_samples',
//        'ms_packet.name_packet',
//        DB::raw('SUM(tb_sample_method.price_method) as price_total_packet'),
//        DB::raw('COUNT(DISTINCT CONCAT(tb_samples.created_at, "-", ms_packet.id_packet)) as count_method')  // Count unique combination of created_at and id_packet
//    )
//    ->groupBy(
//        'tb_samples.sample_type_group',
//        'tb_samples.created_at',
//        'ms_packet.id_packet',
//        'ms_packet.name_packet',
//    )
//    ->orderBy('ms_packet.id_packet', 'DESC')
//    ->get();

    $additionalMethods = DB::table('tb_samples')
      ->whereNull('tb_samples.deleted_at')
      ->where('permohonan_uji_id', '=', $id)
      ->join('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->leftJoin('ms_packet_detail', function ($join) {
        $join->on('ms_packet_detail.packet_id', '=', 'tb_samples.packet_id')
          ->on('ms_packet_detail.method_id', '=', 'tb_sample_method.method_id')
          ->whereNull('ms_packet_detail.deleted_at');
      })
      ->whereNull('ms_packet_detail.id_packet_detail')
      ->get();

    $mappedMethods = $additionalMethods->map(function($item) use ($id) {
      if ($item->method_id === null) {
        $methodInfo = DB::table('tb_sample_method')
          ->where('id_sample_method', $item->id_sample_method)
          ->leftJoin('ms_method', 'ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->select('tb_sample_method.method_id', 'ms_method.params_method as method_name')
          ->first();

        if ($methodInfo) {
          $item->method_id = $methodInfo->method_id;
          $item->method_name = $methodInfo->method_name;
        }
      } else {
        $methodName = DB::table('ms_method')
          ->where('id_method', $item->method_id)
          ->value('params_method');

        if ($methodName) {
          $item->method_name = $methodName;
        }
      }
      return $item;
    });

    $groupedMethods = $mappedMethods->groupBy('method_id');

    foreach ($groupedMethods as $methodId => $methods) {
      $firstMethod = $methods->first();
      $count = $methods->count();

      $price = $firstMethod->price_method ?? 0;
      $totalPrice = $price * $count;

      $additionalMethod = [
        "method_id" => $methodId,
        "method_name" => $firstMethod->method_name ?? null,
        "name_item" => $firstMethod->method_name ?? null,
        "count_item" => $count,
        "price_item" => $price,
        "total" => $totalPrice
      ];

      array_push($value_items, $additionalMethod);
      $total = $total + $totalPrice;
    }


// Data awal dari query Anda
//    $methodPackets = Sample::where('permohonan_uji_id', '=', $id)
//      ->leftjoin('ms_packet', function ($join) {
//        $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
//          ->whereNull('ms_packet.deleted_at')
//          ->whereNull('tb_samples.deleted_at');
//      })
//      ->leftJoin('tb_sample_method', 'tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
//      ->whereNotNull('ms_packet.id_packet')
//      ->select(
//        'tb_samples.sample_type_group',
//        'tb_samples.created_at',
//        'ms_packet.id_packet',
//        'tb_samples.cost_samples',
//        'ms_packet.name_packet',
//        DB::raw('SUM(tb_sample_method.price_method) as price_total_packet'),
//        DB::raw('COUNT(DISTINCT CONCAT(tb_samples.created_at, "-", ms_packet.id_packet)) as count_method')
//      )
//      ->groupBy(
//        'tb_samples.sample_type_group',
//        'tb_samples.created_at',
//        'ms_packet.id_packet',
//        'ms_packet.name_packet',
//      )
//      ->orderBy('ms_packet.id_packet', 'DESC')
//      ->get();

    $additionalMethodIds = $additionalMethods->pluck('id_sample_method')->toArray();
    $excludeCondition = empty($additionalMethodIds) ? '1=1' : 'tb_sample_method.id_sample_method NOT IN (' . implode(',', array_map('intval', $additionalMethodIds)) . ')';


    $methodPackets = Sample::where('permohonan_uji_id', '=', $id)
      ->leftjoin('ms_packet', function ($join) {
        $join->on('ms_packet.id_packet', '=', 'tb_samples.packet_id')
          ->whereNull('ms_packet.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->leftJoin('tb_sample_method', function ($join) {
        $join->on('tb_sample_method.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_method.deleted_at');
      })
      ->leftJoin('ms_packet_detail', function ($join) {
        $join->on('ms_packet_detail.packet_id', '=', 'ms_packet.id_packet')
          ->whereNull('ms_packet_detail.deleted_at');
      })
      ->whereNotNull('ms_packet.id_packet')
      ->select(
        'tb_samples.sample_type_group',
        'tb_samples.created_at',
        'ms_packet.id_packet',
        'tb_samples.cost_samples',
        'ms_packet.name_packet',
        DB::raw('COUNT(DISTINCT ms_packet_detail.id_packet_detail) as count_packet_detail'),
        DB::raw('COUNT(DISTINCT CASE
          WHEN ' . $excludeCondition . '
          THEN tb_sample_method.id_sample_method
          ELSE NULL
        END) as count_sample_method'),
        DB::raw('CASE
  WHEN COUNT(DISTINCT ms_packet_detail.id_packet_detail) >= COUNT(DISTINCT CASE
    WHEN ' . $excludeCondition . '
    THEN tb_sample_method.id_sample_method
    ELSE NULL
  END)
  THEN ms_packet.price_total_packet
  ELSE (
    SELECT SUM(sm.price_method)
    FROM tb_sample_method sm
    INNER JOIN tb_samples s ON sm.sample_id = s.id_samples
    WHERE s.created_at = tb_samples.created_at
    AND s.packet_id = ms_packet.id_packet
    AND s.deleted_at IS NULL
    AND sm.deleted_at IS NULL
    AND (' . $excludeCondition . ')
  )
END as price_total_packet'),
        DB::raw('COUNT(DISTINCT CONCAT(tb_samples.created_at, "-", ms_packet.id_packet)) as count_method')
      )
      ->groupBy(
        'tb_samples.sample_type_group',
        'tb_samples.created_at',
        'ms_packet.id_packet',
        'ms_packet.name_packet',
        'ms_packet.price_total_packet',
      )
      ->orderBy('ms_packet.id_packet', 'DESC')
      ->get();

    $groupedPackets = [];

    foreach ($methodPackets as $packet) {
      $key = $packet->name_packet . '|' . $packet->price_total_packet;

      if (isset($groupedPackets[$key])) {
        $groupedPackets[$key]['count_method'] += $packet->count_method;
      } else {
        $groupedPackets[$key] = [
          'sample_type_group' => $packet->sample_type_group,
          'created_at' => $packet->created_at,
          'id_packet' => $packet->id_packet,
          'cost_samples' => $packet->cost_samples,
          'name_packet' => $packet->name_packet,
          'count_packet_detail' => $packet->count_packet_detail,
          'count_sample_method' => $packet->count_sample_method,
          'price_total_packet' => $packet->price_total_packet,
          'count_method' => $packet->count_method
        ];
      }
    }

    $methodPackets = $groupedPackets;


//    $count = 1;
//    $sample_type_group = null;
//    $i = 0;
//
//    $date_create=null;
//    $packet_id=null;
//
//    foreach ($methodPackets as $methodPacket) {
//
//      if ($date_create!=$methodPacket-> created_at &&  $packet_id!=$methodPacket-> id_packet) {
//        # code...
//        if ($i == 0) {
//          $id_packet = $methodPacket['id_packet'];
//        }
//        if ($i == 0 && isset($methodPacket['sample_type_group'])) {
//          $id_packet = $methodPacket['id_packet'];
//          $methodPackets[$i]['count_method'] = 1;
//        }
//        if ($count = 1 && isset($methodPacket['sample_type_group'])) {
//          $id_packet = $methodPacket['id_packet'];
//          $methodPackets[$i]['count_method'] = 1;
//        }
//
//        if ($id_packet == $methodPacket['id_packet'] && isset($methodPacket['sample_type_group']) && $i != 0) {
//          // unset($methodPackets[$i]);
//          // $count++;
//          // $methodPackets[$i - 1]['count_method'] = $count;
//
//        } else {
//          $count = 1;
//        }
//        print_r( $count );
//        $i++;
//        $date_create=$methodPacket-> created_at;
//        $packet_id=$methodPacket-> id_packet;
//      }
//
//    }




    foreach ($methodPackets as $methodPacket) {

      $methodPacket["name_item"] = $methodPacket["name_packet"];
      $methodPacket["count_item"] = $methodPacket["count_method"];
      $methodPacket["price_item"] = $methodPacket["price_total_packet"];

//      if ($methodPacket['cost_samples'] == 0) {
//
//        $methodPacket["price_item"] = $methodPacket["price_total_packet"];
//        if ($methodPacket["price_total_packet"] == 0) {
//          $packet_details = PacketDetail::where('packet_id', $methodPacket["id_packet"])
//            ->join('ms_method', function ($join) {
//              $join->on('ms_method.id_method', '=', 'ms_packet_detail.method_id')
//                ->whereNull('ms_method.deleted_at')
//                ->whereNull('ms_packet_detail.deleted_at');
//            })->get();
//          $total = 0;
//          foreach ($packet_details as $packet_detail) {
//            # code...
//            $total = $total + $packet_detail->price_total_method;
//          }
//          $methodPacket["price_item"] = $total;
//        }
//      } else {
//        $methodPacket["price_item"] = $methodPacket['cost_samples'];
//      }

      $methodPacket["total"] = $methodPacket["price_item"] * $methodPacket["count_method"];
      array_push($value_items, $methodPacket);

      $total = $total +  $methodPacket["total"];

      $id_packet = $methodPacket['id_packet'];
    }


    $permohonan_uji->total_harga = $total;
    $permohonan_uji->save();
    // dd($total);

    // Tanggal Pemeriksaan
    $sample = Sample::query()->where('permohonan_uji_id', $id)->first();

    try {
      $tanggalPemeriksaan = VerificationActivitySample::query()
        ->where('id_sample', '=', $sample->id_samples)
        ->where('id_verification_activity', '=', 1)
        ->firstOrFail()
        ->stop_date;
    }catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      $tanggalPemeriksaan = null;
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji.nota', compact('value_items', 'user', 'permohonan_uji', 'tanggalPemeriksaan'));

    return $pdf->stream();

    /* return view('masterweb::module.admin.laboratorium.permohonan-uji.nota', compact('value_items', 'user', 'permohonan_uji')); */
  }

  public function permintaan_pemeriksaan($id)
  {

    //    dd("ygygyu");


    $user = Auth()->user();
    $permohonan_uji = PermohonanUji::where("id_permohonan_uji", $id)
      ->join('ms_customer', function ($join) {
        $join->on('ms_customer.id_customer', '=', 'tb_permohonan_uji.customer_id')
          ->whereNull('tb_permohonan_uji.deleted_at')
          ->whereNull('ms_customer.deleted_at');
      })
      ->select('tb_permohonan_uji.*', 'ms_customer.*', 'tb_permohonan_uji.created_at as dibuat')
      ->first();

    $samples = Sample::where('permohonan_uji_id', '=', $id)
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('ms_sample_type.*', 'tb_samples.*', 'tb_samples.created_at as dibuat')
      ->get();
    $samples_first = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('count_id', 'ASC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();
    $samples_last = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('count_id', 'DESC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();

    $sample_types = Sample::where('permohonan_uji_id', '=', $id)
      ->join('ms_sample_type', function ($join) {
        $join->on('ms_sample_type.id_sample_type', '=', 'tb_samples.typesample_samples')
          ->whereNull('ms_sample_type.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('ms_sample_type.*')
      ->distinct('ms_sample_type.id_sample_type')
      ->get();

    $sample_sampling_first = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('datesampling_samples', 'ASC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();
    $sample_sampling_last = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('datesampling_samples', 'DESC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();

    $sample_sending_first = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('date_sending', 'ASC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();
    $sample_sending_last = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('date_sending', 'DESC')

      ->select('tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();

    $sample_penerimaan_first = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('penerimaan_sample_date', 'ASC')
      ->join('tb_sample_penerimaan', function ($join) {
        $join->on('tb_sample_penerimaan.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_penerimaan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('tb_sample_penerimaan.*', 'tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();

    $sample_penerimaan_last = Sample::where('permohonan_uji_id', '=', $id)
      ->orderBy('penerimaan_sample_date', 'DESC')
      ->join('tb_sample_penerimaan', function ($join) {
        $join->on('tb_sample_penerimaan.sample_id', '=', 'tb_samples.id_samples')
          ->whereNull('tb_sample_penerimaan.deleted_at')
          ->whereNull('tb_samples.deleted_at');
      })
      ->select('tb_sample_penerimaan.*', 'tb_samples.*', 'tb_samples.created_at as dibuat')
      ->first();



    // $result=$result->getString();

    // dd($samples);

    $laboratoriums = Sample::where('permohonan_uji_id', '=', $id)
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
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->select('ms_laboratorium.id_laboratorium', 'ms_laboratorium.nama_laboratorium')
      ->distinct('id_laboratorium')
      ->get();


    // view on pdf
    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji.permintaan', compact('sample_penerimaan_first', 'sample_penerimaan_last', 'sample_sending_first', 'sample_sending_last', 'sample_sampling_first', 'sample_sampling_last', 'sample_types', 'samples_last', 'samples_first', 'id', 'user',  'permohonan_uji', 'samples', 'laboratoriums'));

    return $pdf->stream();

    // view on raw html
    /* return view('masterweb::module.admin.laboratorium.permohonan-uji.permintaan', compact('sample_penerimaan_first', 'sample_penerimaan_last', 'sample_sending_first', 'sample_sending_last', 'sample_sampling_first', 'sample_sampling_last', 'sample_types', 'samples_last', 'samples_first', 'id', 'user',  'permohonan_uji', 'samples', 'laboratoriums')); */
  }

  public function daily($date_from = null, $date_to = null)
  {

    $laboratoriummethods = SampleMethod::orderBy('ms_method.created_at')
      ->join('ms_method', function ($join) {
        $join->on('ms_method.id_method', '=', 'tb_sample_method.method_id')
          ->whereNull('tb_sample_method.deleted_at')
          ->whereNull('ms_method.deleted_at');
      })
      ->select('ms_method.params_method as id', DB::raw("count(ms_method.id_method) as count"))
      ->groupBy('ms_method.id_method')
      ->get();

    // ->join('assigned_tags', 'website_tags.id', '=', 'assigned_tags.tag_id')
    // ->select('website_tags.id as id', 'website_tags.title as title', DB::raw("count(assigned_tags.tag_id) as count"))
    // ->groupBy('website_tags.id')
    // ->get();
    // dd($laboratoriummethods);
    //    dd("ygygyu");

    $samples = Sample::where('permohonan_uji_id', '=', $id)
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
      ->select('ms_laboratorium.id_laboratorium', 'ms_laboratorium.nama_laboratorium', 'tb_samples.*', 'ms_sample_type.*', 'tb_sample_penerimaan.*', 'tb_sample_penanganan.*')
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
      ->select('tb_sample_penanganan.*', 'tb_sample_penerimaan.*', 'id_laboratorium', 'date_sending', 'date_analitik_sample', 'nama_laboratorium', 'name_sample_type', 'codesample_samples', 'id_samples')
      ->distinct('id_laboratorium')
      ->get();

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji.printLHU_all', compact('samples'));
    return $pdf->stream();
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
      $totalRecords = PermohonanUji::select('count(*) as allcount')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->count();

      $totalRecordswithFilter = PermohonanUji::select('count(*) as allcount')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->where('code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->count();

      // Fetch records
      $permohonan_uji_all = PermohonanUji::orderBy($columnName, $columnSortOrder)
        ->where('tb_permohonan_uji.code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->select('tb_permohonan_uji.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    } else {
      $totalRecords = PermohonanUji::select('count(*) as allcount')->count();
      $totalRecordswithFilter = PermohonanUji::select('count(*) as allcount')->where('code_permohonan_uji', 'like', '%' . $searchValue . '%')->count();

      // Fetch records
      $permohonan_uji_all = PermohonanUji::orderBy($columnName, $columnSortOrder)
        ->where('tb_permohonan_uji.code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->select('tb_permohonan_uji.*')

        ->skip($start)
        ->take($rowperpage)
        ->get();
    }


    $data_arr = array();

    $no = $start + 1;

    foreach ($permohonan_uji_all as $permohonan_uji) {
      $id_permohonan_uji = $permohonan_uji->id_permohonan_uji;
      $customer_permohonan_uji = $permohonan_uji->customer_id;
      $customer_permohonan_uji = Customer::where("id_customer", $customer_permohonan_uji)->first();
      $qr = QrCode::size(100)->generate(route('scan.verification', [$permohonan_uji->id_permohonan_uji]));
      $code_permohonan_uji = $permohonan_uji->code_permohonan_uji . '<br><br>' . $qr;

      $date_permohonan_uji = Carbon::createFromFormat('Y-m-d H:i:s', $permohonan_uji->date_permohonan_uji)->format('d/m/Y');


      if ($permohonan_uji->status_pembayaran == 0) {
        # code...\
        $status_pembayaran =
          '
                    <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#pembayaran_' . $permohonan_uji->id_permohonan_uji . '" >
                        Belum Terbayar
                    </button>

                    <div class="modal fade" id="pembayaran_' . $permohonan_uji->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="' . route('elits-permohonan-uji.payment', [$permohonan_uji->id_permohonan_uji]) . '" method="POST">

                                    <div class="modal-body">

                                        <div class="form-group">
                                            <p>
                                                Total yang harus dibayar : <b>' . rupiah($permohonan_uji->total_harga) . '</b>
                                            </p>
                                        </div>

                                        <input type="hidden" name="_token" value="' . csrf_token() . '">

                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">TELAH DITERIMA DARI :</label>
                                            <input type="text" class="form-control" name="recipient-name" value="' . $customer_permohonan_uji->name_customer . '" id="recipient-name">
                                        </div>
                                        <div class="form-group">
                                            <label for="message-text" class="col-form-label">ALAMAT :</label>
                                            <textarea class="form-control" name="address" id="message-text">' . $customer_permohonan_uji->address_customer . '</textarea>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success" >TERBAYAR</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                ';

        $btn_detail_sample = '';
        $btn_edit = '';
        $btn_delete = '';

        if (getAction('read')) {
          $btn_detail_sample = '<a class="dropdown-item" href="' . route('elits-samples.index', [$permohonan_uji->id_permohonan_uji]) . '">Lihat Daftar Sampel</a>';
        }

        if (getAction('update')) {
          $btn_edit = '<a class="dropdown-item" href="' . route('elits-permohonan-uji.edit', [$permohonan_uji->id_permohonan_uji]) . '">Edit</a>';
        }

        if (getAction('delete')) {
          $btn_delete = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $permohonan_uji->id_permohonan_uji  . '" data-nama="' . $permohonan_uji->customer->name_customer . '" title="Hapus">Hapus</a>';
        }

        $action = '

                <div class="dropdown show">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Aksi
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        ' . $btn_detail_sample . '
                        ' . $btn_edit . '
                        ' . $btn_delete . '
                    </div>
                </div>';


        // $print='

        // <div class="dropdown show">
        //     <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownPrintLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        //         <i class="fa fa-print" aria-hidden="true"></i> Cetak
        //     </a>

        //     <div class="dropdown-menu" aria-labelledby="dropdownPrintLink">
        //        <a class="dropdown-item" href="'.route('elits-release.permintaan-pemeriksaan',[$permohonan_uji->id_permohonan_uji]).'">Cetak Permintaan Pemeriksaan</a>
        //     </div>
        // </div>
        // ';

      } else {
        $status_pembayaran =
          '
                    <button type="button" class="btn btn-outline-success" >
                        Terbayar
                    </button>
                ';
      }

      $btn_detail_sample = '';
      $btn_edit = '';
      $btn_delete = '';

      if (getAction('read')) {
        $btn_detail_sample = '<a class="dropdown-item" href="' . route('elits-samples.index', [$permohonan_uji->id_permohonan_uji]) . '">Lihat Daftar Sampel</a>';
      }

      if (getAction('update')) {
        $btn_edit = '<a class="dropdown-item" href="' . route('elits-permohonan-uji.edit', [$permohonan_uji->id_permohonan_uji]) . '">Edit</a>';
      }

      if (getAction('delete')) {
        $btn_delete = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $permohonan_uji->id_permohonan_uji  . '" data-nama="' . $permohonan_uji->customer->name_customer . '" title="Hapus">Hapus</a>';
      }

      $action = '

            <div class="dropdown show">
                <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Aksi
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                ' . $btn_detail_sample . '
                ' . $btn_edit . '
                ' . $btn_delete . '
                </div>
            </div>';


      $print = '

            <div class="dropdown show">
                <a class="btn btn-success dropdown-toggle" href="#" role="button" id="dropdownPrintLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-print" aria-hidden="true"></i> Cetak
                </a>

                <div class="dropdown-menu" aria-labelledby="dropdownPrintLink">

                    <a class="dropdown-item" href="' . route('elits-release.permintaan-pemeriksaan', [$permohonan_uji->id_permohonan_uji]) . '">Cetak Permintaan Pemeriksaan</a>
                    <a class="dropdown-item" href="' . route('elits-release.nota', [$permohonan_uji->id_permohonan_uji]) . '">Cetak Nota</a>

                </div>
            </div>
            ';






      $data_arr[] = array(
        "number" => $no,
        "code_permohonan_uji" => $code_permohonan_uji,
        "id_permohonan_uji" => $id_permohonan_uji,
        "action" => $action,
        "print" => $print,
        'status_pembayaran' => $status_pembayaran,
        // "user_samples" => $user_samples,
        "customer_permohonan_uji" => $customer_permohonan_uji->name_customer,
        "date_permohonan_uji" => $date_permohonan_uji,
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

  public function getSamplePagination2(Request $request)
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
      $totalRecords = PermohonanUji::select('count(*) as allcount')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->count();

      $totalRecordswithFilter = PermohonanUji::select('count(*) as allcount')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->where('code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->count();

      // Fetch records
      $permohonan_uji_all = PermohonanUji::orderBy($columnName, $columnSortOrder)
        ->where('tb_permohonan_uji.code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->where('tb_permohonan_uji.status', '=', '0')
        ->select('tb_permohonan_uji.*')
        ->skip($start)
        ->take($rowperpage)
        ->get();
    } else {
      $totalRecords = PermohonanUji::select('count(*) as allcount')->count();
      $totalRecordswithFilter = PermohonanUji::select('count(*) as allcount')->where('code_permohonan_uji', 'like', '%' . $searchValue . '%')->count();

      // Fetch records
      $permohonan_uji_all = PermohonanUji::orderBy($columnName, $columnSortOrder)
        ->where('tb_permohonan_uji.code_permohonan_uji', 'like', '%' . $searchValue . '%')
        ->select('tb_permohonan_uji.*')

        ->skip($start)
        ->take($rowperpage)
        ->get();
    }


    $data_arr = array();

    $no = $start + 1;

    foreach ($permohonan_uji_all as $permohonan_uji) {
      $id_permohonan_uji = $permohonan_uji->id_permohonan_uji;
      $customer_permohonan_uji = $permohonan_uji->customer_id;
      $customer_permohonan_uji = Customer::where("id_customer", $customer_permohonan_uji)->first();
      $qr = QrCode::size(100)->generate($permohonan_uji->code_permohonan_uji);
      $code_permohonan_uji = $permohonan_uji->code_permohonan_uji . '<br><br>' . $qr;

      $date_create = Carbon::createFromFormat('Y-m-d H:i:s', $permohonan_uji->created_at)->format('d/m/Y');



      if ($permohonan_uji->status == '0') {


        $status =
          '
                        <button type="button" class="btn btn-outline-warning" data-toggle="modal" data-target="#exampleModalCenter_' . $permohonan_uji->id_permohonan_uji . '">
                            <i class="fa fa-filter" aria-hidden="true"></i>
                            Proses Persiapan Sampling
                        </button>

                        <div class="modal fade" id="exampleModalCenter_' . $permohonan_uji->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                        <form action="' . route('elits-permohonan-uji.setPersiapanSample', [$permohonan_uji->id_permohonan_uji]) . '" method="POST">
                            <input type="hidden" name="_token" value="' . csrf_token() . '">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle"> Proses Persiapan Sampling</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">

                                        <h5>
                                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                                        </h5>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" name="defaultCheck1" type="checkbox" value="" required id="defaultCheck1">
                                                <label class="form-check-label" for="defaultCheck1">
                                                Penyiapan Alat dan bahan 1
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" name="defaultCheck2" type="checkbox" value="" required id="defaultCheck2">
                                                <label class="form-check-label" for="defaultCheck2">
                                                Penyiapan Alat dan bahan 2
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" name="defaultCheck3" type="checkbox" value="" required id="defaultCheck3">
                                                <label class="form-check-label" for="defaultCheck3">
                                                Penyiapan Alat dan bahan 2
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" name="defaultCheck4" type="checkbox" value="" required id="defaultCheck4">
                                                <label class="form-check-label" for="defaultCheck4">
                                                Penyiapan Alat dan bahan 4
                                                </label>
                                            </div>
                                        </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <input type="submit" class="btn btn-primary" value="Submit"/>
                                </div>
                            </div>
                        </div>
                         </form>
                        </div>


                    ';
      } else if ($permohonan_uji->status == '1') {

        if (isset($permohonan_uji->packet_sampling)) {
          $packet = Packet::where('id_packet', '=', $permohonan_uji->packet_sampling)
            ->first();


          if ($packet->name_packet == "Pencahayaan") {
            $status =
              '<a href="/elits-pencahayaan/' . $permohonan_uji->id_permohonan_uji . '">
                            <button type="button" class="btn btn-outline-primary">
                                <i class="fa fa-shower" aria-hidden="true"></i>
                                Proses Pembacaan Data
                            </button>
                        </a>';
          } else if ($packet->name_packet == "Kebisingan") {
            $status =
              '<a href="/elits-kebisingan/' . $permohonan_uji->id_permohonan_uji . '">
                            <button type="button" class="btn btn-outline-primary">
                                <i class="fa fa-assistive-listening-systems" aria-hidden="true"></i>
                                Proses Pembacaan Data
                            </button>
                        </a>';
          } else {
            // $status=
            // '<a href="/elits-sampling/'.$permohonan_uji->id_permohonan_uji.'">
            //     <button type="button" class="btn btn-outline-primary">
            //         <i class="fa fa-filter" aria-hidden="true"></i>
            //         Proses Sampling
            //     </button>
            // </a>';

            $status =
              '




                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#exampleModalCenter_' . $permohonan_uji->id_permohonan_uji . '">
                                <i class="fa fa-filter" aria-hidden="true"></i>
                                Proses Sampling
                            </button>

                            <div class="modal fade" id="exampleModalCenter_' . $permohonan_uji->id_permohonan_uji . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="' . route('elits-permohonan-uji.setPersiapanSample', [$permohonan_uji->id_permohonan_uji]) . '" method="POST">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLongTitle"> Proses Sampling</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                            <h5>
                                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                                            </h5>
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="defaultCheck1" type="checkbox" value="" required id="defaultCheck1">
                                                    <label class="form-check-label" for="defaultCheck1">
                                                    Penyiapan Alat dan bahan 1
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="defaultCheck2" type="checkbox" value="" required id="defaultCheck2">
                                                    <label class="form-check-label" for="defaultCheck2">
                                                    Penyiapan Alat dan bahan 2
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="defaultCheck3" type="checkbox" value="" required id="defaultCheck3">
                                                    <label class="form-check-label" for="defaultCheck3">
                                                    Penyiapan Alat dan bahan 2
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" name="defaultCheck4" type="checkbox" value="" required id="defaultCheck4">
                                                    <label class="form-check-label" for="defaultCheck4">
                                                    Penyiapan Alat dan bahan 4
                                                    </label>
                                                </div>
                                            </div>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <input type="submit" class="btn btn-primary" value="Submit"/>
                                    </div>
                                </div>
                            </div>
                            </form>
                            </div>



                        ';
          }
        }
      } else if ($permohonan_uji->status == '2') {


        $all_sample = Sample::where('permohonan_uji_id', $permohonan_uji->id_permohonan_uji)->get();
        $sample_text = '';
        foreach ($all_sample as $sample) {
          $sample_text = $sample_text . '<a class="dropdown-item" href="/elits-deligations/' . $sample->id_samples . '">' . $sample->codesample_samples . '</a>';
        }

        $status =
          '
                <button class="btn btn-warning dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Proses Delegation
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    ' . $sample_text . '
                </div>';
      }
      //     else if($samples->status=='2'){
      //         $status=
      //         '<a href="/elits-samples/analys/'.$samples->id_samples.'">
      //             <button type="button" class="btn btn-outline-light">
      //                 <i class="fa fa-users" aria-hidden="true"></i>
      //                 Proses Invoice
      //             </button>
      //         </a>';


      //     }else{
      //         $status=
      //         '<a href="/elits-release/printLHU/'.$samples->id_samples.'">
      //             <button type="button" class="btn btn-outline-success">
      //                 <i class="fa fa-users" aria-hidden="true"></i>
      //                 Rilis Sample
      //             </button>
      //         </a>';


      //     }


      $data_arr[] = array(
        "number" => $no,
        "code_permohonan_uji" => $code_permohonan_uji,
        "id_permohonan_uji" => $id_permohonan_uji,
        // "user_samples" => $user_samples,
        "customer_permohonan_uji" => $customer_permohonan_uji->name_customer,
        "status" => $status,
        "date_create" => $date_create,
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
}
