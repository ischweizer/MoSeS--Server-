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

// Send request to server to save user's profile
$('.btnSaveProfile').click(function(e){        
    
    e.preventDefault();
    
    var password1 = $('[name="password1"]').val();
    var password2 = $('[name="password2"]').val();
    
    if(password1 != password2){
        alert("Please, enter same password twice to proceed.");
        return;
    }
    
    var clickedButton = $(this);
    
    clickedButton.removeClass('btn-success');
    clickedButton.attr('disabled', true);
    clickedButton.text('Working...');
    
    // send
    $.ajax({
        type: "POST",
        url: "content_provider.php",
        data: $('.saveProfileForm').serialize(),
    }).done(function(result) {
        if(result == '0'){
            clickedButton.addClass('btn-success');
            clickedButton.text('Saved!');
            setTimeout(function(){
                clickedButton.attr('disabled', false);
                clickedButton.text('Save Profile');
            },2500);
        }else{
            clickedButton.addClass('btn-success');
            clickedButton.attr('disabled', false);
            clickedButton.text('Save Profile');
            alert("Error while updating your profile: check your internet connection.");
        }
    });
});
