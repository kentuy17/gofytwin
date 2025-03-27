@if (auth()->user()->userlevel > 50)
    <li class="nav-item dropdown {{ (request()->is('admin/results/2d') 
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
    </li>

    <li class="nav-item dropdown {{ (request()->is('admin/results/2d') 
                                || request()->is('admin/bets/2d') 
                                || request()->is('admin/hotnumbers/2d')
                                || request()->is('admin/winners/2d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Pick 2G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/bets/pick-2D')}}">Pick 2 Bets</a></li>
        <li><a class="dropdown-item" href="{{route('admin.hotnumbers.pick2d')}}">Pick 2 Hot Numbers</a></li>
        <li><a class="dropdown-item" href="{{route('claimed_win_pick2')}}">Pick 2 Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_pick2')}}">Pick 2 Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.pick2')}}">Pick 2 Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.pick2')}}">Pick 2 Top 50 Winners</a></li>
      </ul>
    </li>


    <li class="nav-item dropdown {{ (request()->is('admin/results/2d') 
                                || request()->is('admin/bets/2d') 
                                || request()->is('admin/hotnumbers/2d')
                                || request()->is('admin/winners/2d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Play 2G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/results/2d')}}">2D Results</a></li>
        <li><a class="dropdown-item" href="{{url('admin/bets/2d')}}">2D Bets</a></li>
        <li><a class="dropdown-item" href="{{url('admin/hotnumbers/2d')}}">2D Hot Numbers</a></li>
        <li><a class="dropdown-item" href="{{route('claimed_win_2')}}">2D Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_2')}}">2D Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.2')}}">2D Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.two')}}">2D Top 50 Winners</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/results/3d') 
                                || request()->is('admin/bets/3d') 
                                || request()->is('admin/hotnumbers/3d')
                                || request()->is('admin/winners/3d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Play 3G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/results/3d')}}">3D Results</a></li>
        <li><a class="dropdown-item" href="{{url('admin/bets/3d')}}">3D Bets</a></li>
        <li><a class="dropdown-item" href="{{url('admin/hotnumbers/3d')}}">3D Hot Numbers</a></li>
        <li><a class="dropdown-item" href="{{route('claimed_win_3')}}">3D Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_3')}}">3D Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.3')}}">3D Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.three')}}">3D Top 50 Winners</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/results/4d') 
                                  || request()->is('admin/bets/4d') 
                                  || request()->is('admin/hotnumbers/4d')
                                  || request()->is('admin/winners/4d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Play 4G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/results/4d')}}">4D Results</a></li>
        <li><a class="dropdown-item" href="{{url('admin/bets/4d')}}">4D Bets</a></li>
        <li><a class="dropdown-item" href="{{url('admin/hotnumbers/4d')}}">4D Hot Numbers</a></li>
        <li><a class="dropdown-item" href="{{route('claimed_win_4')}}">4D Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_4')}}">4D Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.4')}}">4D Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.four')}}">4D Top 50 Winners</a></li>
      </ul>
    </li>

    <li class="nav-item dropdown {{ (request()->is('admin/results/6d') 
      || request()->is('admin/bets/6d') 
      || request()->is('admin/hotnumbers/6d')
      || request()->is('admin/winners/6d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Play 6G
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.results.six')}}">6D Results</a></li>
        <li><a class="dropdown-item" href="{{route('admin.bets.six')}}">6D Bets</a></li>
        {{-- <li><a class="dropdown-item" href="{{url('admin/hotnumbers/6d')}}">6D Hot Numbers</a></li> --}}
        <li><a class="dropdown-item" href="{{route('claimed_win_6')}}">6D Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list_win_6')}}">6D Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.6')}}">6D Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.six')}}">6D Top 50 Winners</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/results/6d') 
      || request()->is('admin/bets/6d') 
      || request()->is('admin/hotnumbers/6d')
      || request()->is('admin/winners/6d')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Play Jai Alai
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.results.jai')}}">Jai ALai Results</a></li>
        <li><a class="dropdown-item" href="{{route('admin.bets.jai')}}">Jai ALai Bets</a></li>
        <li><a class="dropdown-item" href="{{route('claimed.win.jai')}}">Jai ALai Claimed Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.win.jai')}}">Jai ALai Winners</a></li>
        <li><a class="dropdown-item" href="{{route('list.complete.win.jai')}}">Jai ALai Completed Transactions</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.top.jai')}}">Jai ALai Top 50 Winners</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/users') 
      || request()->is('admin/users/attempts')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Winners Board
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.winners.board.1d')}}">Pick 1G Winners</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.board.pick2')}}">Pick 2G Winners </a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.board.2d')}}">Play 2G Winners</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.board.3d')}}">Play 3G Winners</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.board.4d')}}">Play 4G Winners</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.board.6d')}}">Play 6G Winners</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/users') 
      || request()->is('admin/users/attempts')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Ranked Bets
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        
        <li><a class="dropdown-item" href="{{route('admin.winners.ranked.1d')}}">Pick 1G Bets</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.ranked.pick2')}}">Pick 2G Bets</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.ranked.2d')}}">Play 2G  Bets</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.ranked.3d')}}">Play 3G Bets</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.ranked.4d')}}">Play 4G Bets</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/users') 
      || request()->is('admin/users/attempts')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Money Board
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.ci.board')}}">Cashin Board</a></li>
        <li><a class="dropdown-item" href="{{route('admin.credits.board')}}">Credits Board</a></li>
      </ul>
    </li>

    {{-- Nav item Cash-Ins--}}
    <li class="nav-item dropdown {{ (request()->is('admin/cash-in') 
      || request()->is('admin/cash-in')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Cash-Ins
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.cashin')}}">Cash-In List</a></li>
        <li><a class="dropdown-item" href="{{route('admin.cashin.weekly')}}">Weekly Cash-Ins</a></li>
        <li><a class="dropdown-item" href="{{route('admin.cashin.bi.weekly')}}">Two Weeks Cash-Ins</a></li>
        <li><a class="dropdown-item" href="{{route('admin.cashin.top.fifty')}}">Top 50 Cash-Ins</a></li>
        <li><a class="dropdown-item" href="{{route('csr.cashin')}}">Manual Cash Ins</a></li>
        <li><a class="dropdown-item" href="{{route('csr.cashin.completed')}}">Completed Manual Cash Ins</a></li>
      </ul>
    </li>
    {{-- End Nav item --}}

    {{-- Nav item Users--}}
    <li class="nav-item dropdown {{ (request()->is('admin/users') 
      || request()->is('admin/users/attempts')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Users
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('users')}}">Users List</a></li>
        <li><a class="dropdown-item" href="{{route('user.attempts')}}">User Attempts List</a></li>
        <li><a class="dropdown-item" href="{{route('websites')}}">Websites</a></li>
        <li><a class="dropdown-item" href="{{route('websites.register')}}">Website Registers</a></li>
        <li><a class="dropdown-item" href="{{route('spam')}}">Spam Users</a></li>
        <li><a class="dropdown-item" href="{{route('inactive')}}">Inactive Users</a></li>
        <li><a class="dropdown-item" href="{{route('admin.verification.codes')}}">Verification Codes</a></li>
        <li><a class="dropdown-item" href="{{route('admin.promos')}}">Promos</a></li>
        <li><a class="dropdown-item" href="{{route('users.info')}}">User Search</a></li>
        <li><a class="dropdown-item" href="{{route('admin.registered.today')}}">Registered Today</a></li>
        <li><a class="dropdown-item" href="{{route('admin.cashin.today')}}">List of Successful Cashiners</a></li>
      </ul>
    </li>
     {{-- End Nav item --}}

    {{-- Nav item --}}
    <li class="nav-item">
      <a class="nav-link" href="{{route('agents')}}">
        Teller Agents
      </a>
    </li>
    {{-- End Nav item --}}

    {{-- Nav item Referral Agents--}}
    <li class="nav-item dropdown {{ 
      request()->is('admin/referrals/master-agents') || 
      request()->is('admin/claim/requests') || 
      request()->is('admin/referrals/sub-agents') || 
      request()->is('admin/claim/requests/list') ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Referral Agents
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
      <li><a class="dropdown-item" href="{{route('master.agents')}}">Master Agents</a></li>
      <li><a class="dropdown-item" href="{{route('sub.agents')}}">Sub Agents</a></li>
      <li><a class="dropdown-item" href="{{route('withdrawal')}}">Withdraw Request</a></li>
      <li><a class="dropdown-item" href="{{route('withdrawal.list')}}">Agent Withdraw List</a></li>
      </ul>
    </li>
    {{-- End Nav item --}}


    {{-- Nav item --}}
    <li class="nav-item dropdown {{ (request()->is('/admin/gross/monthly') 
      || request()->is('/admin/gross/monthly')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Gross Report
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.gross.monthly')}}">Monthly Gross</a></li>
        
      </ul>
    </li>
    {{-- ENd Nav item --}}
    
    {{-- Nav item Summary Reports--}}
    <li class="nav-item dropdown {{ (
                                    request()->is('admin/accounting/activity-report') || 
                                    request()->is('admin/accounting/betting-report') || 
                                    request()->is('admin.claim.report')
                                    ) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Summary Reports
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.operations.report')}}">Activity Report</a></li>
        <li><a class="dropdown-item" href="{{route('admin.betting.report')}}">Betting Report</a></li>
        <li><a class="dropdown-item" href="{{route('admin.claim.report')}}">Claim Report</a></li>
        <li><a class="dropdown-item" href="{{route('admin.winners.monthly')}}">Winners Report</a></li>
        <li><a class="dropdown-item" href="{{route('admin.daily.active.bettors')}}">Daily Bets Report</a></li>
      </ul>
    </li>
    {{-- ENd Nav item --}}


    <li class="nav-item dropdown {{ (
                                    request()->is('admin/accounting/cash-outs/form') || 
                                    request()->is('admin/accounting/report') || 
                                    request()->is('admin/accounting/cash-outs')
                                    ) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
      Withdrawals
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('admin.cashouts.form')}}">Cash Out Request</a></li>
        <li><a class="dropdown-item" href="{{route('admin.cashouts')}}">Withrawal Requests</a></li>
        <li><a class="dropdown-item" href="{{route('admin.accounting.report')}}">Report</a></li>
      </ul>
    </li>
    
    <li class="nav-item dropdown {{ (request()->is('admin/core/statistics') 
                                  || request()->is('admin/core/statistics/gross-income') 
                                  || request()->is('admin/core/statistics/won-amount') 
                                  || request()->is('admin/core/statistics/bettors') 
                                  || request()->is('admin/core/statistics/winners') 
                                  || request()->is('admin/core/statistics/prizes-claimed')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Statistics
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{route('statistics.new')}}">Statistics Report</a></li>
        <li><a class="dropdown-item" href="{{url('admin/core/statistics/gross-income')}}">Gross Income</a></li>
        <li><a class="dropdown-item" href="{{url('admin/core/statistics/won-amount')}}">Won Amount</a></li>
        <li><a class="dropdown-item" href="{{url('admin/core/statistics/bettors')}}">Bettors</a></li>
        <li><a class="dropdown-item" href="{{url('admin/core/statistics/winners')}}">Winners</a></li>
        <li><a class="dropdown-item" href="{{url('admin/core/statistics/prizes-claimed')}}">Prizes Claimed</a></li>
        <li><a class="dropdown-item" href="{{route('statistics')}}">Old Statistics Report</a></li>
      </ul>
    </li>
    <li class="nav-item dropdown {{ (request()->is('admin/general-settings') 
                                || request()->is('admin/tipster-settings') 
                                || request()->is('admin/2d-settings') 
                                || request()->is('admin/3d-settings') 
                                || request()->is('admin/4d-settings')
                                || request()->is('admin/6d-settings')
                                || request()->is('admin/logs')) ? 'active' : '' }}">
      <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
        Settings
      </a>
      <ul class="dropdown-menu" aria-labelledby="navbarScrollingDropdown">
        <li><a class="dropdown-item" href="{{url('admin/general-settings')}}">General Settings</a></li>
        <li><a class="dropdown-item" href="{{url('admin/tipster-settings')}}">Tipster Settings</a></li>
        <li><a class="dropdown-item" href="{{route('settings.one')}}">Pick 1 Settings</a></li>
        <li><a class="dropdown-item" href="{{route('settings.pick2')}}">Pick 2 Settings</a></li>
        <li><a class="dropdown-item" href="{{url('admin/2d-settings')}}">2D Settings</a></li>
        <li><a class="dropdown-item" href="{{url('admin/3d-settings')}}">3D Settings</a></li>
        <li><a class="dropdown-item" href="{{url('admin/4d-settings')}}">4D Settings</a></li>
        <li><a class="dropdown-item" href="{{route('settings.six')}}">6D Settings</a></li>
        <li><a class="dropdown-item" href="{{url('admin/logs')}}">Logs</a></li>
        <li><a class="dropdown-item" href="{{route('activity.logs')}}">Admin Activity Logs</a></li>
      </ul>
    </li>
@endif
