<script type="text/javascript">
    $(function () {
        $(".datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
        });
        totalReports();
        getTotalBettors();
        getTotalAgentEarnings();
        getInactive();
        setInterval(function (){
            checkBalance();
        },5000);
        setInterval(function (){
            getTotalBettors();
            getInactive();
        },30000);
    });
    function checkBalance(){
        $.ajax({
            method: "POST",
            url: "{{route('check.balance')}}",
            data: { user_id: "data" },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
                })
            .success(function( msg ) {           
                user_balance_maya = msg.data.paymaya;
                user_balance_gcash = msg.data.gcash;  
                gcash_qr = msg.data.gcash_qr;  
                paymaya_qr = msg.data.paymaya_qr;  
                total_wallet = msg.data.total_wallet;  
                total_manual_wallet = msg.data.total_manual_wallet;  
                total_pg1_wallet = msg.data.total_pg1_wallet;  
                total_pg3_wallet = msg.data.total_pg3_wallet;  
                pg_1_wallet = msg.data.pg_1_wallet;  
                actual_live_profit = msg.data.actual_live_profit;  
                opex_withdrawals = msg.data.opex_withdrawals;  


                $("#opex_withdrawals").text(opex_withdrawals);
                $("#actual_live_profit").text(actual_live_profit);
                $("#pg_1_wallet").text(pg_1_wallet);
                $("#gcash_amount").text(user_balance_gcash);
                $("#paymaya_amount").text(user_balance_maya);
                $("#gcash_qr").text(gcash_qr);
                $("#paymaya_qr").text(paymaya_qr);
                $("#total_wallet").text(total_wallet);
                $("#total_manual_wallet").text(total_manual_wallet);
                $("#total_pg3_wallet").text(total_pg3_wallet);
                $("#total_pg1_wallet").text(total_pg1_wallet);
        }); 
    }
    function getTotalBettors(){
        $.ajax({
            method: "POST",
            url: "{{route('get.bettors')}}",
            data: { 
                user_id: "data", 
                from: $('.from').val(), 
                to: $('.to').val(), 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
                })
            .success(function( msg ) {    
                total_bettors = msg.data.total_bettors;  
                total_cashin = msg.data.total_cashin;  
                daily_ci = msg.data.daily_ci;  
                daily_ci_adjusted = msg.data.daily_ci_adjusted;  
                total_bets = msg.data.total_bets;  
                total_users = msg.data.total_users;  
                gross_amount = msg.data.gross_amount;  
                withdrawal_request = msg.data.withdrawal_request;  
                master_agent_earnings = msg.data.master_agent_earnings;  
                sub_agent_earnings = msg.data.sub_agent_earnings;  
                total_agents = msg.data.total_agents;  
                total_4d_bettors = msg.data.total_4d_bettors;  
                


                total_3d_2pm_bettors = msg.data.total_3d_2pm_bettors;  
                total_3d_bettors = msg.data.total_3d_bettors;  
                total_3d_5pm_bettors = msg.data.total_3d_5pm_bettors;  
                total_3d_9pm_bettors = msg.data.total_3d_9pm_bettors;  
                total_6d_bettors = msg.data.total_6d_bettors;  
                daily_won_amount = msg.data.daily_won_amount;  
                daily_2d_total_amount = msg.data.daily_2d_total_amount;  
                daily_2d_won_amount = msg.data.daily_2d_won_amount;  
                daily_3d_won_amount = msg.data.daily_3d_won_amount;  
                daily_4d_won_amount = msg.data.daily_4d_won_amount;  
                daily_6d_won_amount = msg.data.daily_6d_won_amount;  
                daily_3d_total_amount = msg.data.daily_3d_total_amount;  
                daily_4d_total_amount = msg.data.daily_4d_total_amount;  
                daily_6d_total_amount = msg.data.daily_6d_total_amount;  
                daily_2d_2pm_amount = msg.data.daily_2d_2pm_amount;  
                daily_2d_2pm_won_amount = msg.data.daily_2d_2pm_won_amount;  
                daily_2d_5pm_amount = msg.data.daily_2d_5pm_amount;  
                daily_2d_5pm_won_amount = msg.data.daily_2d_5pm_won_amount;  
                daily_2d_9pm_amount = msg.data.daily_2d_9pm_amount;  
                daily_2d_9pm_won_amount = msg.data.daily_2d_9pm_won_amount;  
                daily_3d_2pm_won_amount = msg.data.daily_3d_2pm_won_amount;  
                daily_3d_5pm_won_amount = msg.data.daily_3d_5pm_won_amount;  
                daily_3d_9pm_won_amount = msg.data.daily_3d_9pm_won_amount;  
                daily_3d_2pm_amount = msg.data.daily_3d_2pm_amount;  
                daily_3d_5pm_amount = msg.data.daily_3d_5pm_amount;  
                daily_3d_9pm_amount = msg.data.daily_3d_9pm_amount;  
                givaways_amount = msg.data.givaways_amount;  
                overall_gross = msg.data.overall_gross;  
                total_ci_promo = msg.data.total_ci_promo;  
                free_credits_amount = msg.data.free_credits_amount;  
                agent_commision = msg.data.agent_commision;  
                pg_1_usercount = msg.data.pg_1_usercount;  
                pg_3_usercount = msg.data.pg_3_usercount;  
                ci_usercount = msg.data.ci_usercount;  
                winners_2_g = msg.data.winners_2_g;  
                winners_3_g = msg.data.winners_3_g;  
                winners_4_g = msg.data.winners_4_g;  
                winners_6_g = msg.data.winners_6_g;  
                total_no_agents = msg.data.total_no_agents;  

                
                //Pick 1
                daily_1d_total_amount = msg.data.daily_1d_total_amount;
                daily_1d_won_amount = msg.data.daily_1d_won_amount;
                daily_1d_2pm_amount = msg.data.daily_1d_2pm_amount;
                daily_1d_2pm_won_amount = msg.data.daily_1d_2pm_won_amount;
                daily_1d_5pm_amount = msg.data.daily_1d_5pm_amount;
                daily_1d_5pm_won_amount = msg.data.daily_1d_5pm_won_amount;
                daily_1d_9pm_amount = msg.data.daily_1d_9pm_amount;
                daily_1d_9pm_won_amount = msg.data.daily_1d_9pm_won_amount;

                
                $("#daily_1d_total_amount").text(daily_1d_total_amount);
                $("#daily_1d_won_amount").text(daily_1d_won_amount);
                $("#daily_1d_2pm_amount").text(daily_1d_2pm_amount);
                $("#daily_1d_2pm_won_amount").text(daily_1d_2pm_won_amount);
                $("#daily_1d_5pm_amount").text(daily_1d_5pm_amount);
                $("#daily_1d_5pm_won_amount").text(daily_1d_5pm_won_amount);
                $("#daily_1d_9pm_amount").text(daily_1d_9pm_amount);
                $("#daily_1d_9pm_won_amount").text(daily_1d_9pm_won_amount);


                //Pick 2
                daily_pick2_total_amount = msg.data.daily_pick2_total_amount;
                daily_pick2_won_amount = msg.data.daily_pick2_won_amount;
                daily_pick2_2pm_amount = msg.data.daily_pick2_2pm_amount;
                daily_pick2_2pm_won_amount = msg.data.daily_pick2_2pm_won_amount;
                daily_pick2_5pm_amount = msg.data.daily_pick2_5pm_amount;
                daily_pick2_5pm_won_amount = msg.data.daily_pick2_5pm_won_amount;
                daily_pick2_9pm_amount = msg.data.daily_pick2_9pm_amount;
                daily_pick2_9pm_won_amount = msg.data.daily_pick2_9pm_won_amount;


                
                $("#daily_pick2_total_amount").text(daily_pick2_total_amount);
                $("#daily_pick2_won_amount").text(daily_pick2_won_amount);
                $("#daily_pick2_2pm_amount").text(daily_pick2_2pm_amount);
                $("#daily_pick2_2pm_won_amount").text(daily_pick2_2pm_won_amount);
                $("#daily_pick2_5pm_amount").text(daily_pick2_5pm_amount);
                $("#daily_pick2_5pm_won_amount").text(daily_pick2_5pm_won_amount);
                $("#daily_pick2_9pm_amount").text(daily_pick2_9pm_amount);
                $("#daily_pick2_9pm_won_amount").text(daily_pick2_9pm_won_amount);




                $("#total_no_agents").text(total_no_agents);
                $("#winners_6_g").text(winners_6_g);
                $("#winners_4_g").text(winners_4_g);
                $("#winners_3_g").text(winners_3_g);
                $("#winners_2_g").text(winners_2_g);
                $("#ci_usercount").text(ci_usercount);
                $("#pg_3_usercount").text(pg_3_usercount);
                $("#pg_1_usercount").text(pg_1_usercount);
                $("#free_credits_amount").text(free_credits_amount);
                $("#total_ci_promo").text(total_ci_promo);
                $("#overall_gross").text(overall_gross);
                $("#givaways_amount").text(givaways_amount);
                $("#daily_3d_9pm_amount").text(daily_3d_9pm_amount);
                $("#daily_3d_5pm_amount").text(daily_3d_5pm_amount);
                $("#daily_3d_2pm_amount").text(daily_3d_2pm_amount);
                $("#daily_3d_9pm_won_amount").text(daily_3d_9pm_won_amount);
                $("#daily_3d_5pm_won_amount").text(daily_3d_5pm_won_amount);
                $("#daily_3d_2pm_won_amount").text(daily_3d_2pm_won_amount);
                $("#daily_2d_9pm_won_amount").text(daily_2d_9pm_won_amount);
                $("#daily_2d_9pm_amount").text(daily_2d_9pm_amount);
                $("#daily_2d_5pm_won_amount").text(daily_2d_5pm_won_amount);
                $("#daily_2d_5pm_amount").text(daily_2d_5pm_amount);
                $("#daily_2d_2pm_won_amount").text(daily_2d_2pm_won_amount);
                $("#daily_2d_2pm_amount").text(daily_2d_2pm_amount);
                $("#daily_6d_total_amount").text(daily_6d_total_amount);
                $("#daily_4d_total_amount").text(daily_4d_total_amount);
                $("#daily_3d_total_amount").text(daily_3d_total_amount);
                $("#daily_6d_won_amount").text(daily_6d_won_amount);
                $("#daily_4d_won_amount").text(daily_4d_won_amount);
                $("#daily_3d_won_amount").text(daily_3d_won_amount);
                $("#daily_2d_won_amount").text(daily_2d_won_amount);
                $("#daily_2d_total_amount").text(daily_2d_total_amount);
                $("#daily_won_amount").text(daily_won_amount);
                $("#total_6d_bettors").text(total_6d_bettors);
                $("#total_4d_bettors").text(total_4d_bettors);
                $("#total_3d_9pm_bettors").text(total_3d_9pm_bettors);
                $("#total_3d_5pm_bettors").text(total_3d_5pm_bettors);
                $("#total_3d_bettors").text(total_3d_bettors);
                $("#total_3d_2pm_bettors").text(total_3d_2pm_bettors);

                total_2d_2pm_bettors = msg.data.total_2d_2pm_bettors;  
                total_2d_bettors = msg.data.total_2d_bettors;  
                total_2d_5pm_bettors = msg.data.total_2d_5pm_bettors;  
                total_2d_9pm_bettors = msg.data.total_2d_9pm_bettors;  
                
                $("#total_2d_9pm_bettors").text(total_2d_9pm_bettors);
                $("#total_2d_5pm_bettors").text(total_2d_5pm_bettors);
                $("#total_2d_bettors").text(total_2d_bettors);
                $("#total_2d_2pm_bettors").text(total_2d_2pm_bettors);




                $("#total_agents").text(total_agents);
                $("#sub_agent_earnings").text(sub_agent_earnings);
                $("#master_agent_earnings").text(master_agent_earnings);
                $("#withdrawal_request").text(withdrawal_request);
                $("#gross_amount").text(gross_amount);
                $("#total_users").text(total_users);
                $("#total_bets").text(total_bets);
                $("#total_bettors").text(total_bettors);
                $("#total_cashin").text(total_cashin);
                $("#daily_ci").text(daily_ci);
                $("#daily_ci_adjusted").text(daily_ci_adjusted);
                $("#agent_commision").text(agent_commision);
        }); 
    }
    function getTotalAgentEarnings(){
        $.ajax({
            method: "POST",
            url: "{{route('get.total.agents.earnings')}}",
            data: { 
                user_id: "data", 
                from: $('.from').val(), 
                to: $('.to').val(), 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
                })
            .success(function( msg ) {    
                total_agent_earnings = msg.data.total_agent_earnings;  
                
                $("#total_agent_earnings").text(total_agent_earnings);
        }); 
    }
    function totalReports(){
        $.ajax({
            method: "POST",
            url: "{{route('get.total.agents.earnings')}}",
            data: { 
                user_id: "data", 
                from: $('.from').val(), 
                to: $('.to').val(), 
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
                })
            .success(function( msg ) {    
                total_agent_earnings = msg.data.total_agent_earnings;  
                
                $("#total_agent_earnings").text(total_agent_earnings);
        }); 
    }
    function getInactive(){
        $.ajax({
            method: "POST",
            url: "{{route('get.inactive')}}",
            data: { 
                user_id: "data",
                from: $('.from').val(), 
                to: $('.to').val(),  
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
                })
            .success(function( msg ) {    
                inactive_players = msg.data.inactive_players;  
                new_registers = msg.data.new_registers;  
                
                $("#new_registers").text(new_registers);
                $("#inactive_players").text(inactive_players);
                daily_new_registers = msg.data.daily_new_registers;  
                $("#daily_new_registers").text(daily_new_registers);
                
        }); 
    }
    
</script>