$(document).ready(function(){

    /*
    * SENSORS
    * 
    */
                                        
    /*
    *  Accelerometer
    */

    $('.accelerometer').click(function(){
        $(this).toggle();
        $('.accelerometer_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.accelerometer_pressed').click(function(){
        $(this).toggle();
        $('.accelerometer').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Magnetic field
    */

    $('.magnetic_field').click(function(){
        $(this).toggle();
        $('.magnetic_field_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.magnetic_field_pressed').click(function(){
        $(this).toggle();
        $('.magnetic_field').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Orientation
    */

    $('.orientation').click(function(){
        $(this).toggle();
        $('.orientation_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.orientation_pressed').click(function(){
        $(this).toggle();
        $('.orientation').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Gyroscope
    */

    $('.gyroscope').click(function(){
        $(this).toggle();
        $('.gyroscope_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.gyroscope_pressed').click(function(){
        $(this).toggle();
        $('.gyroscope').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Light
    */

    $('.light').click(function(){
        $(this).toggle();
        $('.light_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.light_pressed').click(function(){
        $(this).toggle();
        $('.light').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Pressure
    */

    $('.pressure').click(function(){
        $(this).toggle();
        $('.pressure_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.pressure_pressed').click(function(){
        $(this).toggle();
        $('.pressure').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Temperature
    */

    $('.temperature').click(function(){
        $(this).toggle();
        $('.temperature_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.temperature_pressed').click(function(){
        $(this).toggle();
        $('.temperature').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Proximity
    */

    $('.proximity').click(function(){
        $(this).toggle();
        $('.proximity_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.proximity_pressed').click(function(){
        $(this).toggle();
        $('.proximity').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Gravity
    */

    $('.gravity').click(function(){
        $(this).toggle();
        $('.gravity_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.gravity_pressed').click(function(){
        $(this).toggle();
        $('.gravity').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Linear acceleration
    */

    $('.linear_acceleration').click(function(){
        $(this).toggle();
        $('.linear_acceleration_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.linear_acceleration_pressed').click(function(){
        $(this).toggle();
        $('.linear_acceleration').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Rotation
    */

    $('.rotation').click(function(){
        $(this).toggle();
        $('.rotation_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.rotation_pressed').click(function(){
        $(this).toggle();
        $('.rotation').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Humidity
    */

    $('.humidity').click(function(){
        $(this).toggle();
        $('.humidity_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.humidity_pressed').click(function(){
        $(this).toggle();
        $('.humidity').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });

    /*
    *  Ambient temperature
    */

    $('.ambient_temperature').click(function(){
        $(this).toggle();
        $('.ambient_temperature_pressed').toggle();
        $(this).parent().find(':checkbox').attr("checked", true);
    });

    $('.ambient_temperature_pressed').click(function(){
        $(this).toggle();
        $('.ambient_temperature').toggle();
        $(this).parent().find(':checkbox').attr("checked", false);
    });


    /*
    *
    * /SENSORS
    */

    $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('maxlength', 6);

    $('.user_apk_restriction').find('input[name=restrict_users_number]').change(
        function() {
            if ($(this).is(':checked')) {
                $('.user_apk_restriction').find('input[name=number_restricted_users]').removeAttr('disabled');
                $('.send_only_to_my_group').removeAttr('disabled');
            } else {
                $('.user_apk_restriction').find('input[name=number_restricted_users]').attr('disabled', true);
                $('.user_apk_restriction').find('input[name=number_restricted_users]').val('');
                $('.send_only_to_my_group').attr('disabled', true);
                $('.send_only_to_my_group').removeAttr('checked');
            }
    });


    $('.radio_join').attr('checked', true);
    $('.join_group').find(':button').text('Join!');

    $('.radio_join').click(function(){
        $('.join_group').find(':button').text('Join!');
    });

    $('.radio_create').click(function(){
        $('.join_group').find(':button').text('Create!');
    });
      

});
