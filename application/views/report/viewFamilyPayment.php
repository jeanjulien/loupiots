<?php if ($loggedPrivilege >= 2) { ?>
<div class="holder_content">
	<h3>Selectionner la famille</h3>

<form class="form" method="post" action="<?php echo site_url()?>/payment/userPaymentHistory/">
	<div>		
  		<label for="userId">Famille: </label>
		<?php echo form_dropdown('selId', $usersOption, $userId, 'class="InputSelect"'); ?><br/>
   		<input class="InputSubmit" type="submit" value="Selectionner"/>
	</div>
</form>
</div>

<div class="holder_content_separator"></div>
<?php } ?>

<div class="holder_content">
		<h3>Liste des factures</h3>
	
		<br>
			<table border=1>
			<tr>
				<td>&nbsp;</td>
				<td>Restant du mois precedent</td>
				<td>D&eacute;passement du mois precedent</td>
				<td>Montant du mois</td>
				<td>Montant total du</td>
				<td>&nbsp;</td>
				<td>Montant paye</td>
				<td>Date de Paiment</td>
				<td>Type</td>
				<td>Banque</td>
				<td>Num. cheque</td>
				<td>Statut</td>
				<td>&nbsp;</td>
				<td>Restant du</td>
			</tr>
		<?php 
		foreach ($dates as $date) { //Date row title
			
			$payments = $date['userMoneyStatus']["payments"];
			$bill = $date['userMoneyStatus']["bill"];
			$totalPayments = $date['userMoneyStatus']["totalPayments"];

			echo "<tr>";
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
				if ($curPayment['bank_id'] == 0) {
				    $bank = "-";
				} else {
				    $bankId = $curPayment['bank_id'];
				    $bank = $banks[$bankId];
				}
				$rowspan = sizeof($payments);
				$solde = $bill['total'] - $totalPayments;
				
				echo "<tr>";
				if ($row==0) {
					echo "
						<td rowspan='$rowspan'><a class='button' href='".site_url()."/user/viewUser/".$userId."/".$date["year"]."/".$date["month"]."'>".$date["month"]." - ".$date["year"]."</a></td>
						<td rowspan='$rowspan'>".$bill['balanceM2']."</td>		
   						<td rowspan='$rowspan'>".$bill['children']['total']['costDep']."</td>		
   						<td rowspan='$rowspan'>".$bill['children']['total']['costResa']."</td>		
   						<td rowspan='$rowspan'><b>".$bill['total']."</b></td>		
   						<td rowspan='$rowspan'>&nbsp;</td>";
				}
				echo "<td>".$curPayment['amount']."</td>
   						<td>".$curPayment['payment_date']."</td>
						<td>".$curPayment["type"]."</td>
						<td>".$bank."</td>
						<td>".$curPayment["cheque_Num"]."</td>
   						<td>".$status."</td>";
				echo "<td>&nbsp;</td>";
				if ($row==0) {
					echo "<td rowspan='$rowspan'><b>".$solde."</b></td>";
				}
				echo "</tr>";
				$row++;
			}	
		}
		?>
			</table>


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

