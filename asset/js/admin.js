var j = jQuery;


j(document).ready(function(){
    
/////////////////////////////////////////////////////////////////////////////////////

    j('.buatp_type_field_selection').live('change',function(){
        var selected = j(this).val();
        if(selected == 'null')
            return;
        var html = '<form id="hidden_form" method="post" action=""><input type="hidden" name="buatp_selected_field" value="'+selected+'" /></form>';
        j('#buatp_hidden_fields').html(html);
        j('form#hidden_form').submit();
    });

/////////////////////////////////////////////////////////////////////////////////////

    j('.buatp_role_to_type').parent().parent().hide();
    j('.buatp_manage_existing_users').live('click',function(){
       if( j(this).val() === 'role_to_type' || j(this).val() === 'role_to_type_force' )
            j('.buatp_role_to_type').parent().parent().show();
       else
            j('.buatp_role_to_type').parent().parent().hide();
    });

/////////////////////////////////////////////////////////////////////////////////////

  j('div.checkbox_container input[type="checkbox"]').each(function(){
      if(j(this).is (':checked')){
          j(this).parent().css('background','#8BD0AB');
      }
  });

/////////////////////////////////////////////////////////////////////////////////////

  j('div.checkbox_container input[type="checkbox"]').click(function(){
      if(j(this).is (':checked')){
          j(this).parent().css('background','#8BD0AB');
      } else {
          j(this).parent().css('background','#ddd');
      }
  });
  
/////////////////////////////////////////////////////////////////////////////////////

  j('.buatp_page_selection').on('change',function(){
      var page = j(this).val();
      j(this).after('<span class="buatp_ajax_loader"></span>');
      j.post(ajaxurl,{action: 'buatp_root_page_selection', page: page},function(res){
          if(res==="ok")
              j('span.buatp_ajax_loader').remove();
          
      });
  });
/////////////////////////////////////////////////////////////////////////////////////

  j('div#buat_view_mode img').click(function(){
     var view = j(this).attr('id');
     j('#buatp_members').removeClass().addClass(view); 
     j('div#buat_view_mode img').each(function(){
       j(this).removeClass();  
     })
     j(this).addClass('current_view');
  });
  
  /////////////////////////////////////////////////////////////////////////////////////
  
  
  
}) 