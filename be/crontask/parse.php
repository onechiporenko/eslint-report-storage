<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

$remoteHash = getRemoteHash();
$localHash = getLocalHash();

if ($remoteHash === $localHash) {
    echo 'no new commits' . "\n";
    exit(0);
}
doPullRebase();
$diff = getJsFilesDiff($localHash, $remoteHash);
if (!$diff) {
    echo 'no diff' . "\n";
    exit(0);
}
runESLint();

$commitHash = getLastCommitHash();
$commitDate = getLastCommitDate();

$allJsFiles = shell_exec('find ' . APP . '  -type f -name "*.js"');
$allJsFiles = explode("\n", $allJsFiles);

$db = ERS\Db::obtain('localhost', 'root', 'KronuS', 'eslint', '');
$db->connectPDO();
$f = $db->fetchArrayPDO('select * from files');
$f = formatIdsKeys($f, 'path');
$r = $db->fetchArrayPDO('select * from rules');
$r = formatIdsKeys($r, 'name');

$hashExist = $db->queryFirstPDO('select * from reports where hash = ?', ['hash' => $commitHash]);
if ($hashExist) {
    echo $commitHash . ' already exists' . "\n";
    exit(0);
}
$reportId = $db->insertPDO('reports', ['hash' => $commitHash]);
$overallErrors = 0;
$overallWarnings = 0;

$eslintReport = getESLintReport();

$eslintReportMappedByRule = [];
$eslintReportMappedByFile = [];

foreach ($eslintReport as $k => $reportByFile) {
    $filePath = $reportByFile['filePath'];
    if (!array_key_exists($filePath, $f)) {
        $fileId = $db->insertPDO('files', ['path' => $filePath]);
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
            $ruleId = $db->insertPDO('rules', ['name' => $rule]);
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

function formatIdsKeys($data, $k)
{
    $r = [];
    foreach ($data as $row) {
        $r[$row[$k]] = $row['id'];
    }
    return $r;
}

function ambariExec($cmd)
{
    return trim(shell_exec('cd ' . AMBARI . ' && ' . $cmd));
}

function getRemoteHash()
{
    return ambariExec('git ls-remote | grep heads/trunk$ | sed "s/\s.*$//"');
}

function getLocalHash()
{
    return ambariExec('git log --pretty=oneline -n 1 | grep -e "[0-9a-f]\{40\}" -o');
}

function getJsFilesDiff($localHash, $remoteHash)
{
    return ambariExec('git diff --name-only "' . $localHash . '" "' . $remoteHash . '" | grep .*\.js');
}

function doPullRebase() {
    ambariExec('git pull --rebase');
}

function runESLint()
{
    shell_exec(NODE . 'node ' . NODE . 'eslint ' . APP . ' -f json -o ' . ESLINT_OUTPUT_FILE);
}

function getLastCommitHash()
{
    return ambariExec('git log -n 1 | grep -e "[0-9a-f]\{40\}" -o');
}

function getLastCommitDate()
{
    $commitDate = ambariExec('git log -n 1 | grep "Date:" | sed  "s/Date:\s*//"');
    $commitDate = strtotime($commitDate);
    return date('Y-m-d H:i:s', $commitDate);
}

function getESLintReport() {
    $eslintReport = file_get_contents(ESLINT_OUTPUT_FILE);
    return json_decode($eslintReport, true);
}