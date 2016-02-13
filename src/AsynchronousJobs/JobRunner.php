<?php

namespace oliverde8\AsynchronousJobs;

/**
 * @author      Oliver de Cramer (oliverde8 at gmail.com)
 * @copyright    GNU GENERAL PUBLIC LICENSE
 *                     Version 3, 29 June 2007
 *
 * PHP version 5.3 and above
 *
 * LICENSE: This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see {http://www.gnu.org/licenses/}.
 */
class JobRunner
{
    /** @var JobRunner */
    private static $_instance = null;

    private $_id = null;

    private $pendingJobs = array();

    private $runningJobs = array();

    /**
     *
     *
     * @return JobRunner
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new JobRunner();
        }

        return self::$_instance;
    }

    /**
     * JobRunner constructor.
     */
    protected function __construct()
    {
        $this->_id = md5(spl_object_hash($this) . microtime());
    }

    protected function _prepareDirectory($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected function _lockJob($jobDir)
    {
        echo "$jobDir/lock\n";
        $fp = fopen("$jobDir/lock", "w+");
        if (flock($fp, LOCK_EX)) {
            return $fp;
        } else {
            return false;
        }
    }

    protected function _getJobDirectory(Job $job)
    {
        $jobDir = $this->getDirectory() . '/' . $job->getId();
        $this->_prepareDirectory($jobDir);

        return $jobDir;
    }


    public function getDirectory()
    {
        return 'tmp/' . $this->_id;
    }

    public function start(Job $job)
    {
        $jobDir = $this->_getJobDirectory($job);
        $lockFile = $this->_lockJob($jobDir);

        if ($lockFile) {
            $jobData = new JobData();
            $jobData->lockFile = $lockFile;
            $jobData->job = $job;

            $this->runningJobs[spl_object_hash($job)] = $jobData;

            $data = $job->getData();
            $data['___class'] = get_class($job);

            file_put_contents("$jobDir/in.serialize", serialize($data));

            $cmd = "php " . __DIR__  . "/../../bin/AsynchronousJobsRun.php \"$jobDir\"";
            if (substr(php_uname(), 0, 7) == "Windows"){
                $WshShell = new \COM("WScript.Shell");
                $WshShell->Run("$cmd /C dir /S %windir%", 0, false);
            }
            else {
                exec($cmd . " > /dev/null &");
            }

        } else {
            $this->pendingJobs[] = $job;
        }
    }



    protected function _getJobResult(Job $job)
    {
        $jobDir = $this->_getJobDirectory($job);
        if (file_exists("$jobDir/out.serialize")) {
            $data = file_get_contents("$jobDir/out.serialize");

            $job->setData(unserialize($data));
            $job->end();

            unset($this->runningJobs[spl_object_hash($job)]);

            return true;
        } else {
            return false;
        }
    }

    public function isRunning(Job $job)
    {
        return !$this->_getJobResult($job);
    }
}