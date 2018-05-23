<?php  // sample Moodle configuration file
die(
    'This is a sample config.php file. The real file is generated during the' . PHP_EOL .
    'installation process. Since the actual config.php is git-ignored, I have added' . PHP_EOL .
    'this file to the repository so we can see what it is supposed to look like. It' . PHP_EOL .
    'isn’t meant to be included in any code, which is why you are seeing this' . PHP_EOL .
    'message.' . PHP_EOL .
    PHP_EOL .
    'TL/DR: You probably meant to include config.php instead.' . PHP_EOL
); 

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'mysql';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodle';
$CFG->dbpass    = 'moodle';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_general_ci',
);

$CFG->wwwroot   = 'http://206.189.226.81:8080';
$CFG->dataroot  = '/var/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
