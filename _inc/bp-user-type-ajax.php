<?php 

add_action('wp_ajax_append-conditional-fields','buatp_conditional_fields_by_type',1);
add_action('wp_ajax_nopriv_append-conditional-fields','buatp_conditional_fields_by_type',1);

function buatp_conditional_fields_by_type(){
    if(!$_POST['type'])
         die();
    $type_id = buatp_get_field_id_by_name($_POST['type']);
    $settings_profile_data = get_option('buatp_profile_data_setting',true);
    if(!$settings_profile_data['buatp_include_fields_at_registration_for_'.$type_id]){
        die();
    }
    $arr = (array) $settings_profile_data['buatp_include_fields_at_registration_for_'.$type_id];
    foreach ($arr as $names){
        $includes_arr[$i++] = buatp_get_field_id_by_name($names);
    }
    $groups = buatp_get_all_field_groups();
    foreach($groups as $id){
        $all_fields = array_merge((array)$all_fields,buatp_get_all_fields("WHERE group_id = $id AND parent_id = 0",'id'));
    }
    foreach((array)$all_fields as $f_id){
        if(!in_array($f_id, $includes_arr))
            $excludes_arr[$j++] = $f_id;
    }
    
    $excludes = implode(',',(array) $excludes_arr);
    $includes = implode(',',(array) $includes_arr);
    $groups_inc = implode(',', $groups);
    $html = '<div class="conditional_fields">';
    foreach($groups as $id){
    $html .= buatp_generate_fields("profile_group_id=$id&exclude_fields=$excludes");
    }
    $html .= "<input type='hidden' id='conditional_field_values' value='$includes' /> </div>";
    echo $html;
    die();
}

///////////////////////////////////////////////////////////////////////////////////////////////

function buatp_root_page_selection(){
    if( !is_user_logged_in() || !current_user_can('create_users'))
        die();
    $pages = get_option('bp-pages',true);
    $buatp_general_settings = get_option('buatp_basic_setting',true);
    $buatp_page = $_POST['page'];
    $pages['buatp'] = $buatp_page;
    $buatp_general_settings['buatp_page_selection'] = $buatp_page;
    if( update_option('bp-pages', $pages) && update_option('buatp_basic_setting', $buatp_general_settings) )
    die('ok');
}
add_action('wp_ajax_buatp_root_page_selection','buatp_root_page_selection');
?>