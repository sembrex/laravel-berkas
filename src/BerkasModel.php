<?php

namespace Karogis\Berkas;

use Illuminate\Database\Eloquent\Model;

class BerkasModel extends Model
{
    protected $table = 'berkas';

    protected $guarded = [];

    public function user()
    {
    	return $this->belongsTo(config('berkas.user_model'), 'user_id');
    }
}
