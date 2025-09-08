<?php

namespace Smt\Masterweb\Http\Controllers\Auth;

use App\Rules\Captcha;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Smt\Masterweb\Models\User;
use Smt\Masterweb\Models\SatuSehat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
  /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = 'home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')
      ->except('logout');
  }

  public function rules($request)
  {
    $rule = [
      'username' => 'required|string|exists:ms_users,username',
      'password' => 'required|string'
    ];

    $pesan = [
      'username.required' => 'Username tidak boleh kosong!',
      'username.exists' => 'Username tidak ditemukan!',
      'password.required' => 'Password tidak boleh kosong!'
    ];

    return Validator::make($request, $rule, $pesan);
  }

  protected function login(Request $request)
  {
    $input = $request->all();
    $validator = $this->rules($input);

    if ($validator->fails()) {
      // return redirect()->back()->with('errors', $validator->errors());

      return redirect()
        ->back()
        ->withErrors($validator->errors())
        ->withInput();
    } else {
      // ERROR Undefined index: password
      $auth = User::where('username', '=', $request->username)->first();

      if ($auth) {
        if (Hash::check($request->password, $auth->password)) {
          if ($auth->publish == '1') {
            Auth::login($auth);
            $client_auth_satusehat = new Client([
              // Base URI is used with relative requests
              'base_uri' => env('URL_SATUSEHAT'),
              // You can set any number of default request options.
            ]);
            $response_auth_satusehat= $client_auth_satusehat->request('POST',  env('AUTH_URL_SATUSEHAT')."accesstoken?grant_type=client_credentials", ['form_params' => [
              'client_id' => env('CLIENT_ID_SATUSEHAT'),
              'client_secret' => env('CLIENT_SECRET_SATUSEHAT'),
            ]]);
            
            if ($response_auth_satusehat->getStatusCode()==200) {
              # code...
              
              // dd(json_decode($response_auth_satusehat->getBody()));
              $satusehat=SatuSehat::first();

              if(!isset( $satusehat)){
                $satusehat=new SatuSehat();
                $satusehat->token=json_decode($response_auth_satusehat->getBody())->access_token;
                $satusehat->save();
              }else{
                $satusehat->token=json_decode($response_auth_satusehat->getBody())->access_token;
                $satusehat->save();
              }
            }
            
            return redirect()->route($this->redirectTo);
          } else {
            // return redirect()->back()->with('errors', "Akun Anda tidak aktif silahkan hubungi admin kami!");
            return redirect()
              ->back()
              ->withErrors(['username' => "Akun Anda tidak aktif silahkan hubungi admin kami!"])
              ->withInput();
          }
        } else {
          // return redirect()->back()->with('errors', "Password Anda salah silahkan coba lagi!");

          return redirect()
            ->back()
            ->withErrors(['password' => "Password Anda salah silahkan coba lagi!"])
            ->withInput();
        }
      } else {
        // return redirect()->back()->with('errors', "Username Anda salah silahkan coba lagi!");

        return redirect()
          ->back()
          ->withErrors(['username' => "Username Anda salah silahkan coba lagi!"])
          ->withInput();
      }
    }
  }

  public function showLoginForm()
  {
    return view('masterweb::auth.login');
  }

  public function username()
  {
    return 'username';
  }
}