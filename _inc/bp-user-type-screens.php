<?php

function buatp_directory_setup() {
    $buatp_general_settings = get_option('buatp_basic_setting',true);
    global $bp;
    if ( bp_is_buatp_component() && !$bp->current_action) {
        if($buatp_general_settings['buatp_default_type_selection'])
            bp_core_redirect(buatp_get_type_directory_url($buatp_general_settings['buatp_default_type_selection']));
        else
            bp_core_redirect(site_url());
    }else if(bp_is_buatp_component() && $bp->current_action){
        if( $bp->buatp->directory_id ){
            do_action( 'buatp_directory_setup' );
            bp_core_load_template( apply_filters( 'buatp_directory_template', 'members/members-loop' ) );
        }
        else{
            bp_core_redirect(site_url());
        }
            
    }
}
add_action( 'bp_screens', 'buatp_directory_setup' );


?>