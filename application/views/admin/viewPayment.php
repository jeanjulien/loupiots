
<div class="holder_content">
	<h3>Selectionner le mois</h3>

<form class="form" method="post" action="<?php echo site_url()?>/payment/report">
	<div>		
		<label for="paymentMonth">Mois pay&eacute;</label>
   		<?php echo form_dropdown('month', generate_options_array(0,12,'callback_month'), $month, 'class="InputSelect"'); ?>
   		<?php echo form_dropdown('year', generate_options_array(date('Y')+1,2010), $year, 'class="InputSelect"');?>
		<br><label for="paymentMonth">Familles actives seulement</label>
   		<?php echo form_checkbox('onlyActive', TRUE, $onlyActive);?>
   		<input class="InputSubmit" type="submit" value="Selectionner"/>
	</div>
</form>
</div>

<div class="holder_content_separator"></div>

<div class="holder_content">

	<?php if ($loggedPrivilege >= 2) { 		?>
		<h3>G&eacute;rer les factures </h3>
		<br>
		<?php foreach ($users as $user) { //User row title 
		echo "<h4>\n".$user['user_name'];
			echo "<a class='button' href='".site_url()."/payment/userPaymentHistory/".$user['id']."/$year/$month'>Voir l'historique</a>";
			echo "</h4>\n";
			$userId = $user["id"];
			$payments = $userMoneyStatus[$userId]["payments"];
			$bill = $userMoneyStatus[$userId]["bill"];
			$totalPayments = $userMoneyStatus[$userId]["totalPayments"];
?>
			<table border=1>
			<tr>
				<td>Restant du<br><?php echo $prevMonthStr?></td>
				<td>D&eacute;passement<br><?php echo $prevMonthStr?></td>
				<td>Montant<br><?php echo $monthStr?></td>
				<td>Montant total du</td>
				<td>&nbsp;</td>
				<td>Montant paye</td>
				<td>Date de Paiment</td>
				<td>Type</td>
				<td>Banque</td>
				<td>Num. cheque</td>
				<td>Statut</td>
				<td><a class="button" href="<?php echo site_url()?>/payment/create/<?php echo $userId?>">Ajouter paiement</a></td>
				<td>&nbsp;</td>
				<td>Solde</td>
			</tr>
			<?php
			$row=0;
			foreach ($payments as $curPayment) {
				if ($curPayment['status']==1) {
					$status = "En attente de r&eacute;ception";
				} else if ($curPayment['status']==2) {
					$status = "Recu";
				} else if ($curPayment['status']==3) {
					$status = "Valid&eacute;";
				} else if ($curPayment['status']==4) {
					$status = "Annul&eacute;";
				} else {
					$status = "-";
				}
				echo "<tr>";
				if ($row==0) {
					echo "<td rowspan=".sizeof($curPayment).">".$bill['balanceM2']."</td>		
   						<td rowspan=".sizeof($curPayment).">".$bill['children']['total']['costDep']."</td>		
   						<td rowspan=".sizeof($curPayment).">".$bill['children']['total']['costResa']."</td>		
   						<td rowspan=".sizeof($curPayment)."><b>".$bill['total']."</b></td>		
   						<td rowspan=".sizeof($curPayment).">&nbsp;</td>";
				}
				
				if ($curPayment['bank_id'] == 0) {
				    $bank = "-";
				} else {
				    $bankId = $curPayment['bank_id'];
				    $bank = $banks[$bankId];
				}
				echo "<td>".$curPayment['amount']."</td>
   						<td>".$curPayment['payment_date']."</td>
						<td>".$curPayment["type"]."</td>
						<td>".$bank."</td>
						<td>".$curPayment["cheque_Num"]."</td>
   						<td>".$status."</td>";
				if (isset($curPayment["id"])) {
					echo "<td><a class='button' href='".site_url()."/payment/update/".$curPayment["id"]."/1'>Modifier</a></td>\n";
				} else {
					echo "<td>&nbsp;</td>";
				}
				echo "<td>&nbsp;</td>";
				if ($row==0) {
					$solde = $bill['total'] - $totalPayments;
					echo "<td rowspan=".sizeof($curPayment)."><b>".$solde."</b></td>";
				}
				echo "</tr>";
				$row++;
			}
			?>
			</table>
			
			<?php }?>
<?php } ?>

<div class="holder_content_separator"></div>
</div>

</div>

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

