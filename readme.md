E-Mail Obfuscate Plugin
=============

"E-Mail Obfuscate" is a small extension to obfuscate E-Mail Adresses. That way, spammers won't be able to get the email addresses entered by the user.
Use it by placing the following in your template:

	{{ emailObfuscate(record.body)}}

In order to save http requests, please add the following to your stylesheet:

	.emailObfuscate {
  	  unicode-bidi:bidi-override;
  	  direction:rtl;
	}

You can also add a mailto link to your adresses.
If you want to do that:
 - Make sure to include jQuery in the template.
 - Add the following code to your javascript file:

		$('.emailObfuscate').each(function(i) {
		  var email = $(this).html().split("").reverse().join("");
	      $(this).wrap('<a href="mailto:' + email  +'"></a>').html(email).removeClass('emailObfuscate');
		});

How it works:
E-Mail addresses are reversed in the html output by the server.
Your browser reverses them back with CSS.
If JavaScript is activated and the code included, it will reverse it as well and 
wrap it into a mailto link. 