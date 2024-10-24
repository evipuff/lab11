<?php

namespace App\Controllers;

use App\Models\Course;
use App\Controllers\BaseController;


class CourseController extends BaseController
{
    public function list()
    {
        $obj = new Course();
        $courses = $obj->all();

        $template = 'courses';
        $data = [
            'items' => $courses
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function viewCourse($course_code)
    {
        $courseObj = new Course();
        $course = $courseObj->find($course_code);
        $enrolees = $courseObj->getEnrolees($course_code);

        $template = 'single-course';
        $data = [
            'course' => $course,
            'enrolees' => $enrolees
        ];

        $output = $this->render($template, $data);

        return $output;
    }

    public function exportCourse($course_code) {
        $course = Course::where('course_code', $course_code)->first();
        if (!$course) {
            // handle course not found
            die("Course not found.");
        }

        // Fetch the list of enrollees for the course
        $enrollees = Enrollment::where('course_code', $course_code)->get();

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);

        // Course Information
        $pdf->Cell(190, 10, "Course Information", 0, 1, 'C');
        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 10, 'Course Code: ', 0, 0);
        $pdf->Cell(50, 10, $course->course_code, 0, 1);
        $pdf->Cell(50, 10, 'Course Name: ', 0, 0);
        $pdf->Cell(50, 10, $course->course_name, 0, 1);
        $pdf->Cell(50, 10, 'Description: ', 0, 0);
        $pdf->MultiCell(130, 10, $course->description);
        $pdf->Cell(50, 10, 'Credits: ', 0, 0);
        $pdf->Cell(50, 10, $course->credits, 0, 1);
        $pdf->Ln(10);

        // List of Enrollees
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(190, 10, "List of Enrollees", 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, 'Student Code', 1);
        $pdf->Cell(80, 10, 'Student Name', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 12);
        foreach ($enrollees as $enrollee) {
            $pdf->Cell(30, 10, $enrollee->student_code, 1);
            $pdf->Cell(80, 10, $enrollee->student_name, 1);
            $pdf->Ln();
        }

        $pdf->Output('D', 'Course_'.$course_code.'_Details.pdf');
    }
    
}
