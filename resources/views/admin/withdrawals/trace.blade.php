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
                    <label class="draw-label mx-1">From</label>
                    <input type="text" class="form-control datepicker from"  name="from" value="<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');  ?>"/>
                    <label class="draw-label mx-1">To</label>
                    <input type="text" class="form-control datepicker to" name="to" value="<?= isset($_GET['to']) ? $_GET['to'] : date('Y-m-d') ?>"/>
                    <button type="submit" class="btn btn-primary mx-2 rounded-0">Filter</button>
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
                <th>Reference No.</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Date Claimed</th>
                <th>Info Claim</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($winners))
                @foreach($winners as $winner)
                    @php 
                       $date_claimed = null;
                        if($winner->claimed_date){
                            $date = date_create($winner->claimed_date);
                            $date_claimed = date_format($date, 'M d, Y h:iA');
                        }

                       $date = date_create($winner->created_at);
                       $date_won = date_format($date, 'M d, Y h:iA');
                       $is_processed = $winner->approved != 0 ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No</span>';
                       $bet_date = date_format($winner->created_at, 'M d, Y h:iA');
                    @endphp
                    <tr>
                        <td>{{$winner->id}}</td>
                        <td>{{$winner->user->mobile_no}}</td>{{--Contact No.--}}
                        <td>{{$winner->reference_no}}</td>{{--Reference No--}}
                        <td><span class="{{$winner->claimed!=0?'text-success':'text-danger'}}">{{$winner->claimed!=0?'claimed':'Pending'}}</span></td>{{--Status--}}
                        <td>${{number_format($winner->amount,2,'.',',')}}</td>{{--Winning Amount--}}
                        <td>{{isset($winner->claimed) ? $date_claimed : ''}}</td>{{--Date Claimed--}}
                        <td>
                            <div class="info-claim">
                                Receiver name: <strong>{{$winner->receiver_name}}</strong><br>
                                Payout remittance: <strong>{{$winner->payout_remittance}}</strong><br>
                                Receiver mobile: <strong>{{$winner->receiver_mobile_number}}</strong><br>
                                Reference No.: <strong><?=$winner->merchant_order_no?></strong><br>
                                Processed: <strong><?=$is_processed?></strong><br>
                                Approved By: <strong><?=$winner->approved?></strong><br>
                                Date Claimed: <strong><?=$bet_date?></strong><br>
                            </div>
                        </td>{{--Info Claim--}}
                        
                        <td>
                            <span>{{$winner->message}}</span>
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
        // setInterval(function (){
            $.ajax({
                method: "POST",
                url: "{{route('check.balance')}}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                    })
                .success(function( msg ) {           
                    user_balance_maya = msg.data.paymaya;
                    user_balance_gcash = msg.data.gcash;  
                    $("#gcash_amount").text(user_balance_gcash);
                    $("#paymaya_amount").text(user_balance_maya);
            });  
        // },5000);


        
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
        if(confirm('Are you sure you want to continue?')) {
            $.ajax({
                type: 'post',
                url: "{{route('payout.process.referral')}}",
                data: {
                    id: $(e).data('id'),
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
  </script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<x-statistics-ajax />
@endsection
