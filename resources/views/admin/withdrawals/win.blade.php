@extends('layouts.app')
@section('title',$play)
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-group align-items-center">
                    <select class="form-control mx-1" name="status">
                       <option value="" >Select Claim Status</option>
                       <option value="" <?= (isset($_GET['status']) && $_GET['status'] == '') ? 'selected' : '' ?>>Unclaimed</option>
                       <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : '' ?>>Claimed</option>
                       <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : '' ?>>Unprocessed</option>
                    </select>
                    <button type="submit" class="btn btn-primary mx-2">Filter</button>
                    {{-- <a type="submit" class="btn btn-primary mx-2 rounded-0" href="{{route('claimed_win_2')}}">Show All</a> --}}
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <h1><?=$play;?></h1>
        <h3>{{__('GCash Balance: ')}} <strong id="gcash_amount">Loading...</strong></h3>
        <h3>{{__('PayMaya (maya) Balance: ')}} <strong id="paymaya_amount">Loading...</strong></h3>
    </div>
    <table class="table winners">
        <thead>
            <tr>
                <th>#</th>
                <th>Contact No.</th>
                <th>User ID</th>
                <th>Withdrawal Reference No.</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Date Claimed</th>
                <th>Info Claim</th>
                <th>Process Status</th>
                <th>PG</th>
                <th>Action</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($winners))
                @foreach($winners as $winner)
                    @php 
                    if ($winner->process_status =="success") {
                        continue;
                    }
                       $date_claimed = null;
                        if($winner->claimed_date){
                            $date = date_create($winner->claimed_date);
                            $date_claimed = date_format($date, 'M d, Y h:iA');
                        }
                        $userdata = \App\Models\User::find($winner->id);
                        $ref_code = 'User is not MA or SA';
                        $role = 'Not Available';

                        $date = date_create($winner->created_at);
                        $date_won = date_format($date, 'M d, Y h:iA');
                        $is_processed = $winner->approved != 0 ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>';
                        $bet_date = date_format($winner->created_at, 'M d, Y h:iA');
                    @endphp
                    <tr>
                        <td>{{$winner->id}}</td>
                        <td>dsadas</td>{{--Contact No.--}}
                        <td>{{$winner->user_id}}</td>{{--Reference No--}}
                        <td>{{$winner->merchant_order_no}}</td>{{--Reference No--}}
                        <td><span class="{{$winner->claimed !=0 ?'text-success':'text-danger'}}">{{$winner->claimed!=0?'claimed':'unclaimed'}}</span></td>{{--Status--}}
                        <td>${{number_format($winner->amount,2,'.',',')}}</td>{{--Winning Amount--}}
                        <td>{{isset($winner->claimed) ? $date_claimed : ''}}</td>{{--Date Claimed--}}
                        <td>
                            <div class="info-claim">
                                Receiver name: <strong>{{$winner->receiver_name}}</strong><br>
                                Payout remittance: <strong>{{$winner->payout_remittance}}</strong><br>
                                Receiver mobile: <strong>{{$winner->receiver_mobile_number}}</strong><br>
                                Processed: <strong><?=$is_processed?></strong><br>
                                Approved By: <strong><?=$winner->approved?></strong><br>
                                Date Claimed: <strong><?=$bet_date?></strong><br>
                                Referral Code: <strong><?=$ref_code?></strong><br>
                                Role: <strong><?=$role?></strong><br>
                            </div>
                        </td>{{--Info Claim--}}
                        <td>{{$winner->process_status}}</td>{{--Upload--}}
                        <td>
                            <select data-id="{{$winner->id}}" class="process-type-{{$winner->id}}">
                                <option value="">Select PG</option>
                                <option value="pg_4">PG4</option>
                            </select>    
                        </td>{{--Upload--}}
                        <td>
                            @if ($winner->process_status!='success')
                                <a  data-id="{{$winner->id}}" 
                                    data-play="{{$winner->play_type}}" 
                                    onclick="reprocessDraw(this)" 
                                    class="btn btn-primary mx-1">RE-PROCESS</a>    
                            @endif
                            @if ($winner->approved == 0)
                                <a  data-id="{{$winner->id}}" 
                                    data-play="{{$winner->play_type}}" 
                                    onclick="publishDraw(this)" 
                                    class="btn btn-danger mx-1">Process</a>
                            @endif
                            @if (auth()->user()->user_level >= 5000 || auth()->user()->id == 17 )
                                <a  data-id="{{$winner->id}}" 
                                    onclick="deleteRowRecord(this)" 
                                    class="btn btn-danger mx-1">Delete</a>
                            @endif
                                
                        </td>
                        <td>
                            <textarea  data-id="{{$winner->id}}"  class="form-control status-message-{{$winner->id}}"></textarea>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{-- <x-reports /> --}}
