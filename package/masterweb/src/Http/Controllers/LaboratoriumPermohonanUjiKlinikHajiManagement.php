<?php

namespace Smt\Masterweb\Http\Controllers;

use App\Http\Controllers\Controller;
use Smt\Masterweb\Models\PermohonanUjiKlinikHaji;
use Smt\Masterweb\Models\PermohonanUjiPaketKlinik;
use Smt\Masterweb\Models\ParameterPaketKlinik;
use Smt\Masterweb\Models\PermohonanUjiKlinik2;
use Smt\Masterweb\Models\Pasien;
use Smt\Masterweb\Models\BakuMutu;
use Smt\Masterweb\Models\PermohonanUjiParameterKlinik;
use Smt\Masterweb\Models\PermohonanUjiSubParameterKlinik;
use Smt\Masterweb\Models\ParameterSubSatuanKlinik;
use Smt\Masterweb\Models\VerificationActivitySample;
use Smt\Masterweb\Models\BakuMutuDetailParameterKlinik;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Smt\Masterweb\Exports\FormatHajiExport;
use Smt\Masterweb\Imports\HajiImport;
use Illuminate\Support\Facades\DB;
use Smt\Masterweb\Models\NumberKlinik;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;


class LaboratoriumPermohonanUjiKlinikHajiManagement extends Controller
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
    $data = PermohonanUjiKlinikHaji::orderBy('created_at', 'desc')->get();
    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.haji.list', [
      'data' => $data,
    ]);
  }

  public function rules($request)
  {
    $rule = [
      'nama_haji' => 'required|string|max:255',
      'tgl_haji' => 'required',
      'kuota_haji' => 'required',
];

    $pesan = [
      'nama_haji.required' => 'Nama Haji wajib diisi.',
      'tgl_haji.required' => 'Tanggal Haji wajib diisi.',
      'kuota_haji.required' => 'Kuota Haji wajib diisi.',
    ];

    return Validator::make($request, $rule, $pesan);
  }

  public function create()
  {
    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.haji.add');
  }

  public function store(Request $request)
  {
      // Validasi data
      $validator = $this->rules($request->all());

      if ($validator->fails()) {
          return response()->json(['status' => false, 'pesan' => $validator->errors()]);
      }

      DB::beginTransaction(); // Mulai transaksi

      try {
          // Simpan data haji ke dalam database
          $haji = new PermohonanUjiKlinikHaji();
          $haji->nama_haji = $request->input('nama_haji');
          $haji->tgl_haji = $request->input('tgl_haji');
          $haji->kuota_haji = $request->input('kuota_haji');
          $haji->save();

          // Dapatkan last_number terbesar dari tabel NumberKlinik

          $count = NumberKlinik::where( DB::raw( "YEAR(tb_number_klinik.created_at)"), date('Y'))->max('last_number');
          // $count = NumberKlinik::max('last_number');

          // Booking haji dan Simpan data last_number_klinik
          $number_klinik = new NumberKlinik();
          $number_klinik->new_number = $count + 1;
          $number_klinik->last_number = $count + $haji->kuota_haji;
          $number_klinik->id_haji = $haji->id_permohonan_uji_klinik_haji; // Ambil ID setelah penyimpanan
          $number_klinik->save();

          DB::commit(); // Commit transaksi jika semua berhasil

          return response()->json(['status' => true, 'pesan' => "Data haji berhasil disimpan!"], 200);

      } catch (\Exception $e) {
          DB::rollBack(); // Rollback transaksi jika ada error

          // Kirim response error
          return response()->json(['status' => false, 'pesan' => "Terjadi kesalahan: " . $e->getMessage()], 500);
      }
  }

  public function downloadFormatHaji($id)
  {
      DB::beginTransaction();

      try {
          $data = PermohonanUjiKlinikHaji::where('id_permohonan_uji_klinik_haji', $id)->first();
          $data_number = NumberKlinik::where('id_haji', $id)->first();

          if($data_number){
            //ambil data last_number sebelumyanya / booking
            $count = $data_number->last_number - $data->kuota_haji;

          }
          // else{
          //   $count = NumberKlinik::max('last_number');

          //   // simpan last_number_klinik
          //   $number_klinik = new NumberKlinik();
          //   $number_klinik->new_number =  $count + 1;
          //   $number_klinik->last_number =  $count + $data->kuota_haji;
          //   $number_klinik->id_haji =  $id;
          //   $number_klinik->save();
          // }

          DB::commit();

          return Excel::download(new FormatHajiExport($count, $data->nama_haji, $data->tgl_haji, $data->kuota_haji), 'Format-Data-Haji-' . $data->nama_haji . '-' . $data->tgl_haji . '.xlsx');
      } catch (\Exception $e) {
          DB::rollBack();
          return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
      }
  }

  public function importHaji(Request $request, $id)
  {
    $request->validate([
      'file' => 'required|mimes:xlsx'
    ]);

    $import = new HajiImport();
    $data = Excel::toArray($import, $request->file('file'))[0];

    // dd($data);

    //cek momor lab apakah sudah pernah diinput
    foreach ($data as $row) {
        $data_permohonan_uji_klinik = PermohonanUjiKlinik2::where('noregister_permohonan_uji_klinik', $row[6])->first();
        if($data_permohonan_uji_klinik){
          return back()->with('error', 'Data dengan nomor lab format sudah pernah di unggah!');
        }
    }

    DB::beginTransaction();

    try {
        foreach ($data as $row) {

            $bulanIndonesia = [
              'Januari' => 'January',
              'Februari' => 'February',
              'Maret' => 'March',
              'April' => 'April',
              'Mei' => 'May',
              'Juni' => 'June',
              'Juli' => 'July',
              'Agustus' => 'August',
              'September' => 'September',
              'Oktober' => 'October',
              'November' => 'November',
              'Desember' => 'December',
            ];

            $tgl_lahir = $row[3];
            $tgl_registrasi = $row[15];
            $tgl_validasi = $row[16];

            // Mengonversi tanggal
            if (is_numeric($tgl_lahir) && is_numeric($tgl_registrasi) && is_numeric($tgl_validasi)) {
                // Konversi dari format serial Excel ke DateTime
                $tgl_lahir = Date::excelToDateTimeObject($tgl_lahir)->format('Y-m-d');
                $tgl_registrasi = Date::excelToDateTimeObject($tgl_registrasi)->format('Y-m-d');
                $tgl_validasi = Date::excelToDateTimeObject($tgl_validasi)->format('Y-m-d');
            } else {
                try {
                    // Ubah bulan dari bahasa Indonesia ke bahasa Inggris
                    $tgl_lahir = $this->ubahBulanKeInggris($tgl_lahir, $bulanIndonesia);
                    $tgl_registrasi = $this->ubahBulanKeInggris($tgl_registrasi, $bulanIndonesia);
                    $tgl_validasi = $this->ubahBulanKeInggris($tgl_validasi, $bulanIndonesia);

                    // Konversi dari format teks DD MMMM YYYY ke Carbon instance
                    $tgl_lahir = Carbon::createFromFormat('d F Y', $tgl_lahir)->format('Y-m-d');
                    $tgl_registrasi = Carbon::createFromFormat('d F Y', $tgl_registrasi)->format('Y-m-d');
                    $tgl_validasi = Carbon::createFromFormat('d F Y', $tgl_validasi)->format('Y-m-d');
                } catch (\Exception $e) {
                    // Jika format tanggal tidak valid, set ke null atau tangani sesuai kebutuhan
                    $tgl_lahir = null;
                    $tgl_registrasi = null;
                    $tgl_validasi = null;
                }
            }

            // Jika $tgl_validasi tidak memiliki waktu, tambahkan waktu saat ini
            if ($tgl_registrasi || $tgl_validasi) {
                $tgl_registrasi = Carbon::parse($tgl_registrasi)->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s');
                $tgl_validasi = Carbon::parse($tgl_validasi)->format('Y-m-d') . ' ' . Carbon::now()->format('H:i:s');
            }

            // Menghitung umur dalam tahun, bulan, dan hari
            if ($tgl_lahir) {
                $birthdate = Carbon::parse($tgl_lahir);
                $now = Carbon::now();

                $years = $birthdate->diffInYears($now);
                $months = $birthdate->copy()->addYears($years)->diffInMonths($now);
                $days = $birthdate->copy()->addYears($years)->addMonths($months)->diffInDays($now);

                $umur = "{$years} tahun, {$months} bulan, {$days} hari";
            } else {
                $years = null;
                $months = null;
                $days = null;
            }

            // dd($tgl_lahir);


            // Pengecekan data pasien dengan LIKE
            $exists = Pasien::where('nama_pasien', $row[1])
                ->where('tgllahir_pasien', $tgl_lahir)
                ->where('gender_pasien', $row[5])
                ->exists();

            //menentukan nomor urut dan nomor rekamedis
            $count = Pasien::max('nourut_pasien');
            $set_count = $count < 1 ? 1 : $count + 1;

              if (!$exists) {
                $new_pasien = new Pasien();
                $new_pasien->nourut_pasien = $set_count;
                $new_pasien->no_rekammedis_pasien = $set_count;
                $new_pasien->nama_pasien = $row[1];
                $new_pasien->tgllahir_pasien = $tgl_lahir;
                $new_pasien->alamat_pasien = $row[4];
                $new_pasien->gender_pasien = $row[5];
                $new_pasien->save();

                $id_pasien = $new_pasien->id_pasien; // Mengambil ID pasien yang baru disimpan
              } else {
                $pasien = Pasien::where('nama_pasien', $row[1])
                    ->where('tgllahir_pasien', $tgl_lahir)
                    ->where('gender_pasien', $row[5])
                    ->first();

                $id_pasien = $pasien->id_pasien; // Mengambil ID pasien yang ditemukan
              }

              //cari parameter paket klinik
              $data_paket = ParameterPaketKlinik::where('name_parameter_paket_klinik', '=', 'Paket Haji')->first();
              $first_parameter_paket_jenis_klinik = $data_paket->parameterpaketjenisklinik->first();
              // dd($data_paket);

              // simpan permohonan uji klinik
              $permohonan_uji_klinik = new PermohonanUjiKlinik2();
              $permohonan_uji_klinik->nourut_permohonan_uji_klinik = Str::before($row[6], '/');
              $permohonan_uji_klinik->noregister_permohonan_uji_klinik= $row[6];
              $permohonan_uji_klinik->tglregister_permohonan_uji_klinik= $tgl_registrasi;
              $permohonan_uji_klinik->pasien_permohonan_uji_klinik= $id_pasien;
              $permohonan_uji_klinik->umurtahun_pasien_permohonan_uji_klinik= $years;
              $permohonan_uji_klinik->umurbulan_pasien_permohonan_uji_klinik= $months;
              $permohonan_uji_klinik->umurhari_pasien_permohonan_uji_klinik= $days;
              $permohonan_uji_klinik->total_harga_permohonan_uji_klinik= $data_paket->harga_parameter_paket_klinik;
              $permohonan_uji_klinik->metode_pembayaran = 0;
              $permohonan_uji_klinik->status_pembayaran= 0;
              $permohonan_uji_klinik->status_permohonan_uji_klinik= 'SELESAI';
              $permohonan_uji_klinik->id_permohonan_uji_klinik_haji= $id;
              $permohonan_uji_klinik->is_haji= 1;
              $permohonan_uji_klinik->no_amplop_1= $row[17];
              $permohonan_uji_klinik->no_amplop_2= $row[18];
              $permohonan_uji_klinik->no_amplop_3= $row[19];
              $permohonan_uji_klinik->no_amplop_4= $row[20];
              $permohonan_uji_klinik->no_amplop_5= $row[21];
              $permohonan_uji_klinik->save();

              // dd($permohonan_uji_klinik);
              //simpan permohonan uji paket klinik

              if ($data_paket && $data_paket->parameterpaketjenisklinik->isNotEmpty()) {
                  // Mengambil item pertama dari koleksi

                  // Membuat objek baru PermohonanUjiPaketKlinik
                  $permohonan_paket_klinik = new PermohonanUjiPaketKlinik();
                  $permohonan_paket_klinik->permohonan_uji_klinik = $permohonan_uji_klinik->id_permohonan_uji_klinik;
                  $permohonan_paket_klinik->parameter_paket_klinik = $data_paket->id_parameter_paket_klinik;
                  $permohonan_paket_klinik->parameter_jenis_klinik = $first_parameter_paket_jenis_klinik->parameter_jenis_klinik_id;
                  $permohonan_paket_klinik->type_permohonan_uji_paket_klinik = "P";
                  $permohonan_paket_klinik->harga_permohonan_uji_paket_klinik = $data_paket->harga_parameter_paket_klinik;
                  $permohonan_paket_klinik->is_haji = 1;
                  $permohonan_paket_klinik->save();

                  // Debugging untuk memastikan data tersimpan dengan benar
                  // dd($permohonan_paket_klinik);
              } else {
                  // Tangani kasus ketika $data_paket tidak ditemukan atau relasi kosong
                  dd("Parameter Paket Klinik atau Jenis Klinik tidak ditemukan.");
              }


              $item_permohonan_uji_klinik = PermohonanUjiKlinik2::find($permohonan_uji_klinik->id_permohonan_uji_klinik);

              //cek parameter paket jenis klinik
              foreach ($data_paket->parameterpaketjenisklinik as $key_parameterpaketjenisklinik => $value_parameterpaketjenisklinik) {
                # code...
                #looping satuan parameter dari paket
                foreach ($value_parameterpaketjenisklinik->parametersatuanpaketklinik as $key_parametersatuanpaketklinik => $value_parametersatuanpaketklinik) {

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

                  //simpan parameter klinik
                  $post_parameter = new PermohonanUjiParameterKlinik();
                  $post_parameter->permohonan_uji_klinik = $permohonan_uji_klinik->id_permohonan_uji_klinik;
                  $post_parameter->permohonan_uji_paket_klinik = $permohonan_paket_klinik->id_permohonan_uji_paket_klinik;
                  $post_parameter->parameter_paket_jenis_klinik = $value_parameterpaketjenisklinik->id_parameter_paket_jenis_klinik;
                  $post_parameter->parameter_paket_klinik = $permohonan_paket_klinik->parameter_paket_klinik;
                  $post_parameter->parameter_satuan_klinik = $value_parametersatuanpaketklinik->parameter_satuan_klinik;
                  $post_parameter->jenis_parameter_klinik_id = $value_parameterpaketjenisklinik->parameter_jenis_klinik_id;
                  // $post_parameter->hasil_permohonan_uji_parameter_klinik = $row[$i];

                  $post_parameter->method_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->title_library;

                  $post_parameter->sort_jenis_klinik = $value_parameterpaketjenisklinik->sort;
                  $post_parameter->sorting_parameter_satuan = $value_parametersatuanpaketklinik->sorting;
                  $post_parameter->keterangan_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->ket_default_parameter_satuan_klinik;

                  $post_parameter->harga_permohonan_uji_parameter_klinik = $value_parametersatuanpaketklinik->parametersatuanklinik->harga_satuan_parameter_satuan_klinik;
                  $post_parameter->is_haji = 1;

                  if (isset($item_parameter_by_baku_mutu->unit_id)) {
                    $post_parameter->satuan_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->unit_id;
                    $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = $item_parameter_by_baku_mutu->id_baku_mutu;
                  } else {
                    $post_parameter->satuan_permohonan_uji_parameter_klinik = null;
                    $post_parameter->baku_mutu_permohonan_uji_parameter_klinik = null;
                  }

                  $columns = [
                    1 => $row[7],
                    2 => $row[8],
                    3 => $row[9],
                    4 => $row[10],
                    5 => $row[11],
                    6 => $row[12],
                    7 => $row[13],
                    8 => $row[14],
                  ];

                  if (isset($columns[$value_parametersatuanpaketklinik->sorting])) {
                      $post_parameter->hasil_permohonan_uji_parameter_klinik = $columns[$value_parametersatuanpaketklinik->sorting];
                  }

                  // dd($post_parameter);

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

              //simpadn data permohonan uji klinik analis dan update table permohonan uji klinik
              // $permohonan_uji_klinik->tglpengujian_permohonan_uji_klinik = '-';
              // $permohonan_uji_klinik->spesimen_darah_permohonan_uji_klinik = '-';
              // $permohonan_uji_klinik->spesimen_urine_permohonan_uji_klinik = '-';
              // $permohonan_uji_klinik->analis_permohonan_uji_klinik = '-';
              // $permohonan_uji_klinik->name_analis_permohonan_uji_klinik = '-';
              // $permohonan_uji_klinik->nip_analis_permohonan_uji_klinik = '-';
              $permohonan_uji_klinik->status_permohonan_uji_klinik = 'SELESAI';
              $permohonan_uji_klinik->save();


              //simpan verifikkasi
              // Data untuk setiap aktivitas verifikasi
              $activities = [
                ['id_verification_activity' => 1, 'date' => $tgl_registrasi, 'nama_petugas' => 'Umi Sulistiyana'], // pendaftaran/registrasi
                ['id_verification_activity' => 6, 'date' => $tgl_registrasi, 'nama_petugas' => 'Tatin Prastyo Meiningsih'], // pengambilan sample
                ['id_verification_activity' => 2, 'date' => $tgl_registrasi, 'nama_petugas' => 'Hewi Yuliati'], // Pemeriksaan / Analitik
                ['id_verification_activity' => 3, 'date' => $tgl_registrasi, 'nama_petugas' => 'Hewi Yuliati'], // pengambilan sample
                ['id_verification_activity' => 4, 'date' => $tgl_validasi, 'nama_petugas' => 'Hewi Yuliati'],   // verifikasi
                ['id_verification_activity' => 5, 'date' => $tgl_validasi, 'nama_petugas' => 'dr. Muharyati'],   // validasi
              ];

              foreach ($activities as $key => $activity) {
                $verificationActivitySample = new VerificationActivitySample();
                $verificationActivitySample->id = Uuid::uuid4()->toString();
                $verificationActivitySample->id_verification_activity = $activity['id_verification_activity'];
                $verificationActivitySample->start_date = $activity['date'];
                $verificationActivitySample->stop_date = $activity['date'];
                $verificationActivitySample->nama_petugas = $activity['nama_petugas'];
                $verificationActivitySample->is_klinik = $permohonan_uji_klinik->id_permohonan_uji_klinik;
                $verificationActivitySample->is_haji = 1;
                $verificationActivitySample->is_done = 1;
                $verificationActivitySample->created_at = Carbon::now()->addSeconds($key); // Tambah detik berdasarkan urutan;

                // Simpan ke database
                $verificationActivitySample->save();
              }

              $data_haji = PermohonanUjiKlinikHaji::findOrFail($id);
              $data_haji->status_haji = 1; //sudah import

              $data_haji->save();
              // dd($data_haji);
        }

        DB::commit();
        return back()->with('success', 'Data haji berhasil diimport!');
    } catch (Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Terjadi kesalahan saat mengimpor data.');
    }
  }

   // Fungsi untuk mengganti nama bulan dalam bahasa Indonesia ke bahasa Inggris
  public function ubahBulanKeInggris($tanggal, $bulanIndonesia) {
      foreach ($bulanIndonesia as $namaBulanIndo => $namaBulanEng) {
          if (strpos($tanggal, $namaBulanIndo) !== false) {
              // Ganti nama bulan Indonesia dengan nama bulan dalam bahasa Inggris
              return str_replace($namaBulanIndo, $namaBulanEng, $tanggal);
          }
      }
      return $tanggal; // Jika tidak ditemukan, kembalikan tanggal tanpa perubahan
  }

  public function getHaji(Request $request)
  {
      // Ambil data urine dari database
      $data = DB::table('tb_permohonan_uji_klinik_haji')
                      ->select('id_permohonan_uji_klinik_haji', 'nama_haji')
                      ->when($request->is_filter, function($query) {
                          // Jika filter ada, tambahkan kondisi filter
                          $query->where('is_haji', 1);
                      })
                      ->get();

      // Return data sebagai JSON
      return response()->json($data);
  }

  public function printAmplop($id){
    $data = PermohonanUjiKlinik2::where('id_permohonan_uji_klinik', $id)->first();
    $pasien = Pasien::where('id_pasien', $data->pasien_permohonan_uji_klinik)->first();
    return view('masterweb::module.admin.laboratorium.permohonan-uji-klinik-2.formatPrint.print_amplop_haji', [
      'data' => $data,
      'pasien' => $pasien,
    ]);
  }

  public function sortingNumberKlinik()
  {
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
  }

  public function destroy($id)
  {
      DB::beginTransaction();

      try {
          // Hapus data haji
          $hapusHaji = PermohonanUjiKlinikHaji::where('id_permohonan_uji_klinik_haji', $id)->delete();

          // Hapus nomor klinik
          $hapusNumberKlinik = NumberKlinik::where('id_haji', $id)->delete();

          $this->sortingNumberKlinik();

          // dd("cek");


          if ($hapusHaji && $hapusNumberKlinik) {
              DB::commit(); // Commit transaksi jika semua berhasil

              return response()->json(['status' => true, 'pesan' => "Data Haji berhasil dihapus!"], 200);
          }

          // Rollback jika salah satu operasi gagal
          DB::rollback();
          return response()->json(['status' => false, 'pesan' => "Data Haji gagal dihapus!"], 400);

      } catch (\Exception $e) {
          DB::rollback(); // Rollback transaksi jika terjadi exception
          return response()->json(['status' => false, 'pesan' => $e->getMessage()], 500);
      }
  }

}