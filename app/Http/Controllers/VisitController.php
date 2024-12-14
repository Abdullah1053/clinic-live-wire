<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateVisitPrescriptionRequest;
use App\Http\Requests\CreateVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Visit;
use App\Models\VisitDiagnosis;
use App\Models\VisitDrugHistory;
use App\Models\VisitFamilyHistory;
use App\Models\VisitNote;
use App\Models\VisitObservation;
use App\Models\VisitPrescription;
use App\Models\VisitProblem;

use App\Models\VisitHistoryPresentIllness;
use App\Models\VisitInvestigation;
use App\Models\VisitPastHistory;
use App\Models\VisitRayReport;
use App\Models\VisitReview;
use App\Models\VisitTreatment;
use App\Repositories\VisitRepository;
use Flash;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class VisitController extends AppBaseController
{
    /** @var VisitRepository */
    private $visitRepository;

    public function __construct(VisitRepository $visitRepo)
    {
        $this->visitRepository = $visitRepo;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(): \Illuminate\View\View
    {
        return view('visits.index');
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): \Illuminate\View\View
    {
        $data = $this->visitRepository->getData();

        return view('visits.create', compact('data'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function store(CreateVisitRequest $request): RedirectResponse
    {
        dd($request->all());
        $input = $request->all();
        $this->visitRepository->create($input);

        Flash::success(__('messages.flash.visit_create'));

        if (getLoginUser()->hasRole('doctor')) {
            return redirect(route('doctors.visits.index'));
        }

        return redirect(route('visits.index'));
    }

    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function show($id)
    {
        if (getLogInUser()->hasRole('doctor')) {
            $doctor = Visit::whereId($id)->whereDoctorId(getLogInUser()->doctor->id);
            if (! $doctor->exists()) {
                return redirect()->back();
            }
        }

        $visit = $this->visitRepository->getShowData($id);
        // dd($visit);
        return view('visits.show', compact('visit'));
    }

    /**
     * @return Application|Factory|View
     */
    public function edit(Visit $visit)
    {
        if (getLogInUser()->hasRole('doctor')) {
            $doctor = Visit::whereId($visit->id)->whereDoctorId(getLogInUser()->doctor->id);
            if (! $doctor->exists()) {
                return redirect(route('doctors.visits.index'));
            }
        }

        $data = $this->visitRepository->getData();

        return view('visits.edit', compact('data', 'visit'));
    }

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Visit $visit, UpdateVisitRequest $request): RedirectResponse
    {
        $input = $request->all();
        $visit->update($input);

        Flash::success(__('messages.flash.visit_update'));

        if (getLoginUser()->hasRole('doctor')) {
            return redirect(route('doctors.visits.index'));
        }

        return redirect(route('visits.index'));
    }

    public function destroy(Visit $visit): mixed
    {
        if (getLogInUser()->hasrole('doctor')) {
            if ($visit->doctor_id !== getLogInUser()->doctor->id) {
                return $this->sendError(__('messages.common.not_allow__assess_record'));
            }
        }
        $visit->delete();

        return $this->sendSuccess(__('messages.flash.visit_delete'));
    }

    /**
     * @return mixed
     */
    public function addProblem(Request $request)
    {
        $input = $request->all();
        $problem = VisitProblem::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitProblem::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    /**
     * @return mixed
     */
    public function deleteProblem($id)
    {
        $problem = VisitProblem::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitProblem::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }

    /**
     * @return mixed
     */
    public function addObservation(Request $request)
    {
        $input = $request->all();
        $observation = VisitObservation::create([
            'observation_name' => $input['observation_name'],
            'visit_id' => $input['visit_id'],
        ]);
        $observationData = VisitObservation::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($observationData, __('messages.flash.observation_added'));
    }

    /**
     * @return mixed
     */
    public function deleteObservation($id)
    {
        $observation = VisitObservation::findOrFail($id);
        $visitId = $observation->visit_id;
        $observation->delete();
        $observationData = VisitObservation::whereVisitId($visitId)->get();

        return $this->sendResponse($observationData, __('messages.flash.observation_delete'));
    }

    /**
     * @return mixed
     */
    public function addNote(Request $request)
    {
        $input = $request->all();
        $note = VisitNote::create(['note_name' => $input['note_name'], 'visit_id' => $input['visit_id']]);
        $noteData = VisitNote::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($noteData, __('messages.flash.note_added'));
    }

    /**
     * @return mixed
     */
    public function deleteNote($id)
    {
        $note = VisitNote::findOrFail($id);
        $visitId = $note->visit_id;
        $note->delete();
        $noteData = VisitNote::whereVisitId($visitId)->get();

        return $this->sendResponse($noteData, __('messages.flash.note_delete'));
    }

    /**
     * @return mixed
     */
    public function addPrescription(CreateVisitPrescriptionRequest $request)
    {
        $input = $request->all();
        if (! empty($input['prescription_id'])) {
            $prescription = VisitPrescription::findOrFail($input['prescription_id']);
            $prescription->update($input);
            $message = __('messages.flash.visit_prescription_update');
        } else {
            VisitPrescription::create($input);
            $message = __('messages.flash.visit_prescription_added');
        }

        $visitPrescriptions = VisitPrescription::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($visitPrescriptions, $message);
    }

    /**
     * @return mixed
     */
    public function editPrescription($id)
    {
        $prescription = VisitPrescription::findOrFail($id);

        return $this->sendResponse($prescription, __('messages.flash.prescription_retrieved'));
    }

    /**
     * @return mixed
     */
    public function deletePrescription($id)
    {
        $prescription = VisitPrescription::findOrFail($id);
        $visitId = $prescription->visit_id;
        $prescription->delete();
        $prescriptionData = VisitPrescription::whereVisitId($visitId)->get();

        return $this->sendResponse($prescriptionData, __('messages.flash.prescription_delete'));
    }

    // Generic Add Method
    public function add_history_of_present_illness(Request $request)
    {
        $input = $request->all();
        $problem = VisitHistoryPresentIllness::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitHistoryPresentIllness::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_history_of_present_illness($id)
    {

        $problem = VisitHistoryPresentIllness::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitHistoryPresentIllness::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }



    public function add_drug_history(Request $request)
    {

        $input = $request->all();
        $problem = VisitDrugHistory::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitDrugHistory::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_drug_history($id)
    {

        $problem = VisitDrugHistory::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitDrugHistory::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_family_history(Request $request)
    {

        $input = $request->all();
        $problem = VisitFamilyHistory::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitFamilyHistory::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_family_history($id)
    {
        $problem = VisitFamilyHistory::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitFamilyHistory::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_past_history(Request $request)
    {

        $input = $request->all();
        $problem = VisitPastHistory::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitPastHistory::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_past_history($id)
    {

        $problem = VisitPastHistory::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitPastHistory::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }

    public function add_diagnosis(Request $request,)
    {

        $input = $request->all();
        $problem = VisitDiagnosis::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitDiagnosis::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_diagnosis($id)
    {

        $problem = VisitDiagnosis::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitDiagnosis::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_investigation(Request $request)
    {
        $input = $request->all();
        $problem = VisitInvestigation::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitInvestigation::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_investigation($id)
    {
        $problem = VisitInvestigation::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitInvestigation::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_ray_report(Request $request)
    {
        $input = $request->all();
        $problem = VisitRayReport::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitRayReport::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_ray_report($id)
    {
        $problem = VisitRayReport::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitRayReport::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_treatment(Request $request)
    {
        $input = $request->all();
        $problem = VisitTreatment::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitTreatment::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_treatment($id)
    {
        $problem = VisitTreatment::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitTreatment::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


    public function add_review(Request $request)
    {
        $input = $request->all();
        $problem = VisitReview::create(['problem_name' => $input['problem_name'], 'visit_id' => $input['visit_id']]);
        $problemData = VisitReview::whereVisitId($input['visit_id'])->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_added'));
    }

    // Generic Delete Method
    public function delete_review($id, $fieldModel, $fieldName)
    {
        $problem = VisitReview::findOrFail($id);
        $visitId = $problem->visit_id;
        $problem->delete();
        $problemData = VisitReview::whereVisitId($visitId)->get();

        return $this->sendResponse($problemData, __('messages.flash.problem_delete'));
    }


}
