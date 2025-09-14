<?php

namespace App\Models;

use App\Models\Traits\ForCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentTemplate extends Model
{
    use HasFactory, SoftDeletes, ForCompany;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // 'company_id', // Handled by ForCompany trait
        'name',
        'description',
        'content',
        'fields',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fields' => 'array',
    ];

    /**
     * Get the company that owns the document template.
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the submitted documents for the template.
     */
    public function submittedDocuments(): HasMany
    {
        return $this->hasMany(SubmittedDocument::class);
    }
}
