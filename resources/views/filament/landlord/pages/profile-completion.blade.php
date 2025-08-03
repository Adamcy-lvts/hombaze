<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profile Completion Progress -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Profile Completion</h3>
                    <p class="text-sm text-gray-600">Complete your profile to access all features</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-primary-600">{{ auth()->user()->profile_completion_percentage }}%</div>
                    <div class="text-sm text-gray-500">Complete</div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full transition-all duration-500 ease-out" 
                     style="width: {{ auth()->user()->profile_completion_percentage }}%"></div>
            </div>
            
            <!-- Remaining Steps -->
            @if(auth()->user()->getRemainingSteps())
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Remaining Steps:</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        @foreach(auth()->user()->getRemainingSteps() as $step => $description)
                            <div class="flex items-center text-sm text-gray-600">
                                <x-heroicon-o-clock class="w-4 h-4 mr-2 text-amber-500" />
                                {{ $description }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Profile Completion Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            {{ $this->form }}
        </div>
    </div>

    <style>
        /* Custom wizard styling */
        .fi-wizard {
            background: transparent !important;
        }
        
        .fi-wizard-header {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .fi-wizard-step-icon {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .fi-wizard-step-label {
            font-weight: 600;
            color: #374151;
            margin-top: 0.5rem;
        }
        
        .fi-section {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .fi-section-header-heading {
            color: #111827;
            font-weight: 600;
        }
        
        .fi-section-header-description {
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
</x-filament-panels::page>