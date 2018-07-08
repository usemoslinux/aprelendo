<?php
	require_once('header.php')
	?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/solid.css" integrity="sha384-Rw5qeepMFvJVEZdSo1nDQD5B6wX0m7c5Z/pLNvjkB14W6Yki1hKbSEQaX9ffUbWe"
    crossorigin="anonymous">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/fontawesome.css" integrity="sha384-GVa9GOgVQgOk+TNYXu7S/InPTfSDTtBalSgkgqQ7sCik56N9ztlkoTr2f/T44oKV"
    crossorigin="anonymous">

<div class="container mtb">
    <div class="row">
        <div class="col-xs-12">
            <ol class="breadcrumb">
                <li>
                    <a href="texts.php">Home</a>
                </li>
                <li>
                    <a class="active">Word list</a>
                </li>
            </ol>
            <div class="row flex">
                <div class="col-xs-12">
                    <form class="" action="" method="get">
                        <div class="input-group searchbox">
                            <input type="text" id="s" name="s" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>">
                            <div class="input-group-btn">
                                <button type="submit" name="submit" class="btn btn-default">
                                    <i class="glyphicon glyphicon-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php require_once('listwords.php'); ?>
        </div>
    </div>
</div>

<?php require_once('footer.php') ?>