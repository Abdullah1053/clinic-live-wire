<style>
    span {
        text-wrap: wrap;
    }
</style>
@php
    $visitHistory = \App\Models\Visit::where('patient_id', $patient->id)->get();
    //  dd($visitHistory->problems);
@endphp


<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>Problems</h3>

    @foreach ($visitHistory as $item)
        @if (!empty($visit))
            @foreach ($item->problem as $problem)
                <li>
                    <strong>Date:</strong> {{ $item->created_at }}<br>
                    <strong>Description:</strong> {{ $item->problem_name }}
                </li>
            @endforeach

        @endif
    @endforeach
</div>

<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>observations </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->observations as $observations)
            <li>
                <strong>Date:</strong> {{ $observations->created_at }}<br>
                <strong>Description:</strong> {{ $observations->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>

<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>observations </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->notes as $notes)
            <li>
                <strong>Date:</strong> {{ $observations->created_at }}<br>
                <strong>Description:</strong> {{ $observations->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>




<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>diagnosis </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->diagnosis as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>


<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>family history </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->family_history as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>

<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>history of present illness </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->history_of_present_illness as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>


<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>investigation </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->investigation as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>


<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>drug history </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->drug_history as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>


<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>past history </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->past_history as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>

<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>ray report </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->ray_report as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>



<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>review </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->review as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>



<div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
    <h3>treatment </h3>

    @foreach ($visitHistory as $item)
        @foreach ($item->treatment as $diagnosis)
            <li>
                <strong>Date:</strong> {{ $diagnosis->created_at }}<br>
                <strong>Description:</strong> {{ $diagnosis->problem_name }}
            </li>
        @endforeach
    @endforeach
</div>
