/*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/
/*
 * @author: Wladimir Schmidt
 */

$('.btnInstantScientist').click(function(e){
    e.preventDefault();
    
    $.ajax({
        type: "POST",
        url: 'content_provider.php',
        data: {'instantScientist': 7779},
        success: function(result){
            if(result == "1"){
                location.reload();
            }
        }
       });
});

$('.btnLeaveGroup').click(function(e){
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-danger');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: 'content_provider.php',
        data: {'leaveGroup': $('.btnLeaveGroup').val()},
        success: function(result){
            if(result){
                $('.hero-unit').html('<h3 class="text-center">You left the group '+ result +'!</h3>');
            }
        },
        error: function(){
            clickedButton.addClass('btn-danger');
            clickedButton.attr('disabled', false);
            clickedButton.text('Leave group');
        }
       });
});

$('.btnCreateJoinGroup').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    // create group ajax
    if($('#optionsRadios1').is(':checked')){
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: { 'createGroup': clickedButton.val(),
                    'group_name': $('#group_name').val(),
                    'group_password': $('#group_password').val()}
        }).done(function(result) {
            handleGroupServerAnswer(result);
        });
    }
    
    // join group ajax
    if($('#optionsRadios2').is(':checked')){
        $.ajax({
            type: "POST",
            url: "content_provider.php",
            data: { 'joinGroup': clickedButton.val(),
                    'group_name': $('#group_name').val(),
                    'group_password': $('#group_password').val()}
        }).done(function(result) {
           handleGroupServerAnswer(result);
        });
    }    
});

function handleGroupServerAnswer(result){
    switch(result){
        case '1':   location.href = location.protocol + '//' + location.host + location.pathname;
                    //$('.hero-unit').html('<h2 class="text-center">You successfully joined a group!</h2>'); 
                    break;
        case '2':   $('.hero-unit').html('<h2 class="text-center">Error: That name already exists!</h2>'); 
                    break;
        case '3':   location.href = location.protocol + '//' + location.host + location.pathname;
                    //$('.hero-unit').html('<h2 class="text-center">You successfully created a group!</h2>');
                    break;
        case '4':   $('.hero-unit').html('<h2 class="text-center">Error: Entered group doesn\'t exist!</h2>');
                    break;
        default:    alert('Something went wrong! Try again later.');
                    $('.btnCreateJoinGroup').addClass('btn-success');
                    $('.btnCreateJoinGroup').attr('disabled', false);
                    $('.btnCreateJoinGroup').text('GO');
                    break;
    }
}

// iterate through all menus and remove selection
$('.dropdown').each(function(){
    $(this).removeClass('active');   
});
// add selection for this page
$('.nav-menu3').addClass('active');
