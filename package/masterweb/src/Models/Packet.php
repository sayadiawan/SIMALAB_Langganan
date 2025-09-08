<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Smt\Masterweb\Traits\Uuid;

class Packet extends Model
{
    use SoftDeletes;
    use Uuid;

    protected $table = "ms_packet";
    protected $dates = ['deleted_at'];
    public $incrementing = false;
    protected $primaryKey = 'id_packet';

     
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    public function packet_detail()
     {
        return $this->hasMany(PacketDetail::class, 'packet_id', 'id_packet');
     }
   
}