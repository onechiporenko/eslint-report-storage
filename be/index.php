<?php

require './config.php';

use ERS\Db;
use ERS\Models\Project;
use ERS\Models\Report;
use ERS\Models\Rule;
use ERS\Models\File;
use ERS\Schemas\ProjectSchema;
use ERS\Schemas\ReportSchema;
use ERS\Schemas\FileSchema;
use ERS\Schemas\RuleSchema;

use \Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\EncoderOptions;

$db = Db::obtain();

$klein = new \Klein\Klein();

function subResForProject($request, $response, $service, $app, $managerName) {
    $uri = parse_url($request->uri());
    $query = array_key_exists('query', $uri) ? $uri['query'] : '';
    parse_str($query, $query);
    $instances = $app->{$managerName}->all($query);
    $response->body($app->encoder->encodeData($instances));
    $response->send();
}

function subResForProjectById($request, $response, $service, $app, $managerName) {
    $instanceId = $request->paramsNamed()->id;
    $instance = $app->{$managerName}->oneById($instanceId);
    if ($instance) {
        $response->body($app->encoder->encodeData($instance));
        $response->send();
    } else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for id "' . $instanceId . '" is not found']]]);
    }
}

$klein->respond(function ($request, $response, $service, $app) {
    $app->register('projectsManager', function () {
        return new ERS\Managers\ProjectsManager();
    });
    $app->register('reportsManager', function () {
        return new ERS\Managers\ReportsManager();
    });
    $app->register('filesManager', function () {
        return new ERS\Managers\FilesManager();
    });
    $app->register('rulesManager', function () {
        return new ERS\Managers\RulesManager();
    });
    $app->register('encoder', function () {
        return Encoder::instance([
            Project::class => ProjectSchema::class,
            Report::class => ReportSchema::class,
            File::class => FileSchema::class,
            Rule::class => RuleSchema::class
        ], new EncoderOptions(JSON_PRETTY_PRINT));
    });
    $response->header('Content-Type', 'application/json');
});

$klein->respond('GET', '/files', function ($request, $response, $service, $app) {
    subResForProject($request, $response, $service, $app, 'filesManager');
});

$klein->respond('GET', '/files/[i:id]', function ($request, $response, $service, $app) {
    subResForProjectById($request, $response, $service, $app, 'filesManager');
});

$klein->respond('GET', '/files/[i:id]/[i:report_id]', function ($request, $response, $service, $app) {
    $fileId = $request->paramsNamed()->id;
    $file = $app->filesManager->oneById($fileId);
    $reportId = $request->paramsNamed()->report_id;
    $report = $app->reportsManager->oneById($reportId);
    if ($file && $report) {
        $pId = $file->project->id;
        $project = $app->projectsManager->oneById($pId);
        $path = str_replace($project->path, '/', $file->path);
        $hash = $report->hash;
        $content = file_get_contents($project->raw . $hash . $path);
        $response->json(['content' => $content]);
    } else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for file id "' . $fileId . '" and report id "' . $reportId . '" not found']]]);
    }
});

$klein->respond('GET', '/files/[i:id]/[i:report_id]/results', function ($request, $response, $service, $app) {
    $fileId = $request->paramsNamed()->id;
    $reportId = $request->paramsNamed()->report_id;
    $details = $app->filesManager->getResultDetailsByReportId($fileId, $reportId);
    if ($details) {
        $response->json($details);
    } else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for file id "' . $fileId . '" and report id "' . $reportId . '" not found']]]);
    }
});

$klein->respond('GET', '/rules', function ($request, $response, $service, $app) {
    subResForProject($request, $response, $service, $app, 'rulesManager');
});

$klein->respond('GET', '/rules/[i:id]', function ($request, $response, $service, $app) {
    subResForProjectById($request, $response, $service, $app, 'rulesManager');
});

$klein->respond('GET', '/reports', function ($request, $response, $service, $app) {
    subResForProject($request, $response, $service, $app, 'reportsManager');
});

$klein->respond('GET', '/reports/[i:id]', function ($request, $response, $service, $app) {
    subResForProjectById($request, $response, $service, $app, 'reportsManager');
});

$klein->respond('DELETE', '/reports/[i:id]', function ($request, $response, $service, $app) {
    $app->reportsManager->deleteById($request->paramsNamed()->id);
});

$klein->respond('GET', '/projects', function ($request, $response, $service, $app) {
    $projects = $app->projectsManager->all();
    $reports = $app->reportsManager->getGroupedByProjectId();
    $files = $app->filesManager->getGroupedByProjectId();
    $rules = $app->rulesManager->getGroupedByProjectId();
    foreach ($projects as $project) {
        $pId = $project->id;
        $project->reports = $reports[$pId];
        $project->rules = $rules[$pId];
        $project->files = $files[$pId];
    }
    $response->body($app->encoder->encodeData($projects));
    $response->send();
});

$klein->respond('GET', '/projects/[i:id]', function ($request, $response, $service, $app) {
    $projectId = $request->paramsNamed()->id;
    $project = $app->projectsManager->oneById($projectId);
    if ($project) {
        $reports = $app->reportsManager->getGroupedByProjectId();
        $files = $app->filesManager->getGroupedByProjectId();
        $rules = $app->rulesManager->getGroupedByProjectId();
        $project->reports = $reports[$projectId];
        $project->files = $files[$projectId];
        $project->rules = $rules[$projectId];
        $response->body($app->encoder->encodeData($project));
        $response->send();
    } else {
        $response->code(404);
        $response->json(['errors' => ['messages' => ['Data for project id "' . $projectId . '" not found']]]);
    }
});

$klein->dispatch();
