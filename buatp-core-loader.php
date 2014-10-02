<?php

if ( !defined( 'ABSPATH' ) ) exit;

class BP_User_Account_Type extends BP_Component {
    function __construct() {
		global $bp;

		parent::start(
			'buatp',
			__( 'User Account Type', 'buatp' ),
			BUATP_ROOT.'_inc'
		);

		$this->includes();
                $bp->active_components[$this->id] = '1';
    }
        
        
    function includes() {

            // Files to include
            $includes = array(
                    '/bp-user-type-screens.php',
                    '/bp-user-type-template.php',
                    '/bp-user-type-functions.php',
                    '/bp-user-type-hooks.php',
                    '/bp-user-type-ajax.php'
            );

            parent::includes( $includes );

            if ( is_admin() || is_network_admin() ) {
                include( BUATP_INC . '/admin/buatp-options.class.php' );
                include( BUATP_INC . '/admin/buatp-admin-page-functions.php' );
                include( BUATP_INC . '/admin/buatp-admin-pages.php' );
            }
    }
        
        
        function setup_globals() {
		global $bp;

		// Defining the slug in this way makes it possible for site admins to override it
		if ( !defined( 'BUATP_SLUG' ) )
			define( 'BUATP_SLUG', $this->id );

		// Set up the $globals array to be passed along to parent::setup_globals()
		$globals = array(
			'slug'                  => BUATP_SLUG,
			'root_slug'             => isset( $bp->pages->{$this->id}->slug ) ? $bp->pages->{$this->id}->slug : BUATP_SLUG,
			'has_directory'         => true, 
			'notification_callback' => 'buatp_format_notifications',
			'search_string'         => __( 'Search Members...', 'buddypress' ),
		);

		// Let BP_Component::setup_globals() do its work.
		parent::setup_globals( $globals );
	}          
}

/////////////////////////////////////////////////////////////////////////////////////////////

function buatp_load_core_component(){
    global $bp;
    $bp->buatp = new BP_User_Account_Type;
}
add_action( 'bp_loaded', 'buatp_load_core_component' );
?>