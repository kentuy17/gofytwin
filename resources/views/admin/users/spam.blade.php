@extends('layouts.app')
@section('title','2D Bets')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="row my-2">
        <h1>{{$title}} Accounts</h1>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="button-container  py-2 my-2">
                <form method="GET">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="input-group align-items-center">
                            <span class="input-group-text" id="inputGroup-sizing-sm">Search</span>
                            <input type="text" class="form-control"  name="q" value="{{$q}}" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                          
                            <button style="border-radius: 5px !important;" type="submit" class="btn btn-primary mx-1 rounded-0 border-radius-element">Filter</button>
                        </div>
                    </div>
                    {{-- <p class="draw-label total-amounts mt-4">Date Range: <span class="text-success">{{date_format($from,'Y-m-d') .' to '. date_format($to,'Y-m-d')}}</span></p> --}}
                </form>
            </div>
        </div> 
        <div class="col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Contact No.</th>
                        {{-- <th>Credits</th>
                        <th>Cash In Total</th> --}}
                        <th>Location</th>
                        <th>IP</th>
                        <th>Login Attempts</th>
                        <th>User Status</th>
                        <th>Date Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($users))
                       
                        @foreach($users as $user)
                        @php
                        //    $total_cashin = $topup_model->where('user_id',$user->id)->where('status','success')->sum('amount');
                        @endphp
                            <tr class="odd">
                                <td>{{$user->id}}</td>
                                <td><a href="{{route('admin.user.cashin',$user->id)}}">{{$user->mobile_no}}</a></td>
                                {{-- <td>${{isset($user->credits->amount) ? number_format($user->credits->amount, 2, '.',',') : 'NA'}}</td>
                                <td>${{number_format($total_cashin, 2, '.',',')}}</td> --}}
                                <td>{{$user->location}}</td>
                                <td>{{$user->ip}}</td>
                                <td>{{$user->user_attepts}}</td>
                                <td>{{$user->account_status}}</td>
                                <td>
                                    <?php
                                        $date = date_create($user->created_at);
                                        $timestamp = date_format($date,'M d, Y h:iA');
                                        echo $timestamp;
                                    ?>
                                </td>
                                <td>
                                    @if(Auth::user()->userlevel >= $super_admin)
                                        <a  href="{{url('admin/users/edit')}}/{{$user->id}}" 
                                            class="btn btn-success mx-1"><i 
                                            class="bi bi-pencil"></i> Edit</a>
                                        <a  href="{{url('/admin/master-agent/add')}}/{{$user->id}}" 
                                            class="btn btn-danger mx-1"><i 
                                            class="bi bi-person"></i> Add as MA</a>
                                        <a  href="{{url('/admin/sub-agent/add')}}/{{$user->id}}" 
                                            class="btn btn-primary mx-1"><i 
                                            class="bi bi-person"></i> Add as SA</a>    
                                        <a  href="JavaScript:void(0);" 
                                            data-id="{{$user->id}}" 
                                            class="btn btn-warning mx-1 click-me"><i 
                                            class="bi bi-person"></i> View Details</a>    
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
    </div>
    {{ $users->links() }}
</div>
@endsection

<x-js-lib />
<x-modal-info-spam />
<x-datepicker />
<script type="text/javascript">
    $(function () {
        $('.modal-close').on('click',function(){
            $('.modal_notif').modal('hide');
            $('.modal_info').modal('hide');
        });
    
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('.click-me').on('click',function(){
                let current_id = $(this).data('id');
                let play = $(".hot-amount-text-"+current_id).val();
                let play_type = 'spam';
                $.ajax({
                        type: 'post',
                        url: "{{route('winner.info')}}",
                        data: {
                            id:current_id,
                            play:play_type
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function(){
                            $('#create_new').html('....Please wait');
                        },
                        success: function(response){
                            $(".total-gross-since").text('$' + response.data.gross);
                            $(".total-ci").text('$' + response.data.gross);
                            $(".total-gross-pick1").text('$' + response.data.gross_pick1);
                            $(".total-gross-pick2").text('$' + response.data.gross_pick2);
                            $(".total-gross-2d").text('$' + response.data.gross_2d);
                            $(".total-gross-3d").text('$' + response.data.gross_3d);
                            $(".total-gross-4d").text('$' + response.data.gross_4d);
                            $(".total-gross-6d").text('$' + response.data.gross_6d);
                            $(".profitability-ever-since").text(response.data.profitability);
                            $(".total-winnings").text('$' + response.data.total_winnings);
                            $(".win-count").text(response.data.win_count);
                            $(".info_message").text('{{$title}}');
                            $('.modal_info').modal('show');
                        },
                        complete: function(response){
                            // $('#create_new').html('Create New');
                        }
                    });
            });
     
    });
</script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
