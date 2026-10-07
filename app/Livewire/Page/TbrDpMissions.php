<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Laravel\Jetstream\InteractsWithBanner;
use App\Models\DpMission;

class TbrDpMissions extends Component
{
    use InteractsWithBanner;

    public $title = 'TBR - Missões DP';

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;

    public $editId;
    public $editYear;
    public $editDpLevel = 'kids1';
    public $editMissionTitle;
    public $editDescription;
    public $editImage = '';
    public $editItems = [];
    public $editDependsOn = '';

    public $deleteTarget;
    public $filterDpLevel = '';
    public $filterYear = '';

    private function sanitizeItems(array $items): array
    {
        return collect($items)->map(function ($item) {
            $isBonus = !empty($item['is_bonus']);
            $sanitized = [
                'name' => $item['name'] ?? '',
                'type' => $isBonus ? 'bonus' : 'radio',
                'has_max' => false,
                'max_value' => 0,
                'options' => [],
            ];

            if ($isBonus) {
                $sanitized['value'] = (int)($item['value'] ?? 0);
            } else {
                $hasMax = !empty($item['has_max']);
                $sanitized['has_max'] = $hasMax;
                $sanitized['max_value'] = $hasMax ? (int)($item['max_value'] ?? 0) : 0;
                $sanitized['type'] = $hasMax ? 'number' : 'radio';
                $sanitized['options'] = collect($item['options'] ?? [])->filter(fn($opt) => !empty($opt['name']) || !empty($opt['value']))->map(function ($opt) {
                    return [
                        'name' => $opt['name'] ?? '',
                        'value' => (int)($opt['value'] ?? 0),
                    ];
                })->values()->toArray();
            }

            return $sanitized;
        })->values()->toArray();
    }

    public function addItem()
    {
        $this->editItems[] = [
            'name' => '',
            'type' => 'radio',
            'has_max' => false,
            'max_value' => 0,
            'options' => [
                ['name' => '', 'value' => 0],
            ],
        ];
    }

    public function removeItem($index)
    {
        array_splice($this->editItems, $index, 1);
    }

    public function addItemOption($itemIndex)
    {
        $this->editItems[$itemIndex]['options'][] = ['name' => '', 'value' => 0];
    }

    public function removeItemOption($itemIndex, $optIndex)
    {
        array_splice($this->editItems[$itemIndex]['options'], $optIndex, 1);
    }

    public function render()
    {
        $query = DpMission::orderBy('year', 'desc')->orderBy('dp_level')->orderBy('sort_order');

        if ($this->filterYear) {
            $query->where('year', $this->filterYear);
        }

        if ($this->filterDpLevel) {
            $query->where('dp_level', $this->filterDpLevel);
        }

        $years = DpMission::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

        $availableMissions = DpMission::where('year', $this->editYear ?: date('Y'))
            ->where('dp_level', $this->editDpLevel)
            ->when($this->editId, fn($q) => $q->where('id', '!=', $this->editId))
            ->orderBy('sort_order')
            ->get();

        return view('livewire.page.tbr.dp-missions', [
            'missions' => $query->get(),
            'years' => $years,
            'availableMissions' => $availableMissions,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => $this->title,
        ]);
    }

    public function openCreateModal()
    {
        $this->authorize('create');
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->authorize('create');
        $this->validate([
            'editDpLevel' => 'required',
            'editMissionTitle' => 'required|min:2',
        ]);

        $year = $this->editYear ?: date('Y');
        $maxSort = DpMission::where('year', $year)->where('dp_level', $this->editDpLevel)->max('sort_order') ?? 0;

        $items = $this->sanitizeItems($this->editItems);

        DpMission::create([
            'year' => $year,
            'dp_level' => $this->editDpLevel,
            'mission_title' => $this->editMissionTitle,
            'description' => $this->editDescription ?? '',
            'image' => $this->editImage ?? '',
            'items' => $items,
            'depends_on' => $this->editDependsOn ?: null,
            'sort_order' => $maxSort + 1,
        ]);

        $this->banner('Missão criada com sucesso!');
        $this->closeCreateModal();
    }

    public function openEditModal($id)
    {
        $this->authorize('edit');
        $m = DpMission::findOrFail($id);
        $this->editId = $m->id;
        $this->editYear = $m->year;
        $this->editDpLevel = $m->dp_level;
        $this->editMissionTitle = $m->mission_title;
        $this->editDescription = $m->description;
        $this->editImage = $m->image ?? '';
        $this->editItems = $m->items;
        $this->editDependsOn = $m->depends_on ?? '';
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function update()
    {
        $this->authorize('edit');
        $this->validate([
            'editDpLevel' => 'required',
            'editMissionTitle' => 'required|min:2',
        ]);

        $items = $this->sanitizeItems($this->editItems);

        DpMission::findOrFail($this->editId)->update([
            'year' => $this->editYear ?: date('Y'),
            'dp_level' => $this->editDpLevel,
            'mission_title' => $this->editMissionTitle,
            'description' => $this->editDescription ?? '',
            'image' => $this->editImage ?? '',
            'items' => $items,
            'depends_on' => $this->editDependsOn ?: null,
        ]);

        $this->banner('Missão atualizada com sucesso!');
        $this->closeEditModal();
    }

    public function openDeleteModal($id)
    {
        $this->authorize('delete');
        $this->deleteTarget = DpMission::findOrFail($id);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->deleteTarget = null;
    }

    public function delete()
    {
        $this->authorize('delete');
        $this->deleteTarget->delete();
        $this->banner('Missão removida com sucesso!');
        $this->closeDeleteModal();
    }

    private function resetForm()
    {
        $this->editId = null;
        $this->editYear = date('Y');
        $this->editDpLevel = 'kids1';
        $this->editMissionTitle = '';
        $this->editDescription = '';
        $this->editImage = '';
        $this->editItems = [];
        $this->editDependsOn = '';
    }
}
