<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AccountType;
use App\Models\FinancialCategory;
use App\Models\FinancialTransaction;
use Laravel\Jetstream\InteractsWithBanner;

class FinanceiroTransactions extends Component
{
    use InteractsWithBanner, WithPagination;

    public $showModal = false;
    public $transactionId = null;
    public $account_type_id = '';
    public $category_id = '';
    public $type = 'expense';
    public $description = '';
    public $value = '';
    public $due_date = '';
    public $paid_date = '';
    public $paid = false;
    public $month = '';
    public $year = '';
    public $notes = '';
    public $perPage = 10;

    public $accountTypes;
    public $categories;
    public $filterMonth = '';
    public $filterYear = '';

    public $confirmingId = null;
    public $confirmingMessage = '';

    public function mount()
    {
        $this->accountTypes = AccountType::orderBy('name')->get();
        $this->categories = FinancialCategory::orderBy('name')->get();
        $this->month = now()->month;
        $this->year = now()->year;
        $this->filterMonth = now()->month;
        $this->filterYear = now()->year;
    }

    public function updatedFilterMonth()
    {
        $this->resetPage();
    }

    public function updatedFilterYear()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $transaction = FinancialTransaction::where('user_id', auth()->id())->findOrFail($id);
        $this->transactionId = $transaction->id;
        $this->account_type_id = $transaction->account_type_id;
        $this->category_id = $transaction->category_id;
        $this->type = $transaction->value >= 0 ? 'income' : 'expense';
        $this->description = $transaction->description;
        $this->value = abs($transaction->value);
        $this->due_date = $transaction->due_date?->format('Y-m-d');
        $this->paid_date = $transaction->paid_date?->format('Y-m-d');
        $this->paid = $transaction->paid;
        $this->month = $transaction->month;
        $this->year = $transaction->year;
        $this->notes = $transaction->notes;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'type' => 'required|in:income,expense',
            'value' => 'required|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
            'due_date' => 'nullable|date',
            'paid_date' => 'nullable|date',
            'account_type_id' => 'nullable|exists:account_types,id',
            'category_id' => 'nullable|exists:financial_categories,id',
            'notes' => 'nullable|string',
        ]);

        $sign = $this->type === 'income' ? 1 : -1;

        $accountType = $this->account_type_id ? AccountType::find($this->account_type_id) : null;
        $autoDescription = $accountType ? $accountType->name : ($this->type === 'income' ? 'Receita' : 'Despesa');
        $autoDescription .= ' - ' . str_pad($this->month, 2, '0', STR_PAD_LEFT) . '/' . $this->year;

        FinancialTransaction::updateOrCreate(
            ['id' => $this->transactionId],
            [
                'user_id' => $this->transactionId ? FinancialTransaction::find($this->transactionId)->user_id : auth()->id(),
                'account_type_id' => $this->account_type_id ?: null,
                'category_id' => $this->category_id ?: null,
                'description' => $autoDescription,
                'value' => $sign * abs((float) $this->value),
                'due_date' => $this->due_date ?: null,
                'paid_date' => $this->paid_date ?: null,
                'paid' => $this->paid,
                'month' => $this->month,
                'year' => $this->year,
                'notes' => $this->notes,
            ]
        );

        $this->banner($this->transactionId ? 'Conta atualizada com sucesso!' : 'Conta cadastrada com sucesso!');
        $this->showModal = false;
        $this->resetForm();
    }

    public function togglePaid($id)
    {
        $transaction = FinancialTransaction::where('user_id', auth()->id())->findOrFail($id);
        $transaction->update([
            'paid' => !$transaction->paid,
            'paid_date' => !$transaction->paid ? now() : null,
        ]);
    }

    public function confirmDelete($id)
    {
        $this->confirmingId = $id;
        $this->confirmingMessage = 'Excluir esta conta? Esta ação não pode ser desfeita.';
    }

    public function executeAction()
    {
        FinancialTransaction::where('user_id', auth()->id())->findOrFail($this->confirmingId)->delete();
        $this->banner('Conta excluída com sucesso!');
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function cancelConfirmation()
    {
        $this->confirmingId = null;
        $this->confirmingMessage = '';
    }

    public function resetForm()
    {
        $this->transactionId = null;
        $this->account_type_id = '';
        $this->category_id = '';
        $this->type = 'expense';
        $this->description = '';
        $this->value = '';
        $this->due_date = '';
        $this->paid_date = '';
        $this->paid = false;
        $this->month = now()->month;
        $this->year = now()->year;
        $this->notes = '';
    }

    public function render()
    {
        $query = FinancialTransaction::where('user_id', auth()->id());

        if ($this->filterMonth) {
            $query->where('month', $this->filterMonth);
        }
        if ($this->filterYear) {
            $query->where('year', $this->filterYear);
        }

        return view('livewire.page.financeiro.transactions', [
            'transactions' => $query->orderBy('due_date', 'desc')->paginate($this->perPage),
            'types' => $this->accountTypes,
            'cats' => $this->categories,
        ])->layout('layouts.app-sidebar', [
            'showSidebar' => true,
            'title' => 'Contas',
        ]);
    }
}
