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

 ?><div class="push"></div>
</div>

<div id="footer">
  <div class="container">
    <p class="muted credit text-center"><?php echo date("Y"); ?> (c) Telecooperation Lab. All rights reserved.</p>
  </div>
</div>

<!-- JS Init -->  
<script src="js/jquery-2.0.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.bootpag.min.js"></script>
<script src="js/moses.js"></script>
<script src="js/jquery.validate.js"></script>
<?php

// check of user an admin, adjust the menu (css via js)!
if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
?>
    <script type="text/javascript">
        $('.navbar').hide();
        $('.navbar .nav > li').css('width','16%');
        $('.navbar').show();
    </script>
<?php
}                                          
?>
</body>
</html>
