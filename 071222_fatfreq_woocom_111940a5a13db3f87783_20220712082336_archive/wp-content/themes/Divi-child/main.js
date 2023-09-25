jQuery(document).ready(function($){

	var data = {
	  'action': 'cart_count_retriever'
	};


	$( document ).on( "click", ".add_to_cart_button ", function() {

	setTimeout(function(){
		jQuery.post(ajax_object.ajax_url, data, function(response) {
		  // alert('Got this from the server: ' + response);
		  $('.cart-custom span').html(response);
		});
	}, 1000);

	    
	  });

});



