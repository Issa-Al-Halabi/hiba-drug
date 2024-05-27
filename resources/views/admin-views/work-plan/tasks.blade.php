@extends('layouts.back-end.app')
@section('title', \App\CPU\translate('Plans List'))
<meta name="csrf-token" content="{{ csrf_token() }}">


@push('css_or_js')

<style>
    .add-pha {
        width: 25;
        height: 25px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0px 10px;
    }

    .add-pha i {
        font-weight: 700;
    }

    .kanban-heading {
        display: flex;
        flex-direction: row;
        width: 100%;
        justify-content: center;
        font-family: sans-serif;
    }

    .kanban-board {
        padding: 10px;
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        gap: 15px;
        font-family: sans-serif;
        border-color: #041562
    }

    .kanban-heading-text {
        font-size: 1rem;
        background-color: rgba(189, 189, 189, 0.5);
        padding: 0.8rem 1.7rem;
        border-radius: 0.5rem;
        margin: 1rem;
        height: 1%;
        width: 100%;
    }



    .kanban-block {
        border-color: #041562;
        background-color: white;
        box-shadow: 0px 0px 25px -2px rgba(189, 189, 189, 0.5);
        padding: 0.6rem;
        min-width: 32%;
        height: 300px;
        border-radius: 0.3rem;
        overflow-y: scroll;
    }

    .create-new-task-block {

        background-color: #00c9a7;
        padding: 0.6rem;
        min-width: 250px;
        height: 300px;
        border-radius: 0.3rem;
        overflow-y: scroll;
    }

    .kanban-block .title-head {
        border-radius: 10px;
        margin-bottom: 15px;
        background-color: #f1f1fc;
        color: #041562;
        font-size: 17px;
        padding: 0px 20px;
        height: 48px;
        width: 100%;
        display: block;
        text-align: center;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .kanban-block .title-head .conuter {
        background: #ff00003b;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        font-size: 14px;
        margin-bottom: 0px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .row {
        margin-right: 0px !important;
        margin-left: 0px !important;
    }



    .task {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background-color: white;
        margin: 0.2rem 0rem 0.4rem 0rem;
        border: 1px solid #e1e1fa;
        box-shadow: 0px 0px 25px -5px #e9e9fc;
        FONT-WEIGHT: bold;
        font-family: sans-serif;
        padding: 8px 5px;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        position: relative;
    }

    .task a {
        color: gray;
    }

    .task:hover {
        transform: scale(1.08)
    }

    #task-button {
        margin: 0.2rem 0rem 0.1rem 0rem;
        background-color: white;
        border-radius: 0.2rem;
        width: 100%;
        border: 0.25rem solid black;
        padding: 0.5rem 2.7rem;
        border-radius: 0.3rem;
        font-size: 1rem;
    }

    .create-new-task-block {
        display: none;
        background: #ffaf00;
        width: 64.4%;
        flex-direction: column;
    }

    .form-row {
        display: flex;
        flex-direction: row;
        margin: 0.2rem;
    }

    .form-row-label {
        width: 15%;
        padding: 0.2rem;
        padding-right: 0.5rem;
        border: 0.1rem solid black;
        border-right: 0;
        border-radius: 0.2rem 0rem 0rem 0.2rem;
    }

    .form-row-input {
        border: 0.1rem solid black;
        border-radius: 0rem 0.2rem 0.2rem 0rem;
        width: 85%;
    }

    textarea {
        resize: none;
    }

    .form-row-buttons {
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        margin: 0.2rem;
    }

    .phar_name {
        text-align: center
    }

    #edit-button,
    #save-button,
    #cancel-button {
        margin: 0.2rem 0rem 0.1rem 0rem;
        background-color: white;
        border-radius: 0.2rem;
        width: 49.2%;
        border: 0.25rem solid black;
        padding: 0.5rem 2.7rem;
        border-radius: 0.3rem;
        font-size: 1rem;
    }

    #edit-button {
        display: none;
    }

</style>

@endpush

@section('content')

