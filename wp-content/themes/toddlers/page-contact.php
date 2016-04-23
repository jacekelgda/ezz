<?php
/*
Template Name: Contact
*/
date_default_timezone_set('Etc/UTC');
require dirname(dirname(dirname(dirname( __FILE__ )))) . '/vendor/phpmailer/phpmailer/PHPMailerAutoload.php';


//contact form submitted?
if ($unf_options['unf_contactformemail'] ) {
//Only allows page to show if the contact form email has been set... Closes at end of this file.
	$contactformemail = $unf_options['unf_contactformemail']; // this is later fed into the form script

	if(isset($_POST['submitted'])) {

		//Check to see if the honeypot captcha field was filled in
		if(trim($_POST['checking']) !== '') {
			$captchaError = true;
		} else {

			//Check to make sure that the name field is not empty
			if(trim($_POST['contactName']) === '') {
				$nameError = 'You forgot to enter your name.';
				$hasError = true;
			} else {
				$name = trim($_POST['contactName']);
			}

			//Check to make sure sure that a valid email address is submitted
			if(trim($_POST['email']) === '')  {
				$emailError = 'You forgot to enter your email address.';
				$hasError = true;
			} else if (! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ) {
				$emailError = 'You entered an invalid email address.';
				$hasError = true;
			} else {
				$email = trim($_POST['email']);
			}

			//Check to make sure comments were entered
			if(trim($_POST['comments']) === '') {
				$commentError = 'You forgot to enter your comments.';
				$hasError = true;
			} else {
				if(function_exists('stripslashes')) {
					$comments = stripslashes(trim($_POST['comments']));
				} else {
					$comments = trim($_POST['comments']);
				}
			}

			//If there is no error, send the email
			if(!isset($hasError)) {

				$mail = new PHPMailer;
				$mail->SMTPDebug = 2;
                $mail->CharSet = "UTF-8";

				$mail->isSMTP();                            // Set mailer to use SMTP
				$mail->Host = 'in-v3.mailjet.com';  // Specify main and backup SMTP servers
				$mail->Port = 587;                            // TCP port to connect to
				$mail->SMTPSecure = 'tls';
				$mail->SMTPAuth = true;

				$mail->Username = "6b23baeedffafd62effd5c3381e68b16";
				$mail->Password = "63557b93d74dcd0493425d136fee742a";

				$mail->setFrom('biuro@edu-zielonyzakatek.pl', 'Edu-ZielonyZakatek');
				$mail->addAddress($contactformemail, $contactformemail);     // Add a recipient

				$mail->isHTML(true);                                 // Set email format to HTML

				$subject = 'Wiadomość od: ' . $name . ' ( Wysłano z: '.esc_url( home_url() ).')';
				$sendCopy = isset($_POST['sendCopy']) ? trim($_POST['sendCopy']) : false;
				$body = "$comments<br /><br />Od: $name, email: $email";
				$headers = 'From: Edu-ZielonyZakatek.pl <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;

				$mail->Subject = $subject;
				$mail->Body    = $body;
				$mail->AltBody = $body;

				$mail->send();

				if($sendCopy == true) {
					$subject = 'Kopia wiadomosci wyslanej do: '.esc_url( home_url() ).'.';

					$mail->Subject = $subject;
					$mail->Body    = $body;
					$mail->AltBody = $body;
					$mail->send();
				}

				$emailSent = true;

			}
		}
	}

	get_header();
	global $unf_options;

	if (!empty($unf_options['unf_contactsuccess'])){
		$successmessage = $unf_options['unf_contactsuccess'];
	} else {
		$successmessage = '<strong>Thanks!</strong> Your message was successfully sent.';
	}

	if (!empty($unf_options['unf_contactforgot'])){
		$forgotmessage = $unf_options['unf_contactforgot'];
	} else {
		$forgotmessage = 'You forgot to enter your';
	}

	if (!empty($unf_options['unf_contactinvalid'])){
		$invalidmessage = $unf_options['unf_contactinvalid'];
	} else {
		$invalidmessage = 'You entered an invalid';
	}
	?>




	<script type="text/javascript" >


		jQuery(document).ready(function($){
			jQuery('form#contactForm').submit(function() {
				jQuery('form#contactForm .error').remove();
				var hasError = false;
				jQuery('.requiredField').each(function() {
					if(jQuery.trim($(this).val()) == '') {
						var labelText = jQuery(this).prev('label').text();
						jQuery(this).parent().append('<div class="error alert small alert-warning"><?php echo wp_kses_post($forgotmessage);?> '+labelText+'.</div>');
						hasError = true;
					} else if($(this).hasClass('email')) {
						var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
						if(!emailReg.test(jQuery.trim($(this).val()))) {
							var labelText = $(this).prev('label').text();
							jQuery(this).parent().append('<div class="error alert small alert-warning"><?php echo wp_kses_post($invalidmessage);?> '+labelText+'.</div>');
							hasError = true;
						}
					}
				});
				if(!hasError) {
					jQuery('#content #submitted').fadeOut('normal', function() {
						jQuery('#content .sendmessagebtn').hide();
						jQuery(this).parent().append('<img src="<?php echo get_template_directory_uri(); ?>/library/img/loading.svg" alt="Loading&hellip;" height="31" width="31" />');
					});
					var formInput = $(this).serialize();
					$.post($(this).attr('action'),formInput, function(data){
						jQuery('form#contactForm').slideUp("fast", function() {
							jQuery(this).before('<p class="thanks"><?php echo wp_kses_post($successmessage);?></p>');
						});
					});
				}

				return false;

			});
		});

	</script>


	<div id="content-wrapper" class="row clearfix contact-page-wrapper">


		<div id="content" class="col-md-8 column">
			<div class="article clearfix">
				<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					<?php get_template_part( 'library/unf/featured', 'image' ); ?>
					<h1 class="post-title"><?php the_title();?></h1>

					<?php
					$layout = $unf_options['unf_contact-blocks']['enabled'];
					if ($layout): foreach ($layout as $key=>$value) {

						switch($key) {

							case 'googlemap': get_template_part( 'library/unf/contactmap' );
								break;

							case 'contactform': get_template_part( 'library/unf/contactform' );
								break;

							case 'contactdetails': get_template_part( 'library/unf/contactdetails' );
								break;

							case 'pagecontent': the_content();
								break;

						}

					}
					endif;
					?>

				<?php endwhile;
				endif; ?>
			</div>
		</div>



		<?php get_sidebar('contact'); ?>
	</div>

	<?php get_footer(); ?>

<?php } else { ?>
	Please set your contact form's receiver email <a href="<?php echo site_url(); ?>/wp-admin/admin.php?page=toddlers_options&tab=4">here</a>
<?php } ?>
