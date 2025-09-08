<?php

namespace Smt\Masterweb\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Smt\Masterweb\Models\Petugas;

class AdmPetugasController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $listPetugas = Petugas::query()->select(['id_petugas', 'nama', 'nik', 'password'])->get();
    return view("masterweb::module.admin.petugas.index", compact("listPetugas"));
  }

  public function edit($id)
  {
    try {
      $petugas = Petugas::query()->where("id_petugas", $id)->first();

      if (!$petugas){
        return redirect("elits-petugas")->with("error", "Petugas not found");
      }

      return view("masterweb::module.admin.petugas.edit", compact("petugas"));

    }catch (\Exception $exception){
      return redirect("elits-petugas")->with("error", "An error occurred while getting Petugas");
    }
  }

  public function update($id, Request $request)
  {
    $validated = $request->validate([
      'nama' => 'required|string|max:255',
      'nik' => [
        'required',
        'string',
        'digits:16',
        Rule::unique('ms_petugas', 'nik')->ignore($id, 'id_petugas'),
      ],
      'password' => 'required|string',
    ]);

    try {
      $petugas = Petugas::query()->where("id_petugas", $id)->first();

      if (!$petugas) {
        return redirect("elits-petugas")->with("error", "Petugas not found");
      }

      $petugas->nama = $validated['nama'];
      $petugas->nik = $validated['nik'];
      $petugas->password = $validated['password'];
      $petugas->save();

      return redirect("elits-petugas")->with("success", "Petugas successfully updated");

    } catch (\Exception $exception) {
      return redirect("elits-petugas")->with("error", "An error occurred while updating Petugas");
    }
  }

  public function create()
  {
    return view("masterweb::module.admin.petugas.add");
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'nama' => 'required|string|max:255',
      'nik' => 'required|string|digits:16|unique:ms_petugas,nik',
      'password' => 'required|string',
    ]);

    try {
      $petugas = new Petugas();
      $petugas->nama = $validated['nama'];
      $petugas->nik = $validated['nik'];
      $petugas->password = $validated['password'];
      $petugas->save();

      return redirect("elits-petugas")->with("success", "Petugas successfully created");
    }catch (\Exception $exception){
      return redirect("elits-petugas")->with("error", "An error occurred while creating Petugas");
    }
  }

  public function destroy($id)
  {
    try {
      $petugas = Petugas::find($id);
      if (!$petugas) {
        return redirect("elits-petugas")->with("error", "Petugas not found");
      }
      $petugas->delete();

      return redirect("elits-petugas")->with("success", "Petugas successfully deleted");
    } catch (\Exception $exception) {
      return redirect("elits-petugas")->with("error", "An error occurred while deleting Petugas");
    }
  }
}
