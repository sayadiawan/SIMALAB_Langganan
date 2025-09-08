<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class Pasien extends Model
{
  use SoftDeletes;
  use Uuid;

  protected $table = "ms_pasien";
  protected $dates = ['deleted_at'];
  public $incrementing = false;
  protected $primaryKey = 'id_pasien';

  protected $fillable = [
    'nik_pasien',
    'nourut_pasien',
    'no_rekammedis_pasien',
    'id_pasien_satu_sehat',
    'nama_pasien',
    'gender_pasien',
    'tgllahir_pasien',
    'umurtahun_pasien',
    'umurbulan_pasien',
    'umurhari_pasien',
    'alamat_pasien',
    'phone_pasien'
  ];


  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */

  public function permohonanujiklinik()
  {
    return $this->hasMany(PermohonanUjiKlinik::class, 'pasien_permohonan_uji_klinik', 'id_pasien');
  }
}
