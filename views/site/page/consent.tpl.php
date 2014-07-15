<?php
	$email = $SOUP->get('email');

	$fork = $SOUP->fork();
	$fork->set('pageTitle', 'Consent');
	$fork->set('headingURL', Url::consent());
	$fork->startBlockSet('body');
?>
<script type="text/javascript">

$(document).ready(function(){
	$('#btnAdult').click(function(){
		window.location = "<?= Url::register($email) ?>";
		});
	$('#btnMinor').click(function(){
		window.location = "<?= Url::minorConsent() ?>";
		});
	});

</script>

<td class="left">

	<p>Welcome to <?= PIPELINE_NAME ?>!</p>
	<p>This software has the intent to help people collaborate online to create crowdsourcing projects</p>
	<p>First, <strong>please tell us how old you are</strong>.</p>

	<div class="buttons">
		<input id="btnAdult" class="left" type="button" value="13 Or Older" />
		<input id="btnMinor" class="left" type="button" value="Younger Than 13" />
	</div>

</td>

<td class="right"> </td>


<?php
	$fork->endBlockSet();
 	$fork->render('site/partial/page');