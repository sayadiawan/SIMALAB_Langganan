<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class SatuSehatLocation extends Model
{
    protected $table = "ms_satusehat_location";
    use SoftDeletes;
    use Uuid;

    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_satusehat_location';
}