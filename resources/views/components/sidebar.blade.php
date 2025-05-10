@if (auth()->user()->user_level > 50)
    {{-- <li class="nav-item dropdown {{ (request()->is('admin/results/2d') 
                                || request()->is('admin/bets/2d') 
                                || request()->is('admin/hotnumbers/2d')
                                || request()->is('admin/winners/2d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Pick 1G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/bets/1d')}}">1D Bets</a></li>
        <li><a class="dropdown-item" href="{{url('admin/hotnumbers/1d')}}">1D Hot Numbers</a></li>
        <li><a class="dropdown-item" href="{{route('claimed_win_1')}}">1D Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_1')}}">1D Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.1')}}">1D Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.one')}}">1D Top 50 Winners</a></li>
      </ul>
    </li> --}}


    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('cashin')}}">
        Cash-In
      </a>
    </li>
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.cashin')}}">
        Admin Cash-In List
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.net.income')}}">
        Net Income
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.total.gross')}}">
        Total Gross
      </a>
    </li>
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('admin.bets.list')}}">
        Admin Bets
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('users.list')}}">
        Users List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('users.otp.list')}}">
        OTP List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('spam')}}">
        Spam List
      </a>
    </li>
    {{-- End Nav item --}}
    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('statistics')}}">
        Statistics Page
      </a>
    </li>
    {{-- End Nav item --}}
@endif
