<?php

defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class StudentController extends Controller
{
    public function index()
    {
        $this->call->view('student_home.php');
    }

    public function profile()
    {
        // Display student profile
        $student = [
            'Student ID' => 'MCC2024-00131',
            'Student Name' => 'Nicole Cruzat',
            'Course' => 'BS Information Technology',
            'Year Level' => '3rd year',
            'Section' => 'F3',
            'Email' =>'nicollay@gmail.com'
        ];
        $this->call->view('student_profile', $student);
    }
}