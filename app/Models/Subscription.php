<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeIsNotEnded($query)
    {
        $date = Carbon::now('-6');
        $query->where('is_ended', 0)->where('expire_date','<',$date);
    }
}
