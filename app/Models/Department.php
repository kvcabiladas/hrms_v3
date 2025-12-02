<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $guarded = [];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function designations()
    {
        return $this->hasMany(Designation::class);
    }

    public function jobListings()
    {
        return $this->hasMany(JobListing::class);
    }
}