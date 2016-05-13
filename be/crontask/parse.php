<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$db = ERS\Db::obtain();

$pManager = new ERS\ProjectsManager();
$rManager = new ERS\RulesManager();
$fManager = new ERS\FilesManager();

$projects = $pManager->getMany();

foreach($projects['data'] as $project) {
    $path = $project['attributes']['path'];
    $pId = $project['id'];
    $remoteHash = getRemoteHash($path);
    $localHash = getLocalHash($path);
    if ($remoteHash === $localHash) {
        echo 'no new commits' . "\n";
        continue;
    }
    doPullRebase($path);
    $diff = getJsFilesDiff($path, $localHash, $remoteHash);
    if (!$diff) {
        echo 'no diff' . "\n";
        continue;
    }
    $subpath = $path . $project['attributes']['subpath'];
    runESLint($subpath);
    $commitHash = getLastCommitHash($path);
    $commitDate = getLastCommitDate($path);

    $allJsFiles = shell_exec('find ' . $subpath . '  -type f -name "*.js"');
    $allJsFiles = explode("\n", $allJsFiles);


    $f = $db->fetchArrayPDO('select * from files where project_id = ?', ['project_id' => $pId]);
    $f = formatIdsKeys($f, 'path');
    $r = $db->fetchArrayPDO('select * from rules where  project_id = ?', ['project_id' => $pId]);
    $r = formatIdsKeys($r, 'name');

    $hashExist = $db->queryFirstPDO('select * from reports where hash = ? and project_id = ?', ['hash' => $commitHash, 'project_id' => $pId]);
    if ($hashExist) {
        echo $commitHash . ' already exists' . "\n";
        continue;
    }
    $reportId = $db->insertPDO('reports', ['hash' => $commitHash, 'project_id' => $pId]);
    $overallErrors = 0;
    $overallWarnings = 0;

    $eslintReport = getESLintReport();

    $eslintReportMappedByRule = [];
    $eslintReportMappedByFile = [];

    foreach ($eslintReport as $k => $reportByFile) {
        $filePath = $reportByFile['filePath'];
        if (!array_key_exists($filePath, $f)) {
            $fileId = $db->insertPDO('files', ['path' => $filePath, 'project_id' => $pId]);
            $f[$filePath] = $fileId;
        }
        $overallErrors += $reportByFile['errorCount'];
        $overallWarnings += $reportByFile['warningCount'];
        $byLines = [];
        if (!array_key_exists($filePath, $eslintReportMappedByFile)) {
            $eslintReportMappedByFile[$filePath] = [
                'file_id' => $f[$filePath],
                'errors' => $reportByFile['errorCount'],
                'warnings' => $reportByFile['warningCount'],
                'report_id' => $reportId
            ];
        }
        $messages = $reportByFile['messages'];
        foreach ($messages as $msg) {
            $rule = $msg['ruleId'];
            if (!array_key_exists($rule, $r)) {
                $ruleId = $db->insertPDO('rules', ['name' => $rule, 'project_id' => $pId]);
                $r[$rule] = $ruleId;
            }
            if (!array_key_exists($rule, $eslintReportMappedByRule)) {
                $eslintReportMappedByRule[$rule] = [
                    'rule_id' => $r[$rule],
                    'errors' => 0,
                    'warnings' => 0,
                    'report_id' => $reportId
                ];
            }
            $byLine = [
                'severity' => $msg['severity'],
                'line' => $msg['line'],
                'column' => $msg['column'],
                'message' => $msg['message'],
                'ruleId' => $msg['ruleId']
            ];
            array_push($byLines, $byLine);
            $type = $msg['severity'] === 2 ? 'errors' : 'warnings';
            $eslintReportMappedByRule[$rule][$type]++;
        }
        $eslintReportMappedByFile[$filePath]['byLines'] = serialize($byLines);
    }

    $db->updatePDO('reports', ['errors' => $overallErrors, 'warnings' => $overallWarnings, 'date' => $commitDate], ['id' => $reportId]);
    $db->insertManyPDO('report_details_by_file', ['file_id', 'errors', 'warnings', 'report_id', 'lines'], $eslintReportMappedByFile);
    $db->insertManyPDO('report_details_by_rule', ['rule_id', 'errors', 'warnings', 'report_id'], $eslintReportMappedByRule);

}

function formatIdsKeys($data, $k)
{
    $r = [];
    foreach ($data as $row) {
        $r[$row[$k]] = $row['id'];
    }
    return $r;
}

function execInDir($dir, $cmd)
{
    return trim(shell_exec('cd ' . $dir . ' && ' . $cmd));
}

function getRemoteHash($dir)
{
    return execInDir($dir, 'git ls-remote | grep heads/trunk$ | sed "s/\s.*$//"');
}

function getLocalHash($dir)
{
    return execInDir($dir, 'git log --pretty=oneline -n 1 | grep -e "[0-9a-f]\{40\}" -o');
}

function getJsFilesDiff($dir, $localHash, $remoteHash)
{
    return execInDir($dir, 'git diff --name-only "' . $localHash . '" "' . $remoteHash . '" | grep "ambari-web"');
}

function doPullRebase($dir) {
    execInDir($dir, 'git pull --rebase');
}

function runESLint($dir)
{
    shell_exec(NODE . 'node ' . NODE . 'eslint ' . $dir . ' -f json -o ' . ESLINT_OUTPUT_FILE);
}

function getLastCommitHash($dir)
{
    return execInDir($dir, 'git log -n 1 | grep -e "[0-9a-f]\{40\}" -o');
}

function getLastCommitDate($dir)
{
    $commitDate = execInDir($dir, 'git log -n 1 | grep "Date:" | sed  "s/Date:\s*//"');
    $commitDate = strtotime($commitDate);
    return date('Y-m-d H:i:s', $commitDate);
}

function getESLintReport() {
    $eslintReport = file_get_contents(ESLINT_OUTPUT_FILE);
    return json_decode($eslintReport, true);
}