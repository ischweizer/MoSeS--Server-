<div class="push"></div>
</div>

<div id="footer">
  <div class="container">
    <p class="muted credit text-center"><?php echo date("Y"); ?> (c) Telecooperation Lab. All rights reserved.</p>
  </div>
</div>

<!-- JS Init -->  
<script src="js/jquery-2.0.3.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="//rawgithub.com/botmonster/jquery-bootpag/master/lib/jquery.bootpag.min.js"></script>
<script src="js/moses.js"></script><?php

// check of user an admin, adjust the menu (css via js)!
if(isset($_SESSION["ADMIN_ACCOUNT"]) && $_SESSION["ADMIN_ACCOUNT"] == "YES"){
?>
    <script type="text/javascript">$('.navbar .nav > li').css('width','16%')</script>
<?php
}                                          
?>

<script src="http://jzaefferer.github.com/jquery-validation/jquery.validate.js"></script>
</body>
</html>