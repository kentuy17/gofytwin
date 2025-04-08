@extends('layouts.app')
@section('title','Edit User')
@section('content')
<style>
    .master-agents-container{
        display:block;
    }
    .sub-agents-container{
        display:block;
    }
    #account-type{
        background-color: brown;
        color: aliceblue;
    }
</style>
<div class="container">
    <div class="row justify-content-center my-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Edit User') }}</div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Full Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="mobile-number" class="col-md-4 col-form-label text-md-end">{{ __('Mobile Numbersss') }}</label>

                            <div class="col-md-6">
                                <input  id="mobile-number" 
                                        type="text" 
                                        class="form-control @error('mobile_no') is-invalid @enderror" 
                                        name="mobile_no" 
                                        value="{{$user->mobile_no}}"
                                        autofocus>

                                @error('mobile_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3 bg-danger text-white">
                            <label for="credits" class="col-md-4 col-form-label text-md-end">{{ __('Credits') }}</label>
                            <div class="col-md-6">
                                <input  id="credits" 
                                        type="number" 
                                        class="form-control @error('credits') is-invalid @enderror" 
                                        name="credits" 
                                        value="{{isset($user->credits)?$user->credits:0}}"
                                        autofocus>
                                @error('credits')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Total Sales') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="₱{{number_format($total_sales, 2, '.',',')}}"
                                        disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Total Cash In') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="₱{{number_format($total_ci, 2, '.',',')}}"
                                        disabled>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label for="user_attempts" class="col-md-4 col-form-label text-md-end">{{ __('Login Attempts') }}</label>
                            <div class="col-md-6">
                                <input  id="user_attempts" 
                                        type="number" 
                                        class="form-control @error('user_attempts') is-invalid @enderror" 
                                        name="user_attempts" 
                                        value="{{$user->user_attempts}}"
                                        autofocus>
                                @error('user_attempts')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 percentage percentage-container"  <?=$user->account_type=='agent'?'style="display:none;"':''; ?>>
                            <label for="percentage" class="col-md-4 col-form-label text-md-end">{{ __('Percentage (e.g. 45.5% is 0.455)') }}</label>

                            <div class="col-md-6">
                                <input id="percentage" 
                                        type="text" 
                                        class="form-control @error('percentage') is-invalid @enderror" 
                                        name="percentage" 
                                        value="{{$user->percentage}}"
                                        autofocus>

                                @error('percentage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="account_status" class="col-md-4 col-form-label text-md-end">{{ __('Status') }}</label>

                            <div class="col-md-6">
                                <select name="account_status" id="account_status" class="form-control @error('account_status') is-invalid @enderror">
                                    <option value="active" <?=$user->account_status=='active'?'selected':'';?>>Active</option>
                                    <option value="inactive" <?=$user->account_status=='inactive'?'selected':'';?>>Inactive</option>
                                    <option value="spam" <?=$user->account_status=='spam'?'selected':'';?>>Spam Account</option>
                                </select>
                                
                                @error('account_status')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-3">
                            <label for="two_d_bet_limit" class="col-md-4 col-form-label text-md-end">{{ __('2D Bet Limit') }}</label>
                            <div class="col-md-6">
                                <input  id="two_d_bet_limit" 
                                        type="number" 
                                        class="form-control @error('two_d_bet_limit') is-invalid @enderror" 
                                        name="two_d_bet_limit" 
                                        value="{{$user->two_d_bet_limit}}"
                                        autofocus>
                                @error('two_d_bet_limit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="three_d_bet_limit" class="col-md-4 col-form-label text-md-end">{{ __('3D Bet Limit') }}</label>
                            <div class="col-md-6">
                                <input  id="three_d_bet_limit" 
                                        type="number" 
                                        class="form-control @error('three_d_bet_limit') is-invalid @enderror" 
                                        name="three_d_bet_limit" 
                                        value="{{$user->three_d_bet_limit}}"
                                        autofocus>
                                @error('three_d_bet_limit')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Date Registered') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{date_format($user->created_at, 'M d, Y h:iA')}}"
                                        disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('IP Address') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{$user->ip}}"
                                        disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Number of same IPs') }}</label>

                            <div class="col-md-6">
                                <span><a href="{{route('users')}}?q={{$user->ip}}"> {{$number_of_ips}}</a></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Location') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{$user->location}}"
                                        disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('Reference No.') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{$ref_no}}"
                                        disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-md-4 col-form-label text-md-end">{{ __('User ID') }}</label>

                            <div class="col-md-6">
                                <input 
                                        type="text" 
                                        class="form-control" 
                                        value="{{$user->id}}"
                                        disabled>
                            </div>
                        </div>
                        
 
                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" id="rehistro" class="btn btn-primary">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<x-profiles-lib />
@endsection
