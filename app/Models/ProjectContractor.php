<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectContractor extends Model
{
    //
    protected $table = 'project_contractors';
    protected $fillable = [
        'project_name',
        'memo_number',
        'memo_document',
        'start_date',
        'end_date',
        'contractor_id'
    ];
    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function workers()
    {
        return $this->hasMany(ContractorWorker::class, 'project_contractor_id');
    }
}
