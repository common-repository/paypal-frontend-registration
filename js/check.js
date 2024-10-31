jQuery(document).ready(function(){
jQuery("#user_login").keyup(function(){
	
	
    var user_login = jQuery("#user_login").val();
jQuery.ajax({
type: 'POST',
url: MyAjax.ajaxurl,
data: {"action": "post_word_count", "user_login":user_login},
success: function(data){

 document.getElementById("status").innerHTML=data;

}
});
});
});

