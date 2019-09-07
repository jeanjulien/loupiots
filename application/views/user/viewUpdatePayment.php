<div class="holder_content">
	<h3>Modifier un paiement</h3>

	<form class="form" method="post" action="<?php echo site_url()?>/payment/update/<?php echo $payment['id']."/".$fromReport;?>">
	<div>

		<label for="userId">Famille</label>
		<?php echo form_dropdown('user_id', $usersOption, $payment['user_id']); ?><br/>
				
		<label for="amount">Montant</label>
		<input class="InputText" type="input" name="amount" value="<?php echo $payment['amount']; ?>" /><br/>
	
		<label for="type">Type</label>
    	<?php echo form_dropdown('type', $payment_types, $payment['type'], 'type="InputSelect" id="paymentType"'); ?>
    	<br/>
	
		<span id="forCheque">
		<label for="bank">Banque</label>
		<?php echo form_dropdown('bank', $banques, $payment['bank_id'], 'type="InputSelect"'); ?>
		<br/>
		<label for="chequeNum">Numero de cheque</label>
		<input class="InputText" type="input" name="chequeNum" value="<?php echo $payment['cheque_Num']; ?>"/><br/>
		</span>
		
		<span id="forVir">
		Coordonn&eacute;es banquaire des Loupiots:<br/>
		RIB: 10278 08938 00041943240 87<br/>
		IBAN: FR76 1027 8089 3800 0419 4324 087<br/>
		BIC: CMCIFR2A<br/>
		</span>
    				
    	<label for="paymentMonth">Mois pay&eacute;</label>
		<?php 
			setlocale(LC_ALL, 'fr_FR','fra');
			$monthStr=strftime("%B", mktime(0, 0, 0, $payment['month'], 10, $payment['year']));
			echo "$monthStr $year" ;
		?>
		<input type="hidden" name="month" value="<?php echo $payment['month'] ?>" />
		<input type="hidden" name="year" value="<?php echo $payment['year'] ?>" />
		
<!-- 	
   		<?php echo form_dropdown('month', generate_options_array(1,12,'callback_month'), $payment['month'], 'class="InputSelect"'); ?>
   		<?php echo form_dropdown('year', generate_options_array(date('Y')+1,2010), $payment['year'], 'class="InputSelect"');?>
-->
		<br/>
		
		<label for="class">Statut</label>
    	<?php echo form_dropdown('status', $payment_status, $payment['status'], 'class="InputSelect"'); ?>
    	<br/>
	
		<span>
		<?php
			if(validation_errors()) {
				echo validation_errors();
			}
		?>
		</span>
		<input type="hidden" name="childId" value="<?php echo $payment['id']; ?>" /> 
		<input class="InputSubmit" type="submit" value="Enregistrer"/>
	
	</div>

</form>
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
