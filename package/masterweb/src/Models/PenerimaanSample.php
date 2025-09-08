<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class PenerimaanSample extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $table = "tb_sample_penerimaan";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_sample_penerimaan';

     
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
   
   
}
