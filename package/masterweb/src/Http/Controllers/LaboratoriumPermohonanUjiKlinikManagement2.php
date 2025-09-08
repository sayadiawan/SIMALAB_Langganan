<?php

namespace Smt\Masterweb\Http\Controllers;

use DateTime;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Illuminate\Support\Facades\Log;
use PDF;
use Exception;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smt\Masterweb\Helpers\DateHelper;
use Smt\Masterweb\Helpers\SatuSehatHelper;
use Smt\Masterweb\Helpers\Smt;
use Smt\Masterweb\Models\LHU;
use Smt\Masterweb\Models\Petugas;
use Smt\Masterweb\Models\Unit;
use \Smt\Masterweb\Models\User;
use Smt\Masterweb\Models\Pasien;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Smt\Masterweb\Models\BakuMutu;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Smt\Masterweb\Models\ParameterPaketExtra;
use Smt\Masterweb\Models\ParameterSubPaketExtra;
use Smt\Masterweb\Models\PermohonanUjiKlinik;
use Smt\Masterweb\Models\ParameterJenisKlinik;
use Smt\Masterweb\Models\ParameterPaketKlinik;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;
use Smt\Masterweb\Models\ParameterSatuanKlinik;
use Smt\Masterweb\Models\ParameterSubSatuanKlinik;
use Smt\Masterweb\Models\PermohonanUjiPaketKlinik;
use Smt\Masterweb\Models\ParameterPaketJenisKlinik;
use Smt\Masterweb\Models\PermohonanUjiAnalisKlinik;
use Smt\Masterweb\Models\PermohonanUjiJenisKlinik2;
use Smt\Masterweb\Models\PermohonanUjiPaketKlinik2;
use Smt\Masterweb\Models\PermohonanUjiPaketExtraKlinik;
use Smt\Masterweb\Models\PermohonanUjiSubPaketExtraKlinik;
use Smt\Masterweb\Models\ParameterSatuanPaketKlinik;
use Smt\Masterweb\Models\PermohonanUjiPaymentKlinik;
use Smt\Masterweb\Models\VerificationActivitySample;
use Smt\Masterweb\Models\PermohonanUjiParameterKlinik;
use Smt\Masterweb\Models\BakuMutuDetailParameterKlinik;
use Smt\Masterweb\Models\PermohonanUjiParameterKlinik2;
use Smt\Masterweb\Models\PermohonanUjiSubParameterKlinik;
use Smt\Masterweb\Models\SatuSehatLocation;
use Smt\Masterweb\Models\StartNum;

use ESignBSrE;

use Smt\Masterweb\Models\SatuSehatPractitioner;

use Smt\Masterweb\Models\VerificationActivity;
use Smt\Masterweb\Models\NumberKlinik;


use Illuminate\Support\Facades\File;

// use AamDsam\Bpjs\PCare;
use App\Exports\EsignBsre as ExportsEsignBsre;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Writer\Pdf as WriterPdf;
use Smt\Masterweb\Models\SatuSehat;


use Illuminate\Support\Facades\Http;

use Awageeks\Bpjs\PCare\Pendaftaran;
use Awageeks\Bpjs\PCare\Peserta;

class LaboratoriumPermohonanUjiKlinikManagement2 extends Controller
{
  protected $satuSehatHelper;
  public function __construct(SatuSehatHelper $satuSehatHelper)
  {
    $this->middleware('auth');
    $this->satuSehatHelper = $satuSehatHelper;
  }

  public function pcare_conf(){
      $config = [
              'cons_id'      => env('BPJS_PCARE_CONSID'),
              'secret_key'   => env('BPJS_PCARE_SCREET_KEY'),
              'username'     => env('BPJS_PCARE_USERNAME'),
              'password'     => env('BPJS_PCARE_PASSWORD'),
              'app_code'     => env('BPJS_PCARE_APP_CODE'),
              'base_url'     => env('BPJS_PCARE_BASE_URL'),
              'service_name' => env('BPJS_PCARE_SERVICE_NAME'),
      ];
      return $config;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    // dd();
    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.pagination');
  }

