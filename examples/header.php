<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Podio Advanced Form library</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="../assets/css/advanced-webform.css">
	</head>
<body>
	
	<div id="main" class="container">
		<div class="row">
			<div class="span12">
				<?php $title = (isset($title)) ? $title : 'Podio Advanced Form library'; ?>
				<h1><?php echo $title; ?></h1>
			</div>
		</div>
		
		<?php if (isset($error_message)){ ?>
			<div class="row">
				<div class="span12">
					<div class="alert alert-danger"><?php echo $error_message; ?></div>
				</div>
			</div>
		<?php }if (isset($message)){ ?>
			<div class="row">
				<div class="span12">
					<div class="alert alert-success"><?php echo $message; ?></div>
				</div>
			</div>
		<?php }