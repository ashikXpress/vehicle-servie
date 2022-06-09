<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'salary_process_id', 'employee_id', 'date', 'month', 'year', 'basic_salary',
        'house_rent', 'travel', 'medical', 'tax', 'others_deduct', 'gross_salary'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}
