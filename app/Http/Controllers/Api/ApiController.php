<?php

/**
 * @OA\Info(
 *     title="API Documentation For Users",
 *     version="1.0.0",
 *     description="This API allows users to perform operations like login, registration, and more. It provides authentication and other resources.",
 *     @OA\Contact(
 *         email="support@example.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 */


namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    // AddEmployee
    public function addEmployee(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:employees',
            'phone' => 'required|numeric',
            'age' => 'required',
            'gender' => 'required',
        ]);

        Employee::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'age' => $request->age,
            'gender' => $request->gender,
            'created_at' => Carbon::now()
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Employee added successfully'
        ]);
    }
    // Retrieve employees
    public function ListEmployee()
    {
        $employees = Employee::all();

        if (!empty($employees)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Employees added successfully',
                'data' => $employees
            ]);
        }
        return response()->json([
            'status' => 'false',
            'message' => 'No Employees found',
        ]);
    }
    // SingleEmployee
    public function SingleEmployee($id)
    {
        $employee = Employee::find($id);

        if (!empty($employee)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Employee found successfully',
                'data' => $employee
            ]);
        }
        return response()->json([
            'status' => 'false',
            'message' => 'No Employee found',
        ]);
    }
    // UpdateEmployee
    public function UpdateEmployee(Request $request, $id)
    {
        $employeeId = Employee::find($id);

        if (!empty($employeeId)) {
            Employee::find($id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'age' => $request->age,
                'gender' => $request->gender,
                'updated_at' => Carbon::now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Employee profile was updated successfully',
            ]);
        }
        return response()->json([
            'status' => 'false',
            'message' => 'Employee not found',
        ]);
    }
    // DeleteEmployee
    public function DeleteEmployee($id){
        $employeeId = Employee::find($id);

        if (!empty($employeeId)) {
            Employee::find($id)->delete();
            return response()->json([
               'status' => 'success',
               'message' => 'Employee deleted successfully',
            ]);
        }
        return response()->json([
           'status' => 'false',
           'message' => 'Employee not found',
        ]);
    }
}
