$(function(){
	$('.error').hide()

	$('input[required]').each(function() {
		var currentPlaceholder = $(this).attr('placeholder');
		$(this).attr('placeholder', currentPlaceholder + '*');
	});
	
    $('[placeholder]').focus(function () {
		$(this).attr('data-text', $(this).attr('placeholder'));
		$(this).attr('placeholder', '');
	}).blur(function () {
		$(this).attr('placeholder', $(this).attr('data-text'));
	});

	$('#txtemail').change(function(){
		let useremail=$(this).val();
		$('#txtcheckemail').load('ajaxuser/checkuseremail.php?useremail='+useremail)
	})

    $('#txtnewpass').change(function(){
        newpassword=$(this).val();
        if(newpassword==null){
            $('.error').hide();
        }else if(conformpass==null){
            $('.error').hide();
        }else if(newpassword == conformpass){
            $('.error').hide();
        }else if (newpassword != conformpass){
            $('.error').show();
            $('.error').text('error! the password is not the same')
        }
    })

    $('#txtconform').change(function(){
        conformpass=$(this).val();
        if(newpassword==null){
            $('.error').hide();
        }else if(conformpass==null){
            $('.error').hide();
        }else if(newpassword == conformpass){
            $('.error').hide();
        }else if (newpassword != conformpass){
            $('.error').show();
            $('.error').text('error! the password is not the same')
        }
    })
	
	
})