<?php

require './vendor/autoload.php';
require './config.php';
use ERS\Db;
use ERS\ReportsManager;
use ERS\FilesManager;
use ERS\RulesManager;

$db = Db::obtain('localhost', 'root', 'KronuS', 'eslint', '');
$db->connectPDO();

$klein = new \Klein\Klein();

$klein->respond('GET', '/files', function ($request, $response) {
    $fManager = new FilesManager();
    $files = $fManager->getMany();
    $response->json($files);
});

$klein->respond('GET', '/files/[i:id]', function ($request, $response) {
    $fManager = new FilesManager();
    $file = $fManager->getById($request->paramsNamed()->id);
    if ($file) {
        $response->json($file);
    }
    else {
        $response->code(404);
    }
});

$klein->respond('GET', '/files/[i:id]/[i:report_id]', function ($request, $response) {
    $fManager = new FilesManager();
    $file = $fManager->getById($request->paramsNamed()->id);
    $rManager = new ReportsManager();
    $report = $rManager->getById($request->paramsNamed()->report_id);
    if ($file && $report) {
        $path = str_replace(AMBARI, '', $file['data']['attributes']['path']);
        $hash = $report['data']['attributes']['hash'];
        $content = file_get_contents('https://raw.githubusercontent.com/apache/ambari/' . $hash . $path);
        $response->body($content);
    }
    else {
        $response->code(404);
    }
});

$klein->respond('GET', '/files/[i:id]/[i:report_id]/results', function ($request, $response) {
    $fManager = new FilesManager();
    $fileId = $request->paramsNamed()->id;
    $reportId = $request->paramsNamed()->report_id;
    $details = $fManager->getResultDetailsByReportId($fileId, $reportId);
    if ($details) {
        $response->json($details);
    }
    else {
        $response->code(404);
    }
});

$klein->respond('GET', '/rules', function ($request, $response) {
    $rManager = new RulesManager();
    $rules = $rManager->getMany();
    $response->json($rules);
});

$klein->respond('GET', '/rules/[i:id]', function ($request, $response) {
    $rManager = new RulesManager();
    $rule = $rManager->getById($request->paramsNamed()->id);
    if ($rule) {
        $response->json($rule);
    }
    else {
        $response->code(404);
    }
});

$klein->respond('GET', '/reports', function ($request, $response) {
    $rManager = new ReportsManager();
    $reports = $rManager->getMany();
    $response->json($reports);
});

$klein->respond('GET', '/reports/[i:id]', function ($request, $response) {
    $rManager = new ReportsManager();
    $report = $rManager->getById($request->paramsNamed()->id);
    if ($report) {
        $response->json($report);
    }
    else {
        $response->code(404);
    }
});

$klein->respond('DELETE', '/reports/[i:id]', function ($request) {
    $rManager = new ReportsManager();
    $rManager->deleteById($request->paramsNamed()->id);
});

$klein->dispatch();
