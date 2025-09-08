<?php

namespace Smt\Masterweb\Http\Controllers;

use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\Method;
use \Smt\Masterweb\Models\Laboratorium;
use \Smt\Masterweb\Models\LaboratoriumMethod;
use \Smt\Masterweb\Models\Unit;





class LaboratoriumMethodManagement extends Controller
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
    //get auth user
    //$users = User::where('level','01f62b38-fce5-43bf-9088-9d4a33a496da');
    //get auth user
    $user = Auth()->user();
    $methods = Method::orderBy('name_method')->get();

    return view('masterweb::module.admin.laboratorium.method.list', compact('user', 'methods'));
  }

  public function load($id, $id_samples)
  {
    //get auth user
    //$users = User::where('level','01f62b38-fce5-43bf-9088-9d4a33a496da');
    //get auth user
    $user = Auth()->user();
    $method = Method::where('id_method', $id)->first();
    $model_name = substr('Smt\Masterweb\Models\Module\ ', 0, 28) . $method->model_method;
    $model_return_name = substr('Smt\Masterweb\Models\Result\ ', 0, 28) . $method->model_method . "Result";



    $form = $model_name::where('id_samples', $id_samples)->where('id_method', $id)->first();

    $form_result = null;

    if ($form != null) {
      $form_result = $model_return_name::where('id_' . $method->model_method, $form->id)->orderBy('order', 'asc')->get();
    }




    return view('masterweb::module.admin.laboratorium.method.module.' . $method->module_method, compact('form', 'form_result', 'id', 'method'));
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

    $all_laboratorium = Laboratorium::all();

    $units = Unit::all();



    return view('masterweb::module.admin.laboratorium.method.add', compact('user', 'all_laboratorium', 'units'));
    //get all menu public
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $data = $request->all();

    // print_r($data);



    $method = new Method;
    //uuid
    $uuid4 = Uuid::uuid4();

    $method->id_method = $uuid4->toString();
    $method->params_method = $request->post('params_method');
    $method->name_method = $request->post('name_method');
    $method->price_bahan = $request->post('price_bahan');
    $method->price_sarana = $request->post('price_sarana');
    $method->berhubungan_kesehatan = $request->post('berhubungan_kesehatan');
    $method->jenis_parameter_kimia = $request->post('jenis_parameter_kimia');
    $method->price_jasa = $request->post('price_jasa');
    $method->price_total_method = $request->post('price_total_method');
    $method->unit_method = $request->post('unit');
    $method->name_report_method = $request->post('name_report_method');
    $method->id_pdam_method = $request->post('id_pdam_method');
    $method->is_ready = $request->post('is_ready');


    //  $method->kadar_diperbolehkan_method = $request->post('kadar_diperbolehkan_method');
    //  $method->module_method = $request->post('module_method');
    //  $method->model_method = $request->post('model_method');
    $method->save();


    if (isset($data["laboratoriumAttributes"])) {
      for ($i = 0; $i < count($data["laboratoriumAttributes"]); $i++) {
        $methoddetail = new LaboratoriumMethod;
        $methoddetail->id_laboratorium_method = Uuid::uuid4();
        $methoddetail->method_id = $method->id_method;
        $methoddetail->laboratorium_id = $data["laboratoriumAttributes"][$i];
        $methoddetail->save();
      }
    }

    return redirect()->route('elits-methods.index')->with(['status' => 'Method succesfully inserted']);

    //return redirect()->route('user-client-management.index',[$request->get('client_id')])->with(['status'=>'User succesfully inserted','user'=>$user]);

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
    $auth = Auth()->user();
    $method = Method::where('id_method', $id)->first();

    // dd($method);
    $all_laboratorium = Laboratorium::all();
    $units = Unit::all();


    $method_laboratorium_details = LaboratoriumMethod::where('method_id', $id)->get();

    return view('masterweb::module.admin.laboratorium.method.edit', compact('auth', 'method', 'id', 'units', 'all_laboratorium', 'method_laboratorium_details'));
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


    // print_r($data);



    $data = $request->all();
    $method = Method::find($id);
    $method->params_method = $request->post('params_method');
    $method->name_method = $request->post('name_method');
    $method->price_bahan = $request->post('price_bahan');
    $method->price_sarana = $request->post('price_sarana');
    $method->berhubungan_kesehatan = $request->post('berhubungan_kesehatan');
    $method->jenis_parameter_kimia = $request->post('jenis_parameter_kimia');
    $method->price_jasa = $request->post('price_jasa');
    $method->price_total_method = $request->post('price_total_method');
    $method->unit_method = $request->post('unit');
    $method->id_pdam_method = $request->post('id_pdam_method');
    $method->is_ready = $request->post('is_ready');
    //  $method->kadar_diperbolehkan_method = $request->post('kadar_diperbolehkan_method');
    //  $method->module_method = $request->post('module_method');
    //  $method->model_method = $request->post('model_method');
    $method->save();



    if (isset($data["laboratoriumAttributes"])) {
      $laboratorium_methods = LaboratoriumMethod::where('method_id', $id);
      if (isset($laboratorium_methods)) {
        $laboratorium_methods->delete();
      }
      for ($i = 0; $i < count($data["laboratoriumAttributes"]); $i++) {
        $packetdetail = new LaboratoriumMethod;
        $packetdetail->id_laboratorium_method = Uuid::uuid4();
        $packetdetail->method_id = $method->id_method;
        $packetdetail->laboratorium_id = $data["laboratoriumAttributes"][$i];
        $packetdetail->save();
      }
    }

    return redirect()->route('elits-methods.index')->with(['status' => 'Method succesfully updated']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $method = Method::findOrFail($id);
    $method->delete();
    return redirect()->route('elits-methods.index')->with('status', 'Data berhasil dihapus');
  }
}