  public function rules($request)
  {
    $rule = [
      'noregister_permohonan_uji_klinik' => 'required',
      'tglregister_permohonan_uji_klinik' => 'required',
      // 'pasien_permohonan_uji_klinik' => 'required',
      // 'tgl_sampling_permohonan_uji_klinik' => 'required',
      // 'jam_sampling_permohonan_uji_klinik' => 'required',
    ];

    $pesan = [
      'noregister_permohonan_uji_klinik.required' => 'No register permohonan uji klinik tidak boleh kosong!',
      'tglregister_permohonan_uji_klinik.required' => 'Tanggal register permohonan uji klinik tidak boleh kosong!',
      // 'pasien_permohonan_uji_klinik.required' => 'Pasien permohonan uji klinik tidak boleh kosong!',
      // 'tgl_sampling_permohonan_uji_klinik.required' => 'Tanggal sampling tidak boleh kosong!',
      // 'jam_sampling_permohonan_uji_klinik.required' => 'Nama pengirim tidak boleh kosong!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function rules_pasien($request)
  {
    $rule = [
      // 'nik_pasien' => 'required|string|min:16|max:16',
      'nama_pasien' => 'required|string|min:3|max:255',
      'tgllahir_pasien' => 'required',
      // 'phone_pasien' => 'required',
    ];

    $pesan = [
      'nik_pasien.required' => 'NIK pasien tidak boleh kosong dan harus 16 digit!',
      'nama_pasien.required' => 'Nama pasien tidak boleh kosong!',
      'tgllahir_pasien.required' => 'Tanggal lahir pasien tidak boleh kosong!',
      // 'phone_pasien.required' => 'Nomor telepon tidak boleh kosong!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function rules_pasien_satusehat($request)
  {
    $rule = [
      'nik_pasien' => 'required|string|min:16|max:16',

    ];

    $pesan = [
      'nik_pasien.required' => 'NIK pasien tidak boleh kosong dan harus 16 digit!',

    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function data_permohonan_uji_klinik(Request $request)
  {
    if (Auth::user()->getlevel->level == 'DKTR') {
      $query = PermohonanUjiKlinik2::orderBy('nourut_permohonan_uji_klinik', 'desc')->whereIn('status_permohonan_uji_klinik', ['SELESAI']);

      if ($request->is_filter == '1') {
        $query->where('is_prolanis_gula', 1);

        if ($request->filter_prolanis_gula && $request->filter_prolanis_gula != 'all') {
          $query->where('id_permohonan_uji_klinik_prolanis', $request->filter_prolanis_gula); // Filter berdasarkan prolanis gula yang dipilih
        }
      } elseif ($request->is_filter == '2') {
        $query->where('is_prolanis_urine', 1);

        if ($request->filter_prolanis_urine && $request->filter_prolanis_urine != 'all') {
          $query->where('id_permohonan_uji_klinik_prolanis', $request->filter_prolanis_urine);
        }
      } elseif ($request->is_filter == '3') {
        $query->where('is_haji', 1);

        if ($request->filter_haji && $request->filter_haji != 'all') {
          $query->where('id_permohonan_uji_klinik_haji', $request->filter_haji);
        }
      } elseif ($request->is_filter == '0') {

        $query->where('is_prolanis_gula', 0)->where('is_prolanis_urine', 0)->where('is_haji', 0)->where('tipe_pemeriksaan_prolanis', '');
      } else {
        $query->whereIn('is_prolanis_gula', [0, 1, 2, 3]);
      }
    } else {
      $query = PermohonanUjiKlinik2::orderBy('tb_permohonan_uji_klinik_2.created_at', 'desc');

      if ($request->is_filter == '1') {
        $query->where('is_prolanis_gula', 1);

        if ($request->filter_prolanis_gula && $request->filter_prolanis_gula != 'all') {
          $query->where('id_permohonan_uji_klinik_prolanis', $request->filter_prolanis_gula)->orWhereNotNull('tipe_pemeriksaan_prolanis'); // Filter berdasarkan gula yang dipilih
        }
      } elseif ($request->is_filter == '2') {
        $query->where('is_prolanis_urine', 1);

        if ($request->filter_prolanis_urine && $request->filter_prolanis_urine != 'all') {
          $query->where('id_permohonan_uji_klinik_prolanis', $request->filter_prolanis_urine);
        }
      } elseif ($request->is_filter == '4') {
        $query->where('id_permohonan_uji_klinik_prolanis', '!=', null)->orWhereNotNull('tipe_pemeriksaan_prolanis');

        if ($request->filter_prolanis && $request->filter_prolanis != 'all') {
          $query->where('id_permohonan_uji_klinik_prolanis', $request->filter_prolanis);
        }
      } elseif ($request->is_filter == '3') {
        $query->where('is_haji', 1);

        if ($request->filter_haji && $request->filter_haji != 'all') {
          $query->where('id_permohonan_uji_klinik_haji', $request->filter_haji);
        }
      } elseif ($request->is_filter == '0') {
        $query->where('is_prolanis_gula', 0)->where('is_prolanis_urine', 0)->where('is_haji', 0)->whereNull('tipe_pemeriksaan_prolanis');
        $query->whereIn('is_prolanis_gula', [0, 1, 2, 3]);
      }

    }

    $query = $query->join('ms_pasien', 'tb_permohonan_uji_klinik_2.pasien_permohonan_uji_klinik', '=', 'ms_pasien.id_pasien');
    $query = $query->select(
      'tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.nourut_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.noregister_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.tglregister_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.pasien_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.jenis_spesimen',
      'tb_permohonan_uji_klinik_2.tglpengambilan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.tglpengujian_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.nama_dokter_pengirim_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.hp_dokter_pengirim_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.nama_perwakilan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.tanggal_lahir_perwakilan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.gender_perwakilan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.alamat_perwakilan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.vena_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.tgl_sampling_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.jam_sampling_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.plebotomist_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.lokasi_lain_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.penanganan_hasil_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.catatan_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.riwayat_obat_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.spesimen_darah_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.spesimen_urine_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.status_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.analisnip_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.dokter_rekomendasi_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.status_pembayaran',
      'tb_permohonan_uji_klinik_2.metode_pembayaran',
      'tb_permohonan_uji_klinik_2.total_harga_permohonan_uji_klinik',
      'tb_permohonan_uji_klinik_2.tipe_pemeriksaan_prolanis',
      'tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik_prolanis',
      'tb_permohonan_uji_klinik_2.is_prolanis_gula',
      'tb_permohonan_uji_klinik_2.is_prolanis_urine',
      'tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik_haji',
      'tb_permohonan_uji_klinik_2.is_haji',
      'ms_pasien.nama_pasien as nama_pasien',
      'ms_pasien.no_rekammedis_pasien as no_rekammedis'
    );

    $limit_val = $request->input('length');
    $start = $request["start"];
    $search = $request["search"];
    $request["start"] = 0;

    $datas = $query;

    $totalDataRecord = $this->loadDataSearch($datas, $search);
    $totalDataRecord = $totalDataRecord->count();
    $totalFilteredRecord = $totalDataRecord;

    $datas = $query->offset($start)->limit($limit_val);
    $datas = $this->loadDataSearch($datas, $search);
    $datas = $datas->get();

    return Datatables::of($datas)
      ->filter(function ($instance) use ($request) {
        if (!empty($request->get('search'))) {
          $instance->collection = $instance->collection->filter(function ($row) use ($request) {
            if (Str::contains(Str::lower($row['noregister_permohonan_uji_klinik']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['no_rekammedis']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['nama_pasien']), Str::lower($request->get('search')))) {
              return true;
            } else if (Str::contains(Str::lower($row['status_permohonan']), Str::lower($request->get('search')))) {
              return true;
            }

            return false;
          });
        }
      })
      ->addColumn('action', function ($data) use ($request) {
        $parameterButton = '';
        $verifikasiButton = '';
        $editButton = '';
        $deleteButton = '';

        if (getSpesialAction($request->segment(1), 'parameter-klinik', '')) {
          $parameterButton = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.permohonan-uji-klinik-parameter', $data->id_permohonan_uji_klinik) . '" title="Masukkan Parameter">Parameter</a> ';
        }

        if (getAction('read')) {
          $verifikasiButton = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.verification', $data->id_permohonan_uji_klinik) . '" title="Verifikasi">Verifikasi</a> ';
        }

        if (getAction('read')) {
          $editButton = '<a href="' . route('elits-permohonan-uji-klinik-2.edit', $data->id_permohonan_uji_klinik) . '" class="dropdown-item" title="Edit">Edit</a> ';
        }

        if (getAction('delete')) {
          $deleteButton = '<a class="dropdown-item btn-hapus" href="#hapus" data-id="' . $data->id_permohonan_uji_klinik . '" data-nama="' . $data->noregister_permohonan_uji_klinik . '" title="Hapus">Hapus</a> ';
        }

        $button = '<div class="dropdown show m-1">
                            <a class="btn btn-fw btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Aksi
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">


                                ' . $parameterButton . '

                                ' . $verifikasiButton . '


                                ' . $editButton . '

                                ' . $deleteButton . '
                            </div>
                        </div>';

        // BUTTON PRINT
        $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $data->id_permohonan_uji_klinik)
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->orderBy('name_parameter_satuan_klinik', 'asc')
              ->whereNull('deleted_at');
          })
          ->get();

        $printFormulirKlinik = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-formulir', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Formulir Uji Klinik">Print Informed Consent</a> ';

        $printkartuMedis = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-kartu-medis', $data->id_permohonan_uji_klinik) . '" title="Print Kartu Medis" target="__blank">Print Kartu Medis</a> ';

        if (count($data_permohonan_uji_parameter_klinik) > 0) {
          $printNota = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-nota', $data->id_permohonan_uji_klinik) . '" title="Print Nota" target="__blank">Print Nota</a> ';
        } else {
          $printNota = '';
        }

        $printHasilKlinik = '<a class="dropdown-item pointer" data-href="' . route('elits-permohonan-uji-klinik-2.print-permohonan-uji-klinik-hasil', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Hasil Klinik" data-toggle="modal" data-target="#signOptionModal">Print Hasil Klinik</a> ';

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

        if ($data->is_haji == 1) {
          $printAmplopHaji = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-amplop', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Amplop Haji">Print Amplop Haji</a> ';
        } else {
          $printAmplopHaji = '';
        }

        if ($data->id_permohonan_uji_klinik_prolanis != null) {
          if ($data->is_prolanis_gula == 1) {
              $printAmplopProlanisGula = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-amplop-prolanis', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Amplop Prolanis Gula">Print Amplop Prolanis Gula</a> ';
          } else {
              $printAmplopProlanisGula = '';
          }

          if ($data->is_prolanis_urine == 1) {
              $printAmplopProlanisUrine = '<a class="dropdown-item" href="' . route('elits-permohonan-uji-klinik-2.print-amplop-prolanis', $data->id_permohonan_uji_klinik) . '" target="__blank" title="Print Amplop Prolanis Urine">Print Amplop Prolanis Urine</a> ';
          } else {
              $printAmplopProlanisUrine = '';
          }
        } else {
            $printAmplopProlanisGula = '';
            $printAmplopProlanisUrine = '';
        }

        $buttonPrint = '<div class="dropdown show m-1">
                            <a class="btn btn-fw btn-light dropdown-toggle" href="#" role="button" id="dropdownMenuLinkPrint" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Print
                            </a>

                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                ' . $printFormulirKlinik . '
                                ' . $printkartuMedis . '
                                ' . $printNota . '
                                ' . $printHasilKlinik . '
                                ' . $printHasilRapidAntibody . '
                                ' . $printHasilRapidAntigen . '
                                ' . $printHasilPcr . '
                                ' . $printAmplopHaji . '
                                ' . $printAmplopProlanisGula . '
                                ' . $printAmplopProlanisUrine . '
                            </div>
                        </div>';

        return $button . ' ' . $buttonPrint;
      })
      ->addColumn('nama_pasien', function ($data) {
        return $data->pasien->nama_pasien;
      })
      ->addColumn('no_rekammedis', function ($data) {
        return Carbon::createFromFormat('Y-m-d', $data->pasien->tgllahir_pasien)->format('dmY') . str_pad((int) $data->pasien->no_rekammedis_pasien, 4, '0', STR_PAD_LEFT);
      })

      ->addColumn('prolanis', function ($data) {
        if ($data->tipe_pemeriksaan_prolanis == "PROLANIS DM") {
          # code...
          $status = '<label class="badge badge-primary badge-pill">Prolanis DM</label>';
        }else if ($data->tipe_pemeriksaan_prolanis == "PROLANIS HT") {
          # code...
          $status = '<label class="badge badge-primary badge-pill">Prolanis HT</label>';
        }else{
          $status = '<label class="badge badge-primary badge-pill">Pemeriksaan Klinik</label>';
        }
        return $status;
      })
      ->addColumn('tglregister_permohonan_uji_klinik', function ($data) {
        // return Carbon::createFromFormat('Y-m-d H:i:S', $data->tglpengambilan_permohonan_uji_klinik, 'Asia/Jakarta')->isoFormat('D MMMM Y H:i');

        return Carbon::createFromFormat('Y-m-d H:i:s', $data->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');
      })
      ->addColumn('status_permohonan', function ($data) {
        if ($data->status_permohonan_uji_klinik == "PARAMETER") {
          $status = '<label class="badge badge-danger badge-pill">Proses Parameter</label>';
        }

        if ($data->status_permohonan_uji_klinik == "ANALIS") {
          $status = '<label class="badge badge-warning badge-pill">Proses Analisa</label>';
        }

        if ($data->status_permohonan_uji_klinik == "SELESAI") {
          $status = '<label class="badge badge-success badge-pill">Proses Selesai</label>';
        }

        return $status;
      })
      ->addColumn("status_pembayaran", function ($data) {
        $pasien = Pasien::find($data->pasien_permohonan_uji_klinik);
        $data_payment = PermohonanUjiPaymentKlinik::where('permohonan_uji_klinik_id', $data->id_permohonan_uji_klinik)->first();
        if ($data->status_pembayaran == 0) {
          $amountText = '';
          $inputDisabled = '';
          if ($data_payment == null) {
            $amountText = '
                  <div class="form-group">
                      <p>Sudah terbayar: <b>' . rupiah(0) . '</b></p>
                  </div>
            ';

            $text = '
                  <div class="form-group">
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga_permohonan_uji_klinik) . '</b></p>
                  </div>
              ';

            $paymentMethod = $data->metode_pembayaran == 0 ? "Cash" : "Transfer";

            $status_pembayaran = '
              <button type="button" class="btn btn-outline-warning" data-toggle="modal" style="padding:5px 10px !important;" data-target="#pembayaran_' . $data->id_permohonan_uji_klinik . '" >
                          ' . $paymentMethod . '
                      </button><br>
              <button type="button" class="btn btn-outline-danger mt-1" data-toggle="modal" style="padding:5px 10px !important;">
                          Belum Bayar
                      </button>

              <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji_klinik . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <form action="' . route('elits-permohonan-uji-klinik-2.storeDataPermohonanUjiKlinikPayment', [$data->id_permohonan_uji_klinik]) . '" method="POST">
                              <div class="modal-body">
                                  ' . $text . '
                                  ' . $amountText . '
                                  <input type="hidden" name="_token" value="' . csrf_token() . '">
                                  <div class="form-group">
                                      <label for="nama_pasien" class="col-form-label">TELAH DITERIMA DARI :</label>
                                      <input type="text" class="form-control" name="nama_pasien" value="' . $pasien->nama_pasien . '" id="nama_pasien" disabled>
                                  </div>
                                  <div class="form-group">
                                      <label for="alamat_pasien" class="col-form-label">ALAMAT :</label>
                                      <textarea class="form-control" name="alamat_pasien" id="alamat_pasien" disabled>' . $pasien->alamat_pasien . '</textarea>
                                  </div>
                                  <div class="form-group">
                                      <label for="terbayar_permohonan_uji_payment_klinik" class="col-form-label">Nominal :</label>
                                      <input type="number" class="form-control" name="terbayar_permohonan_uji_payment_klinik" id="terbayar_permohonan_uji_payment_klinik" placeholder="Masukkan nominal pembayaran">
                                  </div>
                                  <input type="hidden" class="form-control" name="total_harga_permohonan_uji_payment_klinik" id="total_harga_permohonan_uji_payment_klinik" value="' . $data->total_harga_permohonan_uji_klinik . '" placeholder="">
                                  <input type="hidden" class="form-control" name="id_permohonan_uji_klinik" id="id_permohonan_uji_klinik" value="' . $data->id_permohonan_uji_klinik . '" placeholder="">
                              </div>
                              <div class="modal-footer">
                                  <button type="submit" class="btn btn-danger" >Bayar</button>
                              </div>
                          </form>
                      </div>
                  </div>
                </div>
              ';
          } else {
            $amountText = '
              <div class="form-group">
                  <p>Sisa yang harus dibayar: <b>' . rupiah($data->total_harga_permohonan_uji_klinik - $data_payment->terbayar_permohonan_uji_payment_klinik) . '</b></p>
                  <p>Sudah terbayar: <b>' . rupiah($data_payment->terbayar_permohonan_uji_payment_klinik) . '</b></p>
              </div>
            ';

            $text = '
                  <div class="form-group">
                      <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga_permohonan_uji_klinik) . '</b></p>
                  </div>
            ';

            $paymentMethod = $data->metode_pembayaran == 0 ? "Cash" : "Transfer";

            $status_pembayaran = '
              <button type="button" class="btn btn-outline-warning" data-toggle="modal" style="padding:5px 10px !important;" data-target="#pembayaran_' . $data->id_permohonan_uji_klinik . '" >
                          ' . $paymentMethod . '
                      </button><br>
              <button type="button" class="btn btn-outline-warning mt-1" data-toggle="modal" style="padding:5px 10px !important;">
                          Belum Lunas
                      </button>

              <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji_klinik . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                      <div class="modal-content">
                          <div class="modal-header">
                              <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <form action="' . route('elits-permohonan-uji-klinik-2.updateDataPermohonanUjiKlinikPayment', [$data->id_permohonan_uji_klinik]) . '" method="POST">
                              <div class="modal-body">
                                  ' . $text . '
                                  ' . $amountText . '
                                  <input type="hidden" name="_token" value="' . csrf_token() . '">
                                  <div class="form-group">
                                      <label for="nama_pasien" class="col-form-label">TELAH DITERIMA DARI :</label>
                                      <input type="text" class="form-control" name="nama_pasien" value="' . $pasien->nama_pasien . '" id="nama_pasien" disabled>
                                  </div>
                                  <div class="form-group">
                                      <label for="alamat_pasien" class="col-form-label">ALAMAT :</label>
                                      <textarea class="form-control" name="alamat_pasien" id="alamat_pasien" disabled>' . $pasien->alamat_pasien . '</textarea>
                                  </div>
                                  <div class="form-group">
                                      <label for="terbayar_permohonan_uji_payment_klinik" class="col-form-label">Nominal :</label>
                                      <input type="number" class="form-control" name="terbayar_permohonan_uji_payment_klinik" id="terbayar_permohonan_uji_payment_klinik" placeholder="Masukkan nominal pembayaran">
                                  </div>
                                  <input type="hidden" class="form-control" name="total_harga_permohonan_uji_payment_klinik" id="total_harga_permohonan_uji_payment_klinik" value="' . $data->total_harga_permohonan_uji_klinik . '" placeholder="">
                                  <input type="hidden" class="form-control" name="id_permohonan_uji_klinik" id="id_permohonan_uji_klinik" value="' . $data->id_permohonan_uji_klinik . '" placeholder="">
                                  <input type="hidden" class="form-control" name="total_terbayar" id="total_terbayar" value="' . $data_payment->terbayar_permohonan_uji_payment_klinik . '" placeholder="">
                              </div>
                              <div class="modal-footer">
                                  <button type="submit" class="btn btn-info" >Bayar</button>
                              </div>
                          </form>
                      </div>
                  </div>
                </div>
              ';
          }
        } else {
          $paymentMethod = $data->metode_pembayaran == 0 ? "Cash" : "Transfer";
          $text = '
              <div class="form-group">
                  <p>Total yang harus dibayar: <b>' . rupiah($data->total_harga_permohonan_uji_klinik) . '</b></p>
              </div>
          ';
          $amountText = '
              <div class="form-group">
                  <p>Sudah terbayar: <b>' . rupiah($data_payment->terbayar_permohonan_uji_payment_klinik) . '</b></p>
              </div>
            ';
          $status_pembayaran = '
            <button type="button" class="btn btn-outline-warning" data-toggle="modal" style="padding:5px 10px !important;" data-target="#pembayaran_' . $data->id_permohonan_uji_klinik . '" >
                        ' . $paymentMethod . '
                    </button><br>
            <button type="button" class="btn btn-outline-success mt-1" data-toggle="modal" style="padding:5px 10px !important;">
                        Lunas
                    </button>

            <div class="modal fade" id="pembayaran_' . $data->id_permohonan_uji_klinik . '" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Nota Pembayaran</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="' . route('elits-permohonan-uji-klinik-2.storeDataPermohonanUjiKlinikPayment', [$data->id_permohonan_uji_klinik]) . '" method="POST">
                                    <div class="modal-body">
                                        ' . $text . '
                                        ' . $amountText . '
                                        <input type="hidden" name="_token" value="' . csrf_token() . '">
                                        <div class="form-group">
                                            <label for="nama_pasien" class="col-form-label">TELAH DITERIMA DARI :</label>
                                            <input type="text" class="form-control" name="nama_pasien" value="' . $pasien->nama_pasien . '" id="nama_pasien" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label for="alamat_pasien" class="col-form-label">ALAMAT :</label>
                                            <textarea class="form-control" name="alamat_pasien" id="alamat_pasien" disabled>' . $pasien->alamat_pasien . '</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success" disabled>Lunas</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>';
        }
        return $status_pembayaran;
      })

      ->rawColumns(['action', 'tgl_pengambilan', 'status_permohonan', 'nama_pasien', 'status_pembayaran', 'no_rekammedis' , 'prolanis'])
      ->addIndexColumn()
      ->setFilteredRecords($totalFilteredRecord)
      ->setTotalRecords($totalDataRecord)
      ->make(true);
  }

  private function loadDataSearch($data, $search)
  {
    $data = $data->where(function ($query) use ($search) {
      $query->where('noregister_permohonan_uji_klinik', 'like', "%{$search}%")
        ->Orwhere('nama_pasien', 'like', "%{$search}%")
        ->Orwhere('no_rekammedis_pasien', 'like', "%{$search}%")
        ->Orwhere('status_permohonan_uji_klinik', 'like', "%{$search}%");
    });

    return $data;
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {

    // $bpjs = new PCare\Kunjungan($this->pcare_conf());
    // dd($bpjs->riwayat("2362")->index());

    $user = Auth()->user();
    // $count = PermohonanUjiKlinik2::where(DB::raw('YEAR(tglregister_permohonan_uji_klinik)'), '=', date('Y'))->max('nourut_permohonan_uji_klinik');
    $count = NumberKlinik::where(DB::raw('YEAR(created_at)'), '=', date('Y'))->max('last_number');




    $start_num=StartNum::where('code_lab_start_number','KLI')->first();


    // if ($permohonan_count==0) {
    //   # code...

    //   $count_pasien = Pasien::max('nourut_pasien') + 1 +($start_num->count_start_number);
    // }else{
    //   $count_pasien = Pasien::max('nourut_pasien') + 1 +($start_num->count_start_number);
    // }

    // dd($start_num);

    if ($count < 1) {
      $set_count = 1 + ($start_num->count_start_number);
      if ( date('Y')!=$start_num->year_start_number) {
        # code...
        $count==0;
        $set_count =1;
      }
    } else {


      $set_count = $count + 1 ;



    }


    // dd($count);

    $count_pasien = Pasien::max('nourut_pasien') + 1;

    // if ($count < 1) {
    //   $set_count = 1;
    // } else {
    //   $set_count = $count + 1;
    // }

    $romanMonths = [
      1 => 'I',
      2 => 'II',
      3 => 'III',
      4 => 'IV',
      5 => 'V',
      6 => 'VI',
      7 => 'VII',
      8 => 'VIII',
      9 => 'IX',
      10 => 'X',
      11 => 'XI',
      12 => 'XII'
    ];

    $currentMonth = date('n'); // Mendapatkan bulan saat ini dalam format numerik (1-12)
    $romanMonth = $romanMonths[$currentMonth]; // Mendapatkan bulan dalam format Romawi

    $code = str_pad((int) $set_count, 4, '0', STR_PAD_LEFT) . '/LK/' . date('Y');


    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.add', compact('code', 'set_count', 'count_pasien'));
    //get all menu public
  }



  public function getPatients(Request $request){
      // dd($request->all());
    $params = [];
    if ($request->get('identifier') !== null){
      $params['identifier'] = 'https://fhir.kemkes.go.id/id/nik|'.$request->get('identifier');
    }

    if ($request->get('name') !== null){
      $params['name'] = $request->get('name');
    }

    if ($request->get('birthday') !== null){
      $params['birthdate'] = $request->get('birthday')['year']."-". sprintf("%02d",$request->get('birthday')['month'])."-". sprintf("%02d",$request->get('birthday')['day']);

      // dd($request->get('birthday')['year']."-". sprintf("%02d",$request->get('birthday')['month'])."-". sprintf("%02d",$request->get('birthday')['day']));
    }

    // dd($request->all());
    if ($request->get('gender') !== null){
      $params['gender'] = $request->get('gender');
    }

    $response = $this->satuSehatHelper->get('Patient', $params)['body'];

    $entries = $response['entry'] ?? [];
    $result = [];

    foreach ($entries as $entry) {
      $resource = $entry['resource'] ?? [];
      if (!isset($resource['identifier'][1]['value']) ||
        !ctype_digit($resource['identifier'][1]['value'])) {

        $nik = null;
      } else {
        $nik = $resource['identifier'][1]['value'];
      }

      $gender = $resource['gender'] == 'male' ? 'L' : 'P';


      $result[] = [
        'nik' => $nik,
        'gender' => $gender ?? null,
        'id' => $resource['id'] ?? null,
        'address' => $resource['address'][0]['city'] ?? null,
        'birthDate' => DateHelper::formatOnlyDate($resource['birthDate']) ?? null,
        'name' => $resource['name'][0]['text'] ?? null,
        'telepon' => $resource['telecom'][0]['value'] ?? null,
      ];
    }

    return response()->json($result);
  }

  public function getPatientsSilaboy(Request $request)
  {
    $inputs = $request->all();
    $filteredInputs = collect($inputs)->filter(function ($value, $key) {
      if ($key === 'birthday') {
        return false;
      }
      return !empty($value);
    });

    $patients = Pasien::query()
      ->where(function($query) use ($filteredInputs) {
        foreach ($filteredInputs as $key => $value) {
          $query->orWhere($key, 'LIKE', '%' . $value . '%');
        }
      })
      ->get();

    $result = [];
    foreach ($patients as $patient){
      $result[] = [
        'nik' => $patient->nik_pasien ?? null,
        'gender' => $patient->gender_pasien ?? null,
        'id' => $patient->id_pasien_satu_sehat ?? null,
        'address' => $patient->alamat_pasien ?? null,
        'birthDate' => DateHelper::formatOnlyDate($patient->tgllahir_pasien) ?? null,
        'name' => $patient->nama_pasien ?? null,
        'telepon' => $patient->phone_pasien ?? null,
      ];
    }

    return response()->json($result);
  }

  private function getDataPasienSatuSehat($nik, $name, $dob)
  {
    $params = [
      "identifier" => 'https://fhir.kemkes.go.id/id/nik|'.$nik,
      "name" => $name,
      "birthdate" => $dob
    ];

    $response = $this->satuSehatHelper->get('Patient', $params)['body'];


    $entries = $response['entry'] ?? [];
    $result = [];

    foreach ($entries as $entry) {
      $resource = $entry['resource'] ?? [];
      $result[] = [
        'id' => $resource['id'] ?? null,
      ];
    }
    return $result[0]["id"] ?? null;
  }

  public function store(Request $request)
  {
    // Validasi data yang dikirim

    // dd($request->all() );
    $validator = $this->rules($request->all());

    // dd($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    }

    DB::beginTransaction();

    try {
      $post = new PermohonanUjiKlinik2();


      $count = NumberKlinik::where(DB::raw('YEAR(created_at)'), '=', date('Y'))->max('last_number');

      $start_num=StartNum::where('code_lab_start_number','KLI')->first();

      if ($count < 1) {
        $set_count = 1 + ($start_num->count_start_number);
        if ( date('Y')!=$start_num->year_start_number) {
          # code...
          $count==0;
          $set_count =1;
        }
      } else {


        $set_count = $count + 1 ;
      }

      $count_pasien = Pasien::max('nourut_pasien') + 1;

      $romanMonths = [
        1 => 'I',
        2 => 'II',
        3 => 'III',
        4 => 'IV',
        5 => 'V',
        6 => 'VI',
        7 => 'VII',
        8 => 'VIII',
        9 => 'IX',
        10 => 'X',
        11 => 'XI',
        12 => 'XII'
      ];

      $currentMonth = date('n'); // Mendapatkan bulan saat ini dalam format numerik (1-12)
      $romanMonth = $romanMonths[$currentMonth]; // Mendapatkan bulan dalam format Romawi

      $code = str_pad((int) $set_count, 4, '0', STR_PAD_LEFT) . '/LK/' . date('Y');

      $post->fill([
        'nourut_permohonan_uji_klinik' => $set_count,
        'noregister_permohonan_uji_klinik' => $code,
        'tglregister_permohonan_uji_klinik' => Carbon::parse($request->post('tglregister_permohonan_uji_klinik'))->format('Y-m-d H:i:s'),
        'nopasien_permohonan_uji_klinik' => $request->post('nopasien_permohonan_uji_klinik'),
        'tgllahir_pasien_permohonan_uji_klinik' => $request->post('tgllahir_pasien_permohonan_uji_klinik'),
        'umurtahun_pasien_permohonan_uji_klinik' => $request->post('umurtahun_pasien_permohonan_uji_klinik'),
        'umurbulan_pasien_permohonan_uji_klinik' => $request->post('umurbulan_pasien_permohonan_uji_klinik'),
        'umurhari_pasien_permohonan_uji_klinik' => $request->post('umurhari_pasien_permohonan_uji_klinik'),
        'nama_dokter_pengirim_permohonan_uji_klinik' => $request->post('nama_dokter_pengirim_permohonan_uji_klinik'),
        'hp_dokter_pengirim_permohonan_uji_klinik' => $request->post('hp_dokter_pengirim_permohonan_uji_klinik'),
        'diagnosa_permohonan_uji_klinik' => $request->post('diagnosa_permohonan_uji_klinik'),
        // 'jenis_spesimen' => $request->post('jenis_spesimen'),
        // 'tgl_sampling_permohonan_uji_klinik' => Carbon::createFromFormat('d/m/Y', $request->post('tgl_sampling_permohonan_uji_klinik'))->format('Y-m-d'),
        // 'jam_sampling_permohonan_uji_klinik' => $request->post('jam_sampling_permohonan_uji_klinik'),
        // 'plebotomist_permohonan_uji_klinik' => $request->post('plebotomist_permohonan_uji_klinik'),
        // 'lokasi_lain_permohonan_uji_klinik' => $request->post('lokasi_lain_permohonan_uji_klinik'),
        // 'penanganan_hasil_permohonan_uji_klinik' => $request->post('penanganan_hasil_permohonan_uji_klinik'),
        // 'catatan_permohonan_uji_klinik' => $request->post('catatan_permohonan_uji_klinik'),
        // 'riwayat_obat_permohonan_uji_klinik' => $request->post('riwayat_obat_permohonan_uji_klinik'),
        // 'riwayat_obat_permohonan_uji_klinik' => $request->post('riwayat_obat_permohonan_uji_klinik'),
        'tipe_pemeriksaan_prolanis' => $request->post('tipe_pemeriksaan_prolanis'),
        'status_pembayaran' => 0,
        'status_permohonan_uji_klinik' => 'PARAMETER',
      ]);

      // $type_pemeriksaan=$request->post('is_prolanis');
      // if ($type_pemeriksaan=="is_prolanis_gula") {
      //   # code...
      //   $post->is_prolanis_gula=1;
      // }else if ($type_pemeriksaan=="is_prolanis_urine") {
      //   // if ($type_pemeriksaan=="is_prolanis_gula") {
      //     # code...
      //     $post->is_prolanis_urine=1;
      //   // }
      // }

      $hasPerwakilan = $request->has('isPerwakilan');
      if ($hasPerwakilan){
        $post->nama_perwakilan_permohonan_uji_klinik = $request->post('nama_perwakian_permohonan_uji_klinik');
        $post->tanggal_lahir_perwakilan_permohonan_uji_klinik = $request->post('tanggal_lahir_perwakilan');
        $post->gender_perwakilan_permohonan_uji_klinik = $request->post('gender_perwakilan_permohonan_uji_klinik');
        $post->alamat_perwakilan_permohonan_uji_klinik = $request->post('alamat_perwakilan');
      }

      $store_pasien = false;


      // Jika pasien baru, validasi dan simpan pasien baru
      // dd($request->post('pasien_permohonan_uji_klinik') );
      if ($request->post('pasien_permohonan_uji_klinik') == null) {
        $validator = $this->rules_pasien($request->all());

        if ($validator->fails()) {
          return response()->json(['status' => false, 'pesan' => $validator->errors()]);
        }



        // if ($request->post('nik_pasien') != '-') {
        //   $check = Pasien::where('nik_pasien', $request->post('nik_pasien'))->first();

        //   if ($check) {
        //     return response()->json(['status' => false, 'pesan' => "NIK pasien sudah pernah diinput, silahkan input NIK yang berbeda!"], 200);
        //   }
        // }

        // Buat nomor rekam medis baru

        if (str_contains($request->post('tgllahir_pasien'), '/')) {
          $date = Carbon::createFromFormat("d/m/Y", $request->post('tgllahir_pasien'))->format('Y-m-d');
        } else {
          $date = Carbon::createFromFormat("d-m-Y", $request->post('tgllahir_pasien'))->format('Y-m-d');
        }

        if ($request->post('nik_pasien') !== null){
          $pasien = Pasien::where('nik_pasien',$request->post('nik_pasien'))->first();
        }else{
          $pasien = null;
        }

        $idSatuSehat = $this->getDataPasienSatuSehat($request->post('nik_pasien'), $request->post('nama_pasien'), $date);

        if (isset($pasien)) {
          if (isset($idSatuSehat)){
            $pasien->update([
              "id_pasien_satu_sehat" => $idSatuSehat
            ]);
          }
          # code...
          // $count = Pasien::max('nourut_pasien');
          // $set_count = $count < 1 ? 1 : $count + 1;

          // $post_newpasien = new Pasien();
          // $post_newpasien->fill([
          //   'nik_pasien' => $request->post('nik_pasien'),
          //   'nourut_pasien' => $set_count,
          //   'no_rekammedis_pasien' => $set_count,
          //   'nama_pasien' => $request->post('nama_pasien'),
          //   'gender_pasien' => $request->post('gender_pasien'),
          //   'tgllahir_pasien' => $request->post('tgllahir_pasien'),
          //   'alamat_pasien' => $request->post('alamat_pasien'),
          //   'phone_pasien' => $request->post('phone_pasien'),
          //   'divisi_instansi_pasien' => $request->post('divisi_instansi_pasien'),
          //   'id_pasien_satu_sehat' => $request->post('id_satu_sehat')
          // ]);



          // if (isset($request->id_satu_sehat)) {
          //   # code...
          //   $post_newpasien->id_pasien_satu_sehat = $request->id_satu_sehat;
          // }

          // // dd($post_newpasien);


          // // $validator_pasien_satusehat = $this->rules_pasien_satusehat($request->all());

          // $store_pasien = $post_newpasien->save();
          // $post_newpasien->id_pasien= $pasien->id_pasien;
          $store_pasien=true;

          $post->pasien_permohonan_uji_klinik = $pasien->id_pasien;
        }else{



          $count = Pasien::max('nourut_pasien');
          $set_count = $count < 1 ? 1 : $count + 1;

          $post_newpasien = new Pasien();
          if ($request->post('gender_pasien')=="male" or $request->post('gender_pasien')=="Laki-Laki" or $request->post('gender_pasien')=="L"){
            $gender_pasien = "L";
          }else{
            $gender_pasien = "P";
          }


          $post_newpasien->fill([
            'nik_pasien' => $request->post('nik_pasien'),
            'nourut_pasien' => $set_count,
            'no_rekammedis_pasien' => $set_count,
            'nama_pasien' => $request->post('nama_pasien'),
            'gender_pasien' =>  $gender_pasien,
            'tgllahir_pasien' => $date,
            'alamat_pasien' => $request->post('alamat_pasien'),
            'phone_pasien' => $request->post('phone_pasien'),
            'divisi_instansi_pasien' => $request->post('divisi_instansi_pasien'),
            'id_pasien_satu_sehat' => $request->post('id_satu_sehat')
          ]);




          if (isset($idSatuSehat)){
            $post_newpasien->id_pasien_satu_sehat = $idSatuSehat;
          }

          // dd($post_newpasien);


          // $validator_pasien_satusehat = $this->rules_pasien_satusehat($request->all());
          $store_pasien = $post_newpasien->save();
          $post->pasien_permohonan_uji_klinik = $post_newpasien->id_pasien;

          $pasien=$post_newpasien;
          $store_pasien = true;
        }


        // if (!$validator_pasien_satusehat->fails()) {
        //    // $post->pasien_permohonan_uji_klinik = $post_newpasien->id_pasien;
        //     // $client = new Client([
        //     //   'base_uri' => env('URL_SATUSEHAT'),// Set base URI dari environment variable
        //     // ]);



        //     // // Data yang akan dikirimkan dalam request JSON
        //     // $data = [
        //     //     "resourceType" => "Patient",
        //     //     "meta" => [
        //     //         "profile" => [
        //     //             "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
        //     //         ]
        //     //     ],
        //     //     "identifier" => [
        //     //         [
        //     //             "use" => "official",
        //     //             "system" => "https://fhir.kemkes.go.id/id/nik",
        //     //             "value" => $request->post('nik_pasien')
        //     //         ]
        //     //     ],
        //     //     "active" => true,
        //     //     "name" => [
        //     //         [
        //     //             "use" => "official",
        //     //             "text" => $request->post('nama_pasien')
        //     //         ]
        //     //     ],
        //     //     "telecom" => [
        //     //         [
        //     //             "system" => "phone",
        //     //             "value" =>  $request->post('phone_pasien'),
        //     //             "use" => "mobile"
        //     //         ]
        //     //     ],
        //     //     "gender" =>  $request->post('gender_pasien')=="L"? "male" : "female",
        //     //     "birthDate" =>  Carbon::createFromFormat('d/m/Y', $request->post('tgllahir_pasien'))->format('Y-m-d'),
        //     //     "deceasedBoolean" => false,
        //     //     "address" => [
        //     //         [
        //     //             "use" => "home",
        //     //             "line" => [
        //     //               $request->post('alamat_pasien') ??"-"
        //     //             ]
        //     //         ]
        //     //     ],
        //     //     "multipleBirthInteger" => 0
        //     // ];
        //     // $satusehat= SatuSehat::first();

        //     if (isset($satusehat)) {

        //       # code...
        //       // try {

        //       //     // Melakukan POST request dengan JSON body
        //       //     $response = $client->post( env('BASE_URL_SATUSEHAT').'/Patient', [
        //       //         'headers' => [
        //       //             'Authorization' => 'Bearer ' . $satusehat->token, // Gunakan token jika dibutuhkan
        //       //             'Content-Type' => 'application/json',
        //       //             'Accept' => 'application/json',
        //       //         ],
        //       //         'json' => $data, // Mengirim data dalam format JSON
        //       //     ]);

        //       //     // Mendapatkan body response
        //       //     $responseBody = json_decode($response->getBody(), true);

        //       //     $post_newpasien->id_pasien_satu_sehat=$responseBody["data"]["patient_id"];


        //       //     $store_pasien = $post_newpasien->save();
        //       //     $post->pasien_permohonan_uji_klinik = $post_newpasien->id_pasien;
        //       // } catch (RequestException $e) {
        //       //     // Menangani error
        //       //     // dd($e->getResponse()->getBody()->getContents());


        //       //     if ($e->hasResponse()) {
        //       //         return response()->json(['status' => false, 'pesan' => $e->getResponse()->getBody()->getContents(), 'error' => $e->getMessage()], 200);
        //       //     } else {
        //       //         return response()->json(['status' => false, 'pesan' => $e->getMessage(), 'error' => $e->getMessage()], 200);
        //       //     }

        //       //     // $store_pasien = $post_newpasien->save();

        //       // }
        //     }
        // }




      } else {
        $pasien = Pasien::find($request->post('pasien_permohonan_uji_klinik'));



        // if( strlen($pasien->nik_pasien)==16){

        //   $client = new Client([
        //     'base_uri' => env('URL_SATUSEHAT'),// Set base URI dari environment variable
        //   ]);

        //   try {
        //       $satusehat= SatuSehat::first();

        //       // Melakukan POST request dengan JSON body
        //       $response = $client->get( env('BASE_URL_SATUSEHAT').'/Patient?identifier=https://fhir.kemkes.go.id/id/nik|'.$pasien->nik_pasien, [
        //           'headers' => [
        //               'Authorization' => 'Bearer ' . $satusehat->token,
        //           ]
        //       ]);

        //       // Mendapatkan body response
        //       $responseBody = json_decode($response->getBody(), true);

        //       if ($responseBody["entry"]==[]) {
        //         # code...
        //         $client = new Client([
        //           'base_uri' => env('URL_SATUSEHAT'),// Set base URI dari environment variable
        //         ]);



        //         // Data yang akan dikirimkan dalam request JSON
        //         $data = [
        //             "resourceType" => "Patient",
        //             "meta" => [
        //                 "profile" => [
        //                     "https://fhir.kemkes.go.id/r4/StructureDefinition/Patient"
        //                 ]
        //             ],
        //             "identifier" => [
        //                 [
        //                     "use" => "official",
        //                     "system" => "https://fhir.kemkes.go.id/id/nik",
        //                     "value" => $pasien->nik_pasien
        //                 ]
        //             ],
        //             "active" => true,
        //             "name" => [
        //                 [
        //                     "use" => "official",
        //                     "text" => $pasien->nama_pasien
        //                 ]
        //             ],
        //             "telecom" => [
        //                 [
        //                     "system" => "phone",
        //                     "value" =>  $pasien->phone_pasien,
        //                     "use" => "mobile"
        //                 ]
        //             ],
        //             "gender" =>  $pasien->gender_pasien=="L"? "male" : "female",
        //             "birthDate" =>  Carbon::createFromFormat('Y-m-d',  $pasien->tgllahir_pasien)->format('Y-m-d'),
        //             "deceasedBoolean" => false,
        //             "address" => [
        //                 [
        //                     "use" => "home",
        //                     "line" => [
        //                       $request->post('alamat_pasien') ??"-"
        //                     ]
        //                 ]
        //             ],
        //             "multipleBirthInteger" => 0
        //         ];
        //         $satusehat= SatuSehat::first();

        //         if (isset($satusehat)) {

        //           # code...
        //           try {

        //               // Melakukan POST request dengan JSON body
        //               $response = $client->post( env('BASE_URL_SATUSEHAT').'/Patient', [
        //                   'headers' => [
        //                       'Authorization' => 'Bearer ' . $satusehat->token, // Gunakan token jika dibutuhkan
        //                       'Content-Type' => 'application/json',
        //                       'Accept' => 'application/json',
        //                   ],
        //                   'json' => $data, // Mengirim data dalam format JSON
        //               ]);

        //               // Mendapatkan body response
        //               $responseBody = json_decode($response->getBody(), true);

        //               // dd( $responseBody );

        //               $pasien->id_pasien_satu_sehat=$responseBody["data"]["patient_id"];


        //               $pasien->save();
        //           } catch (RequestException $e) {
        //               // Menangani error
        //               // dd($e->getResponse()->getBody()->getContents());


        //               if ($e->hasResponse()) {
        //                   return response()->json(['status' => false, 'pesan' => $e->getResponse()->getBody()->getContents(), 'error' => $e->getMessage()], 200);
        //               } else {
        //                   return response()->json(['status' => false, 'pesan' => $e->getMessage(), 'error' => $e->getMessage()], 200);
        //               }

        //               // $store_pasien = $post_newpasien->save();

        //           }
        //         }

        //       }else{
        //         $pasien->id_pasien_satu_sehat = $responseBody["entry"][0]["resource"]["id"];
        //         $pasien->save();
        //       }
        //       // $post_newpasien->id_pasien_satu_sehat=$responseBody["data"]["patient_id"];


        //       // $store_pasien = $post_newpasien->save();
        //       // $post->pasien_permohonan_uji_klinik = $post_newpasien->id_pasien;
        //   } catch (RequestException $e) {
        //       // Menangani error
        //       // dd($e->getResponse()->getBody()->getContents());


        //       if ($e->hasResponse()) {
        //           return response()->json(['status' => false, 'pesan' => $e->getResponse()->getBody()->getContents(), 'error' => $e->getMessage()], 200);
        //       } else {
        //           return response()->json(['status' => false, 'pesan' => $e->getMessage(), 'error' => $e->getMessage()], 200);
        //       }

        //       // $store_pasien = $post_newpasien->save();

        //   }


        // }

        $store_pasien = true;
        $post->pasien_permohonan_uji_klinik = $request->post('pasien_permohonan_uji_klinik');


      }

      // Simpan data permohonan uji klinik
      $simpan = $post->save();

      // simpan number_klinik
      $number_klinik = new NumberKlinik();
      $number_klinik->new_number = $request->post('nourut_permohonan_uji_klinik');
      $number_klinik->last_number = $request->post('nourut_permohonan_uji_klinik');
      $number_klinik->id_permohonan_uji_klinik = $post->id_permohonan_uji_klinik;
      $number_klinik->save();

      //simpan ke verifikasi
      $startDate = Carbon::parse($request->post('tglregister_permohonan_uji_klinik'))->format('Y-m-d H:i:s');
      if ($request->post('tglregister_permohonan_uji_klinik')) {
        $verificationActivitySample = new VerificationActivitySample();
        $verificationActivitySample->id = Uuid::uuid4()->toString();
        $verificationActivitySample->id_verification_activity = 1; // Pendaftaran / Registrasi
        $verificationActivitySample->start_date = $startDate;
        $verificationActivitySample->stop_date = Carbon::parse($startDate)->addMinutes(5)->format('Y-m-d H:i:s');
        $verificationActivitySample->nama_petugas = $request->post('petugas_penerima');
        $verificationActivitySample->is_klinik = $post->id_permohonan_uji_klinik;
        $verificationActivitySample->is_done = 1;
        // Simpan ke database
        $verificationActivitySample->save();
      }


      DB::commit();

      if ($simpan && $store_pasien) {
        return response()->json(['status' => true, 'pesan' => "Data permohonan uji klinik berhasil disimpan!","id"=>$pasien->id_pasien], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data permohonan uji klinik tidak berhasil disimpan!","id"=>$pasien->id_pasien], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();
      Log::error($e->getMessage());
      return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!', 'error' => $e->getMessage()], 200);
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
    $auth = Auth()->user();
    $item = PermohonanUjiKlinik2::find($id);

    // $tgl_register = Carbon::createFromFormat('Y-m-d', $item->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');
    // $tgl_pengambilan = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglpengambilan_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');

    // data parameter
    // $data_parameter_jenis = ParameterJenisKlinik::whereNull('deleted_at')->orderBy('name_parameter_jenis_klinik', 'asc')->get();
    // $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)
    //   ->whereNull('deleted_at')
    //   ->get();
    // $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    // // data analis
    // if ($item->tglpengujian_permohonan_uji_klinik !== null) {
    //   $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    // } else {
    //   $tgl_pengujian = '';
    // }

    // if ($item->spesimen_darah_permohonan_uji_klinik !== null) {
    //   $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_darah_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    // } else {
    //   $tgl_spesimen_darah = '-';
    // }

    // if ($item->spesimen_urine_permohonan_uji_klinik !== null) {
    //   $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_urine_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    // } else {
    //   $tgl_spesimen_urine = '-';
    // }

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

    // if (count($data_permohonan_uji_paket_klinik) > 0) {
    //   foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
    //     $item_permohonan_parameter_paket = [];
    //     $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
    //     $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
    //     $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
    //     $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
    //     $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

    //     // get data perameter satuan yang ada dalam paket
    //     $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)
    //       ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
    //       ->get();

    //     $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

    //     if (count($data_permohonan_uji_satuan_klinik) > 0) {
    //       foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
    //         // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
    //         // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
    //         $pasien_umur = $item->umurtahun_pasien_permohonan_uji_klinik;
    //         $pasien_gender = $item->pasien->gender_pasien;

    //         $item_permohonan_parameter_satuan = [];
    //         $item_permohonan_parameter_satuan['id_permohonan_uji_parameter_klinik'] = $value_satuan->id_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_klinik'] = $value_satuan->permohonan_uji_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_paket_klinik'] = $value_satuan->permohonan_uji_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_paket_klinik'] = $value_satuan->parameter_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_satuan_klinik'] = $value_satuan->parameter_satuan_klinik;
    //         $item_permohonan_parameter_satuan['nama_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->name_parameter_satuan_klinik ?? null;
    //         $item_permohonan_parameter_satuan['harga_permohonan_uji_parameter_klinik'] = $value_satuan->harga_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['hasil_permohonan_uji_parameter_klinik'] = $value_satuan->hasil_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['flag_permohonan_uji_parameter_klinik'] = $value_satuan->flag_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['keterangan_permohonan_uji_parameter_klinik'] = $value_satuan->keterangan_permohonan_uji_parameter_klinik;

    //         $id_parameter_jenis_klinik = null;

    //         if (isset($value_paket->parameter_jenis_klinik)) {
    //           $id_parameter_jenis_klinik = $value_paket->parameter_jenis_klinik;
    //         } else {
    //           $id_parameter_jenis_klinik = $value_satuan->permohonanujijenispaketklinik->parameter_jenis_klinik_id;
    //         }
    //         $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //           ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //           ->get();

    //         $item_parameter_by_baku_mutu = null;
    //         if (count($data_parameter_by_baku_mutu) > 0) {

    //           $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //             ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //             ->where('is_khusus_baku_mutu', '0')
    //             ->first();

    //           // cek dulu apakah ada data dengan data khusus sperti general atau specific

    //           if ($check_parameter_by_baku_mutu) {
    //             $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
    //           } else {
    //             $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //               ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //               ->where('is_khusus_baku_mutu', '1')
    //               ->where('gender_baku_mutu', $pasien_gender)
    //               ->where(function ($query) use ($pasien_umur) {
    //                 $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
    //                   ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
    //               })
    //               ->first();
    //           }
    //         }

    //         // jika tidak ada satuan di permohonan uji satuan maka ambil value dari baku mutu
    //         // jika ada nilai satuan di permohonan uji satuan maka ambil nilai dari tabel itu sendiri
    //         if ($value_satuan->satuan_permohonan_uji_parameter_klinik != null) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $value_satuan->satuan_permohonan_uji_parameter_klinik;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $value_satuan->unit->name_unit ?? null;
    //         } else if (isset($item_parameter_by_baku_mutu->unit_id)) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit_id;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit->name_unit ?? null;
    //         } else {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = null;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = null;
    //         }

    //          $item_permohonan_parameter_satuan['min'] = $item_parameter_by_baku_mutu->min ?? null;
    $item_permohonan_parameter_satuan['max'] = $item_parameter_by_baku_mutu->max ?? null;
    $item_permohonan_parameter_satuan['equal'] = $item_parameter_by_baku_mutu->equal ?? null;
    //         $item_permohonan_parameter_satuan['nilai_baku_mutu'] = $item_parameter_by_baku_mutu->nilai_baku_mutu ?? null;

    //         // menyiapkan data array untuk mapping jika data parameter memiliki subsatuan
    //         $item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'] = [];

    //         $data_permohonan_uji_subsatuan_klinik = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $value_satuan->id_permohonan_uji_parameter_klinik)
    //           ->get();

    //         if (count($data_permohonan_uji_subsatuan_klinik)) {
    //           foreach ($data_permohonan_uji_subsatuan_klinik as $key_subsatuan => $value_subsatuan) {
    //             $item_permohonan_parameter_subsatuan = [];
    //             $item_permohonan_parameter_subsatuan['id_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->id_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['permohonan_uji_parameter_klinik_id'] = $value_subsatuan->permohonan_uji_parameter_klinik_id;
    //             $item_permohonan_parameter_subsatuan['parameter_sub_satuan_klinik_id'] = $value_subsatuan->parameter_sub_satuan_klinik_id;
    //             $item_permohonan_parameter_subsatuan['nama_parameter_sub_satuan_klinik_id'] = $value_subsatuan->parametersubsatuanklinik->name_parameter_sub_satuan_klinik ?? null;
    //             $item_permohonan_parameter_subsatuan['hasil_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->hasil_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['flag_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->flag_permohonan_uji_sub_parameter_klinik;
    //             // $item_permohonan_parameter_subsatuan['baku_mutu_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['keterangan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->keterangan_permohonan_uji_sub_parameter_klinik;

    //             // cek data baku mutu jika memiliki data subsatuan dengan nilai rujukan
    //             if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
    //               $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
    //                 ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_subsatuan->parameter_sub_satuan_klinik_id)
    //                 ->first();

    //               if ($item_parameter_subsatuan_by_baku_mutu) {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik;
    //               } else {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //               }
    //             } else {
    //               $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //             }

    //             if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->unit->name_unit ?? null;
    //             } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit->name_unit ?? null;
    //             } else {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = null;
    //             }

    //             array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
    //           }
    //         }

    //         array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
    //       }
    //     }

    //     array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
    //   }
    // }

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

    foreach ($parameterCustom as $valueCustom) {
      $nilaiPermohonanUjiKlinik["name_item"] = $valueCustom->name_parameter_satuan_klinik;
      $nilaiPermohonanUjiKlinik["count_item"] = $valueCustom->count_method;
      $nilaiPermohonanUjiKlinik["price_item"] = $valueCustom->harga_permohonan_uji_parameter_klinik;
      $nilaiPermohonanUjiKlinik["total"] = $valueCustom->harga_permohonan_uji_parameter_klinik * $valueCustom->count_method;

      array_push($value_items, $nilaiPermohonanUjiKlinik);
    }

    // dd($value_items);

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

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.read', [
      'item' => $item,
      // 'tgl_register' => $tgl_register,
      // 'tgl_pengambilan' => $tgl_pengambilan,
      // 'data_parameter_jenis' => $data_parameter_jenis,
      // 'data_parameter_satuan' => $data_parameter_satuan,
      'data_permohonan_uji_paket_klinik' => $data_permohonan_uji_paket_klinik,
      // 'tgl_pengujian' => $tgl_pengujian,
      // 'tgl_spesimen_darah' => $tgl_spesimen_darah,
      // 'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_detail_uji_paket' => $data_permohonan_uji_paket_klinik,
      'value_items' => $value_items,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
    // return "coba";
  }

  function print($id) {}

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $auth = Auth()->user();
    $item = PermohonanUjiKlinik2::find($id);
    // dd($item);
    $pasien = Pasien::where('id_pasien', $item->pasien_permohonan_uji_klinik)->first();
    $verifikasi = VerificationActivitySample::where('is_klinik', $id)
    ->where('id_verification_activity', 1)
    ->first();
    // dd($verifikasi);

    // $tgl_pengambilan = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglpengambilan_permohonan_uji_klinik)->format('d/m/Y H:m:s');

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.edit', [
      'item' => $item,
      'pasien' => $pasien,
      'verifikasi' => $verifikasi,
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
    // dd($request->all());
      // Validasi input
      $validator = $this->rules($request->all());

      if ($validator->fails()) {
          return response()->json(['status' => false, 'pesan' => $validator->errors()], 422);
      }

      DB::beginTransaction();

      try {
          $post = PermohonanUjiKlinik2::findOrFail($id);

          // Update data
          $post->noregister_permohonan_uji_klinik = $request->post('noregister_permohonan_uji_klinik');
          // $post->jenis_spesimen = $request->post('jenis_spesimen');
          $post->tglregister_permohonan_uji_klinik = Carbon::parse($request->post('tglregister_permohonan_uji_klinik'))->format('Y-m-d H:i:s');
          $post->nama_dokter_pengirim_permohonan_uji_klinik = $request->post('nama_dokter_pengirim_permohonan_uji_klinik');
          $post->hp_dokter_pengirim_permohonan_uji_klinik = $request->post('hp_dokter_pengirim_permohonan_uji_klinik');
          $post->diagnosa_permohonan_uji_klinik = $request->post('diagnosa_permohonan_uji_klinik');
          $post->tipe_pemeriksaan_prolanis = $request->post('tipe_pemeriksaan_prolanis');
          $post->metode_pembayaran = $request->post('metode_pembayaran');

          $type_pemeriksaan=$request->post('is_prolanis');

          // if ($type_pemeriksaan=="is_prolanis_gula") {
          //   # code...
          //   $post->is_prolanis_gula=1;
          // }else if ($type_pemeriksaan=="is_prolanis_urine") {
          //   // if ($type_pemeriksaan=="is_prolanis_gula") {
          //     # code...
          //     $post->is_prolanis_urine=1;
          //   // }
          // }

          $hasPerwakilan = $request->has('isPerwakilan');
          if ($hasPerwakilan){
            $post->nama_perwakilan_permohonan_uji_klinik = $request->post('nama_perwakilan_permohonan_uji_klinik');
            $post->tanggal_lahir_perwakilan_permohonan_uji_klinik = $request->post('tanggal_lahir_perwakilan');
            $post->gender_perwakilan_permohonan_uji_klinik = $request->post('gender_perwakilan_permohonan_uji_klinik');
            $post->alamat_perwakilan_permohonan_uji_klinik = $request->post('alamat_perwakilan');
          }else{
            $post->nama_perwakilan_permohonan_uji_klinik = null;
            $post->tanggal_lahir_perwakilan_permohonan_uji_klinik = null;
            $post->gender_perwakilan_permohonan_uji_klinik = null;
            $post->alamat_perwakilan_permohonan_uji_klinik =null;
          }
          // Simpan data
          $post->save();
          // dd($post);

          //update nama petugas diverifikasi

          $startDate = Carbon::parse($request->post('tglregister_permohonan_uji_klinik'))->format('Y-m-d H:i:s');
          if ($request->post('tglregister_permohonan_uji_klinik')) {
            $verifikasi = VerificationActivitySample::where('is_klinik', $id)
            ->where('id_verification_activity', 1)
            ->first();

            $verifikasi->start_date = $startDate;
            $verifikasi->stop_date = Carbon::parse($startDate)->addMinutes(10)->format('Y-m-d H:i:s');
            $verifikasi->nama_petugas = $request->post('petugas_penerima');
            $verifikasi->updated_at = date('Y-m-d H:i:s');

            $verifikasi->save();
            // dd($verifikasi);
          }


          DB::commit();

          return response()->json(['status' => true, 'pesan' => "Data permohonan uji klinik berhasil diubah!"], 200);
      } catch (\Exception $e) {
          DB::rollback();

          return response()->json(['status' => false, 'pesan' => 'System gagal melakukan perubahan!'], 500);
      }
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

   public function sortingNumberKlinikAll()
  {
    $this->sortingNumberKlinik();
    return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['status' => 'Sampel Klinik berhasil diurutkan']);

  }

  public function sortingNumberKlinik()
  {

    $start_num=StartNum::where('code_lab_start_number','KLI')->first();

    if ( date('Y')==$start_num->year_start_number) {
        //UPDATE tb_number_klinik n
        DB::statement("
            SET @current_number = 2831;
        ");
        DB::statement("

            UPDATE tb_number_klinik n
            JOIN (
                SELECT
                    id_number_klinik,
                    @current_number AS new_number,
                    CASE
                        WHEN id_permohonan_uji_klinik IS NOT NULL THEN @current_number
                        ELSE
                            CASE
                                WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis - 1
                                WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji - 1
                                WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula - 1
                                WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine - 1
                            END
                    END AS last_number,
                    @current_number :=
                        CASE
                          WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis
                            WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji
                            WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula
                            WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine
                            ELSE @current_number + 1
                        END AS updated_current_number

                FROM (
                    SELECT *
                    FROM tb_number_klinik
                    WHERE deleted_at IS NULL
                    ORDER BY created_at ASC
                ) sorted


                LEFT JOIN tb_permohonan_uji_klinik_haji h
                    ON sorted.id_haji = h.id_permohonan_uji_klinik_haji AND h.deleted_at IS NULL
                LEFT JOIN tb_permohonan_uji_klinik_prolanis_gula g
                    ON sorted.id_prolanis_gula = g.id_permohonan_uji_klinik_prolanis_gula AND g.deleted_at IS NULL
                LEFT JOIN tb_permohonan_uji_klinik_prolanis_urine u
                    ON sorted.id_prolanis_urine = u.id_permohonan_uji_klinik_prolanis_urine AND u.deleted_at IS NULL
                LEFT JOIN tb_permohonan_uji_klinik_prolanis v
                    ON sorted.id_prolanis = v.id_permohonan_uji_klinik_prolanis AND v.deleted_at IS NULL
            ) ordered
            ON n.id_number_klinik = ordered.id_number_klinik
            SET
                n.new_number = ordered.new_number,
                n.last_number = ordered.last_number;

        ");


        //NON PROLANIS DAN HAJI
        DB::statement("
            UPDATE
                        tb_permohonan_uji_klinik_2 p
                    JOIN
                        tb_number_klinik n
                        ON p.id_permohonan_uji_klinik = n.id_permohonan_uji_klinik
                    SET
                        p.nourut_permohonan_uji_klinik = n.new_number,
                        p.noregister_permohonan_uji_klinik = CONCAT(
                            n.new_number,
                            '/LK/',
                            CASE
                                WHEN MONTH(p.created_at) = 1 THEN 'I'
                                WHEN MONTH(p.created_at) = 2 THEN 'II'
                                WHEN MONTH(p.created_at) = 3 THEN 'III'
                                WHEN MONTH(p.created_at) = 4 THEN 'IV'
                                WHEN MONTH(p.created_at) = 5 THEN 'V'
                                WHEN MONTH(p.created_at) = 6 THEN 'VI'
                                WHEN MONTH(p.created_at) = 7 THEN 'VII'
                                WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                                WHEN MONTH(p.created_at) = 9 THEN 'IX'
                                WHEN MONTH(p.created_at) = 10 THEN 'X'
                                WHEN MONTH(p.created_at) = 11 THEN 'XI'
                                WHEN MONTH(p.created_at) = 12 THEN 'XII'
                            END,
                            '/',
                            YEAR(p.created_at)
                        )
                    WHERE
                        n.id_permohonan_uji_klinik IS NOT NULL
                        AND p.deleted_at IS NULL
                        AND n.deleted_at IS NULL;
            ");


        //UBAH PROLANIS
        DB::statement("

          UPDATE
                `tb_permohonan_uji_klinik_2` p
            JOIN (
              SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_prolanis, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_prolanis ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_prolanis IN (n.id_prolanis, n.id_prolanis_gula, n.id_prolanis_urine) WHERE n.id_prolanis IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
            ) u
            ON
              p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
            SET
                p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
                p.noregister_permohonan_uji_klinik = CONCAT(
                            u.new_number + u.urutan - 1,
                            '/LK/',
                            CASE
                                WHEN MONTH(p.created_at) = 1 THEN 'I'
                                WHEN MONTH(p.created_at) = 2 THEN 'II'
                                WHEN MONTH(p.created_at) = 3 THEN 'III'
                                WHEN MONTH(p.created_at) = 4 THEN 'IV'
                                WHEN MONTH(p.created_at) = 5 THEN 'V'
                                WHEN MONTH(p.created_at) = 6 THEN 'VI'
                                WHEN MONTH(p.created_at) = 7 THEN 'VII'
                                WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                                WHEN MONTH(p.created_at) = 9 THEN 'IX'
                                WHEN MONTH(p.created_at) = 10 THEN 'X'
                                WHEN MONTH(p.created_at) = 11 THEN 'XI'
                                WHEN MONTH(p.created_at) = 12 THEN 'XII'
                            END,
                            '/',
                            YEAR(p.created_at)
                        );

          ");

        // UBAH HAJI
        DB::statement("

                  UPDATE
                `tb_permohonan_uji_klinik_2` p
            JOIN (
              SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_haji, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_haji ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_haji = n.id_haji WHERE n.id_haji IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
            ) u
            ON p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
            SET
                p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
                p.noregister_permohonan_uji_klinik = CONCAT(
                            u.new_number + u.urutan - 1,
                            '/LK/',
                            CASE
                                WHEN MONTH(p.created_at) = 1 THEN 'I'
                                WHEN MONTH(p.created_at) = 2 THEN 'II'
                                WHEN MONTH(p.created_at) = 3 THEN 'III'
                                WHEN MONTH(p.created_at) = 4 THEN 'IV'
                                WHEN MONTH(p.created_at) = 5 THEN 'V'
                                WHEN MONTH(p.created_at) = 6 THEN 'VI'
                                WHEN MONTH(p.created_at) = 7 THEN 'VII'
                                WHEN MONTH(p.created_at) = 8 THEN 'VIII'
                                WHEN MONTH(p.created_at) = 9 THEN 'IX'
                                WHEN MONTH(p.created_at) = 10 THEN 'X'
                                WHEN MONTH(p.created_at) = 11 THEN 'XI'
                                WHEN MONTH(p.created_at) = 12 THEN 'XII'
                            END,
                            '/',
                            YEAR(p.created_at)
                        );
          ");

    }else{
         //UPDATE tb_number_klinik n
    DB::statement("
         SET @current_number = 1;
     ");
     DB::statement("

         UPDATE tb_number_klinik n
         JOIN (
             SELECT
                 id_number_klinik,
                 @current_number AS new_number,
                 CASE
                     WHEN id_permohonan_uji_klinik IS NOT NULL THEN @current_number
                     ELSE
                         CASE
                             WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis - 1
                             WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji - 1
                             WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula - 1
                             WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine - 1
                         END
                 END AS last_number,
                 @current_number :=
                     CASE
                       WHEN id_prolanis IS NOT NULL THEN @current_number + v.kuota_prolanis
                         WHEN id_haji IS NOT NULL THEN @current_number + h.kuota_haji
                         WHEN id_prolanis_gula IS NOT NULL THEN @current_number + g.kuota_prolanis_gula
                         WHEN id_prolanis_urine IS NOT NULL THEN @current_number + u.kuota_prolanis_urine
                         ELSE @current_number + 1
                     END AS updated_current_number

             FROM (
                 SELECT *
                 FROM tb_number_klinik
                 WHERE deleted_at IS NULL
                 AND YEAR(tb_number_klinik.created_at) = ".date('Y')."
                 ORDER BY created_at ASC
             ) sorted


             LEFT JOIN tb_permohonan_uji_klinik_haji h
                 ON sorted.id_haji = h.id_permohonan_uji_klinik_haji AND h.deleted_at IS NULL
             LEFT JOIN tb_permohonan_uji_klinik_prolanis_gula g
                 ON sorted.id_prolanis_gula = g.id_permohonan_uji_klinik_prolanis_gula AND g.deleted_at IS NULL
             LEFT JOIN tb_permohonan_uji_klinik_prolanis_urine u
                 ON sorted.id_prolanis_urine = u.id_permohonan_uji_klinik_prolanis_urine AND u.deleted_at IS NULL
             LEFT JOIN tb_permohonan_uji_klinik_prolanis v
                 ON sorted.id_prolanis = v.id_permohonan_uji_klinik_prolanis AND v.deleted_at IS NULL
         ) ordered
         ON n.id_number_klinik = ordered.id_number_klinik

         SET
             n.new_number = ordered.new_number,
             n.last_number = ordered.last_number
        WHERE YEAR(n.created_at) = ".date('Y').";
     ");


     //NON PROLANIS DAN HAJI
     DB::statement("
         UPDATE
                     tb_permohonan_uji_klinik_2 p
                 JOIN
                     tb_number_klinik n
                     ON p.id_permohonan_uji_klinik = n.id_permohonan_uji_klinik
                 SET
                     p.nourut_permohonan_uji_klinik = n.new_number,
                     p.noregister_permohonan_uji_klinik = CONCAT(
                          LPAD(n.new_number, 4, '0'),
                         '/LK/',

                         YEAR(p.created_at)
                     )
                 WHERE
                     n.id_permohonan_uji_klinik IS NOT NULL
                     AND p.deleted_at IS NULL
                      AND YEAR(p.created_at) = ".date('Y')."
                         AND YEAR( n.created_at) = ".date('Y')."
                     AND n.deleted_at IS NULL;
         ");


     //UBAH PROLANIS
     DB::statement("

       UPDATE
             `tb_permohonan_uji_klinik_2` p
         JOIN (
           SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_prolanis, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_prolanis ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_prolanis IN (n.id_prolanis, n.id_prolanis_gula, n.id_prolanis_urine) WHERE n.id_prolanis IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL AND YEAR(p.created_at) = ".date('Y')." AND YEAR(n.created_at) = ".date('Y')."
         ) u
         ON
           p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
         SET
             p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
             p.noregister_permohonan_uji_klinik = CONCAT(
                          LPAD(u.new_number + u.urutan - 1, 4, '0'),

                         '/LK/',

                         YEAR(p.created_at)
                     )

                 WHERE YEAR(p.created_at) = ".date('Y').";

       ");

     // UBAH HAJI
     DB::statement("

               UPDATE
             `tb_permohonan_uji_klinik_2` p
         JOIN (
           SELECT p.id_permohonan_uji_klinik, n.new_number, n.id_haji, ROW_NUMBER() OVER (PARTITION BY p.id_permohonan_uji_klinik_haji ORDER BY p.nourut_permohonan_uji_klinik) AS urutan FROM `tb_permohonan_uji_klinik_2` p LEFT JOIN tb_number_klinik n ON p.id_permohonan_uji_klinik_haji = n.id_haji WHERE n.id_haji IS NOT NULL AND n.id_permohonan_uji_klinik IS NULL
         ) u
         ON p.id_permohonan_uji_klinik = u.id_permohonan_uji_klinik
         SET
             p.nourut_permohonan_uji_klinik = u.new_number + u.urutan - 1,
             p.noregister_permohonan_uji_klinik = CONCAT(
                          LPAD(u.new_number + u.urutan - 1, 4, '0'),
                         '/LK/',

                         YEAR(p.created_at)
                     )
                  WHERE YEAR(p.created_at) = ".date('Y')."
                     ;


       ");
    }
  }

  public function destroy($id)
  {
    // $data = PermohonanUjiKlinik2::find($id);
    $hapus = PermohonanUjiKlinik2::where('id_permohonan_uji_klinik', $id)->delete();

    $lab_num = NumberKlinik::where('id_permohonan_uji_klinik', $id)->delete();

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


    // $this->sortingNumberKlinik();


    if ($hapus == true &&  $lab_num ==true) {
      $this->sortingNumberKlinik();
      return response()->json(['status' => true, 'pesan' => "Data permohonan uji klinik berhasil dihapus!"], 200);
    } else {
      return response()->json(['status' => false, 'pesan' => "Data permohonan uji klinik tidak berhasil dihapus!"], 200);
    }
  }

  public function buktiDaftarPermohonanUjiParameter(Request $request, $id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($id_permohonan_uji_klinik);

    if ($item_permohonan_uji_klinik !== null) {
      if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
        $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
      } else {
        $tgl_pengujian = '';
      }

      if ($item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik !== null) {
        $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
      } else {
        $tgl_spesimen_darah = '';
      }

      if ($item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik !== null) {
        $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
      } else {
        $tgl_spesimen_urine = '';
      }

      $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

      if (!isset($no_LHU)) {
        $no_LHU = new LHU;
        //uuid
        $uuid4 = Uuid::uuid4();

        $no_LHU_urutan = LHU::max('nomer_urut_LHU');
        $no_LHU->id_lhu = $uuid4->toString();
        $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
        $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

        // permintaan pertama
        // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

        // permintaan kedua
        $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

        $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
        $no_LHU->save();

        $no_LHU = $no_LHU->nomer_LHU;
      } else {
        $get_nomor_lhu = $no_LHU->nomer_LHU;
        $explode_nomor_lhu = explode("/", $get_nomor_lhu);
        $get_pisah0_lhu = $explode_nomor_lhu[0];
        $get_pisah2_lhu = $explode_nomor_lhu[2];
        $get_pisah3_lhu = $explode_nomor_lhu[3];

        $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
      }

      $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->whereHas('parametersatuanklinik', function ($query) {
          return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
            ->whereNull('deleted_at')
            ->orderBy('name_parameter_satuan_klinik', 'asc');
        })
        ->whereNull('deleted_at')
        ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->get();

      $data_permohonan_uji_parameter_klinik_pcr = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->whereHas('parametersatuanklinik', function ($query) {
          return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '1')
            ->whereNull('deleted_at')
            ->orderBy('name_parameter_satuan_klinik', 'asc');
        })
        ->whereNull('deleted_at')
        ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->get();

      $data_permohonan_uji_parameter_klinik_antigen = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->whereHas('parametersatuanklinik', function ($query) {
          return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '3')
            ->whereNull('deleted_at')
            ->orderBy('name_parameter_satuan_klinik', 'asc');
        })
        ->whereNull('deleted_at')
        ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->get();

      $data_permohonan_uji_parameter_klinik_antibody = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->whereHas('parametersatuanklinik', function ($query) {
          return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '2')
            ->whereNull('deleted_at')
            ->orderBy('name_parameter_satuan_klinik', 'asc');
        })
        ->whereNull('deleted_at')
        ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->get();

      $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

      $text_redirect_barcode = route('scan.permohonan-uji-klinik', $item_permohonan_uji_klinik->id_permohonan_uji_klinik);

      // dd($text_redirect_barcode);

      return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.bukti-daftar-permohonan-uji-klinik', [
        'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
        'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
        'data_permohonan_uji_parameter_klinik_pcr' => $data_permohonan_uji_parameter_klinik_pcr,
        'data_permohonan_uji_parameter_klinik_antigen' => $data_permohonan_uji_parameter_klinik_antigen,
        'data_permohonan_uji_parameter_klinik_antibody' => $data_permohonan_uji_parameter_klinik_antibody,
        'tgl_pengujian' => $tgl_pengujian,
        'tgl_spesimen_darah' => $tgl_spesimen_darah,
        'tgl_spesimen_urine' => $tgl_spesimen_urine,
        'data_parameter_satuan' => $data_parameter_satuan,
        'no_LHU' => $no_LHU,
        'text_redirect_barcode' => $text_redirect_barcode,
      ]);
    } else {
      return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.pagination');
    }
  }

  public function rules_permohonan_uji_parameter($request)
  {
    $rule = [
      'type_parameter.*' => 'required',
    ];

    $pesan = [
      'type_parameter.*.required' => 'Tipe Parameter tidak boleh kosong!',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function addParameter(Request $request)
  {

    $item = PermohonanUjiKlinik2::where('noregister_permohonan_uji_klinik', $request->no_reg)->first();

    $id = $item->id_permohonan_uji_klinik;
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');
    $code = $request->no_reg;

    $pasien = isset($request->id_pasien) ? Pasien::where('id_pasien', $request->id_pasien)->firstOrFail() : Pasien::first();
    $umur = date_diff(date_create(date('Y-m-d')), date_create($pasien->tgllahir_pasien));
    $umur_string = $umur->y . " tahun " . $umur->m . " bulan " . $umur->d . " hari";

    $parameter_jenis_klinik = ParameterJenisKlinik::with('pakets')->orderBy('created_at', 'asc')->get();

    $parameter_paket_extra = ParameterPaketExtra::with('parameterSubPaketExtra')->orderBy('created_at', 'asc')->get();

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.add-paramater', [
      'item' => $item,
      'tgl_register' => $tgl_register,
      'id' => $id,
      'code' => $code,
      'pasien' => $pasien,
      'umur_string' => $umur_string,
      'parameter_paket_extra' => $parameter_paket_extra,
      'parameter_jenis_klinik' => $parameter_jenis_klinik,
    ]);
  }

  public function createPermohonanUjiParameter($id)
  {
    $item = PermohonanUjiKlinik2::find($id);
    $tgl_register = Carbon::createFromFormat('Y-m-d H-i-s', $item->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');

    $data_detail_uji_paket = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id)
      ->whereNull('deleted_at')
      ->get();


    if ($item->status_permohonan_uji_klinik == 'SELESAI') {
      $data_detail_uji_parameter = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)
        ->whereNull('deleted_at')
        ->get();

      $data_parameter_paket = ParameterPaketKlinik::whereNull('deleted_at')->orderBy('name_parameter_paket_klinik', 'asc')->get();

      return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.show-permohonan-uji-parameter-klinik', [
        'item' => $item,
        'tgl_register' => $tgl_register,
        'data_detail_uji_paket' => $data_detail_uji_paket,
        'data_detail_uji_parameter' => $data_detail_uji_parameter,
        'data_parameter_paket' => $data_parameter_paket,
      ]);
    } else {
      if (count($data_detail_uji_paket) > 0) {
        $data_detail_uji_parameter = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id)
          ->whereNull('deleted_at')
          ->get();

        $data_parameter_paket = ParameterPaketKlinik::whereNull('deleted_at')->orderBy('name_parameter_paket_klinik', 'asc')->get();

        return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.edit-permohonan-uji-paramater-klinik', [
          'item' => $item,
          'tgl_register' => $tgl_register,
          'data_detail_uji_paket' => $data_detail_uji_paket,
          'data_detail_uji_parameter' => $data_detail_uji_parameter,
          'data_parameter_paket' => $data_parameter_paket,
        ]);
      } else {
        return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.create-permohonan-uji-paramater-klinik', [
          'item' => $item,
          'tgl_register' => $tgl_register,
          'id' => $id,
        ]);
      }
    }
  }

  public function getParameterDanHarga(Request $request)
  {
    $search = $request->search;
    $jenis_parameter = $request->jenis_parameter;
    $type_parameter = $request->type_parameter;
    $response = array();
    $set_data = [];

    if ($type_parameter == "P") {
      if (!isset($search)) {
        $data = ParameterPaketKlinik::orderby('name_parameter_paket_klinik', 'asc')
          ->limit(10)
          ->get();
      } else {
        $data = ParameterPaketKlinik::where('name_parameter_paket_klinik', 'like', '%' . $search . '%')
          ->orderby('name_parameter_paket_klinik', 'asc')
          ->limit(10)
          ->get();
      }

      foreach ($data as $item) {
        $response[] = array(
          "id" => $item->id_parameter_paket_klinik,
          "text" => $item->name_parameter_paket_klinik,
          "harga" => $item->harga_parameter_paket_klinik,
        );
      }

      $set_data = [
        'data' => $data,
      ];
    }

    if ($type_parameter == "C") {
      if ($search) {
        $data = ParameterSatuanKlinik::where('parameter_jenis_klinik', $jenis_parameter)
          ->orderby('name_parameter_satuan_klinik', 'asc')
          ->limit(10)
          ->get();
      } else {
        $data = ParameterSatuanKlinik::where('parameter_jenis_klinik', $jenis_parameter)
          ->where('name_parameter_satuan_klinik', 'like', '%' . $search . '%')
          ->orderby('name_parameter_satuan_klinik', 'asc')
          ->limit(10)
          ->get();
      }

      foreach ($data as $item) {
        $response[] = array(
          "id" => $item->id_parameter_satuan_klinik,
          "text" => $item->name_parameter_satuan_klinik,
          "harga" => $item->harga_satuan_parameter_satuan_klinik,
        );
      }

      $set_data = [
        'data' => $data,
      ];
    }

    return response()->json($response);
  }

  public function countParameterDanHarga(Request $request)
  {
    $jenis_parameter = $request->jenis_parameter;
    $type_parameter = $request->type_parameter;
    $satuan_parameter = $request->satuan_parameter;
    $harga_satuan = 0;
    $response = array();

    if ($type_parameter == "P") {
      $data = ParameterPaketKlinik::where('id_parameter_paket_klinik', $satuan_parameter)
        ->whereNull('deleted_at')
        ->first();

      if (isset($data)) {
        $response[] = array(
          "harga" => $data->harga_parameter_paket_klinik,
        );
      } else {
        $response[] = array(
          "harga" => 0,
        );
      }
    }

    if (isset($jenis_parameter) || isset($type_parameter) || isset($satuan_parameter)) {
      if ($type_parameter == "C") {
        if (isset($request->satuan_parameter)) {
          $count_parameter = count($request->satuan_parameter);

          for ($i = 0; $i < $count_parameter; $i++) {
            // print_r($jenis_parameter . ', ');

            $data = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->satuan_parameter[$i])
              ->where('parameter_jenis_klinik', $jenis_parameter)
              ->whereNull('deleted_at')
              ->first();

            $harga_satuan = $harga_satuan + $data->harga_satuan_parameter_satuan_klinik;
          }

          // dd();
        } else {
          $harga_satuan = 0;
        }

        if (isset($data)) {
          if ($harga_satuan > 0 || $harga_satuan !== null) {
            $get_harga_satuan = $harga_satuan;
          } else {
            $get_harga_satuan = 0;
          }

          $response[] = array(
            "harga" => $get_harga_satuan,
          );
        } else {
          $response[] = array(
            "harga" => 0,
          );
        }
      }
    }

    return response()->json($response);
  }

  private function _storePermohonanUjiParameter(Request $request)
  {
    $validator = $this->rules_permohonan_uji_parameter($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {

      DB::beginTransaction();

      try {
        if (isset($request->jenis_parameter) || $request->jenis_parameter !== null || count($request->jenis_parameter) > 0) {
          // update ke permohonan uji klinik
          $post = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);
          $post->total_harga_permohonan_uji_klinik = $request->post('subamount_harga_parameter');
          $post->status_permohonan_uji_klinik = 'ANALIS';
          $simpan_post = $post->save();

          // store ke permohonan uji paket klinik
          $no_ajaib = array();

          // store ke permohonan uji paket klinik
          foreach ($request->jenis_parameter as $key => $value) {
            $post_paket = new PermohonanUjiPaketKlinik();
            $post_paket->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
            $post_paket->parameter_jenis_klinik = $request->post('jenis_parameter')[$key];
            $post_paket->type_permohonan_uji_paket_klinik = $request->post('type_parameter')[$key];

            if ($request->post('type_parameter')[$key] == "P") {
              $post_paket->parameter_paket_klinik = $request->post('satuan_parameter')[$key][0];
            } else {
              $post_paket->parameter_paket_klinik = null;
            }

            $post_paket->harga_permohonan_uji_paket_klinik = $request->post('harga_parameter')[$key];

            $simpan_post_paket = $post_paket->save();

            array_push($no_ajaib, $post_paket->id_permohonan_uji_paket_klinik);
          }

          // dd($no_ajaib);

          $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);

          foreach ($no_ajaib as $key => $value) {
            if ($request->post('type_parameter')[$key + 1] == "P") {
              // jika memilih paket name field satuan akan menjadi parameter_paket_klinik
              $data_parameter_paket = ParameterSatuanPaketKlinik::where('parameter_paket_klinik', $request->post('satuan_parameter')[$key + 1])
                ->whereNull('deleted_at')
                ->get();

              // mapping data parmeter satuan untuk dimasukkan ke permohonanujiparameterklinik
              foreach ($data_parameter_paket as $key3 => $value3) {
                // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
                $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

                $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key])
                  ->where('parameter_satuan_klinik_id', $value3->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '0')
                  ->first();

                // cek dulu apakah ada data dengan data khusus sperti general atau specific
                if ($check_parameter_by_baku_mutu) {
                  $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
                } else {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key])
                    ->where('parameter_satuan_klinik_id', $value3->parameter_satuan_klinik)
                    ->where('is_khusus_baku_mutu', '1')
                    ->where('gender_baku_mutu', $pasien_gender)
                    ->where(function ($query) use ($pasien_umur) {
                      $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                        ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                    })
                    ->first();
                  if (!isset($item_parameter_by_baku_mutu)) {
                    $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key])
                      ->where('parameter_satuan_klinik_id', $value3->parameter_satuan_klinik)
                      ->where('is_khusus_baku_mutu', '1')
                      ->where('gender_baku_mutu', $pasien_gender)
                      ->first();
                  }
                }

                $post_parameter = new PermohonanUjiParameterKlinik();
                $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
                $post_parameter->permohonan_uji_paket_klinik = $value;
                $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key + 1][0];
                $post_parameter->parameter_satuan_klinik = $value3->parameter_satuan_klinik;
                $post_parameter->harga_permohonan_uji_parameter_klinik = $value3->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;

