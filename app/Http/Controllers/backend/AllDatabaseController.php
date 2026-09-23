<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\in_product_lists;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AllDatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function attachTutorNames($items)
    {
        $tutorIds = $items->pluck('TutorID')
            ->merge($items->pluck('redactor_id'))
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $tutors = DB::connection('mysql_platonus')
            ->table('tutors')
            ->whereIn('TutorID', $tutorIds)
            ->select('TutorID', 'lastname', 'firstname')
            ->get()
            ->keyBy('TutorID');

        $items->transform(function ($item) use ($tutors) {
            $tutor = $tutors->get($item->TutorID);
            $redactor = $tutors->get($item->redactor_id);

            $item->tutor_fullname = $tutor
                ? trim($tutor->lastname . ' ' . $tutor->firstname)
                : '';

            $item->redactor_fullname = $redactor
                ? trim($redactor->lastname . ' ' . $redactor->firstname)
                : '';

            return $item;
        });

        return $items;
    }

    public function all(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

        $query = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('notes', 'in_product_lists.id_product', '=', 'notes.id_product')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                'notes.note'
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->where('in_product_lists.write_off', 1);

        if (!$isAdmin) {
            $query->where('in_product_lists.TutorID', $user->TutorID);
        }

        if ($request->filled('buildingID')) {
            $query->where('in_product_lists.buildingID', $request->buildingID);
        }
        if ($request->filled('auditoryID')) {
            $query->where('in_product_lists.auditoryID', $request->auditoryID);
        }
        if ($request->filled('id_name')) {
            $query->where('in_product_lists.id_name', $request->id_name);
        }
        if ($request->filled('type')) {
            $query->where('in_product_lists.type', $request->type);
        }
        if ($request->filled('verification_status')) {
            $query->where('in_product_lists.verification_status', $request->verification_status);
        }
        if ($isAdmin && $request->filled('tutorID')) {
            $query->where('in_product_lists.TutorID', $request->tutorID);
        }

        $items = $query->orderBy('id_product', 'desc')->get();
        $items = $this->attachTutorNames($items);

        // Списки для фильтров
        $buildings = DB::connection('mysql_platonus')->table('buildings')->get();
        $auditories = DB::table('auditories')->orderByRaw('CAST(auditoryName AS UNSIGNED) asc')->get();
        $productNames = DB::table('in_product_name')->get();
        $tutorsList = $isAdmin ? DB::connection('mysql_platonus')->table('tutors')->get() : collect();

        return view('backend.invertory.create_invertory.all_db', compact('items', 'buildings', 'auditories', 'productNames'))
            ->with('tutors', $tutorsList);
    }


    public function filter(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

        $query = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('notes', 'in_product_lists.id_product', '=', 'notes.id_product')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                'notes.note'
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->where('in_product_lists.write_off', 1)
            ->whereNotNull('in_product_lists.TutorID')
            ->whereNotNull('in_product_lists.type')
            ->whereNotNull('in_product_lists.auditoryID');

        if (!$isAdmin) {
            $query->where('in_product_lists.TutorID', $user->TutorID);
        }

        $items = $query->orderBy('id_product', 'desc')->get();
        $items = $this->attachTutorNames($items);

        $buildings = DB::connection('mysql_platonus')->table('buildings')->get();
        $auditories = DB::table('auditories')->orderByRaw('CAST(auditoryName AS UNSIGNED) asc')->get();
        $productNames = DB::table('in_product_name')->get();
        $tutors = $isAdmin ? DB::connection('mysql_platonus')->table('tutors')->get() : collect();

        return view('backend.invertory.create_invertory.all_db', compact('items', 'buildings', 'auditories', 'productNames', 'tutors'));
    }

    public function noSorted()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

        $query = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('notes', 'in_product_lists.id_product', '=', 'notes.id_product')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                'notes.note'
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->where('in_product_lists.write_off', 1)
            ->where(function($q) {
                $q->whereNull('in_product_lists.type')
                    ->orWhereNull('in_product_lists.auditoryID')
                    ->orWhereNull('in_product_lists.inv_number');
            });

        if (!$isAdmin) {
            $query->where('in_product_lists.TutorID', $user->TutorID);
        }

        $items = $query->orderBy('id_product', 'desc')->get();
        $items = $this->attachTutorNames($items);

        $buildings = DB::connection('mysql_platonus')->table('buildings')->get();
        $auditories = DB::table('auditories')->orderByRaw('CAST(auditoryName AS UNSIGNED) asc')->get();
        $productNames = DB::table('in_product_name')->get();
        $tutors = $isAdmin ? DB::connection('mysql_platonus')->table('tutors')->get() : collect();

        return view('backend.invertory.create_invertory.all_db', compact('items', 'buildings', 'auditories', 'productNames', 'tutors'));
    }

    public function noNumber()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

        $query = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('notes', 'in_product_lists.id_product', '=', 'notes.id_product')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                'notes.note'
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->where('in_product_lists.write_off', 1)
            ->whereNull('in_product_lists.inv_number');

        if (!$isAdmin) {
            $query->where('in_product_lists.TutorID', $user->TutorID);
        }

        $items = $query->orderBy('id_product', 'desc')->get();
        $items = $this->attachTutorNames($items);

        $buildings = DB::connection('mysql_platonus')->table('buildings')->get();
        $auditories = DB::table('auditories')->orderByRaw('CAST(auditoryName AS UNSIGNED) asc')->get();
        $productNames = DB::table('in_product_name')->get();
        $tutors = $isAdmin ? DB::connection('mysql_platonus')->table('tutors')->get() : collect();

        return view('backend.invertory.create_invertory.all_db', compact('items', 'buildings', 'auditories', 'productNames', 'tutors'));
    }

    public function export()
    {
        $user = Auth::user();
        $isAdmin = $user && $user->hasAnyRole(['admin', 'super-admin']);

        $query = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('tutors AS tutor', 'in_product_lists.TutorID', '=', 'tutor.TutorID')
            ->leftJoin('tutors AS redactor', 'in_product_lists.redactor_id', '=', 'redactor.TutorID')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                DB::raw("CONCAT(tutor.lastname, ' ', tutor.firstname) AS tutor_fullname"),
                DB::raw("CONCAT(redactor.lastname, ' ', redactor.firstname) AS redactor_fullname")
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->where('in_product_lists.write_off', 1);

        if (!$isAdmin) {
            $query->where('in_product_lists.TutorID', $user->TutorID);
        }

        $items = $query->orderBy('id_product', 'desc')->get();

        return view('backend.invertory.create_invertory.export_db', ['items' => $items]);
    }
}
