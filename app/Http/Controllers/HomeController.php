<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\in_messages;
use App\Models\in_product_lists;
use App\Models\Transmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $messages = DB::table('in_messages AS m')
            ->where('m.TutorID', Auth::user()->TutorID)
            ->leftJoin('tutors AS t', 'm.TutorID', '=', 't.TutorID')
            ->leftJoin('in_product_name AS p', 'm.id_name', '=', 'p.id_name')
            ->select(
                'm.*',
                DB::raw("CONCAT(t.lastname, ' ', t.firstname) AS tutor_fullname"),
                'p.name_product'
            )
            ->orderBy('id_message', 'desc')
            ->get();

        $info = DB::connection('mysql_ais')->table('db_users')
            ->where('db_users.platonus_login', Auth::user()->Login)
            ->leftJoin('db_users_profiles', 'db_users.user_login', '=', 'db_users_profiles.user_login')
            ->leftJoin('db_jobs', 'db_users_profiles.id_job', '=', 'db_jobs.id_job')
            ->leftJoin('db_organigramme', 'db_users_profiles.division_id', '=', 'db_organigramme.division_id')
            ->select(
                'db_users_profiles.email',
                'db_users_profiles.fio_rus',
                'db_users_profiles.avatar_url',
                'db_users_profiles.mobile_phone',
                'db_jobs.name_job_rus',
                'db_organigramme.division_name_rus'
            )
            ->get();

        $hasPendingTransmissions = DB::table('transmissions')
            ->where('NewTutorID', Auth::user()->TutorID)
            ->whereIn('status', [1, 3])
            ->exists();

        // получаем список инвентаря текущего пользователя
        $inventory = in_product_lists::with(['characteristics.characteristic'])
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->where('in_product_lists.TutorID', Auth::user()->TutorID)
            ->where('in_product_lists.transmission_status', '!=', 0)
            ->select(
                'in_product_lists.id_product',
                'in_product_lists.inv_number',
                'in_product_lists.transmission_status',
                'in_product_name.name_product',
                'auditories.auditoryName'
            )
            ->orderBy('in_product_lists.id_product', 'desc')
            ->get();

        $tutorList = DB::connection('mysql_platonus')->table('tutors')->get();

        $todaySchedule = \App\Http\Controllers\backend\ClassroomCheckController::getTodayScheduleForTutor(Auth::user()->TutorID);

        return view('home', compact('messages', 'info', 'hasPendingTransmissions', 'inventory', 'tutorList', 'todaySchedule'));
    }

    public function showConfirmPage()
    {
        $currentTutorID = Auth::user()->TutorID;

        $groupedTransmissions  = Transmission::with(['characteristics.characteristic'])
            ->leftJoin('in_product_lists', 'transmissions.id_product', '=', 'in_product_lists.id_product')
            ->leftJoin('tutors as sender_tutor', 'transmissions.TutorID', '=', 'sender_tutor.TutorID')
            ->leftJoin('auditories', 'transmissions.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('in_product_name', 'in_product_name.id_name', '=', 'in_product_lists.id_name')
            ->select(
                'transmissions.id_product',
                'transmissions.TutorID',
                'in_product_name.name_product',
                'in_product_lists.inv_number',
                'in_product_lists.transmission_status',
                'auditories.auditoryName',
                'transmissions.created_at',
                DB::raw("CONCAT(sender_tutor.lastname, ' ', sender_tutor.firstname, ' ', sender_tutor.patronymic_ru) AS sender")
            )
            ->where('transmissions.NewTutorID', $currentTutorID)
            ->whereIn('transmissions.status', [1, 3])
            ->orderBy('transmissions.created_at', 'desc')
            ->get()
            ->groupBy('sender');

        if (request()->ajax()) {
            return view('backend.invertory.transmissions._confirm_partial', compact('groupedTransmissions'));
        }

        return view('backend.invertory.transmissions.confirm', compact('groupedTransmissions'));
    }

    public function accept(Request $request)
    {
        $currentTutorID = Auth::user()->TutorID;
        $idProducts = $request->input('id_products', []);
        $senderTutorID = $request->input('senderTutorID');

        if (empty($idProducts)) {
            return back()->with('error', 'Выберите хотя бы одно ОС для приёма.');
        }

        DB::beginTransaction();
        try {
            //Получаем все переданные предметы от этого отправителя текущему пользователю
            $allTransmitted = DB::table('transmissions')
                ->where('NewTutorID', $currentTutorID)
                ->where('TutorID', $senderTutorID)
                ->where('status', 1)
                ->pluck('id_product')
                ->toArray();

            //ПРИНЯТЫЕ ОС
            DB::table('transmissions')
                ->whereIn('id_product', $idProducts)
                ->where('NewTutorID', $currentTutorID)
                ->where('TutorID', $senderTutorID)
                ->update(['status' => 2, 'updated_at' => now()]);

            $acceptedProducts = []; // для PDF

            foreach ($idProducts as $oldId) {
                $old = DB::table('in_product_lists')->where('id_product', $oldId)->first();
                if (!$old) continue;

                //Деактивируем старую запись
                DB::table('in_product_lists')
                    ->where('id_product', $oldId)
                    ->update(['transmission_status' => 0, 'updated_at' => now()]);

                //Создаем новую запись
                $newId = DB::table('in_product_lists')->insertGetId([
                    'id_name'              => $old->id_name,
                    'buildingID'           => $old->buildingID,
                    'auditoryID'           => $old->auditoryID,
                    'TutorID'              => $currentTutorID,
                    'type'                 => $old->type,
                    'inv_number'           => $old->inv_number,
                    'redactor_id'          => $old->redactor_id,
                    'verification_status'  => $old->verification_status,
                    'current_status'       => $old->current_status,
                    'actual_inventory'     => $old->actual_inventory,
                    'write_off'            => $old->write_off,
                    'transmission_status'  => 1,
                    'updated_at'           => now(),
                ]);

                //Переносим характеристики
                DB::table('in_characteristics_for_products')
                    ->where('id_product', $oldId)
                    ->update(['id_product' => $newId]);

                $acceptedProducts[] = $newId;
            }

            //ОТКЛОНЁННЫЕ ОС
            $unselected = array_diff($allTransmitted, $idProducts);

            if (!empty($unselected)) {
                DB::table('transmissions')
                    ->whereIn('id_product', $unselected)
                    ->where('NewTutorID', $currentTutorID)
                    ->where('TutorID', $senderTutorID)
                    ->update(['status' => 3, 'updated_at' => now()]);

                DB::table('in_product_lists')
                    ->whereIn('id_product', $unselected)
                    ->update(['transmission_status' => 3, 'updated_at' => now()]);
            }

            //ГЕНЕРАЦИЯ PDF
            $pdfData = DB::table('transmissions')
                ->leftJoin('in_product_lists', 'transmissions.id_product', '=', 'in_product_lists.id_product')
                ->leftJoin('tutors as sender_tutor', 'transmissions.TutorID', '=', 'sender_tutor.TutorID')
                ->leftJoin('tutors as receiver_tutor', 'transmissions.NewTutorID', '=', 'receiver_tutor.TutorID')
                ->leftJoin('auditories', 'transmissions.auditoryID', '=', 'auditories.auditoryID')
                ->leftJoin('in_product_name', 'in_product_name.id_name', '=', 'in_product_lists.id_name')
                ->select(
                    'transmissions.id_trans',
                    'transmissions.id_product',
                    'in_product_name.name_product as product_name',
                    'in_product_lists.inv_number',
                    'auditories.auditoryName',
                    DB::raw("CONCAT(sender_tutor.lastname, ' ', sender_tutor.firstname, ' ', sender_tutor.patronymic_ru) AS sender"),
                    DB::raw("CONCAT(receiver_tutor.lastname, ' ', receiver_tutor.firstname, ' ', receiver_tutor.patronymic_ru) AS receiver"),
                    'transmissions.created_at'
                )
                ->whereIn('transmissions.id_product', $idProducts)
                ->where('transmissions.TutorID', $senderTutorID)
                ->where('transmissions.NewTutorID', $currentTutorID)
                ->where('transmissions.status', 2)
                ->orderBy('transmissions.id_trans', 'desc')
                ->get();

            foreach ($pdfData as $item) {
                $item->characteristics = DB::table('in_characteristics_for_products')
                    ->where('id_product', $item->id_product)
                    ->where('current_status', 0)
                    ->get();
            }

            $sender = $pdfData->first()->sender ?? '—';
            $receiver = $pdfData->first()->receiver ?? '—';

            DB::commit();

            //Генерация PDF
            $pdf = Pdf::loadView('backend.invertory.redactor.transmission_pdf', compact('pdfData', 'sender', 'receiver'))
                ->setPaper('a4', 'portrait');

            // Сохраняем в сессию для отображения
            session([
                'pdf_data' => $pdfData,
                'sender' => $sender,
                'receiver' => $receiver,
            ]);

            return redirect()->route('showPdfTransmission')
                ->with('success', 'Передача успешно обработана.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка: ' . $e->getMessage());
        }
    }

    public function showPdfTransmission()
    {
        $pdfData = session('pdf_data');
        $sender = session('sender');
        $receiver = session('receiver');

        if (!$pdfData) {
            return redirect()->route('home')->with('error', 'Нет данных для PDF.');
        }

        $pdf = Pdf::loadView('backend.invertory.redactor.transmission_pdf', compact('pdfData', 'sender', 'receiver'))
            ->setPaper('a4', 'portrait');

        // Очищаем сессию, чтобы при F5 не повторялось
        session()->forget(['pdf_data', 'sender', 'receiver']);

        return $pdf->stream('transmission_' . now()->format('Y_m_d_H_i_s') . '.pdf');
    }

}
