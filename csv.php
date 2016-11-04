<?php
require '_config.php';
require '_helper.php';

define('__FILE', basename(__FILE__));

$enclosureMap = [
    'DQ'=>'"',
    'SQ'=>"'",
];
$doAction = (isset($_FILES['csv']) && $_FILES['csv']['error'] === UPLOAD_ERR_OK);
$delimiter = r::data('delimiter', ',');
// DQ = Double Quotes , SQ = Single Quotes
$enclosure = r::data('enclosure', 'DQ');
$escape = r::data('escape', '\\');
$skipLine = r::data('skipLine', null);
$startLine = r::data('startLine', 2);
$action = r::data('action', 'trim');
$length = r::data('length', 500);
$cell = r::data('cell', 3);
$append = r::data('append', '(');
$prepend = r::data('prepend', '),');

$error = null;
$result = '';
if ($doAction) {
    $file = $_FILES['csv']['tmp_name'];
    $handle = fopen($file, 'r');

    if ($file && false !== $handle) {
        $lineNumber = 0;
        $skips = array_filter(explode(',', str_replace(' ', '', $skipLine)), function($val) use ($startLine) {
            return $val > $startLine;
        });
        $actions = array_filter(explode(',', str_replace(' ', '', $action)));
        $realEnclosure = array_key_exists($enclosure, $enclosureMap)?$enclosureMap[$enclosure]:$enclosure;
        while (($data = fgetcsv($handle, $length, $delimiter, $realEnclosure, $escape)) !== false) {
            $lineNumber++;
            $num = count($data);
            if ($lineNumber < $startLine || in_array($lineNumber, $skips) || $num != $cell) {
                continue;
            }

            $result .= $append.$realEnclosure.implode($realEnclosure.$delimiter.$realEnclosure, array_map(function($val) use ($actions) {
                $val = str_replace(['"',"'"], ['\\"', "\\'"], $val);
                foreach ($actions as $act) {
                    if (function_exists($act)) {
                        $val = call_user_func_array($act, [$val]);
                    }
                }

                return $val;
            }, $data)).$realEnclosure.$prepend.PHP_EOL;
        }
        fclose($handle);
    } else {
        $error = 'We can open your uploaded file, Sir! Please do not joke me!';
    }
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
    <form enctype="multipart/form-data" method="post">
        File: <?php echo h::file('csv'); ?>
        Length: <?php echo h::text('length', ['value'=>$length, 'size'=>2, 'title'=>'Row length']); ?>
        Delimiter: <?php echo h::text('delimiter', ['value'=>$delimiter, 'size'=>2, 'title'=>'Cell delimiter']); ?>
        Enclosure: <?php echo h::text('enclosure', ['value'=>$enclosure, 'size'=>2, 'title'=>'Cell enclosure']); ?>
        Escape: <?php echo h::text('escape', ['value'=>$escape, 'size'=>2, 'title'=>'Escape char']); ?>
        Skipline: <?php echo h::text('skipLine', ['value'=>$skipLine, 'size'=>2, 'title'=>'Skip line within this range, separate by comma please']); ?>
        Startline: <?php echo h::text('startLine', ['value'=>$startLine, 'size'=>2, 'title'=>'Start action from line']); ?>
        <br>
        <br>
        Cell: <?php echo h::text('cell', ['value'=>$cell, 'title'=>'How much cell per row', 'size'=>2]); ?>
        Append: <?php echo h::text('append', ['value'=>$append, 'title'=>'Append this each row', 'size'=>2]); ?>
        Prepend: <?php echo h::text('prepend', ['value'=>$prepend, 'title'=>'Prepend this each row', 'size'=>2]); ?>
        Action: <?php echo h::text('action', ['value'=>$action, 'title'=>'Action to each cell']); ?>
        <?php echo h::button('submit'); ?>
        <?php echo h::button('reset', 'clear'); ?>
    </form>
    <hr>
    <?php if ($doAction): ?>
        <p><em>Results :</em></p>
        <?php if ($error): ?>
            <?php echo e::show('Cannot open uploaded file!'); ?>
        <?php else: ?>
            <textarea id="csv-result" style="width: 90%; height: 300px"><?php echo $result; ?></textarea>
        <?php endif; ?>
    <?php else: ?>
        <p><em>We do nothing, Sir!</em></p>
    <?php endif; ?>
</body>
</html>

