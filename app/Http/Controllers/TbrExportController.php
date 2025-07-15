<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Shape\Drawing\File as DrawingFile;
use Illuminate\Support\Facades\Log;

use Dompdf\Dompdf;
use Illuminate\Support\Str;

class TbrExportController extends Controller
{
    // Função para logar o uso de memória
    // Função para logar o uso de memória e pico de memória
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


    private function createBackground($slide, $imagePath, $width, $height)
    {
        $bg = new DrawingFile();
        $bg->setName('Background')
            ->setDescription('Imagem de fundo')
            ->setPath($imagePath)
            ->setWidth($width)
            ->setHeight($height)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $slide->addShape($bg);
    }

    private function addAwardSlides($ppt, $slideWidth, $slideHeight, $imagePath, string $title, int $position, ?string $teamName = null)
    {
        // Primeiro slide (sem nome)
        $slide1 = $ppt->createSlide();
        $this->createBackground($slide1, $imagePath, $slideWidth, $slideHeight);
        $text1 = $slide1->createRichTextShape()
            ->setWidth($slideWidth)
            ->setHeight($slideHeight)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $text1->getFill()->setFillType(Fill::FILL_NONE);
        $text1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $text1->createTextRun($title)->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
        $text1->createBreak();
        $text1->createTextRun("{$position}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));
        gc_collect_cycles();

        // Segundo slide (com nome, se houver)
        $slide2 = $ppt->createSlide();
        $this->createBackground($slide2, $imagePath, $slideWidth, $slideHeight);
        $text2 = $slide2->createRichTextShape()
            ->setWidth($slideWidth)
            ->setHeight($slideHeight)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $text2->getFill()->setFillType(Fill::FILL_NONE);
        $text2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $text2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $text2->createTextRun($title)->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
        $text2->createBreak();
        $text2->createTextRun("{$position}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));

        if ($teamName) {
            $text2->createBreak();
            $text2->createTextRun($teamName)->getFont()->setBold(true)->setSize(52)->setColor(new Color('FF3399FF'));
        }

        gc_collect_cycles();
    }

    public function pptx($eventId)
    {
        ini_set('memory_limit', '1024M'); // 1GB
        set_time_limit(900);

        $this->logMemoryUsage('Início do método');

        $json = Storage::disk('public')->get('tbr/json/data.json');
        $this->logMemoryUsage('Após carregar JSON');

        $events = json_decode($json, true);
        $event = collect($events)->firstWhere('id', $eventId);
        $this->logMemoryUsage('Após decodificar JSON e pegar evento');

        if (!$event) {
            return abort(404, 'Evento não encontrado');
        }

        $categories = config('tbr-config.categories');
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $rankingConfig = $event['ranking_config'] ?? [];

        $topPositions = (int)($rankingConfig['top_positions'] ?? 0);
        $generalTopPositions = (int)($rankingConfig['general_top_positions'] ?? 3);

        $ppt = new PhpPresentation();
        $ppt->removeSlideByIndex(0);

        $slideWidth = 960;
        $slideHeight = 720;

        // Slide inicial com nome do evento
        $slideTitle = $ppt->createSlide();
        $shape = $slideTitle->createRichTextShape()
            ->setWidth($slideWidth)
            ->setHeight($slideHeight)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shape->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $shape->createTextRun($event['nome'] ?? 'Evento')
            ->getFont()->setBold(true)->setSize(72)->setColor(new Color('FF000000'));

        // Loop de categorias
        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $catLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];

            $teamsCategory = collect($event['equipes'] ?? [])->filter(fn($t) => ($t['category'] ?? '') === $catSlug)->values();
            if ($teamsCategory->isEmpty()) continue;

            $this->logMemoryUsage("Após filtrar equipes da categoria $catSlug");

            // Categoria baby - apenas nota geral
            if ($catSlug === 'baby' && $generalTopPositions > 0) {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
                $teamsTopTotal = $teamsSortedTotal->take($generalTopPositions)->reverse()->values();

                foreach ($teamsTopTotal as $pos => $team) {
                    $posNumber = $generalTopPositions - $pos;

                    // Slide 1
                    $slide1 = $ppt->createSlide();
                    $shape1 = $slide1->createRichTextShape()
                        ->setWidth($slideWidth)
                        ->setHeight($slideHeight)
                        ->setOffsetX(0)
                        ->setOffsetY(0);
                    $shape1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $shape1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $shape1->createTextRun("{$catLabel} - Nota Geral")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                    $shape1->createBreak();
                    $shape1->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));

                    // Slide 2
                    $slide2 = $ppt->createSlide();
                    $shape2 = $slide2->createRichTextShape()
                        ->setWidth($slideWidth)
                        ->setHeight($slideHeight)
                        ->setOffsetX(0)
                        ->setOffsetY(0);
                    $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $shape2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $shape2->createTextRun("{$catLabel} - Nota Geral")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                    $shape2->createBreak();
                    $shape2->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));
                    $shape2->createBreak();
                    $shape2->createTextRun($team['name'])->getFont()->setBold(true)->setSize(52)->setColor(new Color('FF3399FF'));
                }

                continue;
            }

            // Por modalidade
            if ($topPositions > 0) {
                foreach ($modalities as $mod) {
                    $modSlug = $mod['slug'];
                    $modLabel = $mod['label'];
                    $teamsSorted = $teamsCategory->sortByDesc(fn($t) => $t['modalities'][$modSlug]['total'] ?? 0)->values();
                    if ($teamsSorted->isEmpty()) continue;

                    $teamsTop = $teamsSorted->take($topPositions)->reverse()->values();

                    foreach ($teamsTop as $pos => $team) {
                        $posNumber = $topPositions - $pos;

                        // Slide 1
                        $slide1 = $ppt->createSlide();
                        $shape1 = $slide1->createRichTextShape()
                            ->setWidth($slideWidth)
                            ->setHeight($slideHeight)
                            ->setOffsetX(0)
                            ->setOffsetY(0);
                        $shape1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $shape1->createTextRun("{$catLabel} - {$modLabel}")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                        $shape1->createBreak();
                        $shape1->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));

                        // Slide 2
                        $slide2 = $ppt->createSlide();
                        $shape2 = $slide2->createRichTextShape()
                            ->setWidth($slideWidth)
                            ->setHeight($slideHeight)
                            ->setOffsetX(0)
                            ->setOffsetY(0);
                        $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $shape2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        $shape2->createTextRun("{$catLabel} - {$modLabel}")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                        $shape2->createBreak();
                        $shape2->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));
                        $shape2->createBreak();
                        $shape2->createTextRun($team['name'])->getFont()->setBold(true)->setSize(52)->setColor(new Color('FF3399FF'));
                    }
                }
            }

            // Nota geral (se não for baby)
            if ($generalTopPositions > 0 && $catSlug !== 'baby') {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
                $teamsTopTotal = $teamsSortedTotal->take($generalTopPositions)->reverse()->values();

                foreach ($teamsTopTotal as $pos => $team) {
                    $posNumber = $generalTopPositions - $pos;

                    // Slide 1
                    $slide1 = $ppt->createSlide();
                    $shape1 = $slide1->createRichTextShape()
                        ->setWidth($slideWidth)
                        ->setHeight($slideHeight)
                        ->setOffsetX(0)
                        ->setOffsetY(0);
                    $shape1->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $shape1->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $shape1->createTextRun("{$catLabel} - Nota Geral")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                    $shape1->createBreak();
                    $shape1->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));

                    // Slide 2
                    $slide2 = $ppt->createSlide();
                    $shape2 = $slide2->createRichTextShape()
                        ->setWidth($slideWidth)
                        ->setHeight($slideHeight)
                        ->setOffsetX(0)
                        ->setOffsetY(0);
                    $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $shape2->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $shape2->createTextRun("{$catLabel} - Nota Geral")->getFont()->setBold(true)->setSize(48)->setColor(new Color('FF000000'));
                    $shape2->createBreak();
                    $shape2->createTextRun("{$posNumber}º Lugar")->getFont()->setBold(true)->setSize(56)->setColor(new Color('FFFFCC00'));
                    $shape2->createBreak();
                    $shape2->createTextRun($team['name'])->getFont()->setBold(true)->setSize(52)->setColor(new Color('FF3399FF'));
                }
            }
        }

        // Slide final
        $slideThankYou = $ppt->createSlide();
        $shapeThankYou = $slideThankYou->createRichTextShape()
            ->setWidth($slideWidth)
            ->setHeight($slideHeight)
            ->setOffsetX(0)
            ->setOffsetY(0);
        $shapeThankYou->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shapeThankYou->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $shapeThankYou->createTextRun("Muito obrigado pela participação!")
            ->getFont()->setBold(true)->setSize(60)->setColor(new Color('FF000000'));

        $this->logMemoryUsage('Após criar todos os slides');

        // Nome do arquivo
        $eventName = $event['nome'] ?? 'evento';
        $eventDateRaw = $event['data'] ?? null;
        $eventDate = 'sem_data';

        if ($eventDateRaw) {
            try {
                $dateObj = new \DateTime($eventDateRaw);
                $eventDate = $dateObj->format('Y_m_d');
            } catch (\Exception $e) {
                // ignora
            }
        }

        $safeEventName = iconv('UTF-8', 'ASCII//TRANSLIT', $eventName);
        $safeEventName = preg_replace('/[^A-Za-z0-9]+/', '_', $safeEventName);
        $safeEventName = strtolower(trim($safeEventName, '_'));

        $fileName = "{$safeEventName}_{$eventDate}.pptx";
        $tempFile = storage_path('app/public/' . $fileName);

        $this->logMemoryUsage('Antes de salvar o arquivo PPTX');
        IOFactory::createWriter($ppt, 'PowerPoint2007')->save($tempFile);
        $this->logMemoryUsage('Após salvar o arquivo PPTX');

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function pdf($eventId)
    {
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $this->logMemoryUsage('Início do método PDF');

        $json = Storage::disk('public')->get('tbr/json/data.json');
        $events = json_decode($json, true);
        $event = collect($events)->firstWhere('id', $eventId);

        if (!$event) {
            return abort(404, 'Evento não encontrado');
        }

        $categories = config('tbr-config.categories');
        $modalitiesByLevel = config('tbr-config.modalities_by_level');
        $rankingConfig = $event['ranking_config'] ?? [];

        $topPositions = (int)($rankingConfig['top_positions'] ?? 0);
        $generalTopPositions = (int)($rankingConfig['general_top_positions'] ?? 3);

        $backgroundPath = storage_path('app/public/tbr/image/bg_pptx.jpg');
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
        $html .= '<h1 class="title-event">' . htmlspecialchars($event['nome'] ?? 'Evento') . '</h1>';
        $html .= '</div></div>';

        foreach ($categories as $category) {
            $catSlug = $category['slug'];
            $catLabel = $category['label'];
            $modalitieLevel = $category['modalitie'];
            $modalities = $modalitiesByLevel[$modalitieLevel] ?? [];

            $teamsCategory = collect($event['equipes'] ?? [])
                ->filter(fn($t) => ($t['category'] ?? '') === $catSlug)
                ->values();

            if ($teamsCategory->isEmpty()) continue;

            // Baby com Nota Geral
            if ($catSlug === 'baby' && $generalTopPositions > 0) {
                $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
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
                    $html .= "<h2 class='team-name'>" . htmlspecialchars($team['name']) . "</h2>";
                    $html .= '</div></div>';
                }
                continue;
            }

            if ($topPositions > 0) {
                foreach ($modalities as $mod) {
                    $modSlug = $mod['slug'];
                    $modLabel = $mod['label'];

                    $teamsSorted = $teamsCategory->sortByDesc(fn($t) => $t['modalities'][$modSlug]['total'] ?? 0)->values();
                    if ($teamsSorted->isEmpty()) continue;

                    $teamsTop = $teamsSorted->take($topPositions)->reverse()->values();

                    foreach ($teamsTop as $pos => $team) {
                        $posNumber = $topPositions - $pos;

                        $html .= '<div class="page"><div class="content-center">';
                        $html .= "<h2 class='title-category'>{$catLabel}</h2>";  // Categoria em linha separada
                        $html .= "<h2 class='title-category'>{$modLabel}</h2>"; // Modalidade abaixo, separado
                        $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                        $html .= '</div></div>';

                        $html .= '<div class="page"><div class="content-center">';
                        $html .= "<h2 class='title-category'>{$catLabel}</h2>";
                        $html .= "<h2 class='title-category'>{$modLabel}</h2>";
                        $html .= "<h3 class='position'>{$posNumber}º Lugar</h3>";
                        $html .= "<h2 class='team-name'>" . htmlspecialchars($team['name']) . "</h2>";
                        $html .= '</div></div>';
                    }
                }
            }

            if ($generalTopPositions > 0) { // Nota geral para todas as categorias, inclusive baby já tratado acima, mas aqui não entra baby pois já continuou.
                if ($catSlug !== 'baby') {
                    $teamsSortedTotal = $teamsCategory->sortByDesc(fn($t) => floatval($t['nota_total'] ?? 0))->values();
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
                        $html .= "<h2 class='team-name'>" . htmlspecialchars($team['name']) . "</h2>";
                        $html .= '</div></div>';
                    }
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

        $eventName = Str::slug($event['nome'] ?? 'evento', '_');
        $eventDate = $event['data'] ?? now()->format('Y-m-d');
        $eventDateSlug = Str::slug($eventDate, '_');

        $fileName = strtolower("{$eventName}_{$eventDateSlug}.pdf");

        return response($dompdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
