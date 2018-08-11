<?php
/*require __DIR__. '/protected/bootstrap/autoload.php';
$app = require_once __DIR__. '/protected/bootstrap/start.php';
$request = $app['request'];
$client = (new \Stack\Builder)
    ->push('Illuminate\Cookie\Guard', $app['encrypter'])
    ->push('Illuminate\Cookie\Queue', $app['cookie'])
    ->push('Illuminate\Session\Middleware', $app['session'], null);
$stack = $client->resolve($app);
$stack->handle($request);*/

function public_path($path = '') {
    return __DIR__ . ($path ? '/'.$path : $path);
}

function app_path($path = '') {
    return __DIR__ . '/protected/app' . ($path ? '/'.$path : $path);
}

function content_path($path = '') {
    return __DIR__ . '/content' . ($path ? '/'.$path : $path);
}

if(!function_exists('dd')) {
    function dd($v) {
        die(var_dump($v));
    }
}

/*
 * Update
 */

$updateError = '';
function add_update_error($error) {
    global $updateError;
    $updateError .= "<div class='alert alert-warning'>" . $error . "</div>";
}
function has_update_error() {
    global $updateError;
    return !empty($updateError);
}

function get_update_errors() {
    global $updateError;
    return $updateError;
}

function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir."/".$object))
                    rrmdir($dir."/".$object);
                else
                    unlink($dir."/".$object);
            }
        }
        rmdir($dir);
    }
}

function ensure_writable($path, $isFile = false) {
    $type = "Directory";
    if($isFile)
        $type = "File";
    if(!is_writable($path)) {
        add_update_error("<div>{$type} <span class='nwp'>'{$path}'</span> is not writable. Make it writable.</div>");
        return;
    }
    if(is_dir($path)) {
        foreach (glob(rtrim($path, '/') . '/*') as $dir) {
            if(!is_writable($path)) {
                add_update_error("<div><span class='nwp'>'{$dir}'</span> is not writable. Make it writable.</div>");
            }
        }
    }
}

function writeConfigFile($path, $config) {
    $configFileContent = "<?php \n return " . var_export($config, true) . ";";
    file_put_contents($path, $configFileContent);
}
/*
 * Move media folder
 */
function moveMediaDirectory($simulate = false) {
    $sourcePath = public_path('media');
    $contentPath = public_path('content');
    $targetPath = public_path('content/media');
    if(!file_exists($sourcePath))
        return;

    ensure_writable($sourcePath);
    ensure_writable($contentPath);
    if(file_exists($targetPath)) {
        ensure_writable($targetPath);
    }
    if($simulate)
        return;
    //Remove target path to enable moving
    //rrmdir($targetPath);
    foreach (glob($sourcePath . '/*') as $file) {
        rename($file, $targetPath . '/' . basename($file));
    }

}

/*
 * Get config(DB and admin) and store in new config file.
 */

function extractConfig($simulate = false) {
    $newConfigFile = public_path('config.php');
    ensure_writable($newConfigFile, true);
    if($simulate)
        return;
    $dbConfig = require(app_path('config/database.php'));
    $dbConfig = $dbConfig['connections']['mysql'];
    $newConfig = require($newConfigFile);
    $newConfig['DB_HOST'] = $dbConfig['host'];
    $newConfig['DB_DATABASE'] = $dbConfig['database'];
    $newConfig['DB_USERNAME'] = $dbConfig['username'];
    $newConfig['DB_PASSWORD'] = $dbConfig['password'];

    writeConfigFile($newConfigFile, $newConfig);
}

/*
 * Running migrations
 */
//\Artisan::call('migrate', array('--force' => true));

/*
 * Delete old version files
 */
function getDirectoriesToDelete() {
    $v2Directories = ['application', 'content'];
    $toDelete = [];
    foreach (glob('./*', GLOB_ONLYDIR) as $directory) {
        if(in_array(basename($directory), $v2Directories))
            continue;
        $toDelete[] = $directory;
    }
    return $toDelete;
}

function checkWritePermissions($directories) {
    $notWritable = [];
    foreach ($directories as $directory) {
        if(!is_writable($directory))
            $notWritable[] = $directory;
        else {
            foreach (glob(rtrim($directory, '/') . '/*') as $subdir) {
                if(!is_writable($subdir))
                    $notWritable[] = $subdir;
            }
        }
    }
    return empty($notWritable) ? true : $notWritable;
}

