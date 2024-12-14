<?php

namespace App\Repositories;

use App\Models\Country;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use Carbon\Carbon;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * Class PatientRepository
 *
 * @version July 29, 2021, 11:37 am UTC
 */
class PatientRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [

    ];

    /**
     * Return searchable fields
     */
    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Patient::class;
    }

    public function getData(): array
    {
        $data['patientUniqueId'] = mb_strtoupper(Patient::generatePatientUniqueId());
        $data['countries'] = Country::toBase()->pluck('name', 'id');
        $data['bloodGroupList'] = Patient::BLOOD_GROUP_ARRAY;

        return $data;
    }

    public function store($input)
    {
        try {
            DB::beginTransaction();
            $addressInputArray = Arr::only($input,
                ['address1', 'address2', 'city_id', 'state_id', 'country_id', 'postal_code']);
            $input['patient_unique_id'] = Str::upper($input['patient_unique_id']);
            $input['email'] = setEmailLowerCase($input['email']  ??  $input['first_name'].'@gmail.com'    );
            $patientArray = Arr::only($input, ['patient_unique_id','age']);
            $input['type'] = User::PATIENT;
            $input['language'] = Setting::where('key','language')->get()->toArray()[0]['value'];
            $input['password'] = Hash::make($input['password'] ?? '123456');
            $user = User::create($input);

            $patient = $user->patient()->create($patientArray);
            $address = $patient->address()->create($addressInputArray);
            $user->assignRole('patient');
            if (isset($input['profile']) && ! empty($input['profile'])) {
                $patient->addMedia($input['profile'])->toMediaCollection(Patient::PROFILE, config('app.media_disc'));
            }
            // $user->sendEmailVerificationNotification();

            $user->update([
                'email_verified_at' => Carbon::now(),
            ]);
            DB::commit();

            return $patient->id;
        } catch (\Exception $e) {
            \Log::info($e);
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    public function update($input, $patient): bool
    {
        try {
            DB::beginTransaction();

            $addressInputArray = Arr::only($input,
                ['address1', 'address2', 'city_id', 'state_id', 'country_id', 'postal_code']);
            $input['type'] = User::PATIENT;
            // $input['email'] = setEmailLowerCase($input['email']);
            /** @var Patient $patient */
            $patient->user()->update(Arr::except($input, [
                'address1', 'address2', 'city_id', 'state_id', 'country_id', 'postal_code', 'patient_unique_id',
                'avatar_remove',
                'profile', 'is_edit', 'edit_patient_country_id', 'edit_patient_state_id', 'edit_patient_city_id',
                'backgroundImg','date_history_of_present_illness','date_drug_history','date_family_history','date_past_history',
                'date_diagnosis','date_investigation','date_ray_report','date_treatment','date_review','date_notes'
                ,'history_of_present_illness','drug_history','family_history','past_history',
                'diagnosis','investigation','ray_report','treatment','review','notes','_method', '_token', 'patient_unique_id'
            ]));
            $user = User::where('id',$patient->user_id)->first();
            $user->history_of_present_illness = $input['history_of_present_illness'];
            $user->drug_history = $input['drug_history'];
            $user->family_history = $input['family_history'];
            $user->past_history = $input['past_history'];
            $user->diagnosis = $input['diagnosis'];
            $user->investigation = $input['investigation'];
            $user->ray_report = $input['ray_report'];
            $user->treatment = $input['treatment'];
            $user->review = $input['review'];
            $user->notes = $input['notes'];
            $user->save();

            if(isset($patient->address)){
                $patient->address()->update($addressInputArray);
            }else{
                $patient->address()->create($addressInputArray);
            }

            if (isset($input['profile']) && ! empty($input['profile'])) {
                $patient->clearMediaCollection(Patient::PROFILE);
                $patient->media()->delete();
                $patient->addMedia($input['profile'])->toMediaCollection(Patient::PROFILE, config('app.media_disc'));
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getPatientData($input)
    {
        $patient = Patient::with(['user.address', 'appointments', 'address'])->findOrFail($input['id']);

        return $patient;
    }



}
