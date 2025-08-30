<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmittedDocument extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_template_id',
        'company_id',
        'submitted_by_user_id',
        'data',
        'token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the document template for the submitted document.
     */
    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'document_template_id');
    }

    /**
     * Get the company that owns the submitted document.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who submitted the document.
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }
}
