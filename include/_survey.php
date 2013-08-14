<div class="container-fluid" name="survey_controls" style="display: none;">
  <div class="row-fluid">
    <div class="span2">
    <label>Select forms -></label>
    </div>
    <div class="span4 offset2"><select id="survey_select"><?php 
        
     foreach($FORMS as $FORM){   
        ?><option value="<?php echo $FORM['survey_form_id']; ?>"><?php echo $FORM['survey_form_name']; ?></option><?php
     }
     ?>
     <option value="9001">Custom form</option>
     </select></div>
    <div class="span2"><button class="btn btn-success btnAddForm">Add form</button></div>
  </div>
  <div id="content_appears_here"></div>
</div>
<?php

?>