<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Please confirm access to your account by entering the authentication code provided to your email address.') }}
    </div>

    @if (session('message'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.verify') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" :value="old('code')" required autofocus autocomplete="one-time-code" maxlength="6" placeholder="Enter 6-digit code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <button type="button" id="resend-code" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Resend Code') }}
            </button>

            <x-primary-button>
                {{ __('Verify') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-4">
        <a href="{{ route('login') }}" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            {{ __('Back to Login') }}
        </a>
    </div>

    <!-- Modal for 2FA Resend Notification -->
    <div id="resend-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div id="modal-icon" class="mx-auto flex items-center justify-center h-12 w-12 rounded-full mb-4">
                    <!-- Success Icon -->
                    <svg id="success-icon" class="h-6 w-6 text-green-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <!-- Error Icon -->
                    <svg id="error-icon" class="h-6 w-6 text-red-600 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h3 id="modal-title" class="text-lg leading-6 font-medium text-gray-900 mb-2">Verification Code</h3>
                <div class="mt-2 px-7 py-3">
                    <p id="modal-message" class="text-sm text-gray-500"></p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="modal-ok-btn" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function showModal(message, isSuccess = true) {
            const modal = document.getElementById('resend-modal');
            const modalMessage = document.getElementById('modal-message');
            const successIcon = document.getElementById('success-icon');
            const errorIcon = document.getElementById('error-icon');
            const modalIcon = document.getElementById('modal-icon');
            
            modalMessage.textContent = message;
            
            if (isSuccess) {
                successIcon.classList.remove('hidden');
                errorIcon.classList.add('hidden');
                modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4';
            } else {
                successIcon.classList.add('hidden');
                errorIcon.classList.remove('hidden');
                modalIcon.className = 'mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4';
            }
            
            modal.classList.remove('hidden');
        }
        
        function hideModal() {
            document.getElementById('resend-modal').classList.add('hidden');
        }
        
        // Close modal when OK button is clicked
        document.getElementById('modal-ok-btn').addEventListener('click', hideModal);
        
        // Close modal when clicking outside
        document.getElementById('resend-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideModal();
            }
        });

        document.getElementById('resend-code').addEventListener('click', function() {
            const button = this;
            button.disabled = true;
            button.textContent = 'Sending...';
            
            fetch('{{ route("two-factor.resend") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showModal('New verification code sent to your email!', true);
                } else {
                    showModal(data.message || 'Failed to resend code. Please try again.', false);
                }
            })
            .catch(error => {
                showModal('An error occurred. Please try again.', false);
            })
            .finally(() => {
                button.disabled = false;
                button.textContent = 'Resend Code';
            });
        });

        // Auto-format code input
        document.getElementById('code').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 6) {
                value = value.substring(0, 6);
            }
            e.target.value = value;
        });
    </script>
</x-guest-layout>