<?php
/**
 * File manipulator abstract class.
 *
 * @package    mahara
 * @subpackage module.objectfs
 * @author     Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace module_objectfs\object_manipulator;

use module_objectfs\object_manipulator\logger;

defined('INTERNAL') || die();

require_once(get_config('docroot') . 'module/objectfs/objectfslib.php');
require_once(get_config('docroot') . 'module/objectfs/classes/s3_file_system.php');
require_once(get_config('docroot') . 'module/objectfs/classes/log/aggregate_logger.php');
require_once(get_config('docroot') . 'module/objectfs/classes/object_manipulator/pusher.php');
require_once(get_config('docroot') . 'module/objectfs/classes/object_manipulator/puller.php');
require_once(get_config('docroot') . 'module/objectfs/classes/object_manipulator/deleter.php');
require_once(get_config('docroot') . 'module/objectfs/classes/object_manipulator/recoverer.php');
require_once(get_config('docroot') . 'artefact/file/lib.php');

abstract class manipulator {

    /**
     * object file system
     *
     * @var object_file_system
     */
    protected $filesystem;

    /**
     * What time the file manipulator should finish execution by.
     *
     * @var int
     */
    protected $finishtime;

    protected $logger;

    /**
     * Manipulator constructor
     *
     * @param object_file_system $filesystem object file system
     * @param int $maxruntime What time the file manipulator should finish execution by
     */
    public function __construct($filesystem, $config) {
         $this->finishtime = time() + $config->maxtaskruntime;
         $this->filesystem = $filesystem;
    }

    /**
     * get candidate content hashes for execution.
     *
     * @return array $candidatehashes candidate content hashes
     */
    abstract public function get_candidate_objects();

    /**
     * Pushes files from local file system to remote.
     *
     * @param  array $candidatehashes content hashes to push
     */
    public function execute($objectrecords) {

        if (!$this->manipulator_can_execute()) {
            mtrace('Objectfs manipulator exiting early');
            return;
        }

        if (count($objectrecords) == 0) {
            mtrace('No candidate objects found.');
            return;
        }

        $this->logger->start_timing();

        foreach ($objectrecords as $objectrecord) {
            if (time() >= $this->finishtime) {
                break;
            }

            // Prepare the file artefact, and try and fix no content hash errors here.
            $fileartefact = new \ArtefactTypeFile($objectrecord->artefact);

            if (empty($fileartefact->get('contenthash'))) {
                $contenthash = $fileartefact::generate_content_hash($fileartefact->get_local_path());
                $fileartefact->set('contenthash', $contenthash);
                $fileartefact->commit();
            }

            $this->filesystem->acquire_object_lock($fileartefact);

            // Object is currently being manipulated elsewhere.
            if (get_field('artefact', 'locked', 'id', $objectrecord->artefact)) {
                continue;
            }

            $newlocation = $this->manipulate_object($objectrecord, $fileartefact);

            update_object_record($fileartefact, $newlocation);

            $this->filesystem->release_object_lock($fileartefact);
        }

        $this->logger->end_timing();
        $this->logger->output_move_statistics();
    }

    protected function manipulator_can_execute() {
        return true;
    }



    public static function get_all_manipulator_classnames() {
        $manipulators = array('deleter',
                              'puller',
                              'pusher',
                              'recoverer');

        foreach ($manipulators as $key => $manipulator) {
            $manipulators[$key] = '\\module_objectfs\\object_manipulator\\' . $manipulator;
        }

        return $manipulators;
    }

    public static function setup_and_run_object_manipulator($manipulatorclassname) {
        $config = get_objectfs_config();

        $shouldtaskrun = module_objectfs_should_tasks_run();

        if ($shouldtaskrun) {
            $logger = new \module_objectfs\log\aggregate_logger();
            $filesystem = new \module_objectfs\s3_file_system();
            $manipulator = new $manipulatorclassname($filesystem, $config, $logger);
            $candidateobjects = $manipulator->get_candidate_objects();
            $manipulator->execute($candidateobjects);
        } else {
            mtrace(get_string('not_enabled', 'module_objectfs'));
        }
    }
}
