<?php /*******************************************************************************
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
 ******************************************************************************/ ?>
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
