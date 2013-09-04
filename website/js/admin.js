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

// allow scientist
$('.btnAllowAccess').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-warning');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: { 
                'hash': clickedButton.val(),
                'allow': 4343
              }
    }).done(function(result) {
        if(result == '0'){
            clickedButton.addClass('btn-success');
            clickedButton.text('Approved');
            clickedButton.html(clickedButton.html()+' <i class="icon-white icon-ok"></i>');
            
        }
    });
});

// reject scientist
$('.btnRejectAccess').click(function(e){        
    
    e.preventDefault();
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-danger');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: { 
                'hash': clickedButton.val(),
                'reject': 3434
              }
    }).done(function(result) {
        if(result == '0'){
            clickedButton.text('Rejected');
            clickedButton.html(clickedButton.html()+' <i class="icon-ok"></i>');
        }
    });
});

// iterate through all menus and remove selection
$('.dropdown').each(function(){
    $(this).removeClass('active');   
});
// add selection for this page
$('.nav-menu7').addClass('active');
