<?php

/*
 * @author: Wladimir Schmidt
 */
                                                                        
?><div class="container-fluid survey_controls" style="display: none;">
  <div class="row-fluid">
    <div class="span2">
    <label>Select forms -></label>
    </div>
    <div class="span4 offset2"><select class="survey_select"><?php 
        
     foreach($FORMS as $form){   
        ?><option value="<?php echo $form['survey_form_id']; ?>"><?php echo $form['survey_form_name']; ?></option><?php
     }
     ?>
     <option value="9001">Custom form</option>
     </select></div>
    <div class="span2"><button class="btn btnAddForm">Add form</button></div>
  </div>
  <div class="content_appears_here"></div>
</div>