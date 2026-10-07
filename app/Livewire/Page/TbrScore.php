<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Helpers\ScoreHelper;
use App\Models\Category;
use App\Models\DpMission;
use App\Models\Event;
use App\Models\Team;
use App\Models\TeamModalityScore;
use Illuminate\Support\Facades\Log;

class TbrScore extends Component
{
    use InteractsWithBanner;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public $title = 'TBR - Notas';

    public $event_id;
    public $category_id;
    public $modality_id;

    public $questionLevel;
    public $event;
    public $category;
    public $modality;
    public $modalitieSlug;

    public $question = null;
    public $hasAssessment = false;
    private $totalQuestions = 0;
    public $scorePerQuestion = 0;

    public $filteredTeams = [];
    public $selectedTeamId = null;

    public $scores = [];
    public $scoresDP = [];
    public $scoresBonus = [];

    public $comment = "";
    public $showSidebar = false;
    public $dp_pontos = 0;
    public $showSuccessModal = false;

    public function mount($event_id, $category_id, $modality_id)
    {
        $this->event_id = $event_id;
        $this->category_id = $category_id;
        $this->modality_id = $modality_id;

        $this->loadEvent();
        $this->setQuestionLevelFromCategoryId($category_id);
        $this->loadCategory();
        $this->loadModality();
        $this->filterTeams();
        $this->loadQuestion();
        $this->calculateScorePerQuestion();
    }

    private function loadEvent()
    {
        $this->event = Event::with('teams.scores')->find($this->event_id);
    }

    private function setQuestionLevelFromCategoryId($category_id)
    {
        $category = Category::find($category_id);
        $this->questionLevel = $category?->question_level ?? 'basic';
    }

    private function loadCategory()
    {
        $this->category = Category::find($this->category_id)?->toArray();
    }

    private function loadModality()
    {
        if (!$this->category) return;

        $modalitieLevel = $this->category['modality_level'] ?? 'basic';
        $modalities = config("tbr-config.modalities_by_level.$modalitieLevel") ?? [];
        $this->modality = collect($modalities)->firstWhere('id', $this->modality_id)
            ?? collect($modalities)->firstWhere('slug', $this->modality_id);
    }

    public function loadQuestion()
    {
        if (!$this->category || !$this->modality) {
            $this->question = null;
            $this->hasAssessment = false;
            return;
        }

        $this->modalitieSlug = $this->modality['slug'];

        if ($this->modalitieSlug == 'dp') {
            $questionModalitie = $this->category['dp_level'] ?? null;
            $eventYear = $this->event?->date?->year ?? date('Y');

            $missions = DpMission::where('year', $eventYear)
                ->where('dp_level', $questionModalitie)
                ->orderBy('sort_order')
                ->get()
                ->map(fn($m) => [
                    'mission' => $m->mission_title,
                    'description' => $m->description,
                    'image' => $m->image,
                    'itens' => $m->items,
                    'depends_on' => $m->depends_on,
                    'locked' => false,
                ]);

            $this->scores = [];
            $this->scoresDP = [];
            $this->scoresBonus = [];

            $this->question = $missions->toArray();
            $this->hasAssessment = $missions->isNotEmpty();

            if ($this->hasAssessment) {
                foreach ($this->question as $blockIndex => $block) {
                    foreach ($block['itens'] as $itemIndex => $item) {
                        if ($item['type'] === 'radio') {
                            $this->scores[$blockIndex][$itemIndex] = "";
                        } elseif ($item['type'] === 'number') {
                            foreach ($item['options'] as $optIndex => $option) {
                                $this->scoresDP[$blockIndex][$itemIndex][$optIndex] = 0;
                            }
                        } elseif ($item['type'] === 'bonus') {
                            $this->scoresBonus[$blockIndex][$itemIndex] = false;
                        }
                    }
                }
            }
        } else {
            $questionModalitie = $this->category['question_level'] ?? 'basic';
            $questions = config("tbr-config.questions_by_level.$questionModalitie", []);

            $this->question = collect($questions)->firstWhere('id', $this->modality_id);
            $this->hasAssessment = collect($this->question['assessment'] ?? [])
                ->filter(fn($item) => !empty($item['description']))
                ->isNotEmpty();

            $this->scores = [];

            if ($this->hasAssessment) {
                foreach ($this->question['assessment'] as $blockIndex => $block) {
                    $blockObject = $block['object'] ?? null;
                    if ($blockObject && !empty($block['description'])) {
                        foreach ($block['description'] as $index => $desc) {
                            $this->scores[$blockIndex][$index] = "5";
                        }
                    }
                }
            }
        }
    }