@php
$planDetails=\App\Model\PlanDetails::get(['Wpharmacy_id']);
@endphp
<div>
    <div class="kanban-heading" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
        <div class="col-md-4" style="height:10%; padding: 1px 9px 1px 9px;">
            <p style="padding: 11px 0px;" class="kanban-heading-text task">{{\App\CPU\translate('Begin_Plan')}}: <span> &nbsp;{{$begin}}</span></p>

        </div>
        <div class="col-md-4" style="height:10%; padding: 1px 15px 1px 3px;">
            <p style="padding: 11px 0px;" class="kanban-heading-text task">{{\App\CPU\translate('End_Plan')}}: <span> &nbsp;{{$end}}</span></p>
        </div>
        <div class="col-md-4" style="height:10%; padding: 0px 21px 0px 0px;"><span class="kanban-heading-text task">

                <button id="CompanyImportFile" data-toggle="modal" data-target='#practice_modal_import_file' data-plan_id={{$plan_id}} class="btn btn-primary btn-sm" href="#" style="color: #fff !important">
                    <i class="tio-print"></i> {{ \App\CPU\translate('Import')}}
                </button>

                <a href="{{asset('public/assets/Task-Saler-format.xlsx')}}" download="" class="btn btn-danger btn-sm " style="margin-right: 3%; margin-left: 3%; color: #fff !important">
                    <i class="tio-print"></i> {{ \App\CPU\translate('Foramt')}}
                </a>
            </span>
        </div>
    </div>


    <div class="kanban-board">
        <div class="kanban-block" id="pharmacies" ondrop="drop(event,id)" ondragover="allowDrop(event)">
            <div class="d-flex justify-content-between title-head">
                <strong>{{\App\CPU\translate('Pharmacies')}}</strong>

                <div class="d-flex align-items-center">
                    <button id="editCompanyPharmacy" data-toggle="modal" data-target='#practice_modal_pharmacy' class="btn btn-primary btn-sm add-pha" data-plan_id={{$plan_id}}>
                        <i class="tio-add"></i>
                    </button>
                    <span class="conuter">{{$pharmacies->count()}}</span>
                </div>
            </div>
            @foreach ($pharmacies as $pharmacy)
            <div class="task" id={{$pharmacy->id}} draggable="true" ondragstart="drag(event)">
                <span style="">{{$pharmacy->name}}</span>
            </div>
            @endforeach
        </div>

        @foreach ($periods as $period)
        @php
        $date= $period->format('Y-m-d');
        $d= new \DateTime($date);
        $pharmaciesSelectedTasks = \App\Model\WorkPlanTask::where([['task_plan_id','=',$plan_id],['task_date','=',$period->format('Y-m-d')]])->get(['pharmacy_id']);
        $pharmaciesTask = \App\Pharmacy::whereIn('id', $pharmaciesSelectedTasks)->get();
        @endphp

        <div class="kanban-block" id={{$period->format('Y-m-d')}} ondrop="drop(event,id)" ondragover="allowDrop(event)">
            <div class="d-flex justify-content-between title-head">
                <strong>{{$period->format('Y-m-d')}}&nbsp;&nbsp;&nbsp;{{\App\CPU\translate($d->format('l'))}}</strong>
                <span class="conuter">{{$pharmaciesTask->count()}}</span>
            </div>
            @foreach ($pharmaciesTask as $pharmacyTask)
            <div class="task" id={{$pharmacyTask->id}} draggable="true" ondragstart="drag(event)">
                <span style="">{{$pharmacyTask->name}}</span>
            </div>
            @endforeach
        </div>
        @endforeach

    </div>
</div>

 <div class="modal fade" id="practice_modal_pharmacy" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
    <div class="modal-dialog">
        <form class="row w-100 align-items-center" action="{{route('admin.sales-man.plan-pharmacy-insert')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="min-height: 150px; padding: 20px">
                <input type="hidden" id="plan_id" name="plan_id" value="" required>
                <div class="modal-body mb-4" style="padding: 0px;">

                    <div >
                        <p> {{\App\CPU\translate('Choose_pharmacy')}}</p>
                    </div>

                    <select name="pharmacy_id" class="js-example-basic-single" style="width: 100%" required>
                         @foreach ($pharmaciesNew as $pharmacyNew)
                        <option value="{{$pharmacyNew->id}}">{{$pharmacyNew->name}}</option>
                        @endforeach
                    </select>
                </div>
                <input  type="submit" value={{\App\CPU\translate('submit')}} id="submit" class="btn btn-sm btn-primary py-0" style="font-size: 1.2em; height: 30px; width: 100%;">
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="practice_modal_import_file" style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
    <div class="modal-dialog">
        <form class="row w-100 align-items-center" action="{{route('admin.sales-man.tasks-import-file')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-content" style="min-height: 150px; padding: 20px">
                <input type="hidden" id="task_plan_id" name="plan_id" value="" required>
                <div class="modal-body mb-4" style="padding: 0px">
                    <div class="card-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="file" name="tasks_file">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <input  type="submit" value={{\App\CPU\translate('submit')}} id="submit" class="btn btn-sm btn-primary py-0" style="font-size: 1.2em; height: 30px; width: 100%;">
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')


<script>

    function drag(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
    }


    function allowDrop(ev) {
        ev.preventDefault();
    }



    function drop(ev, task_date) {
        ev.preventDefault();
        var pharmacy_id = ev.dataTransfer.getData("text");
        ev.currentTarget.appendChild(document.getElementById(pharmacy_id));
        var plan_id = {{json_encode($plan_id)}};
        $("document").ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/admin/sales-man/work-plan/task/store/' + plan_id
                , type: "POST"
                , dataType: "json"
                , data: {
                    pharmacy_id: pharmacy_id
                    , task_date: task_date
                }
                , success: function(data) {

                }
            });

        });
    }



    $(document).ready(function() {
        $('.js-example-basic-single').select2();
    });


    $(document).ready(function() {
    $('body').on('click', '#editCompanyPharmacy', function(event) {
           event.preventDefault();
           var plan_id = $(this).data('plan_id');
           document.getElementById('plan_id').value = plan_id;

    });
    });

    $(document).ready(function() {
    $('body').on('click', '#CompanyImportFile', function(event) {
           event.preventDefault();
           var plan_id = $(this).data('plan_id');
           document.getElementById('task_plan_id').value = plan_id;

    });
    });

</script>

@endpush
