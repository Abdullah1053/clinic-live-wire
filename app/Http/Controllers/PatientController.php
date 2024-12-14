<?php

namespace App\Http\Controllers;

use Flash;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use App\DataTables\PatientDataTable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\Factory;
use App\Repositories\PatientRepository;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class PatientController extends AppBaseController
{
    /** @var PatientRepository */
    private $patientRepository;

    public function __construct(PatientRepository $patientRepo)
    {
        $this->patientRepository = $patientRepo;
    }

    /**
     * Display a listing of the Patient.
     *
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        return view('patients.index');
    }

    /**
     * Show the form for creating a new Patient.
     *
     * @return Application|Factory|View
     */
    public function create(): \Illuminate\View\View
    {
        $data = $this->patientRepository->getData();

        return view('patients.create', compact('data'));
    }

    /**
     * Store a newly created Patient in storage.
     *
     * @return Application|Redirector|RedirectResponse
     */
    public function store(CreatePatientRequest $request): RedirectResponse
    {


        // $request = $this->saveCategoryData( $request);

        $input = $request->all();

        $patient = $this->patientRepository->store($input);
        $visit = new Visit();
        $visit->visit_date = now()->format('Y-m-d');
        $visit->doctor_id = 1;
        $visit->patient_id = $patient;
        $visit->description = "Auto Reservation";
        $visit->save();
        Flash::success(__('messages.flash.patient_create'));

        return redirect(route('patients.index'));
    }

    /**
     * Display the specified Patient.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function show(Patient $patient)
    {
        if (getLogInUser()->hasRole('doctor')) {
            $doctor = Appointment::wherePatientId($patient->id)->whereDoctorId(getLogInUser()->doctor->id);
            if (! $doctor->exists()) {
                return redirect()->back();
            }
        }

        if (empty($patient)) {
            Flash::error(__('messages.flash.patient_not_found'));

            return redirect(route('patients.index'));
        }

        $patient = $this->patientRepository->getPatientData($patient);
        $appointmentStatus = Appointment::ALL_STATUS;
        $todayDate = Carbon::now()->format('Y-m-d');
        $data['todayAppointmentCount'] = Appointment::wherePatientId($patient['id'])->where('date', '=',
            $todayDate)->count();
        $data['upcomingAppointmentCount'] = Appointment::wherePatientId($patient['id'])->where('date', '>',
            $todayDate)->count();
        $data['completedAppointmentCount'] = Appointment::wherePatientId($patient['id'])->where('date', '<',
            $todayDate)->count();

        return view('patients.show', compact('patient', 'appointmentStatus', 'data'));
    }

    /**
     * Show the form for editing the specified Patient.
     *
     * @return Application|Factory|View
     */
    public function edit(Patient $patient)
    {
        if (empty($patient)) {
            Flash::error(__('messages.flash.patient_not_found'));

            return redirect(route('patients.index'));
        }
        $data = $this->patientRepository->getData();
        unset($data['patientUniqueId']);

        return view('patients.edit', compact('data', 'patient'));
    }

    /**
     * Update the specified Patient in storage.
     *
     * @return Application|Redirector|RedirectResponse
     */
    public function update(Patient $patient, UpdatePatientRequest $request): RedirectResponse
    {
        // $request = $this->saveCategoryData( $request);
        // dd($request);
        // $input = request()->except(['_method', '_token', 'patient_unique_id']);
        $input = $request->all();

        if (empty($patient)) {
            Flash::error(__('messages.flash.patient_not_found'));

            return redirect(route('patients.index'));
        }

        $patient = $this->patientRepository->update($input, $patient);

        Flash::success(__('messages.flash.patient_update'));

        return redirect(route('patients.index'));
    }

    /**
     * Remove the specified Patient from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $existAppointment = Appointment::wherePatientId($patient->id)
            ->whereNotIn('status', [Appointment::CANCELLED, Appointment::CHECK_OUT])
            ->exists();

        $existVisit = Visit::wherePatientId($patient->id)->exists();

        $transactions = Transaction::whereUserId($patient->user_id)->exists();

        if ($existAppointment || $existVisit || $transactions) {
            return $this->sendError(__('messages.flash.patient_used'));
        }

        try {
            DB::beginTransaction();

            $patient->delete();
            $patient->media()->delete();
            $patient->user()->delete();
            $patient->address()->delete();

            DB::commit();

            return $this->sendSuccess(__('messages.flash.patient_delete'));
        } catch (Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }
    }

    /**
     * @return Application|RedirectResponse|Redirector
     *
     * @throws Exception
     */
    public function patientAppointment(Patient $patient, Request $request)
    {
        if ($request->ajax()) {
            return DataTables::of((new PatientDataTable())->getAppointment($request->only([
                'status', 'patientId', 'filter_date',
            ])))->make(true);
        }

        return redirect(route('patients.index'));
    }

    public function deleteOldPatient()
    {
       $patients =  Patient::pluck('user_id')->toArray();

       User::whereType(User::PATIENT)->whereNotIn('id', $patients)->delete();


    }


    public function saveCategoryData(Request $request):Request
    {
        // Define the category keys with their corresponding date fields
        $categories = [
            'history_of_present_illness' => 'date_history_of_present_illness',
            'drug_history' => 'date_drug_history',
            'family_history' => 'date_family_history',
            'past_history' => 'date_past_history',
            'diagnosis' => 'date_diagnosis',
            'investigation' => 'date_investigation',
            'ray_report' => 'date_ray_report',
            'treatment' => 'date_treatment',
            'review' => 'date_review',
            'notes' => 'date_notes',
        ];

        // Iterate through each category
        foreach ($categories as $category => $dateField) {
            // Get the descriptions and dates for the current category
            $descriptions = $request->input($category, []);
            $dates = $request->input($dateField, []);

            // Combine dates and descriptions into a structured array
            $data = [];
            foreach ($descriptions as $index => $description) {
                $data[] = [
                    'date' => $dates[$index] ?? null,
                    'description' => $description,
                ];
            }

            // Add the combined JSON structure back to the request
            $request->merge([$category => json_encode($data)]);
        }

        // Return the updated request data as a JSON response for debugging (optional)
        return $request;
        // return $request->all();
    }



}
