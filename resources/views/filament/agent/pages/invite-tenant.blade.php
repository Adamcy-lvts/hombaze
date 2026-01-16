<x-filament-panels::page>
    <div class="max-w-md mx-auto pb-20">
        
        @if(!$generatedLink)
            {{-- Form State --}}
            <div class="space-y-6">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-900/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-primary-700/50">
                        <x-heroicon-o-paper-airplane class="w-8 h-8 text-primary-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-white">Invite a Tenant</h2>
                    <p class="text-gray-400 mt-2 text-sm">Send an invitation link to onboard a new tenant to your property.</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Tenant Phone Number</label>
                        <input type="tel" 
                               wire:model="phone"
                               placeholder="+234 801 234 5678"
                               class="block w-full rounded-xl border-gray-700 bg-gray-800 text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm p-3">
                        @error('phone') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Property (Optional)</label>
                        <select wire:model="property_id"
                                class="block w-full rounded-xl border-gray-700 bg-gray-800 text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm p-3">
                            <option value="">Select a property...</option>
                            @foreach($this->properties as $id => $title)
                                <option value="{{ $id }}">{{ $title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button wire:click="generateLink" 
                        wire:loading.attr="disabled"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 transition-all">
                    <span wire:loading.remove>Generate Invitation Link</span>
                    <span wire:loading>
                        <x-filament::loading-indicator class="h-5 w-5" />
                    </span>
                </button>
            </div>
        @else
            {{-- Success/Link State --}}
            <div class="text-center space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500 pt-8">
                <div class="w-24 h-24 bg-green-500/10 rounded-full flex items-center justify-center mx-auto mb-6 ring-1 ring-green-500/50 shadow-[0_0_20px_rgba(34,197,94,0.2)]">
                    <x-heroicon-o-check class="w-12 h-12 text-green-400" />
                </div>
                
                <div>
                    <h2 class="text-3xl font-bold text-white tracking-tight">Access Granted!</h2>
                    <p class="text-gray-400 mt-3 text-base leading-relaxed max-w-xs mx-auto">Your invitation link is ready. Share it with the tenant to get them started.</p>
                </div>

                <div class="bg-gray-900 rounded-2xl p-2 border border-gray-800 shadow-inner flex items-center gap-2">
                    <input type="text" 
                           value="{{ $generatedLink }}" 
                           readonly 
                           class="flex-1 bg-transparent border-none text-gray-300 text-sm focus:ring-0 px-3 py-2 w-full truncate font-mono"
                           onclick="this.select()">
                    
                    <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ $generatedLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="p-2 bg-gray-800 hover:bg-gray-700 rounded-xl text-gray-400 hover:text-white transition-colors">
                        <x-heroicon-o-clipboard-document class="w-5 h-5" x-show="!copied" />
                        <x-heroicon-o-check class="w-5 h-5 text-green-400" x-show="copied" style="display: none;" />
                    </button>
                </div>

                <div class="space-y-3">
                    @php
                        $agentName = auth()->user()->name;
                        
                        if ($invitation->property) {
                            $propertyDetails = "*{$invitation->property->title} at {$invitation->property->address}*";
                        } else {
                            $propertyDetails = "a property";
                        }

                        $whatsappMessage = "Hi! $agentName has invited you register as a tenant for $propertyDetails on hombaze platform, the best place to buy and sell or rent your property house, land etc.. Click here: $generatedLink";
                    @endphp

                    <a href="https://wa.me/?text={{ urlencode($whatsappMessage) }}" 
                       target="_blank"
                       class="w-full flex items-center justify-center py-4 px-6 rounded-xl shadow-lg shadow-green-900/20 text-base font-bold text-white bg-[#25D366] hover:bg-[#128C7E] active:scale-[0.98] transition-all">
                        <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 mr-3" />
                        Share via WhatsApp
                    </a>

                    <button x-data="{ copied: false }"
                            @click="navigator.clipboard.writeText('{{ $generatedLink }}'); copied = true; setTimeout(() => copied = false, 2000)"
                            class="w-full flex items-center justify-center py-4 px-6 border border-gray-700 rounded-xl shadow-sm text-base font-medium text-white bg-gray-800 hover:bg-gray-700 active:bg-gray-700 active:scale-[0.98] transition-all">
                        <span x-text="copied ? 'Link Copied!' : 'Copy Link'"></span>
                    </button>
                </div>

                <button wire:click="resetForm" class="text-gray-500 text-sm hover:text-white transition-colors py-2">
                    Send another invitation
                </button>
            </div>
        @endif

        {{-- Recent Invitations List --}}
        @if(!$generatedLink && $this->recentInvitations->isNotEmpty())
            <div class="mt-12">
                <h3 class="text-lg font-semibold text-white mb-4 px-1">Recent Invitations</h3>
                <div class="space-y-3">
                    @foreach($this->recentInvitations as $invite)
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    @if($invite->tenantUser)
                                        <p class="text-white font-bold text-sm">{{ $invite->tenantUser->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $invite->phone }}</p>
                                    @else
                                        <p class="text-white font-bold text-sm">{{ $invite->phone }}</p>
                                        <p class="text-xs text-gray-500">Guest Tenant</p>
                                    @endif
                                    
                                    <p class="text-xs text-primary-400 mt-1 font-medium">
                                        {{ $invite->property ? $invite->property->title : 'No property specified' }}
                                    </p>
                                </div>
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider
                                    {{ $invite->status === 'pending' ? 'bg-yellow-500/10 text-yellow-500' : '' }}
                                    {{ $invite->status === 'accepted' ? 'bg-green-500/10 text-green-500' : '' }}
                                    {{ $invite->status === 'expired' ? 'bg-red-500/10 text-red-500' : '' }}
                                ">
                                    {{ $invite->status }}
                                </span>
                            </div>

                            {{-- Actions for Pending --}}
                            @if($invite->status === 'pending')
                                <div class="flex items-center gap-3 mt-3 pt-3 border-t border-gray-800">
                                    <button x-data="{ copied: false }"
                                            @click="navigator.clipboard.writeText('{{ $invite->getInvitationUrl() }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                            class="flex-1 flex items-center justify-center text-xs font-medium py-2 rounded-lg bg-gray-800 text-gray-300 active:bg-gray-700 transition">
                                        <x-heroicon-o-link class="w-3.5 h-3.5 mr-1.5" />
                                        <span x-text="copied ? 'Copied' : 'Copy Link'"></span>
                                    </button>

                                    @php
                                        $agentName = auth()->user()->name;
                                        $propTitle = $invite->property ? "*{$invite->property->title} at {$invite->property->address}*" : "a property";
                                        $waMsg = "Hi! $agentName has invited you register as a tenant for $propTitle on hombaze platform, the best place to buy and sell or rent your property house, land etc.. Click here: " . $invite->getInvitationUrl();
                                    @endphp

                                    <a href="https://wa.me/?text={{ urlencode($waMsg) }}&phone={{ preg_replace('/[^0-9]/', '', $invite->phone) }}" 
                                       target="_blank"
                                       class="flex-1 flex items-center justify-center text-xs font-medium py-2 rounded-lg bg-[#25D366]/10 text-[#25D366] active:bg-[#25D366]/20 transition border border-[#25D366]/20">
                                        <x-heroicon-o-chat-bubble-left-right class="w-3.5 h-3.5 mr-1.5" />
                                        WhatsApp
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
