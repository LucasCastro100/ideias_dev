<?php

namespace App\Livewire\Page;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminUsers extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected const ONLINE_THRESHOLD_SECONDS = 300;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $onlineSince = now()->subSeconds(self::ONLINE_THRESHOLD_SECONDS)->timestamp;

        $sessionsPerUser = DB::table('sessions')
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('MAX(last_activity) as last_activity'))
            ->groupBy('user_id');

        $query = User::query()
            ->with('role')
            ->leftJoinSub($sessionsPerUser, 'user_sessions', 'user_sessions.user_id', '=', 'users.id')
            ->select('users.*', 'user_sessions.last_activity')
            ->selectRaw('CASE WHEN user_sessions.last_activity >= ? THEN 1 ELSE 0 END as is_online', [$onlineSince]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('users.name', 'like', "%{$this->search}%")
                    ->orWhere('users.email', 'like', "%{$this->search}%");
            });
        }

        $users = $query->orderByDesc('is_online')->orderBy('users.name')->paginate($this->perPage);

        $onlineCount = DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', $onlineSince)
            ->distinct('user_id')
            ->count('user_id');

        $users->getCollection()->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->label ?? '-',
                'online' => (bool) $user->is_online,
                'last_seen' => $user->last_activity ? \Carbon\Carbon::createFromTimestamp($user->last_activity)->diffForHumans() : null,
            ];
        });

        return view('livewire.page.admin.users', [
            'users' => $users,
            'onlineCount' => $onlineCount,
        ])->layout('layouts.app');
    }
}