    private function calculateScorePerQuestion()
    {
        if ($this->modalitieSlug === 'dp') return;

        $this->totalQuestions = collect($this->question['assessment'] ?? [])
            ->sum(fn($block) => count($block['description'] ?? []));

        $this->scorePerQuestion = $this->totalQuestions > 0
            ? 500 / ($this->totalQuestions * 9)
            : 0;
    }

    public function filterTeams()
    {
        $categorySlug = Category::find($this->category_id)?->slug;

        $modalitieLevel = $this->category['modality_level'] ?? 'basic';
        $modalitiesConfig = config("tbr-config.modalities_by_level.$modalitieLevel") ?? [];

        $modality = collect($modalitiesConfig)->firstWhere('id', $this->modality_id)
            ?? collect($modalitiesConfig)->firstWhere('slug', $this->modality_id);

        $modalitySlug = $modality['slug'] ?? null;

        if (!$categorySlug || !$modalitySlug || !$this->event) {
            $this->filteredTeams = [];
            return;
        }

        $this->filteredTeams = $this->event->teams
            ->filter(function ($team) use ($categorySlug, $modalitySlug, $modalitieLevel) {
                if ($team->category_slug !== $categorySlug) return false;

                $score = $team->scores->firstWhere('modality_slug', $modalitySlug);
                if (!$score) return false;

                if ($modalitieLevel === 'basic') return true;

                if ($modalitySlug === 'dp') {
                    $filledRounds = $team->scores
                        ->where('modality_slug', 'dp')
                        ->filter(fn($s) => !empty($s->scores))
                        ->count();
                    return $filledRounds < 3;
                }

                return empty($score->scores);
            })
            ->values()
            ->map(fn($team) => ['id' => $team->id, 'name' => $team->name])
            ->all();
    }

    public function getSelectedTeamProperty()
    {
        if (!$this->selectedTeamId || !$this->event) return null;
        return $this->event->teams->firstWhere('id', $this->selectedTeamId);
    }

    public function updateScoreDPManual($value, $path)
    {
        $parts = explode('.', $path);
        if (count($parts) !== 3) return;

        [$blockIndex, $itemIndex, $optIndex] = $parts;
        $blockIndex = (int)$blockIndex;
        $itemIndex = (int)$itemIndex;
        $optIndex = (int)$optIndex;
        $value = (int)$value;

        if ($value < 0) $value = 0;

        $missionConfig = $this->question[$blockIndex] ?? null;
        if ($missionConfig) {
            $itemConfig = $missionConfig['itens'][$itemIndex] ?? null;

            if ($itemConfig && ($itemConfig['has_max'] ?? false)) {
                $maxAllowed = $itemConfig['max_value'];

                $currentTotalCount = 0;
                foreach ($this->scoresDP[$blockIndex][$itemIndex] ?? [] as $oKey => $inputVal) {
                    if ($oKey != $optIndex) {
                        $currentTotalCount += (int)$inputVal;
                    }
                }

                if (($currentTotalCount + $value) > $maxAllowed) {
                    $remainingSpace = $maxAllowed - $currentTotalCount;
                    $value = $remainingSpace > 0 ? $remainingSpace : 0;

                    $this->scoresDP[$blockIndex][$itemIndex][$optIndex] = $value;
                    $this->dangerBanner("Limite atingido! O item '{$itemConfig['name']}' aceita no máximo {$maxAllowed} elementos combinados.");
                    $this->recalculateDPPoints();
                    return;
                }
            }
        }

        $this->scoresDP[$blockIndex][$itemIndex][$optIndex] = $value;
        $this->recalculateDPPoints();
    }

