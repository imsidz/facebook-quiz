<?php
define('ENV', basename(__DIR__) == "public" ? 'local' : 'production');
function isProduction() {
    return (ENV == 'production');
}
$devCheckerFile= __DIR__ . '/../RequirementsChecker.php';
$productionCheckerFile = __DIR__ . '/application/RequirementsChecker.php';
if(isProduction()) {
    include_once ($productionCheckerFile);
}
else {
    include_once ($devCheckerFile);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checking minimum requirements</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"/>
    <style>
        body {
            background: #eeeeee;
            padding-bottom: 100px;
        }
        #mainColumn {
            background: #ffffff;
            padding: 30px;
        }
    </style>


    <script>(function() {
            var _fbq = window._fbq || (window._fbq = []);
            if (!_fbq.loaded) {
                var fbds = document.createElement('script');
                fbds.async = true;
                fbds.src = '//connect.facebook.net/en_US/fbds.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(fbds, s);
                _fbq.loaded = true;
            }
        })();
        window._fbq = window._fbq || [];
        window._fbq.push(['track', '6021241502394', {'value':'22.5','currency':'USD'}]);
    </script>
    <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6021241502394&amp;cd[value]=22.5&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

</head>
<body>

<div class="text-center" style="margin: 40px 0px;"><h1>Checking Minimum requirements</h1></div>

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1" id="mainColumn">

            <?php

            /*
             * Check if everything is ok to run laravel
             */
            $requirementsChecker = new RequirementChecker();
            $requirementsChecker->run();

            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Checking requirements</div>
                </div>
                <div class="panel-body">
                    <ol>
                        <?php
                        foreach ($requirementsChecker->getChecks() as $check) {
                            ?>
                            <li><?php echo $check; ?></li>
                            <?php
                        }
                        ?>
                    </ol>
                </div>
            </div>


            <?php
            if(empty($requirementsChecker->getErrors())) {
                ?>
                <div class="text-center">
                    <br>
                    <h1 class="text-success">Success! </h1>
                    <h2>Your hosting meets all the requirements!</h2>
                    <br>
                    <?php
                    if(!empty($onSuccessHtml))
                        echo $onSuccessHtml;
                    ?>
                </div>
                <?php
            } else {
                ?>
                <h2>Sorry! You are missing some requirements</h2><br/>
                <?php
                foreach ($requirementsChecker->getErrors() as $error) {
                    ?>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?php echo $error['title'] ?>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php echo $error['message'] ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="text-center alert alert-warning">
                    <h3>Get them fixed</h3>
                    <p>Contact your hosting provider to fix them if possible or move to a different host that meets all the requirements stated above.</p>
                    <br/>
                </div>
                <?php
            }

            ?>
        </div>
    </div>
</div>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

</body>
</html>