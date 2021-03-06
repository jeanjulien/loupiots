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
		<br>
		
   		<input class="InputText" type="hidden" name="payment_date" value="<?php echo date("Y-m-d"); ?>"/>
   		<input class="InputText" type="hidden" name="fromReport" value="<?php echo $fromReport; ?>"/>
   		
		<label for="month_paided">Mois pay&eacute; :</label>
              <input type="radio" id="dateChoice1" name="month_paided" value="<?php echo $prevMonth?>" checked>
              <label for="dateChoice1"><?php echo strftime("%B %Y", strtotime($prevMonth)) ?></label>
              <input type="radio" id="dateChoice2" name="month_paided" value="<?php echo $month?>">
              <label for="dateChoice2"><?php echo strftime("%B %Y", strtotime($month)) ?></label>
		
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
