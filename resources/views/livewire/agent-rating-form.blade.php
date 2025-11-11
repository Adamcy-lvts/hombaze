<div class="agent-rating-form">
    @auth
        <!-- Rating Button -->
            <div class="mb-4">
                <button
                    wire:click="toggleForm"
                    class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span>
                        @if($existingReview)
                            Update Your Review
                        @else
                            Write a Review
                        @endif
                    </span>
                </button>
            </div>

            <!-- Existing Review Display -->
            @if($existingReview && !$showForm)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2 mb-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-semibold text-gray-900">Your Review</span>
                            </div>
                            @if($existingReview->title)
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $existingReview->title }}</h4>
                            @endif
                            <p class="text-gray-700 text-sm">{{ $existingReview->comment }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Rating Form -->
            @if($showForm)
                <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-lg">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        @if($existingReview)
                            Update Your Review for {{ $agent->name }}
                        @else
                            Rate {{ $agent->name }}
                        @endif
                    </h3>

                    <form wire:submit="submitReview">
                        <!-- Star Rating -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-900 mb-3">Rating *</label>
                            <div class="flex items-center space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button
                                        type="button"
                                        wire:click="$set('rating', {{ $i }})"
                                        class="p-1 hover:scale-110 transition-transform duration-200"
                                    >
                                        <svg class="w-8 h-8 {{ $i <= $rating ? 'text-yellow-400 hover:text-yellow-500' : 'text-gray-300 hover:text-yellow-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                                <span class="ml-3 text-sm text-gray-600">{{ $rating }} out of 5 stars</span>
                            </div>
                            @error('rating')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Title (Optional) -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">Review Title (Optional)</label>
                            <input
                                type="text"
                                id="title"
                                wire:model="title"
                                placeholder="Summarize your experience in a few words"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                maxlength="255"
                            >
                            @error('title')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Comment -->
                        <div class="mb-6">
                            <label for="comment" class="block text-sm font-semibold text-gray-900 mb-2">Your Review *</label>
                            <textarea
                                id="comment"
                                wire:model="comment"
                                rows="5"
                                placeholder="Share details about your experience with this agent. What made them stand out? How did they help you? (Minimum 20 characters)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                                maxlength="1000"
                            ></textarea>
                            <div class="flex justify-between items-center mt-2">
                                @error('comment')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @else
                                    <span class="text-gray-500 text-sm">Minimum 20 characters</span>
                                @enderror
                                <span class="text-gray-500 text-sm">{{ strlen($comment) }}/1000</span>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center space-x-4">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="submitReview"
                                class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 disabled:opacity-50 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300"
                                {{ $isSubmitting ? 'disabled' : '' }}
                            >
                                <span wire:loading.remove wire:target="submitReview">
                                    @if($existingReview)
                                        Update Review
                                    @else
                                        Submit Review
                                    @endif
                                </span>
                                <span wire:loading wire:target="submitReview" class="flex items-center space-x-2">
                                    <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Submitting...</span>
                                </span>
                            </button>

                            <button
                                type="button"
                                wire:click="toggleForm"
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            @endif
    @else
        <!-- Not Logged In -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <div class="flex items-center space-x-3">
                <svg class="w-5 h-5 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Login Required</p>
                    <p class="text-sm text-gray-700">You must be logged in to write a review. <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Sign in here</a></p>
                </div>
            </div>
        </div>
    @endauth
</div>