function deleteOldFiles($simulate = false) {
    $installDir = __DIR__;
    //The base directory should be writable to delete the old directories and files
    ensure_writable($installDir);
    $directoriesToDelete = getDirectoriesToDelete();
    $permissionCheck = checkWritePermissions($directoriesToDelete);
    if($permissionCheck !== true) {
        $error = "<b>These folders are not writable. Make them writable</b><br><ol>";
        foreach ($permissionCheck as $dir) {
            $error .= "<li><span class='nwp'>" . public_path(str_replace("./", '', $dir)) . "</span></li>";
        }
        $error .= "</ol>";
        add_update_error($error);
        return;
    }
    if($simulate)
        return;
    foreach ($directoriesToDelete as $dir) {
        rrmdir($dir);
    }
}

function run_update_steps($simulate = false) {
    moveMediaDirectory($simulate);
    extractConfig($simulate);
    deleteOldFiles($simulate);
}

function after_update_checks() {

}

function display_update_errors($updateDone = false) {
    global $updateError;
    if(!$updateDone)
        echo("<h3>Fix these issues to start the update:</h3>");
    else
        echo("<h3>Update has been done. But there are some permission issues you need to fix to run it properly:</h3>");
    ?>
    <div class="row">
        <div class="form-group">
            <label for="inputID" class="col-sm-2 control-label">Default writable permission: </label>
            <div class="col-sm-4">
                <input type="text" id="permissionField" value="755" class="form-control pull-left" style="width: 60px;">
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h4>Note: The required permission number to make files/folders writable may vary with the host.</h4>
                <p><b>Follow these steps:</b></p>
                <ol>
                    <li>Contact your host to know the proper permission number.</li>
                    <li>Then change the permission number suitable for your host in the field "Default writable permission:" above and the commands will be updated automatically.</li>
                    <li>Then run all the commands to make the required files/folders writable.</li>
                    <li>Then REFRESH this page</li>
                </ol>
            </div>
        </div>
    </div>
    <?php
    echo $updateError;
    echo '<h4>All Commands:</h4><textarea rows="5" class="form-control" id="allCommands"></textarea><br><br>';
    echo '<div class="text-center"><span class="btn btn-success btn-lg" onclick="window.location.href = window.location.href;">Click here to retry after fixing the above issues</span></div>';
}

if(empty($_GET['do'])) {
    $onSuccessHtml = '<a class="btn btn-success btn-lg" href="'. $_SERVER['PHP_SELF'] . '?do=true">Go to update script</a>';
    include('check.php');
    die();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Socioquiz</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

    <style>
        body {
            padding-bottom: 100px;
        }
    </style>
</head>
<body>
<br><br><h1 class="text-center">Updating to SocioQuiz v2.0</h1>
<hr>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            /*
             * Simulate the steps first
             */
            run_update_steps(true);

            if(has_update_error()) {
                display_update_errors();
            } else {
                //Run the actual steps
                run_update_steps();
                ?>
                <div class="alert alert-success text-center">
                    <h2>Update successful!</h2>
                </div>
                <?php
                after_update_checks();
                if(has_update_error()) {
                    display_update_errors(true);
                }
            }
            ?>
        </div>
    </div>
</div>
<style>
    .command {
        display: block;
        margin-top:5px;
        padding: 4px 8px;
    }
</style>
<script>
    function updateCommands() {
        var commands = [];
        $('.nwp').each(function () {
            var permission = $('#permissionField').val();
            var parent = $(this).parent();
            parent.children('.command').remove();
            var path = $(this).text();
            path = path.replace(/'/g, '');
            parent.append('<div class="command alert alert-info"><div style="margin-bottom: 3px;"><b>Command:</b></div><input class="command-input form-control" type="text" style="width: 100%;"></div>')
            var command = "chmod -R " + permission + ' "'+ path +'"';
            commands.push(command);
            parent.find('input').val(command)
        })
        $('#allCommands').html(commands.join("\n"))
        $('.command-input').focus(function () {
            $(this).select();
        })
    }
    $('#permissionField').change(updateCommands);
    $('#permissionField').keyup(updateCommands);
    updateCommands();
</script>
</body>
</html>

