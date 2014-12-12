<?hh

// Find the project dir based on composer
$basedir = __DIR__;

do {
    if(file_exists($basedir . '/composer.json') && file_exists($basedir . '/vendor/autoload.php')){
        break;
    }
    $basedir = dirname($basedir);
    if($basedir === '/'){
        // This will cause all of the tests in this suite to fail
        echo 'Could not find the base project directory.';
        exit(1);
    }
} while (true);

spl_autoload_register((string $class) ==> {
    $fName = $basedir . '/FactoryContainer.php';
    if(is_file($fName)) {
        require_once $fName;
    } else {
        echo 'Run \'bin/findfactories Tests/Fixtures\' before running the unit tests.';
        exit(1);
    }
});
