jQuery(document).ready(function() {	
    function migrate_from_wp_user_avatar() {
        jQuery.ajax({
            url: ajaxurl,type:'GET', timeout: 30000,
            dataType: 'html',
            data: 'action=basic_user_avatars_wp_user_avatar_migrate',
            error: function(xml){
                alert('Error with update. Try refreshing.');				
            },
            success: function(responseHTML){
                console.log(responseHTML);
                if(responseHTML.indexOf('[done]') > -1) {
                    jQuery('#basic_user_avatars_updates_intro').html('Migration complete.');
                } else {
                    jQuery('#basic_user_avatars_updates_intro').append(responseHTML);
                    $update_timer = setTimeout(function() { migrate_from_wp_user_avatar();}, 200);
                }
            }
        });
    }

		var $update_timer = setTimeout(function() { migrate_from_wp_user_avatar();}, 200);
    });