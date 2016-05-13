<?php

require './vendor/autoload.php';
require './config.php';

use ERS\Db;
use ERS\ReportsManager;
use ERS\ProjectsManager;
use ERS\FilesManager;
use ERS\RulesManager;

$db = Db::obtain();

$klein = new \Klein\Klein();

$klein->respond('GET', '/projects', function ($request, $response) {
    $pManager = new ProjectsManager();
    $reports = $pManager->getMany();
    $response->json($reports);
});

$klein->respond('GET', '/projects/[i:id]', function ($request, $response) {
    $pManager = new ProjectsManager();
    $projectId = $request->paramsNamed()->id;
    $project = $pManager->getById($projectId);
    if ($project) {
        $response->json($project);
    }
    else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for project id "' . $projectId . '" not found']]]);
    }
});

$klein->respond('GET', '/files', function ($request, $response) {
    $uri = parse_url($request->uri());
    $query = array_key_exists('query', $uri) ? $uri['query'] : '';
    parse_str($query, $query);
    $fManager = new FilesManager();
    $files = $fManager->getMany($query);
    $response->json($files);
});

$klein->respond('GET', '/files/[i:id]', function ($request, $response) {
    $fManager = new FilesManager();
    $fileId = $request->paramsNamed()->id;
    $file = $fManager->getById($fileId);
    if ($file) {
        $response->json($file);
    }
    else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['File with id "'.$fileId.'" not found']]]);
    }
});

$klein->respond('GET', '/files/[i:id]/[i:report_id]', function ($request, $response) {
    $fManager = new FilesManager();
    $fileId = $request->paramsNamed()->id;
    $file = $fManager->getById($fileId);
    $rManager = new ReportsManager();
    $reportId = $request->paramsNamed()->report_id;
    $report = $rManager->getById($reportId);
    $pManager = new ProjectsManager();
    if ($file && $report) {
        $pId = $file['data']['attributes']['project_id'];
        $project = $pManager->getById($pId);
        $path = str_replace($project['data']['attributes']['path'], '/', $file['data']['attributes']['path']);
        $hash = $report['data']['attributes']['hash'];
        $content = file_get_contents($project['data']['attributes']['raw'] . $hash . $path);
        $response->body($content);
    }
    else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for file id "' . $fileId . '" and report id "' . $reportId . '" not found']]]);
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
        $response->json(['errors' => ['messages' => ['Data for file id "' . $fileId . '" and report id "' . $reportId . '" not found']]]);
    }
});

$klein->respond('GET', '/rules', function ($request, $response) {
    $uri = parse_url($request->uri());
    $query = array_key_exists('query', $uri) ? $uri['query'] : '';
    parse_str($query, $query);
    $rManager = new RulesManager();
    $rules = $rManager->getMany($query);
    $response->json($rules);
});

$klein->respond('GET', '/rules/[i:id]', function ($request, $response) {
    $rManager = new RulesManager();
    $ruleId = $request->paramsNamed()->id;
    $rule = $rManager->getById($ruleId);
    if ($rule) {
        $response->json($rule);
    }
    else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for rule id "' . $ruleId . '" not found']]]);
    }
});

$klein->respond('GET', '/reports', function ($request, $response) {
    $uri = parse_url($request->uri());
    $query = array_key_exists('query', $uri) ? $uri['query'] : '';
    parse_str($query, $query);
    $rManager = new ReportsManager();
    $reports = $rManager->getMany($query);
    $response->json($reports);
});

$klein->respond('GET', '/reports/[i:id]', function ($request, $response) {
    $rManager = new ReportsManager();
    $reportId = $request->paramsNamed()->id;
    $report = $rManager->getById($reportId);
    if ($report) {
        $response->json($report);
    }
    else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for report id "' . $reportId . '" not found']]]);
    }
});

$klein->respond('DELETE', '/reports/[i:id]', function ($request) {
    $rManager = new ReportsManager();
    $rManager->deleteById($request->paramsNamed()->id);
});

$klein->dispatch();
