@extends('layouts.user')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Profile') }}
    </h2>
@endsection

@section('content')




    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>
                    
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 success-message">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 error-message">
                            {{ $errors->first('error') }}
                        </div>
                    @endif

                    @if ($errors->has('fullname'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 error-message">
                            {{ $errors->first('fullname') }}
                        </div>
                    @endif

                    @if ($errors->has('email'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 error-message">
                            {{ $errors->first('email') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <!-- Profile Picture -->
                        <div class="mb-6">
                            <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    @if(Auth::user()->profile_picture)
                                        <img id="preview" class="h-16 w-16 object-cover rounded-full" src="{{ Auth::user()->profile_picture_url }}" alt="Current profile photo" />
                                    @else
                                        <div id="preview" class="h-16 w-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-600 font-semibold text-lg">
                                            {{ Auth::user()->initials }}
                                        </div>
                                    @endif
                                </div>
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*" 
                                           class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100" 
                                           onchange="previewImage(event)" />
                                </label>
                            </div>
                            @error('profile_picture')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Alumni ID -->
                            <div>
                                <label for="alumni_id" class="block text-sm font-medium text-gray-700">Alumni ID</label>
                                <input type="text" name="alumni_id" id="alumni_id" 
                                       value="{{ old('alumni_id', Auth::user()->alumni_id) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('alumni_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Student Number -->
                            <div>
                                <label for="student_number" class="block text-sm font-medium text-gray-700">Student Number</label>
                                <input type="text" name="student_number" id="student_number" 
                                       value="{{ old('student_number', Auth::user()->student_number) }}"
                                       placeholder="Enter your student number"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('student_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Full Name -->
                            <div>
                                <label for="fullname" class="block text-sm font-medium text-gray-700">Full Name</label>
                                <input type="text" name="fullname" id="fullname" 
                                       value="{{ old('fullname', Auth::user()->fullname ?: Auth::user()->name) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('fullname')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Age -->
                            <div>
                                <label for="age" class="block text-sm font-medium text-gray-700">Age</label>
                                <input type="number" name="age" id="age" 
                                       value="{{ old('age', Auth::user()->age) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('age')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700">Gender</label>
                                <select name="gender" id="gender" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', Auth::user()->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', Auth::user()->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', Auth::user()->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Course -->
                            <div>
                                <label for="course" class="block text-sm font-medium text-gray-700">Course</label>
                                <select name="course" id="course" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="" {{ old('course', Auth::user()->course) == '' ? 'selected' : '' }}>Please fill out this field.</option>
                                    <option value="BS Information Technology" {{ old('course', Auth::user()->course) == 'BS Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                                    <option value="BS Hospitality Management" {{ old('course', Auth::user()->course) == 'BS Hospitality Management' ? 'selected' : '' }}>BS Hospitality Management</option>
                                    <option value="BS Office Administration" {{ old('course', Auth::user()->course) == 'BS Office Administration' ? 'selected' : '' }}>BS Office Administration</option>
                                    <option value="BS Business Administration" {{ old('course', Auth::user()->course) == 'BS Business Administration' ? 'selected' : '' }}>BS Business Administration</option>
                                    <option value="BS Criminology" {{ old('course', Auth::user()->course) == 'BS Criminology' ? 'selected' : '' }}>BS Criminology</option>
                                    <option value="Bachelor of Elementary Education" {{ old('course', Auth::user()->course) == 'Bachelor of Elementary Education' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                                    <option value="Bachelor of Secondary Education" {{ old('course', Auth::user()->course) == 'Bachelor of Secondary Education' ? 'selected' : '' }}>Bachelor of Secondary Education</option>
                                    <option value="BS Computer Engineering" {{ old('course', Auth::user()->course) == 'BS Computer Engineering' ? 'selected' : '' }}>BS Computer Engineering</option>
                                    <option value="BS Tourism Management" {{ old('course', Auth::user()->course) == 'BS Tourism Management' ? 'selected' : '' }}>BS Tourism Management</option>
                                    <option value="BS Entrepreneurship" {{ old('course', Auth::user()->course) == 'BS Entrepreneurship' ? 'selected' : '' }}>BS Entrepreneurship</option>
                                    <option value="BS Accounting Information System" {{ old('course', Auth::user()->course) == 'BS Accounting Information System' ? 'selected' : '' }}>BS Accounting Information System</option>
                                    <option value="BS Psychology" {{ old('course', Auth::user()->course) == 'BS Psychology' ? 'selected' : '' }}>BS Psychology</option>
                                    <option value="BL Information Science" {{ old('course', Auth::user()->course) == 'BL Information Science' ? 'selected' : '' }}>BL Information Science</option>
                                </select>
                                @error('course')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Section -->
                            <div>
                                <label for="section" class="block text-sm font-medium text-gray-700">Year/Section</label>
                                <input type="text" name="section" id="section" 
                                       value="{{ old('section', Auth::user()->section) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('section')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Batch -->
                            <div>
                                <label for="batch" class="block text-sm font-medium text-gray-700">Batch</label>
                                <input type="text" name="batch" id="batch" 
                                       value="{{ old('batch', Auth::user()->batch) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('batch')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Contact -->
                            <div>
                                <label for="contact" class="block text-sm font-medium text-gray-700">Contact</label>
                                <input
                                    type="tel"
                                    name="contact"
                                    id="contact"
                                    value="{{ old('contact', Auth::user()->contact) }}"
                                    inputmode="numeric"
                                    pattern="\d{11}"
                                    minlength="11"
                                    maxlength="11"
                                    placeholder="11-digit contact number"
                                    title="Enter 11 digits, numbers only"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('contact')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                <textarea name="address" id="address" rows="3" 
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', Auth::user()->address) }}</textarea>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Address -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                <input type="email" name="email" id="email" 
                                       value="{{ old('email', Auth::user()->email) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Occupation -->
                            <div>
                                <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                                <input type="text" name="occupation" id="occupation" 
                                       value="{{ old('occupation', Auth::user()->occupation) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('occupation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Company -->
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700">Company</label>
                                <input type="text" name="company" id="company" 
                                       value="{{ old('company', Auth::user()->company) }}"
                                       placeholder="Enter your company name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('company')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Social Media Profiles Section -->
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Social Media Profiles</h3>
                            <p class="text-sm text-gray-600 mb-6">Add your social media profile links to connect with other alumni.</p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Facebook Profile -->
                                <div>
                                    <label for="facebook_profile" class="block text-sm font-medium text-gray-700">
                                        <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook Profile
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="url" name="facebook_profile" id="facebook_profile" 
                                               value="{{ old('facebook_profile', Auth::user()->facebook_profile) }}"
                                               placeholder="https://facebook.com/yourprofile"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                                        <button type="button" onclick="openFacebookHelp()" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        📱 Mobile: Open Facebook app → Your profile → "More" → "Copy Link"
                                    </p>
                                    @error('facebook_profile')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- LinkedIn Profile -->
                                <div>
                                    <label for="linkedin_profile" class="block text-sm font-medium text-gray-700">
                                        <i class="fab fa-linkedin text-blue-700 mr-2"></i>LinkedIn Profile
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="url" name="linkedin_profile" id="linkedin_profile" 
                                               value="{{ old('linkedin_profile', Auth::user()->linkedin_profile) }}"
                                               placeholder="https://linkedin.com/in/yourprofile"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                                        <button type="button" onclick="openLinkedInHelp()" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        📱 Mobile: LinkedIn app → Your profile → "Share" → "Copy link"
                                    </p>
                                    @error('linkedin_profile')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Twitter Profile -->
                                <div>
                                    <label for="twitter_profile" class="block text-sm font-medium text-gray-700">
                                        <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter Profile
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="url" name="twitter_profile" id="twitter_profile" 
                                               value="{{ old('twitter_profile', Auth::user()->twitter_profile) }}"
                                               placeholder="https://twitter.com/yourprofile"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                                        <button type="button" onclick="openTwitterHelp()" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        📱 Mobile: Twitter app → Your profile → Share icon → "Copy link"
                                    </p>
                                    @error('twitter_profile')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Instagram Profile -->
                                <div>
                                    <label for="instagram_profile" class="block text-sm font-medium text-gray-700">
                                        <i class="fab fa-instagram text-pink-600 mr-2"></i>Instagram Profile
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="url" name="instagram_profile" id="instagram_profile" 
                                               value="{{ old('instagram_profile', Auth::user()->instagram_profile) }}"
                                               placeholder="https://instagram.com/yourprofile"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
                                        <button type="button" onclick="openInstagramHelp()" 
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">
                                        📱 Mobile: Instagram app → Your profile → "..." → "Copy Profile URL"
                                    </p>
                                    @error('instagram_profile')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Profile
                            </button>
                        </div>
                    </form>

                    <!-- Password Update Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                            <p class="mt-1 text-sm text-gray-600">Ensure your account is using a long, random password to stay secure.</p>
                        </div>

                        <form method="POST" action="{{ route('user.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Current Password -->
                                <div class="md:col-span-2">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error('current_password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="password" id="password" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           required>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" 
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media Help Modal -->
    <div id="socialMediaHelpModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modalTitle">How to get your profile link</h3>
                    <button onclick="closeHelpModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent" class="text-sm text-gray-600">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="mt-4">
                    <button onclick="closeHelpModal()" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img class="h-16 w-16 object-cover rounded-full" src="${e.target.result}" alt="Profile preview" />`;
                }
                reader.readAsDataURL(file);
            }
        }

        // Social Media Help Functions
        function openFacebookHelp() {
            document.getElementById('modalTitle').textContent = 'How to get your Facebook profile link';
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-blue-600 mb-2">📱 On Mobile (Facebook App):</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Open the Facebook app</li>
                            <li>Go to your profile (tap your profile picture)</li>
                            <li>Tap the "More" button (three dots)</li>
                            <li>Select "Copy Link"</li>
                            <li>Paste the link in the field above</li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-600 mb-2">💻 On Desktop:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Go to facebook.com and log in</li>
                            <li>Click on your name/profile picture</li>
                            <li>Copy the URL from your browser's address bar</li>
                        </ol>
                    </div>
                </div>
            `;
            document.getElementById('socialMediaHelpModal').classList.remove('hidden');
        }

        function openLinkedInHelp() {
            document.getElementById('modalTitle').textContent = 'How to get your LinkedIn profile link';
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-blue-700 mb-2">📱 On Mobile (LinkedIn App):</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Open the LinkedIn app</li>
                            <li>Go to your profile</li>
                            <li>Tap the "Share" button</li>
                            <li>Select "Copy link"</li>
                            <li>Paste the link in the field above</li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-700 mb-2">💻 On Desktop:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Go to linkedin.com and log in</li>
                            <li>Click on "Me" and then "View profile"</li>
                            <li>Copy the URL from your browser's address bar</li>
                        </ol>
                    </div>
                </div>
            `;
            document.getElementById('socialMediaHelpModal').classList.remove('hidden');
        }

        function openTwitterHelp() {
            document.getElementById('modalTitle').textContent = 'How to get your Twitter profile link';
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-blue-400 mb-2">📱 On Mobile (Twitter/X App):</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Open the Twitter/X app</li>
                            <li>Go to your profile</li>
                            <li>Tap the share icon (arrow pointing up)</li>
                            <li>Select "Copy link"</li>
                            <li>Paste the link in the field above</li>
                        </ol>
                    </div>
                    <div>
                        <h4 class="font-semibold text-blue-400 mb-2">💻 On Desktop:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Go to twitter.com (or x.com) and log in</li>
                            <li>Click on your profile</li>
                            <li>Copy the URL from your browser's address bar</li>
                        </ol>
                    </div>
                </div>
            `;
            document.getElementById('socialMediaHelpModal').classList.remove('hidden');
        }

        function openInstagramHelp() {
            document.getElementById('modalTitle').textContent = 'How to get your Instagram profile link';
            document.getElementById('modalContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <h4 class="font-semibold text-pink-600 mb-2">📱 On Mobile (Instagram App):</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Open the Instagram app</li>
                            <li>Go to your profile</li>
                            <li>Tap the three lines (menu) in the top right</li>
                            <li>Select "QR code"</li>
                            <li>Tap "Share Profile" at the bottom</li>
                            <li>Select "Copy Profile URL"</li>
                            <li>Paste the link in the field above</li>
                        </ol>
                        <p class="text-xs text-gray-500 mt-2">Alternative: Tap "..." on your profile → "Copy Profile URL"</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-pink-600 mb-2">💻 On Desktop:</h4>
                        <ol class="list-decimal list-inside space-y-1 text-sm">
                            <li>Go to instagram.com and log in</li>
                            <li>Click on your profile picture</li>
                            <li>Copy the URL from your browser's address bar</li>
                        </ol>
                    </div>
                </div>
            `;
            document.getElementById('socialMediaHelpModal').classList.remove('hidden');
        }

        function closeHelpModal() {
            document.getElementById('socialMediaHelpModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('socialMediaHelpModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeHelpModal();
            }
        });

        // Auto-hide success message after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                let successMessage = document.querySelector(".success-message");
                if (successMessage) {
                    successMessage.style.transition = "opacity 0.5s";
                    successMessage.style.opacity = "0";
                    setTimeout(() => successMessage.remove(), 500);
                }
            }, 3000);
        });
    </script>

@endsection