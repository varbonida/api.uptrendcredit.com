<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectedAccount extends Model
{
    use HasFactory;

	protected $fillable = ['user_id', 'connected_institution_id', 'account_id', 'mask', 'name', 'official_name', 'subtype', 'type'];

    public function connected_institution() {
        return $this->belongsTo('App\Models\ConnectedInstitution');
    }
}
