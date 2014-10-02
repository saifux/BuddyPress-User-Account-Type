<?php

function buatp_update_option_on_delete_fields($field){
    $settings = get_option('buatp_basic_setting',false);
    $field_name = $settings['buatp_type_field_selection'];
    $settings_profile_data = get_option('buatp_profile_data_setting',false);
    
    $types = buatp_get_all_types( buatp_get_field_id_by_name( $field_name ) );
    $field_id = (int)$field->id;
    $field_name = $field->name;
    foreach( (array)$types as $type ){
          if( in_array ( $field_name , (array)$settings_profile_data['buatp_exclude_fields_for_'.$type['id']] ) ){
              unset($settings_profile_data['buatp_exclude_fields_for_'.$type['id']][$field_name]);  
          }
          
          if( in_array ( $field_name , (array)$settings_profile_data['buatp_include_fields_at_registration_for_'.$type['id']] ) ){
            unset($settings_profile_data['buatp_include_fields_at_registration_for_'.$type['id']][$field_name]); 
          }
    }
    update_option('buatp_profile_data_setting', $settings_profile_data);
}
add_action('xprofile_fields_deleted_field','buatp_update_option_on_delete_fields');

?>