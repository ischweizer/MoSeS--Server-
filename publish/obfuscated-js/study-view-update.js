$('[name="btnDownloadApp"]').click(function(a){a.preventDefault();a=$(this).parent().parent().parent();location.href="./apk/"+a.find('[name="userhash"]').val()+"/"+a.find('[name="apkhash"]').val()+".apk"});
$(".btnUpdateSurveyOnly").click(function(a){a.preventDefault();a=$(this).parent().parent().parent();a.find(".survey_content").hide();a.find(".surveyShowHide").hide();a.find(".surveyRemove").hide();a.find(".btnUpdateOK").show();a.find(".btnUpdateCancel").show();a.find('[name="btnAddSurvey"]').show();a.find('[name="btnModifySurvey"]').show();$(this).attr("disabled",!0)});$(".confirm-delete").click(function(a){a.preventDefault();$(".btnConfirm").val($(this).val());$("#modal-from-dom").modal("show")});
$(".btnConfirm").click(function(a){a.preventDefault();a=$(this);a.removeClass("btn-danger");a.attr("disabled",!0);a.text("Working...");$.post("content_provider.php",{study_remove:$(".btnConfirm").val()}).done(function(){location.reload()})});$(".btnConfirmCancel, .close").click(function(){$("#modal-from-dom").modal("hide")});
$('[name="btnUpdateStudy"]').click(function(a){a.preventDefault();a=$(this).parent().parent().parent();a.find('[name="study_title_text"]').hide();a.find('[name="android_version_text"]').hide();a.find('[name="start_date_text"]').hide();a.find('[name="end_date_text"]').hide();a.find('[name="description_text"]').hide();a.find('[name="max_devices_number_text"]').hide();a.find('[name="allowed_join_text"]').hide();a.find('[name="joined_devices_text"]').hide();a.find(".survey_available_text").hide();a.find('[name="private_text"]').hide();
a.find(".survey_content").hide();a.find(".surveyShowHide").hide();a.find(".surveyRemove").hide();a.find('[name="android_version_select"]').show();a.find(".controls :input").show();a.find('[name="study_period_text1"]').show();a.find('[name="study_period_text2"]').show();a.find('[name="start_after_n_devices_text"]').show();a.find('[name="running_time_text"]').show();a.find('[name="description"]').show();a.find('[name="allowed_join"]').show();a.find('[name="invites_only_install"]').show();a.find('[name="private_type"]').show();
a.find('[name="btnAddSurvey"]').show();a.find('[name="btnModifySurvey"]').show();a.find('[name="uploadFile"]').show();a.find(".btnUpdateOK").show();a.find(".btnUpdateCancel").show();$(this).attr("disabled",!0)});
$('[name="btnModifySurvey"]').click(function(a){a.preventDefault();a=$(this).parent();a.find(".survey_question_text").hide();a.find(".survey_content").show();a.find(".survey_question").show();a.find(".survey_question_mandatory_text").show();a.find(".btnRemoveQuestion").show();a.find(".survey_elements_container").parent().show();a.find(".btnRemoveSurvey").parent().show();a.find(".survey_controls").show();a.find(".survey_controls > div:first-child").show();a.find(".survey_answer").attr("disabled",!1)});
$(".form-horizontal").on("click",".btnUpdateOK",function(a){a.preventDefault();var b=null,b="FORM"==$(this).parent().parent().parent().parent().prop("tagName")?$(this).parent().parent().parent().parent():$(this).parent().parent().parent();if(0==$.trim(b.find('[name="apk_title"]').val()).length)alert("Please enter the apk title.");else{if("1"==b.find('input[name="study_period"]:checked').val()){if(0==$.trim(b.find('[name="start_date"]').val()).length){alert("Please enter start date.");return}if(0==
$.trim(b.find('[name="end_date"]').val()).length){alert("Please enter end date.");return}a=$.datepicker.parseDate("yy-mm-dd",b.find('[name="start_date"]').val());var c=$.datepicker.parseDate("yy-mm-dd",b.find('[name="end_date"]').val());if(0<a.getTime()-c.getTime()){alert("Start date can not be after the end date!");return}}if("2"==b.find('input[name="study_period"]:checked').val()){a=$.trim(b.find('[name="start_after_n_devices"]').val());if(0==a.length){alert("Please minimum devices to start after.");
return}c=$.trim(b.find('[name="running_time"]').val());if(0==c.length){alert("Please enter running time.");return}if(!isNumber(a)||!isNumber(c)){alert("Please enter a number value.");return}if(1>a){alert("Please enter minimum start number greater or equal 1.");return}if(1>c){alert("Please enter running period greater or equal 1.");return}}var d=$(this);d.removeClass("btn-success");d.attr("disabled",!0);d.text("Working...");$(this).attr("disabled",!0);a=new FormData(b.parent().find("form")[0]);var g=
{};b.parent().find(".survey_form").each(function(a,b){var c=$(this),d=parseInt(c.find(".survey_form_id").val()),f=[];c.find(".survey_question").each(function(a,b){var c=$(this),d=[],h=c.parent().find(".survey_question_type").val(),e=0;c.parent().find(".survey_question_mandatory").is(":checked")&&(e=1);c.parent().find(".survey_answer").each(function(a,b){var c=$(this);0!=c.text().length&&0==c.val().length?d.push(c.text()):d.push(c.val())});0!=c.text().length&&0==c.val().length?f.push({question_type:h,
question_mandatory:e,question:c.text(),answers:d}):f.push({question_type:h,question_mandatory:e,question:c.val(),answers:d})});g[a]={survey_form_id:d,survey_form_questions:f}});a.append("survey_json",JSON.stringify(g));$.ajax({url:"content_provider.php",type:"POST",xhr:function(){var a=$.ajaxSettings.xhr();a.upload&&a.upload.addEventListener("progress",function(a){a.lengthComputable&&b.find("progress").attr({value:a.loaded,max:a.total})},!1);return a},success:function(a){"1"==a&&location.reload()},
error:function(){d.addClass("btn-success");d.text("Send");d.attr("disabled",!1)},data:a,cache:!1,contentType:!1,processData:!1})}});
$(".form-horizontal").on("click",".btnUpdateOK, .btnUpdateCancel",function(a){a.preventDefault();a=null;a="FORM"==$(this).parent().parent().parent().parent().prop("tagName")?$(this).parent().parent().parent().parent():$(this).parent().parent().parent();a.find('[name="study_title_text"]').show();a.find('[name="android_version_text"]').show();a.find('[name="start_date_text"]').show();a.find('[name="end_date_text"]').show();a.find('[name="description_text"]').show();a.find('[name="max_devices_number_text"]').show();
a.find('[name="allowed_join_text"]').show();a.find('[name="private_text"]').show();a.find('[name="joined_devices_text"]').show();a.find(".survey_available_text").show();a.parent().find(".surveyShowHide").show();a.parent().find(".surveyRemove").show();a.find('[name="progress"]').show();a.find('[name="android_version_select"]').hide();a.find(".controls :input").hide();a.find('[name="study_period_text1"]').hide();a.find('[name="study_period_text2"]').hide();a.find('[name="start_after_n_devices_text"]').hide();
a.find('[name="running_time_text"]').hide();a.find('[name="description"]').hide();a.find('[name="allowed_join"]').hide();a.find('[name="invites_only_install"]').hide();a.find('[name="private_type"]').hide();a.find('[name="uploadFile"]').hide();a.find(".btnUpdateCancel").hide();a.find('[name="progress"]').hide();a.parent().parent().find('[name="btnAddSurvey"]').hide();a.parent().parent().find('[name="btnModifySurvey"]').hide();a.parent().parent().find('[name="btnUpdateStudy"]').attr("disabled",!1);
a.parent().parent().find(".btnUpdateSurveyOnly").attr("disabled",!1)});
$(".form-horizontal").on("click",".btnUpdateCancel",function(a){a.preventDefault();a=null;a="FORM"==$(this).parent().parent().parent().parent().prop("tagName")?$(this).parent().parent().parent().parent():$(this).parent().parent().parent();a.parent().find(".survey_controls").hide();a.find(".survey_question_text").show();a.find(".survey_question").hide();a.find(".survey_answer").attr("disabled",!0);a.find(".survey_question_mandatory").attr("disabled",!0);a.find(".btnRemoveQuestion").hide();a.find(".btnUpdateOK").hide();
a.find(".survey_elements_container").parent().hide();a.find(".btnRemoveSurvey").parent().hide();location.reload()});
$('[name="study_period"]').click(function(){var a=$(this).parent().parent().parent().parent().parent();$(this).is(":checked")&&(1==$(this).val()&&(a.find('[name="start_date"]').attr("disabled",!1),a.find('[name="end_date"]').attr("disabled",!1),a.find('[name="start_after_n_devices"]').attr("disabled",!0),a.find('[name="running_time"]').attr("disabled",!0),a.find('[name="running_time_value"]').attr("disabled",!0)),2==$(this).val()&&(a.find('[name="start_date"]').attr("disabled",!0),a.find('[name="end_date"]').attr("disabled",
!0),a.find('[name="start_after_n_devices"]').attr("disabled",!1),a.find('[name="running_time"]').attr("disabled",!1),a.find('[name="running_time_value"]').attr("disabled",!1)))});
$(".surveyShowHide").click(function(a){a.preventDefault();a=$(this).parent().parent().parent();a.find(".survey_content").is(":visible")?(a.find(".survey_content").hide(),a.find(".survey_controls").hide(),a.find(".surveyShowHide").find("i").addClass("icon-chevron-right"),a.find(".surveyShowHide").find("i").removeClass("icon-chevron-down")):(a.find(".survey_content").show(),a.find(".survey_controls").show(),a.find(".survey_controls > div:first-child").hide(),a.find(".surveyShowHide").find("i").addClass("icon-chevron-down"),
a.find(".surveyShowHide").find("i").removeClass("icon-chevron-right"))});$(".surveyRemove").click(function(a){a.preventDefault();var b=$(this).parent().parent().parent();$.post("content_provider.php",{study_survey_remove:b.find(".surveyRemove").val(),study_survey_remove_code:4931}).done(function(a){a&&1==parseInt(a)?(b.find(".survey_content").remove(),b.find(".surveyShowHide").remove(),b.find(".surveyRemove").remove(),b.find(".survey_available_text").remove()):alert("Cannot remove user study survey! Try again later.")})});
$(".btnSurveyResultsExportCsv").click(function(a){a.preventDefault();location.href="./export.php?id="+$(this).val()+"&m=csv"});