<?php

function buatp_get_all_field_groups($condition = ''){
    global $wpdb;
    $query = "SELECT * FROM ".$wpdb->base_prefix."bp_xprofile_groups $condition";
    $groups = $wpdb->get_results($query, ARRAY_A);
    foreach((array) $groups as $group){
        $ids[$i++] = $group['id'];
    }
    return (array) $ids;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_fields($condition = "WHERE parent_id = 0" , $return = 'name'){
   global $wpdb;
   $query="SELECT * FROM ".$wpdb->base_prefix."bp_xprofile_fields $condition";
   $fields=$wpdb->get_results($query,ARRAY_A);
   if(!count($fields))
       return array();
   foreach($fields as $field) {
       $name = $field[$return];
       $arr[$name] = $name;
   }
   return $arr;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_profile_groups(){
    if(class_exists('BP_XProfile_Group')) {
      $group_obj = BP_XProfile_Group::get();
      foreach($group_obj as $groups ){
          if(!$i){
              $i++;
              continue;
          }
          $arr[$groups->id] = $groups->name;
          
      }
      return (array)$arr;
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_select_box_fields() {
   global $wpdb;
   $query="SELECT * FROM ".$wpdb->base_prefix."bp_xprofile_fields WHERE type='selectbox'";
   return buatp_get_all_fields("WHERE type='selectbox'");
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_check_type_field_exist(){
   $fields = buatp_get_all_select_box_fields();
   if(is_array($fields) && count($fields))
       return true;
   else
       return false;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_root_page($return = 'name'){
    global $bp;
    if(!$return)
        $return = 'name';
    $buatp_page = $bp->pages->buatp;
   // echo $buatp_page->{$return};
    if($buatp_page->{$return})
        return $buatp_page->{$return};
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_type_directory_slug($type){
    if(!$type)
        return;
    $buatp_general_settings = get_option('buatp_basic_setting',true);
    if($buatp_general_settings['buatp_slug_selection_for_'.buatp_get_field_id_by_name($type)])
        return $buatp_general_settings['buatp_slug_selection_for_'.buatp_get_field_id_by_name($type)];
    else
        return buatp_text_to_slug($type);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_type_directory_url($type){
    if(!$type)
        return;
    $type_slug = buatp_get_type_directory_slug($type);
    $root_page = buatp_get_root_page('slug');
    return site_url().'/'.$root_page.'/'.$type_slug;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_text_to_slug($text){
    return preg_replace('/[^a-z0-9_]/i','-', strtolower($text));
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_types($field_id , $selection = '*', $output = 'multidimantion'){
    global $wpdb;
    $query="SELECT $selection FROM ".$wpdb->base_prefix."bp_xprofile_fields WHERE type='option' AND parent_id='".$field_id."'";
    $types=$wpdb->get_results($query,ARRAY_A);
    if(count($types)){
        if($output == 'multidimantion')
        return $types;
        else {
            foreach ((array)$types as $val) {
                $arr[$val[$selection]] = $val[$selection];
            }
            return (array) $arr;
        }
    }
    else
        return false;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_field_id_by_name($name) {
    return xprofile_get_field_id_from_name($name);
}

//////////////////////////////////////////////////////////////////////////////////////////////


function buatp_get_field_name_by_id($field_id){
    return xprofile_get_field($field_id)->name;
}
//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_convert_fields_name_to_id($fields) {
    if(!$fields)
        return;
    foreach((array)$fields as $index => $name){
        $arr[$index] = buatp_get_field_id_by_name($name);
    }
    return $arr;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_roles() {
	$editable_roles = get_editable_roles();
        foreach ( $editable_roles as $role => $details ) {
		$name = translate_user_role($details['name'] );
                $arr[esc_attr($role)] = $name;
	}
	return array_reverse((array)$arr);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_dir_name(){
    global $bp;
    if(!bp_is_buatp_component())
        return false;
    $type_id = $bp->buatp->directory_id;
    
    return buatp_get_field_name_by_id($type_id);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_the_dir_name(){
    echo buatp_get_dir_name();
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_all_users_by_type($type_name){
    global $wpdb;
    $buatp_general_settings = get_option('buatp_basic_setting',true);
    $field_id = buatp_get_field_id_by_name($buatp_general_settings['buatp_type_field_selection']);
    $query = "SELECT user_id FROM ".$wpdb->base_prefix."bp_xprofile_data WHERE field_id = $field_id AND value = '$type_name'";
    $users = $wpdb->get_results($query,ARRAY_A);
    if(!count($users))
        return 0;
    foreach($users as $user) {
        $ids[$i++] = $user['user_id'];
    }
    return apply_filters('buatp_get_all_users_by_type',$ids);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_current_page_type() {
    global $bp;
    if ( !bp_is_buatp_component() || !$bp->current_action) 
    return;
    $type_id = $bp->buatp->directory_id;
    return buatp_get_field_name_by_id($type_id);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_filtered_members($return = 'exclude' , $type_name = '' , $query = '') {
    if(!$type_name)
        $type_name = buatp_current_page_type();
    if(!$type_name && $return != 'all')
        return;
    if(!$query)
        $query = 'type=alphabetical&per_page=false';
    $users = (array) buatp_get_all_users_by_type($type_name);
    if ( bp_has_members( $query ) ): 
        while ( bp_members() ) : bp_the_member(); $i++;
           if(!in_array(bp_get_member_user_id(), $users)) {
                   $excludes[$i] = (int)bp_get_member_user_id(); 
           }
           else { 
                    $includes[$i] = (int)bp_get_member_user_id();
           }
        endwhile;
    endif;
    if($return == 'exclude')
        return (array) $excludes;
    else if($return == 'include')
        return (array) $includes;
    else 
        return array_merge((array) $excludes , (array) $includes); 
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_field_data($field_name,$user_id){
    if(!$field_name || !$user_id)
        return;
   $data = xprofile_get_field_data( $field_name, $user_id , 'comma' );
   return $data;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_user_type($user_id){
    $settings_basic = get_option('buatp_basic_setting',false);
    if(!$user_id || !$settings_basic)
        return;
    return buatp_get_field_data($settings_basic['buatp_type_field_selection'], $user_id);
}
//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_set_field_data($field_name,$user_id,$value){
    if(!$field_name || !$user_id)
        return;
    return xprofile_set_field_data($field_name,$user_id,$value,false);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_update_user_role($user_id,$role) {
    if(!$user_id || !$role)
        return;
    $user = new WP_User( $user_id );
    $user->set_role($role);
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_user_role($user_id){
    if(!$user_id)
        return;
    $user = new WP_User( $user_id );
    return $user->roles[1];
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_full_url(){
    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
    $port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
    return $protocol . "://" . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_prepare_url( $url = '' ){
    global $bp;
    if( !$url )
        return;
    if( $url == 'current' ){
    $url = str_replace(site_url(),'',buatp_get_full_url()) ;
    }
    $current_user = get_userdata($bp->displayed_user->id);
    if(function_exists('bp_get_current_group_slug'))
        $current_group_slug = bp_get_current_group_slug();
    $bp_components = array(
        '[user_name]' => $current_user->user_login,
        '[group_name]' => $current_group_slug
    );
    foreach( $bp_components as $code => $val ){
        if( strpos($url, $code )  && $val  )  {
            $url = str_replace ($code, $val, $url);
        }
    }
    return $url;
}

//////////////////////////////////////////////////////////////////////////////////////////////

function buatp_generate_fields($query = ''){
 $html = '';
 ob_start();
 if ( bp_has_profile( $query ) ) : while ( bp_profile_groups() ) : bp_the_profile_group();
        while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
            <div class="editfield">

                    <?php if ( 'textbox' == bp_get_the_profile_field_type() ) : ?>

                            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                            <input type="text" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" value="<?php bp_the_profile_field_edit_value(); ?>" />

                    <?php endif; ?>

                    <?php if ( 'textarea' == bp_get_the_profile_field_type() ) : ?>

                            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                            <textarea rows="5" cols="40" name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_edit_value(); ?></textarea>

                    <?php endif; ?>

                    <?php if ( 'selectbox' == bp_get_the_profile_field_type() ) : ?>

                            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                            <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>">
                                    <?php bp_the_profile_field_options(); ?>
                            </select>

                    <?php endif; ?>

                    <?php if ( 'multiselectbox' == bp_get_the_profile_field_type() ) : ?>

                            <label for="<?php bp_the_profile_field_input_name(); ?>"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                            <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                            <select name="<?php bp_the_profile_field_input_name(); ?>" id="<?php bp_the_profile_field_input_name(); ?>" multiple="multiple">
                                    <?php bp_the_profile_field_options(); ?>
                            </select>

                    <?php endif; ?>

                    <?php if ( 'radio' == bp_get_the_profile_field_type() ) : ?>

                            <div class="radio">
                                    <span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

                                    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                                    <?php bp_the_profile_field_options(); ?>

                                    <?php if ( !bp_get_the_profile_field_is_required() ) : ?>
                                            <a class="clear-value" href="javascript:clear( '<?php bp_the_profile_field_input_name(); ?>' );"><?php _e( 'Clear', 'buddypress' ); ?></a>
                                    <?php endif; ?>
                            </div>

                    <?php endif; ?>

                    <?php if ( 'checkbox' == bp_get_the_profile_field_type() ) : ?>

                            <div class="checkbox">
                                    <span class="label"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></span>

                                    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>
                                    <?php bp_the_profile_field_options(); ?>
                            </div>

                    <?php endif; ?>

                    <?php if ( 'datebox' == bp_get_the_profile_field_type() ) : ?>

                            <div class="datebox">
                                    <label for="<?php bp_the_profile_field_input_name(); ?>_day"><?php bp_the_profile_field_name(); ?> <?php if ( bp_get_the_profile_field_is_required() ) : ?><?php _e( '(required)', 'buddypress' ); ?><?php endif; ?></label>
                                    <?php do_action( bp_get_the_profile_field_errors_action() ); ?>

                                    <select name="<?php bp_the_profile_field_input_name(); ?>_day" id="<?php bp_the_profile_field_input_name(); ?>_day">
                                            <?php bp_the_profile_field_options( 'type=day' ); ?>
                                    </select>

                                    <select name="<?php bp_the_profile_field_input_name(); ?>_month" id="<?php bp_the_profile_field_input_name(); ?>_month">
                                            <?php bp_the_profile_field_options( 'type=month' ); ?>
                                    </select>

                                    <select name="<?php bp_the_profile_field_input_name(); ?>_year" id="<?php bp_the_profile_field_input_name(); ?>_year">
                                            <?php bp_the_profile_field_options( 'type=year' ); ?>
                                    </select>
                            </div>

                    <?php endif; ?>

                    <?php do_action( 'bp_custom_profile_edit_fields_pre_visibility' ); ?>

                    <?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
                            <p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
                                    <?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?> <a href="#" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'buddypress' ); ?></a>
                            </p>

                            <div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
                                    <fieldset>
                                            <legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

                                            <?php bp_profile_visibility_radio_buttons() ?>

                                    </fieldset>
                                    <a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>

                            </div>
                    <?php else : ?>
                            <p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
                                    <?php printf( __( 'This field can be seen by: <span class="current-visibility-level">%s</span>', 'buddypress' ), bp_get_the_profile_field_visibility_level_label() ) ?>
                            </p>
                    <?php endif ?>

                    <?php do_action( 'bp_custom_profile_edit_fields' ); ?>

                    <p class="description"><?php bp_the_profile_field_description(); ?></p>

            </div>
        
        <?php
        endwhile;
    endwhile;
 endif;
$html = ob_get_clean();  
return $html;
}

//////////////////////////////////////////////////////////////////////////////////////////////////

function buatp_get_content_error_messsage($type = ''){
    $access = get_option('buatp_access_setting', true);
    if(!$access['buatp_text_for_shortcode_restriction'])
        $text = "You are not permitted to see this content,only $type users can access it";
    else
        $text = str_replace ('[type_name]', $type, $access['buatp_text_for_shortcode_restriction']);
    return '<div class="buatp_content_error"><p>'.$text.'</p></div>';
}

//////////////////////////////////////////////////////////////////////////////////////////////////

function buatp_the_content_error_messsage($type = ''){
    echo buatp_get_content_error_messsage($type);
}

//////////////////////////////////////////////////////////////////////////////////////////////////

function buatp_reset_settings(){
    update_option('buatp_basic_setting','');
    update_option('buatp_profile_data_setting','');
    update_option( 'buatp_style_setting',''); 
    update_option( 'buatp_access_setting','');
}
//buatp_reset_settings();


?>