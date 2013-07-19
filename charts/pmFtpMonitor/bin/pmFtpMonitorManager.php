<?php

define('FUNC_NAME', 'pmFtpMonitor');

require_once 'Net/Gearman/Manager.php';

$config = parse_ini_file('pmFtpMonitor.ini');
if (isset($config) && is_array($config)) {
    $delay = (array_key_exists('delay', $config) && $config['delay'] > 0) ? intval($config['delay']) * 1000 : 2000;
    $gmclient = new GearmanClient();
    if (array_key_exists('srv', $config) && is_array($config['srv'])) {
        $servers = array();
        foreach ($config['srv'] as $srv) {
            if (intval(isAvailableWorker(array($srv))) > 0)
                $servers[] = $srv;
        }
        $gmclient->addServers(implode(',', $servers));
        if (isAvailableWorker($servers) > 0)
            if (array_key_exists('wk', $config)) {
                if (isset($config['wk']) && is_array($config['wk'])) {
                    foreach ($config['wk'] as $wk) {
                        echo "Starting pmFtpMonitor for '$wk' workspace\n";
                        $wait = true;
                        while ($wait)
                            if (isAvailableWorker($config['srv']) == 0)
                                $gmclient->setTimeout($delay);
                            else
                                $wait = false;
                        $job_handle = $gmclient->doBackground(FUNC_NAME, $wk);
                        if ($gmclient->returnCode() == GEARMAN_SUCCESS)
                            echo "[OK]\n";
                        else
                            echo "[ERROR]\n";
                    }
                }
            } else
                echo "There are no workspaces confugured to be processed\n";
        else
            echo "There are no workers available\n";
    }
}

function isAvailableWorker($srvrs) {
    $res = -1;
    if (isset($srvrs) && is_array($srvrs))
        foreach ($srvrs as $srv) {
            $gManager = new Net_Gearman_Manager($srv);
            $status = $gManager->status();
            $gManager->disconnect();
            unset($gManager);
            if (isset($status) && is_array($status) && array_key_exists(FUNC_NAME, $status)) {
                $inQueue = $status[FUNC_NAME]['in_queue'];
                $jobsRunning = $status[FUNC_NAME]['jobs_running'];
                $capableWorkers = $status[FUNC_NAME]['capable_workers'];
                if (intval($capableWorkers) > 0)
                    if (intval($inQueue) > 0)
                        $res = 0;
                    elseif (intval($jobsRunning) > 0)
                        $res = max($capableWorkers - $jobsRunning, 0);
                    else
                        $res = intval($capableWorkers);
            }
        }
    return $res;
}
