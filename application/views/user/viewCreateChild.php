
<div class="holder_content">
<h3>Ajouter un enfant</h3>

<?php $this->load->helper('dob'); ?>

<form class='form' method='post' action='<?php echo site_url()?>/child/create/<?php echo $userId ?>'>
	<div>

		<label for="name">Prenom</label>
		<input class="InputText" type="input" name="name"/><br/>
 	
		<label for="class">Classe</label>
    	<?php echo form_dropdown('class_id', $classesOption, 1, 'class="InputSelect"'); ?><br/>
 	
		<label for="birth">Date de naissance</label><br>
   		<select name="day"><option value="0">Jour:</option><?php echo generate_options(1,31)?></select>
		<select name="month"><option value="0">Mois:</option><?php echo generate_options(1,12,'callback_month')?></select>
   		<select name="year"><option value="0">Annee:</option><?php echo generate_options(date("Y")+1,2010)?></select>
	
		<span>
		<?php
			if(validation_errors()) {
				echo validation_errors();
			}
		?>
		</span>
		<input type="hidden" name="userId" value="<?php echo $userId ?>" />
		<input class="InputSubmit" type="submit" value="Enregistrer"/>
	
	</div>

</form>

<div class="holder_content_separator"></div>

</div>
