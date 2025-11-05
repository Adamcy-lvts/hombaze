<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<div>
    <div class="flex items-center mb-4 sm:mb-6">
        <div class="p-2 sm:p-3 rounded-lg sm:rounded-xl bg-gradient-to-br from-red-600 to-red-700 shadow-lg mr-3">
            <x-heroicon-o-trash class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
        </div>
        <h2 class="text-lg sm:text-xl font-semibold text-gray-900">Delete Account</h2>
    </div>

    <div class="p-4 bg-red-50 rounded-lg border border-red-200 mb-4">
        <div class="flex items-start">
            <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-600 mt-0.5 mr-3 flex-shrink-0" />
            <div>
                <p class="text-sm font-medium text-red-900">Permanent Action</p>
                <p class="text-sm text-red-700 mt-1">
                    Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
                </p>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="button"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
            Delete Account
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6">
            <div class="flex items-center mb-4">
                <div class="p-2 rounded-lg bg-red-100 mr-3">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600" />
                </div>
                <h2 class="text-lg font-semibold text-gray-900">
                    Are you sure you want to delete your account?
                </h2>
            </div>

            <p class="text-sm text-gray-600 mb-6">
                Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.
            </p>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" wire:model="password" placeholder="Enter your password"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium transition-colors"
                        x-on:click="$dispatch('close')">
                    Cancel
                </button>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </x-modal>
</div>
