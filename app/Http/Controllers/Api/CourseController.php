<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    // CourseEnroll
    public function CourseEnroll(Request $request)
    {
        // code to enroll a course goes here
        // Validate the data
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:3048',
        ]);

        // Upload the photo
        $image = $request->file('photo');
        $filename = date('YmdHi') . $image->getClientOriginalName();
        $image->move(public_path('upload/course_images/'), $filename);

        $save_url = 'upload/course_images/' . $filename;

        $userId = auth()->user()->id;

        // Create the user
        $course = Course::create([
            'user_id' => $userId,
            'title' => $request->title,
            'description' => $request->description,
            'photo' => $save_url
        ]);
        // Response
        return response()->json([
            'status' => true,
            'message' => 'Course created successfully',
            'course' => $course
        ]);
    }
    // ListCourse by id
    public function ListCourse() {
        $userId = auth()->user()->id;

        // Assuming there's a 'user_id' column in the 'courses' table
        $courses = Course::where('user_id', $userId)->get();

        // Check if courses are empty
        if ($courses->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Courses not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'courses' => $courses
        ]);
    }

    // Delete Course by id
public function DeleteCourse($id) {
    $userId = auth()->user()->id;

    // Find the course or fail if not found
    $course = Course::findOrFail($id);

    // Check if the course belongs to the authenticated user
    if ($course->user_id != $userId) {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized access to delete course'
        ], 403);
    }

    // Delete course image if it exists
    if (file_exists(public_path($course->photo))) {
        unlink(public_path($course->photo));
    }

    // Delete the course
    $course->delete();

    return response()->json([
        'status' => true,
        'message' => 'Course deleted successfully'
    ]);
}


}
