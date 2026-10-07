<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\DpMission;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Dompdf\Dompdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class TbrExportController extends Controller
{
    function logMemoryUsage(string $label = '')
    {
        $usageBytes = memory_get_usage();
        $usageMB = round($usageBytes / 1024 / 1024, 2);
        $peakBytes = memory_get_peak_usage();
        $peakMB = round($peakBytes / 1024 / 1024, 2);
        $msg = $label ? "[$label] " : '';
        $msg .= "Memória usada: {$usageBytes} bytes ({$usageMB} MB); ";
        $msg .= "Pico de memória: {$peakBytes} bytes ({$peakMB} MB)";
        Log::info($msg);
    }

    public function pdf($eventId)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $this->logMemoryUsage('Início do método PDF');

        $event = Event::with('teams.scores')->find($eventId);

        if (!$event) {
            return abort(404, 'Evento não encontrado');
        }

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
    'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
    'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');

        $topPositions = 3;
        $generalTopPositions = 3;

        $backgroundPath = storage_path('app/public/tbr/image/apresentacao/img.jpg');
        if (!file_exists($backgroundPath)) {
            abort(500, 'Imagem de fundo não encontrada.');
        }
        $backgroundData = base64_encode(file_get_contents($backgroundPath));
        $backgroundBase64 = 'data:image/jpg;base64,' . $backgroundData;

        $html = <<<HTML
    <style>
        @page { size: A4 landscape; margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            height: 210mm;
            width: 100%;
            font-family: Arial, sans-serif;
            background-image: url("$backgroundBase64");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
        }
        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;
            background-color: rgba(0, 0, 0, 0.5);
            box-sizing: border-box;
        }
        .content-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;        
        }
        h1, h2, h3 {
            margin: 0;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.7);
        }
        .title-event {
            font-size: 5rem;
            font-weight: bold;
        }
        .title-category {
            font-size: 5rem;
            font-weight: bold;
        }
        .title-sub {
            font-size: 4rem;
            font-weight: bold;
        }
        .position {
            font-size: 4rem;
            font-weight: bold;
            color: #FFCC00;
        }
        .team-name {
            font-size: 3rem;
            font-weight: bold;
            color: #00AAFF;
        }
        .thank-you {
            font-size: 72px;
            font-weight: bold;
        }
    </style>
    HTML;

        $html .= '<div class="page"><div class="content-center">';
        $html .= '<h1 class="title-event">' . htmlspecialchars($event->name ?? 'Evento') . '</h1>';
        $html .= '</div></div>';

        $isInterno = ($event->tipo_evento ?? 'interno') === 'interno';

        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $catLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];

            $teamsCategory = $event->teams
                ->filter(fn($t) => ($t->category_slug ?? '') === $catSlug)
                ->values();

            if ($teamsCategory->isEmpty()) continue;

            if ($catSlug === 'baby' && $generalTopPositions > 0) {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => (float) $t->total_score)->values();
                $teamsTopTotal = $teamsSortedTotal->take($generalTopPositions)->reverse()->values();

                foreach ($teamsTopTotal as $pos => $team) {
                    $posNumber = $generalTopPositions - $pos;

                    $html .= '<div class="page"><div class="content-center">';
                    $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                    $html .= "<h3 class='title-sub'>Nota Geral</h3>";
                    $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                    $html .= '</div></div>';

                    $html .= '<div class="page"><div class="content-center">';
                    $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                    $html .= "<h3 class='title-sub'>Nota Geral</h3>";
                    $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                    $html .= "<h2 class='team-name'>" . htmlspecialchars($team->name) . "</h2>";
                    $html .= '</div></div>';
                }
                continue;
            }

            if ($topPositions > 0 && !$isInterno) {
                foreach ($modalities as $mod) {
                    $modSlug = $mod['slug'];
                    $modLabel = $mod['label'];

                    $teamsSorted = $teamsCategory->sortByDesc(function ($t) use ($modSlug) {
                        $scores = $t->scores->where('modality_slug', $modSlug);
                        if ($modSlug === 'dp') {
                            return (float) $scores->max('total');
                        }
                        $first = $scores->first();
                        return $first ? (float) $first->total : 0;
                    })->values();

                    if ($teamsSorted->isEmpty()) continue;

                    $teamsTop = $teamsSorted->take($topPositions)->reverse()->values();

                    foreach ($teamsTop as $pos => $team) {
                        $posNumber = $topPositions - $pos;

                        $html .= '<div class="page"><div class="content-center">';
                        $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                        $html .= "<h2 class='title-category'>{$modLabel}</h2>";
                        $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                        $html .= '</div></div>';

                        $html .= '<div class="page"><div class="content-center">';
                        $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                        $html .= "<h2 class='title-category'>{$modLabel}</h2>";
                        $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                        $html .= "<h2 class='team-name'>" . htmlspecialchars($team->name) . "</h2>";
                        $html .= '</div></div>';
                    }
                }
            }

            if ($generalTopPositions > 0 && $catSlug !== 'baby') {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => (float) $t->total_score)->values();
                $teamsTopTotal = $teamsSortedTotal->take($generalTopPositions)->reverse()->values();

                foreach ($teamsTopTotal as $pos => $team) {
                    $posNumber = $generalTopPositions - $pos;

                    $html .= '<div class="page"><div class="content-center">';
                    $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                    $html .= "<h3 class='title-sub'>Nota Geral</h3>";
                    $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                    $html .= '</div></div>';

                    $html .= '<div class="page"><div class="content-center">';
                    $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                    $html .= "<h3 class='title-sub'>Nota Geral</h3>";
                    $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                    $html .= "<h2 class='team-name'>" . htmlspecialchars($team->name) . "</h2>";
                    $html .= '</div></div>';
                }
            }
        }

        $html .= '<div class="page"><div class="content-center">';
        $html .= '<h1 class="thank-you">Muito obrigado pela participação!</h1>';
        $html .= '</div></div>';

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $this->logMemoryUsage('Após gerar PDF');

        $eventName = Str::slug($event->name ?? 'evento', '_');
        $eventDate = $event->date?->format('Y-m-d') ?? now()->format('Y-m-d');
        $eventDateSlug = Str::slug($eventDate, '_');
        $fileName = strtolower("{$eventName}_{$eventDateSlug}.pdf");

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function teamPdf($eventId, $teamId)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $event = Event::with('teams.scores')->find($eventId);
        if (!$event) return abort(404, 'Evento não encontrado');

        $team = $event->teams->firstWhere('id', $teamId);
        if (!$team) return abort(404, 'Equipe não encontrada');

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
            'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
        ])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $questionsByLevel = config('tbr-config.questions_by_level');
        $dpByLevel = config('tbr-config.dp_by_level');

        $category = collect($categories)->firstWhere('slug', $team->category_slug);
        if (!$category) return abort(404, 'Categoria não encontrada');

        $categorySlug = $category['slug'];
        $categoryLabel = $category['label'];
        $modalitieLevel = $category['modalitie'];
        $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];
        $questionsConfigLevel = $questionsByLevel[$modalitieLevel] ?? [];
        $dpConfigLevel = $dpByLevel[$category['dp']] ?? [];

        $teamsCategory = $event->teams->filter(fn($t) => $t->category_slug === $categorySlug)
            ->sortByDesc(fn($t) => (float) $t->total_score)->values();

        $position = collect($teamsCategory)->search(fn($t) => $t->id === $team->id);
        $position = $position !== false ? $position + 1 : null;

        $pdfConfig = $event->ranking_config['pdf'] ?? [];
        $dpMissions = $event->ranking_config['dp_missions']
            ?? DpMission::where('year', $event->date?->year ?? date('Y'))
                ->where('dp_level', $category['dp'])
                ->orderBy('sort_order')
                ->pluck('mission_title')
                ->toArray()
            ?: null;

        $html = $this->buildTeamPdfHtml($event, $team, $categoryLabel, $modalities, $questionsConfigLevel, $dpConfigLevel, $teamsCategory, $position, $pdfConfig, $dpMissions);

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = Str::slug($team->name, '_') . '.pdf';
        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function teamModalityPdf($eventId, $teamId, $modalitySlug)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $event = Event::with('teams.scores')->find($eventId);
        if (!$event) return abort(404, 'Evento não encontrado');

        $team = $event->teams->firstWhere('id', $teamId);
        if (!$team) return abort(404, 'Equipe não encontrada');

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
            'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
        ])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $questionsByLevel = config('tbr-config.questions_by_level');
        $dpByLevel = config('tbr-config.dp_by_level');

        $category = collect($categories)->firstWhere('slug', $team->category_slug);
        if (!$category) return abort(404, 'Categoria não encontrada');

        $categorySlug = $category['slug'];
        $categoryLabel = $category['label'];
        $modalitieLevel = $category['modalitie'];
        $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];
        $questionsConfigLevel = $questionsByLevel[$modalitieLevel] ?? [];
        $dpConfigLevel = $dpByLevel[$category['dp']] ?? [];

        $teamsCategory = $event->teams->filter(fn($t) => $t->category_slug === $categorySlug)
            ->sortByDesc(fn($t) => (float) $t->total_score)->values();

        $position = collect($teamsCategory)->search(fn($t) => $t->id === $team->id);
        $position = $position !== false ? $position + 1 : null;

        $pdfConfig = $event->ranking_config['pdf'] ?? [];
        $dpMissions = $event->ranking_config['dp_missions']
            ?? DpMission::where('year', $event->date?->year ?? date('Y'))
                ->where('dp_level', $category['dp'])
                ->orderBy('sort_order')
                ->pluck('mission_title')
                ->toArray()
            ?: null;

        $mod = collect($modalities)->firstWhere('slug', $modalitySlug);
        if (!$mod) return abort(404, 'Modalidade não encontrada');
        $modLabel = $mod['label'];

        $html = $this->buildTeamModalityPdfHtml($event, $team, $categoryLabel, $modalitySlug, $modLabel, $questionsConfigLevel, $dpConfigLevel, $teamsCategory, $position, $pdfConfig, $dpMissions);

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = Str::slug($team->name, '_') . '_' . $modalitySlug . '.pdf';
        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    private function buildTeamPdfHtml($event, $team, $categoryLabel, $modalities, $questionsConfigLevel, $dpConfigLevel, $teamsCategory = null, $position = null, $pdfConfig = [], $dpMissions = null)
    {
        $showPosition = $pdfConfig['show_position'] ?? true;
        $showTotal = $pdfConfig['show_total'] ?? true;
        $showModalities = $pdfConfig['show_modalities'] ?? true;

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
        <meta charset="UTF-8">
        <style>
        @page { size: A4 portrait; margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            height: 297mm;
            width: 100%;
            font-family: Arial, sans-serif;
            color: rgba(0, 0, 0, 1);
        }
        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;
            background-color: rgba(255, 255, 255, 1);
            box-sizing: border-box;
            color: rgba(0, 0, 0, 1);
        }
        .content-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
        }
        .content-normal {
            width: 100%;
            text-align: center;
        }
        h1.title-event { font-size: 26pt; margin-bottom: 10pt; }
        h2.team-name { font-size: 22pt; color: #00AAFF; margin-bottom: 6pt; }
        p.position, p.total, p.category {
            font-size: 14pt;
            font-weight: bold;
            color: #FFCC00;
            margin: 0;
        }
        h2.modality-title {
            font-size: 18pt;
            padding: 10px;
            text-align: center;
            color: rgba(0, 0, 0, 1);
        }
        .tables-wrapper {
            display: flex;
            justify-content: space-between;
            width: 100%;
            position: relative;
        }
        .table-container_direita{
            width: 60%;
        }
        .table-container_esquerda {
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            color: rgba(0, 0, 0, 1);
        }
        .bloco_filho_esquerda {
            position: absolute;
            left: 10px;
            top: 0px;
        }
        .bloco_filho_direita {
            position: absolute;
            right: 10px;
            top: 0px;
        }
        .bloco_filho_direita table{
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid rgba(0, 0, 0, 1);
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: rgba(255, 255, 255, 0.1);
        }
        td.desc {
            text-align: left;
        }
        .mission-header {
            background-color: rgba(0, 170, 255, 0.1);
            font-weight: bold;
            text-align: left;
            padding-left: 5px;
        }
        .comment {
            font-size: 7pt;
            font-style: italic;
            color: rgba(0, 0, 0, 1);
        }
        </style>
        </head>
        <body>
        HTML;

        $html .= '<div class="page">';
        $html .= '<div class="content-center">';
        $html .= '<h1 class="title-event">' . htmlspecialchars($event->name) . '</h1>';
        $html .= '<h2 class="team-name">' . htmlspecialchars($team->name) . '</h2>';
        $html .= '<p class="category">Categoria: ' . htmlspecialchars($categoryLabel) . '</p>';
        if ($showPosition && $position) {
            $html .= '<p class="position">' . $position . 'º Lugar</p>';
        }
        if ($showTotal) {
            $html .= '<p class="total">Nota Total: ' . round($team->total_score) . '</p>';
        }
        $html .= '</div></div>';

        if ($showModalities) {
            foreach ($modalities as $mod) {
                $html .= $this->renderModalityHtml($mod, $team, $questionsConfigLevel, $dpConfigLevel, $teamsCategory, $dpMissions);
            }
        }

        $html .= '</body></html>';
        return $html;
    }

    private function buildTeamModalityPdfHtml($event, $team, $categoryLabel, $modalitySlug, $modLabel, $questionsConfigLevel, $dpConfigLevel, $teamsCategory = null, $position = null, $pdfConfig = [], $dpMissions = null)
    {
        $showPosition = $pdfConfig['show_position'] ?? true;
        $showTotal = $pdfConfig['show_total'] ?? true;

        $mod = ['slug' => $modalitySlug, 'label' => $modLabel];

        $html = <<<HTML
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
        <meta charset="UTF-8">
        <style>
        @page { size: A4 portrait; margin: 0; }
        html, body {
            margin: 0;
            padding: 0;
            height: 297mm;
            width: 100%;
            font-family: Arial, sans-serif;
            color: rgba(0, 0, 0, 1);
        }
        .page {
            width: 100%;
            height: 100%;
            page-break-after: always;
            background-color: rgba(255, 255, 255, 1);
            box-sizing: border-box;
            color: rgba(0, 0, 0, 1);
        }
        .content-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
        }
        .content-normal {
            width: 100%;
            text-align: center;
        }
        h1.title-event { font-size: 26pt; margin-bottom: 10pt; }
        h2.team-name { font-size: 22pt; color: #00AAFF; margin-bottom: 6pt; }
        p.position, p.total, p.category {
            font-size: 14pt;
            font-weight: bold;
            color: #FFCC00;
            margin: 0;
        }
        h2.modality-title {
            font-size: 18pt;
            padding: 10px;
            text-align: center;
            color: rgba(0, 0, 0, 1);
        }
        .tables-wrapper {
            display: flex;
            justify-content: space-between;
            width: 100%;
            position: relative;
        }
        .table-container_direita{
            width: 60%;
        }
        .table-container_esquerda {
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            color: rgba(0, 0, 0, 1);
        }
        .bloco_filho_esquerda {
            position: absolute;
            left: 10px;
            top: 0px;
        }
        .bloco_filho_direita {
            position: absolute;
            right: 10px;
            top: 0px;
        }
        .bloco_filho_direita table{
            margin-bottom: 10px;
        }
        th, td {
            border: 1px solid rgba(0, 0, 0, 1);
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: rgba(255, 255, 255, 0.1);
        }
        td.desc {
            text-align: left;
        }
        .mission-header {
            background-color: rgba(0, 170, 255, 0.1);
            font-weight: bold;
            text-align: left;
            padding-left: 5px;
        }
        .comment {
            font-size: 7pt;
            font-style: italic;
            color: rgba(0, 0, 0, 1);
        }
        </style>
        </head>
        <body>
        HTML;

        $html .= '<div class="page">';
        $html .= '<div class="content-center">';
        $html .= '<h1 class="title-event">' . htmlspecialchars($event->name) . '</h1>';
        $html .= '<h2 class="team-name">' . htmlspecialchars($team->name) . '</h2>';
        $html .= '<p class="category">Categoria: ' . htmlspecialchars($categoryLabel) . '</p>';
        if ($showPosition && $position) {
            $html .= '<p class="position">' . $position . 'º Lugar</p>';
        }
        if ($showTotal) {
            $html .= '<p class="total">Nota Total: ' . round($team->total_score) . '</p>';
        }
        $html .= '</div></div>';

        $html .= $this->renderModalityHtml($mod, $team, $questionsConfigLevel, $dpConfigLevel, $teamsCategory, $dpMissions);

        $html .= '</body></html>';
        return $html;
    }

    private function renderModalityHtml($mod, $team, $questionsConfigLevel, $dpConfigLevel, $teamsCategory = null, $dpMissions = null)
    {
        $modSlug = $mod['slug'];
        $modLabel = $mod['label'];

        $questionsConfigList = [];
        foreach ($questionsConfigLevel as $qConf) {
            if ($qConf['modality'] === $modSlug) {
                $questionsConfigList = $qConf['assessment'] ?? [];
                break;
            }
        }

        $teamScores = $team->scores->where('modality_slug', $modSlug);
        $teamNotes = [];
        $teamComment = '';

        if ($modSlug === 'dp') {
            $bestRound = $teamScores->sortByDesc('total')->first();
            $teamNotes = $bestRound?->scores ?? [];
            $teamComment = $bestRound?->comment ?? '';
            $notaEquipe = (float) ($bestRound?->total ?? 0);
        } else {
            $firstScore = $teamScores->first();
            $teamNotes = $firstScore?->scores ?? [];
            $teamComment = $firstScore?->comment ?? '';
            $notaEquipe = (float) ($firstScore?->total ?? 0);
        }

        $notasModalidade = [];
        if ($teamsCategory) {
            foreach ($teamsCategory as $catTeam) {
                $catScores = $catTeam->scores->where('modality_slug', $modSlug);
                if ($modSlug === 'dp') {
                    $best = $catScores->sortByDesc('total')->first();
                    if ($best) $notasModalidade[] = (float) $best->total;
                } else {
                    $first = $catScores->first();
                    if ($first) $notasModalidade[] = (float) $first->total;
                }
            }
        }
        $media = count($notasModalidade) ? array_sum($notasModalidade) / count($notasModalidade) : 0;
        $maxima = count($notasModalidade) ? max($notasModalidade) : 0;

        $html = '<div class="page">';
        $html .= '<div class="content-normal">';
        $html .= '<h2 class="modality-title">' . htmlspecialchars($modLabel) . '</h2>';
        $html .= '<div class="tables-wrapper">';

        $html .= '<div class="table-container_direita bloco_filho_esquerda"><table><thead><tr><th>Descrição</th><th style="width: 70px;">Nota</th></tr></thead><tbody>';
        if ($modSlug == 'dp') {
            if (!empty($dpConfigLevel)) {
                $notasRoundMaior = $teamNotes;
                foreach ($dpConfigLevel as $mIndex => $block) {
                    $missionName = $dpMissions[$mIndex] ?? $block['mission'];
                    $html .= '<tr><td colspan="2" class="mission-header">' . htmlspecialchars($missionName) . '</td></tr>';
                    $notaMissao = isset($notasRoundMaior[$mIndex]) ? round(floatval($notasRoundMaior[$mIndex])) : '0';
                    foreach ($block['itens'] as $item) {
                        $html .= '<tr>';
                        $html .= '<td class="desc" style="padding-left: 15px; color: #555;">• ' . htmlspecialchars($item['name']) . ' <small style="color:#aaa;">(' . strtoupper($item['type']) . ')</small></td>';
                        $html .= '<td style="color: #aaa;">—</td>';
                        $html .= '</tr>';
                    }
                    $html .= '<tr style="background-color: #fafafa; font-weight: bold;">';
                    $html .= '<td class="desc" style="padding-left: 10px; color: #0284c7;">Subtotal da Missão</td>';
                    $html .= '<td style="color: #0284c7;">' . $notaMissao . '</td>';
                    $html .= '</tr>';
                }
            } else {
                foreach ($teamNotes as $i => $nota) {
                    $missionName = $dpMissions[$i] ?? ('Missão ' . ($i + 1));
                    $html .= '<tr><td class="desc">' . htmlspecialchars($missionName) . '</td><td>' . round(floatval($nota)) . '</td></tr>';
                }
            }
        } else {
            $noteIndex = 0;
            foreach ($questionsConfigList as $block) {
                foreach ($block['description'] ?? [] as $desc) {
                    $nota = isset($teamNotes[$noteIndex]) ? round(floatval($teamNotes[$noteIndex])) : '-';
                    $html .= '<tr><td class="desc">' . htmlspecialchars($desc) . '</td><td>' . $nota . '</td></tr>';
                    $noteIndex++;
                }
            }
        }

        $html .= '</tbody></table></div>';

        $html .= '<div class="table-container_esquerda bloco_filho_direita">';
        $html .= '<table><thead><tr><th>Nota da Equipe</th></tr></thead><tbody>';
        $html .= '<tr><td style="font-size: 11pt; font-weight: bold; color: #00AAFF;">' . round($notaEquipe) . '</td></tr>';
        $html .= '</tbody></table>';

        if ($teamsCategory) {
            $html .= '<table><thead><tr><th>Média da Categoria</th></tr></thead><tbody>';
            $html .= '<tr><td>' . round($media) . '</td></tr>';
            $html .= '</tbody></table>';
            $html .= '<table><thead><tr><th>Nota Máxima</th></tr></thead><tbody>';
            $html .= '<tr><td>' . round($maxima) . '</td></tr>';
            $html .= '</tbody></table>';
        }

        $html .= '<table><thead><tr><th>Comentário</th></tr></thead><tbody>';
        $html .= '<tr><td class="comment" style="text-align: left; padding: 6px;">' . ($teamComment ? nl2br(htmlspecialchars($teamComment)) : '-') . '</td></tr>';
        $html .= '</tbody></table>';
        $html .= '</div></div>';
        $html .= '</div></div>';

        return $html;
    }

    public function scoresPdf($eventId)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $event = Event::with('teams.scores')->find($eventId);
        if (!$event) {
            return abort(404, 'Evento não encontrado');
        }

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
    'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
    'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $questionsByLevel = config('tbr-config.questions_by_level');
        $dpByLevel = config('tbr-config.dp_by_level');

        $eventNameSlug = Str::slug($event->name ?? 'evento');
        $tempDir = storage_path("app/public/tmp/{$eventNameSlug}");

        if (File::exists($tempDir)) File::deleteDirectory($tempDir);
        File::makeDirectory($tempDir, 0755, true);

        $teamsCategory = [];
        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $teams = $event->teams->filter(fn($t) => $t->category_slug === $catSlug)
                ->sortByDesc(fn($t) => (float) $t->total_score)->values();
            $teamsCategory[$catSlug] = $teams;
        }

        foreach ($event->teams as $team) {
            $teamId = $team->id ?? null;
            if (!$teamId) continue;

            $categorySlug = $team->category_slug;
            $category = collect($categories)->firstWhere('slug', $categorySlug);
            if (!$category) continue;

            $categoryLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];
            $questionsConfigLevel = $questionsByLevel[$modalitieLevel] ?? [];
            $dpConfigLevel = $dpByLevel[$category['dp']] ?? [];

            $dpMissions = $event->ranking_config['dp_missions']
                ?? DpMission::where('year', $event->date?->year ?? date('Y'))
                    ->where('dp_level', $category['dp'])
                    ->orderBy('sort_order')
                    ->pluck('mission_title')
                    ->toArray()
                ?: null;

            $position = collect($teamsCategory[$categorySlug])->search(fn($t) => $t->id === $teamId) + 1;

            $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
            <meta charset="UTF-8">
            <style>
            @page { size: A4 portrait; margin: 0; }
            html, body {
                margin: 0;
                padding: 0;
                height: 297mm;
                width: 100%;
                font-family: Arial, sans-serif;
                color: rgba(0, 0, 0, 1);
            }
            .page {
                width: 100%;
                height: 100%;
                page-break-after: always;
                background-color: rgba(255, 255, 255, 1);
                box-sizing: border-box;
                color: rgba(0, 0, 0, 1);
            }
            .content-center {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                text-align: center;        
            }
            .content-normal {
                width: 100%;
                text-align: center;   
            }
            h1.title-event { font-size: 26pt; margin-bottom: 10pt; }
            h2.team-name { font-size: 22pt; color: #00AAFF; margin-bottom: 6pt; }
            p.position, p.total, p.category {
                font-size: 14pt;
                font-weight: bold;
                color: #FFCC00;
                margin: 0;
            }
            h2.modality-title {
                font-size: 18pt;
                padding: 10px;
                text-align: center;
                color: rgba(0, 0, 0, 1);
            }
            .tables-wrapper {                
                display: flex;
                justify-content: space-between;
                width: 100%;                
                position: relative;                
            }
            .table-container_direita{
                width: 60%;
            }
            .table-container_esquerda {
                width: 30%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                color: rgba(0, 0, 0, 1);                
            }
            .bloco_filho_direita {
                position: absolute;
                right: 10px;
                top: 0px;
            }
            .bloco_filho_esquerda {
                position: absolute;
                left: 10px;
                top: 0px;
            }
            .bloco_filho_direita table{
                margin-bottom: 10px;
            }
            th, td {
                border: 1px solid rgba(0, 0, 0, 1);
                padding: 4px;
                text-align: center;
            }
            th {
                background-color: rgba(255, 255, 255, 0.1);
            }
            td.desc {
                text-align: left;
            }
            .mission-header {
                background-color: rgba(0, 170, 255, 0.1);
                font-weight: bold;
                text-align: left;
                padding-left: 5px;
            }
            .comment {
                font-size: 7pt;
                font-style: italic;
                color: rgba(0, 0, 0, 1);                
            }
            </style>
            </head>
            <body>
            HTML;

            $html .= '<div class="page">';
            $html .= '<div class="content-center">';
            $html .= '<h1 class="title-event">' . htmlspecialchars($event->name) . '</h1>';
            $html .= '<h2 class="team-name">' . htmlspecialchars($team->name) . '</h2>';
            $html .= '<p class="position">Posição Geral: ' . $position . '</p>';
            $html .= '<p class="total">Nota Total: ' . round($team->total_score) . '</p>';
            $html .= '<p class="category">Categoria: ' . htmlspecialchars($categoryLabel) . '</p>';
            $html .= '</div></div>';

            foreach ($modalities as $mod) {
                $modSlug = $mod['slug'];
                $modLabel = $mod['label'];

                $questionsConfigList = [];
                foreach ($questionsConfigLevel as $qConf) {
                    if ($qConf['modality'] === $modSlug) {
                        $questionsConfigList = $qConf['assessment'] ?? [];
                        break;
                    }
                }

                $teamScores = $team->scores->where('modality_slug', $modSlug);
                $teamNotes = [];
                $teamComment = '';

                if ($modSlug === 'dp') {
                    $bestRound = $teamScores->sortByDesc('total')->first();
                    $teamNotes = $bestRound?->scores ?? [];
                    $teamComment = $bestRound?->comment ?? '';
                    $notaEquipe = (float) ($bestRound?->total ?? 0);
                } else {
                    $firstScore = $teamScores->first();
                    $teamNotes = $firstScore?->scores ?? [];
                    $teamComment = $firstScore?->comment ?? '';
                    $notaEquipe = (float) ($firstScore?->total ?? 0);
                }

                $notasModalidade = [];
                foreach ($teamsCategory[$categorySlug] as $catTeam) {
                    $catScores = $catTeam->scores->where('modality_slug', $modSlug);
                    if ($modSlug === 'dp') {
                        $best = $catScores->sortByDesc('total')->first();
                        if ($best) $notasModalidade[] = (float) $best->total;
                    } else {
                        $first = $catScores->first();
                        if ($first) $notasModalidade[] = (float) $first->total;
                    }
                }

                $media = count($notasModalidade) ? array_sum($notasModalidade) / count($notasModalidade) : 0;
                $maxima = count($notasModalidade) ? max($notasModalidade) : 0;

                $html .= '<div class="page">';
                $html .= '<div class="content-normal">';
                $html .= '<h2 class="modality-title">' . htmlspecialchars($modLabel) . '</h2>';
                $html .= '<div class="tables-wrapper">';

                $html .= '<div class="table-container_direita bloco_filho_esquerda"><table><thead><tr><th>Descrição</th><th style="width: 70px;">Nota</th></tr></thead><tbody>';
                if ($modSlug == 'dp') {
                    $notasRoundMaior = $teamNotes;

                    foreach ($dpConfigLevel as $mIndex => $block) {
                        $missionName = $dpMissions[$mIndex] ?? $block['mission'];
                        $html .= '<tr><td colspan="2" class="mission-header">' . htmlspecialchars($missionName) . '</td></tr>';
                        $notaMissao = isset($notasRoundMaior[$mIndex]) ? round(floatval($notasRoundMaior[$mIndex])) : '0';

                        foreach ($block['itens'] as $item) {
                            $html .= '<tr>';
                            $html .= '<td class="desc" style="padding-left: 15px; color: #555;">• ' . htmlspecialchars($item['name']) . ' <small style="color:#aaa;">(' . strtoupper($item['type']) . ')</small></td>';
                            $html .= '<td style="color: #aaa;">—</td>';
                            $html .= '</tr>';
                        }

                        $html .= '<tr style="background-color: #fafafa; font-weight: bold;">';
                        $html .= '<td class="desc" style="padding-left: 10px; color: #0284c7;">Subtotal da Missão</td>';
                        $html .= '<td style="color: #0284c7;">' . $notaMissao . '</td>';
                        $html .= '</tr>';
                    }
                } else {
                    $noteIndex = 0;
                    foreach ($questionsConfigList as $block) {
                        foreach ($block['description'] ?? [] as $desc) {
                            $nota = isset($teamNotes[$noteIndex]) ? round(floatval($teamNotes[$noteIndex])) : '-';
                            $html .= '<tr><td class="desc">' . htmlspecialchars($desc) . '</td><td>' . $nota . '</td></tr>';
                            $noteIndex++;
                        }
                    }
                }

                $html .= '</tbody></table></div>';

                $html .= '<div class="table-container_esquerda bloco_filho_direita">';

                $html .= '<table><thead><tr><th>Nota da Equipe</th></tr></thead><tbody>';
                $html .= '<tr><td style="font-size: 11pt; font-weight: bold; color: #00AAFF;">' . round($notaEquipe) . '</td></tr>';
                $html .= '</tbody></table>';

                $html .= '<table><thead><tr><th>Média da Categoria</th></tr></thead><tbody>';
                $html .= '<tr><td>' . round($media) . '</td></tr>';
                $html .= '</tbody></table>';

                $html .= '<table><thead><tr><th>Nota Máxima</th></tr></thead><tbody>';
                $html .= '<tr><td>' . round($maxima) . '</td></tr>';
                $html .= '</tbody></table>';

                $html .= '<table><thead><tr><th>Comentário</th></tr></thead><tbody>';
                $html .= '<tr><td class="comment" style="text-align: left; padding: 6px;">' . ($teamComment ? nl2br(htmlspecialchars($teamComment)) : '-') . '</td></tr>';
                $html .= '</tbody></table>';

                $html .= '</div></div>';
                $html .= '</div></div>';
            }

            $html .= '</body></html>';

            $dompdf = new Dompdf(['enable_remote' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $categoryDir = "{$tempDir}/{$categorySlug}";
            if (!File::exists($categoryDir)) {
                File::makeDirectory($categoryDir, 0755, true);
            }

            $fileName = Str::slug($team->name, '_') . '.pdf';
            file_put_contents("{$categoryDir}/{$fileName}", $dompdf->output());
        }

        $zipPath = storage_path("app/public/tmp/{$eventNameSlug}.zip");
        if (File::exists($zipPath)) File::delete($zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempDir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function scoresPdfFiltered($eventId)
    {
        $selectedModalities = request()->query('modalities', []);
        if (empty($selectedModalities)) {
            return $this->scoresPdf($eventId);
        }

        $selectedModalities = is_array($selectedModalities) ? $selectedModalities : explode(',', $selectedModalities);

        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $event = Event::with('teams.scores')->find($eventId);
        if (!$event) {
            return abort(404, 'Evento não encontrado');
        }

        $categories = Category::orderBy('sort_order')->get()->map(fn($c) => [
            'slug' => $c->slug, 'label' => $c->label, 'id' => $c->id,
            'modalitie' => $c->modality_level, 'question' => $c->question_level, 'dp' => $c->dp_level,
        ])->toArray();
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $questionsByLevel = config('tbr-config.questions_by_level');
        $dpByLevel = config('tbr-config.dp_by_level');

        $eventNameSlug = Str::slug($event->name ?? 'evento');
        $tempDir = storage_path("app/public/tmp/{$eventNameSlug}");

        if (File::exists($tempDir)) File::deleteDirectory($tempDir);
        File::makeDirectory($tempDir, 0755, true);

        $teamsCategory = [];
        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $teams = $event->teams->filter(fn($t) => $t->category_slug === $catSlug)
                ->sortByDesc(fn($t) => (float) $t->total_score)->values();
            $teamsCategory[$catSlug] = $teams;
        }

        foreach ($event->teams as $team) {
            $teamId = $team->id ?? null;
            if (!$teamId) continue;

            $categorySlug = $team->category_slug;
            $category = collect($categories)->firstWhere('slug', $categorySlug);
            if (!$category) continue;

            $categoryLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalitiesAll = $modalitiesByLevel[$modalitieLevel] ?? [];
            $modalities = collect($modalitiesAll)->filter(fn($m) => in_array($m['slug'], $selectedModalities))->values()->toArray();
            $questionsConfigLevel = $questionsByLevel[$modalitieLevel] ?? [];
            $dpConfigLevel = $dpByLevel[$category['dp']] ?? [];

            $dpMissions = $event->ranking_config['dp_missions']
                ?? DpMission::where('year', $event->date?->year ?? date('Y'))
                    ->where('dp_level', $category['dp'])
                    ->orderBy('sort_order')
                    ->pluck('mission_title')
                    ->toArray()
                ?: null;

            $position = collect($teamsCategory[$categorySlug])->search(fn($t) => $t->id === $teamId) + 1;

            $pdfConfig = $event->ranking_config['pdf'] ?? [];
            $showPosition = filter_var(request()->query('show_position', $pdfConfig['show_position'] ?? true), FILTER_VALIDATE_BOOLEAN);
            $showTotal = filter_var(request()->query('show_total', $pdfConfig['show_total'] ?? true), FILTER_VALIDATE_BOOLEAN);
            $showModalities = filter_var(request()->query('show_modalities', $pdfConfig['show_modalities'] ?? true), FILTER_VALIDATE_BOOLEAN);

            $html = <<<HTML
            <!DOCTYPE html>
            <html lang="pt-BR">
            <head>
            <meta charset="UTF-8">
            <style>
            @page { size: A4 portrait; margin: 0; }
            html, body {
                margin: 0;
                padding: 0;
                height: 297mm;
                width: 100%;
                font-family: Arial, sans-serif;
                color: rgba(0, 0, 0, 1);
            }
            .page {
                width: 100%;
                height: 100%;
                page-break-after: always;
                background-color: rgba(255, 255, 255, 1);
                box-sizing: border-box;
                color: rgba(0, 0, 0, 1);
            }
            .content-center {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100%;
                text-align: center;
            }
            .content-normal {
                width: 100%;
                text-align: center;
            }
            h1.title-event { font-size: 26pt; margin-bottom: 10pt; }
            h2.team-name { font-size: 22pt; color: #00AAFF; margin-bottom: 6pt; }
            p.position, p.total, p.category {
                font-size: 14pt;
                font-weight: bold;
                color: #FFCC00;
                margin: 0;
            }
            h2.modality-title {
                font-size: 18pt;
                padding: 10px;
                text-align: center;
                color: rgba(0, 0, 0, 1);
            }
            .tables-wrapper {
                display: flex;
                justify-content: space-between;
                width: 100%;
                position: relative;
            }
            .table-container_direita{
                width: 60%;
            }
            .table-container_esquerda {
                width: 30%;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10pt;
                color: rgba(0, 0, 0, 1);
            }
            .bloco_filho_esquerda {
                position: absolute;
                left: 10px;
                top: 0px;
            }
            .bloco_filho_direita {
                position: absolute;
                right: 10px;
                top: 0px;
            }
            .bloco_filho_direita table{
                margin-bottom: 10px;
            }
            th, td {
                border: 1px solid rgba(0, 0, 0, 1);
                padding: 4px;
                text-align: center;
            }
            th {
                background-color: rgba(255, 255, 255, 0.1);
            }
            td.desc {
                text-align: left;
            }
            .mission-header {
                background-color: rgba(0, 170, 255, 0.1);
                font-weight: bold;
                text-align: left;
                padding-left: 5px;
            }
            .comment {
                font-size: 7pt;
                font-style: italic;
                color: rgba(0, 0, 0, 1);
            }
            </style>
            </head>
            <body>
            HTML;

            $html .= '<div class="page">';
            $html .= '<div class="content-center">';
            $html .= '<h1 class="title-event">' . htmlspecialchars($event->name) . '</h1>';
            $html .= '<h2 class="team-name">' . htmlspecialchars($team->name) . '</h2>';
            $html .= '<p class="category">Categoria: ' . htmlspecialchars($categoryLabel) . '</p>';
            if ($showPosition && $position) {
                $html .= '<p class="position">Posição Geral: ' . $position . '</p>';
            }
            if ($showTotal) {
                $html .= '<p class="total">Nota Total: ' . round($team->total_score) . '</p>';
            }
            $html .= '</div></div>';

            if ($showModalities) {
                foreach ($modalities as $mod) {
                    $html .= $this->renderModalityHtml($mod, $team, $questionsConfigLevel, $dpConfigLevel, $teamsCategory[$categorySlug], $dpMissions);
                }
            }

            $html .= '</body></html>';

            $dompdf = new Dompdf(['enable_remote' => true]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $categoryDir = "{$tempDir}/{$categorySlug}";
            if (!File::exists($categoryDir)) {
                File::makeDirectory($categoryDir, 0755, true);
            }

            $fileName = Str::slug($team->name, '_') . '.pdf';
            file_put_contents("{$categoryDir}/{$fileName}", $dompdf->output());
        }

        $zipPath = storage_path("app/public/tmp/{$eventNameSlug}.zip");
        if (File::exists($zipPath)) File::delete($zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($tempDir),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }

        File::deleteDirectory($tempDir);
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
