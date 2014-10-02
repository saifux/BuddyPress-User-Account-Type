<?php
function buatp_dummy_post(){
    bp_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => __('', 'buddypress' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => 'bp_members',
			'post_status'    => 'publish',
			'is_archive'     => true,
			'comment_status' => 'closed'
		) );
}

//////////////////////////////////////////////////////////////////////////////////////

function buatp_get_template_part(){    
    return bp_buffer_template_part( 'buatp/index' );
}

function buatp_directory_search_text($text){
    global $bp;
    if(!bp_is_buatp_component())
        return $text;
    return 'Search '.$bp->current_action.'...';
}
add_filter('bp_get_search_default_text','buatp_directory_search_text',1,1);
//////////////////////////////////////////////////////////////////////////////////////

function buatp_load_template_filter( $found_template, $templates ) {
	global $bp;
        if ( ! bp_is_current_component( $bp->buatp->slug ) )
		return $found_template;
        
	if ( empty( $found_template ) ) {
            
            bp_register_template_stack( 'buatp_get_template_directory', 14 );
            
            add_action( 'bp_template_include_reset_dummy_post_data','buatp_dummy_post');
            add_filter( 'bp_replace_the_content','buatp_get_template_part');
	}
	return apply_filters( 'buatp_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'buatp_load_template_filter', 10, 2 );

//////////////////////////////////////////////////////////////////////////////////////

function bp_is_buatp_component() {
	$is_buatp_component = bp_is_current_component( 'buatp' );
	return apply_filters( 'bp_is_buatp_component', $is_buatp_component );
}

//////////////////////////////////////////////////////////////////////////////////////

function buatp_get_template_directory(){
    return apply_filters( 'buatp_get_template_directory', BUATP_TEMPLATE );
}
?>