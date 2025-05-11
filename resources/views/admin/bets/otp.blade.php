@extends('layouts.app')
@section('title','OTP Page')
@section('styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <h1>OTP Codes List</h1>
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
                        <th>OTP</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($users))
                       
                        @foreach($users as $user)
                        @php
                        //    $total_cashin = $user->topups->where('status','success')->sum('amount');
                           $total_cashin = 0;
           
                        @endphp
                            <tr class="odd">
                                <td>{{$user->id}}</td>
                                <td><a href="{{route('admin.user.cashin',$user->id)}}">{{$user->mobile_no}}</a></td>
                                <td>{{$user->otp}}</td>
                                <td>
                                    {{-- @if(Auth::user()->user_level >= $super_admin) --}}
                                        <a  href="{{url('admin/users/edit')}}/{{$user->id}}" 
                                            class="btn btn-success mx-1"><i 
                                            class="bi bi-pencil"></i> Edit</a>
                                    {{-- @endif --}}
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
<x-datepicker />
<script type="text/javascript">
    $(function () {
        $('.modal-close').on('click',function(){
            $('.modal_notif').modal('hide');
        });
    
        $("#datepicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });
</script>
@section('scripts')
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
@endsection
