import './bootstrap';
import '../css/app.css'; 
import '../sass/app.scss'
// import Alpine from 'alpinejs';

// window.Alpine = Alpine;

// Alpine.start();

// function getAge(dateString) {
//     var today = new Date();
//     var birthDate = new Date(dateString);
//     var age = today.getFullYear() - birthDate.getFullYear();
//     var m = today.getMonth() - birthDate.getMonth();
//     if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
//         age--;
//     }
//     if(age < 1){
//         return age+' years, '+m+' months';
//     }
    
//     return age;
// }
// //  console.log('age: ' + getAge("1987/09/18"));

//     setInterval(function (){
//           $.ajax({
//               method: "POST",
//               url: "{{route('server.time')}}",
//               data: { time: "trigger" }
//                   })
//               .success(function( msg ) {           
//                   $('#servertime').text(msg.data);        
//               });  
//       },5000);
//     $('#rumble_comb').click(function() {
//         checkInputFields();
//     });
//     $('#straight_com').click(function() {
//         checkInputFields();
//     });
//     $('#draw_time_selector').change(function() { 
//         checkInputFields(); 
//     });
//     $('#draw_time_selector').blur(function() { 
//         let dr_time = this.value;
//         if(dr_time==""){
//             showErrs('Invalid selection.');
//         }
//         $("#draw_time").val(dr_time);
//         checkInputFields(); 
//     });
//     function checkInputFields(){
//         let rumble_comb = $('#rumble_comb').is(':checked');
//         let straight = $('#straight_com').is(':checked');
//         var amt = $("#amount").val();
//         let combination = $("#combination").val();
//         let contact_no = $("#contact_no").val();
//         // var let = $("#amount").val();
//         let draw_time_selector = $('#draw_time_selector').val();
        
//         if(contact_no.length<10){
//             showErrs('Invalid Mobile. Check Again.');

//             return;
//         }
//         if(contact_no==""){
//             showErrs('Contact No. is required');
            
//             return;
//         }
//         if(combination==""){
//             showErrs('Enter a 3D No. Combination');
            
//             return;
//         }
//         if(combination.length<3){
//             showErrs('Invalid 3D no. combination');

//             return;
//         }
//         if(combination.length == 3 && combination[0] == combination[1] && combination[1] == combination[2]){
//             if(rumble_comb===true){
//                 showErrs('Cannot rumble combination.');
//                 $('.add-bet').attr('disabled',true); 
//                 $('#rumble_comb').prop('checked', false);
//                 // $('#straight_com').prop('checked', false);
//                 return;
//             }
//         }
//         if(amt<10){
//             showErrs('Minimum Bet Allowed is P10.00');

//             return;
//         }
//         if(amt>300){
//             showErrs('Maximum Bet Allowed is P300.00');

//             return;
//         }

//         if(amt == null || amt == ""){
//             showErrs('Amount is required.');

//             return;
//         }
//         if(rumble_comb===false && straight===false){
//             showErrs('Choose a Combination Order.');

//             return;
//         }
//         if(draw_time_selector==""){
//             showErrs('Select Draw Time.');

//             return;
//         }
//         showErrs('&nbsp;');
//         $('.add-bet').attr('disabled',false); 
//         $('.display-area').show(); 
//     }
//     function showErrs(errs){
//         $(".irror").html(errs);
//     }
    
        
//     var table = $('.my-bets-table').DataTable({
//         "language": {
//             "emptyTable": "Your Bets are Place Here!"
//         },
//         "pageLength": 5,
//         "order": [0,'desc'],
//         "dom": 'rtip',
//         "info": false,
//         'columnDefs': [ {
//                 'targets': [0,1,2,3,4], // column index (start from 0)
//                 'orderable': false, // set orderable false for selected columns
//         }]
//     });
//     $('#draw_time_selector').focus();
//     $(".my-bets-table").on('click','.remove-bet',function(e){
//         // console.log(this.getAttribute('data-record'));
//         table
//         .row( $(this).parents('tr') )
//         .remove()
//         .draw();
//         let sum = 0;
//         if (table.data().any()) {
//             sum = table.column(2).data().reduce( function ( a, b ) {
//                             return parseInt(a) + parseInt(b);
//                         }); 
//         } else {
//             $('.proceed').attr('disabled',true); 
//             $('#draw_time_selector').attr('disabled',false); 
//         }
//         $(".topay").html(parseInt(sum).toFixed(2));
//     });
 
//     $("#contact_no,#combination,#amount").keyup(function(){
//         checkInputFields();
//     });
