<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Smt\Masterweb\Traits\Uuid;

class Petugas extends Model
{
    use Uuid;

    protected $table = 'ms_petugas';
    protected $primaryKey = 'id_petugas';
    public $timestamps = false;
}
