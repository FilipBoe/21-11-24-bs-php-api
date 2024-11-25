<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../build/index.css">
    <title><?php echo $title ?? 'Title'; ?></title>
</head>

<body class="p-40 text-white">
    <?php echo $content ?? ''; ?>
</body>

</html>