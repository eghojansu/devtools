<?php
require '_config.php';
require '_helper.php';

define('__FILE', basename(__FILE__));

$host = r::query('host', 'localhost');
$port = r::query('port', '3306');
$user = r::query('user', 'root');
$pass = r::query('pass', 'root');
$name = r::query('name', null);
$table = r::query('table', null);
$column = r::query('column', null);

$error = false;
try {
	$database = new PDO("mysql:host=$host;port=$port;dbname=$name", $user, $pass);

	$databaseList = [];
	$query = $database->prepare('show databases');
	$query->execute();
	while ($row = $query->fetch()) {
		if (!in_array($row[0], $_config[__FILE]['filters'])) {
			$databaseList[] = $row[0];
		}
	}

	$tableList = [];
	$viewList = [];
	if ($name) {
		$query = $database->prepare('select table_name as "name", table_comment as "comment" from information_schema.tables where table_schema = :schema');
		$params = [':schema'=>$name];
		$query->execute($params);
		while ($row = $query->fetch()) {
			if ('VIEW' === $row['comment']) {
				$viewList[] = $row['name'];
			} else {
				$tableList[] = $row['name'];
			}
		}
	}
	$columnList = [];
	if ($table) {
		$query = $database->prepare("show columns from `$table`");
		$query->execute();
		while ($row = $query->fetch()) {
			$columnList[] = $row[0];
		}
	}
} catch (Exception $e) {
	$error = 'Cannot connect with your database configuration';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $_config['tools'][__FILE].' ~ '.$_config['name']; ?></title>
	<link rel="stylesheet" href="asset/style.css">
	<script src="asset/jquery.min.js"></script>
	<script src="asset/script.js"></script>
</head>
<body>
    <?php echo h::toolHeading($_config['tools'][__FILE], $_config['name']); ?>
	<hr>
	<p><em>your configuration :</em></p>
	<form>
		Host: <?php echo h::text('host', ['value'=>$host]); ?>
		Port: <?php echo h::text('port', ['value'=>$port]); ?>
		User: <?php echo h::text('user', ['value'=>$user]); ?>
		Pass: <?php echo h::text('pass', ['value'=>$pass]); ?>
		Name: <?php echo h::text('name', ['value'=>$name]); ?>
		<?php echo h::button('submit'); ?>
		<?php echo h::button('reset', 'clear'); ?>
	</form>
	<hr>
	<?php if ($error): ?>
		<?php echo e::show($error); ?>
	<?php else: ?>
		<p><em>Results :</em></p>
		<table class="table">
			<thead>
				<tr>
					<th width="200px">Databases</th>
					<th width="200px">Tables &amp; Views</th>
					<th width="200px">Columns</th>
					<th>tools</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<ol class="db-item database">
							<?php foreach ($databaseList as $key => $value): ?>
								<li><?php echo h::checkbox('database[]').' '.h::a($value, ['class'=>$value===$name?'active':''], ['name'=>$value]); ?></li>
							<?php endforeach; ?>
						</ol>
					</td>
					<td>
						<p><em>Tables</em></p>
						<ol class="db-item table">
							<?php foreach ($tableList as $key => $value): ?>
								<li><?php echo h::checkbox('table[]').' '.h::a($value, ['class'=>$value===$table?'active':''], ['table'=>$value]); ?></li>
							<?php endforeach; ?>
						</ol>
						<p><em>Views</em></p>
						<ol class="db-item view">
							<?php foreach ($viewList as $key => $value): ?>
								<li><?php echo h::checkbox('view[]').' '.h::a($value, ['class'=>$value===$table?'active':''], ['table'=>$value]); ?></li>
							<?php endforeach; ?>
						</ol>
					</td>
					<td>
						<ol class="db-item column">
							<?php foreach ($columnList as $key => $value): ?>
								<li><?php echo h::checkbox('column[]').' '.h::a($value, ['class'=>$value===$column?'active':''], ['column'=>$value]); ?></li>
							<?php endforeach; ?>
						</ol>
					</td>
					<td>
                        <?php $implode = $table?$columnList:$tableList; ?>
                        <?php $implode = implode(',', $implode); ?>
                        <div class="toolbar" data-target="#textarea">
                            <button data-action-select>Select</button>
                        </div>
                        <textarea id="textarea" data-content="<?php echo $implode; ?>"><?php echo $implode; ?></textarea>
                    </td>
				</tr>
			</tbody>
		</table>
	<?php endif; ?>
</body>
</html>

