<div id=getData>
	<?php echo "year=".$getData['year']."&month=".$getData['month']."&user_id=".$getData['user_id'];?>
</div>
<div id=siteUrl>
	<?php echo site_url()?>
</div>

<div class="holder_content">
<section class="container_left">

	<h3>Liste des enfants <?php echo $user['name']?></h3>
	<?php 
	if (isset($user['children'])) {
	foreach ($user['children'] as $child): 
	echo ('<b>'.$child['name'].'</b> en '.$child['class']['class'].' naissance:'.$child['birth']);?>
	<a class="button" href="<?php echo site_url()?>/child/update/<?php echo $child['id']?>">Modifier</a><br>
	<br\>
	<?php 
	endforeach;
	} ?>
	<a class="button" href="<?php echo site_url()?>/child/create/<?php echo $user['id']?>">Ajouter un enfant</a>
	<a class="button" href="<?php echo site_url()?>/user/create/<?php echo $user['id']?>">Modifier les parametres de la famille</a><br>
</section>

<?php if ($this->session->userdata('privilege')>=2) {?>
<section class="container_rigth">
	<h3>Selection de la famille</h3>
	<form class="form" method="post" action="<?php echo site_url()?>/user/0">
	<div>
  		<label for="userId">Famille: </label>
		<?php echo form_dropdown('selId', $usersOption, $userId, 'class="InputSelect"'); ?><br/>
   		<label for="month">Date: </label>
   		<?php echo form_dropdown('month', generate_options_array(0,12,'callback_month'), $getData['month'], 'class="InputSelect"'); ?>
   		<?php echo form_dropdown('year', generate_options_array(date('Y')+1,2010), $getData['year'], 'class="InputSelect"');?><br/>
		<input class="InputSubmit" type="submit" name="select" value="Selectionner"/>
	</div>
	</form>
</section>
<?php } ?>
</div>

<?php 
	if (isset($user['children'])) {
?>
<div class="holder_content">
<section class="container_left">
	<h3>Calendrier</h3>
	<table>
		<tr>
			<td bgcolor="#EEEEDD">Non reservable</td>
			<td bgcolor="#EEEEFF">Disponible</td>
			<td bgcolor="#ACF28A">Reserv&eacute;e</td>
			<td bgcolor="#2A7E8A">Valid&eacute;e</td>
			<td bgcolor="#A6233C">D&eacute;passement</td>
		</tr>
	</table>
</section>

<section class="container_rigth">
	<a class="button" href="<?php echo site_url()."/report/userCalendar/".$getData['user_id']."/".$getData['year']."/".$getData['month'];?>">Imprimer</a>
</section>

<section class="holder_content">
	<div id=calendarContent></div>	
</section>
</div>
<?php } ?>

<div class="holder_content">
<section class="container_left">

	<h3>Encours <?php echo $getData['monthStr'] ?></h3>
	<table border=1>
			<tr>
				<td>&nbsp;</td>
				<td>Reservation</td>
				<td>Depassement</td>
				<td><b>Total</b></td>
			</tr>
			<?php
			if (isset($user['children'])) {
				foreach ($user['children'] as $child) {
					echo "
						<tr>
							<td>".$child['name']."</td>
							<td><div class='".$child['id']."-cost'></div></td>
							<td><div class='".$child['id']."-costDepassement'></div></td>
							<td><b><div class='".$child['id']."-total'></div></b></td>
						</tr>";
				}
			}
			?>
				<tr>
					<td>Total</td>
					<td><div class='cost'></div></td>
					<td><div class='totalDepassement'></div></td>
					<td><b><div class='total'></div></b></td>
				</tr>
		</table>

</section>
</div>

<?php 
	if ($getData['curMonthYear'] >= $getData['viewMonthYear']) {
?>

<div class="holder_content">
<section class="container_left">
	<h3>Facture <?php echo $getData['month-1Str'] ?></h3>
	
	<table border=1>
			<tr>
				<td>&nbsp;</td>
				<td>Restant du <?php echo $getData['month-2Str'] ?></td>
				<td>Depassement <?php echo $getData['month-2Str'] ?></td>
				<td>Reservation <?php echo $getData['month-1Str'] ?></td>
				<td><b>Total</b></td>
			</tr>
			<?php
			if (isset($user['children'])) {
				foreach ($user['children'] as $child) {
					$childNum = $child['id'];
					echo "
						<tr>
							<td>".$child['name']."</td>
							<td>&nbsp;-</td>
							<td>".$bill['children'][$childNum]['costDepStr']."</td>
							<td>".$bill['children'][$childNum]['costResaStr']."</td>
							<td><b>".$bill['children'][$childNum]['sum']."</b></td>
						</tr>";
				}
			}
			?>
				<tr>
					<td>Total</td>
					<td><?php echo $balance ?></td>
					<td><?php echo $bill['children']['total']['costDep'] ?></td>
					<td><?php echo $bill['children']['total']['costResa'] ?></td>
					<td><b><?php echo $bill['totalCost'] ?></b></td>
				</tr>
		</table>
		<i>Le restant du ne prend pas en compte les paiements non validés.</br>
		Ils seront pris en compte au mois de leur validation.</i>
	</section>

<section class="container_rigth">
	<h3>Reglement</h3>
	<table border=1>
		<tr>
			<td>Mois pay&eacute;</td>
			<td>Montant</td>
			<td>Type</td>
			<td>Statut</td>
			<td>Date de Validation</td>
			<td>&nbsp;</td>
		</tr>
		<?php 
		foreach ($payment as $curPayment) {
			if ($curPayment['status']==1) {
				$staus = "En attente de r&eacute;ception";
			} else if ($curPayment['status']==2) {
				$staus = "Recu";
			} else if ($curPayment['status']==3) {
				$staus = "Valid&eacute;";
			} else if ($curPayment['status']==4) {
				$staus = "Annul&eacute;";
			} else {
				$staus = "En attente de r&eacute;ception";
			}
			
			echo "	
				<tr>
					<td>".$curPayment['datePaid']."</td>
					<td>".$curPayment['amount']."</td>
					<td>".$curPayment["type"]."</td>
					<td>".$staus."</td>
					<td>".$curPayment["payment_date"]."</td>
					<td><a class='button' href='".site_url()."/payment/update/".$curPayment["id"]."'>Modifier</a></td>\n
				</tr>";
		}
		?>
	</table>
		
	<?php 
		if ($getData['curMonthYear'] == $getData['viewMonthYear']) {
			echo '<a class="button" href="'.site_url().'/payment/create/'.$user['id']."/".$getData['year']."/".$getData['month'].'">Ajouter paiement</a>';
		}
	?>
	<a class="button" href="<?php echo site_url()?>/payment/userPaymentHistory/<?php echo $user['id']."/".$getData['year']."/".$getData['month']?>">Voir l'historique</a>		
	
</section>
<?php 
	}
?>

</div>

<div class="holder_content_separator"></div>
 
<?php 
function generate_options_array($from,$to,$callback=false) {
	$reverse=false;
	if($from>$to) {
		$tmp=$from;
		$from=$to;
		$to=$tmp;
		$reverse=true;
	}
	$options=array();
	$init=$from-1;
	for($i=$from;$i<=$to;$i++) {
		$val=$callback?$callback($i):$i;
		$options["$i"]=$val;
	}
	if($reverse) {
		$options=array_reverse($options, true);
	}
	return $options;
}
?>


