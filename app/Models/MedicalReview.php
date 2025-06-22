<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalReview extends Model
{
    //
    protected $table = 'medical_reviews';

    protected $fillable = [
        'worker_id',
        'reviewed_by',
        'status',
        'notes',
        'reviewed_at',
    ];

    public function worker()
    {
        return $this->belongsTo(ContractorWorker::class, 'worker_id');
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
