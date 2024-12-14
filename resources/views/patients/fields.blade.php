    <div class="row">
        <div class="col-md-12 mb-5">
            {{ Form::label('firstName', __('messages.patient.full_name') . ':', ['class' => 'form-label required']) }}
            {{ Form::text('first_name', !empty($patient->user) ? $patient->user->first_name : null, ['class' => 'form-control', 'placeholder' => __('messages.patient.first_name'), 'required']) }}
        </div>
        {{-- <div class="col-md-6 mb-5">
        {{ Form::label('lastName',__('messages.patient.last_name').':' ,['class' => 'form-label required']) }}
        {{ Form::text('last_name',!empty($patient->user) ? $patient->user->last_name : null,['class' => 'form-control','placeholder' => __('messages.patient.last_name'),'required']) }}
    </div> --}}
        <div class="col-md-6 mb-5">
            {{ Form::label('patientUniqueId', __('messages.patient.file_number') . ':', ['class' => 'form-label required']) }}
            {{ Form::text('patient_unique_id', isset($data['patientUniqueId']) ? $data['patientUniqueId'] : null, ['class' => 'form-control', 'required', 'maxLength' => '8', 'readonly']) }}
        </div>
        {{-- <div class="col-md-6 mb-5">
        {{ Form::label('email',__('messages.patient.email').':' ,['class' => 'form-label required']) }}
        {{ Form::email('email',!empty($patient->user) ? $patient->user->email : null,['class' => 'form-control','placeholder' => __('messages.patient.email'),'required']) }}
    </div> --}}
        {{-- @if (empty($patient))
        <div class="col-md-6 mb-5">
            <div class="mb-1">
                {{ Form::label('password',__('messages.patient.password').':' ,['class' => 'form-label required']) }}
                <span data-bs-toggle="tooltip" title="{{ __('messages.flash.user_8_or') }}">
                <i class="fa fa-question-circle"></i></span>
                <div class="mb-3 position-relative">
                    {{Form::password('password',['class' => 'form-control','placeholder' => __('messages.patient.password'),'autocomplete' => 'off','required','aria-label'=>"Password",'data-toggle'=>"password"])}}
                    <span class="position-absolute d-flex align-items-center top-0 bottom-0 end-0 me-4 input-icon input-password-hide cursor-pointer text-gray-600"> <i class="bi bi-eye-slash-fill"></i> </span>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-5">
                <div class="mb-1">
                    {{ Form::label('confirmPassword',__('messages.patient.confirm_password').':' ,['class' => 'form-label required']) }}
                    <span data-bs-toggle="tooltip"
                          title="{{ __('messages.flash.user_8_or') }}">
                    <i class="fa fa-question-circle"></i></span>
                    <div class="mb-3 position-relative">
                        {{Form::password('password_confirmation',['class' => 'form-control','placeholder' => __('messages.user.confirm_password'),'autocomplete' => 'off','required','aria-label'=>"Password",'data-toggle'=>"password"])}}
                        <span class="position-absolute d-flex align-items-center top-0 bottom-0 end-0 me-4 input-icon input-password-hide cursor-pointer text-gray-600"> <i class="bi bi-eye-slash-fill"></i> </span>
                    </div>
                </div>
        </div>
    @endif --}}
        <div class="col-md-6 mb-5">
            {{ Form::label('contact', __('messages.patient.contact_no') . ':', ['class' => 'form-label']) }}
            {{ Form::tel(
                'contact',
                !empty($patient->user) ? '+' . $patient->user->region_code . $patient->user->contact : '+967',
                [
                    'class' => 'form-control',
                    'placeholder' => __('messages.patient.contact_no'),
                    'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")',
                    'id' => 'phoneNumber',
                ],
            ) }}
            {{ Form::hidden('region_code', !empty($patient->user) ? $patient->user->region_code : null, ['id' => 'prefix_code']) }}
            <span id="valid-msg" class="text-success d-none fw-400 fs-small mt-2">{{ __('messages.valid_number') }}</span>
            <span id="error-msg"
                class="text-danger d-none fw-400 fs-small mt-2">{{ __('messages.invalid_number') }}</span>
        </div>
        <div class="col-md-6 mb-5">
            {{ Form::label('gender', __('messages.staff.gender') . ':', ['class' => 'form-label required']) }}
            <span class="is-valid">
                <div class="mt-2">
                    <input class="form-check-input" type="radio" name="gender" value="1" checked
                        {{ !empty($patient->user) && $patient->user->gender === 1 ? 'checked' : '' }}>
                    <label class="form-label">{{ __('messages.staff.male') }}</label>&nbsp;&nbsp;
                    <input class="form-check-input" type="radio" name="gender" value="2"
                        {{ !empty($patient->user) && $patient->user->gender === 2 ? 'checked' : '' }}>
                    <label class="form-label">{{ __('messages.staff.female') }}</label>
                </div>
            </span>
        </div>


        <div class="col-md-6 mb-5">
            {{ Form::label('dob', __('messages.patient.dob') . ':', ['class' => 'form-label']) }}
            {{ Form::text('dob', !empty($patient->user) ? $patient->user->dob : null, ['class' => 'form-control patient-dob', 'id' => __('messages.patient.dob'), 'placeholder' => __('messages.doctor.dob')]) }}
        </div>
        <div class="col-md-6 mb-5">
            {{ Form::label('weight', __('messages.patient.weight') . ':', ['class' => 'form-label']) }}
            {{ Form::text('weight', !empty($patient->user) ? $patient->user->weight : null, ['class' => 'form-control patient-weight', 'id' => __('messages.patient.weight'), 'placeholder' => __('messages.doctor.weight')]) }}
        </div>
        <div class="col-md-6 mb-5">
            {{ Form::label('age', __('messages.patient.age') . ':', ['class' => 'form-label']) }}
            {{ Form::text('age', !empty($patient->user) ? $patient->user->age : null, ['class' => 'form-control patient-age', 'id' => __('messages.patient.age'), 'placeholder' => __('messages.doctor.age')]) }}
        </div>
        <div class="col-md-6 mb-5">
            <label class="form-label">{{ __('messages.patient.blood_group') . ':' }}</label>
            {{ Form::select('blood_group', $data['bloodGroupList'], !empty($patient->user) ? $patient->user->blood_group : null, ['placeholder' => __('messages.patient.blood_group'), 'class' => 'form-select io-select2', 'aria-label' => 'Select a Blood Group', 'data-control' => 'select2']) }}
        </div>






    </div>



    <div>
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2']) }}
        <a href="{{ route('patients.index') }}" type="reset"
            class="btn btn-secondary">{{ __('messages.common.discard') }}</a>
    </div>

    <script>
        var i = 1;
        var length;

        function appendingform(form_name, field_name, date_name) {

            $('#' + form_name).append(
                '<div class="row" id="row' + i + '">' +
                '<div class="col-md-2 p-1">' +
                '<button type="button" name="remove" id="' + i +
                '" class="btn btn-danger btn_remove" onclick="deleteRow(' + i + ')">X</button>' +
                '</div>' +
                '<div class="col-md-3 p-1">' +
                '<input type="text" name="' + date_name +
                '[]" placeholder="Date" class="form-control  patient-dob" />' +
                '</div>' +
                '<div class="col-md-9 p-1" >' +
                '<input type="text" name="' + field_name +
                '[]" placeholder="Description" class="form-control " />' +
                '</div>' +
                '</div>'
            );

            $('.patient-dob').flatpickr({
                locale: 'en',
                maxDate: new Date(),
                disableMobile: true
            });

            i++;
        }


        function deleteRow(rowId) {
            $('#row' + rowId).remove();
        }
        // $(document).ready(function() {





        //     $(document).on('click', '.btn_remove', function() {
        //         addamount -= 700;
        //         console.log('amount: ' + addamount);


        //         var rowIndex = $('#dynamic_field').find('tr').length;

        //         addamount -= (700 * rowIndex);
        //         console.log(addamount);


        //         var button_id = $(this).attr("id");
        //         $('#row' + button_id + '').remove();
        //     });



        //     $("#submit").on('click', function(event) {
        //         var formdata = $("#add_name").serialize();
        //         console.log(formdata);

        //         event.preventDefault()

        //         $.ajax({
        //             url: "action.php",
        //             type: "POST",
        //             data: formdata,
        //             cache: false,
        //             success: function(result) {
        //                 alert(result);
        //                 $("#add_name")[0].reset();
        //             }
        //         });

        //     });
        // });
    </script>
