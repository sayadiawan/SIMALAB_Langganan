<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class Sample extends Model
{
  use SoftDeletes;
  use Uuid;

  protected $table = "tb_samples";
  protected $dates = ['deleted_at'];
  public $incrementing = false;
  protected $primaryKey = 'id_samples';

  // function user(){
  //     return $this->hasMany("App\User",'level','id');
  // }

  public function sampletype()
  {
    return $this->belongsTo(SampleType::class, 'typesample_samples', 'id_sample_type')->where('deleted_at', NULL);
  }


  public function jenis_makanan()
  {
    return $this->belongsTo(JenisMakanan::class, 'jenis_makanan_id', 'id_jenis_makanan')->where('deleted_at', NULL);
  }

  public function permohonanuji()
  {
    return $this->hasOne(PermohonanUji::class, 'id_permohonan_uji', 'permohonan_uji_id');
  }

  public function samplemethod()
  {
    return $this->hasMany(SampleMethod::class, 'sample_id', 'id_samples');
  }

  public function labnum()
  {
    return $this->hasMany(LabNum::class, 'sample_id', 'id_samples');
  }

  public function labnumByLab($id_lab)
  {
    return $this->hasMany(LabNum::class, 'sample_id', 'id_samples')->where('lab_id', $id_lab);
  }

 
  public function pengesahanhasil()
  {
    return $this->belongsTo(PengesahanHasil::class, 'id_samples', 'sample_id');
  }

  public function sampleresult()
  {
    return $this->hasMany(SampleResult::class, 'sample_id', 'id_samples');
  }
}