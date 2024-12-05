<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmployeeApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function it_can_create_a_employee()
    {
        $employeeData = [
            'name' => 'Segun',
            'email' => 'segun@example.com',
            'phone' => '0809885788',
            'age' => 36,
            'gender' => 'male',
        ];

        $response = $this->postJson('/api/add/employees', $employeeData);

        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                        'name' => 'Segun',
                        'email' => 'segun@example.com',
                        'phone' => '0809885788',
                        'age' => 36,
                        'gender' => 'male',
                     ]
                 ]);

        $this->assertDatabaseHas('employees', [
            'name' => 'Segun',
            'email' => 'segun@example.com',
            'phone' => '0809885788',
            'age' => 36,
            'gender' => 'male',
        ]);
    }
}
