<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\Issuance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function issuance(){
        $tutors = DB::connection('mysql_platonus')->table('tutors')
            ->select(
                'TutorID',
                DB::raw("CONCAT(lastname, ' ', firstname) AS full_name")
            )
            ->orderBy('desc')
            ->get();

        $laptop_list = Issuance::all();

        return view('backend.teacher.issuance', compact('tutors', 'laptop_list'));
    }

    public function laptop_for_teachers(Request $request)
    {
        $request->validate([
            'tutor_name'    => 'required|string|max:255',
            'model'         => 'required|string|max:255',
            'serial_number' => 'required|string|max:255',
            'file'          => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $fileName = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/act_pp', $fileName);
        }

        Issuance::create([
            'full_name'     => trim(preg_replace('/\s+/u', ' ', $request->tutor_name)),
            'model'         => $request->model,
            'serial_number' => $request->serial_number,
            'file_act'      => $fileName,
        ]);

        return redirect()->back()->with('success', 'Запись успешно добавлена.');
    }
}
