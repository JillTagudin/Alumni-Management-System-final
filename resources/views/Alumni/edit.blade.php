<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Edit Alumni Record</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 900px;
                margin: 50px auto;
                padding: 20px;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
            }

            h1 {
                text-align: center;
                color: #333;
            }

            .form-group {
                margin-bottom: 15px;
            }

            label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
                color: #333;
            }

            input[type="text"],
            input[type="number"],
            input[type="email"] {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }

            input[type="submit"] {
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
                font-size: 16px;
            }

            input[type="submit"]:hover {
                background-color: #45a049;
            }

            .error-list {
                background-color: #f8d7da;
                color: #721c24;
                padding: 10px;
                margin-bottom: 20px;
                border: 1px solid #f5c6cb;
                border-radius: 4px;
            }

            .error-list ul {
                list-style-type: none;
                padding: 0;
            }

            .error-list li {
                margin: 5px 0;
            }

            /* Add these new modal styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 500px;
                border-radius: 5px;
            }

            .modal-buttons {
                margin-top: 20px;
                text-align: right;
            }

            .modal-button {
                padding: 8px 16px;
                margin-left: 10px;
                border-radius: 4px;
                cursor: pointer;
            }

            .confirm-button {
                background-color: #4CAF50;
                color: white;
                border: none;
            }

            .cancel-button {
                background-color: #f44336;
                color: white;
                border: none;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <h1>Edit Alumni Record</h1>

            @if ($errors->any())
            <div class="error-list">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="post" action="{{ route('Alumni.update', ['id' => $alumni->id]) }}" id="updateForm">
                @csrf
                @method('put')

                <div class="form-group">
                    <label for="AlumniID">Alumni ID</label>
                    <input type="text" id="AlumniID" name="AlumniID" value="{{ $alumni->AlumniID }}" placeholder="Leave blank if not specified" />
                </div>

                <div class="form-group">
                    <label for="student_number">Student Number</label>
                    <input type="text" id="student_number" name="student_number" value="{{ $alumni->student_number }}" placeholder="Student Number" required />
                </div>

                <div class="form-group">
                    <label for="Fullname">Fullname</label>
                    <input type="text" id="Fullname" name="Fullname" value="{{ $alumni->Fullname }}" required />
                </div>

                <div class="form-group">
                    <label for="Age">Age</label>
                    <input type="number" id="Age" name="Age" value="{{ $alumni->Age }}" required />
                </div>

                <div class="form-group">
                    <label for="Gender">Gender</label>
                    <select id="Gender" name="Gender" required>
                        <option value="">Please fill out this field.</option>
                        <option value="Male" {{ $alumni->Gender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $alumni->Gender == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Course">Course</label>
                    <select id="Course" name="Course" required>
                        <option value="">Please fill out this field.</option>
                        <option value="BS Information Technology" {{ $alumni->Course == 'BS Information Technology' ? 'selected' : '' }}>BS Information Technology</option>
                        <option value="BS Hospitality Management" {{ $alumni->Course == 'BS Hospitality Management' ? 'selected' : '' }}>BS Hospitality Management</option>
                        <option value="BS Office Administration" {{ $alumni->Course == 'BS Office Administration' ? 'selected' : '' }}>BS Office Administration</option>
                        <option value="BS Business Administration" {{ $alumni->Course == 'BS Business Administration' ? 'selected' : '' }}>BS Business Administration</option>
                        <option value="BS Criminology" {{ $alumni->Course == 'BS Criminology' ? 'selected' : '' }}>BS Criminology</option>
                        <option value="Bachelor of Elementary Education" {{ $alumni->Course == 'Bachelor of Elementary Education' ? 'selected' : '' }}>Bachelor of Elementary Education</option>
                        <option value="Bachelor of Secondary Education" {{ $alumni->Course == 'Bachelor of Secondary Education' ? 'selected' : '' }}>Bachelor of Secondary Education</option>
                        <option value="BS Computer Engineering" {{ $alumni->Course == 'BS Computer Engineering' ? 'selected' : '' }}>BS Computer Engineering</option>
                        <option value="BS Tourism Management" {{ $alumni->Course == 'BS Tourism Management' ? 'selected' : '' }}>BS Tourism Management</option>
                        <option value="BS Entrepreneurship" {{ $alumni->Course == 'BS Entrepreneurship' ? 'selected' : '' }}>BS Entrepreneurship</option>
                        <option value="BS Accounting Information System" {{ $alumni->Course == 'BS Accounting Information System' ? 'selected' : '' }}>BS Accounting Information System</option>
                        <option value="BS Psychology" {{ $alumni->Course == 'BS Psychology' ? 'selected' : '' }}>BS Psychology</option>
                        <option value="BL Information Science" {{ $alumni->Course == 'BL Information Science' ? 'selected' : '' }}>BL Information Science</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="Section">Year/Section</label>
                    <input type="text" id="Section" name="Section" value="{{ $alumni->Section }}" required />
                </div>

                <div class="form-group">
                    <label for="Batch">Batch</label>
                    <input type="text" id="Batch" name="Batch" value="{{ $alumni->Batch }}" required />
                </div>

                <!-- Contact -->
                <div class="form-group">
                    <label for="Contact">Contact</label>
                    <input
                        type="tel"
                        id="Contact"
                        name="Contact"
                        value="{{ $alumni->Contact }}"
                        required
                        inputmode="numeric"
                        pattern="\d{11}"
                        minlength="11"
                        maxlength="11"
                        title="Contact number must be exactly 11 digits"
                    />
                </div>

                <div class="form-group">
                    <label for="Address">Address</label>
                    <input type="text" id="Address" name="Address" value="{{ $alumni->Address }}" required />
                </div>

                <div class="form-group">
                    <label for="Emailaddress">Email Address</label>
                    <input type="email" id="Emailaddress" name="Emailaddress" value="{{ $alumni->Emailaddress }}" required />
                </div>

                <div class="form-group">
                    <label for="Occupation">Occupation</label>
                    <input type="text" id="Occupation" name="Occupation" value="{{ $alumni->Occupation }}" required />
                </div>

                <div class="form-group">
                    <label for="Company">Company</label>
                    <input type="text" id="Company" name="Company" value="{{ $alumni->Company }}" placeholder="Enter company name" />
                </div>

                <div class="form-group">
                    @if(auth()->user()->role === 'Staff')
                  <input type="button" value="Update Record" onclick="handleStaffUpdate()" class="submit-button" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
                @elseif(in_array(auth()->user()->role, ['Admin', 'SuperAdmin']))
                  <input type="button" value="Update Record" onclick="showConfirmModal()" class="submit-button" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
                @else
                  <input type="submit" value="Update Record" class="submit-button" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
                @endif
                </div>
            </form>

            <!-- Include Staff Approval Modal for Staff Users -->
            @if(auth()->user()->role === 'Staff')
              <x-staff-approval-modal />
            @endif

            <!-- Add the modal HTML for Admin and SuperAdmin users -->
            @if(in_array(auth()->user()->role, ['Admin', 'SuperAdmin']))
            <div id="confirmModal" class="modal">
                <div class="modal-content">
                    <h2 style="margin-top: 0;">Confirm Update</h2>
                    <p>Are you sure you want to update this alumni record?</p>
                    <div class="modal-buttons">
                        <button class="modal-button cancel-button" onclick="hideConfirmModal()">Cancel</button>
                        <button class="modal-button confirm-button" onclick="submitForm()">Confirm</button>
                    </div>
                </div>
            </div>
            @endif

            <script>
                @if(in_array(auth()->user()->role, ['Admin', 'SuperAdmin']))
                const modal = document.getElementById('confirmModal');
                const form = document.getElementById('updateForm');

                function showConfirmModal() {
                    modal.style.display = "block";
                }

                function hideConfirmModal() {
                    modal.style.display = "none";
                }

                function submitForm() {
                    form.submit();
                }

                // Close modal when clicking outside
                window.onclick = function(event) {
                    if (event.target == modal) {
                        hideConfirmModal();
                    }
                }
                @endif

                @if(auth()->user()->role === 'Staff')
                function handleStaffUpdate() {
                    // Submit the form first to create the pending change
                    const form = document.getElementById('updateForm');
                    
                    // Create a hidden input to indicate this is a staff submission
                    const staffInput = document.createElement('input');
                    staffInput.type = 'hidden';
                    staffInput.name = 'staff_submission';
                    staffInput.value = '1';
                    form.appendChild(staffInput);
                    
                    // Submit the form
                    form.submit();
                    
                    // The controller will handle the redirect and show success message
                    // No need to show modal here as the page will redirect
                }
                @endif
            </script>
        </div>
    </body>
    </html>
</x-admin-layout>
