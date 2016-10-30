<?php require '_config.php'; ?>
<?php require '_helper.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_config['name']; ?></title>
	<link rel="stylesheet" href="asset/style.css">
</head>
<body>
    <?php echo h::toolHeading($_config['name']); ?>
	<hr>
	<p><em>Select your needs:</em></p>
	<ul>
		<?php foreach ($_config['tools'] as $file=>$desc): ?>
			<li><a href="<?php echo $file; ?>"><?php echo $file.' ~ '.$desc; ?></a></li>
		<?php endforeach; ?>
	</ul>
</body>
</html>

