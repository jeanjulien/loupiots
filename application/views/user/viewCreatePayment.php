<div class="holder_content">
	<h3>Ajouter un paiement</h3>
<form class="form" method="post" action="<?php echo site_url()?>/payment/create">
	<div>

		<label for="userId">Famille</label>
		<?php echo form_dropdown('user_id', $usersOption, $userId); ?><br/>
				
		<label for="amount">Montant</label>
		<input class="InputText" type="input" name="amount"/><br/>
	
		<label for="type">Type</label>
		<?php echo form_dropdown('type', $payment_types, '', 'type="InputSelect" id="paymentType"'); ?>
		<br/>

		<span id="forCheque">
		<label for="bank">Banque</label>
		<?php echo form_dropdown('bank', $banques, '', 'bank="InputSelect"'); ?>
		<br/>
		<label for="chequeNum">Numero de cheque</label>
		<input class="InputText" type="input" name="chequeNum"/><br/>
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
			$monthStr=strftime("%B", mktime(0, 0, 0, $month, 10, $year));
			echo "$monthStr $year" ;
		?>
<!-- 	
		<select name="month"><option value="0">Mois:</option><?php echo generate_options(1,12,'callback_month')?></select>
   		<select name="year"><?php echo generate_options(date('Y')+1,2010)?></select>
 -->	
		<input type="hidden" name="month" value="<?php echo $month ?>" />
		<input type="hidden" name="year" value="<?php echo $year ?>" />


	
		<span>
		<?php
			if(validation_errors()) {
				echo validation_errors();
			}
		?>
		</span>
		<input class="InputSubmit" type="submit" value="Enregistrer"/>
	
	</div>

</form>
</div>

<div class="holder_content_separator"></div>
