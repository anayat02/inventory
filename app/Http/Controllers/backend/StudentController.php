<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function student(){

        $list = DB::connection('mysql_platonus')->table('students as s')
            ->join('studyforms as s1', function($join) {
                $join->on('s1.Id', '=', 's.StudyFormID')
                    ->where('s1.degreeID', '=', 1);
            })
            ->join('specializations as s2', 's2.id', '=', 's.specializationID')
            ->join('paymentforms as p', 'p.ID', '=', 's.PaymentFormID')
            ->join('groups as g', 'g.groupID', '=', 's.groupID')
            ->leftJoin('tutors as t', 't.TutorID', '=', 's.advisorID')
            ->join('profession_cafedra as pc', 'pc.id', '=', 's2.prof_caf_id')
            ->join('cafedras as c', 'c.cafedraID', '=', 'pc.cafedraID')
            ->select(
                'c.cafedraNameRU',
                DB::raw("CONCAT(s2.specializationCode, ' - ', s2.nameru) as Spec"),
                'g.name as group',
                's.StudentID',
                DB::raw("CONCAT(s.lastname, ' ', s.firstname, ' ', s.patronymic) as FioStudent"),
                's.iinplt',
                'p.NameRU as PaymentForm'
            )
            ->where('s.isStudent', 1)
            ->where('s.isinretire', 0)
            ->where('s.CourseNumber', 1)
            ->where('s.PaymentFormID', 2)
            ->orderBy('c.cafedraNameRU')
            ->orderBy('s2.nameru')
            ->orderBy('g.name')
            ->orderBy('FioStudent')
            ->get();

        $student_status = DB::connection('mysql')->table('student_status')
            ->select('StudentID', 'serialNumber', 'status', 'login')
            ->get();

        $merged = $list->map(function ($student) use ($student_status) {
            $status = $student_status->firstWhere('StudentID', $student->StudentID);
            $student->serialNumber = $status ? $status->serialNumber : null;
            $student->status = $status ? $status->status : null;
            $student->login = $status ? $status->login : null;
            return $student;
        });

        return view('backend.student.grant', compact('merged'));
    }

    public function confirm($id){
        Student::where('StudentID', $id)->update(['status' => '0']);

        return back()->with('success', 'Ноутбук успешно возвращен');

    }

    public function release(Request $request) {
        $insert = new Student;
        $insert->StudentID = $request->StudentID;
        $insert->serialNumber = 'Lenovo  IdeaPad 1 145IJL7; color: CLOUD_GREY';
        $insert->login = Auth::user()->Login;
        $insert->status = 1;
        $insert->save();

        return redirect()->route('act', $request->StudentID)->with('success', 'Ноутбук отпущен');
    }

    public function act($id)
    {
        $student = DB::connection('mysql_platonus')->table('students')
            ->select(
                'StudentID',
                DB::raw("CONCAT(students.lastname, ' ', students.firstname, ' ', students.patronymic) as full_name"),
                'students.iinplt'
            )
            ->where('StudentID', $id)
            ->first();

        $status = DB::connection('mysql')->table('student_status')
            ->where('StudentID', $id)
            ->first();

        if ($student) {
            $student->serialNumber = $status ? $status->serialNumber : null;
            $student->status = $status ? $status->status : null;
            $student->updated_at = $status ? \Carbon\Carbon::parse($status->updated_at) : now();
        }

        $pdf = \PDF::loadView('backend.student.act_app', compact('student'));
        return $pdf->stream('document.pdf');
    }


    public function laptop_list(){

        $list = DB::connection('mysql_platonus')->table('students as s')
            ->join('studyforms as s1', function($join) {
                $join->on('s1.Id', '=', 's.StudyFormID')
                    ->where('s1.degreeID', '=', 1);
            })
            ->join('specializations as s2', 's2.id', '=', 's.specializationID')
            ->join('paymentforms as p', 'p.ID', '=', 's.PaymentFormID')
            ->join('groups as g', 'g.groupID', '=', 's.groupID')
            ->leftJoin('tutors as t', 't.TutorID', '=', 's.advisorID')
            ->join('profession_cafedra as pc', 'pc.id', '=', 's2.prof_caf_id')
            ->join('cafedras as c', 'c.cafedraID', '=', 'pc.cafedraID')
            ->select(
                'c.cafedraNameRU',
                DB::raw("CONCAT(s2.specializationCode, ' - ', s2.nameru) as Spec"),
                'g.name as group',
                's.StudentID',
                DB::raw("CONCAT(s.lastname, ' ', s.firstname, ' ', s.patronymic) as FioStudent"),
                's.iinplt',
                'p.NameRU as PaymentForm'
            )
            ->where('s.isinretire', 0)
            ->where('s.PaymentFormID', 2)
            ->orderBy('c.cafedraNameRU')
            ->orderBy('s2.nameru')
            ->orderBy('g.name')
            ->orderBy('FioStudent')
            ->get();

        $student_status = DB::connection('mysql')->table('student_status')
            ->select('StudentID', 'serialNumber', 'status')
            ->get();

        $merged = $list->map(function ($student) use ($student_status) {
            $status = $student_status->firstWhere('StudentID', $student->StudentID);
            $student->serialNumber = $status ? $status->serialNumber : null;
            $student->status = $status ? $status->status : null;
            return $student;
        });

        return view('backend.student.laptop_list', compact('merged'));
    }
}
