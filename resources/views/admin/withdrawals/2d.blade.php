@extends('layouts.app')
@section('title','2D Winners')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="input-group align-items-center">
                    <label class="draw-label mx-1">Date</label>
                    <input type="text" class="form-control" name="date" id="datepicker" value="<?= isset($_GET['date']) ? $_GET['date'] : date('Y-m-d') ?>"/>
                    <select class="form-control mx-1" name="draw_time">
                       <option value="">Draw Time</option>
                       @foreach($draw_types as $draw)
                        <option value="<?=$draw->draw?>" <?= (isset($_GET['draw_time']) && $draw->draw == $_GET['draw_time']) ? 'selected' : '' ?>>{{$draw->draw}}</option>
                       @endforeach
                    </select>
                    <select class="form-control mx-1" name="status">
                       <option value="" >Select Claim Status</option>
                       <option value="" <?= (isset($_GET['status']) && $_GET['status'] == '') ? 'selected' : '' ?>>Unclaimed</option>
                       <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == 1) ? 'selected' : '' ?>>Claimed</option>
                       <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == 2) ? 'selected' : '' ?>>Unprocessed</option>
                    </select>
                    <button type="submit" class="btn btn-primary mx-2">Filter</button>
                    <a type="submit" class="btn btn-primary mx-2 rounded-0" href="{{route('win_2')}}">Show All</a>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <h1>2D Winners</h1>
    </div>
    Show All<table class="table winners">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
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
                <th>Upload</th>
                <th>Processed</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($winners))
                @foreach($winners as $winner)
                    <tr>
                        <td>{{$winner->id}}</td>
                        <td>{{$winner->receiver_name}}</td>
                        <td>{{$winner->contact_no}}</td>
                        <td>{{$winner->reference_no}}</td>
                        <td>{{str_replace(',','-',$winner->winning_combination)}}</td>
                        <td>{{$winner->draw_date}}</td>
                        <td>{{$winner->draw_time}}</td>
                        <td>{{$winner->combination_type}}</td>
                        <td><span class="{{isset($winner->claimed)?'text-success':'text-danger'}}">{{isset($winner->claimed)?'claimed':'unclaimed'}}</span></td>
                        <td>{{number_format($winner->bet_amount,2,'.',',')}}</td>
                        <td>{{number_format($winner->winning_amount,2)}}</td>
                        <td>{{$winner->date_claimed}}</td>
                        <td>{{$winner->upload}}</td>
                        <td>
                            <select data-id="{{$winner->id}}" class="process_status form-control">
                                <option value="yes" <?=$winner->process_status=='yes'?'selected':'';?>>Yes</option>
                                <option value="no" <?=$winner->process_status!='yes'?'selected':'';?>>No</option>
                            </select>
                        </td>
                        <td>
                            <textarea  data-id="{{$winner->id}}"  class="form-control status-message"></textarea>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{-- <x-reports /> --}}
</div>
@endsection

<x-js-lib />
<script type="text/javascript">
    $(function () {
        
        $(".status-message").blur(function(){
            let mess = $(this).val();
            alert(mess);
        });

        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
  </script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
