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
            $(".overall_company_share").text( response.data.overall_company_share);
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
                    // $(".company_cut_" + e).text( response.data.company_share);


                    $(".net_pool_" + e).text( response.data.net_pool);
                    $(".payout_rate_" + e).text( response.data.payout_rate);


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

    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <p class="draw-label total-amounts">Daily Company Share: ₱ <span class="text-success daily_company_share">Loading...</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-row justify-content-between">
                <div>
                    <p class="draw-label total-amounts">Overall Company Share: ₱ <span class="text-success overall_company_share">Loading...</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <h1>Game Fights Report Bets</h1>
    </div>
    <div class="row">
        <table class="table bets-2d">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fight No.</th>
                    <th class="text-danger" >Meron Bets (Red Color)</th>
                    <th class="text-primary">Wala Bets (Blue Color)</th>
                    <th>Company Share</th>
                    <th>Winner</th>
                    <th>Company Cut: 6% (0.06)</th>
                    <th>Date Timestamp</th>
                    <th>Fight ID</th>
                    <th>Net Pool</th>
                    <th>Payout Rate</th>
                </tr>
            </thead>
            <tbody>
                    @php
                        $cntr = 1;
                        
                    @endphp
                    @foreach($fights as $fight)
                    @php

                        $profitability = 100;
                        $winSide = null;

                        switch ($fight->game_winner) {
                            case 'M':
                                    $winSide = '<span class="text-primary">MERON</span>';
                                break;
                            case 'W':
                                    $winSide = '<span class="text-danger">WALA</span>';
                                break;
                            
                            case 'C':
                                    $winSide = '<span class="text-warning">CANCELLED</span>';
                                break;

                            case 'X':
                                    $winSide = '<span class="text-secondary">DRAW</span>';
                                break;
                            
                            default:
                                # code...
                                break;
                        }
                        
                    @endphp
                    @php
                        @endphp
                        <tr>
                            <td>{{$cntr}}</td>
                            <td>{{$fight->fight_no}}</td>
                            <td class="text-danger total_meron_{{$fight->id}}">Loading...</td>
                            <td class="text-primary total_wala_{{$fight->id}}">Loading...</td>
                            <td class="company_share_{{$fight->id}}">Loading...</td> {{--company share--}}
                            <td><?php echo $winSide ?></td>
                            <td>6%</td>
                            <td><?php echo date('M d, Y h:iA',strtotime($fight->created_at)) ?></td>
                            <td>{{$fight->id}}</td>
                            <td class="net_pool_{{$fight->id}}">Loading...</td>
                            <td class="payout_rate_{{$fight->id}}">Loading...</td>


                            <script>loadRowData({{$fight->id}})</script>
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
