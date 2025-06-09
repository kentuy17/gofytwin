@extends('layouts.app')
@section('title','Winners Board'.$title)
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<script>
    var total_income = 0;
    $.ajax({
        type: 'post',
        url: "{{route('winner.info.company.share.since.birth')}}",
        data: {
            from:"{{$from}}",
        },  
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $('#create_new').html('....Please wait');
        },
        success: function(response){
            $(".daily_company_share").text( response.data.daily_company_share);
        },
        complete: function(response){
            // $('#create_new').html('Create New');
        }
    });
    setTimeout(function(){
        $.ajax({
            type: 'post',
            url: "{{route('winner.info.company.share')}}",
            data: {
                from:"{{$from}}",
            },  
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function(){
                $('#create_new').html('....Please wait');
            },
            success: function(response){
                $(".daily_company_share").text( response.data.daily_company_share);
            },
            complete: function(response){
                // $('#create_new').html('Create New');
            }
        });
    },30000);
    function loadRowData(e){
        setTimeout(function(){
            $.ajax({
                type: 'post',
                url: "{{route('winner.info.winner.board')}}",
                data: {
                    from:"{{$from}}",
                    to:"{{$to}}",
                    record_id:e,
                    play:"players"
                },  
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function(){
                    $('#create_new').html('....Please wait');
                },
                success: function(response){
                    $(".total_meron_" + e).text( response.data.total_meron);
                    $(".total_wala_" + e).text( response.data.total_wala);
                    $(".company_share_" + e).text( response.data.company_share);
                    total_income = total_income + response.data.total_bets;
                    $(".totall").text(total_income);
                },
                complete: function(response){
                    // $('#create_new').html('Create New');
                }
            });
       },1000);
    }
</script>
<div class="container-fluid">
    <div class="button-container  p-2 my-2">
        <form method="GET" action="">
            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                <div class="input-group align-items-center">
                    <div class="input-group align-items-center my-2">
                        <label class="draw-label mx-1">Date</label>
                        <input type="text" class="form-control datepicker"  name="from" value="<?= isset($_GET['from']) ? $_GET['from'] : date('Y-m-d', strtotime(date('Y-m-d')));  ?>"/>
                        <button type="submit" class="btn btn-primary mx-2">Filter</button>

                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="button-container  p-2 ">
      <span>Daily Company Share: </span> <span class="daily_company_share">Loading...</span>
      <br>
      <span>Overall Company Share: </span> <span class="company_share">Loading...</span>
    </div>

    <div class="row">
        <h1>Game Fights Report Bets</h1>
    </div>
    <div class="row">
        <table class="table bets-2d">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fight Number</th>
                    <th>Total Meron</th>
                    <th>Total Wala</th>
                    <th>Company Share</th>
                    <th>Winning Side</th>
                </tr>
            </thead>
            <tbody>
                    @php
                        $cntr = 1;
                        
                    @endphp
                    @foreach($fights as $fight)
                    @php

                        $profitability = 100;
                        
                    @endphp
                    @php
                        @endphp
                        <tr>
                            <td>{{$cntr}}</td>
                            <td>{{$fight->fight_no}}</td>
                            <td class="total_meron_{{$fight->fight_no}}">Loading...</td>
                            <td class="total_wala_{{$fight->fight_no}}">Loading...</td>
                            <td class="company_share_{{$fight->fight_no}}">Loading...</td>
                            <td>{{$fight->game_winner}}</td>
                            <script>loadRowData({{$fight->fight_no}})</script>
                        </tr>
                        @php
                            $cntr ++;
                        @endphp
                    @endforeach
                 
            </tbody>
        </table>
    </div>
   
</div>
@endsection

<x-js-lib />
<x-modal-info />
<x-datepicker />
 
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
