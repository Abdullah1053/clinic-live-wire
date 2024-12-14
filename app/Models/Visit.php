<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Encounter
 *
 * @version September 3, 2021, 7:09 am UTC
 *
 * @property string $doctor
 * @property string $patient
 * @property string $description
 * @property int $id
 * @property int $doctor_id
 * @property int $patient_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static \Database\Factories\EncounterFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereDoctorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereEncounterDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit wherePatientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereUpdatedAt($value)
 *
 * @mixin Model
 *
 * @property string $visit_date
 * @property-read Doctor $visitDoctor
 * @property-read \App\Models\Patient $visitPatient
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Visit whereVisitDate($value)
 */
class Visit extends Model
{
    use HasFactory;

    public $table = 'visits';

    public $fillable = [
        'visit_date',
        'doctor_id',
        'patient_id',
        'description',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'visit_date' => 'string',
        'doctor' => 'integer',
        'patient' => 'integer',
        'description' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'visit_date' => 'required',
        'doctor_id' => 'required',
        'patient_id' => 'required',
    ];

    public function visitDoctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function visitPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function problems(): HasMany
    {
        return $this->hasMany(VisitProblem::class, 'visit_id');
    }

    public function observations(): HasMany
    {
        return $this->hasMany(VisitObservation::class, 'visit_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(VisitNote::class, 'visit_id');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(VisitPrescription::class, 'visit_id');
    }

    public function diagnosis(): HasMany
    {
        return $this->hasMany(VisitDiagnosis::class, 'visit_id');
    }


    public function family_history(): HasMany
    {
        return $this->hasMany(VisitFamilyHistory::class, 'visit_id');
    }


    public function history_of_present_illness(): HasMany
    {
        return $this->hasMany(VisitHistoryPresentIllness::class, 'visit_id');
    }

    public function investigation(): HasMany
    {
        return $this->hasMany(VisitInvestigation::class, 'visit_id');
    }


    public function drug_history(): HasMany
    {
        return $this->hasMany(VisitDrugHistory::class, 'visit_id');
    }


    public function past_history(): HasMany
    {
        return $this->hasMany(VisitPastHistory::class, 'visit_id');
    }


    public function ray_report(): HasMany
    {
        return $this->hasMany(VisitRayReport::class, 'visit_id');
    }


    public function review(): HasMany
    {
        return $this->hasMany(VisitReview::class, 'visit_id');
    }

    public function treatment(): HasMany
    {
        return $this->hasMany(VisitTreatment::class, 'visit_id');
    }
}
