<?php
//Starting the session
session_start();
//import of the header
include_once("./include/_header.php");
  
?>
  
<title>The Mobile Sensing System - Impressum</title>

<?php  
// import of menu
include_once("./include/_menu.php");  
?>  
  
    <!-- Main Block -->
    <div class="hero-unit" style="font-family: "Myriad Pro", "Gill Sans", "Gill Sans MT", Calibri, sans-serif;">
        <h2>Impressum</h2>
        <p> 
               Technische Universität Darmstadt<br>
            Telekooperation<br>
            Immanuel Schweizer <br>
            Hochschulstraße 10 <br>
            64289 Darmstadt <br>
            schweizer@tk.informatik.tu-darmstadt.de<br> 
            www.da-sense.de <br>
        </p>
        <br><p> Disclaimer</p> <br>

        <p>    <b>1.</b> Content 
            The author reserves the right not to be responsible for the topicality, correctness, completeness or quality of the information provided. Liability claims regarding damage caused by the use of any information provided, including any kind of information which is incomplete or incorrect,will therefore be rejected. 
            All offers are not-binding and without obligation. Parts of the pages or the complete publication including all offers and information might be extended, changed or partly or completely deleted by the author without separate announcement.</p> 
            <br>    
        <p>    <b>2.</b> Referrals and links 
            The author is not responsible for any contents linked or referred to from his pages - unless he has full knowledge of illegal contents and would be able to prevent the visitors of his site fromviewing those pages. If any damage occurs by the use of information presented there, only the author of the respective pages might be liable, not the one who has linked to these pages. Furthermore the author is not liable for any postings or messages published by users of discussion boards, guestbooks or mailinglists provided on his page.</p> 
            <br>
        <p>    <b>3.</b> Copyright 
            The author intended not to use any copyrighted material for the publication or, if not possible, to indicatethe copyright of the respective object. 
            The copyright for any material created by the author is reserved. Any duplication or use of objects such as diagrams, sounds or texts in other electronic or printed publications is not permitted without the author s agreement. </p>
            <br>    
        <p>    <b>4.</b> Privacy policy 
            If the opportunity for the input of personal or business data (email addresses, name, addresses) is given, the input of these data takes place voluntarily. The use and payment of all offered services are permitted - if and so far technically possible and reasonable - without specification of any personal data or under specification of anonymized data or an alias. The use of published postal addresses, telephone or fax numbers and email addresses for marketing purposes is prohibited, offenders sending unwanted spam messages will be punished.</p> 
            <br>
        <p>    <b>5.</b> Legal validity of this disclaimer 
            This disclaimer is to be regarded as part of the internet publication which you were referred from. If sections or individual terms of this statement are not legal or correct, the content or validity of the other parts remain uninfluenced by this fact.</p>
        <br />
    </div>
    <!-- / Main Block -->
    
    <hr>

<?php 
  include_once("./include/_login.php");
  //import of the footer to affich the year of project
  include_once("./include/_footer.php");  
?>
<script type="text/javascript">
    // iterate through all menus and remove selection
    $('.dropdown').each(function(){
        $(this).removeClass('active');   
    });
    // add selection for this page
    $('.nav-menu6').addClass('active');
</script>