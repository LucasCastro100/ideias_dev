<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\Attributes\Url;
use Laravel\Jetstream\InteractsWithBanner;
use App\Helpers\ScoreHelper;
use App\Models\Category;
use App\Models\Event;
use App\Models\Team;
use App\Models\TeamModalityScore;

class TbrEditScores extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Editar Notas';
    public ?string $eventId = null;
    public $event = null;

    #[Url(as: 'equipe', history: true)]
    public ?string $selectedTeamId = null;
    public $selectedTeam = null;
    public $editScores = [];
    public $editComment = '';
    public $editTotalScore = 0;

    public function mount(?string $event_id = null): void
    {
        $this->eventId = $event_id;
        $this->event = Event::with('teams.scores')->find($event_id);
        if ($this->event) {
            $this->title = 'Editar Notas - ' . $this->event->name;
        }
        if ($this->selectedTeamId) {
            $this->selectTeam($this->selectedTeamId);
        }
    }

    public function selectTeam(string $teamId): void
    {
        $this->selectedTeamId = $teamId;
        $this->selectedTeam = Team::with('scores')->find($teamId);
        $this->loadScoresForEdit();
    }

    public function loadScoresForEdit(): void
    {
        $this->editScores = [];
        $this->editComment = '';
        $this->editTotalScore = 0;

        if (!$this->selectedTeam) return;

        $this->editTotalScore = (float) $this->selectedTeam->total_score;

        foreach ($this->selectedTeam->scores as $score) {
            $modSlug = $score->modality_slug;
            $round = $score->round ?? 'default';

            $this->editScores[$modSlug][$round] = [
                'id' => $score->id,
                'scores' => $score->scores ?? [],
                'total' => (float) $score->total,
                'comment' => $score->comment ?? '',
            ];

            if ($modSlug === 'dp' && $round === 'default') {
                $this->editScores[$modSlug][$round]['round'] = 'r1';
            }
        }
    }

    private function recalculateModalityTotals(): void
    {
        $categories = Category::orderBy('sort_order')->get();
        $modalitiesByLevel = config('tbr-config.modalities_by_level') ?? [];
        $questionsByLevel = config('tbr-config.questions_by_level') ?? [];

        $teamCategory = $categories->firstWhere('slug', $this->selectedTeam?->category_slug);
        $modalitieLevel = $teamCategory?->modality_level ?? 'basic';
        $questionLevel = $teamCategory?->question_level ?? 'basic';

        foreach ($this->editScores as $modSlug => $rounds) {
            foreach ($rounds as $round => $data) {
                $flatScores = $data['scores'] ?? [];

                if ($modSlug === 'dp') {
                    $this->editScores[$modSlug][$round]['total'] = ScoreHelper::calculateDpTotal($flatScores);
                } else {
                    $totalQuestions = 0;
                    foreach ($questionsByLevel[$questionLevel] ?? [] as $qConf) {
                        if (($qConf['modality'] ?? '') === $modSlug) {
                            foreach ($qConf['assessment'] ?? [] as $block) {
                                $totalQuestions += count($block['description'] ?? []);
                            }
                        }
                    }
                    $scorePerQuestion = $totalQuestions > 0 ? 500 / ($totalQuestions * 9) : 0;
                    $this->editScores[$modSlug][$round]['total'] = ScoreHelper::calculateModalityTotal($flatScores, $questionLevel, $scorePerQuestion);
                }
            }
        }
    }

    public function saveScores(): void
    {
        $this->authorize('edit');

        if (!$this->selectedTeam) return;

        $this->recalculateModalityTotals();

        $nonDpTotal = 0;
        $bestDp = 0;

        foreach ($this->editScores as $modSlug => $rounds) {
            foreach ($rounds as $round => $data) {
                $record = TeamModalityScore::find($data['id']);
                if ($record) {
                    $record->update([
                        'scores' => $data['scores'],
                        'total' => $data['total'],
                        'comment' => $data['comment'] ?? '',
                    ]);
                }

                if ($modSlug === 'dp') {
                    $bestDp = max($bestDp, (float) $data['total']);
                } else {
                    $nonDpTotal += (float) $data['total'];
                }
            }
        }

        $this->selectedTeam->update([
            'total_score' => ScoreHelper::calculateTotalScore($nonDpTotal, $bestDp),
        ]);

        $this->editTotalScore = (float) $this->selectedTeam->fresh()->total_score;

        $this->banner('Notas atualizadas com sucesso!');
    }

    public function resetRoundScores(string $modSlug, string $round): void
    {
        $this->authorize('edit');

        if (!$this->selectedTeam || !isset($this->editScores[$modSlug][$round])) return;

        $this->editScores[$modSlug][$round]['scores'] = [];
        $this->editScores[$modSlug][$round]['total'] = 0;

        $record = TeamModalityScore::find($this->editScores[$modSlug][$round]['id']);
        if ($record) {
            $record->update([
                'scores' => [],
                'total' => 0,
            ]);
        }

        $nonDpTotal = 0;
        $bestDp = 0;
        foreach ($this->editScores as $ms => $rounds) {
            foreach ($rounds as $r => $data) {
                if ($ms === 'dp') {
                    $bestDp = max($bestDp, (float) $data['total']);
                } else {
                    $nonDpTotal += (float) $data['total'];
                }
            }
        }
        $this->selectedTeam->update([
            'total_score' => ScoreHelper::calculateTotalScore($nonDpTotal, $bestDp),
        ]);

        $this->editTotalScore = (float) $this->selectedTeam->fresh()->total_score;

        $this->banner('Notas do round ' . strtoupper($round) . ' foram resetadas!');
    }

    public function backToTeamList(): void
    {
        $this->selectedTeamId = null;
        $this->selectedTeam = null;
        $this->editScores = [];
    }

    public function render()
    {
        $categories = Category::orderBy('sort_order')->get();
        $questionsByLevel = config('tbr-config.questions_by_level') ?? [];
        $modalitiesByLevel = config('tbr-config.modalities_by_level') ?? [];

        $teamCategory = $this->selectedTeam
            ? $categories->firstWhere('slug', $this->selectedTeam->category_slug)
            : null;
        $modalitieLevel = $teamCategory?->modality_level ?? 'basic';
        $questionLevel = $teamCategory?->question_level ?? 'basic';
        $modalitiesConfig = $modalitiesByLevel[$modalitieLevel] ?? [];

        $questionsConfig = [];
        foreach ($questionsByLevel[$questionLevel] ?? [] as $qConf) {
            $questionsConfig[$qConf['modality']] = $qConf['assessment'] ?? [];
        }

        if ($this->eventId) {
            $teams = Team::where('event_id', $this->eventId)
                ->with('scores')
                ->orderBy('name')
                ->get();
        } else {
            $teams = collect();
        }

        return view('livewire.page.tbr.edit-scores', [
            'teams' => $teams,
            'event' => $this->event,
            'categories' => $categories,
            'modalitiesConfig' => $modalitiesConfig,
            'questionsConfig' => $questionsConfig,
            'modalitieLevel' => $modalitieLevel,
            'questionLevel' => $questionLevel,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => $this->title,
        ]);
    }
}
