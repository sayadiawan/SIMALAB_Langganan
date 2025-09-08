<?php

namespace Smt\Masterweb\Models;

use Illuminate\Database\Eloquent\Model;
use Smt\Masterweb\Traits\Uuid;

class VerificationActivitySample extends Model
{
  use Uuid;

  protected $table = 'tb_verification_activity_samples';
  protected $primaryKey = 'id';
  public $timestamps = false;

}
