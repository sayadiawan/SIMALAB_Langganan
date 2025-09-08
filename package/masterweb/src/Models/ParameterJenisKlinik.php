<?php

namespace Smt\Masterweb\Models;

use Smt\Masterweb\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Models\ParameterPaketKlinik;

class ParameterJenisKlinik extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_parameter_jenis_klinik";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_parameter_jenis_klinik';


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function parametersatuanklinik()
    {
        return $this->hasMany(ParameterSatuanPaketKlinik::class, 'parameter_paket_jenis_klinik', 'id_parameter_jenis_klinik');
    }

    public function parameterpaketklinik()
    {
        return $this->hasMany(ParameterPaketKlinik::class, 'parameter_jenis_klinik', 'id_parameter_jenis_klinik');
    }

    public function pakets()
    {
        return $this->hasManyThrough(
            ParameterPaketKlinik::class,
            ParameterPaketJenisKlinik::class,
            'parameter_jenis_klinik_id', // Foreign key on ParameterPaketJenisKlinik table...
            'id_parameter_paket_klinik', // Foreign key on ParameterPaketKlinik table...
            'id_parameter_jenis_klinik', // Local key on ParameterJenisKlinik table...
            'parameter_paket_klinik_id'  // Local key on ParameterPaketJenisKlinik table...
        );
    }
}
