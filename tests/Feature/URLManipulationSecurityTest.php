<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Alumni;
use Illuminate\Support\Facades\Hash;

class URLManipulationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
            'email_verified_at' => now(),
            'student_number' => 'ADM001',
        ]);

        $this->staffUser = User::create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role' => 'Staff',
            'email_verified_at' => now(),
            'student_number' => 'STF001',
        ]);

        $this->alumniUser1 = User::create([
            'name' => 'Alumni User 1',
            'email' => 'alumni1@test.com',
            'password' => Hash::make('password'),
            'role' => 'Alumni',
            'email_verified_at' => now(),
            'student_number' => 'STU001',
        ]);

        $this->alumniUser2 = User::create([
            'name' => 'Alumni User 2',
            'email' => 'alumni2@test.com',
            'password' => Hash::make('password'),
            'role' => 'Alumni',
            'email_verified_at' => now(),
            'student_number' => 'STU002',
        ]);

        // Create test alumni records
        $this->alumni1 = Alumni::create([
            'AlumniID' => 'ALU001',
            'StudentID' => 'STU001',
            'Fullname' => 'John Doe',
            'Emailaddress' => 'alumni1@test.com',
            'Contact' => '1234567890',
            'Address' => '123 Test St',
            'Course' => 'Computer Science',
            'Section' => 'A',
            'Batch' => '2023',
            'Age' => 25,
            'Gender' => 'Male',
            'Occupation' => 'Developer'
        ]);

        $this->alumni2 = Alumni::create([
            'AlumniID' => 'ALU002',
            'StudentID' => 'STU002',
            'Fullname' => 'Jane Smith',
            'Emailaddress' => 'alumni2@test.com',
            'Contact' => '0987654321',
            'Address' => '456 Test Ave',
            'Course' => 'Information Technology',
            'Section' => 'B',
            'Batch' => '2023',
            'Age' => 24,
            'Gender' => 'Female',
            'Occupation' => 'Analyst'
        ]);
    }

    /** @test */
    public function alumni_cannot_access_other_alumni_records_via_url()
    {
        // Alumni user 1 tries to access alumni user 2's record
        $response = $this->actingAs($this->alumniUser1)
            ->get(route('Alumni.edit', $this->alumni2->id));

        $response->assertStatus(302); // Should redirect
        $response->assertRedirect('/user/dashboard');
        $response->assertSessionHas('error', 'You can only access your own alumni record.');
    }

    /** @test */
    public function alumni_can_access_their_own_alumni_record()
    {
        // Alumni user 1 accesses their own record
        $response = $this->actingAs($this->alumniUser1)
            ->get(route('Alumni.edit', $this->alumni1->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_any_alumni_record()
    {
        // Admin accesses any alumni record
        $response = $this->actingAs($this->adminUser)
            ->get(route('Alumni.edit', $this->alumni1->id));

        $response->assertStatus(200);

        $response = $this->actingAs($this->adminUser)
            ->get(route('Alumni.edit', $this->alumni2->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function staff_can_access_any_alumni_record()
    {
        // Staff accesses any alumni record
        $response = $this->actingAs($this->staffUser)
            ->get(route('Alumni.edit', $this->alumni1->id));

        $response->assertStatus(200);

        $response = $this->actingAs($this->staffUser)
            ->get(route('Alumni.edit', $this->alumni2->id));

        $response->assertStatus(200);
    }

    /** @test */
    public function alumni_cannot_update_other_alumni_records()
    {
        // Alumni user 1 tries to update alumni user 2's record
        $response = $this->actingAs($this->alumniUser1)
            ->put(route('Alumni.update', $this->alumni2->id), [
                'StudentID' => 'STU002',
                'FirstName' => 'Modified',
                'LastName' => 'Name',
                'Emailaddress' => 'alumni2@test.com',
                'PhoneNumber' => '1111111111',
                'Address' => 'Modified Address',
                'Course' => 'Modified Course',
                'Section' => 'C',
                'Batch' => '2024',
                'Age' => 26,
                'Gender' => 'Male',
                'Occupation' => 'Modified Job'
            ]);

        $response->assertStatus(302); // Should redirect
        $response->assertRedirect('/user/dashboard');
        $response->assertSessionHas('error', 'You can only access your own alumni record.');
    }

    /** @test */
    public function alumni_cannot_delete_other_alumni_records()
    {
        // Alumni user 1 tries to delete alumni user 2's record
        $response = $this->actingAs($this->alumniUser1)
            ->delete(route('Alumni.destroy', $this->alumni2->id));

        $response->assertStatus(403); // Should return forbidden
    }

    /** @test */
    public function unauthenticated_users_cannot_access_alumni_routes()
    {
        // Test various alumni routes without authentication
        $response = $this->get(route('Alumni.index'));
        $response->assertRedirect('/login');

        $response = $this->get(route('Alumni.create'));
        $response->assertRedirect('/login');

        $response = $this->get(route('Alumni.edit', $this->alumni1->id));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function alumni_cannot_access_admin_only_routes()
    {
        // Alumni tries to access admin-only routes
        $response = $this->actingAs($this->alumniUser1)
            ->get('/dashboard');
        $response->assertStatus(403);

        $response = $this->actingAs($this->alumniUser1)
            ->get('/AccountManagement');
        $response->assertStatus(403);
    }

    /** @test */
    public function authorization_policies_are_enforced()
    {
        // Test that authorization policies prevent unauthorized access
        $response = $this->actingAs($this->alumniUser1)
            ->get(route('Alumni.index'));
        $response->assertStatus(403); // Alumni should not be able to view all alumni

        $response = $this->actingAs($this->alumniUser1)
            ->get(route('Alumni.create'));
        $response->assertStatus(403); // Alumni should not be able to create new alumni records
    }
}