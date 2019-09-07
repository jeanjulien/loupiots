
<div class="holder_content">
<h3>Modifier un enfant</h3>

<?php $this->load->helper('dob'); ?>

<form class='form' method='post' action='<?php echo site_url()?>/child/update/<?php echo $child['id'];?>'>
<div><label for="name">Prenom</label> 
<input class="InputText" type="input" name="name" value="<?php echo $child['name']; ?>" /><br />
<label for="isActive">Actif</label>
<input type="checkbox" name="isActive" <?php echo $child['isActive']; ?> /><br />

<label for="class">Classe</label> <?php echo form_dropdown('class_id', $classesOption, $classId, 'class="InputSelect"'); ?><br />

<label for="birth">Date de naissance</label><br>
	<?php echo form_dropdown('day', generate_options(1,31), $day, 'class="InputSelect"'); ?>
	<?php echo form_dropdown('month', generate_options(1,12,'callback_month'), $month, 'class="InputSelect"'); ?>
	<?php echo form_dropdown('year', generate_options(date('Y')+1,2010), $year, 'class="InputSelect"'); ?>

<span> <?php
if(validation_errors()) {
	echo validation_errors();
}
?> </span> <input type="hidden" name="userId" value="<?php echo $userId ?>" /> 
	<input type="hidden" name="childId" value="<?php echo $child['id']; ?>" /> 
	<input class="InputSubmit" type="submit" value="Enregistrer" /></div>

</form>

<div class="holder_content_separator"></div>

<?php 
function generate_options($from,$to,$callback=false) {
	$reverse=false;
	if($from>$to) {
		$tmp=$from;
		$from=$to;
		$to=$tmp;
		$reverse=true;
	}
	$options=array();
	for($i=$from;$i<=$to;$i++) {
		$options[$i]=$callback?$callback($i):$i;
	}
	if($reverse) {
		$options=array_reverse($options);
	}
	return $options;
}
?></div>
