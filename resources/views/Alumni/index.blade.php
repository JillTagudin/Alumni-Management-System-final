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
        <title>Alumni Records</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 100%;
                margin: 30px auto;
                padding: 20px;
                background-color: #fff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 8px;
                overflow-x: auto;
            }

            h1 {
                text-align: center;
                font-size: 22px;
                color: #333;
            }

            .top-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }

            .create-btn {
                padding: 10px 15px;
                background-color: #0000FF;  /* Changed from #4CAF50 to blue */
                color: white;
                text-decoration: none;
                border-radius: 4px;
                font-size: 14px;
            }

            .create-btn:hover {
                background-color: #0000DD;  /* Changed hover color to darker blue */
            }

            .search-bar {
                padding: 8px;
                width: 300px;
                border: 1px solid #ccc;
                border-radius: 4px;
                font-size: 14px;
            }

            .table-container {
                width: 100%;
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13px;
                table-layout: fixed;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: center;
                word-wrap: break-word;
            }

            th {
                background-color: #0000FF;  /* Changed from #4CAF50 to blue */
                color: white;
                font-size: 14px;
            }

            .action-links a {
                margin: 0 5px;
                text-decoration: none;
                color: #007bff;
                font-size: 12px;
            }

            .action-links a:hover {
                text-decoration: underline;
            }

            /* Remove these styles */
            input[type="submit"] {
                background-color: #d9534f;
                color: white;
                border: none;
                padding: 5px 10px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
            }

            input[type="submit"]:hover {
                background-color: #c9302c;
            }

            @media screen and (max-width: 768px) {
                table {
                    font-size: 12px;
                }

                th, td {
                    padding: 6px;
                }

                .create-btn {
                    font-size: 12px;
                    padding: 8px 12px;
                }

                .search-bar {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Alumni Records</h1>

            <div class="top-bar">
                <a href="{{ route('Alumni.create') }}" class="create-btn">Add Record</a>
                <input type="text" id="searchInput" class="search-bar" placeholder="Search Alumni Records...">
            </div>

        



                                        <div>
                                @if(session()->has('success'))
                                <div class="success-message">
                                    {{ session()->get('success') }}
                                </div>
                                @endif
                            </div>

                            <style>
                                .success-message {
                                    background-color: #d4edda;
                                    color: #155724;
                                    padding: 10px;
                                    margin-bottom: 20px;
                                    border: 1px solid #c3e6cb;
                                    border-radius: 4px;
                                    text-align: center;
                                    font-weight: bold;
                                }
                            </style>

                            <script>
                                
                                setTimeout(() => {
                                    let successMessage = document.querySelector(".success-message");
                                    if (successMessage) {
                                        successMessage.style.transition = "opacity 0.5s";
                                        successMessage.style.opacity = "0";
                                        setTimeout(() => successMessage.remove(), 500);
                                    }
                                }, 3000);
                            </script>







            
            <div class="table-container">
                <table id="alumniTable">
                    <tr>
                        <th>Student ID</th>
                        <th>Fullname</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Batch</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Email Address</th>
                        <th>Occupation</th>
                        <th>Edit</th>
                        <!-- Removed Delete column header -->
                    </tr>
                    @foreach ($Alumni as $alumnis)
                    <tr>
                        <td>{{ $alumnis->StudentID }}</td>
                        <td>{{ $alumnis->Fullname }}</td>
                        <td>{{ $alumnis->Age }}</td>
                        <td>{{ $alumnis->Gender }}</td>
                        <td>{{ $alumnis->Course }}</td>
                        <td>{{ $alumnis->Section }}</td>
                        <td>{{ $alumnis->Batch }}</td>
                        <td>{{ $alumnis->Contact }}</td>
                        <td>{{ $alumnis->Address }}</td> 
                        <td>{{ $alumnis->Emailaddress }}</td>
                        <td>{{ $alumnis->Occupation }}</td>
                        <td class="action-links">
                            <a href="{{ route('Alumni.edit', ['Alumni' => $alumnis]) }}">Edit</a>
                        </td>
                        <!-- Removed Delete button cell -->
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>

        <script>
           document.getElementById("searchInput").addEventListener("keyup", function () {
        let filter = this.value.toLowerCase().trim().split(/\s+/); 
        let rows = document.querySelectorAll("#alumniTable tr:not(:first-child)");

        rows.forEach(row => {
            let columns = Array.from(row.getElementsByTagName("td")); 
            let rowText = columns.map(td => td.textContent.toLowerCase()).join(" "); 

            
            let match = filter.every(keyword => rowText.includes(keyword));

            row.style.display = match ? "" : "none"; 
        });
    });
        </script>

        
    </body>
    </html>
</x-admin-layout>
