<?php error_reporting(-1); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Gugle v0.1</title>
</head>
<body>
    <form name="search" method="GET">
        <input type="text" name="query" />
        <input type="submit" name="Search" />
    </form>

    <?php if (isset($_GET['query'])): ?>
        <div class="tabs">
            <a href="?query=<?php echo $_GET['query'] ?>&tab=all">All</a>
            <a href="?query=<?php echo $_GET['query'] ?>&tab=images">Images</a>
        </div>
        <?php $results = array('lolcat 1', 'lolcat 2', 'lolcat 3'); ?>
        <?php if (!isset($_GET['tab']) || $_GET['tab'] === 'all'): ?>
            <?php foreach ($results as $result): ?>
                <h3 class="result-item"><a href="#"><?php echo $result ?></a></h3>
            <?php endforeach ?>
        <?php else: ?>
            <?php foreach ($results as $result): ?>
                <img class="result-item" src="/images/cat.png" alt="<?php echo $result ?>" />
            <?php endforeach ?>
        <?php endif ?>
    <?php endif ?>
</body>
</html>
