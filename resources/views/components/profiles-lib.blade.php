<script type="text/javascript">
    $(function () {
        $('.coordinator-container').hide();
        $('.master-agent-container').hide();
        
        $('#rehistro').click(function(e) {
            let sub_agent = $('#sub-agent').val();
            let account_type = $('#account-type').val();
            let master_agent = $('#master-agent').val();
            let percentage = $('#percentage').val();
            if(account_type == ''){   
                alert('Please choose an account type.');
                
                return false;
            }
            if(account_type == 'sub_agent'){   
                // alert(master_agent);
                if(master_agent === null){
                    alert('Master agent field is required.');

                    return false;
                } 
                if(percentage == ''){
                    alert('Percentage field is required.');

                    return false;
                } 

                 
            }
            if($('#area').val()===null){
                alert('Area is required.');

                return false;
            }   

            if(account_type == 'master_agent'){   
                if(percentage == ''){
                    alert('Percentage field is required.');

                    return false;
                } 
            }
            if(account_type == 'agent'){   
                if(sub_agent == ''){
                    alert('Agent must be assigned to a Sub Agent.');

                    return false;
                } 
            }

            return true;
        });
        
        $('#account-type').change(function() {
            $('#master-agent').val('').trigger('change');
            $('#sub-agent').val('').trigger('change');
            var type = $(this).val();
            if(type == 'sub_agent') {
                $('.percentage').show();
               $('.master-agents-container').show();
               $('.sub-agents-container').hide();
               $('#percentage').attr('required',true);
            } else if(type == 'agent') {
                $('.percentage').hide();
               $('.sub-agents-container').show();
               $('.master-agents-container').hide();
               $('#percentage').removeAttr('required');
            } else if(type == 'master_agent') {
                $('.percentage').show();
               $('.sub-agents-container').hide();
               $('.master-agents-container').hide();
               $('#percentage').attr('required',true);
            } else {
                $('.percentage').hide();
                $('#percentage').removeAttr('required');
                // $('.sub-agents-container').hide();
            }
        });
    });
</script>