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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Create Alumni Record</title>
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
      transition: opacity 0.5s;
    }

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
      background-color: #0000FF;
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
    <h1>Create Alumni Record</h1>

    @if ($errors->any())
    <div class="error-list" id="errorList">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>

    <script>
      setTimeout(() => {
        let errorList = document.getElementById("errorList");
        if (errorList) {
          errorList.style.transition = "opacity 0.5s";
          errorList.style.opacity = "0";
          setTimeout(() => errorList.remove(), 500);
        }
      }, 3000);
    </script>
    @endif

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative success-message" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <form method="post" action="{{ route('Alumni.store') }}" id="createForm">
      @csrf
      @method('post')

      <div class="form-group">
        <label for="AlumniID">Alumni ID</label>
        <input type="text" id="AlumniID" name="AlumniID" placeholder="Alumni ID" required />
      </div>
      
      <div class="form-group">
        <label for="student_number">Student Number</label>
        <input type="text" id="student_number" name="student_number" placeholder="Student Number" required />
      </div>

      <div class="form-group">
        <label for="Fullname">Fullname</label>
        <input type="text" id="Fullname" name="Fullname" placeholder="Fullname" required />
      </div>

      <div class="form-group">
        <label for="Age">Age</label>
        <input type="number" id="Age" name="Age" placeholder="Age" required />
      </div>

      <div class="form-group">
        <label for="Gender">Gender</label>
        <select id="Gender" name="Gender" required>
          <option value="">Please fill out this field.</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>

      <div class="form-group">
        <label for="Course">Course</label>
        <select id="Course" name="Course" required>
          <option value="">Please fill out this field.</option>
          <option value="BS Information Technology">BS Information Technology</option>
          <option value="BS Hospitality Management">BS Hospitality Management</option>
          <option value="BS Office Administration">BS Office Administration</option>
          <option value="BS Business Administration">BS Business Administration</option>
          <option value="BS Criminology">BS Criminology</option>
          <option value="Bachelor of Elementary Education">Bachelor of Elementary Education</option>
          <option value="Bachelor of Secondary Education">Bachelor of Secondary Education</option>
          <option value="BS Computer Engineering">BS Computer Engineering</option>
          <option value="BS Tourism Management">BS Tourism Management</option>
          <option value="BS Entrepreneurship">BS Entrepreneurship</option>
          <option value="BS Accounting Information System">BS Accounting Information System</option>
          <option value="BS Psychology">BS Psychology</option>
          <option value="BL Information Science">BL Information Science</option>
        </select>
      </div>

      <div class="form-group">
        <label for="Section">Year/Section</label>
        <input type="text" id="Section" name="Section" placeholder="Section" required />
      </div>

      <div class="form-group">
        <label for="Batch">Batch</label>
        <input type="text" id="Batch" name="Batch" placeholder="Batch" required />
      </div>

      <!-- Contact -->
      <div class="form-group">
        <label for="Contact">Contact</label>
        <input
          type="tel"
          id="Contact"
          name="Contact"
          placeholder="Contact"
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
        <input type="text" id="Address" name="Address" placeholder="Address" required />
      </div>

      <div class="form-group">
        <label for="Emailaddress">Email Address</label>
        <input type="email" id="Emailaddress" name="Emailaddress" placeholder="Email Address" required />
      </div>

      <div class="form-group">
        <label for="Occupation">Occupation</label>
        <input type="text" id="Occupation" name="Occupation" placeholder="Occupation" required />
      </div>

      <div class="form-group">
        @if(auth()->user()->role === 'Staff')
          <input type="button" value="Submit Record" onclick="handleStaffSubmission()" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
        @elseif(auth()->user()->role === 'SuperAdmin')
          <input type="button" value="Submit Record" onclick="handleSuperAdminSubmission()" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
        @else
          <input type="button" value="Submit Record" onclick="showConfirmModal()" style="background-color: #0000FF; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px;"/>
        @endif
      </div>
    </form>

    @if(auth()->user()->role === 'Staff')
      <x-staff-approval-modal />
    @endif

    @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')
    <div id="confirmModal" class="modal">
      <div class="modal-content">
        <h2 style="margin-top: 0;">Confirm Submission</h2>
        <p>Are you sure you want to submit this alumni record?</p>
        <div class="modal-buttons">
          <button class="modal-button cancel-button" onclick="hideConfirmModal()">Cancel</button>
          <button class="modal-button confirm-button" onclick="submitForm()">Confirm</button>
        </div>
      </div>
    </div>
    @endif

    <script>
      @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')
      const modal = document.getElementById('confirmModal');
      const form = document.getElementById('createForm');

      function showConfirmModal() {
        modal.style.display = "block";
      }

      function hideConfirmModal() {
        modal.style.display = "none";
      }

      function submitForm() {
        console.log('Submitting form...');
        form.submit();
      }

      function handleSuperAdminSubmission() {
        console.log('SuperAdmin submission triggered');
        showConfirmModal();
      }

      window.onclick = function(event) {
        if (event.target == modal) {
          hideConfirmModal();
        }
      }
      @endif

      @if(auth()->user()->role === 'Staff')
      function handleStaffSubmission() {
          const form = document.getElementById('createForm');
          
          const staffInput = document.createElement('input');
          staffInput.type = 'hidden';
          staffInput.name = 'staff_submission';
          staffInput.value = '1';
          form.appendChild(staffInput);
          
          form.submit();
      }
      @endif

      // Form validation
      document.getElementById('createForm').addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        
        // Basic validation
        const requiredFields = ['student_number', 'Fullname', 'Section', 'Contact', 'Emailaddress'];
        let isValid = true;
        
        requiredFields.forEach(fieldName => {
          const field = document.getElementById(fieldName);
          if (!field.value.trim()) {
            console.log(`Field ${fieldName} is empty`);
            isValid = false;
          }
        });
        
        if (!isValid) {
          e.preventDefault();
          alert('Please fill in all required fields');
          return false;
        }
        
        console.log('Form validation passed, submitting...');
      });

      // Auto-hide success message after 3 seconds
      setTimeout(() => {
        let successMessage = document.querySelector(".success-message");
        if (successMessage) {
          successMessage.style.transition = "opacity 0.5s";
          successMessage.style.opacity = "0";
          setTimeout(() => successMessage.remove(), 500);
        }
      }, 3000);
    </script>
  </div>
</body>
</html>

</x-admin-layout>
