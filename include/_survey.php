<div class="container-fluid" name="survey_controls" style="display: none;">
  <div class="row-fluid">
    <div class="span2">
    <label>Please, select your survey -></label>
    </div>
    <div class="span4 offset2"><select id="survey_select"><?php 
        
     foreach($SURVEYS as $SURVEY){   
    
     ?><option value="<?php echo $SURVEY['survey_id']; ?>"><?php echo $SURVEY['survey_name']; ?></option><?php
     
     }
     
     ?>
     <option value="9001">Custom survey</option>
     </select></div>
    <div class="span2"><button class="btn btn-success" name="btnAddSurveyOK">ADD</button></div>
  </div>
  <div id="content_appears_here"></div>
</div>
<?php

?>