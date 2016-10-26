$(init);

function init() {
	$(".signup-response").css("display", "none");
}

$(document).ready(function(){
	$('#newsletter').submit(function(){
		
		var form = $(this),
		formData = form.serialize(),
		formUrl = form.attr('action'),
		formMethod = form.attr('method'),
		responseMsg = $('#signup-response');
		
		responseMsg.hide()
			.addClass('response-waiting')
			.text('Sending...')
            .fadeIn(200);
		
		$.ajax({  
            url: formUrl,  
            type: formMethod,  
            data: formData,  
            success:function(data){  
  
                //setup variables  
                var responseData = jQuery.parseJSON(data),   
                    klass = '';  
  
                //response conditional  
                switch(responseData.status){  
                    case 'error':  
                        klass = 'response-error';  
                    break;  
                    case 'success':  
                        klass = 'response-success';  
                    break;    
                }  
  				$('#submit');
                //show reponse message  
                responseMsg.fadeOut(200,function(){  
                    $(this).removeClass('response-waiting')  
                           .addClass(klass)  
                           .html(responseData.message)  
                           .fadeIn(800,function(){  
                               //set timeout to hide response message  
                               setTimeout(function(){  
                                   responseMsg.fadeOut(800,function(){  
								   	$('#submit');
                                       $(this).removeClass(klass);
                                   });  
                               },2000);  
                            });  
                 });  
              }  
        });  
  
        //prevent form from submitting  
        return false;  
    });  
});  