</div>
 
@endsection

<x-confirmation-modal />
<x-modal-notif />
<x-js-lib />
<script type="text/javascript">
    $(function () {
        // $.ajax({
        //     method: "POST",
        //     url: "{{route('check.balance')}}",
        //     headers: {
        //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //     }
        //         })
        //     .success(function( msg ) {           
        //         user_balance_maya = msg.data.paymaya;
        //         user_balance_gcash = msg.data.gcash;  
        //         $("#gcash_amount").text(user_balance_gcash);
        //         $("#paymaya_amount").text(user_balance_maya);
        // });  
        
        $(".status-message").blur(function(){
            let mess = $(this).val();
        });
        $(".process_status").on('change',function(){

            $('#exampleModal').modal('show');
        });
        $(".modal-close").on('click',function(){
            $('#exampleModal').modal('hide');
        });


        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $('.winners').DataTable({
            "pageLength": 1000,
            "lengthMenu": [ [1000, 5000, 10000, -1], [1000, 5000, 10000,'All'] ]
        });

    });
    function publishDraw(e) {
        let my_id = $(e).data('id');
        let process_type = $(".process-type-"+my_id).val();
        // alert(process_type);
        if(process_type ==  ''){
            alert('Please select a payment gateway.');

            return;
        }
        if(confirm('Are you sure you want to continue?')) {
            $.ajax({
                type: 'post',
                url: "{{route('payout.process.referral')}}", //Payout JDpay
                data: {
                    id: $(e).data('id'),
                    process_type: process_type,
                    pg: process_type
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(){
                    $('#create_new').html('....Please wait');
                },
                success: function(response){
                    // console.log(response);
                    $('.modal_notif').modal('show');
                    $('.notif_message').text(response.message.msg);
                    window.location.reload();
                },
                complete: function(response){
                    if(response.success=='false'){
                        alert('missing');
                    }
                    $('#create_new').html('Create New');
                },
                error: function(response){
                    
                }
            });
        }
    }
    function reprocessDraw(e) {
        let my_id = $(e).data('id');
        let process_type = $(".process-type-"+my_id).val();
        // alert(process_type);
        if(confirm('Are you sure you want to continue?')) {
            $.ajax({
                type: 'post',
                url: "{{route('payout.reprocess')}}",
                data: {
                    id: $(e).data('id'),
                    play: "agent_withdrawals",
                    process_type: process_type
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(){
                    $('#create_new').html('....Please wait');
                },
                success: function(response){
                    // console.log(response);
                    $('.modal_notif').modal('show');
                    $('.notif_message').text(response.message.msg);
                    window.location.reload();
                },
                complete: function(response){
                    if(response.success=='false'){
                        alert('missing');
                    }
                    $('#create_new').html('Create New');
                },
                error: function(response){
                    
                }
            });
        }
    }
    function deleteRowRecord(e) {
        let my_id = $(e).data('id');
        
        if(confirm('Are you sure you want to continue?')) {
            $.ajax({
                type: 'post',
                url: "{{route('payout.delete.referral')}}",
                data: {
                    id: $(e).data('id'),
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(){
                    $('#create_new').html('....Please wait');
                },
                success: function(response){
                    // console.log(response);
                    $('.modal_notif').modal('show');
                    $('.notif_message').text(response.message.msg);
                    window.location.reload();
                },
                complete: function(response){
                    if(response.success=='false'){
                        alert('missing');
                    }
                    $('#create_new').html('Create New');
                },
                error: function(response){
                    
                }
            });
        }
    }
  </script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
