@extends('layouts.app')
@section('title',$play)
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="input-group align-items-center">
                    <div class="input-group align-items-center my-2">
                        <label class="draw-label mx-1">From</label>
                        <input type="text" class="form-control datepicker"  name="from" value="<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d') ?>"/>
                        <label class="draw-label mx-1">To</label>
                        <input type="text" class="form-control mx-1 datepicker" name="to" value="<?= isset($_GET['to']) ? $_GET['to'] : date('Y-m-d') ?>"/>
                    </div>
                    <label class="draw-label mx-1">Draw Time</label>
                     
                    <select class="form-control mx-1" name="draw_time">
                        <option value="">All</option>
                       @foreach($draw_types as $draw)
                       <option value="<?=$draw->draw?>" <?= (isset($_GET['draw_time']) && $draw->draw == $_GET['draw_time']) ? 'selected' : '' ?>>{{$draw->draw}}</option>
                       @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary mx-2">Filter</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <h1><?=$play;?></h1>
    </div>
    <table class="table winners">
        <thead>
            <tr>
                <th>#</th>
                <th>User ID.</th>
                <th>Contact No.</th>
                <th>Reference No.</th>
                <th>Winning Number</th>
                <th>Draw Date</th>
                <th>Draw Time</th>
                <th>Order</th>
                <th>Status</th>
                <th>Bet Amount</th>
                <th>Winning Amount</th>
                <th>Date Claimed</th>
                <th>Date Won</th>
                <th>Info Claim</th>
                <th>Process Status</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($winners))
                    @php 
                       $cntr = 1;
                    @endphp
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
                    @endphp
                    <tr>
                        <td>{{$cntr}}</td>
                        <td>{{$winner->user_id}}</td>
                        <td>{{$winner->contact_no}}</td>{{--Contact No.--}}
                        <td>{{$winner->reference_no}}</td>{{--Reference No--}}
                        <td>{{str_replace(',','-',$winner->winning_combination)}}</td>{{--Winning Number--}}
                        <td>{{$winner->draw_date}}</td>{{--Draw Date--}}
                        <td>{{$winner->draw_time}}</td>{{--Draw Time--}}
                        <td>{{$winner->combination_type}}</td>{{--Order--}}
                        <td><span class="{{$winner->claimed != 0 ?'text-success':'text-danger'}}">{{$winner->claimed != 0 ?'claimed':'unclaimed'}}</span></td>{{--Status--}}
                        <td>${{number_format($winner->bet_amount,2,'.',',')}}</td>{{--Bet Amount--}}
                        <td>${{number_format($winner->winning_amount,2,'.',',')}}</td>{{--Winning Amount--}}
                        <td>{{$date_claimed}}</td>{{--Date Claimed--}}
                        <td>{{$date_won}}</td>{{--Date Won--}}
                        <td>
                            Receiver name: <strong>{{$winner->receiver_name}}</strong><br>
                            Payout remittance: <strong>{{$winner->payout_remittance}}</strong><br>
                            Receiver mobile: <strong>{{$winner->receiver_mobile_number}}</strong><br>
                            Processed: <strong><?=$is_processed?></strong><br>
                            Approved by: <strong><?=$winner->approved?></strong><br>
                            Bet ID: <strong><?=$winner->bet_id?></strong><br>
                            Order No.: <strong><?=$winner->merchant_order_no?></strong><br>
                        </td>{{--Info Claim--}}
                        <td>{{$winner->process_status}}</td>{{--Upload--}}
                        <td>
                            {{$winner->message}} 
                        </td>
                    </tr>
                    @php 
                       $cntr++;
                    @endphp
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
<x-datepicker />
<script type="text/javascript">
    $(function () {
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $('.winners').DataTable({
            "pageLength": 1000,
            "lengthMenu": [ [1000, 5000, 10000, -1], [1000, 5000, 10000,'All'] ],
            order: [[0, 'desc']],
        });
    });
  </script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
