<?php

namespace Smt\Masterweb\Http\Controllers;

use App\Http\Controllers\Controller;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use \Smt\Masterweb\Models\User;
use \Smt\Masterweb\Models\Packet;
use \Smt\Masterweb\Models\JenisMakanan;
use \Smt\Masterweb\Models\Method;
use \Smt\Masterweb\Models\Laboratorium;
use \Smt\Masterweb\Models\PacketDetail;
use \Smt\Masterweb\Models\SampleType;

use \Smt\Masterweb\Models\LaboratoriumPacket;





use Carbon\Carbon;


class LaboratoriumPaketManagement extends Controller
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
    $user = Auth()->user();
    $level = $user->getlevel->level;

    $packets=Packet::all();

    return view('masterweb::module.admin.laboratorium.packet.list', compact('packets'));
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
    $level = $user->getlevel->level;

    $sampletypes = SampleType::all();

    $all_jenis_makanan = JenisMakanan::all();


    return view('masterweb::module.admin.laboratorium.packet.add', compact('all_jenis_makanan', 'sampletypes'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {

    $validated = $request->validate([
      'name_packet' => ['required', 'max:255'],
      'methodAttributes' => ['required'],
      'sample_type_id' => ['required'],
      'price_bahan_packet' => ['required'],
      'price_jasa_packet' => ['required'],
      'price_sarana_packet' => ['required'],
      'price_total_packet' => ['required'],

    ]);

    $packet = new Packet;
    //uuid
    $uuid4 = Uuid::uuid4();

    $packet->id_packet = $uuid4->toString();
    $packet->name_packet = $request->post('name_packet');
    $packet->sample_type_id = $request->post('sample_type_id');
    $jenis_makanan_id = $request->post('jenis_makanan_id');
    if (isset($jenis_makanan_id)) {
      $packet->jenis_makanan_id = $request->post('jenis_makanan_id');
    } else {
      $packet->jenis_makanan_id = NULL;
    }
    $packet->price_bahan_packet = $request->post('price_bahan_packet');
    $packet->price_jasa_packet = $request->post('price_jasa_packet');
    $packet->price_total_packet = $request->post('price_total_packet');
    $packet->price_sarana_packet = $request->post('price_sarana_packet');

    $packet->save();
    $data = $request->all();





    if (isset($data["methodAttributes"])) {
      for ($i = 0; $i < count($data["methodAttributes"]); $i++) {
        $packetdetail = new PacketDetail;
        $packetdetail->id_packet_detail = Uuid::uuid4();
        $packetdetail->packet_id = $packet->id_packet;
        $packetdetail->method_id = $data["methodAttributes"][$i];
        $packetdetail->save();
      }
    }

    // if(isset ($data["laboratoriumAttributes"])){
    //     for($i = 0; $i < count($data["laboratoriumAttributes"]);$i++ ) {
    //         $packetdetail = new LaboratoriumPacket;
    //         $packetdetail->id_laboratorium_packet= Uuid::uuid4();
    //         $packetdetail->packet_id= $packet->id_packet;
    //         $packetdetail->laboratorium_id=$data["laboratoriumAttributes"][$i];
    //         $packetdetail->save();
    //     }

    // }



    // return redirect()->route('elits-containers.index')->with(['status'=>'Container succesfully inserted']);


    return response()->json(['message' => 'Successfully add']);







    //return redirect()->route('module.admin.users.index')->with('status', 'User succesfully inserted');
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
    //get auth user

    $user = Auth()->user();
    $level = $user->getlevel->level;

    $packet = Packet::findOrFail($id);
    $packet_details = PacketDetail::where('packet_id', $id)->join('ms_method', function ($join) {
      $join->on('ms_method.id_method', '=', 'ms_packet_detail.method_id')
        ->whereNull('ms_method.deleted_at')
        ->whereNull('ms_packet_detail.deleted_at');
    })
      ->get();

    $sampletypes = SampleType::all();
    $all_jenis_makanan = JenisMakanan::all();



    return view('masterweb::module.admin.laboratorium.packet.edit', compact('all_jenis_makanan', 'sampletypes', 'packet', 'packet_details', 'id'));
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

    $validated = $request->validate([
      // 'name_packet' => ['required', 'max:255'],
      // // 'methodAttributes' => ['required'],
      // 'price_packet' => ['required'],
      'name_packet' => ['required', 'max:255'],
      'methodAttributes' => ['required'],
      'sample_type_id' => ['required'],
      'price_bahan_packet' => ['required'],
      'price_jasa_packet' => ['required'],
      'price_sarana_packet' => ['required'],
      'price_total_packet' => ['required'],

    ]);

    $packet = Packet::findOrFail($id);
    $packet->name_packet = $request->post('name_packet');
    $packet->sample_type_id = $request->post('sample_type_id');
    $packet->price_bahan_packet = $request->post('price_bahan_packet');
    $packet->price_jasa_packet = $request->post('price_jasa_packet');
    $packet->price_total_packet = $request->post('price_total_packet');
    $packet->price_sarana_packet = $request->post('price_sarana_packet');

    $jenis_makanan_id = $request->post('jenis_makanan_id');
    // dd($jenis_makanan_id);
    if (isset($jenis_makanan_id)) {
      $packet->jenis_makanan_id = $request->post('jenis_makanan_id');
    } else {
      $packet->jenis_makanan_id = NULL;
    }
    $packet->save();

    PacketDetail::where('packet_id', $id)->delete();

    $data = $request->all();

    if (isset($data["methodAttributes"])) {
      for ($i = 0; $i < count($data["methodAttributes"]); $i++) {
        $packetdetail = new PacketDetail;
        $packetdetail->id_packet_detail = Uuid::uuid4();
        $packetdetail->packet_id = $packet->id_packet;
        $packetdetail->method_id = $data["methodAttributes"][$i];
        $packetdetail->save();
      }
    }



    // if(isset ($data["laboratoriumAttributes"])){
    //     $laboratorium_packets= LaboratoriumPacket::where('packet_id',$id);
    //     if(isset($laboratorium_packets)){
    //         $laboratorium_packets->delete();
    //     }
    //     for($i = 0; $i < count($data["laboratoriumAttributes"]);$i++ ) {
    //         $packetdetail = new LaboratoriumPacket;
    //         $packetdetail->id_laboratorium_packet= Uuid::uuid4();
    //         $packetdetail->packet_id= $packet->id_packet;
    //         $packetdetail->laboratorium_id=$data["laboratoriumAttributes"][$i];
    //         $packetdetail->save();
    //     }

    // }


    return response()->json(['message' => 'Successfully edit']);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $packet = Packet::findOrFail($id);
    $packet->delete();
    PacketDetail::where('packet_id', $id)->delete();

    return redirect()->route('elits-packet.index', [$id])->with('status', 'Paket succesfully updated');
  }
}