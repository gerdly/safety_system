<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    
    // Properties for custom delete confirmation modal
    public $confirmingDeletion = false;
    public $userToDeleteId = null;
    
    public $userId;
    public $name;
    public $email;
    public $password;
    public $role = 'user';

    public function mount()
    {
        abort_if(auth()->user()->role !== 'superadmin', 403, 'Unauthorized Action.');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->isOpen = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'role' => 'required|in:user,superadmin',
        ];

        if (! $this->userId) {
            $rules['password'] = 'required|min:8';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(['id' => $this->userId], $data);

        session()->flash('message', $this->userId ? 'User successfully updated.' : 'User successfully created.');
        $this->isOpen = false;
        $this->resetInputFields();
    }

    /**
     * Trigger the custom confirmation modal instead of browser alert.
     */
    public function confirmDeletion($id)
    {
        $this->userToDeleteId = $id;
        $this->confirmingDeletion = true;
    }

    /**
     * Execute the deletion after confirmation.
     */
    public function delete()
    {
        if ($this->userToDeleteId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own active session account.');
            $this->confirmingDeletion = false;
            return;
        }

        User::findOrFail($this->userToDeleteId)->delete();
        
        session()->flash('message', 'User successfully deleted.');
        
        // Reset deletion states
        $this->confirmingDeletion = false;
        $this->userToDeleteId = null;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'user';
        $this->resetValidation();
    }

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.user-management', [
            'users' => $users
        ]);
    }
}