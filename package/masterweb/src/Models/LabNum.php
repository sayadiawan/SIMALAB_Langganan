<?php

namespace Smt\Masterweb\Models;

use Smt\Masterweb\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class LabNum extends Model
{
  use Uuid;
  use SoftDeletes;

  public $incrementing = false;
  protected $table = "tb_lab_num";

  protected $dates = ['deleted_at'];
  protected $primaryKey = 'id_lab_num';

  public function permohonan_uji()
  {
    return $this->belongsTo(PermohonanUji::class, 'permohonan_uji_id', 'id_permohonan_uji')->withDefault();
  }
  
   public function sample()
  {
    return $this->belongsTo(Sample::class, 'sample_id', 'id_sample')->withDefault();
  }

 


  public function lab()
  {
    return $this->belongsTo(Laboratorium::class, 'lab_id', 'id_laboratorium')->withDefault();
  }

}