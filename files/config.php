<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'pgsql';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'joskel';
$CFG->dbuser    = 'joskel';
$CFG->dbpass    = 'P@ssw0rd';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

$CFG->wwwroot   = 'http://59.144.171.90';
$CFG->dataroot  = '/var/www/moodledata';
$CFG->admin     = 'admin';
$CFG->xsendfile = 'X-Accel-Redirect';     // Nginx {@see http://wiki.nginx.org/XSendfile}
// If your X-Sendfile implementation (usually Nginx) uses directory aliases specify them
// in the following array setting:
$CFG->directorypermissions = 0777;

/*@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
@ini_set('display_errors', '1');    // NOT FOR PRODUCTION SERVERS!
$CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
$CFG->debugdisplay = 1;             // NOT FOR PRODUCTION SERVERS!*/

require_once('lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!
