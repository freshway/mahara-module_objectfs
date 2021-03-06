<?php
/**
 * azure_file_system class.
 *
 * Remote object storage providers extend this class.
 * At minimum you need to impletment get_external_client.
 *
 * @package    mahara
 * @subpackage module_objectfs
 * @author     Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace module_objectfs;

defined('INTERNAL') || die();

use module_objectfs\object_file_system;
use module_objectfs\client\azure_client;

require_once(get_config('docroot') . 'module/objectfs/objectfslib.php');
require_once(get_config('docroot') . 'module/objectfs/classes/mahara_external_filesystem.php');
require_once(get_config('docroot') . 'module/objectfs/classes/client/azure_client.php');

class azure_file_system extends mahara_external_filesystem {

    protected function get_external_client($config) {
        $azureclient = new azure_client($config);
        return $azureclient;
    }
}