                if (isset($item_parameter_by_baku_mutu->unit_id)) {
                  $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
                  $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
                } else {
                  $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
                  $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
                }

                $simpan_post_parameter = $post_parameter->save();

                // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
                $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $value3->parameter_satuan_klinik)
                  ->get();

                if (count($data_parameter_subsatuan) > 0) {
                  foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
                    $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
                    $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
                    $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

                    // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                    if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
                      $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
                        ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
                        ->first();

                      $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
                      $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
                    }

                    $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
                  }
                }
              }
            }

            if ($request->post('type_parameter')[$key + 1] == "C") {
              foreach ($request->post('satuan_parameter')[$key + 1] as $key_c => $value_c) {
                // #attempt 1
                $post_parameter = new PermohonanUjiParameterKlinik();
                $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
                $post_parameter->permohonan_uji_paket_klinik = $value;
                $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key + 1][$key_c];

                // get harga parameter
                $item_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key + 1][$key_c])->first();

                $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter->harga_satuan_parameter_satuan_klinik;

                $simpan_post_parameter = $post_parameter->save();

                // #attempt 2
              }
            }
          }

          // store ke permohonan uji parameter klinik
          /* foreach ($request->jenis_parameter as $key => $value) {
          $data_permohonan_paket_p = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $request->post('permohonan_uji_klinik'))->where('parameter_jenis_klinik', $request->post('jenis_parameter')[$key])->where('type_permohonan_uji_paket_klinik', 'P')->whereNotNull('parameter_paket_klinik')->first();

          $data_permohonan_paket_c = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $request->post('permohonan_uji_klinik'))
          ->where('parameter_jenis_klinik', $request->post('jenis_parameter')[$key])
          ->where('type_permohonan_uji_paket_klinik', 'C')
          ->whereNull('parameter_paket_klinik')
          ->get();

          foreach ($request->post('satuan_parameter')[$key] as $key2 => $value2) {
          if ($request->post('type_parameter')[$key] == "P") {
          $data_parameter_paket = ParameterSatuanPaketKlinik::where('parameter_paket_klinik', $request->post('satuan_parameter')[$key][$key2])
          ->whereNull('deleted_at')
          ->get();

          foreach ($data_parameter_paket as $key3 => $value3) {
          $post_parameter = new PermohonanUjiParameterKlinik();
          $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          $post_parameter->permohonan_uji_paket_klinik = $data_permohonan_paket_p->id_permohonan_uji_paket_klinik;
          $post_parameter->parameter_satuan_klinik = $value3->parameter_satuan_klinik;
          $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key][0];

          $simpan_post_parameter = $post_parameter->save();
          }
          }

          if ($request->post('type_parameter')[$key] == "C") {
          foreach ($data_permohonan_paket_c as $key_ppc => $value_ppc) {
          $post_parameter = new PermohonanUjiParameterKlinik();
          $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          $post_parameter->permohonan_uji_paket_klinik = $value_ppc->id_permohonan_uji_paket_klinik;
          $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key][$key2];

          // get harga parameter
          $item_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key][$key2])->first();

          $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter->harga_satuan_parameter_satuan_klinik;

          $simpan_post_parameter = $post_parameter->save();
          }
          }
          }
          } */

          if (($request->complete_step != null && $request->complete_step == 1)) {
            $urlNextStep = route('elits-permohonan-uji-klinik.bukti-daftar-permohonan-uji-parameter', $post->id_permohonan_uji_klinik);
          } else {
            $urlNextStep = route('elits-permohonan-uji-klinik.index');
          }

          DB::commit();

          if ($simpan_post == true && $simpan_post_paket == true && $simpan_post_parameter == true) {
            return response()->json(['status' => true, 'pesan' => "Data permohonan uji paket klinik berhasil disimpan!", 'urlNextStep' => $urlNextStep], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data permohonan uji paket klinik tidak berhasil disimpan!"], 200);
          }
        }
      } catch (Exception $e) {
        DB::rollback();
        return response()->json(['status' => false, 'pesan' => "System gagal menyimpan data permohonan uji paket klinik!"], 200);
      }
    }
  }

  public function storePermohonanUjiParameter(Request $request)
  {

    $validator = $this->rules_permohonan_uji_parameter($request->all());
    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {

      // DB::beginTransaction();

      // try {
      if (isset($request->jenis_parameter) || $request->jenis_parameter !== null || count($request->jenis_parameter) > 0) {
        // update ke permohonan uji klinik
        $post = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);
        $post->total_harga_permohonan_uji_klinik = $request->post('subamount_harga_parameter');
        $post->status_permohonan_uji_klinik = 'ANALIS';
        $simpan_post = $post->save();

        // store ke permohonan uji paket klinik
        $no_ajaib = array();

        // store ke permohonan uji paket klinik
        foreach ($request->type_parameter as $key => $value) {
          $post_paket = new PermohonanUjiPaketKlinik();
          $post_paket->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          $post_paket->parameter_jenis_klinik = $request->post('jenis_parameter')[$key];
          $post_paket->type_permohonan_uji_paket_klinik = $request->post('type_parameter')[$key];

          if ($request->post('type_parameter')[$key] == "P") {
            if (is_array($request->post('satuan_parameter')[$key])) {
              $post_paket->parameter_paket_klinik = $request->post('satuan_parameter')[$key][0];
            } else {
              $post_paket->parameter_paket_klinik = $request->post('satuan_parameter')[$key];
            }
          } else {
            $post_paket->parameter_paket_klinik = null;
          }

          $post_paket->harga_permohonan_uji_paket_klinik = $request->post('harga_parameter')[$key];

          $simpan_post_paket = $post_paket->save();

          array_push($no_ajaib, $post_paket->id_permohonan_uji_paket_klinik);
        }

        $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);

        foreach ($no_ajaib as $key => $value) {
          // if ($request->post('type_parameter')[$key + 1] == "P") {
          //   // jika memilih paket name field satuan akan menjadi parameter_paket_klinik
          //   $data_parameter_paket = ParameterSatuanPaketKlinik::where('parameter_paket_klinik', $request->post('satuan_parameter')[$key + 1])
          //     ->whereNull('deleted_at')
          //     ->get();

          //   // mapping data parmeter satuan untuk dimasukkan ke permohonanujiparameterklinik
          //   foreach ($data_parameter_paket as $key3 => $value3) {
          //     // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
          //     $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
          //     $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

          //     $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key + 1])
          //       ->where('parameter_satuan_klinik_id', $value3->parameter_satuan_klinik)
          //       ->where('is_khusus_baku_mutu', '0')
          //       ->first();

          //     // cek dulu apakah ada data dengan data khusus sperti general atau specific
          //     if ($check_parameter_by_baku_mutu) {
          //       $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
          //     } else {
          //       $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key + 1])
          //         ->where('parameter_satuan_klinik_id', $value3->parameter_satuan_klinik)
          //         ->where('is_khusus_baku_mutu', '1')
          //         ->where('gender_baku_mutu', $pasien_gender)
          //         ->where(function ($query) use ($pasien_umur) {
          //           $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
          //             ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
          //         })
          //         ->first();
          //     }

          //     $post_parameter = new PermohonanUjiParameterKlinik();
          //     $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          //     $post_parameter->permohonan_uji_paket_klinik = $value;
          //     $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key + 1][0];
          //     $post_parameter->parameter_satuan_klinik = $value3->parameter_satuan_klinik;
          //     $post_parameter->harga_permohonan_uji_parameter_klinik = $value3->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;

          //     if (isset($item_parameter_by_baku_mutu->unit_id)) {
          //       $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
          //       $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
          //     } else {
          //       $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
          //       $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
          //     }

          //     $simpan_post_parameter = $post_parameter->save();

          //     // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
          //     $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $value3->parameter_satuan_klinik)
          //       ->get();

          //     if (count($data_parameter_subsatuan) > 0) {
          //       foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
          //         $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
          //         $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
          //         $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

          //         // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
          //         if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
          //           $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
          //             ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
          //             ->first();

          //           $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
          //           $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
          //         }

          //         $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
          //       }
          //     }
          //   }
          // }

          if ($request->post('type_parameter')[$key + 1] == "P") {
            $parameterpaketklinik = ParameterPaketKlinik::where('id_parameter_paket_klinik', $request->post('satuan_parameter')[$key + 1])
              ->whereNull('deleted_at')
              ->first();

            #looping jenis parameter paket
            foreach ($parameterpaketklinik->parameterpaketjenisklinik as $key_parameterpaketjenisklinik => $value_parameterpaketjenisklinik) {
              # code...
              #looping satuan parameter dari paket
              foreach ($value_parameterpaketjenisklinik->parametersatuanpaketklinik as $key_parametersatuanpaketklinik => $value_parametersatuanpaketklinik) {

                // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
                $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

                $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
                  ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '0')
                  ->first();
                // cek dulu apakah ada data dengan data khusus sperti general atau specific
                if ($check_parameter_by_baku_mutu) {
                  $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
                } else {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
                    ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                    ->where('is_khusus_baku_mutu', '1')
                    ->where('gender_baku_mutu', $pasien_gender)
                    ->where(function ($query) use ($pasien_umur) {
                      $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                        ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                    })
                    ->first();
                  if (!isset($item_parameter_by_baku_mutu)) {
                    $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
                      ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                      ->where('is_khusus_baku_mutu', '1')
                      ->where('gender_baku_mutu', $pasien_gender)
                      ->first();
                  }
                }

                $post_parameter = new PermohonanUjiParameterKlinik();
                $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
                $post_parameter->permohonan_uji_paket_klinik = $value;
                $post_parameter->parameter_paket_jenis_klinik = $value_parameterpaketjenisklinik->id_parameter_paket_jenis_klinik;
                $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key + 1];
                $post_parameter->parameter_satuan_klinik = $value_parametersatuanpaketklinik->parameter_satuan_klinik;
                $post_parameter->jenis_parameter_klinik_id = $value_parameterpaketjenisklinik->parameter_jenis_klinik_id;

                $post_parameter->sort_jenis_klinik = $value_parameterpaketjenisklinik->sort;
                $post_parameter->sorting_parameter_satuan = $value_parametersatuanpaketklinik->sorting;

                $post_parameter->harga_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;

                if (isset($item_parameter_by_baku_mutu->unit_id)) {
                  $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
                  $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
                } else {
                  $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
                  $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
                }

                $simpan_post_parameter = $post_parameter->save();

                // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
                $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                  ->get();

                if (count($data_parameter_subsatuan) > 0) {
                  foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
                    $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
                    $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
                    $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

                    // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                    if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
                      $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
                        ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
                        ->first();

                      $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
                      $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
                    }

                    $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
                  }
                }
              }
            }
          }

          if ($request->post('type_parameter')[$key + 1] == "C") {
            $no_param = 0;
            foreach ($request->post('satuan_parameter')[$key + 1] as $key_c => $value_c) {
              // #attempt 1
              /* $post_parameter = new PermohonanUjiParameterKlinik();
              $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
              $post_parameter->permohonan_uji_paket_klinik = $value;
              $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key + 1][$key_c];

              // get harga parameter
              $item_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key + 1][$key_c])->first();

              $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter->harga_satuan_parameter_satuan_klinik;

              $simpan_post_parameter = $post_parameter->save(); */

              // #attempt 2
              $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
              $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

              $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key + 1])
                ->where('parameter_satuan_klinik_id', $request->post('satuan_parameter')[$key + 1][$key_c])
                ->where('is_khusus_baku_mutu', '0')
                ->first();

              // cek dulu apakah ada data dengan data khusus sperti general atau specific
              if ($check_parameter_by_baku_mutu) {
                $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
              } else {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key + 1])
                  ->where('parameter_satuan_klinik_id', $request->post('satuan_parameter')[$key + 1][$key_c])
                  ->where('is_khusus_baku_mutu', '1')
                  ->where('gender_baku_mutu', $pasien_gender)
                  ->where(function ($query) use ($pasien_umur) {
                    $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                      ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                  })
                  ->first();
                if (!isset($item_parameter_by_baku_mutu)) {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $request->post('jenis_parameter')[$key + 1])
                    ->where('parameter_satuan_klinik_id', $request->post('satuan_parameter')[$key + 1][$key_c])
                    ->where('is_khusus_baku_mutu', '1')
                    ->where('gender_baku_mutu', $pasien_gender)
                    ->first();
                }
              }

              $post_parameter = new PermohonanUjiParameterKlinik();
              $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
              $post_parameter->permohonan_uji_paket_klinik = $value;

              $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key + 1][$key_c];
              $post_parameter->jenis_parameter_klinik_id  = $request->post('jenis_parameter')[$key + 1];

              $post_parameter->sort_jenis_klinik = $key + 1;
              $post_parameter->sorting_parameter_satuan = $no_param;


              // get harga parameter
              $item_parameter_satuan = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key + 1][$key_c])->first();

              $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter_satuan->harga_satuan_parameter_satuan_klinik;

              if (isset($item_parameter_by_baku_mutu->unit_id)) {
                $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
                $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
              } else {
                $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
                $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
              }

              $simpan_post_parameter = $post_parameter->save();

              // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
              $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $request->post('satuan_parameter')[$key + 1][$key_c])
                ->get();

              if (count($data_parameter_subsatuan) > 0) {
                foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
                  $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
                  $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
                  $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

                  // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                  if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
                    $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
                      ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
                      ->first();

                    if ($item_parameter_subsatuan_by_baku_mutu) {
                      $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
                      $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
                    }
                  }

                  $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
                }
              }
            }
          }
        }

        if (($request->complete_step != null && $request->complete_step == 1)) {
          $urlNextStep = route('elits-permohonan-uji-klinik.bukti-daftar-permohonan-uji-parameter', $post->id_permohonan_uji_klinik);
        } else {
          $urlNextStep = route('elits-permohonan-uji-klinik.index');
        }

        DB::commit();

        if ($simpan_post == true && $simpan_post_paket == true && $simpan_post_parameter == true) {
          return response()->json(['status' => true, 'pesan' => "Data permohonan uji paket klinik berhasil disimpan!", 'urlNextStep' => $urlNextStep], 200);
        } else {
          return response()->json(['status' => false, 'pesan' => "Data permohonan uji paket klinik tidak berhasil disimpan!"], 200);
        }
      }
      //   } catch (Exception $e) {
      //     DB::rollback();
      //     return response()->json(['status' => false, 'pesan' => "System gagal menyimpan data permohonan uji paket klinik!"], 200);
      //   }
    }
  }

  public function updatePermohonanUjiParameter(Request $request, $id_permohonan_uji_klinik)
  {
    $validator = $this->rules_permohonan_uji_parameter($request->all());

    if ($validator->fails()) {
      return response()->json(['status' => false, 'pesan' => $validator->errors()]);
    } else {
      // dd($request->all());

      DB::beginTransaction();

      try {
        if (isset($request->jenis_parameter) || $request->jenis_parameter !== null || count($request->jenis_parameter) > 0) {
          // update ke permohonan uji klinik
          $post = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);
          $post->total_harga_permohonan_uji_klinik = $request->post('subamount_harga_parameter');
          $simpan_post = $post->save();

          // delete
          PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)->delete();
          PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)->delete();

          $no_ajaib = array();

          // store ke permohonan uji paket klinik
          foreach ($request->jenis_parameter as $key => $value) {
            $post_paket = new PermohonanUjiPaketKlinik();
            $post_paket->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
            $post_paket->parameter_jenis_klinik = $request->post('jenis_parameter')[$key];
            $post_paket->type_permohonan_uji_paket_klinik = $request->post('type_parameter')[$key];

            if ($request->post('type_parameter')[$key] == "P") {
              $post_paket->parameter_paket_klinik = $request->post('satuan_parameter')[$key][0];
            }

            $post_paket->harga_permohonan_uji_paket_klinik = $request->post('harga_parameter')[$key];

            $simpan_post_paket = $post_paket->save();

            array_push($no_ajaib, $post_paket->id_permohonan_uji_paket_klinik);
          }

          // dd($no_ajaib);

          foreach ($no_ajaib as $key => $value) {
            if ($request->post('type_parameter')[$key + 1] == "P") {
              $data_parameter_paket = ParameterSatuanPaketKlinik::where('parameter_paket_klinik', $request->post('satuan_parameter')[$key + 1])
                ->whereNull('deleted_at')
                ->get();

              foreach ($data_parameter_paket as $key3 => $value3) {
                $post_parameter = new PermohonanUjiParameterKlinik();
                $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
                $post_parameter->permohonan_uji_paket_klinik = $value;
                $post_parameter->parameter_satuan_klinik = $value3->parameter_satuan_klinik;
                $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key + 1][0];

                $simpan_post_parameter = $post_parameter->save();
              }
            }

            if ($request->post('type_parameter')[$key + 1] == "C") {
              foreach ($request->post('satuan_parameter')[$key + 1] as $key_c => $value_c) {
                $post_parameter = new PermohonanUjiParameterKlinik();
                $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
                $post_parameter->permohonan_uji_paket_klinik = $value;
                $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key + 1][$key_c];

                // get harga parameter
                $item_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key + 1][$key_c])->first();

                $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter->harga_satuan_parameter_satuan_klinik;

                $simpan_post_parameter = $post_parameter->save();
              }
            }
          }

          // store ke permohonan uji parameter klinik
          /* foreach ($request->jenis_parameter as $key => $value) {
          $data_permohonan_paket_p = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $request->post('permohonan_uji_klinik'))
          ->where('parameter_jenis_klinik', $request->post('jenis_parameter')[$key])
          ->where('type_permohonan_uji_paket_klinik', 'P')
          ->whereNotNull('parameter_paket_klinik')
          ->first();

          $data_permohonan_paket_c = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $request->post('permohonan_uji_klinik'))
          ->where('parameter_jenis_klinik', $request->post('jenis_parameter')[$key])
          ->where('type_permohonan_uji_paket_klinik', 'C')
          ->whereNull('parameter_paket_klinik')
          ->get();

          foreach ($request->post('satuan_parameter')[$key] as $key2 => $value2) {
          if ($request->post('type_parameter')[$key] == "P") {
          $data_parameter_paket = ParameterSatuanPaketKlinik::where('parameter_paket_klinik', $request->post('satuan_parameter')[$key][$key2])
          ->whereNull('deleted_at')
          ->get();

          foreach ($data_parameter_paket as $key3 => $value3) {
          $post_parameter = new PermohonanUjiParameterKlinik();
          $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          $post_parameter->permohonan_uji_paket_klinik = $data_permohonan_paket_p->id_permohonan_uji_paket_klinik;
          $post_parameter->parameter_satuan_klinik = $value3->parameter_satuan_klinik;
          $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key][0];

          $simpan_post_parameter = $post_parameter->save();
          }
          }

          if ($request->post('type_parameter')[$key] == "C") {
          foreach ($data_permohonan_paket_c as $key_ppc => $value_ppc) {
          $post_parameter = new PermohonanUjiParameterKlinik();
          $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
          $post_parameter->permohonan_uji_paket_klinik = $value_ppc->id_permohonan_uji_paket_klinik;
          $post_parameter->parameter_satuan_klinik = $request->post('satuan_parameter')[$key][$key2];

          // get harga parameter
          $item_parameter = ParameterSatuanKlinik::where('id_parameter_satuan_klinik', $request->post('satuan_parameter')[$key][$key2])->first();

          $post_parameter->harga_permohonan_uji_parameter_klinik = $item_parameter->harga_satuan_parameter_satuan_klinik;

          $simpan_post_parameter = $post_parameter->save();
          }
          }
          }
          } */

          DB::commit();

          if ($simpan_post == true && $simpan_post_paket == true && $simpan_post_parameter == true) {
            return response()->json(['status' => true, 'pesan' => "Data permohonan uji paket klinik berhasil disimpan!"], 200);
          } else {
            return response()->json(['status' => false, 'pesan' => "Data permohonan uji paket klinik tidak berhasil disimpan!"], 200);
          }
        }
      } catch (Exception $e) {
        DB::rollback();
        return response()->json(['status' => false, 'pesan' => "System gagal menyimpan data permohonan uji paket klinik!"], 200);
      }
    }
  }

  public function createPermohonanUjiAnalis($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');

    $verificationActivitySample = VerificationActivitySample::where('is_klinik',$id_permohonan_uji_klinik)->where('id_verification_activity',4)->first();

    // dd( $verificationActivitySample);


    $data_verifikasi_analitik = VerificationActivitySample::where([
      ['is_klinik', '=', $id_permohonan_uji_klinik],
      ['id_verification_activity', '=', 2],
    ])->first();

    if ($data_verifikasi_analitik) {
        // Jika data ditemukan, tambahkan 1 jam pada stop_date
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data_verifikasi_analitik->stop_date)
            ->addHour()
            ->format('Y-m-d H:i:s');
    } else {
        // Jika tidak ditemukan, atur nilai default atau lakukan tindakan sesuai kebutuhan
        $startDate = null;
    }

    if (isset($verificationActivitySample)) {
      # code...
      if($verificationActivitySample->start_date  !== null){
        $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s',$verificationActivitySample->start_date)->toISOString();
      }else{
        // Cek apakah $data_verifikasi_analitik ada dan stop_date-nya tidak null

            // Jika tidak ada data atau stop_date null, gunakan waktu saat ini
          $tgl_pengujian = null;

      }
    }else{
      $tgl_pengujian = null;

    }


    if ($item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik !== null) {
      $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik)->toISOString();
    } else {
      $tgl_spesimen_darah = null;
    }

    if ($item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik !== null) {
      $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik)->toISOString();
    } else {
      $tgl_spesimen_urine = null;
    }

    if ($item_permohonan_uji_klinik->name_analis_permohonan_uji_klinik !== null) {
      $nama_analis = $item_permohonan_uji_klinik->name_analis_permohonan_uji_klinik;
    } else {
      $nama_analis = '';
    }

    // if ($item_permohonan_uji_klinik->vena_permohonan_uji_klinik !== null) {
    //   $vena = $item_permohonan_uji_klinik->vena_permohonan_uji_klinik;
    // } else {
    //   $vena = '';
    // }

    // if ($item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik !== null) {mer
    //   $lokasi_lain = $item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik;
    // } else {
    //   $lokasi_lain = '';
    // }

    // if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik !== null) {
    //   $penanganan_hasil = $item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik;
    // } else {
    //   $penanganan_hasil = '';
    // }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    // if (count($data_permohonan_uji_paket_klinik) > 0) {
    //   foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
    //     $item_permohonan_parameter_paket = [];
    //     $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
    //     $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
    //     $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
    //     $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
    //     $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

    //     // get data perameter satuan yang ada dalam paket
    //     $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
    //       ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
    //       // ->groupBy('jenis_parameter_klinik_id')
    //       ->get();

    //     $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

    //     if (count($data_permohonan_uji_satuan_klinik) > 0) {
    //       foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
    //         // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
    //         // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
    //         $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
    //         $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

    //         $item_permohonan_parameter_satuan = [];
    //         $item_permohonan_parameter_satuan['id_permohonan_uji_parameter_klinik'] = $value_satuan->id_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_klinik'] = $value_satuan->permohonan_uji_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_paket_klinik'] = $value_satuan->permohonan_uji_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_paket_klinik'] = $value_satuan->parameter_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_satuan_klinik'] = $value_satuan->parameter_satuan_klinik;
    //         $item_permohonan_parameter_satuan['nama_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->name_parameter_satuan_klinik ?? null;
    //         $item_permohonan_parameter_satuan['harga_permohonan_uji_parameter_klinik'] = $value_satuan->harga_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['hasil_permohonan_uji_parameter_klinik'] = $value_satuan->hasil_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['flag_permohonan_uji_parameter_klinik'] = $value_satuan->flag_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['keterangan_permohonan_uji_parameter_klinik'] = $value_satuan->keterangan_permohonan_uji_parameter_klinik;

    //         $id_parameter_jenis_klinik = null;
    //         if (isset($value_paket->parameter_jenis_klinik)) {
    //           $id_parameter_jenis_klinik = $value_paket->parameter_jenis_klinik;
    //         } else {
    //           $id_parameter_jenis_klinik = $value_satuan->permohonanujijenispaketklinik->parameter_jenis_klinik_id;
    //         }

    //         $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //           ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //           ->get();

    //         $item_parameter_by_baku_mutu = null;
    //         if (count($data_parameter_by_baku_mutu) > 0) {

    //           $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //             ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //             ->where('is_khusus_baku_mutu', '0')
    //             ->first();

    //           // cek dulu apakah ada data dengan data khusus sperti general atau specific

    //           if ($check_parameter_by_baku_mutu) {
    //             $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
    //           } else {
    //             $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //               ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //               ->where('is_khusus_baku_mutu', '1')
    //               ->where('gender_baku_mutu', $pasien_gender)
    //               ->where(function ($query) use ($pasien_umur) {
    //                 $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
    //                   ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
    //               })
    //               ->first();
    //           }
    //         }

    //         // jika tidak ada satuan di permohonan uji satuan maka ambil value dari baku mutu
    //         // jika ada nilai satuan di permohonan uji satuan maka ambil nilai dari tabel itu sendiri
    //         if ($value_satuan->satuan_permohonan_uji_parameter_klinik != null) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $value_satuan->satuan_permohonan_uji_parameter_klinik;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $value_satuan->unit->name_unit ?? null;
    //         } else if (isset($item_parameter_by_baku_mutu->unit_id)) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit_id;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit->name_unit ?? null;
    //         } else {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = null;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = null;
    //         }

    //          $item_permohonan_parameter_satuan['min'] = $item_parameter_by_baku_mutu->min ?? null;
    // $item_permohonan_parameter_satuan['max'] = $item_parameter_by_baku_mutu->max ?? null;
    // $item_permohonan_parameter_satuan['equal'] = $item_parameter_by_baku_mutu->equal ?? null;
    //         $item_permohonan_parameter_satuan['nilai_baku_mutu'] = $item_parameter_by_baku_mutu->nilai_baku_mutu ?? null;

    //         // menyiapkan data array untuk mapping jika data parameter memiliki subsatuan
    //         $item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'] = [];

    //         $data_permohonan_uji_subsatuan_klinik = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $value_satuan->id_permohonan_uji_parameter_klinik)
    //           ->get();

    //         if (count($data_permohonan_uji_subsatuan_klinik)) {
    //           foreach ($data_permohonan_uji_subsatuan_klinik as $key_subsatuan => $value_subsatuan) {
    //             $item_permohonan_parameter_subsatuan = [];
    //             $item_permohonan_parameter_subsatuan['id_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->id_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['permohonan_uji_parameter_klinik_id'] = $value_subsatuan->permohonan_uji_parameter_klinik_id;
    //             $item_permohonan_parameter_subsatuan['parameter_sub_satuan_klinik_id'] = $value_subsatuan->parameter_sub_satuan_klinik_id;
    //             $item_permohonan_parameter_subsatuan['nama_parameter_sub_satuan_klinik_id'] = $value_subsatuan->parametersubsatuanklinik->name_parameter_sub_satuan_klinik ?? null;
    //             $item_permohonan_parameter_subsatuan['hasil_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->hasil_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['flag_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->flag_permohonan_uji_sub_parameter_klinik;
    //             // $item_permohonan_parameter_subsatuan['baku_mutu_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['keterangan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->keterangan_permohonan_uji_sub_parameter_klinik;

    //             // cek data baku mutu jika memiliki data subsatuan dengan nilai rujukan
    //             if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
    //               $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
    //                 ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_subsatuan->parameter_sub_satuan_klinik_id)
    //                 ->first();

    //               if ($item_parameter_subsatuan_by_baku_mutu) {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik;
    //               } else {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //               }
    //             } else {
    //               $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //             }

    //             if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->unit->name_unit ?? null;
    //             } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit->name_unit ?? null;
    //             } else {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = null;
    //             }

    //             // cek dulu punya satuan di subparameter atau tidak
    //             // jika tidak ada maka ambil ke baku mutu
    //             // else null
    //             /* if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;
    //             } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
    //             } else {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;
    //             }

    //             $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik; */

    //             array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
    //           }
    //         }

    //         array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
    //       }
    //     }

    //     array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
    //   }
    // }

    $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->orderBy('jenis_parameter_klinik_id', 'ASC')
      ->orderBy('sorting_parameter_satuan', 'ASC')
      ->get();

    $data_jenis_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->groupBy('jenis_parameter_klinik_id')
      ->orderBy('sort_jenis_klinik', 'ASC')
      ->get();

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
            // dd($value_satuan->method_permohonan_uji_parameter_klinik);
            $item_permohonan_parameter_satuan['method_permohonan_uji_parameter_klinik'] = $value_satuan->method_permohonan_uji_parameter_klinik;
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

    $uniqueParameters = collect($arr_permohonan_parameter)
      ->map(function ($item) {
        $item['item_permohonan_parameter_satuan'] = collect($item['item_permohonan_parameter_satuan'])
          ->unique('nama_parameter_satuan_klinik')
          ->values();
        return $item;
      });

    $arr_permohonan_parameter = $uniqueParameters->toArray();


    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.analis-permohonan-uji-paramater-klinik', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_register_permohonan_uji_klinik' => $tgl_register,
      'tgl_pengujian' => $tgl_pengujian,
      // 'plebotomist' => $plebotomist,
      // 'vena' => $vena,
      // 'lokasi_lain' => $lokasi_lain,
      // 'penanganan_hasil' => $penanganan_hasil,
      'data_verifikasi_analitik' => $data_verifikasi_analitik,
      'nama_analis' => $nama_analis,
      'tgl_spesimen_darah' => $tgl_spesimen_darah,
      'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_detail_uji_paket' => $data_permohonan_uji_paket_klinik,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
  }

  public function storePermohonanUjiAnalis(Request $request, $id_permohonan_uji_klinik)
  {
    // dd($request->all());
    // try {
      DB::beginTransaction();

      $post = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

      if ($request->post('tglpengujian_permohonan_uji_klinik') !== null) {
        $tglpengujian_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $request->post('tglpengujian_permohonan_uji_klinik'))->format('Y-m-d H:i');
      } else {
        $tglpengujian_permohonan_uji_klinik = null;
      }

      $post->tglpengujian_permohonan_uji_klinik = $tglpengujian_permohonan_uji_klinik;
      $is_not_darah = $request->post('is_not_darah');
      if (!isset($is_not_darah) && $request->post('spesimen_darah_permohonan_uji_klinik') !== null) {
        $spesimen_darah_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $request->post('spesimen_darah_permohonan_uji_klinik'))->format('Y-m-d H:i');
      } else {
        $spesimen_darah_permohonan_uji_klinik = null;
      }

      $post->spesimen_darah_permohonan_uji_klinik = $spesimen_darah_permohonan_uji_klinik;
      $is_not_urine = $request->post('is_not_urine');

      if (!isset($is_not_urine) && $request->post('spesimen_urine_permohonan_uji_klinik') !== null) {
        $spesimen_urine_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $request->post('spesimen_urine_permohonan_uji_klinik'))->format('Y-m-d H:i');
      } else {
        $spesimen_urine_permohonan_uji_klinik = null;
      }

      if (!isset($request->name_analis_permohonan_uji_klinik)){
        return response()->json(['status' => false, 'pesan' => "Analis wajib diisi!"], 200);
      }

      $post->spesimen_urine_permohonan_uji_klinik = $spesimen_urine_permohonan_uji_klinik;
      $post->analis_permohonan_uji_klinik = $request->analis_permohonan_uji_klinik;
      $post->name_analis_permohonan_uji_klinik = $request->name_analis_permohonan_uji_klinik;
      $post->nip_analis_permohonan_uji_klinik = $request->nip_analis_permohonan_uji_klinik;
      $post->status_permohonan_uji_klinik = 'SELESAI';

      $simpan = $post->save();

      //verifiksi sample Output Hasil Px
      $verifikasi_hasil_px = VerificationActivitySample::where([
        ['is_klinik', '=', $id_permohonan_uji_klinik],
        ['id_verification_activity', '=', 3],
      ])->first();

      if($verifikasi_hasil_px == null){
        $verificationActivitySamplePX = new VerificationActivitySample();
        $verificationActivitySamplePX->id = Uuid::uuid4()->toString();
        $verificationActivitySamplePX->id_verification_activity = 3;

        // Gabungkan tanggal dan jam menjadi start_date
        $startDate = Carbon::createFromFormat('Y-m-d H:i', $post->tglpengujian_permohonan_uji_klinik)->format('Y-m-d H:i:s');

        // Set start_date
        $verificationActivitySamplePX->start_date = $startDate;

        // Tambahkan 10 menit untuk stop_date
        $verificationActivitySamplePX->stop_date = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)
        ->addMinutes(10) // Menambahkan 10 menit
        ->format('Y-m-d H:i:s');

        $verificationActivitySamplePX->nama_petugas = ucwords(strtolower($post->name_analis_permohonan_uji_klinik));
        $verificationActivitySamplePX->is_klinik = $id_permohonan_uji_klinik;
        // $verificationActivitySamplePX->is_done = 1;

        $verificationActivitySamplePX->save();
      }

      //verifiksi
      $verifikasi = VerificationActivitySample::where([
        ['is_klinik', '=', $id_permohonan_uji_klinik],
        ['id_verification_activity', '=', 4],
      ])->first();

      if(isset($verifikasi_hasil_px) && $verifikasi == null){
        // Simpan data Verifikasi step [4]
        $verificationActivitySample = new VerificationActivitySample();
        $verificationActivitySample->id = Uuid::uuid4()->toString();
        $verificationActivitySample->id_verification_activity = 4;

        // tambah 1 jam dari stop_date verifiksi sample Output Hasil Px
        $verificationActivitySample->start_date = Carbon::createFromFormat('Y-m-d H:i:s',  $verifikasi_hasil_px->stop_date)
        ->addHour()
        ->format('Y-m-d H:i:s');

        // Tambahkan 1 jam dari start_date
        $verificationActivitySample->stop_date = Carbon::createFromFormat('Y-m-d H:i:s',  $verificationActivitySample->start_date)
        ->addHour()
        ->format('Y-m-d H:i:s');

        $verificationActivitySample->nama_petugas = ucwords(strtolower($post->name_analis_permohonan_uji_klinik));
        $verificationActivitySample->is_klinik = $id_permohonan_uji_klinik;
        // $verificationActivitySample->is_done = 1;

        $verificationActivitySample->save();
      }

      $idPasienSatuSehat = $post->pasien->id_pasien_satu_sehat;




      if (isset($idPasienSatuSehat)) {
        # code...
        $practitioner = SatuSehatPractitioner::query()->where('name_satu_sehat_practitioner', '=', $request->name_analis_permohonan_uji_klinik)->first();
        $encounterId = $post->id_satu_sehat_encounter;
        $encounter = json_decode($post->encounter_json_satu_sehat, true);
        $encounterDisplay = $encounter['class']['display'] ?? null;

        $observationResults = [];
        $codingParameters = [];

        // mapping data untuk disimpan ke tb_permohonan_uji_paket_klinik, tb_permohonan_uji_parameter_klinik, tb_permohonan_uji_sub_parameter_klinik
        // cek dulu ada foreach untuk field name permohonan_uji_parameter_klinik
        if (count($request->permohonan_uji_parameter_klinik) > 0) {
          // mapping data untuk parameter satuan dulu
          foreach ($request->permohonan_uji_parameter_klinik as $key_permohonan_uji_parameter_klinik => $value_permohonan_uji_parameter_klinik) {
            // cek dulu setiap forloop ada data parameter subsatuan atau tidak
            if (isset($request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik])) {
              foreach ($request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik] as $key_parameter_sub_satuan_klinik => $value_parameter_sub_satuan_klinik) {
                // cek dulu di permohonan uji sub parameter klinik sudah ada datanya atau belum
                $check_pu_sub_parameter = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik])
                  ->where('id_permohonan_uji_sub_parameter_klinik', $request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik])
                  ->first();

                /* if (isset($check_pu_sub_parameter)) {
                $post_detail_sub_pupark =  $check_pu_sub_parameter;
                } else {
                $post_detail_sub_pupark = new PermohonanUjiSubParameterKlinik();
                } */

                if (isset($check_pu_sub_parameter)) {
                  $post_detail_sub_pupark = $check_pu_sub_parameter;
                  /* $post_detail_sub_pupark->permohonan_uji_parameter_klinik_id = $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
                  $post_detail_sub_pupark->parameter_sub_satuan_klinik_id = $request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik]; */
                  $post_detail_sub_pupark->hasil_permohonan_uji_sub_parameter_klinik = $request->hasil_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik];
                  $post_detail_sub_pupark->flag_permohonan_uji_sub_parameter_klinik = $request->flag_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                  $post_detail_sub_pupark->satuan_permohonan_uji_sub_parameter_klinik = $request->satuan_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                  $post_detail_sub_pupark->baku_mutu_permohonan_uji_sub_parameter_klinik = $request->baku_mutu_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                  $post_detail_sub_pupark->keterangan_permohonan_uji_sub_parameter_klinik = $request->keterangan_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;

                  $post_detail_sub_pupark->save();
                }
              }
            } else {
              $check_detail_pupark = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
                ->where('id_permohonan_uji_parameter_klinik', $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik])
                ->first();

              $post_detail_pupark = $check_detail_pupark;
              $post_detail_pupark->hasil_permohonan_uji_parameter_klinik = $request->hasil_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
              $post_detail_pupark->flag_permohonan_uji_parameter_klinik = "-";
              $post_detail_pupark->method_permohonan_uji_parameter_klinik = $request->method_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];

              $post_detail_pupark->satuan_permohonan_uji_parameter_klinik = $request->satuan_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
              $post_detail_pupark->baku_mutu_permohonan_uji_parameter_klinik = $request->baku_mutu_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
              $post_detail_pupark->keterangan_permohonan_uji_parameter_klinik = $request->keterangan_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
              $post_detail_pupark->save();

              $parameter = ParameterSatuanKlinik::query()->where('id_parameter_satuan_klinik', '=', $post_detail_pupark->parameter_satuan_klinik)->first();
              $codeIonic = $parameter->loinc_parameter_satuan_klinik;
              if ($codeIonic == null or $codeIonic == ""){
                $codeIonic = "31100-1";
              }

              $unit = Unit::query()->where('id_unit', '=', $request->satuan_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik])->first();

              // observation
              if (isset($encounterDisplay) and $encounterDisplay != "" and isset($encounterId) and $encounterId != ""){
                try {
                  $observationId = $this->createObservation(
                    $post_detail_pupark,
                    $codeIonic,
                    $parameter->name_parameter_satuan_klinik,
                    $idPasienSatuSehat,
                    $practitioner->code_satu_sehat_practitioner,
                    $encounterId,
                    $encounterDisplay,
                    Carbon::createFromFormat('d/m/Y H:i', $request->post('tglpengujian_permohonan_uji_klinik'))->toIso8601String(),
                    (float) $request->hasil_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik],
                    "microliter",
                    "uL"
                  );
                }catch (Exception $exception){
                  return response()->json(['status' => false, 'pesan' => $exception->getMessage()], 200);
                }

                $observationResults[] = [ "reference" => "Observation/" . $observationId];
                $codingParameters[] = [
                  "system" => "http://loinc.org",
                  "code" => $codeIonic,
                  "display" => $parameter->name_parameter_satuan_klinik
                ];
              }
            }
          }
        }

        $serviceRequests = [];
        $pakets = PermohonanUjiPaketKlinik::query()->where('permohonan_uji_klinik', '=', $post->id_permohonan_uji_klinik)->get(['id_service_request']);
        foreach ($pakets as $paket){
          $serviceRequests[] = ["reference" => "ServiceRequest/".$paket->id_service_request];
        }

        if (count($serviceRequests) > 0 and isset($encounterId) and $encounterId != ""){
          try {
            $this->createDiagnosticReport(
              $post,
              $codingParameters,
              $idPasienSatuSehat,
              $encounterId,
              Carbon::createFromFormat('d/m/Y H:i', $request->post('tglpengujian_permohonan_uji_klinik'))->toIso8601String(),
              $practitioner->code_satu_sehat_practitioner,
              $observationResults,
              $post->id_spesimen,
              $serviceRequests
            );
          }catch (Exception $exception){
            return response()->json(['status' => false, 'pesan' => $exception->getMessage()], 200);
          }
        }

      }else{
        foreach ($request->permohonan_uji_parameter_klinik as $key_permohonan_uji_parameter_klinik => $value_permohonan_uji_parameter_klinik) {
          // cek dulu setiap forloop ada data parameter subsatuan atau tidak
          if (isset($request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik])) {
            foreach ($request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik] as $key_parameter_sub_satuan_klinik => $value_parameter_sub_satuan_klinik) {
              // cek dulu di permohonan uji sub parameter klinik sudah ada datanya atau belum
              $check_pu_sub_parameter = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik])
                ->where('id_permohonan_uji_sub_parameter_klinik', $request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik])
                ->first();

              /* if (isset($check_pu_sub_parameter)) {
              $post_detail_sub_pupark =  $check_pu_sub_parameter;
              } else {
              $post_detail_sub_pupark = new PermohonanUjiSubParameterKlinik();
              } */

              if (isset($check_pu_sub_parameter)) {
                $post_detail_sub_pupark = $check_pu_sub_parameter;
                /* $post_detail_sub_pupark->permohonan_uji_parameter_klinik_id = $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
                $post_detail_sub_pupark->parameter_sub_satuan_klinik_id = $request->parameter_sub_satuan_klinik_id[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik]; */
                $post_detail_sub_pupark->hasil_permohonan_uji_sub_parameter_klinik = $request->hasil_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik];
                $post_detail_sub_pupark->flag_permohonan_uji_sub_parameter_klinik = $request->flag_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                $post_detail_sub_pupark->satuan_permohonan_uji_sub_parameter_klinik = $request->satuan_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                $post_detail_sub_pupark->baku_mutu_permohonan_uji_sub_parameter_klinik = $request->baku_mutu_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;
                $post_detail_sub_pupark->keterangan_permohonan_uji_sub_parameter_klinik = $request->keterangan_permohonan_uji_sub_parameter_klinik[$value_permohonan_uji_parameter_klinik][$value_parameter_sub_satuan_klinik] ?? null;

                $post_detail_sub_pupark->save();
              }
            }
          } else {
            $check_detail_pupark = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
              ->where('id_permohonan_uji_parameter_klinik', $request->permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik])
              ->first();

            $post_detail_pupark = $check_detail_pupark;
            $post_detail_pupark->hasil_permohonan_uji_parameter_klinik = $request->hasil_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
            $post_detail_pupark->flag_permohonan_uji_parameter_klinik = "-";
            $post_detail_pupark->method_permohonan_uji_parameter_klinik = $request->method_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];

            $post_detail_pupark->satuan_permohonan_uji_parameter_klinik = $request->satuan_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
            $post_detail_pupark->baku_mutu_permohonan_uji_parameter_klinik = $request->baku_mutu_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
            $post_detail_pupark->keterangan_permohonan_uji_parameter_klinik = $request->keterangan_permohonan_uji_parameter_klinik[$value_permohonan_uji_parameter_klinik];
            $post_detail_pupark->save();


          }
        }
      }

      DB::commit();

      if ($simpan == true) {
        return response()->json(['status' => true, 'pesan' => "Data permohonan uji klinik untuk analis berhasil diubah!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data permohonan uji klinik untuk analis tidak berhasil diubah!"], 200);
      }
    // } catch (\Exception $e) {
    //   DB::rollback();

    //   return response()->json(['status' => false, 'pesan' => 'System gagal melakukan perubahan!'], 200);
    // }
  }

  private function createObservation($parameter, $ionicCode, $parameterName, $patientId, $practitionerId, $encounterId, $encounterDisplay, $datetime, $result, $resultUnit, $resultCode)
  {
    $data = [
      "resourceType" => "Observation",
      "status" => "final",
      "category" => [
        [
          "coding" => [
            [
              "system" => "http://terminology.hl7.org/CodeSystem/observation-category",
              "code" => "laboratory",
              "display" => "Laboratory"
            ]
          ]
        ]
      ],
      "code" => [
        "coding" => [
          [
            "system" => "http://loinc.org",
            "code" => $ionicCode,
            "display" => $parameterName
          ]
        ]
      ],
      "subject" => [
        "reference" => "Patient/".$patientId
      ],
      "performer" => [
        [
          "reference" => "Practitioner/".$practitionerId
        ]
      ],
      "encounter" => [
        "reference" => "Encounter/".$encounterId,
        "display" => $encounterDisplay
      ],
      "effectiveDateTime" => $datetime,
      "issued" => $datetime,
      "valueQuantity" => [
        "value" => $result,
        "unit" => $resultUnit,
        "system" => "http://unitsofmeasure.org",
        "code" => $resultCode
      ]
    ];

    $idObservation = $parameter->id_observation;
    if ($idObservation != null or $idObservation != ""){
      $data['id'] = $idObservation;
      $response = $this->satuSehatHelper->put('Observation', $idObservation, $data);

      if ($response['status_code'] == '200'){
        return $idObservation;
      }else{
        throw new Exception("Gagal update observasi!, ". $response['body']['issue'][0]['details']['text']);
      }
    }

    $response = $this->satuSehatHelper->post('Observation', $data);

    if ($response['status_code'] == '201'){
      $parameter->id_observation = $response['body']['id'];
      $parameter->save();

      return $response['body']['id'];
    }else{
      throw new Exception("Gagal membuat observasi!, ". $response['body']['issue'][0]['details']['text']);
    }

  }

  private function createDiagnosticReport($permohonanUji, $codingParameters, $patientId, $encounterId, $datePengujian, $practitionerId, $observations, $specimenId, $serviceRequests)
  {
      $diagnosticReport = [
        "resourceType" => "DiagnosticReport",
        "identifier" => [
          [
            "system" => "http://sys-ids.kemkes.go.id/diagnostic/".env("ORG_ID")."/lab",
            "use" => "official",
            "value" => $permohonanUji->noregister_permohonan_uji_klinik // id permohonan uji
          ]
        ],
        "status" => "final",
        "category" => [
          [
            "coding" => [
              [
                "system" => "http://terminology.hl7.org/CodeSystem/v2-0074",
                "code" => "LAB", // Lab
                "display" => "Laboratory" // Laboratory
              ]
            ]
          ]
        ],
        "code" => [
          "coding" => $codingParameters
        ],
        "subject" => [
          "reference" => "Patient/".$patientId
        ],
        "encounter" => [
          "reference" => "Encounter/".$encounterId
        ],
        "effectiveDateTime" => $datePengujian,
        "issued" => $datePengujian,
        "performer" => [
          [
            "reference" => "Practitioner/".$practitionerId
          ],
          [
            "reference" => "Organization/".env("ORG_ID")
          ]
        ],
        "basedOn" => $serviceRequests,
        "result" => $observations,
        "specimen" => "",
        "conclusionCode" => [
          [
            "coding" => [
              [
                "system" => "http://snomed.info/sct",
                "code" => "260347006",
                "display" => "+"
              ]
            ]
          ]
        ]
      ];

      if ($specimenId == ""){
        unset($diagnosticReport['specimen']);
      }else{
        $idSpecimenArray = explode(',', $specimenId);
        $speciments = [];
        foreach ($idSpecimenArray as $idSpecimen){
          $speciments[] = ["reference" => "Specimen/".$idSpecimen];
        }
        $diagnosticReport["specimen"] = $speciments;
      }

    $idDiagnosticReport = $permohonanUji->id_diagnostic_report;
    if ($idDiagnosticReport != null or $idDiagnosticReport != ""){
      $diagnosticReport['id'] = $idDiagnosticReport;
      $response = $this->satuSehatHelper->put('DiagnosticReport', $idDiagnosticReport, $diagnosticReport);

      if ($response['status_code'] != '200'){
        throw new Exception("Gagal update diagnostic report!, ". $response['body']['issue'][0]['details']['text']);
      }
    }

    $response = $this->satuSehatHelper->post('DiagnosticReport', $diagnosticReport);
    if ($response['status_code'] == '201'){
      $permohonanUji->id_diagnostic_report = $response['body']["id"];
      $permohonanUji->save();
    }else{
      throw new Exception("Gagal membuat diagnostic report! " . $response['body']['issue'][0]['details']['text']);
    }

  }

  public function disabledPermohonanUjiAnalis($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');


    $verificationActivitySample = VerificationActivitySample::where('is_klinik',$id_permohonan_uji_klinik)->where('id_verification_activity',4)->first();


    if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian =Carbon::createFromFormat('Y-m-d H:i:s',$verificationActivitySample->start_date)->toISOString();
    } else {
      $tgl_pengujian = null;
    }

    if ($item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik !== null) {
      $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik)->toISOString();
    } else {
      $tgl_spesimen_darah = null;
    }

    if ($item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik !== null) {
      $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik)->toISOString();
    } else {
      $tgl_spesimen_urine = null;
    }

    // if ($item_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik !== null) {
    //   $plebotomist = $item_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik;
    // } else {
    //   $plebotomist = '';
    // }

    // if ($item_permohonan_uji_klinik->vena_permohonan_uji_klinik !== null) {
    //   $vena = $item_permohonan_uji_klinik->vena_permohonan_uji_klinik;
    // } else {
    //   $vena = '';
    // }

    // if ($item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik !== null) {
    //   $lokasi_lain = $item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik;
    // } else {
    //   $lokasi_lain = '';
    // }

    // if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik !== null) {
    //   $penanganan_hasil = $item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik;
    // } else {
    //   $penanganan_hasil = '';
    // }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    // if (count($data_permohonan_uji_paket_klinik) > 0) {
    //   foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
    //     $item_permohonan_parameter_paket = [];
    //     $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
    //     $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
    //     $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
    //     $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
    //     $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
    //     $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

    //     // get data perameter satuan yang ada dalam paket
    //     $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
    //       ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
    //       // ->groupBy('jenis_parameter_klinik_id')
    //       ->get();

    //     $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

    //     if (count($data_permohonan_uji_satuan_klinik) > 0) {
    //       foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
    //         // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
    //         // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
    //         $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
    //         $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

    //         $item_permohonan_parameter_satuan = [];
    //         $item_permohonan_parameter_satuan['id_permohonan_uji_parameter_klinik'] = $value_satuan->id_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_klinik'] = $value_satuan->permohonan_uji_klinik;
    //         $item_permohonan_parameter_satuan['permohonan_uji_paket_klinik'] = $value_satuan->permohonan_uji_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_paket_klinik'] = $value_satuan->parameter_paket_klinik;
    //         $item_permohonan_parameter_satuan['parameter_satuan_klinik'] = $value_satuan->parameter_satuan_klinik;
    //         $item_permohonan_parameter_satuan['nama_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->name_parameter_satuan_klinik ?? null;
    //         $item_permohonan_parameter_satuan['harga_permohonan_uji_parameter_klinik'] = $value_satuan->harga_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['hasil_permohonan_uji_parameter_klinik'] = $value_satuan->hasil_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['flag_permohonan_uji_parameter_klinik'] = $value_satuan->flag_permohonan_uji_parameter_klinik;
    //         $item_permohonan_parameter_satuan['keterangan_permohonan_uji_parameter_klinik'] = $value_satuan->keterangan_permohonan_uji_parameter_klinik;

    //         $id_parameter_jenis_klinik = null;
    //         if (isset($value_paket->parameter_jenis_klinik)) {
    //           $id_parameter_jenis_klinik = $value_paket->parameter_jenis_klinik;
    //         } else {
    //           $id_parameter_jenis_klinik = $value_satuan->permohonanujijenispaketklinik->parameter_jenis_klinik_id;
    //         }

    //         $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //           ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //           ->get();

    //         $item_parameter_by_baku_mutu = null;
    //         if (count($data_parameter_by_baku_mutu) > 0) {

    //           $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //             ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //             ->where('is_khusus_baku_mutu', '0')
    //             ->first();

    //           // cek dulu apakah ada data dengan data khusus sperti general atau specific

    //           if ($check_parameter_by_baku_mutu) {
    //             $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
    //           } else {
    //             $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $id_parameter_jenis_klinik)
    //               ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
    //               ->where('is_khusus_baku_mutu', '1')
    //               ->where('gender_baku_mutu', $pasien_gender)
    //               ->where(function ($query) use ($pasien_umur) {
    //                 $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
    //                   ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
    //               })
    //               ->first();
    //           }
    //         }

    //         // jika tidak ada satuan di permohonan uji satuan maka ambil value dari baku mutu
    //         // jika ada nilai satuan di permohonan uji satuan maka ambil nilai dari tabel itu sendiri
    //         if ($value_satuan->satuan_permohonan_uji_parameter_klinik != null) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $value_satuan->satuan_permohonan_uji_parameter_klinik;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $value_satuan->unit->name_unit ?? null;
    //         } else if (isset($item_parameter_by_baku_mutu->unit_id)) {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit_id;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = $item_parameter_by_baku_mutu->unit->name_unit ?? null;
    //         } else {
    //           $item_permohonan_parameter_satuan['satuan_permohonan_uji_parameter_klinik'] = null;
    //           $item_permohonan_parameter_satuan['nama_satuan_permohonan_uji_parameter_klinik'] = null;
    //         }

    //          $item_permohonan_parameter_satuan['min'] = $item_parameter_by_baku_mutu->min ?? null;
    // $item_permohonan_parameter_satuan['max'] = $item_parameter_by_baku_mutu->max ?? null;
    // $item_permohonan_parameter_satuan['equal'] = $item_parameter_by_baku_mutu->equal ?? null;
    //         $item_permohonan_parameter_satuan['nilai_baku_mutu'] = $item_parameter_by_baku_mutu->nilai_baku_mutu ?? null;

    //         // menyiapkan data array untuk mapping jika data parameter memiliki subsatuan
    //         $item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'] = [];

    //         $data_permohonan_uji_subsatuan_klinik = PermohonanUjiSubParameterKlinik::where('permohonan_uji_parameter_klinik_id', $value_satuan->id_permohonan_uji_parameter_klinik)
    //           ->get();

    //         if (count($data_permohonan_uji_subsatuan_klinik)) {
    //           foreach ($data_permohonan_uji_subsatuan_klinik as $key_subsatuan => $value_subsatuan) {
    //             $item_permohonan_parameter_subsatuan = [];
    //             $item_permohonan_parameter_subsatuan['id_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->id_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['permohonan_uji_parameter_klinik_id'] = $value_subsatuan->permohonan_uji_parameter_klinik_id;
    //             $item_permohonan_parameter_subsatuan['parameter_sub_satuan_klinik_id'] = $value_subsatuan->parameter_sub_satuan_klinik_id;
    //             $item_permohonan_parameter_subsatuan['nama_parameter_sub_satuan_klinik_id'] = $value_subsatuan->parametersubsatuanklinik->name_parameter_sub_satuan_klinik ?? null;
    //             $item_permohonan_parameter_subsatuan['hasil_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->hasil_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['flag_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->flag_permohonan_uji_sub_parameter_klinik;
    //             // $item_permohonan_parameter_subsatuan['baku_mutu_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['keterangan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->keterangan_permohonan_uji_sub_parameter_klinik;

    //             // cek data baku mutu jika memiliki data subsatuan dengan nilai rujukan
    //             if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
    //               $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
    //                 ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_subsatuan->parameter_sub_satuan_klinik_id)
    //                 ->first();

    //               if ($item_parameter_subsatuan_by_baku_mutu) {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik;
    //               } else {
    //                 $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //                 $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //               }
    //             } else {
    //               $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = null;
    //               $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = null;
    //             }

    //             if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->unit->name_unit ?? null;
    //             } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit->name_unit ?? null;
    //             } else {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;

    //               $item_permohonan_parameter_subsatuan['nama_satuan_permohonan_uji_sub_parameter_klinik'] = null;
    //             }

    //             // cek dulu punya satuan di subparameter atau tidak
    //             // jika tidak ada maka ambil ke baku mutu
    //             // else null
    //             /* if ($value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik != null) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $value_subsatuan->satuan_permohonan_uji_sub_parameter_klinik;
    //             } else if (isset($item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik)) {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
    //             } else {
    //               $item_permohonan_parameter_subsatuan['satuan_permohonan_uji_sub_parameter_klinik'] = null;
    //             }

    //             $item_permohonan_parameter_subsatuan['min_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->min_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['max_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->max_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['equal_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->equal_baku_mutu_detail_parameter_klinik;
    //             $item_permohonan_parameter_subsatuan['nilai_baku_mutu_detail_parameter_klinik'] = $item_parameter_subsatuan_by_baku_mutu->nilai_baku_mutu_detail_parameter_klinik; */

    //             array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
    //           }
    //         }

    //         array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
    //       }
    //     }

    //     array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
    //   }
    // }

    $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->orderBy('jenis_parameter_klinik_id', 'ASC')
      ->orderBy('sorting_parameter_satuan', 'ASC')
      ->get();

    $data_jenis_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->groupBy('jenis_parameter_klinik_id')
      ->orderBy('sort_jenis_klinik', 'ASC')
      ->get();

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
            // dd($value_satuan->method_permohonan_uji_parameter_klinik);
            $item_permohonan_parameter_satuan['method_permohonan_uji_parameter_klinik'] = $value_satuan->method_permohonan_uji_parameter_klinik;
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

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.disabled-analis-permohonan-uji-paramater-klinik', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_register_permohonan_uji_klinik' => $tgl_register,
      'tgl_pengujian' => $tgl_pengujian,
      // 'plebotomist' => $plebotomist,
      // 'vena' => $vena,
      // 'lokasi_lain' => $lokasi_lain,
      // 'penanganan_hasil' => $penanganan_hasil,
      'tgl_spesimen_darah' => $tgl_spesimen_darah,
      'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_detail_uji_paket' => $data_permohonan_uji_paket_klinik,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
  }

  public function convertToRoman($integer)
  {
    // Convert the integer into an integer (just to make sure)
    $integer = intval($integer);
    $result = '';

    // Create a lookup array that contains all of the Roman numerals.
    $lookup = array(
      'M' => 1000,
      'CM' => 900,
      'D' => 500,
      'CD' => 400,
      'C' => 100,
      'XC' => 90,
      'L' => 50,
      'XL' => 40,
      'X' => 10,
      'IX' => 9,
      'V' => 5,
      'IV' => 4,
      'I' => 1,
    );

    foreach ($lookup as $roman => $value) {
      // Determine the number of matches
      $matches = intval($integer / $value);

      // Add the same number of characters to the string
      $result .= str_repeat($roman, $matches);

      // Set the integer to be the remainder of the integer and the value
      $integer = $integer % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
  }

  public function getDataPermohonanUjiKlinikPayment(Request $request)
  {
    $id_permohonan_uji_klinik = $request->permohonan_uji_klinik_id;
    $itemPermohonanUjiKlinik = PermohonanUjiKlinik2::findOrFail($id_permohonan_uji_klinik);

    $data = [
      'id_permohonan_uji_klinik' => $itemPermohonanUjiKlinik->id_permohonan_uji_klinik ?? null,
      'nota_petugas' => Auth::user()->id,
      'nota_namapetugas' => Auth::user()->name,
      'nama_pasien' => $itemPermohonanUjiKlinik->pasien->nama_pasien ?? null,
      'alamat_pasien' => $itemPermohonanUjiKlinik->pasien->alamat_pasien ?? null,
      'total_harga' => $itemPermohonanUjiKlinik->total_harga_permohonan_uji_klinik,
      'total_harga_custom' => $itemPermohonanUjiKlinik->total_harga_permohonan_uji_klinik != null ? rupiah($itemPermohonanUjiKlinik->total_harga_permohonan_uji_klinik) : 'Rp. 0',
    ];

    return response()->json($data);
  }

  public function storeDataPermohonanUjiKlinikPayment(Request $request)
  {
    if ($request->terbayar_permohonan_uji_payment_klinik == 0) {
      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal harus diisi!']);
    }

    if ($request->terbayar_permohonan_uji_payment_klinik < 0) {
      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal tidak boleh mines!']);
    }

    // Mulai DB transaction
    DB::beginTransaction();

    try {
      // Ambil nilai maksimal no nota dan tambahkan 1
      $max_nota = (int) PermohonanUjiPaymentKlinik::max('no_nota_permohonan_uji_payment_klinik');

      // store ke payment
      $post = new PermohonanUjiPaymentKlinik();
      $post->permohonan_uji_klinik_id = $request->id_permohonan_uji_klinik;
      $post->no_nota_permohonan_uji_payment_klinik = $max_nota + 1;
      $post->nota_petugas_permohonan_uji_payment_klinik = auth()->id();
      $post->total_harga_permohonan_uji_payment_klinik = $request->total_harga_permohonan_uji_payment_klinik;
      $post->terbayar_permohonan_uji_payment_klinik = $request->terbayar_permohonan_uji_payment_klinik;

      $post->date_done_estimation_permohonan_uji_payment_klinik = date('Y-m-d H:i:s', time());
      $simpan = $post->save();

      // Update is_paid pada permohonan uji klinik
      $permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($request->id_permohonan_uji_klinik);
      if ($request->terbayar_permohonan_uji_payment_klinik > $permohonan_uji_klinik->total_harga_permohonan_uji_klinik) {
        return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal terlalu besar!']);
      }

      if ($request->total_harga_permohonan_uji_payment_klinik == $request->terbayar_permohonan_uji_payment_klinik) {
        // $permohonan_uji_klinik->is_paid_permohonan_uji_klinik='1';
        $permohonan_uji_klinik->status_pembayaran = '1';
        $permohonan_uji_klinik->save();
      }

      // Commit transaksi jika semua operasi berhasil
      DB::commit();

      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['status' => 'Proses pembayaran berhasil disimpan!']);
    } catch (\Exception $e) {
      // Rollback transaksi jika terjadi kesalahan
      DB::rollBack();

      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Proses pembayaran tidak berhasil disimpan!']);
    }
  }

  public function updateDataPermohonanUjiKlinikPayment(Request $request)
  {
    // dd($request->all());

    if ($request->terbayar_permohonan_uji_payment_klinik == 0) {
      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal harus diisi!']);
    }

    $permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($request->id_permohonan_uji_klinik);
    if (($request->total_terbayar + $request->terbayar_permohonan_uji_payment_klinik) > $permohonan_uji_klinik->total_harga_permohonan_uji_klinik) {
      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal terlalu besar!']);
    }

    if ($request->terbayar_permohonan_uji_payment_klinik < 0) {
      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Pembayaran gagal, nominal tidak boleh mines!']);
    }

    // Mulai DB transaction
    DB::beginTransaction();

    try {
      // update ke payment
      $post = PermohonanUjiPaymentKlinik::where('permohonan_uji_klinik_id', $request->id_permohonan_uji_klinik)->first();
      $post->nota_petugas_permohonan_uji_payment_klinik = auth()->id();
      $post->total_harga_permohonan_uji_payment_klinik = $request->total_harga_permohonan_uji_payment_klinik;
      $post->terbayar_permohonan_uji_payment_klinik += $request->terbayar_permohonan_uji_payment_klinik;
      $post->date_done_estimation_permohonan_uji_payment_klinik = date('Y-m-d H:i:s', time());
      $simpan = $post->save();

      // Update is_paid pada permohonan uji klinik
      if (($request->total_terbayar + $request->terbayar_permohonan_uji_payment_klinik) == $request->total_harga_permohonan_uji_payment_klinik) {
        $permohonan_uji_klinik->status_pembayaran = '1';
        $permohonan_uji_klinik->save();
      }

      // Commit transaksi jika semua operasi berhasil
      DB::commit();

      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['status' => 'Proses pembayaran berhasil disimpan!']);
    } catch (\Exception $e) {
      // Rollback transaksi jika terjadi kesalahan
      DB::rollBack();

      return redirect()->route('elits-permohonan-uji-klinik-2.index')->with(['error' => 'Proses pembayaran tidak berhasil disimpan!']);
    }
  }


  public function createPermohonanUjiRekomendasiDokter($id_permohonan_uji_klinik)
  {
    $item = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM YYYY');

    if ($item->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglpengujian_permohonan_uji_klinik)->isoFormat('D/M/Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    if ($item->spesimen_darah_permohonan_uji_klinik !== null) {
      $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_darah_permohonan_uji_klinik)->isoFormat('D/M/Y HH:mm');
    } else {
      $tgl_spesimen_darah = '-';
    }

    if ($item->spesimen_urine_permohonan_uji_klinik !== null) {
      $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item->spesimen_urine_permohonan_uji_klinik)->isoFormat('D/M/Y HH:mm');
    } else {
      $tgl_spesimen_urine = '-';
    }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    $arr_permohonan_parameter = [];

    $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->orderBy('jenis_parameter_klinik_id', 'ASC')
      ->orderBy('sorting_parameter_satuan', 'ASC')
      ->get();

    $data_jenis_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->groupBy('jenis_parameter_klinik_id')
      ->orderBy('sort_jenis_klinik', 'ASC')
      ->get();




    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

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

    // dd($data_detail_uji_paket);

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.dokter-permohonan-uji-klinik', [
      'item' => $item,
      'tgl_register_permohonan_uji_klinik' => $tgl_register,
      'tgl_pengujian' => $tgl_pengujian,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
      'tgl_spesimen_darah' => $tgl_spesimen_darah,
      'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_detail_uji_paket' => $data_permohonan_uji_paket_klinik,
    ]);
  }

  public function storePermohonanUjiRekomendasiDokter(Request $request, $id_permohonan_uji_klinik)
  {
    DB::beginTransaction();

    try {
      $post = PermohonanUjiKlinik2::findOrFail($id_permohonan_uji_klinik);
      $post->dokter_rekomendasi_permohonan_uji_klinik = Auth::user()->id;
      $post->save();

      $check_detail_klinik = PermohonanUjiAnalisKlinik::where('permohonan_uji_klinik_id', $id_permohonan_uji_klinik)->first();

      if ($check_detail_klinik) {
        $post_detail_klinik = PermohonanUjiAnalisKlinik::where('permohonan_uji_klinik_id', $id_permohonan_uji_klinik)->first();
        $post_detail_klinik->kesimpulan_permohonan_uji_analis_klinik = $request->kesimpulan_permohonan_uji_analis_klinik;
        $post_detail_klinik->kategori_permohonan_uji_analis_klinik = $request->kategori_permohonan_uji_analis_klinik;
        $post_detail_klinik->saran_permohonan_uji_analis_klinik = $request->saran_permohonan_uji_analis_klinik;
        $simpan = $post_detail_klinik->save();
      } else {
        $post_detail_klinik = new PermohonanUjiAnalisKlinik();
        $post_detail_klinik->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
        $post_detail_klinik->kesimpulan_permohonan_uji_analis_klinik = $request->kesimpulan_permohonan_uji_analis_klinik;
        $post_detail_klinik->kategori_permohonan_uji_analis_klinik = $request->kategori_permohonan_uji_analis_klinik;
        $post_detail_klinik->saran_permohonan_uji_analis_klinik = $request->saran_permohonan_uji_analis_klinik;
        $simpan = $post_detail_klinik->save();
      }

      DB::commit();

      if ($simpan == true) {
        return response()->json(['status' => true, 'pesan' => "Data rekomendasi berhasil disimpan!"], 200);
      } else {
        return response()->json(['status' => false, 'pesan' => "Data rekomendasi tidak berhasil disimpan!"], 200);
      }
    } catch (\Exception $e) {
      DB::rollback();

      return response()->json(['status' => false, 'pesan' => 'System gagal melakukan perubahan!'], 200);
    }
  }

  public function _printPermohonanUjiKlinikNota($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $itemPermohonanUjiPaymentKlinik = PermohonanUjiPaymentKlinik::where('permohonan_uji_klinik_id', $id_permohonan_uji_klinik)->first();

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->with(['parameterjenisklinik', 'parameterpaketklinik', 'permohonanujiklinik', 'permohonanujiparameterklinik'])
      ->orderBy('created_at', 'desc')
      ->whereNull('deleted_at')
      ->get();

    $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      // ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    // forloop into view
    $detail_permohonan_uji_klinik = [];

    // looping paket dulu
    foreach ($data_permohonan_uji_paket_klinik as $key => $value) {
      $item_data_permohonan_uji_paket_klinik = [];
      $item_data_permohonan_uji_paket_klinik["id_parameter_jenis_klinik"] = $value->parameter_jenis_klinik;
      $item_data_permohonan_uji_paket_klinik["name_parameter_jenis_klinik"] = $value->parameterjenisklinik->name_parameter_jenis_klinik;
      $item_data_permohonan_uji_paket_klinik["type_permohonan_uji_paket_klinik"] = $value->type_permohonan_uji_paket_klinik;
      $item_data_permohonan_uji_paket_klinik["id_parameter_paket_klinik"] = $value->parameter_paket_klinik;
      $item_data_permohonan_uji_paket_klinik["name_parameter_paket_klinik"] = $value->parameterpaketklinik->name_parameter_paket_klinik ?? null;
      $item_data_permohonan_uji_paket_klinik["harga_permohonan_uji_paket_klinik"] = $value->harga_permohonan_uji_paket_klinik;

      // panggil parameter
      $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
        ->where('permohonan_uji_paket_klinik', $value->id_permohonan_uji_paket_klinik)
        ->whereNull('deleted_at')
        ->get();

      $item_data_permohonan_uji_paket_klinik["permohonan_uji_parameter_klinik"] = [];

      foreach ($data_permohonan_uji_parameter_klinik as $key_pupk => $value_pupk) {
        $bakumutu = BakuMutu::where('parameter_jenis_klinik_id', $value->parameter_jenis_klinik)
          ->where('parameter_satuan_klinik_id', $value_pupk->parameter_satuan_klinik)
          ->first();

        $item_permohonan_uji_parameter_klinik = [];
        $item_permohonan_uji_parameter_klinik['permohonan_uji_klinik'] = $value_pupk->permohonan_uji_klinik;
        $item_permohonan_uji_parameter_klinik['permohonan_uji_paket_klinik'] = $value_pupk->permohonan_uji_paket_klinik;
        $item_permohonan_uji_parameter_klinik['id_parameter_satuan_klinik'] = $value_pupk->parameter_satuan_klinik;
        $item_permohonan_uji_parameter_klinik['name_parameter_satuan_klinik'] = $value_pupk->parametersatuanklinik->name_parameter_satuan_klinik;
        $item_permohonan_uji_parameter_klinik['harga_permohonan_uji_parameter_klinik'] = $value_pupk->harga_permohonan_uji_parameter_klinik;
        $item_permohonan_uji_parameter_klinik['hasil_permohonan_uji_parameter_klinik'] = $value_pupk->hasil_permohonan_uji_parameter_klinik;
        $item_permohonan_uji_parameter_klinik['flag_permohonan_uji_parameter_klinik'] = $value_pupk->flag_permohonan_uji_parameter_klinik;
        $item_permohonan_uji_parameter_klinik['id_satuan_permohonan_uji_parameter_klinik'] = $value_pupk->satuan_permohonan_uji_parameter_klinik;
        $item_permohonan_uji_parameter_klinik['name_satuan_permohonan_uji_parameter_klinik'] = $value_pupk->unit->name_unit;
        $item_permohonan_uji_parameter_klinik['id_baku_mutu_permohonan_uji_parameter_klinik'] = $value_pupk->baku_mutu_permohonan_uji_parameter_klinik;
        $item_permohonan_uji_parameter_klinik['keterangan_permohonan_uji_parameter_klinik'] = $value_pupk->keterangan_permohonan_uji_parameter_klinik;

        $item_permohonan_uji_parameter_klinik['permohonan_uji_sub_parameter_klinik'] = [];

        // get data parameter sub satuan

        foreach ($value_pupk->permohonanujisubparameterklinik as $key_puspk => $value_puspk) {
          $item_permohonan_uji_sub_parameter_klinik = [];
          $item_permohonan_uji_sub_parameter_klinik['permohonan_uji_parameter_klinik_id'] = $value_puspk->permohonan_uji_parameter_klinik_id;
          $item_permohonan_uji_sub_parameter_klinik['parameter_sub_satuan_klinik_id'] = $value_puspk->parameter_sub_satuan_klinik_id;
          $item_permohonan_uji_sub_parameter_klinik['name_parameter_sub_satuan_klinik_id'] = $value_puspk->parametersubsatuanklinik->name_parameter_sub_satuan_klinik;
          $item_permohonan_uji_sub_parameter_klinik['hasil_permohonan_uji_sub_parameter_klinik'] = $value_puspk->hasil_permohonan_uji_sub_parameter_klinik;
          $item_permohonan_uji_sub_parameter_klinik['flag_permohonan_uji_sub_parameter_klinik'] = $value_puspk->flag_permohonan_uji_sub_parameter_klinik;
          $item_permohonan_uji_sub_parameter_klinik['satuan_permohonan_uji_sub_parameter_klinik'] = $value_puspk->satuan_permohonan_uji_sub_parameter_klinik;
          $item_permohonan_uji_sub_parameter_klinik['baku_mutu_permohonan_uji_sub_parameter_klinik'] = $value_puspk->baku_mutu_permohonan_uji_sub_parameter_klinik;
          $item_permohonan_uji_sub_parameter_klinik['keterangan_permohonan_uji_sub_parameter_klinik'] = $value_puspk->keterangan_permohonan_uji_sub_parameter_klinik;

          array_push($item_permohonan_uji_parameter_klinik['permohonan_uji_sub_parameter_klinik'], $item_permohonan_uji_sub_parameter_klinik);
        }

        array_push($item_data_permohonan_uji_paket_klinik["permohonan_uji_parameter_klinik"], $item_permohonan_uji_parameter_klinik);
      }

      array_push($detail_permohonan_uji_klinik, $item_data_permohonan_uji_paket_klinik);
    }

    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    $pdf = PDF::loadView(
      'masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.nota',
      [
        'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
        'detail_payment' => $itemPermohonanUjiPaymentKlinik,
        'detail_permohonan_uji_klinik' => $detail_permohonan_uji_klinik,
      ]
    )
      ->setPaper('a5', 'landscape');
    return $pdf->stream();

    // dd($detail_permohonan_uji_klinik);

    /* return view(
  'masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.nota',
  [
  'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
  'detail_payment' => $itemPermohonanUjiPaymentKlinik,
  'tgl_register' => $tgl_register,
  'tgl_pengambilan' => $tgl_pengambilan,
  'tgl_pengujian' => $tgl_pengujian,
  'tgl_spesimen_darah' => $tgl_spesimen_darah,
  'tgl_spesimen_urine' => $tgl_spesimen_urine,
  'detail_permohonan_uji_klinik' => $detail_permohonan_uji_klinik
  ]
  ); */
  }

  public function printPermohonanUjiKlinikNota($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);


    $itemPermohonanUjiPaymentKlinik = PermohonanUjiPaymentKlinik::where('permohonan_uji_klinik_id', $id_permohonan_uji_klinik)->first();

    // $parameterCustom = DB::table('tb_permohonan_uji_paket_klinik')->where('tb_permohonan_uji_paket_klinik.permohonan_uji_klinik', '=', $id_permohonan_uji_klinik)
    //   ->join('tb_permohonan_uji_klinik_2', function ($join) {
    //     $join->on('tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik', '=', 'tb_permohonan_uji_paket_klinik.permohonan_uji_klinik')
    //       ->whereNull('tb_permohonan_uji_klinik_2.deleted_at')
    //       ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
    //   })
    //   ->join('ms_parameter_paket_klinik', function ($join) {
    //     $join->on('tb_permohonan_uji_paket_klinik.parameter_paket_klinik', '=', 'ms_parameter_paket_klinik.id_parameter_paket_klinik')
    //       ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at')
    //       ->whereNull('ms_parameter_paket_klinik.deleted_at');
    //   })
    //   // ->leftjoin('ms_parameter_satuan_klinik', function ($join) {
    //   //   $join->on('ms_parameter_satuan_klinik.id_parameter_satuan_klinik', '=', 'ms_parameter_paket_klinik.id_parameter_paket_klinik')
    //   //     ->whereNull('ms_parameter_satuan_klinik.deleted_at')
    //   //     ->whereNull('ms_parameter_paket_klinik.deleted_at');
    //   // })
    //   ->where('tb_permohonan_uji_paket_klinik.type_permohonan_uji_paket_klinik', "P")
    //   // ->select(
    //   //   'ms_parameter_satuan_klinik.name_parameter_satuan_klinik',
    //   //   'ms_parameter_satuan_klinik.id_parameter_satuan_klinik',
    //   //   'tb_permohonan_uji_parameter_klinik.parameter_satuan_klinik',
    //   //   'tb_permohonan_uji_parameter_klinik.harga_permohonan_uji_parameter_klinik',
    //   //   DB::raw('count(*) as count_method')
    //   // )
    //   // ->groupBy(
    //   //   'ms_parameter_satuan_klinik.name_parameter_satuan_klinik',
    //   //   'ms_parameter_satuan_klinik.id_parameter_satuan_klinik',
    //   //   'tb_permohonan_uji_parameter_klinik.parameter_satuan_klinik',
    //   //   'tb_permohonan_uji_parameter_klinik.harga_permohonan_uji_parameter_klinik'
    //   // )
    //   ->get();

    //   // dd($parameterCustom);
    $value_items = array();

    // $i=0;
    // foreach ($parameterCustom as $valueCustom) {
    //   $nilaiPermohonanUjiKlinik["name_item"] = $valueCustom->name_parameter_paket_klinik;
    //   $nilaiPermohonanUjiKlinik["count_item"] = $i;
    //   $nilaiPermohonanUjiKlinik["price_item"] = $valueCustom->harga_parameter_paket_klinik;
    //   $nilaiPermohonanUjiKlinik["total"] = $valueCustom->harga_parameter_paket_klinik * $valueCustom->count_method;

    //   array_push($value_items, $nilaiPermohonanUjiKlinik);
    //   $i++;
    // }

    $nilaiPermohonanUjiKlinik=[];
    // $parameterPaket = DB::table('tb_permohonan_uji_paket_klinik')->where('tb_permohonan_uji_paket_klinik.permohonan_uji_klinik', '=', $id_permohonan_uji_klinik)
    //   ->join('tb_permohonan_uji_klinik_2', function ($join) {
    //     $join->on('tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik', '=', 'tb_permohonan_uji_paket_klinik.permohonan_uji_klinik')
    //       ->whereNull('tb_permohonan_uji_klinik_2.deleted_at')
    //       ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
    //   })
    //   ->join('ms_parameter_paket_klinik', function ($join) {
    //     $join->on('ms_parameter_paket_klinik.id_parameter_paket_klinik', '=', 'tb_permohonan_uji_paket_klinik.parameter_paket_klinik')
    //       ->whereNull('ms_parameter_paket_klinik.deleted_at')
    //       ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
    //   })
    //   ->where('tb_permohonan_uji_paket_klinik.type_permohonan_uji_paket_klinik', "P")
    //   ->select(
    //     'ms_parameter_paket_klinik.name_parameter_paket_klinik',
    //     'tb_permohonan_uji_paket_klinik.parameter_paket_klinik',
    //     'tb_permohonan_uji_paket_klinik.harga_permohonan_uji_paket_klinik',
    //     DB::raw('count(tb_permohonan_uji_paket_klinik.parameter_paket_klinik) as count_method')
    //   )
    //   ->groupBy(
    //     'ms_parameter_paket_klinik.name_parameter_paket_klinik',
    //     'tb_permohonan_uji_paket_klinik.parameter_paket_klinik',
    //     'tb_permohonan_uji_paket_klinik.harga_permohonan_uji_paket_klinik'
    //   )
    //   ->get();


    $parameterPaket = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
    ->orderBy('parameter_paket_extra', 'desc')
    ->get();
    // dd( $parameterPaket);

    $temp_datas = [];

    $temp_extra_paket = null;
    foreach($parameterPaket as $val){
      // dd($val->parameter_paket_extra);
      if(isset($val->parameter_paket_extra) && $temp_extra_paket != $val->parameter_paket_extra && $val->parameter_paket_extra != ''){
        $extra_paket = ParameterPaketExtra::where('id_parameter_paket_extra', $val->parameter_paket_extra)->first();
        // dd('test');
        $value_extra_paket = [
          "id_permohonan_uji_paket_klinik" => $val->id_permohonan_uji_paket_klinik,
          "permohonan_uji_klinik" => $val->permohonan_uji_klinik,
          "parameter_jenis_klinik" =>  $val->parameter_jenis_klinik,
          "type_permohonan_uji_paket_klinik" =>  'EP',
          "nama_parameter_paket_extra" => $extra_paket->nama_parameter_paket_extra,
          "parameter_paket_klinik" =>  $val->parameter_paket_klinik,
          "parameter_paket_extra" =>  $val->parameter_paket_extra,
          "harga_permohonan_uji_paket_klinik" =>  $extra_paket->harga_parameter_paket_extra,
          "is_prolanis_gula" =>  $val->is_prolanis_gula,
          "is_prolanis_urine" =>  $val->is_prolanis_urine,
          "is_haji" => $val->is_haji,
          "id_service_request" =>  $val->id_service_request,
          "response_service_request" =>  $val->response_service_request,
        ];

        array_push($temp_datas, $value_extra_paket);
        $temp_extra_paket = $val->parameter_paket_extra;

      }elseif($val->parameter_paket_extra == ''){
        array_push($temp_datas, $val);
      }
    }

    // dd( $temp_datas);

    $parameterPaket = $temp_datas;
    $count = 0;

    foreach ($parameterPaket as $valuePaket) {
      if($valuePaket['type_permohonan_uji_paket_klinik'] == 'EP'){
        $nilaiPermohonanUjiKlinik["name_item"] = $valuePaket['nama_parameter_paket_extra'];
        $nilaiPermohonanUjiKlinik["price_item"] = $valuePaket['harga_permohonan_uji_paket_klinik'];
        $nilaiPermohonanUjiKlinik["total"] = $count + $valuePaket['harga_permohonan_uji_paket_klinik'];

      }else{
        $paket = ParameterPaketKlinik::where('id_parameter_paket_klinik', $valuePaket->parameter_paket_klinik)->first();
          // dd($paket);
        $nilaiPermohonanUjiKlinik["name_item"] = $paket->name_parameter_paket_klinik;
        $nilaiPermohonanUjiKlinik["price_item"] = $valuePaket->harga_permohonan_uji_paket_klinik;
        $nilaiPermohonanUjiKlinik["total"] = $count + $valuePaket->harga_permohonan_uji_paket_klinik;
      }
      // $nilaiPermohonanUjiKlinik["count_item"] = $valuePaket->count_method;

      array_push($value_items, $nilaiPermohonanUjiKlinik);
    }

    // tanggal pemeriksaan
    try {
      $tanggalPemeriksaan = VerificationActivitySample::query()
        ->where('is_klinik', '=', $id_permohonan_uji_klinik)
        ->where('id_verification_activity', '=', 1)
        ->firstOrFail()
        ->stop_date;
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
      $tanggalPemeriksaan = null;
    }

    // dd($value_items);

    // Pengecekan kondisi
