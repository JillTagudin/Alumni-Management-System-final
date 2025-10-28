<x-guest-layout>
    <!-- Registration Status Indicator -->
    <div id="registration-status" class="mb-4 p-4 rounded-lg hidden">
        <div class="flex items-center">
            <div id="status-icon" class="mr-3"></div>
            <div>
                <div id="status-title" class="font-semibold"></div>
                <div id="status-message" class="text-sm"></div>
            </div>
        </div>
        <div id="progress-bar" class="mt-3 w-full bg-gray-200 rounded-full h-2 hidden">
            <div id="progress-fill" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
    </div>

    <form method="POST" action="{{ route('user.register') }}" id="registration-form">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <div class="relative">
                <x-text-input id="name" class="block mt-1 w-full pr-10" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <div id="name-validation" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
            <div id="name-feedback" class="mt-1 text-sm hidden"></div>
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative">
                <x-text-input id="email" class="block mt-1 w-full pr-10" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <div id="email-validation" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            <div id="email-feedback" class="mt-1 text-sm hidden"></div>
        </div>

        <!-- Student Number -->
        <div class="mt-4">
            <x-input-label for="student_number" :value="__('Student Number')" />
            <div class="relative">
                <x-text-input id="student_number" class="block mt-1 w-full pr-10" type="text" name="student_number" :value="old('student_number')" required maxlength="20" placeholder="Enter your student number" />
                <div id="student-number-validation" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
            <div id="student-number-feedback" class="mt-1 text-sm hidden"></div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative">
                <x-text-input id="password" class="block mt-1 w-full pr-10"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
                <div id="password-validation" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div id="password-feedback" class="mt-1 text-sm hidden"></div>
            <!-- Password Strength Indicator -->
            <div id="password-strength" class="mt-2 hidden">
                <div class="flex space-x-1">
                    <div class="h-2 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-2 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-2 w-1/4 bg-gray-200 rounded"></div>
                    <div class="h-2 w-1/4 bg-gray-200 rounded"></div>
                </div>
                <div id="strength-text" class="text-xs mt-1"></div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-password-input 
                id="password_confirmation" 
                name="password_confirmation" 
                class="block mt-1 w-full"
                placeholder="Confirm your password"
                required 
            />
            <div class="relative">
                <x-text-input id="password_confirmation" class="block mt-1 w-full pr-10"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />
                <div id="password-confirmation-validation" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            <div id="password-confirmation-feedback" class="mt-1 text-sm hidden"></div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button id="register-button" class="ms-4" disabled>
                <span id="button-text">{{ __('Register') }}</span>
                <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('registration-form');
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const studentNumberInput = document.getElementById('student_number');
            const passwordInput = document.getElementById('password');
            const passwordConfirmationInput = document.getElementById('password_confirmation');
            const registerButton = document.getElementById('register-button');
            const buttonText = document.getElementById('button-text');
            const loadingSpinner = document.getElementById('loading-spinner');
            
            let validationState = {
                name: false,
                email: false,
                studentNumber: false,
                password: false,
                passwordConfirmation: false
            };
            
            // Real-time validation functions
            function validateName() {
                const value = nameInput.value.trim();
                const isValid = value.length >= 2;
                
                updateValidationUI('name', isValid, isValid ? 'Name looks good!' : 'Name must be at least 2 characters');
                validationState.name = isValid;
                updateSubmitButton();
            }
            
            function validateEmail() {
                const value = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                const isValid = emailRegex.test(value);
                
                if (isValid) {
                    // Check for duplicate email
                    checkEmailAvailability(value);
                } else {
                    updateValidationUI('email', false, 'Please enter a valid email address');
                    validationState.email = false;
                    updateSubmitButton();
                }
            }
            
            function checkEmailAvailability(email) {
                fetch('/api/check-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email: email })
                })
                .then(response => response.json())
                .then(data => {
                    const isAvailable = data.available;
                    updateValidationUI('email', isAvailable, isAvailable ? 'Email is available!' : 'Email is already registered');
                    validationState.email = isAvailable;
                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Error checking email:', error);
                    updateValidationUI('email', true, 'Email format is valid');
                    validationState.email = true;
                    updateSubmitButton();
                });
            }
            
            function validateStudentNumber() {
                const value = studentNumberInput.value.trim();
                const formatValid = value.length >= 3 && value.length <= 20 && /^[A-Za-z0-9-]+$/.test(value);
                
                if (!formatValid) {
                    updateValidationUI('student-number', false, 'Student number must be 3-20 characters (letters, numbers, hyphens only)');
                    validationState.studentNumber = false;
                    updateSubmitButton();
                    return;
                }
                
                // Check student number availability
                fetch('/api/check-student-number', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ student_number: value })
                })
                .then(response => response.json())
                .then(data => {
                    const isAvailable = data.available;
                    updateValidationUI('student-number', isAvailable, 
                        isAvailable ? 'Student number is available!' : 'Student number is already registered');
                    validationState.studentNumber = isAvailable;
                    updateSubmitButton();
                })
                .catch(error => {
                    console.error('Error checking student number:', error);
                    // On error, just validate format
                    updateValidationUI('student-number', true, 'Student number format is valid');
                    validationState.studentNumber = true;
                    updateSubmitButton();
                });
            }
            
            function validatePassword() {
                const value = passwordInput.value;
                const strength = calculatePasswordStrength(value);
                const isValid = strength >= 3; // Require at least medium strength
                
                updatePasswordStrength(strength);
                updateValidationUI('password', isValid, getPasswordFeedback(strength));
                validationState.password = isValid;
                
                // Also validate confirmation if it has a value
                if (passwordConfirmationInput.value) {
                    validatePasswordConfirmation();
                }
                
                updateSubmitButton();
            }
            
            function validatePasswordConfirmation() {
                const password = passwordInput.value;
                const confirmation = passwordConfirmationInput.value;
                const isValid = password === confirmation && password.length > 0;
                
                updateValidationUI('password-confirmation', isValid, isValid ? 'Passwords match!' : 'Passwords do not match');
                validationState.passwordConfirmation = isValid;
                updateSubmitButton();
            }
            
            function calculatePasswordStrength(password) {
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/[0-9]/.test(password)) strength++;
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                return strength;
            }
            
            function updatePasswordStrength(strength) {
                const strengthIndicator = document.getElementById('password-strength');
                const strengthText = document.getElementById('strength-text');
                const bars = strengthIndicator.querySelectorAll('.h-2');
                
                strengthIndicator.classList.remove('hidden');
                
                const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];
                const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
                
                bars.forEach((bar, index) => {
                    bar.className = `h-2 w-1/4 rounded ${index < strength ? colors[strength - 1] : 'bg-gray-200'}`;
                });
                
                strengthText.textContent = strength > 0 ? texts[strength - 1] : '';
                strengthText.className = `text-xs mt-1 ${strength >= 3 ? 'text-green-600' : 'text-red-600'}`;
            }
            
            function getPasswordFeedback(strength) {
                if (strength < 3) return 'Password should be stronger (8+ chars, mixed case, numbers, symbols)';
                if (strength === 3) return 'Good password strength';
                return 'Strong password!';
            }
            
            function updateValidationUI(field, isValid, message) {
                const input = document.getElementById(field);
                const validation = document.getElementById(`${field}-validation`);
                const feedback = document.getElementById(`${field}-feedback`);
                
                if (isValid) {
                    input.classList.remove('border-red-500');
                    input.classList.add('border-green-500');
                    validation.classList.remove('hidden', 'text-red-500');
                    validation.classList.add('text-green-500');
                    feedback.classList.remove('hidden', 'text-red-600');
                    feedback.classList.add('text-green-600');
                } else {
                    input.classList.remove('border-green-500');
                    input.classList.add('border-red-500');
                    validation.classList.add('hidden');
                    feedback.classList.remove('hidden', 'text-green-600');
                    feedback.classList.add('text-red-600');
                }
                
                feedback.textContent = message;
            }
            
            function updateSubmitButton() {
                const allValid = Object.values(validationState).every(state => state);
                registerButton.disabled = !allValid;
                
                if (allValid) {
                    registerButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    registerButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
            
            function showRegistrationStatus(type, title, message, showProgress = false) {
                const statusDiv = document.getElementById('registration-status');
                const statusIcon = document.getElementById('status-icon');
                const statusTitle = document.getElementById('status-title');
                const statusMessage = document.getElementById('status-message');
                const progressBar = document.getElementById('progress-bar');
                
                statusDiv.classList.remove('hidden', 'bg-blue-50', 'bg-green-50', 'bg-red-50', 'bg-yellow-50');
                statusDiv.classList.add(`bg-${type}-50`);
                
                const icons = {
                    blue: '🔄',
                    green: '✅',
                    red: '❌',
                    yellow: '⚠️'
                };
                
                statusIcon.textContent = icons[type] || '📝';
                statusTitle.textContent = title;
                statusMessage.textContent = message;
                
                if (showProgress) {
                    progressBar.classList.remove('hidden');
                } else {
                    progressBar.classList.add('hidden');
                }
            }
            
            function updateProgress(percentage) {
                const progressFill = document.getElementById('progress-fill');
                progressFill.style.width = `${percentage}%`;
            }
            
            // Event listeners
            nameInput.addEventListener('input', validateName);
            emailInput.addEventListener('input', debounce(validateEmail, 500));
            studentNumberInput.addEventListener('input', validateStudentNumber);
            passwordInput.addEventListener('input', validatePassword);
            passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);
            
            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Show loading state
                buttonText.textContent = 'Creating Account...';
                loadingSpinner.classList.remove('hidden');
                registerButton.disabled = true;
                
                showRegistrationStatus('blue', 'Creating Account', 'Please wait while we set up your account...', true);
                updateProgress(25);
                
                // Simulate registration steps
                setTimeout(() => {
                    updateProgress(50);
                    showRegistrationStatus('blue', 'Validating Information', 'Checking your details...');
                }, 1000);
                
                setTimeout(() => {
                    updateProgress(75);
                    showRegistrationStatus('blue', 'Setting Up Profile', 'Almost done...');
                }, 2000);
                
                // Submit the form
                setTimeout(() => {
                    updateProgress(100);
                    form.submit();
                }, 3000);
            });
            
            // Debounce function
            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }
        });
    </script>
</x-guest-layout>