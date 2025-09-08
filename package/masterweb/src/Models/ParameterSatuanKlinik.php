<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class ParameterSatuanKlinik extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_parameter_satuan_klinik";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_parameter_satuan_klinik';


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function parameterjenisklinik()
    {
        return $this->belongsTo(ParameterJenisKlinik::class, 'parameter_jenis_klinik', 'id_parameter_jenis_klinik')->withDefault();
    }

    public function parametersatuanpaketklinik()
    {
        return $this->hasMany(ParameterSatuanPaketKlinik::class, 'parameter_satuan_klinik', 'id_parameter_satuan_klinik');
    }

    public function parametersubsatuanklinik()
    {
        return $this->hasMany(ParameterSubSatuanKlinik::class, 'parameter_satuan_klinik', 'id_parameter_satuan_klinik');
    }

    public function BakuMutu()
    {
        return $this->belongsTo(BakuMutu::class, 'parameter_satuan_klinik_id', 'id_parameter_satuan_klinik')->withDefault();
    }
}
