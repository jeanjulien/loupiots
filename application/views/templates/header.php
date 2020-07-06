<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
header('Content-type: text/html; charset=iso-8859-1'); 
?>
<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
	<title><?php echo $title ?> - Garderie de Laval</title>
	
	<link rel="shortcut icon" href="<?php echo base_url()?>resource/img/favicon.ico">
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>resource/css/calendar.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>resource/css/styles.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>resource/css/form.css"/>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url()?>resource/css/jquery-ui-1.9.2.custom.min.css"/>
	
	<script type="text/javascript" src="<?php echo base_url()?>resource/js/jquery-1.8.2.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>resource/js/jquery-ui-1.9.2.custom.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>resource/js/calendar.js"></script>
	<script type="text/javascript" src="<?php echo base_url()?>resource/js/misc.js"></script>
	
<script>
    </script>
		
</head>
<body>
    <!--start header-->
    <header>
 
    <!--start logo-->
    <a href="#" id="logo"><img src="<?php echo base_url()?>resource/img/logo.jpg" height="100" alt="logo"/></a>    
	<!--end logo-->
	
	Login: <b><?php echo $this->session->userdata('name'); ?></b>
	<br>

   <!--start menu-->
    <nav id="nav">
    <ul id="navigation">
    	<li><a href="<?php echo site_url()?>"  class="first">Inscription</a>
    		<ul>
				<li><a href="<?php echo site_url()?>/news">Actualit&eacute;s</a></li>
			</ul>
		</li>
		<?php if ($this->session->userdata('privilege')>=2) { ?>
		<li><a href="#">Administration</a>
    		<ul>
				<li><a href="<?php echo site_url()?>/payment/report">Facturation</a></li>
			<?php if ($this->session->userdata('privilege')==3) { ?>
				<li><a href="<?php echo site_url()?>/user/create">Familles</a></li>
				<li><a href="<?php echo site_url()?>/report/balance">Balance comptable</a></li>
				<li><a href="<?php echo site_url()?>/calendar">Calendrier</a></li>
				<li><a href="<?php echo site_url()?>/news/create">Actualit&eacute;s</a></li>
			<?php } ?>
			</ul>
		</li>
		<li><a href="#">Impression</a>
    		<ul>
				<li><a href="<?php echo site_url()?>/report/classroomCall">Feuille d'appel</a></li>
				<li><a href="<?php echo site_url()?>/report/weeklySummary">Recapitulatif</a></li>
			<?php if ($this->session->userdata('privilege')==3) { ?>
				<li><a href="<?php echo site_url()?>/report/cheque">Remise de cheque</a></li>
			<?php } ?>
			</ul>
		</li>
		<?php } ?>
		<li><a href="<?php echo site_url()?>/contact"  class="last">Contact</a></li>
    	<li id="logout"><a href="<?php echo site_url()?>/user/logout">Deconnexion</a></li>
    </ul>
    </nav>
	<!--end menu-->
	

    <!--end header-->
	</header>
 
    <!--start container-->
    <div id="container">

	
	