//    if ($itemPermohonanUjiPaymentKlinik != null) {
//      if ($itemPermohonanUjiPaymentKlinik->terbayar_permohonan_uji_payment_klinik == $itemPermohonanUjiPaymentKlinik->total_harga_permohonan_uji_payment_klinik) {
//        $tanggal_transaksi_lunas = $itemPermohonanUjiPaymentKlinik->date_done_estimation_permohonan_uji_payment_klinik;
//      }else{
//        $tanggal_transaksi_lunas = null;
//      }
//    }else{
//      $tanggal_transaksi_lunas = null;
//    }

    $pdf = PDF::loadView(
      'masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.nota',
      [
        'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
        'detail_payment' => $itemPermohonanUjiPaymentKlinik,
        'value_items' => $value_items,
        'tanggal_transaksi_lunas' => $tanggalPemeriksaan,
      ]
    )
      ->setPaper('a4', 'potrait');
    return $pdf->stream();

    /* return view(
  'masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.nota',
  [
  'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
  'detail_payment' => $itemPermohonanUjiPaymentKlinik,
  'tgl_register' => $tgl_register,
  'tgl_pengambilan' => $tgl_pengambilan,
  'tgl_pengujian' => $tgl_pengujian,
  'tgl_spesimen_darah' => $tgl_spesimen_darah,
  'tgl_spesimen_urine' => $tgl_spesimen_urine,
  'detail_permohonan_uji_klinik' => $detail_permohonan_uji_klinik
  ]
  ); */
  }

  public function printPermohonanUjiKlinikFormulir($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $item_pasien = Pasien::find($item_permohonan_uji_klinik->pasien_permohonan_uji_klinik);
    $parameter_jenis_klinik = ParameterJenisKlinik::with('pakets')->orderBy('created_at', 'asc')->get();
    $parameter_paket_klinik = PermohonanUjiParameterKlinik2::where('id_permohonan_uji_klinik', $id_permohonan_uji_klinik)->get();
    // dd($parameter_paket_klinik->id_jenis_parameter_klinik);
    $nama_petugas = auth()->user()->name;
    $permohonanUjiPaketKlinik = PermohonanUjiPaketKlinik::query()
      ->where('permohonan_uji_klinik', '=', $id_permohonan_uji_klinik)
      ->pluck('parameter_paket_klinik')
      ->toArray();

    $umurPerwakilan = '-';
    if (isset($item_permohonan_uji_klinik->tanggal_lahir_perwakilan_permohonan_uji_klinik)){
      $tanggalLahir = new DateTime($item_permohonan_uji_klinik->tanggal_lahir_perwakilan_permohonan_uji_klinik);
      $sekarang = new DateTime();
      $umurPerwakilan = $sekarang->diff($tanggalLahir)->y;
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.formulir', [
      "item_permohonan_uji_klinik" => $item_permohonan_uji_klinik,
      "item_pasien" => $item_pasien,
      "parameter_jenis_klinik" => $parameter_jenis_klinik,
      "parameter_paket_klinik" => $parameter_paket_klinik,
      "nama_petugas" => $nama_petugas,
      "umur_perwakilan_permohonan_uji_klinik" => $umurPerwakilan,
      "permohonanUjiPaketKlinik" => $permohonanUjiPaketKlinik
    ]);

    return $pdf->stream();
  }

  public function testBpjs(){


    $no_kartu= "0002234594081";

    $tgl_daftar= "2024-11-29";
    $kd_provider_peserta="1234U001";
    $kunj_sakit="1";
    $sistole="120";
    $diastole="80";
    $berat_badan="60";
    $tinggi_badan="170";
    $resp_rate="20";
    $heart_rate="80";
    $kd_diag1="D50";
    $kd_diag2="";
    $kd_diag3="";
    $kd_poli="UMU";
    $kd_sadar="01";
    $terapi="Suplemen zat besi dan diet kaya zat besi";
    $kd_status_pulang="1";
    $tgl_pulang="2024-11-29";

    // $baseUrl = env('BPJS_BASE_URL');
    // $consId = env('BPJS_CONS_ID');
    // $secretKey = env('BPJS_SECRET_KEY');
    // $timestamp = now()->format('YmdHis');
    // $kdAplikasi= env('BPJS_PCARE_APP_CODE');
    // $username= env('BPJS_PCARE_USERNAME');
    // $password= env('BPJS_PCARE_PASSWORD');

    // $signature = base64_encode(hash_hmac('sha256', $consId . "&" . $timestamp, $secretKey, true));


    // Generate signature
    // $signature = base64_encode(hash_hmac('sha256', $consId . "&" . $timestamp, $secretKey, true));

    // Data kunjungan
    $data = [
        "noKartu" => $no_kartu,
        "tglDaftar" => $tgl_daftar,
        "kdProviderPeserta" => $kd_provider_peserta,
        "kunjSakit" => $kunj_sakit,
        "sistole" => $sistole,
        "diastole" => $diastole,
        "beratBadan" => $berat_badan,
        "tinggiBadan" => $tinggi_badan,
        "respRate" => $resp_rate,
        "heartRate" => $heart_rate,
        "kdDiag1" => $kd_diag1,
        "kdDiag2" => $kd_diag2,
        "kdDiag3" => $kd_diag3,
        "kdPoli" => $kd_poli,
        "kdSadar" => $kd_sadar,
        "terapi" => $terapi,
        "kdStatusPulang" => $kd_status_pulang,
        "tglPulang" => $tgl_pulang,
    ];

    $pcare = new Peserta();
  $response = $pcare->getPesertaByNoka('0002234594081');


    // $request = Request::create('/', 'POST', $data);
    // $pcare = new Pendaftaran();
    // $response = $pcare->addPendaftaran($request);
    // dd($response);

    // dd($baseUrl . '/pcare-rest-v3.0/kunjungan');

  //   $client = new Client();

  //   try {


  //     // dd("{$username}:{$password}:{$kdAplikasi}");

  //     // dd('Basic ' . base64_encode("{$username}:{$password}:{$kdAplikasi}"));
  //     // Guzzle HTTP POST request with form data
  //     $response = $client->request('POST',$baseUrl . '/pcare-rest-v3.0/kunjungan', [
  //         'headers' => [
  //           'X-Cons-ID' => $consId,
  //           'X-Timestamp' => $timestamp,
  //           'X-Signature' => $signature,
  //            'Authorization' => 'Basic ' . base64_encode("{$username}:{$password}:{$kdAplikasi}"),
  //           'Content-Type' => 'application/json',
  //         ],
  //         'json' => $data,
  //         'timeout' => 7000
  //     ]);



  //     // Handle the response and parse file if needed
  //     $responseData = $response->getBody()->getContents();

  //     return [
  //         'status'      => $response->getStatusCode(),
  //         'file'        => $responseData,
  //         'id_dokumen'  => $response->getHeader('id_dokumen')[0] ?? null,
  //         'message'     => 'Success generating data'
  //     ];

  // } catch (RequestException $e) {
  //     return [
  //         'status'  => $e->getResponse() ? $e->getResponse()->getStatusCode() : 500,
  //         'message' => $e->getMessage()
  //     ];
  // }


    // Kirim request ke API PCare
    // $response = Http::withHeaders([
    //     'X-Cons-ID' => $consId,
    //     'X-Timestamp' => $timestamp,
    //     'X-Signature' => $signature,
    //     'Content-Type' => 'application/json',
    // ])->post($baseUrl . '/pcare-rest-v3.0/kunjungan', $data);

    // // Cek respons dari API
    // if ($response->successful()) {
    //     return response()->json([
    //         'message' => 'Kunjungan berhasil dicatat',
    //         'data' => $response->json()
    //     ]);
    // } else {
    //     return response()->json([
    //         'message' => 'Terjadi kesalahan',
    //         'error' => $response->json()
    //     ], $response->status());
    // }
    // dd();
    // $bpjs = new PCare\Diagnosa($this->pcare_conf());
    // dd($bpjs->keyword('001')->index(0, 2));

  }


  public function printPermohonanUjiKlinikKartuMedis($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.kartu-medis', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
    ]);
    return $pdf->stream();

    /* return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.kartu-medis', [
  'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik
  ]); */
  }

  public function printPermohonanUjiKlinikHasil(Request $request, $id_permohonan_uji_klinik)
  {

        $validated = $request->validate([
          'signoption' => 'nullable|integer|min:0|max:1',
        ]);

        $signoption = $validated['signoption'] ?? 0;

        $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

        $verificationActivity = VerificationActivitySample::query()->where('is_klinik', '=', $id_permohonan_uji_klinik)->where('id_verification_activity', '=', 3)->first();

        $nama_petugas_pemeriksa=$verificationActivity->nama_petugas??"...................";

        if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
          $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
        } else {
          $tgl_pengujian = '';
        }

        if ($item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik !== null) {
          $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
        } else {
          $tgl_spesimen_darah = '-';
        }

        if ($item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik !== null) {
          $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
        } else {
          $tgl_spesimen_urine = '-';
        }

        $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
              ->whereNull('deleted_at')
              ->orderBy('sort_parameter_satuan_klinik', 'asc');
          })
          ->whereNull('deleted_at')
          ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->get();

        $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('sort_parameter_satuan_klinik', 'asc')->get();

        $arr_permohonan_parameter = [];
        $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->orderBy('jenis_parameter_klinik_id', 'ASC')
          ->orderBy('sorting_parameter_satuan', 'ASC')
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->orderBy('sort_parameter_satuan_klinik', 'asc')
              ->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
              ->whereNull('deleted_at');
          })
          ->get();

        $data_jenis_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->groupBy('jenis_parameter_klinik_id')
          ->orderBy('sort_jenis_klinik', 'ASC')
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->orderBy('sort_parameter_satuan_klinik', 'asc')
              ->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
              ->whereNull('deleted_at');
          })
          ->get();

        if (count($data_permohonan_uji_satuan_klinik) > 0) {
          foreach ($data_jenis_parameter_klinik as $key_jenis_parameter_klinik => $value_jenis_parameter_klinik) {
            $parameter_jenis_klinik = [];
            $parameter_jenis_klinik['name_parameter_jenis_klinik'] = $value_jenis_parameter_klinik->jenisparameterklinik->name_parameter_jenis_klinik;
            $parameter_jenis_klinik['id_parameter_jenis_klinik'] = $value_jenis_parameter_klinik->jenisparameterklinik->id_parameter_jenis_klinik;
            $parameter_jenis_klinik['sort_parameter_jenis_klinik'] = $value_jenis_parameter_klinik->jenisparameterklinik->sort_parameter_jenis_klinik;
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
                $item_permohonan_parameter_satuan['method_permohonan_uji_parameter_klinik'] = $value_satuan->method_permohonan_uji_parameter_klinik;
                $item_permohonan_parameter_satuan['parameter_satuan_klinik'] = $value_satuan->parameter_satuan_klinik;
                $item_permohonan_parameter_satuan['nama_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->name_parameter_satuan_klinik ?? null;
                $item_permohonan_parameter_satuan['sort_parameter_satuan_klinik'] = $value_satuan->parametersatuanklinik->sort_parameter_satuan_klinik ?? null;
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

            //ururutkan berdasarkan sorting parameter satuan terkecil
            usort($parameter_jenis_klinik['item_permohonan_parameter_satuan'], function($a, $b) {
              return (int)$a['sort_parameter_satuan_klinik'] <=> (int)$b['sort_parameter_satuan_klinik'];
            });

            array_push($arr_permohonan_parameter, $parameter_jenis_klinik);
            // // Debug hasil array setelah diurutkan
          }

          $collection = collect($arr_permohonan_parameter);

          // Mengurutkan berdasarkan sort_parameter_jenis_klinik secara ascending
          $arr_permohonan_parameter = $collection->sortBy('sort_parameter_jenis_klinik')->values()->all();

        }

          // $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

          // if (!isset($no_LHU)) {
          //   $no_LHU = new LHU;
          //   //uuid
          //   $uuid4 = Uuid::uuid4();

          //   $no_LHU_urutan = LHU::max('nomer_urut_LHU');
          //   $no_LHU->id_lhu = $uuid4->toString();
          //   $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
          //   $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

          //   // permintaan pertama
          //   // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

          //   // permintaan kedua
          //   $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

          //   $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
          //   $no_LHU->save();

          //   $no_LHU = $no_LHU->nomer_LHU;
          // } else {
          //   $get_nomor_lhu = $no_LHU->nomer_LHU;
          //   $explode_nomor_lhu = explode("/", $get_nomor_lhu);
          //   $get_pisah0_lhu = $explode_nomor_lhu[0];
          //   $get_pisah2_lhu = $explode_nomor_lhu[2];
          //   $get_pisah3_lhu = $explode_nomor_lhu[3];

          //   $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
          // }

          $url = env("APP_URL");

          if (env("APP_ENV") == "local"){
            $url .= ":8000";
          }

          $url .= "/elits-signature/progress/".$id_permohonan_uji_klinik."/1";

          $result = Builder::create()
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(600)
            // ->logoPath( public_path('assets/admin/images/logo/silaboy_mini_bg_white.png'))
            // ->logoResizeToWidth(200)
            // ->logoPunchoutBackground(true)
            ->margin(2)

            ->build();
          $qrBase64 = base64_encode($result->getString());


          if (!$signoption){
            $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-klinik', [
              'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
              'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
              'tgl_pengujian' => $tgl_pengujian,
              'tgl_spesimen_darah' => $tgl_spesimen_darah,
              'tgl_spesimen_urine' => $tgl_spesimen_urine,
              'data_parameter_satuan' => $data_parameter_satuan,
              'arr_permohonan_parameter' => $arr_permohonan_parameter,
              'nama_petugas_pemeriksa'=>$nama_petugas_pemeriksa,
              'no_LHU' => $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik,
              'signOption' => 0
            ])->setPaper('a4', 'potrait');

            return $pdf->stream();
          }

          $verificationActivitySamples = VerificationActivitySample::where('is_klinik', $id_permohonan_uji_klinik)->get();
          if (count($verificationActivitySamples) < 6){
            return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
          }


          $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-klinik', [
            'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
            'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
            'tgl_pengujian' => $tgl_pengujian,
            'tgl_spesimen_darah' => $tgl_spesimen_darah,
            'tgl_spesimen_urine' => $tgl_spesimen_urine,
            'data_parameter_satuan' => $data_parameter_satuan,
            'arr_permohonan_parameter' => $arr_permohonan_parameter,
            'nama_petugas_pemeriksa'=>$nama_petugas_pemeriksa,
            'no_LHU' => $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik,
            'qrBase64' => $qrBase64
          ]);

          $datas = [
            [
              'nik' => 3309094611720002,
              'passPhrase' => "@Muharyati123",
              'tampilan' => 'invisible',
              'reason' => "hasil12121",
              'location' => "boyolali",
              'text' => "hasil22323"
            ],
            [
              'nik' => 3309054611779004,
              'passPhrase' => "St290899*",
              'tampilan' => 'invisible',
              'reason' => "hasil12121",
              'location' => "boyolali",
              'text' => "hasil22323"
            ]
          ];

          $signBSRE = Smt::signBSRE($pdf, $datas);

          if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
            $data =  base64_encode($signBSRE["data"]["file"]);
            return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.blob',
              compact('data')
            );
          }elseif ($signBSRE['status'] == 500){
            return redirect()->back()->with('error-laporan', 'errors');
          }else{
            return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Silahkan coba lagi!');
          }
  }

  public function printPermohonanUjiKlinikHasilRapidAntibody($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

    if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    // non sedimen
    $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '2')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

      // permintaan pertama
      // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      // permintaan kedua
      $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
      $no_LHU->save();

      $no_LHU = $no_LHU->nomer_LHU;
    } else {
      $get_nomor_lhu = $no_LHU->nomer_LHU;
      $explode_nomor_lhu = explode("/", $get_nomor_lhu);
      $get_pisah0_lhu = $explode_nomor_lhu[0];
      $get_pisah2_lhu = $explode_nomor_lhu[2];
      $get_pisah3_lhu = $explode_nomor_lhu[3];

      $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
    }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    if (count($data_permohonan_uji_paket_klinik) > 0) {
      foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
        $item_permohonan_parameter_paket = [];
        $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
        $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
        $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
        $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
        $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

        // get data perameter satuan yang ada dalam paket
        $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '2')
              ->whereNull('deleted_at')
              ->orderBy('name_parameter_satuan_klinik', 'asc');
          })
          ->get();

        $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

        if (count($data_permohonan_uji_satuan_klinik) > 0) {
          foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
            // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
            // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
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

            $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
              ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
              ->get();

            if (count($data_parameter_by_baku_mutu) > 0) {
              $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                ->where('is_khusus_baku_mutu', '0')
                ->first();

              // cek dulu apakah ada data dengan data khusus sperti general atau specific
              if ($check_parameter_by_baku_mutu) {
                $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
              } else {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                  ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '1')
                  ->where('gender_baku_mutu', $pasien_gender)
                  ->where(function ($query) use ($pasien_umur) {
                    $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                      ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                  })
                  ->first();
                if (!isset($item_parameter_by_baku_mutu)) {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
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

                array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
              }
            }

            array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
          }
        }

        array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
      }
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-rapid-antibody', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
    return $pdf->stream($no_LHU);

    /* return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.hasil-rapid-antibody', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter
    ]); */
  }

  public function printPermohonanUjiKlinikHasilRapidAntigen($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

    if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    // non sedimen
    $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '3')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

      // permintaan pertama
      // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      // permintaan kedua
      $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
      $no_LHU->save();

      $no_LHU = $no_LHU->nomer_LHU;
    } else {
      $get_nomor_lhu = $no_LHU->nomer_LHU;
      $explode_nomor_lhu = explode("/", $get_nomor_lhu);
      $get_pisah0_lhu = $explode_nomor_lhu[0];
      $get_pisah2_lhu = $explode_nomor_lhu[2];
      $get_pisah3_lhu = $explode_nomor_lhu[3];

      $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
    }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    if (count($data_permohonan_uji_paket_klinik) > 0) {
      foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
        $item_permohonan_parameter_paket = [];
        $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
        $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
        $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
        $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
        $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

        // get data perameter satuan yang ada dalam paket
        $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '3')
              ->whereNull('deleted_at')
              ->orderBy('name_parameter_satuan_klinik', 'asc');
          })
          ->get();

        $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

        if (count($data_permohonan_uji_satuan_klinik) > 0) {
          foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
            // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
            // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
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

            $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
              ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
              ->get();

            if (count($data_parameter_by_baku_mutu) > 0) {
              $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                ->where('is_khusus_baku_mutu', '0')
                ->first();

              // cek dulu apakah ada data dengan data khusus sperti general atau specific
              if ($check_parameter_by_baku_mutu) {
                $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
              } else {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                  ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '1')
                  ->where('gender_baku_mutu', $pasien_gender)
                  ->where(function ($query) use ($pasien_umur) {
                    $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                      ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                  })
                  ->first();
                if (!isset($item_parameter_by_baku_mutu)) {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
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

                array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
              }
            }

            array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
          }
        }

        array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
      }
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-rapid-antigen', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
    return $pdf->stream($no_LHU);

    /* return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.hasil-rapid-antigen', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter
    ]); */
  }

  public function printPermohonanUjiKlinikHasilPcr($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

    if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    // non sedimen
    $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '1')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

      // permintaan pertama
      // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      // permintaan kedua
      $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
      $no_LHU->save();

      $no_LHU = $no_LHU->nomer_LHU;
    } else {
      $get_nomor_lhu = $no_LHU->nomer_LHU;
      $explode_nomor_lhu = explode("/", $get_nomor_lhu);
      $get_pisah0_lhu = $explode_nomor_lhu[0];
      $get_pisah2_lhu = $explode_nomor_lhu[2];
      $get_pisah3_lhu = $explode_nomor_lhu[3];

      $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
    }

    $data_permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereNull('deleted_at')
      ->get();

    // array untuk mapping data parameter satuan ke baku mutu
    $arr_permohonan_parameter = [];

    if (count($data_permohonan_uji_paket_klinik) > 0) {
      foreach ($data_permohonan_uji_paket_klinik as $key_paket => $value_paket) {
        $item_permohonan_parameter_paket = [];
        $item_permohonan_parameter_paket['id_permohonan_uji_paket_klinik'] = $value_paket->id_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['permohonan_uji_klinik'] = $value_paket->permohonan_uji_klinik;
        $item_permohonan_parameter_paket['parameter_jenis_klinik'] = $value_paket->parameter_jenis_klinik;
        $item_permohonan_parameter_paket['type_permohonan_uji_paket_klinik'] = $value_paket->type_permohonan_uji_paket_klinik;
        $item_permohonan_parameter_paket['parameter_paket_klinik'] = $value_paket->parameter_paket_klinik;
        $item_permohonan_parameter_paket['nama_parameter_paket_klinik'] = $value_paket->parameterpaketklinik->name_parameter_paket_klinik ?? null;
        $item_permohonan_parameter_paket['harga_permohonan_uji_paket_klinik'] = $value_paket->harga_permohonan_uji_paket_klinik;

        // get data perameter satuan yang ada dalam paket
        $data_permohonan_uji_satuan_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
          ->where('permohonan_uji_paket_klinik', $value_paket->id_permohonan_uji_paket_klinik)
          ->whereHas('parametersatuanklinik', function ($query) {
            return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '1')
              ->whereNull('deleted_at')
              ->orderBy('name_parameter_satuan_klinik', 'asc');
          })
          ->get();

        $item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'] = [];

        if (count($data_permohonan_uji_satuan_klinik) > 0) {
          foreach ($data_permohonan_uji_satuan_klinik as $key_satuan => $value_satuan) {
            // get data baku mutu untuk menyiapkan data nilai rujukan jika ada dimasukkan jika tidak maka akan dilewati
            // melewati data kondisi pasien berupa umur dan gender dahulu ya kawaaaaan
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

            $data_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
              ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
              ->get();

            if (count($data_parameter_by_baku_mutu) > 0) {
              $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                ->where('is_khusus_baku_mutu', '0')
                ->first();

              // cek dulu apakah ada data dengan data khusus sperti general atau specific
              if ($check_parameter_by_baku_mutu) {
                $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
              } else {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
                  ->where('parameter_satuan_klinik_id', $value_satuan->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '1')
                  ->where('gender_baku_mutu', $pasien_gender)
                  ->where(function ($query) use ($pasien_umur) {
                    $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                      ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                  })
                  ->first();
                if (!isset($item_parameter_by_baku_mutu)) {
                  $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_paket->parameter_jenis_klinik)
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

                array_push($item_permohonan_parameter_satuan['data_permohonan_uji_subsatuan_klinik'], $item_permohonan_parameter_subsatuan);
              }
            }

            array_push($item_permohonan_parameter_paket['data_permohonan_uji_satuan_klinik'], $item_permohonan_parameter_satuan);
          }
        }

        array_push($arr_permohonan_parameter, $item_permohonan_parameter_paket);
      }
    }

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-pcr', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter,
    ]);
    return $pdf->stream($no_LHU);

    /*  return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.hasil-pcr', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_pengujian' => $tgl_pengujian,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'arr_permohonan_parameter' => $arr_permohonan_parameter
    ]); */
  }

  public function printPermohonanUjiKlinikQrcode($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

    if ($item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik !== null) {
      $tgl_pengujian = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_pengujian = '';
    }

    if ($item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik !== null) {
      $tgl_spesimen_darah = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_spesimen_darah = '';
    }

    if ($item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik !== null) {
      $tgl_spesimen_urine = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik)->isoFormat('D MMMM Y HH:mm');
    } else {
      $tgl_spesimen_urine = '';
    }

    $no_LHU = LHU::where('permohonan_uji_klinik_id', '=', $id_permohonan_uji_klinik)->first();

    if (!isset($no_LHU)) {
      $no_LHU = new LHU;
      //uuid
      $uuid4 = Uuid::uuid4();

      $no_LHU_urutan = LHU::max('nomer_urut_LHU');
      $no_LHU->id_lhu = $uuid4->toString();
      $no_LHU->nomer_urut_LHU = $no_LHU_urutan + 1;
      $romawi_bulan = $this->convertToRoman(Carbon::now()->format('m'));

      // permintaan pertama
      // $no_LHU->nomer_LHU = '449.5/A.' . $no_LHU->nomer_urut_LHU . '.5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      // permintaan kedua
      $no_LHU->nomer_LHU = '449.5/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $romawi_bulan . '/' . Carbon::now()->format('Y');

      $no_LHU->permohonan_uji_klinik_id = $id_permohonan_uji_klinik;
      $no_LHU->save();

      $no_LHU = $no_LHU->nomer_LHU;
    } else {
      $get_nomor_lhu = $no_LHU->nomer_LHU;
      $explode_nomor_lhu = explode("/", $get_nomor_lhu);
      $get_pisah0_lhu = $explode_nomor_lhu[0];
      $get_pisah2_lhu = $explode_nomor_lhu[2];
      $get_pisah3_lhu = $explode_nomor_lhu[3];

      $no_LHU = $get_pisah0_lhu . '/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5.22/' . $get_pisah2_lhu . '/' . $get_pisah3_lhu;
    }

    $data_permohonan_uji_parameter_klinik = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '0')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_permohonan_uji_parameter_klinik_pcr = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '1')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_permohonan_uji_parameter_klinik_antigen = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '3')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_permohonan_uji_parameter_klinik_antibody = PermohonanUjiParameterKlinik::where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->whereHas('parametersatuanklinik', function ($query) {
        return $query->where('jenis_pemeriksaan_parameter_satuan_klinik', '2')
          ->whereNull('deleted_at')
          ->orderBy('name_parameter_satuan_klinik', 'asc');
      })
      ->whereNull('deleted_at')
      ->where('permohonan_uji_klinik', $id_permohonan_uji_klinik)
      ->get();

    $data_parameter_satuan = ParameterSatuanKlinik::whereNull('deleted_at')->orderBy('name_parameter_satuan_klinik', 'asc')->get();

    $text_redirect_barcode = route('scan.permohonan-uji-klinik', $item_permohonan_uji_klinik->id_permohonan_uji_klinik);

    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.hasil-qrcode', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
      'data_permohonan_uji_parameter_klinik_pcr' => $data_permohonan_uji_parameter_klinik_pcr,
      'data_permohonan_uji_parameter_klinik_antigen' => $data_permohonan_uji_parameter_klinik_antigen,
      'data_permohonan_uji_parameter_klinik_antibody' => $data_permohonan_uji_parameter_klinik_antibody,
      'tgl_pengujian' => $tgl_pengujian,
      'tgl_spesimen_darah' => $tgl_spesimen_darah,
      'tgl_spesimen_urine' => $tgl_spesimen_urine,
      'data_parameter_satuan' => $data_parameter_satuan,
      'no_LHU' => $no_LHU,
      'text_redirect_barcode' => $text_redirect_barcode,
    ]);
    return $pdf->stream();

    /* return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik.formatPrint.hasil-qrcode', [
  'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
  'data_permohonan_uji_parameter_klinik' => $data_permohonan_uji_parameter_klinik,
  'data_permohonan_uji_parameter_klinik_pcr' => $data_permohonan_uji_parameter_klinik_pcr,
  'data_permohonan_uji_parameter_klinik_antigen' => $data_permohonan_uji_parameter_klinik_antigen,
  'data_permohonan_uji_parameter_klinik_antibody' => $data_permohonan_uji_parameter_klinik_antibody,
  'tgl_pengujian' => $tgl_pengujian,
  'tgl_spesimen_darah' => $tgl_spesimen_darah,
  'tgl_spesimen_urine' => $tgl_spesimen_urine,
  'data_parameter_satuan' => $data_parameter_satuan,
  'no_LHU' => $no_LHU,
  'text_redirect_barcode' => $text_redirect_barcode
  ]); */
  }

  public function label(Request $request)
  {
    // dd();
    if (request()->ajax()) {
      // $datas = PermohonanUjiPaketKlinik::leftJoin('tb_permohonan_uji_klinik_2', function ($join) {
      //   $join->on('tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik', '=', 'tb_permohonan_uji_paket_klinik.permohonan_uji_klinik')
      //     ->whereNull('tb_permohonan_uji_klinik_2.deleted_at')
      //     ->whereNull('tb_permohonan_uji_paket_klinik.deleted_at');
      // })
      // ->whereNotNull('tb_permohonan_uji_klinik_2.id_permohonan_uji_klinik')
      // ->get();

      $datas = PermohonanUjiKlinik2::orderBy('nourut_permohonan_uji_klinik', 'desc')->whereNull('deleted_at')->get();

      // dd($datas);
      return Datatables::of($datas)
        ->filter(function ($instance) use ($request) {
          if (!empty($request->get('search'))) {
            $instance->collection = $instance->collection->filter(function ($row) use ($request) {
              if (Str::contains(Str::lower($row['nama_pasien']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['no_rekammedis_pasien']), Str::lower($request->get('search')))) {
                return true;
              } else if (Str::contains(Str::lower($row['tgllahir_pasien']), Str::lower($request->get('search')))) {
                return true;
              }
              return false;
            });
          }
        })
        ->addColumn('set_checkbox', function ($data) {
          $set_checkbox = '<input type="checkbox" class="form-control checkbox-label" name="checkbox_label[' . $data->id_permohonan_uji_klinik . ']" value="' . $data->id_permohonan_uji_klinik . '">';

          return $set_checkbox;
        })
        ->addColumn('nama_pasien', function ($data) {
          $nama_pasien = '<div class="text-center">' . $data->pasien->nama_pasien . '</div>';
          // dd($nama_pasien);
          return $nama_pasien;
        })
        ->addColumn('no_rekammedis_pasien', function ($data) {
          $no_rekammedis_pasien = '<div class="text-center">' . $data->pasien->no_rekammedis_pasien . '</div>';
          // dd($no_rekammedis_pasien);
          return $no_rekammedis_pasien;
        })
        ->addColumn('tgllahir_pasien', function ($data) {

          $tgllahir_pasien = Carbon::createFromFormat("Y-m-d", $data->pasien->tgllahir_pasien)->isoFormat("D MMMM YYYY");
          $tgllahir_pasien = '<div class="text-center">' . $tgllahir_pasien . '</div>';
          // dd($tgllahir_pasien);
          return $tgllahir_pasien;
        })
        // ->addColumn('set_jenis_pemeriksaan', function ($data) {
        //   if (isset($data->type_permohonan_uji_paket_klinik)) {
        //     # code...
        //     if ($data->type_permohonan_uji_paket_klinik == 'P') {
        //       $set_jenis_pemeriksaan = $data->parameterpaketklinik->name_parameter_paket_klinik ?? $data->parameterpaketklinik2->name_parameter_paket_klinik;
        //     } else if ($data->type_permohonan_uji_paket_klinik == 'C') {
        //       $set_jenis_pemeriksaan = $data->parameterjenisklinik->name_parameter_jenis_klinik ?? $data->parameterjenisklinik2->name_parameter_jenis_klinik;
        //     } else {
        //       $set_jenis_pemeriksaan = "-";
        //     }
        //   } else {
        //     $set_jenis_pemeriksaan = "-";
        //   }
        //   // dd($set_jenis_pemeriksaan);
        //   return $set_jenis_pemeriksaan;
        // })
        ->addColumn('tgl_pemeriksaan', function ($data) {

          $tgl_pemeriksaan = Carbon::createFromFormat("Y-m-d H:i:s", $data->created_at)->format("d-m-Y, H:i:s");
          $tgl_pemeriksaan = '<div class="text-center">' . $tgl_pemeriksaan . '</div>';
          // dd($tgl_pemeriksaan );
          return $tgl_pemeriksaan;
        })
        ->rawColumns(['set_checkbox', 'nama_pasien', 'no_rekammedis_pasien', 'tgllahir_pasien', 'tgl_pemeriksaan'])
        ->addIndexColumn() //increment
        ->make(true);
    }

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.label.index');
  }

  public function printLabel(Request $request)
  {
    // dd($request->all());
    $id_permohonan_uji_klinik_string = $request->permohonan_uji_klinik;
    $id_permohonan_uji_klinik_arr = explode(",", $id_permohonan_uji_klinik_string);


    $i = 0;
    foreach ($id_permohonan_uji_klinik_arr as $id_permohonan_uji_klinik) {
      $get_data[$i] = PermohonanUjiKlinik2::where('id_permohonan_uji_klinik', $id_permohonan_uji_klinik)->first();
      $i++;
    }

    // dd($get_data);
    if (count($get_data) > 0) {
      for ($i = 0; $i < count($get_data); $i++) {
        $get_data[$i]->pasien->tgllahir_pasien = Carbon::createFromFormat("Y-m-d", $get_data[$i]->pasien->tgllahir_pasien)->format("d-m-Y");
        $full_name = explode(" ", $get_data[$i]->pasien->nama_pasien);
        if (implode(" ", array_splice($full_name, 3)) != null) {
          $get_data[$i]->pasien->nama_pasien = implode(" ", array_splice($full_name, 0, 3)) . ' ...';
        } else {
          $get_data[$i]->pasien->nama_pasien = implode(" ", array_splice($full_name, 0, 3));
        }
        $full_address = explode(" ", $get_data[$i]->pasien->alamat_pasien);
        if (implode(" ", array_splice($full_address, 6)) != null) {
          $get_data[$i]->pasien->alamat_pasien = implode(" ", array_splice($full_address, 0, 6)) . ' ...';
        } else {
          $get_data[$i]->pasien->alamat_pasien = implode(" ", array_splice($full_address, 0, 6));
        }
        // $get_data[$i]->pasien->alamat_pasien;
        // if ($get_data[$i]->type_permohonan_uji_paket_klinik == 'P') {

        //   $get_data[$i]->jenis_pemeriksaan = $get_data[$i]->parameterpaketklinik->name_parameter_paket_klinik;
        // } else if ($get_data[$i]->type_permohonan_uji_paket_klinik == 'C') {
        //   $get_data[$i]->jenis_pemeriksaan = $get_data[$i]->parameterjenisklinik->name_parameter_jenis_klinik;
        // }
      }
    }

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.label.print-label', compact('get_data'));

    // $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.label.print-label', compact('get_data'));
    // return $pdf->stream();
  }




  //test coba
  public function storeParameter(Request $request)
  {
    // Inisialisasi total harga
    $total_harga = 0;

    // Mulai DB transaction
    DB::beginTransaction();

    // try {

      $paketExtras = $request->input('paket_extra');
      // dd($paketExtras);

    //  foreach ($paketExtras as $id => $value) {
    //      // Pisahkan value yang digabungkan dengan underscore
    //      [$id_parameter_paket_extra, $harga_parameter_paket_extra] = explode('_', $value);


    //      // Simpan data paket ekstra
    //      $paketExtra = new PermohonanUjiPaketExtraKlinik();  // Pastikan mengganti `PaketExtra` dengan model yang sesuai

    //      $paketExtra->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
    //      $paketExtra->id_parameter_paket_extra = $id_parameter_paket_extra;
    //      $paketExtra->harga_permohonan_uji_paket_extra = $harga_parameter_paket_extra;
    //      $paketExtra->save();

    //      // Simpan sub paketnya jika ada

    //     $dataSubPaketExtra = ParameterSubPaketExtra::where('id_parameter_paket_extra', $id_parameter_paket_extra)->get();
    //     foreach ($dataSubPaketExtra as $subPaket) {
    //         $subPaketExtra = new PermohonanUjiSubPaketExtraKlinik();
    //         $subPaketExtra->id_permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
    //         $subPaketExtra->id_permohonan_paket_extra = $paketExtra->id_permohonan_uji_paket_extra_klinik;
    //         // $subPaketExtra->id_parameter_sub_paket_klinik = $subPaket->id_parameter_sub_paket_klinik;
    //         $subPaketExtra->id_parameter_paket_klinik = $subPaket->id_parameter_paket_klinik;
    //         $subPaketExtra->save();

    //         // dd($subPaketExtra);
    //     }
    //  }

    $total_harga_paket_extra=0;


    $no_ajaib = array();


    $data_jenis_parameter = $request->input('jenis_parameters');


    if(!empty($paketExtras)){
      foreach ($paketExtras as $id => $value) {

          // Pisahkan value yang digabungkan dengan underscore
          [$id_parameter_paket_extra, $harga_parameter_paket_extra] = explode('_', $value);
          //total harga paket extra
          $total_harga_paket_extra += $harga_parameter_paket_extra;

          $dataSubPaketExtra = ParameterSubPaketExtra::where('id_parameter_paket_extra', $id_parameter_paket_extra)->get();
          // dd($dataSubPaketExtra);
          foreach ($dataSubPaketExtra as $subPaket) {
              $paketExtra = new PermohonanUjiPaketKlinik();
              $paketExtra->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
              $paketExtra->parameter_paket_extra = $id_parameter_paket_extra;
              $paketExtra->type_permohonan_uji_paket_klinik = "P";
              $paketExtra->harga_permohonan_uji_paket_klinik = $subPaket->parameterPaketKlinik->harga_parameter_paket_klinik;
              $paketExtra->parameter_paket_klinik = $subPaket->id_parameter_paket_klinik;
              $paketExtra->save();

              // Tambahkan packet_id ke array $paket_extra
              $packet_id = $subPaket->id_parameter_paket_klinik;
              array_push($no_ajaib, $packet_id);
          }
      }

    }

      // dd($no_ajaib);
      // dd($request->all());
      // Validasi input




      // store ke permohonan uji paket klinik

      $price_total=0;
      $name_permohonan="";
      if (isset($request->jenis_parameters)) {
        # code...
        foreach ($request->jenis_parameters as $key => $value) {


          // dd($value["pakets"][0]);
          // $packectParameter = $value["pakets"][0];

          foreach ($value["pakets"] as $key => $packectParameter) {
            $post_paket = new PermohonanUjiPaketKlinik();
            $post_paket->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
            $post_paket->parameter_jenis_klinik = $key;
            $post_paket->type_permohonan_uji_paket_klinik = "P";
            # code...
            $array_packet = explode("_", $packectParameter);
            $packet_id = $array_packet[0];
            $price = $array_packet[1];
            //

            $post_paket->parameter_paket_klinik =  $packet_id;
            $post_paket->harga_permohonan_uji_paket_klinik = $price;

            $simpan_post_paket = $post_paket->save();

            $parameterpaketklinik = ParameterPaketKlinik::where('id_parameter_paket_klinik', $packet_id)
              ->whereNull('deleted_at')
              ->first();
            $name_permohonan=$name_permohonan.$parameterpaketklinik->name_parameter_paket_klinik." ";
            array_push($no_ajaib, $packet_id);


            $price_total=$price_total+(int)$price;
          }
        }
      }



      $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($request->permohonan_uji_klinik);
      $item_permohonan_uji_klinik->total_harga_permohonan_uji_klinik= $price_total+$total_harga_paket_extra;


      // dd(Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->toIso8601String());
      $encounterId = null;
      $encounterDisplay = "Permohonanan Pengujian ".$name_permohonan;
      $practitionerId = null;
      $practitionerName = null;
      if (isset($item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat)) {
        # code...
        if ( env('VERSION_SATUSEHAT')=="prd") {
          # code...

          // $satusehat = SatuSehat::first();
          $location_satusehat= SatuSehatLocation::where('name_satusehat_location',"LIKE",'%Administrasi%')->where('version_satusehat_location','prd')->first();
          $practitioner_satusehat= SatuSehatPractitioner::where('type','adm')->first();
          $practitionerId = $practitioner_satusehat->code_satu_sehat_practitioner;
          $practitionerName = $practitioner_satusehat->name_satu_sehat_practitioner;

          // dd($item_permohonan_uji_klinik);

          // dd($satusehat);
          $data = [
              "resourceType" => "Encounter",
              "status" => "arrived", // Status Encounter (mulai, selesai, atau dibatalkan)
              "subject" => [
                  "reference" => "Patient/".$item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat, // Tambahkan ID Pasien yang sesuai
                  "display" => $item_permohonan_uji_klinik->pasien->nama_pasien
              ],
              "class" => [
                  "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
                  "code" => "PRENC",
                  "display" => "Permohonanan Pengujian ".$name_permohonan
              ],
              "participant" => [
                  [
                      "type" => [
                          [
                              "coding" => [
                                  [
                                      "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                                      "code" => "ADM", // Tipe partisipasi: pendaftaran, pengambilan sample, pemeriksaan, verifikasi
                                      "display" => "pendaftaran"
                                  ]
                              ]
                          ]
                      ],
                      "individual" => [
                          "reference" => "Practitioner/".$practitioner_satusehat->code_satu_sehat_practitioner, // Tambahkan UUID Praktisi yang sesuai
                          "display" => $practitioner_satusehat->name_satu_sehat_practitioner
                      ]
                  ]
              ],
              "period" => [
                  "start" => Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->toIso8601String()
              ],
              "location" => [
                  [
                      "location" => [
                          "reference" => "Location/".$location_satusehat->kode_satusehat_location, // Lokasi yang sesuai
                          "display" => "Ruang Administrasi Laboratorium Kesehatan Boyolali"
                      ]
                  ]
              ],
              "statusHistory" => [
                  [
                      "status" => "arrived",
                      "period" => [
                          "start" => Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->toIso8601String(),
                          "end" => Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->toIso8601String()
                      ]
                  ]
              ],
              "serviceProvider" => [
                  "reference" => "Organization/".env('ORG_ID') // Tambahkan ID Organisasi yang sesuai
              ],
              "identifier" => [
                  [
                      "system" => "http://sys-ids.kemkes.go.id/encounter/".env('ORG_ID'), // Sistem Identifier
                      "value" =>  $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik
                  ]
              ]
          ];
          // dd($data);

          $response = $this->satuSehatHelper->post('Encounter',$data);
          // dd(json_encode($response));
          // if (!isset($item_permohonan_uji_klinik->id_satu_sehat_encounter )) {
          //   # code...

          // // }else{
          // dd($response);
          if ($response['status_code'] == '201') {
            # code...
            $encounterId = $response['body']["id"];
            $item_permohonan_uji_klinik->id_satu_sehat_encounter=$response['body']["id"];
            $item_permohonan_uji_klinik->encounter_json_satu_sehat=json_encode($response['body']);
            $item_permohonan_uji_klinik->save();
            $satu_sehat=true;
          }else{
            $satu_sehat=null;
          }
          // }

          // dd($response);

        }
      }
      $item_permohonan_uji_klinik->save();

      foreach ($no_ajaib as $key => $value) {
        $parameterpaketklinik = ParameterPaketKlinik::where('id_parameter_paket_klinik', $value)
          ->whereNull('deleted_at')
          ->first();



      // if (isset($item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat)) {


      // else{
      //     $satusehat= SatuSehat::first();

      //     $location_satusehat= SatuSehatLocation::where('name_satusehat_location','Administrasi')->where('version_satusehat_location','sttg')->first();


      //     $data = [
      //           "resourceType" => "Encounter",
      //           "status" => "arrived", // Status Encounter (mulai, selesai, atau dibatalkan)
      //           "subject" => [
      //               "reference" => "Patient/".$item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat, // Tambahkan ID Pasien yang sesuai
      //               "display" => $item_permohonan_uji_klinik->pasien->nama_pasien
      //           ],
      //           "class" => [
      //               "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
      //               "code" => "PRENC",
      //               "display" => "Permohonanan Pengujian ".$name_permohonan
      //           ],
      //           "participant" => [
      //               [
      //                   "type" => [
      //                       [
      //                           "coding" => [
      //                               [
      //                                   "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
      //                                   "code" => "ADM", // Tipe partisipasi: pendaftaran, pengambilan sample, pemeriksaan, verifikasi
      //                                   "display" => "pendaftaran"
      //                               ]
      //                           ]
      //                       ]
      //                   ],
      //                   "individual" => [
      //                       "reference" => "Practitioner/".$satusehat->Practition_uuid, // Tambahkan UUID Praktisi yang sesuai
      //                       "display" => "Administrator"
      //                   ]
      //               ]
      //           ],
      //           "period" => [
      //               "start" => "2024-08-20T12:00:00+07:00"
      //           ],
      //           "location" => [
      //               [
      //                   "location" => [
      //                       "reference" => "Location/".$location_satusehat->kode_satusehat_location, // Lokasi yang sesuai
      //                       "display" => "Ruang Administrasi Laboratorium Kesehatan Boyolali"
      //                   ]
      //               ]
      //           ],
      //           "statusHistory" => [
      //               [
      //                   "status" => "arrived",
      //                   "period" => [
      //                       "start" => "2022-06-15T07:00:00+07:00"
      //                   ]
      //               ]
      //           ],
      //           "serviceProvider" => [
      //               "reference" => "Organization/".$satusehat->Org_lab // Tambahkan ID Organisasi yang sesuai
      //           ],
      //           "identifier" => [
      //               [
      //                   "system" => "http://sys-ids.kemkes.go.id/encounter/".$satusehat->Org_lab, // Sistem Identifier
      //                   "value" =>  $item_permohonan_uji_klinik->code_permohonan_uji
      //               ]
      //           ]
      //       ];

      //       if (isset($satusehat)) {

      //         $client = new Client([
      //           'base_uri' => env('URL_SATUSEHAT'),// Set base URI dari environment variable
      //         ]);

      //         # code...
      //         try {

      //             // Melakukan POST request dengan JSON body
      //             $response = $client->post( env('BASE_URL_SATUSEHAT').'/Encounter', [
      //                 'headers' => [
      //                     'Authorization' => 'Bearer ' . $satusehat->token, // Gunakan token jika dibutuhkan
      //                     'Content-Type' => 'application/json',
      //                     'Accept' => 'application/json',
      //                 ],
      //                 'json' => $data, // Mengirim data dalam format JSON
      //             ]);

      //             // Mendapatkan body response
      //             $responseBody = json_decode($response->getBody(), true);
      //             dd(  $responseBody);

      //             // $post_newpasien->id_pasien_satu_sehat=$responseBody["data"]["patient_id"];


      //             // $store_pasien = $post_newpasien->save();
      //             // $post->pasien_permohonan_uji_klinik = $post_newpasien->id_pasien;
      //         } catch (RequestException $e) {
      //             // Menangani error
      //             // dd($e->getResponse()->getBody()->getContents());


      //             if ($e->hasResponse()) {
      //                 return response()->json(['status' => false, 'pesan' => $e->getResponse()->getBody()->getContents(), 'error' => $e->getMessage()], 200);
      //             } else {
      //                 return response()->json(['status' => false, 'pesan' => $e->getMessage(), 'error' => $e->getMessage()], 200);
      //             }

      //             // $store_pasien = $post_newpasien->save();

      //         }
      //       }

      //   }
      // }


        $detailPacket = [];
        $packetName = $parameterpaketklinik->name_parameter_paket_klinik;
        foreach ($parameterpaketklinik->parameterpaketjenisklinik as $key_parameterpaketjenisklinik => $value_parameterpaketjenisklinik) {
          # code...
          #looping satuan parameter dari paket
          foreach ($value_parameterpaketjenisklinik->parametersatuanpaketklinik as $key_parametersatuanpaketklinik => $value_parametersatuanpaketklinik) {

            if (!isset($value_parametersatuanpaketklinik->parametersatuanklinik->loinc_parameter_satuan_klinik) or $value_parametersatuanpaketklinik->parametersatuanklinik->loinc_parameter_satuan_klinik == ""){
              $code = "31100-1";
            }else{
              $code = $value_parametersatuanpaketklinik->parametersatuanklinik->loinc_parameter_satuan_klinik;
            }
            $detailPacket[] = [
              "system" => "http://loinc.org",
              "code" => $code,
              "display" =>
                $value_parametersatuanpaketklinik->parametersatuanklinik->name_parameter_satuan_klinik,
            ];




            // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
            $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
            $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

            $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
              ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
              ->where('is_khusus_baku_mutu', '0')
              ->leftJoin('ms_library', function ($join) {
                $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                  ->whereNull('ms_library.deleted_at')
                  ->whereNull('tb_baku_mutu.deleted_at');
              })
              ->first();
            // cek dulu apakah ada data dengan data khusus sperti general atau specific
            if ($check_parameter_by_baku_mutu) {
              $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
            } else {
              $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
                ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                ->where('is_khusus_baku_mutu', '1')
                ->where('gender_baku_mutu', $pasien_gender=="male"?"L":"P")
                ->where(function ($query) use ($pasien_umur) {
                  $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
                    ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
                })
                ->leftJoin('ms_library', function ($join) {
                  $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                    ->whereNull('ms_library.deleted_at')
                    ->whereNull('tb_baku_mutu.deleted_at');
                })
                ->first();
              if (!isset($item_parameter_by_baku_mutu)) {
                $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
                  ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
                  ->where('is_khusus_baku_mutu', '1')
                  ->leftJoin('ms_library', function ($join) {
                    $join->on('ms_library.id_library', '=', 'tb_baku_mutu.library_id')
                      ->whereNull('ms_library.deleted_at')
                      ->whereNull('tb_baku_mutu.deleted_at');
                  })
                  ->where('gender_baku_mutu', $pasien_gender=="male"?"L":"P")
                  ->first();
              }

            }


            $permohonan_uji_paket_klinik = PermohonanUjiPaketKlinik::where('parameter_paket_klinik', $value)
            ->where('permohonan_uji_klinik', $request->post('permohonan_uji_klinik'))->first();

            $post_parameter = new PermohonanUjiParameterKlinik();
            $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
            $post_parameter->permohonan_uji_paket_klinik = $permohonan_uji_paket_klinik->id_permohonan_uji_paket_klinik;
            $post_parameter->parameter_paket_jenis_klinik = $value_parameterpaketjenisklinik->id_parameter_paket_jenis_klinik;
            $post_parameter->parameter_paket_klinik = $value;
            $post_parameter->parameter_satuan_klinik = $value_parametersatuanpaketklinik->parameter_satuan_klinik;
            $post_parameter->jenis_parameter_klinik_id = $value_parameterpaketjenisklinik->parameter_jenis_klinik_id;

            $post_parameter->method_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->title_library;

            $post_parameter->sort_jenis_klinik = $value_parameterpaketjenisklinik->sort;
            $post_parameter->sorting_parameter_satuan = $value_parametersatuanpaketklinik->sorting;

            $post_parameter->harga_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;

            if (isset($item_parameter_by_baku_mutu->unit_id)) {
              $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
              $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
            } else {
              $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
              $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
            }

            $post_parameter->keterangan_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->ket_default_parameter_satuan_klinik;

            // dd($post_parameter);

            $simpan_post_parameter = $post_parameter->save();

            // // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
            $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
              ->get();

            if (count($data_parameter_subsatuan) > 0) {
              foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
                $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
                $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
                $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

                // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
                if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
                  $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
                    ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
                    ->first();

                  $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
                  $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
                }

                $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
              }
            }
          }
        }

        // ServiceRequest
        // dd($satu_sehat);

        if ((isset($item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat) or isset($encounterId) or isset($practitionerId) or $practitionerName) && isset($satu_sehat)){
          $this->createServiceRequest(
            $item_permohonan_uji_klinik->noregister_permohonan_uji_klinik,
            $item_permohonan_uji_klinik->pasien->id_pasien_satu_sehat,
            $encounterId,
            $encounterDisplay,
            $practitionerId,
            $practitionerName,
            Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->toIso8601String(),
            $detailPacket,
            $packetName,
            $request->post('permohonan_uji_klinik'),
            $parameterpaketklinik->id_parameter_paket_klinik
          );
        }

        // #looping jenis parameter paket
        // foreach ($parameterpaketklinik->parameterpaketjenisklinik as $key_parameterpaketjenisklinik => $value_parameterpaketjenisklinik) {
        //   # code...
        //   #looping satuan parameter dari paket
        //   foreach ($value_parameterpaketjenisklinik->parametersatuanpaketklinik as $key_parametersatuanpaketklinik => $value_parametersatuanpaketklinik) {

        //     // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
        //     $pasien_umur = $item_permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik;
        //     $pasien_gender = $item_permohonan_uji_klinik->pasien->gender_pasien;

        //     $check_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
        //       ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
        //       ->where('is_khusus_baku_mutu', '0')
        //       ->first();
        //     // cek dulu apakah ada data dengan data khusus sperti general atau specific
        //     if ($check_parameter_by_baku_mutu) {
        //       $item_parameter_by_baku_mutu = $check_parameter_by_baku_mutu;
        //     } else {
        //       $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
        //         ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
        //         ->where('is_khusus_baku_mutu', '1')
        //         ->where('gender_baku_mutu', $pasien_gender)
        //         ->where(function ($query) use ($pasien_umur) {
        //           $query->where('minimal_umur_baku_mutu', '<=', $pasien_umur)
        //             ->where('maksimal_umur_baku_mutu', '>=', $pasien_umur);
        //         })
        //         ->first();
        //       if (!isset($item_parameter_by_baku_mutu)) {
        //         $item_parameter_by_baku_mutu = BakuMutu::where('parameter_jenis_klinik_id', $value_parameterpaketjenisklinik->parameter_jenis_klinik_id)
        //           ->where('parameter_satuan_klinik_id', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
        //           ->where('is_khusus_baku_mutu', '1')
        //           ->where('gender_baku_mutu', $pasien_gender)
        //           ->first();
        //       }
        //     }

        //     $post_parameter = new PermohonanUjiParameterKlinik();
        //     $post_parameter->permohonan_uji_klinik = $request->post('permohonan_uji_klinik');
        //     $post_parameter->permohonan_uji_paket_klinik = $value;
        //     $post_parameter->parameter_paket_jenis_klinik = $value_parameterpaketjenisklinik->id_parameter_paket_jenis_klinik;
        //     $post_parameter->parameter_paket_klinik = $request->post('satuan_parameter')[$key + 1];
        //     $post_parameter->parameter_satuan_klinik = $value_parametersatuanpaketklinik->parameter_satuan_klinik;
        //     $post_parameter->jenis_parameter_klinik_id = $value_parameterpaketjenisklinik->parameter_jenis_klinik_id;

        //     $post_parameter->sort_jenis_klinik = $value_parameterpaketjenisklinik->sort;
        //     $post_parameter->sorting_parameter_satuan = $value_parametersatuanpaketklinik->sorting;

        //     $post_parameter->harga_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;

        //     if (isset($item_parameter_by_baku_mutu->unit_id)) {
        //       $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
        //       $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
        //     } else {
        //       $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
        //       $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
        //     }

        //     $simpan_post_parameter = $post_parameter->save();

        //     // jika ada parameter yang memiliki subparameter maka akan diinputkan juga ke permohonan_uji_sub_parameter_klinik
        //     $data_parameter_subsatuan = ParameterSubSatuanKlinik::where('parameter_satuan_klinik', $value_parametersatuanpaketklinik->parameter_satuan_klinik)
        //       ->get();

        //     if (count($data_parameter_subsatuan) > 0) {
        //       foreach ($data_parameter_subsatuan as $key_parameter_subsatuan => $value_parameter_subsatuan) {
        //         $post_parameter_subsatuan = new PermohonanUjiSubParameterKlinik();
        //         $post_parameter_subsatuan->permohonan_uji_parameter_klinik_id = $post_parameter->id_permohonan_uji_parameter_klinik;
        //         $post_parameter_subsatuan->parameter_sub_satuan_klinik_id = $value_parameter_subsatuan->id_parameter_sub_satuan_klinik;

        //         // mapping baku mutu untuk data parameter yang memiliki subparameter guna memasukkan data baku mutu dan satuan/unitnya
        //         if (isset($item_parameter_by_baku_mutu->id_baku_mutu)) {
        //           $item_parameter_subsatuan_by_baku_mutu = BakuMutuDetailParameterKlinik::where('baku_mutu_id', $item_parameter_by_baku_mutu->id_baku_mutu)
        //             ->where('parameter_sub_satuan_baku_mutu_detail_parameter_klinik', $value_parameter_subsatuan->parameter_sub_satuan_klinik_id)
        //             ->first();

        //           $post_parameter_subsatuan->satuan_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->unit_id_baku_mutu_detail_parameter_klinik;
        //           $post_parameter_subsatuan->baku_mutu_permohonan_uji_sub_parameter_klinik = $item_parameter_subsatuan_by_baku_mutu->id_baku_mutu_detail_parameter_klinik;
        //         }

        //         $simpan_parameter_subsatuan = $post_parameter_subsatuan->save();
        //       }
        //     }
        //   }
        // }
      }

      // foreach ($data_jenis_parameter as $jenis_parameter_id => $pakets) {
      //     $total_harga_jenis = 0; // Inisialisasi total harga untuk jenis parameter

      //     // Simpan data ke model PermohonanUjiPaketKlinik2 dengan total harga jenis
      //     $data_jenis = [
      //         'id_permohonan_uji_klinik' => $request->permohonan_uji_klinik,
      //         'id_jenis_parameter_klinik' => $jenis_parameter_id,
      //         'total_harga' => $total_harga_jenis, // Akan di-update nanti setelah menghitung total harga
      //     ];
      //     $permohonan_uji_paket = PermohonanUjiPaketKlinik2::create($data_jenis);
      //     $new_paket_id = $permohonan_uji_paket->id_permohonan_uji_paket_klinik;

      //     foreach ($pakets['pakets'] as $parameter_paket_id) {
      //         // Ambil harga dari ParameterPaketKlinik
      //         $harga_parameter_paket = ParameterPaketKlinik::where('id_parameter_paket_klinik', $parameter_paket_id)
      //             ->value('harga_parameter_paket_klinik'); // Ambil nilai langsung, bukan objek

      //         if ($harga_parameter_paket === null) {
      //             // Tangani jika harga tidak ditemukan
      //             \Log::warning("Harga tidak ditemukan untuk paket parameter ID: $parameter_paket_id");
      //             continue;
      //         }

      //         // Tambahkan harga ke total harga dan total harga jenis
      //         $total_harga += $harga_parameter_paket;
      //         $total_harga_jenis += $harga_parameter_paket;

      //         // Simpan data ke model PermohonanUjiParameterKlinik2
      //         $data_paket = [
      //             'id_permohonan_uji_klinik' => $request->permohonan_uji_klinik,
      //             'id_jenis_parameter_klinik' => $jenis_parameter_id,
      //             'id_permohonan_uji_paket_klinik' => $new_paket_id,
      //             'id_parameter_paket_klinik' => $parameter_paket_id,
      //             'harga' => $harga_parameter_paket,
      //         ];

      //         PermohonanUjiParameterKlinik2::create($data_paket);
      //     }

      //     // Update total harga jenis di PermohonanUjiPaketKlinik2
      //     $permohonan_uji_paket->total_harga = $total_harga_jenis;
      //     $permohonan_uji_paket->save();
      // }

      // // Update total harga di PermohonanUjiKlinik2
      // $update_permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($request->permohonan_uji_klinik);
      // $update_permohonan_uji_klinik->total_harga_permohonan_uji_klinik = $total_harga;
      // $update_permohonan_uji_klinik->status_permohonan_uji_klinik = 'ANALIS';
      // $update_permohonan_uji_klinik->save();

      // Create Specimen

      // dd($satu_sehat);

      // Commit transaction
      DB::commit();

      return response()->json(['status' => true, 'pesan' => "Parameter permohonan uji klinik berhasil disimpan!"], 200);
    // } catch (\Exception $e) {
    //   // Jika ada kesalahan, rollback transaction
    //   DB::rollBack();

    //   // Log error dan kirim response dengan status 500
    //   \Log::error('Error menyimpan parameter permohonan uji klinik: ' . $e->getMessage(), [
    //     'permohonan_uji_klinik' => $request->permohonan_uji_klinik,
    //     'jenis_parameters' => $request->jenis_parameters,
    //   ]);

    //   return response()->json(['status' => false, 'pesan' => 'System gagal melakukan penyimpanan!', 'error' => $e->getMessage()], 500);
    // }
  }

  private function createServiceRequest($identifier, $patientId, $encounterId, $encounterDisplay, $practitionerId, $practitionerName, $date, $coding, $text, $permohonanUjiKlinikId, $parameterPaketKlinikId)
  {
      $data = [
        "resourceType" => "ServiceRequest",
        "identifier" => [
          [
            "system" => "http://sys-ids.kemkes.go.id/servicerequest/".env('ORG_ID'),
            "value" => $identifier,
          ],
        ],
        "status" => "active",
        "intent" => "original-order",
        "priority" => "routine",
        "category" => [
          [
            "coding" => [
              [
                "system" => "http://snomed.info/sct",
                "code" => "108252007",
                "display" => "Laboratory procedure",
              ],
            ],
          ],
        ],
        "code" => [
          "coding" => $coding,
          "text" => $text
        ],
        "subject" => ["reference" => "Patient/".$patientId],
        "encounter" => [
          "reference" => "Encounter/".$encounterId,
          "display" => $encounterDisplay,
        ],
        "occurrenceDateTime" => $date,
        "authoredOn" => $date,
        "requester" => [
          "reference" => "Practitioner/".$practitionerId,
          "display" => $practitionerName,
        ],
        "performer" => [
          ["reference" => "Practitioner/".$practitionerId, "display" => $practitionerName],
        ]
      ];

      $response = $this->satuSehatHelper->post('ServiceRequest', $data);

      if ($response['status_code'] == '201'){
        $permohonanUjiPaketKlinik = PermohonanUjiPaketKlinik::query()->where('permohonan_uji_klinik', '=', $permohonanUjiKlinikId)->where('parameter_paket_klinik', '=', $parameterPaketKlinikId)->first();
        if (isset($permohonanUjiPaketKlinik)){
          $permohonanUjiPaketKlinik->id_service_request = $response['body']["id"];
          $permohonanUjiPaketKlinik->response_service_request = json_encode($response['body']);
          $permohonanUjiPaketKlinik->save();
        }
      }else{
        throw new Exception("Gagal membuat service request!");
      }
  }

  private function createSpecimen($identifier, $specimens, $collectedDateTime, $patientId, $patientName, $idRequests, $receivedTime)
  {
    $specimenCode = [
      "Darah Beku" => [
        "code" => "119294007",
        "display" => "Dried blood specimen"
      ],
      "NaF" => [
        "code" => "129501009",
        "display" => "NaF"
      ],
      "EDTA" => [
        "code" => "69519002",
        "display" => "EDTA"
      ],
      "Urine" => [
        "code" => "78014005",
        "display" => "Urine"
      ]
    ];

    $idSpecimenSatuSehat = [];
    foreach ($specimens as $specimen){
      $data = [
        "resourceType" => "Specimen",
        "identifier" => [
          [
            "system" => "http://sys-ids.kemkes.go.id/specimen/".env("ORG_ID"),
            "value" => $identifier,
            "assigner" => [
              "reference" => "Organization/".env("ORG_ID")
            ]
          ]
        ],
        "status" => "available",
        "type" => [
          "coding" => [
            [
              "system" => "http://snomed.info/sct",
              "code" => $specimenCode[$specimen]["code"],
              "display" => $specimenCode[$specimen]["display"]
            ]
          ]
        ],
        "collection" => [
          "collectedDateTime" => $collectedDateTime,
          "extension" => [
            [
              "url" => "https://fhir.kemkes.go.id/r4/StructureDefinition/CollectorOrganization",
              "valueReference" => [
                "reference" => "Organization/".env("ORG_ID")
              ]
            ]
          ]
        ],
        "subject" => [
          "reference" => "Patient/".$patientId,
          "display" => $patientName
        ],
        "request" => $idRequests,
        "receivedTime" => $receivedTime
      ];


      $response = $this->satuSehatHelper->post("Specimen", $data);

      if ($response['status_code'] == '201'){
        $idSpecimenSatuSehat[] = $response['body']["id"];
      }else{
        throw new Exception("Gagal membuat spesimen!");
      }
    }

    $permohonanUjiKlinik = PermohonanUjiKlinik2::query()->where('noregister_permohonan_uji_klinik', '=', $identifier)->first();
    $permohonanUjiKlinik->id_spesimen = implode(',', $idSpecimenSatuSehat);
    $permohonanUjiKlinik->save();
  }

  public function verification($id)
  {

    $item = PermohonanUjiKlinik2::find($id);

    if ($item && isset($item->tglregister_permohonan_uji_klinik)) {
        $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglregister_permohonan_uji_klinik);
    } else {
      $tgl_register = '-';
    }

    $verificationActivitySamples = VerificationActivitySample::where('is_klinik',$id)->get();
    $listVerifications = [];
    foreach ($verificationActivitySamples as  $verificationActivitySample){
      $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
    }

    $verificationActivity = VerificationActivity::all();
    // dd($verificationActivity);

    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.verification', [
      'item' => $item,
      'tgl_register' => $tgl_register,
      'listVerifications' => $listVerifications,
      'verificationActivity' => $verificationActivity,

    ]);
  }

  public function verificationAnalytic(Request $request, $id)
  {
    $request->validate([
      'verification_step' => 'required',
      'start_date' => 'required',
      'stop_date' => 'required',
      'nama_petugas' => 'required'
    ]);

    DB::beginTransaction();
    try {

      if (str_contains( $request->get('start_date'),'/')) {
        $requestStartDate = $request->get('start_date');
      }else{
        $requestStartDate = Carbon::createFromFormat('Y-m-d H:i:s', $request->get('start_date'))->format('d/m/Y H:i');
      }

      if (str_contains( $request->get('stop_date'),'/')) {
        $requestStopDate  = $request->get('stop_date');
      }else{
        $requestStopDate  = Carbon::createFromFormat('Y-m-d H:i:s', $request->get('stop_date')) ->format('d/m/Y H:i');
      }

      $data_verifikasi = VerificationActivitySample::where([
        ['is_klinik', '=', $id],
        ['id_verification_activity', '=', $request->get('verification_step')],
      ])->first();

      if(isset($data_verifikasi) && $data_verifikasi->is_done == 0){
        $data_verifikasi->nama_petugas = $request->get('nama_petugas');
        $data_verifikasi->is_done = 1;
        $data_verifikasi->save();

        $data_permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($id);

        if($request->get('verification_step') == 3){ //Input / Output Hasil Px
          $data_permohonan_uji_klinik->name_analis_permohonan_uji_klinik = strtoupper($request->get('nama_petugas'));

          $data_permohonan_uji_klinik->save();
        }

      }else if(isset($data_verifikasi) && $data_verifikasi->is_done == 1){
        //update start date, stop date, nama petugas
        $data_verifikasi->start_date = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
        $data_verifikasi->stop_date =  Carbon::createFromFormat('d/m/Y H:i', $requestStopDate) ->format('Y-m-d H:i:s');
        $data_verifikasi->nama_petugas = $request->get('nama_petugas');

        $data_permohonan_uji_klinik = PermohonanUjiKlinik2::findOrFail($id);
        // dd($data_permohonan_uji_klinik);

        if($request->get('verification_step') == 1){ //registrasi
          $data_permohonan_uji_klinik->tglregister_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');

          $data_permohonan_uji_klinik->save();
        }elseif($request->get('verification_step') == 6){ //sample
          $data_permohonan_uji_klinik->tgl_sampling_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d');
          $data_permohonan_uji_klinik->jam_sampling_permohonan_uji_klinik =Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('H:i:s');
          $data_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik = strtoupper($request->get('nama_petugas'));

          $data_permohonan_uji_klinik->save();
        }elseif($request->get('verification_step') == 3){ //Input / Output Hasil Px
          $data_permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
          $data_permohonan_uji_klinik->name_analis_permohonan_uji_klinik = strtoupper($request->get('nama_petugas'));

          $data_permohonan_uji_klinik->save();
        }

        $data_verifikasi->save();
      }else{
        //create
        $verificationActivitySample = new VerificationActivitySample();
        $verificationActivitySample->id = Uuid::uuid4()->toString();
        $verificationActivitySample->id_verification_activity = $request->get('verification_step');
        $verificationActivitySample->start_date = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
        $verificationActivitySample->stop_date = Carbon::createFromFormat('d/m/Y H:i', $requestStopDate)->format('Y-m-d H:i:s');
        $verificationActivitySample->nama_petugas = $request->get('nama_petugas');
        $verificationActivitySample->is_klinik = $id;
        $verificationActivitySample->is_done = 1;

        $verificationActivitySample->save();
      }


      if ($request->get('verification_step') == 6){
        $start_date = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
        $stop_date = Carbon::createFromFormat('d/m/Y H:i', $requestStopDate)->format('Y-m-d H:i:s');

        $newStatusHistory = [
          "status" => "in-progress",
          "period" => [
            "start" => Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
            "end" => Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String()
          ]
        ];


        $this->updateEncounter(
          $id,
          strtoupper($request->get('nama_petugas')),
          "in-progress",
          "Ruang Pengambilan Sample",
          "OBSENC",
          "Pengambilan Sample",
          "ATND",
          Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
          Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String(),
          $newStatusHistory
        );
      }

      if ($request->get('verification_step') == 3){

        // dd($request->get('start_date'));

        $start_date = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
        $stop_date = Carbon::createFromFormat('d/m/Y H:i', $requestStopDate)->format('Y-m-d H:i:s');

        $newStatusHistory = [
          "status" => "in-progress",
          "period" => [
            "start" => Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
            "end" => Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String()
          ]
        ];
        $this->updateEncounter(
          $id,
          strtoupper($request->get('nama_petugas')),
          "in-progress",
          "Ruang Lab Klinik",
          "OBSENC",
          "Pemeriksaan Sample",
          "analyte",
          Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
          Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String(),
          $newStatusHistory
        );
      }

      if ($request->get('verification_step') == 5){

        $conditions = [
          [
            "code" => "MN000001",
            "display" => "Stabil"
          ]
        ];

        $idConditions = $this->createCondition($id, $conditions);

        $diagnosis = [];

        if (count($idConditions) > 0){
          foreach ($idConditions as $index => $idCondition){
            $diagnosis[] = [
              "condition" => [
                "reference" => "Condition/".$idCondition,
                "display" => "Pemeriksaan"
              ],
              "use" => [
                "coding" => [
                  [
                    "system" => "http://terminology.hl7.org/CodeSystem/diagnosis-role",
                    "code" => "DD",
                    "display" => "Discharge diagnosis"
                  ]
                ]
              ],
              "rank" => $index + 1
            ];
          }
        }

        $analis = PermohonanUjiKlinik2::query()->where('id_permohonan_uji_klinik', $id)->get('name_analis_permohonan_uji_klinik')->first();
        $start_date = Carbon::createFromFormat('d/m/Y H:i', $requestStartDate)->format('Y-m-d H:i:s');
        $stop_date = Carbon::createFromFormat('d/m/Y H:i', $requestStopDate)->format('Y-m-d H:i:s');

        $newStatusHistory = [
          "status" => "finished",
          "period" => [
            "start" => Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
            "end" => Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String()
          ]
        ];

        if (count($diagnosis) > 0){
          $this->updateEncounterFinished(
            $id,
            $analis->name_analis_permohonan_uji_klinik,
            "finished",
            "Ruang Lab Klinik",
            "OBSENC",
            "Validasi",
            "analyte",
            Carbon::createFromFormat('Y-m-d H:i:s', $start_date)->toIso8601String(),
            Carbon::createFromFormat('Y-m-d H:i:s', $stop_date)->toIso8601String(),
            $diagnosis,
            $newStatusHistory
          );
        }
      }


      DB::commit();

      return redirect()->back()->with(['status' => 'Verifikasi berhasil disimpan!']);
    }catch (Exception $exception){
      DB::rollBack();
      return redirect()->back()->withErrors(['error' => $exception->getMessage()]);
    }

  }

  private function updateEncounter($idPermohonanUjiKlinik, $namaPetugas, $status, $ruangan, $class_code, $class_display, $coding_code, $startDate, $endDate, $newStatusHistory)
  {
    $permohonanUjiKlinik = PermohonanUjiKlinik2::query()->where('id_permohonan_uji_klinik', '=', $idPermohonanUjiKlinik)->first();

    if (isset($permohonanUjiKlinik)){
      if (isset($permohonanUjiKlinik->pasien->id_pasien_satu_sehat) and $permohonanUjiKlinik->pasien->id_pasien_satu_sehat != "" and isset($permohonanUjiKlinik->id_satu_sehat_encounter) and $permohonanUjiKlinik->id_satu_sehat_encounter != "") {
        if ( env('VERSION_SATUSEHAT')=="prd") {
          $location_satusehat= SatuSehatLocation::where('name_satusehat_location',"LIKE",'%'.$ruangan.'%')->where('version_satusehat_location','prd')->first();
          $practitioner_satusehat= SatuSehatPractitioner::where('name_satu_sehat_practitioner','like', $namaPetugas)->first();
          $arrayStatusHistory = json_decode($permohonanUjiKlinik->encounter_json_satu_sehat, true);
          $statusHistory = $arrayStatusHistory['statusHistory'];

          $statusHistory[] = $newStatusHistory;

          $participants = $arrayStatusHistory["participant"];
          $display = $arrayStatusHistory["class"]["display"];

          $participants[] = [
            "type" => [
              [
                "coding" => [
                  [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                    "code" => $coding_code,
                    "display" => $class_display
                  ]
                ]
              ]
            ],
            "individual" => [
              "reference" => "Practitioner/".$practitioner_satusehat->code_satu_sehat_practitioner,
              "display" => $practitioner_satusehat->name_satu_sehat_practitioner
            ]
          ];

           $data = [
             "resourceType" => "Encounter",
             "id" => $permohonanUjiKlinik->id_satu_sehat_encounter,
             "identifier" => [
               [
                 "system" => "http://sys-ids.kemkes.go.id/encounter/".env('ORG_ID'),
                 "value" => $permohonanUjiKlinik->noregister_permohonan_uji_klinik
               ]
             ],
             "status" => $status,
             "class" => [
               "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
               "code" => $class_code,
               "display" => $display
             ],
             "subject" => [
               "reference" => "Patient/".$permohonanUjiKlinik->pasien->id_pasien_satu_sehat,
               "display" => $permohonanUjiKlinik->pasien->nama_pasien
             ],
             "participant" => $participants,
             "period" => [
               "start" => $startDate,
               "end" => $endDate
             ],
             "location" => [
               [
                 "location" => [
                   "reference" => "Location/".$location_satusehat->kode_satusehat_location,
                   "display" => $location_satusehat->name_satusehat_location
                 ]
               ]
             ],
             "statusHistory" => $statusHistory,
             "serviceProvider" => [
               "reference" => "Organization/".env('ORG_ID')
             ]
           ];

          $response = $this->satuSehatHelper->put('Encounter', $permohonanUjiKlinik->id_satu_sehat_encounter,$data);

          if ($response['status_code'] == '200') {
            $permohonanUjiKlinik->encounter_json_satu_sehat=json_encode($response['body']);
            $permohonanUjiKlinik->save();
          }else{
            throw new Exception("gagal update encounter, ". $response['body']['issue'][0]['details']['text']);
          }

        }
      }
    }
  }

  private function updateEncounterFinished($idPermohonanUjiKlinik, $namaPetugas, $status, $ruangan, $class_code, $class_display, $coding_code, $startDate, $endDate, $diagnosis, $newStatusHistory)
  {
    $permohonanUjiKlinik = PermohonanUjiKlinik2::query()->where('id_permohonan_uji_klinik', '=', $idPermohonanUjiKlinik)->first();

    if (isset($permohonanUjiKlinik)){
      if (isset($permohonanUjiKlinik->pasien->id_pasien_satu_sehat) and $permohonanUjiKlinik->pasien->id_pasien_satu_sehat != "" and isset($permohonanUjiKlinik->id_satu_sehat_encounter) and $permohonanUjiKlinik->id_satu_sehat_encounter != "") {
        if ( env('VERSION_SATUSEHAT')=="prd") {
          $location_satusehat= SatuSehatLocation::where('name_satusehat_location',"LIKE",'%'.$ruangan.'%')->where('version_satusehat_location','prd')->first();
          $practitioner_satusehat= SatuSehatPractitioner::where('name_satu_sehat_practitioner','like', $namaPetugas)->first();
          $arrayStatusHistory = json_decode($permohonanUjiKlinik->encounter_json_satu_sehat, true);
          $statusHistory = $arrayStatusHistory['statusHistory'];

          $statusHistory[] = $newStatusHistory;

          $participants = $arrayStatusHistory["participant"];
          $display = $arrayStatusHistory["class"]["display"];

          $participants[] = [
            "type" => [
              [
                "coding" => [
                  [
                    "system" => "http://terminology.hl7.org/CodeSystem/v3-ParticipationType",
                    "code" => $coding_code,
                    "display" => $class_display
                  ]
                ]
              ]
            ],
            "individual" => [
              "reference" => "Practitioner/".$practitioner_satusehat->code_satu_sehat_practitioner,
              "display" => $practitioner_satusehat->name_satu_sehat_practitioner
            ]
          ];

          $data = [
            "resourceType" => "Encounter",
            "id" => $permohonanUjiKlinik->id_satu_sehat_encounter,
            "identifier" => [
              [
                "system" => "http://sys-ids.kemkes.go.id/encounter/".env('ORG_ID'),
                "value" => $permohonanUjiKlinik->noregister_permohonan_uji_klinik
              ]
            ],
            "status" => $status,
            "class" => [
              "system" => "http://terminology.hl7.org/CodeSystem/v3-ActCode",
              "code" => $class_code,
              "display" => $display
            ],
            "subject" => [
              "reference" => "Patient/".$permohonanUjiKlinik->pasien->id_pasien_satu_sehat,
              "display" => $permohonanUjiKlinik->pasien->nama_pasien
            ],
            "participant" => $participants,
            "period" => [
              "start" => $startDate,
              "end" => $endDate
            ],
            "location" => [
              [
                "location" => [
                  "reference" => "Location/".$location_satusehat->kode_satusehat_location,
                  "display" => $location_satusehat->name_satusehat_location
                ]
              ]
            ],
            "diagnosis" => $diagnosis,
            "statusHistory" => $statusHistory,
            "serviceProvider" => [
              "reference" => "Organization/".env('ORG_ID')
            ]
          ];


          $response = $this->satuSehatHelper->put('Encounter', $permohonanUjiKlinik->id_satu_sehat_encounter, $data);

          if ($response['status_code'] == '200') {
            $permohonanUjiKlinik->encounter_json_satu_sehat=json_encode($response['body']);
            $permohonanUjiKlinik->save();
          }else{
            throw new Exception("gagal update encounter, ". $response['body']['issue'][0]['details']['text']);
          }

        }
      }
    }
  }

  private function createCondition($idPermohonanUjiKlinik, $conditions = [])
  {
    $permohonanUjiKlinik = PermohonanUjiKlinik2::query()->where('id_permohonan_uji_klinik', '=', $idPermohonanUjiKlinik)->first();

    if (isset($permohonanUjiKlinik) and isset($permohonanUjiKlinik->id_satu_sehat_encounter) and $permohonanUjiKlinik->id_satu_sehat_encounter != ""){

      $encounter = json_decode($permohonanUjiKlinik->encounter_json_satu_sehat, true);
      $encounterDisplay = $encounter['class']['display'] ?? '-';

      $idConditions = [];
      foreach ($conditions as $condition){
        $data = [
          "resourceType" => "Condition",
          "clinicalStatus" => [
            "coding" => [
              [
                "system" => "http://terminology.hl7.org/CodeSystem/condition-clinical",
                "code" => "active",
                "display" => "Active"
              ]
            ]
          ],
          "category" => [
            [
              "coding" => [
                [
                  "system" => "http://terminology.hl7.org/CodeSystem/condition-category",
                  "code" => "encounter-diagnosis",
                  "display" => "Encounter Diagnosis"
                ]
              ]
            ]
          ],
          "code" => [
            "coding" => [
              [
                "system" => "http://terminology.kemkes.go.id/CodeSystem/clinical-term",
                "code" => $condition["code"],
                "display" => $condition["display"]
              ]
            ]
          ],
          "subject" => [
            "reference" => "Patient/".$permohonanUjiKlinik->pasien->id_pasien_satu_sehat,
            "display" => $permohonanUjiKlinik->pasien->nama_pasien
          ],
          "encounter" => [
            "reference" => "Encounter/".$permohonanUjiKlinik->id_satu_sehat_encounter,
            "display" => $encounterDisplay
          ]
        ];

        $response = $this->satuSehatHelper->post('Condition', $data);

        if ($response['status_code'] == '201'){
          $idConditions[] = $response["body"]["id"];
        }
      }

      return $idConditions;

    }

    return [];

  }

  public function print_verifikasi($id, Request $request)
  {
    $item = PermohonanUjiKlinik2::find($id);
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item->tglregister_permohonan_uji_klinik);

    $url = env("APP_URL");
    if (env("APP_ENV") == "local"){
      $url .= ":8000";
    }
    $url .= "/elits-signature/progress/".$id."/1";
    $result = Builder::create()
      ->data($url)
      ->encoding(new Encoding('UTF-8'))
      ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
      // ->logoPath( asset('assets/admin/images/logo/logo-silaboy.png'))
      ->size(600)
      ->margin(2)
      ->build();
    $qrBase64 = base64_encode($result->getString());

    $verificationActivitySamples = VerificationActivitySample::where('is_klinik', $id)->get();
    $listVerifications = [];

    if (isset($request->tanggal_cetak_verifikasi)){
      $date = DateTime::createFromFormat('d/m/Y', $request->tanggal_cetak_verifikasi);
      $formattedDate = $date->format('Y-m-d');
      $datePrintVerification = $formattedDate;
    }else{
      $datePrintVerification = Carbon::now()->toDateString();
    }

    if (isset($request->signOption) and $request->signOption == 0){
      foreach ($verificationActivitySamples as  $verificationActivitySample){
        $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;
      }

      $signOption = 0;

      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.print_verifikasi', compact('item','listVerifications', 'datePrintVerification', 'signOption'));

      return $pdf->stream();
    }


    $dataPetugasBSRE = [];
    foreach ($verificationActivitySamples as  $verificationActivitySample){
      $listVerifications[$verificationActivitySample->id_verification_activity] = $verificationActivitySample;

      $petugas = Petugas::query()->where('nama', '=', $verificationActivitySample->nama_petugas)->get()->first();
      if (isset($petugas)){
        $dataPetugasBSRE[] = [
          'nik' => $petugas->nik,
          'passPhrase' => $petugas->password,
          'tampilan' => 'invisible',
          'reason' => "hasil12121",
          'location' => "boyolali",
          'text' => "hasil22323"
        ];
      }
    }

    if (count($listVerifications) == 6 and count($dataPetugasBSRE) == 6){
      $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.print_verifikasi', compact('item','listVerifications', 'datePrintVerification', 'qrBase64'));

      $signBSRE = Smt::signBSRE($pdf, $dataPetugasBSRE);

      if (isset($signBSRE["status"]) and $signBSRE["status"] == "success" and isset($signBSRE["data"]) and $signBSRE["data"]["status"] == 200){
        $data =  base64_encode($signBSRE["data"]["file"]);

        return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.blob',
          compact('data')
        );
      }elseif ($signBSRE['status'] == 500){
        return redirect()->back()->with('error-verifikasi', 'errors');
      }else{
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap cek kembali kredensial tanda tangan elektronik untuk setiap petugas!');
      }
    }else{
      if (count($listVerifications) < 6){
        return redirect()->back()->with('error-bsre', 'Dokumen belum bisa ditanda tangani secara elektronik. Harap selesaikan seluruh step verifikasi!');
      }

      if (count($dataPetugasBSRE) < 6){
        return redirect()->back()->with('error-bsre', 'Kredensial untuk tanda tangan elektronik belum lengkap. Harap lengkapi kredensial seluruh petugas!');
      }
    }

    $signOption = 0;
    $pdf = PDF::loadView('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.print_verifikasi', compact('item','listVerifications', 'datePrintVerification', 'signOption'));

    return $pdf->stream();
  }

  public function createPermohonanUjiSample($id_permohonan_uji_klinik)
  {
    $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);
    $tgl_register = Carbon::createFromFormat('Y-m-d H:i:s', $item_permohonan_uji_klinik->tglregister_permohonan_uji_klinik)->isoFormat('D MMMM Y');

    //ambil data verifikasi sample pendaftaran/registrasi
    $data_verifikasi_registrasi = VerificationActivitySample::where([
      ['is_klinik', '=', $id_permohonan_uji_klinik],
      ['id_verification_activity', '=', 1],
    ])->first();

    //set tgl pengujian ditambah 1 jam dari stop date sample analitik
    $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $data_verifikasi_registrasi->stop_date)
    // ->addHour()
    ->format('Y-m-d');

    $startHour = Carbon::createFromFormat('Y-m-d H:i:s', $data_verifikasi_registrasi->stop_date)
    // ->addHour()
    ->format('H:i:s');

    // dd($startHour);

    if ($item_permohonan_uji_klinik->tgl_sampling_permohonan_uji_klinik !== null) {
      $tgl_sampling = $item_permohonan_uji_klinik->tgl_sampling_permohonan_uji_klinik;
    } else {
      if($data_verifikasi_registrasi !== null ){
        $tgl_sampling = $startDate;
      }else{
        $tgl_sampling = '';
      }
    }

    if ($item_permohonan_uji_klinik->jam_sampling_permohonan_uji_klinik !== null) {
      $jam_sampling = $item_permohonan_uji_klinik->jam_sampling_permohonan_uji_klinik;
    } else {
      if($data_verifikasi_registrasi !== null ){
        $jam_sampling = $startHour;
      }else{
        $jam_sampling = '';
      }
    }

    if ($item_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik !== null) {
      $plebotomist = $item_permohonan_uji_klinik->plebotomist_permohonan_uji_klinik;
    } else {
      $plebotomist = '';
    }

    if ($item_permohonan_uji_klinik->vena_permohonan_uji_klinik !== null) {
      $vena = $item_permohonan_uji_klinik->vena_permohonan_uji_klinik;
    } else {
      $vena = '';
    }

    if ($item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik !== null) {
      $lokasi_lain = $item_permohonan_uji_klinik->lokasi_lain_permohonan_uji_klinik;
    } else {
      $lokasi_lain = '';
    }

    if ($item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik !== null) {
      $penanganan_hasil = $item_permohonan_uji_klinik->penanganan_hasil_permohonan_uji_klinik;
    } else {
      $penanganan_hasil = '';
    }

    if ($item_permohonan_uji_klinik->catatan_permohonan_uji_klinik !== null) {
      $catatan = $item_permohonan_uji_klinik->catatan_permohonan_uji_klinik;
    } else {
      $catatan = '';
    }

    if ($item_permohonan_uji_klinik->riwayat_obat_permohonan_uji_klinik !== null) {
      $riwayat_obat = $item_permohonan_uji_klinik->riwayat_obat_permohonan_uji_klinik;
    } else {
      $riwayat_obat = '';
    }

    if ($item_permohonan_uji_klinik->jenis_spesimen !== null) {
      $jenis_spesimen = $item_permohonan_uji_klinik->jenis_spesimen;
    } else {
      $jenis_spesimen = '';
    }

    $optionPlebotomist = SatuSehatPractitioner::query()->where('type', '!=', 'adm')->select(['name_satu_sehat_practitioner'])->get();



    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.sample-permohonan-uji-paramater-klinik', [
      'item_permohonan_uji_klinik' => $item_permohonan_uji_klinik,
      'tgl_register_permohonan_uji_klinik' => $tgl_register,
      'tgl_sampling' => $tgl_sampling,
      'jam_sampling' => $jam_sampling,
      'plebotomist' => $plebotomist,
      'vena' => $vena,
      'lokasi_lain' => $lokasi_lain,
      'penanganan_hasil' => $penanganan_hasil,
      'catatan' => $catatan,
      'riwayat_obat' => $riwayat_obat,
      'optionPlebotomist' => $optionPlebotomist,
      'data_verifikasi_registrasi' => $data_verifikasi_registrasi,
      'jenis_spesimen' => $jenis_spesimen,
    ]);
  }

  public function storePermohonanUjiSample(Request $request, $id_permohonan_uji_klinik)
{
    // Mulai transaksi database
    DB::beginTransaction();

    try {
        // Temukan data permohonan uji klinik berdasarkan ID
        $post = PermohonanUjiKlinik2::find($id_permohonan_uji_klinik);

        // Jika data tidak ditemukan, kembalikan respon error
        if (!$post) {
            return response()->json(['status' => false, 'pesan' => "Data permohonan uji klinik tidak ditemukan!"], 404);
        }

        $plebotomist = $request->plebotomist_permohonan_uji_klinik;

        // validasi required plebotomist
        if (!isset($plebotomist)){
          return response()->json(['status' => false, 'pesan' => "PLEBOTOMIST wajib diisi!"], 200);
        }

        // Format dan simpan data dari request ke dalam model
        $post->tgl_sampling_permohonan_uji_klinik = Carbon::createFromFormat('Y-m-d', $request->tgl_sampling_permohonan_uji_klinik)->format('Y-m-d');
        $post->jam_sampling_permohonan_uji_klinik = $request->jam_sampling_permohonan_uji_klinik;
        $post->jenis_spesimen = implode(',', $request->jenis_spesimen);
        $post->plebotomist_permohonan_uji_klinik = $plebotomist;
        $post->lokasi_lain_permohonan_uji_klinik = $request->lokasi_lain_permohonan_uji_klinik;
        $post->vena_permohonan_uji_klinik = $request->vena_permohonan_uji_klinik;
        $post->penanganan_hasil_permohonan_uji_klinik = $request->penanganan_hasil_permohonan_uji_klinik;
        $post->catatan_permohonan_uji_klinik = $request->catatan_permohonan_uji_klinik;
        $post->riwayat_obat_permohonan_uji_klinik = $request->riwayat_obat_permohonan_uji_klinik;

        $simpan = $post->save();

        $data_verifikasi_sample = VerificationActivitySample::where([
          ['is_klinik', '=', $id_permohonan_uji_klinik],
          ['id_verification_activity', '=', 6],
        ])->first();

        if($data_verifikasi_sample == null){
          $verificationActivitySample = new VerificationActivitySample();
          $verificationActivitySample->id = Uuid::uuid4()->toString();
          $verificationActivitySample->id_verification_activity = 6;

          $time = $post->jam_sampling_permohonan_uji_klinik; // Ambil nilai awal dari input waktu

          if (strlen($time) == 5) { // Jika format waktu `h:i` memiliki panjang 5 karakter
              $time .= ':00'; // Tambahkan `:00` di bagian detik
          }

          // Gabungkan tanggal dan jam menjadi start_date
          $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $post->tgl_sampling_permohonan_uji_klinik . ' ' . $time);

          $verificationActivitySample->start_date = $startDate;

          // Tambahkan 1 jam untuk stop_date
          $verificationActivitySample->stop_date = Carbon::createFromFormat('Y-m-d H:i:s', $startDate)
          ->addMinutes(5)
          ->format('Y-m-d H:i:s');


          $verificationActivitySample->nama_petugas = ucwords(strtolower($post->plebotomist_permohonan_uji_klinik));
          $verificationActivitySample->is_klinik = $id_permohonan_uji_klinik;
          // $verificationActivitySample->is_done = 1;

          $verificationActivitySample->save();
        }

        $jenisSpesimens = $request->jenis_spesimen;

        // filter membuang pilihan "-"
        $jenisSpesimens = array_filter($jenisSpesimens, function ($spesimen){
          return $spesimen !== "-";
        });

        if (count($jenisSpesimens) > 0){
          $identifier = $post->noregister_permohonan_uji_klinik;
          $collectedDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $post->tgl_sampling_permohonan_uji_klinik . ' ' . $post->jam_sampling_permohonan_uji_klinik)->toIso8601String();
          $pasien = $post->pasien;

          $idRequestpakets = PermohonanUjiPaketKlinik::query()->where('permohonan_uji_klinik', '=', $post->id_permohonan_uji_klinik)->get(["id_service_request"]);

          $idRequests = [];
          foreach ($idRequestpakets as $idRequestpaket){
            $idRequests[] = [
              "reference" => "ServiceRequest/".$idRequestpaket->id_service_request
            ];
          }

          $receivedTime = Carbon::createFromFormat('Y-m-d H:i:s', $post->tglregister_permohonan_uji_klinik)->toIso8601String();

          if (
            isset($pasien->id_pasien_satu_sehat) and
            isset($pasien->nama_pasien) and
            isset($identifier) and
            isset($collectedDateTime) and
            isset($idRequestpakets) and isset($receivedTime) and
            isset($post->id_satu_sehat_encounter) and $post->id_satu_sehat_encounter != ""
          ){

            $this->createSpecimen($identifier, $jenisSpesimens, $collectedDateTime, $pasien->id_pasien_satu_sehat, $pasien->nama_pasien, $idRequests, $receivedTime);
          }
        }



        // Commit transaksi jika semua berhasil
        DB::commit();

        return response()->json(['status' => true, 'pesan' => "Data permohonan uji klinik untuk sample berhasil diubah!"], 200);

    } catch (\Exception $e) {
        // Rollback transaksi jika terjadi error
        DB::rollBack();

        // Log error atau lakukan hal lain yang diperlukan
        Log::error('Error saat menyimpan permohonan uji sample: ' . $e->getMessage());
        // dd($e->getMessage());

        return response()->json(['status' => false, 'pesan' => "Data permohonan uji klinik untuk sample tidak berhasil diubah!"], 500);
    }
}

}
