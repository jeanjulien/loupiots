<div id=siteUrl>
	<?php echo site_url()?>
</div>

<?php
$lastCloseFileName = "lastVisit.txt";
if (!file_exists($lastCloseFileName)) {
	touch($lastCloseFileName);
}

$lastCloseDate=filemtime($lastCloseFileName);
//echo "La date de derniere fermeture etait ".date("l d M Y H:i:s.", $lastCloseDate)." ".$lastCloseDate."</br>";
$nextCloseDate=date(strtotime('next thursday', $lastCloseDate));

//echo "Prochaine fermeture prevue : ". date('l d M Y H:i:s', $nextCloseDate) ." ". $nextCloseDate ."</br>";

$now=date(time());
//echo "Now:       ". date('l d M Y H:i:s') ." ".date(time())." ".$now."</br>";

//echo "<br>des que qq'un se connecte apres le ".date('l d M Y H:i:s', $nextCloseDate)."<br>";
if ($now > $nextCloseDate) {
	$nextCloseDate=date(strtotime('next thursday', $now));
	$closeDate=date(strtotime('next saturday', $nextCloseDate));
//	echo "Cloturer du ".date('l d M Y H:i:s', $lastCloseDate)." ".$lastCloseDate." au ".date('l d M Y H:i:s', $closeDate)." ".$closeDate."</br>";
	$sql = $this->Resa_model->validateResaByDate($lastCloseDate, $closeDate);
//	echo $sql."<br>";
//	echo "Changer la date de cloture a ".date('l d M Y H:i:s', $now)."</br>";
//	echo "Nouvelle fermeture prevue : ". date('l d M Y H:i:s', $nextCloseDate) ." ". $nextCloseDate ."</br>";
	touch($lastCloseFileName);
	file_put_contents($lastCloseFileName, $closeDate);
	
	//sauvegarder le cout
	
	//si nouveau mois verouiller le debit
	//si ligne cost du mois est vide => c'est un nouveau mois.
	//current month
	
	//last month paid 
	//if last cost.month/yearpaid < current month/year
}

?>

<div class="holder_content">
<section class="container_left">
<form class="form" method="post" action="<?php echo site_url()?>/login">
	<div>
		<label for="username" >Mail:</label>
		<input class="InputText" type="text" size="20" name="username" value="<?php echo set_value('username'); ?>" /></br>
		<label for="pass">Mot de passe:</label>
		<input class="InputText" type="password" size="20" name="pass"/>
		<span>
		<?php
			if(validation_errors()) {
				echo validation_errors();
			}
		?>
		</span>
		<input class="InputSubmit" type="submit" value="Login"/>
	</div>
</form>
</section>
<section class="container_rigth">
<div>
	<a class="button" href="mailto:loupiot@free.fr">Envoyer un mail</a><br/>
	<a class="button" href="<?php echo site_url()?>/contact/viewLogin"  class="last">Nous contacter</a>
</div>
</section>
<div class="holder_content_separator"></div>
</div>

<div class="holder_content">
<h3>Actualit&eacute;s</h3>
<div id=newsContent></div>

</div>




