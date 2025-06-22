<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractorWorker extends Model
{
    //

    protected $table = 'workers';
    protected $fillable = [
        'full_name',
        'nik',
        'birth_place',
        'birth_date',
        'position',
        'photo',
        'ktp_document',
        'induction_card_number',
        'security_card_number',
        'form_b_document',
        'age_justification',
        'domicile',
        'status',
        'jenis_kelamin',
        'project_contractor_id',
    ];

    // worker -> project contractor -> user

    public function project_contractor()
    {
        return $this->belongsTo(ProjectContractor::class);
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            ProjectContractor::class,
            'id', // Foreign key on ProjectContractor table
            'id', // Foreign key on User table
            'project_contractor_id', // Local key on ContractorWorker table
            'contractor_id' // Local key on ProjectContractor table
        );
    }

    public function medical_review()
    {
        return $this->hasOne(MedicalReview::class, 'worker_id');
    }

    public function security_review()
    {
        return $this->hasOne(SecurityReview::class, 'worker_id');
    }
}
