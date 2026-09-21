<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponsibleController extends Controller
{
    public function index_responsible(){

        $auditory = DB::table('auditories')->get();

        $sortedAuditories = $auditory->sortBy('auditoryName');

        $user = DB::connection('mysql_platonus')->table('tutors')
            ->select(
                'TutorID',
                DB::raw("CONCAT(lastname, ' ', firstname) AS tutor_fullname"),
            )
            ->get();

        return view('backend.invertory.redactor.responsible', compact('sortedAuditories', 'user'));
    }

    public  function synchronize(Request $request){
        $request->validate([
            'auditoryID' => 'required|integer',
            'TutorID'    => 'required|integer',
        ]);

        $auditoryID = $request->auditoryID;
        $tutorID    = $request->TutorID;

        $tutor = DB::connection('mysql_platonus')
            ->table('tutors')
            ->select(DB::raw("CONCAT(lastname, ' ', firstname) AS fullname"))
            ->where('TutorID', $tutorID)
            ->first();

        $tutorName = $tutor?->fullname ?? 'неизвестному сотруднику';

        $count = DB::table('in_product_lists')
            ->where('auditoryID', $auditoryID)
            ->update([
                'TutorID'    => $tutorID,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            "Аудитория успешно передана ответственному: {$tutorName}. Обновлено записей: {$count}"
        );
    }
}
