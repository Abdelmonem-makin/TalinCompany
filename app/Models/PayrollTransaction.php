<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'amount',
        'date',
        'description',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