    private function isDependencySatisfied(int $blockIndex): bool
    {
        $block = $this->question[$blockIndex] ?? null;
        if (!$block || empty($block['depends_on'])) {
            return true;
        }

        $depIndex = null;
        foreach ($this->question as $idx => $q) {
            if (($q['mission'] ?? '') === $block['depends_on']) {
                $depIndex = $idx;
                break;
            }
        }

        if ($depIndex === null) {
            return true;
        }

        $depBlock = $this->question[$depIndex];
        foreach ($depBlock['itens'] as $itemIndex => $item) {
            if ($item['type'] === 'radio') {
                if (!empty($this->scores[$depIndex][$itemIndex]) && (int)$this->scores[$depIndex][$itemIndex] > 0) {
                    return true;
                }
            } elseif ($item['type'] === 'number') {
                foreach ($item['options'] as $optIndex => $option) {
                    $qty = (int)($this->scoresDP[$depIndex][$itemIndex][$optIndex] ?? 0);
                    if ($qty > 0) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function recalculateDPPoints()
    {
        $this->dp_pontos = 0;

        foreach ($this->question ?? [] as $blockIndex => $block) {
            if (!$this->isDependencySatisfied($blockIndex)) {
                foreach ($block['itens'] as $itemIndex => $item) {
                    if ($item['type'] === 'radio') {
                        $this->scores[$blockIndex][$itemIndex] = "";
                    } elseif ($item['type'] === 'number') {
                        foreach ($item['options'] as $optIndex => $option) {
                            $this->scoresDP[$blockIndex][$itemIndex][$optIndex] = 0;
                        }
                    } elseif ($item['type'] === 'bonus') {
                        $this->scoresBonus[$blockIndex][$itemIndex] = false;
                    }
                }
                continue;
            }

            foreach ($block['itens'] as $itemIndex => $item) {
                if ($item['type'] === 'radio') {
                    $this->dp_pontos += (int)($this->scores[$blockIndex][$itemIndex] ?? 0);
                } elseif ($item['type'] === 'number') {
                    foreach ($item['options'] as $optIndex => $option) {
                        $qty = (int)($this->scoresDP[$blockIndex][$itemIndex][$optIndex] ?? 0);
                        $this->dp_pontos += ($qty * (int)$option['value']);
                    }
                } elseif ($item['type'] === 'bonus') {
                    if (!empty($this->scoresBonus[$blockIndex][$itemIndex])) {
                        $this->dp_pontos += (int)($item['value'] ?? 0);
                    }
                }
            }
        }

        $this->dp_pontos = max(0, $this->dp_pontos);
    }

    private function buildRoundScoresArray(): array
    {
        $roundScores = [];

        foreach ($this->question as $blockIndex => $block) {
            if (!$this->isDependencySatisfied($blockIndex)) {
                $roundScores[] = 0;
                continue;
            }

            $missionSum = 0;

            foreach ($block['itens'] as $itemIndex => $item) {
                if ($item['type'] === 'radio') {
                    $missionSum += (int)($this->scores[$blockIndex][$itemIndex] ?? 0);
                } elseif ($item['type'] === 'number') {
                    foreach ($item['options'] as $optIndex => $option) {
                        $qty = (int)($this->scoresDP[$blockIndex][$itemIndex][$optIndex] ?? 0);
                        $missionSum += ($qty * (int)$option['value']);
                    }
                } elseif ($item['type'] === 'bonus') {
                    if (!empty($this->scoresBonus[$blockIndex][$itemIndex])) {
                        $missionSum += (int)$item['value'];
                    }
                }
            }

            $roundScores[] = $missionSum;
        }

        return $roundScores;
    }

    private function findNextDpRound(Team $team): ?string
    {
        $dpScores = $team->scores->where('modality_slug', 'dp');

        foreach (['r1', 'r2', 'r3'] as $round) {
            $existing = $dpScores->firstWhere('round', $round);
            if (!$existing || empty($existing->scores)) return $round;
        }

        return null;
    }

    private function validateScores()
    {
        if (!$this->selectedTeamId) {
            $this->dangerBanner('Selecione uma equipe antes de salvar.');
            return false;
        }

        if ($this->modalitieSlug === 'dp') {
            foreach ($this->question ?? [] as $blockIndex => $block) {
                if (!$this->isDependencySatisfied($blockIndex)) {
                    continue;
                }
                foreach ($block['itens'] as $itemIndex => $item) {
                    if ($item['type'] === 'radio') {
                        if (!isset($this->scores[$blockIndex][$itemIndex]) || $this->scores[$blockIndex][$itemIndex] === '') {
                            $this->dangerBanner("Falta responder o critério '{$item['name']}' na '{$block['mission']}'.");
                            return false;
                        }
                    }
                }
            }
        } else {
            if (empty($this->scores)) {
                $this->dangerBanner('Você precisa responder todas as perguntas de avaliação.');
                return false;
            }
        }

        return true;
    }

    public function saveScores()
    {
        if (!$this->validateScores()) return;

        $modalitySlug = $this->modality['slug'];

        $team = Team::with('scores')->find($this->selectedTeamId);
        if (!$team) {
            $this->dangerBanner('Equipe não encontrada.');
            return;
        }

        if ($modalitySlug === 'dp') {
            $roundScores = $this->buildRoundScoresArray();
            $roundTotal = ScoreHelper::calculateDpTotal($roundScores);

            $nextRound = $this->findNextDpRound($team);

            if (!$nextRound) {
                $this->dangerBanner('Esta equipe já possui todos os 3 rounds preenchidos para DP.');
                return;
            }

            TeamModalityScore::updateOrCreate(
                [
                    'team_id' => $team->id,
                    'modality_slug' => 'dp',
                    'round' => $nextRound,
                ],
                [
                    'scores' => $roundScores,
                    'total' => $roundTotal,
                    'comment' => $this->comment,
                ]
            );

            $nonDpTotal = ScoreHelper::floatVal($team->scores()
                ->where('modality_slug', '!=', 'dp')
                ->sum('total'));
            $bestDp = ScoreHelper::floatVal($team->scores()
                ->where('modality_slug', 'dp')
                ->max('total'));
            $team->update(['total_score' => ScoreHelper::calculateTotalScore($nonDpTotal, $bestDp)]);
        } else {
            $flatScores = [];
            foreach ($this->scores as $blockIndex => $indices) {
                foreach ($indices as $index => $value) {
                    $flatScores[] = (string) $value;
                }
            }

            $total = ScoreHelper::calculateModalityTotal($flatScores, $this->questionLevel, $this->scorePerQuestion);

            TeamModalityScore::updateOrCreate(
                [
                    'team_id' => $team->id,
                    'modality_slug' => $modalitySlug,
                    'round' => null,
                ],
                [
                    'scores' => $flatScores,
                    'total' => $total,
                    'comment' => $this->comment,
                ]
            );

            $nonDpTotal = ScoreHelper::floatVal($team->scores()
                ->where('modality_slug', '!=', 'dp')
                ->sum('total'));
            $bestDp = ScoreHelper::floatVal($team->scores()
                ->where('modality_slug', 'dp')
                ->max('total'));
            $team->update(['total_score' => ScoreHelper::calculateTotalScore($nonDpTotal, $bestDp)]);
        }

        $this->showSuccessModal = true;

        $this->dispatch('toast-message', message: 'Nota salva com sucesso!', style: 'success');
    }

    public function closeSuccessModal()
    {
        $this->showSuccessModal = false;
        $this->dispatch('reload-page');
    }

    public function render()
    {
        $lockedBlocks = [];
        if ($this->modalitieSlug === 'dp') {
            foreach ($this->question ?? [] as $blockIndex => $block) {
                $lockedBlocks[$blockIndex] = !$this->isDependencySatisfied($blockIndex);
            }
        }

        return view('livewire.page.tbr.score', [
            'event' => $this->event,
            'filteredTeams' => $this->filteredTeams,
            'category' => $this->category,
            'modality' => $this->modality,
            'question' => $this->question,
            'hasAssessment' => $this->hasAssessment,
            'modalitieSlug' => $this->modalitieSlug,
            'lockedBlocks' => $lockedBlocks,
        ])->layout('layouts.app-tbr-public', [
            'title' => $this->title
        ]);
    }
}
