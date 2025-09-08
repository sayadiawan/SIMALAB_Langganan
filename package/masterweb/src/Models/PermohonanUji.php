<?php

namespace Smt\Masterweb\Models;

use Smt\Masterweb\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanUji extends Model
{
  use SoftDeletes;
  use Uuid;

  protected $table = "tb_permohonan_uji";
  protected $dates = ['deleted_at'];
  public $incrementing = false;
  protected $primaryKey = 'id_permohonan_uji';


  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  /**
   * Get the permohonanuji associated with the PermohonanUji
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function customer()
  {
    return $this->hasOne(Customer::class, 'id_customer', 'customer_id');
  }

  public function sample()
  {
    return $this->hasMany(Sample::class, 'permohonan_uji_id', 'id_permohonan_uji');
  }

  public function samples()
  {
    return $this->hasMany(Sample::class, 'permohonan_uji_id', 'id_permohonan_uji');
  }
}