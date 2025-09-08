<?php

use Illuminate\Http\Request;
use GuzzleHttp\Client;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('/', function () {
//   return redirect('/');
// });


Route::post('/login', 'AuthController@login');
Route::post('/logout', 'AuthController@logout');

// Route::group(['middleware' => ['web']], function () {
Route::post('/method', 'MethodController@index');
Route::post('/customer', 'CustomerController@index');

Route::get('/customer/{id}', ['as' => 'customer.detail', 'uses' =>  'CustomerController@detail']);

Route::post('/products', 'ProductController@index');
Route::post('/packet/{sample_type}/{jenis_makanan?}', 'PacketController@sampletype');
Route::post('/packet', 'PacketController@index');



Route::post('/unit', 'UnitController@index');
Route::post('/lab-officer', 'LabOfficerController@index');
Route::post('/sampling-officer', 'SamplingOfficerController@index');


Route::post('/pendaftara-bpjs', 'PendaftaranController@store');

Route::get('/pcare/ping', function () {
  $client = new Client([
      'base_uri' => config('bpjs.base_url'),
      'timeout'  => 15,
      'verify'   => false, // kalau SSL lokal bermasalah
  ]);

  // headers dari service kamu
  $headers = app(\App\Services\BpjsPcareGuzzleClient::class)->headers();

  $url = '/' . trim(config('bpjs.service'), '/') . '/referensi/poli';

  try {
      $res = $client->get($url, [
          'headers' => $headers,
      ]);

      return response()->json([
          'called' => config('bpjs.base_url') . $url,
          'status' => $res->getStatusCode(),
          'body'   => (string) $res->getBody(),
      ]);
  } catch (\GuzzleHttp\Exception\RequestException $e) {

    $status = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;
    $body   = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null;

    return [
        'error'   => true,
        'status'  => $status,
        'message' => $e->getMessage(),
        'body'    => $body,
    ];
      // return response()->json([
      //     'called'  => config('bpjs.base_url') . $url,
      //     'status'  => $e->getResponse()?->getStatusCode(),
      //     'message' => $e->getMessage(),
      //     'body'    => $e->getResponse()?->getBody()->getContents(),
      // ], 500);
  }
});



Route::get('/pcare/debug-probe', function () {
  $base   = rtrim(config('bpjs.base_url'), '/');         // ex: https://new-api.bpjs-kesehatan.go.id
  $svc    = trim(config('bpjs.service'), '/');           // ex: pcare-rest-v3.0 (sementara set v3 utk tes)
  $consId = config('bpjs.cons_id');
  $secret = config('bpjs.secret_key');
  $uKey   = config('bpjs.user_key');
  $user   = config('bpjs.pcare_user');
  $pass   = config('bpjs.pcare_pass');
  $kdApp  = config('bpjs.kd_aplikasi');

  // HMAC headers
  $t   = (string) Carbon\Carbon::now('UTC')->timestamp;
  $sig = base64_encode(hash_hmac('sha256', $consId . '&' . $t, $secret, true));
  $basic = 'Basic ' . base64_encode($user . ':' . $pass . ':' . $kdApp);

  $headers = [
      'X-cons-id'     => $consId,
      'X-timestamp'   => $t,
      'X-signature'   => $sig,
      'X-User-Key'    => $uKey,
      'Authorization' => $basic,
      'Accept'        => 'application/json',
  ];

  $client = new Client([
      'base_uri' => $base,
      'timeout'  => 15,
      'verify'   => false, // jika cert lokal rewel
  ]);

  // Beberapa kandidat path umum (v3). Tambahkan/ubah sesuai dokumen yang kamu punya.
  $paths = [
      "/$svc/",                              // root context
      "/$svc/referensi/poli",                // referensi poli (umum v3)
      "/$svc/referensi/diagnosa/A00",        // referensi diagnosa (cek cepat)
      "/$svc/peserta/0000000000000",         // peserta (dummy noka)
      "/$svc/kunjungan",                     // resource create (POST biasanya; GET utk cek 405/404)
      // Jika kamu punya dugaan nama lain (mis. pcarews4.0), tambahkan di sini:
      // "/pcarews4.0/referensi/poli",
  ];

  $results = [];
  foreach ($paths as $p) {
      try {
          $res = $client->get($p, ['headers' => $headers]);
          $results[] = ['path' => $p, 'status' => $res->getStatusCode(), 'len' => strlen((string)$res->getBody())];
      } catch (RequestException $e) {
          $code = $e->getResponse() ? $e->getResponse()->getStatusCode() : null;
          // potong body agar respons nggak kepanjangan
          $body = $e->getResponse() ? substr($e->getResponse()->getBody()->getContents(), 0, 300) : null;
          $results[] = ['path' => $p, 'status' => $code, 'err' => $e->getMessage(), 'body' => $body];
      }
  }

  return response()->json([
      'base'   => $base,
      'svc'    => $svc,
      'probe'  => $results,
      'hint'   => 'Jika semua 404 => context path salah / belum diprovision. Jika ada 401/403 => context benar, header/signature perlu dicek.',
  ]);
});



// });




Route::group(['middleware' => ['jwt.verify']], function () {
  Route::get('/user', 'AuthController@getAuthenticatedUser');
});

Route::group(['middleware' => ['jwt.verify', 'role:ADMD, admin']], function () {
  Route::get('/device', 'DeviceController@index');
  Route::post('/user-tele/create', 'UserTeleController@store');
  Route::put('/user-tele/login-tele/{id}', 'UserTeleController@loginTele');
  Route::get('/user-tele', 'UserTeleController@index');
});


// Route::get('/logut', 'AuthController@login');