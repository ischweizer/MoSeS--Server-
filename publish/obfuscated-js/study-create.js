$('[name="study_period"]').click(function(){$(this).is(":checked")&&(1==$(this).val()&&($('[name="start_date"]').attr("disabled",!1),$('[name="end_date"]').attr("disabled",!1),$('[name="start_after_n_devices"]').attr("disabled",!0),$('[name="running_time"]').attr("disabled",!0),$('[name="running_time_value"]').attr("disabled",!0)),2==$(this).val()&&($('[name="start_date"]').attr("disabled",!0),$('[name="end_date"]').attr("disabled",!0),$('[name="start_after_n_devices"]').attr("disabled",!1),$('[name="running_time"]').attr("disabled",
!1),$('[name="running_time_value"]').attr("disabled",!1)))});function isNumber(a){return!isNaN(parseFloat(a))&&isFinite(a)}
$(".form-horizontal").on("click",".btnCreateOK",function(a){a.preventDefault();if(0==$.trim($('[name="apk_title"]').val()).length)alert("Please enter the apk title.");else{if("1"==$('input[name="study_period"]:checked').val()){if(0==$.trim($('[name="start_date"]').val()).length){alert("Please enter start date.");return}if(0==$.trim($('[name="end_date"]').val()).length){alert("Please enter end date.");return}a=$.datepicker.parseDate("yy-mm-dd",$('[name="start_date"]').val());var c=$.datepicker.parseDate("yy-mm-dd",
$('[name="end_date"]').val());if(0<a.getTime()-c.getTime()){alert("Start date can not be after the end date!");return}}if("2"==$('input[name="study_period"]:checked').val()){a=$.trim($('[name="start_after_n_devices"]').val());if(0==a.length){alert("Please minimum devices to start after.");return}c=$.trim($('[name="running_time"]').val());if(0==c.length){alert("Please enter running time.");return}if(!isNumber(a)||!isNumber(c)){alert("Please enter a number value.");return}if(1>a){alert("Please enter minimum start number greater or equal 1.");
return}if(1>c){alert("Please enter running period greater or equal 1.");return}}a=$.trim($('[name="file"]').val()).toLowerCase();if(a.lastIndexOf("apk")!=a.length-3)alert("Please select an APK file.");else{$("progress").show();$(this).attr("disabled",!0);a=new FormData($("form")[0]);var g={};$(".survey_form").each(function(a,c){var k=$(this),l=parseInt(k.find(".survey_form_id").val()),e=[];k.find(".survey_question").each(function(a,c){var b=$(this),d=[],h=b.parent().find(".survey_question_type").val(),
f=0;b.parent().find(".survey_question_mandatory").is(":checked")&&(f=1);b.parent().find(".survey_answer").each(function(a,c){var b=$(this);0!=b.text().length&&0==b.val().length?d.push(b.text()):d.push(b.val())});0!=b.text().length&&0==b.val().length?e.push({question_type:h,question_mandatory:f,question:b.text(),answers:d}):e.push({question_type:h,question_mandatory:f,question:b.val(),answers:d})});g[a]={survey_form_id:l,survey_form_questions:e}});a.append("survey_json",JSON.stringify(g));$.ajax({url:"content_provider.php",
type:"POST",xhr:function(){var a=$.ajaxSettings.xhr();a.upload&&a.upload.addEventListener("progress",function(a){a.lengthComputable&&$("progress").attr({value:a.loaded,max:a.total})},!1);return a},success:function(a){$("progress").hide();switch(a){case "0":$(".hero-unit").html('<h3 class="text-center">No such user found!</h3>');break;case "1":$(".hero-unit").html('<h3 class="text-center">You created a study "<strong>'+$('[name="apk_title"]').val()+'</strong>"</h3>');break;case "2":$(".hero-unit").html('<h3 class="text-center">That file extension was not accepted by server! Please, use <strong>*.apk</strong></h3>');
break;case "3":$(".hero-unit").html('<h3 class="text-center">The filesize exceeds permitted size!</h3>');break;case "4":$(".hero-unit").html('<h3 class="text-center">Cannot set permission for file on server!</h3>');break;default:$(".hero-unit").html('<h3 class="text-center">Something went wrong with creating user study. Sorry.</h3>')}},data:a,cache:!1,contentType:!1,processData:!1})}}});