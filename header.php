<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Podio Advanced Form library</title>
		<link rel="stylesheet" href="css/bootstrap.min">
	</head>
<body>
	
	<div id="main" class="container">
		<div class="row">
			<div class="span12">
				<h1>Podio Advanced Form library</h1>
			</div>
		</div>
		
		<?php if (isset($error_message)){ ?>
			<div class="row">
				<div class="span12">
					<div class="alert alert-error"><?php echo $error_message; ?></div>
				</div>
			</div>
		<?php } ?>