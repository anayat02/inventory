<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\ClassroomCheck;
use App\Models\ClassroomCheckItem;
use App\Models\in_product_lists;
use App\Models\User;
use App\Mail\ClassroomDiscrepancyMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ClassroomCheckController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Получить сгруппированные блоки расписания преподавателя на указанную дату.
     */
    public static function getTodayScheduleForTutor($tutorId, $dateStr = null)
    {
        $date = $dateStr ? Carbon::parse($dateStr) : Carbon::today();
        
        // В Platonus 1 = Понедельник ... 7 = Воскресенье
        $dayOfWeek = $date->dayOfWeekIso; 
        
        // Примерное определение года и семестра
        $currentYear = $date->year;
        // Если месяц < 8, то идет весенний семестр (2), иначе осенний (1)
        $term = ($date->month >= 8 || $date->month == 1) ? 1 : 2;

        // Попробуем получить номер недели от Platonus или вычислить поумолчанию (например, 3 или динамически)
        $weekNumber = 3; 

        $sql = "
            WITH base AS (
                SELECT DISTINCT
                    c.cafedraNameRU,
                    t1.TutorID,
                    CONCAT(t1.lastname, ' ', t1.firstname, ' ', t1.patronymic) AS TutorFIO,
                    b.buildingName,
                    a.auditoryID,
                    a.auditoryName,
                    a1.typeID,
                    a1.name AS auditoryTypeName,
                    lh.number,
                    lh.start,
                    lh.finish
                FROM timetable t

                JOIN lesson_hours lh
                    ON lh.number = t.number
                    AND lh.lessonSettingID = 10
                JOIN timetable_weeks tw 
                    ON tw.lessonID = t.lessonID
                    AND tw.weekNumber = :weekNumber

                JOIN auditories a
                    ON a.auditoryID = t.auditoryID

                JOIN buildings b
                    ON b.buildingID = a.buildingID

                JOIN studygroups s
                    ON s.StudyGroupID = t.studyGroupID

                JOIN tutorsubject t2
                    ON t2.TutorSubjectID = s.tutorSubjectID

                JOIN tutors t1
                    ON t1.TutorID = t2.TutorID

                JOIN tutor_cafedra tc
                    ON tc.tutorID = t1.TutorID
                    AND tc.deleted = 0

                JOIN cafedras c
                    ON c.cafedraID = tc.cafedraid

                JOIN auditorytypes a1
                    ON a1.typeID = a.auditoryType

                WHERE t.week_day = :week_day
                  AND t1.TutorID = :tutor_id
                  AND s.year = :year
                  AND s.Term = :term
                  AND (
                      a1.typeID = 7
                      OR a.auditoryID IN (73, 98)
                  )
            ),

            ordered AS (
                SELECT
                    base.*,
                    LAG(number) OVER (
                        PARTITION BY TutorID, auditoryID
                        ORDER BY number
                    ) AS prev_number
                FROM base
            ),

            marked AS (
                SELECT
                    ordered.*,
                    CASE
                        WHEN prev_number IS NULL THEN 1
                        WHEN number = prev_number + 1 THEN 0
                        ELSE 1
                    END AS new_group
                FROM ordered
            ),

            grouped AS (
                SELECT
                    marked.*,
                    SUM(new_group) OVER (
                        PARTITION BY TutorID, auditoryID
                        ORDER BY number
                        ROWS UNBOUNDED PRECEDING
                    ) AS group_id
                FROM marked
            )

            SELECT
                cafedraNameRU,
                TutorFIO,
                buildingName,
                auditoryID,
                auditoryName,
                typeID,
                auditoryTypeName,
                MIN(start) AS start,
                MAX(finish) AS finish,
                COUNT(*) AS lesson_count
            FROM grouped
            GROUP BY
                TutorID,
                TutorFIO,
                cafedraNameRU,
                buildingName,
                auditoryID,
                auditoryName,
                typeID,
                auditoryTypeName,
                group_id
            ORDER BY
                TIME(MIN(start))
        ";

        try {
            $schedule = DB::connection('mysql_platonus')->select($sql, [
                'weekNumber' => $weekNumber,
                'week_day' => $dayOfWeek,
                'tutor_id' => $tutorId,
                'year' => $currentYear,
                'term' => $term,
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка получения расписания из Platonus: ' . $e->getMessage());
            $schedule = [];
        }

        $todayStr = $date->format('Y-m-d');
        $now = Carbon::now();
        $isAdmin = Auth::check() && Auth::user()->hasAnyRole(['admin', 'super-admin']);

        foreach ($schedule as $slot) {
            $startTime = Carbon::parse($todayStr . ' ' . $slot->start);
            $finishTime = Carbon::parse($todayStr . ' ' . $slot->finish);
            
            $entranceUnlockTime = (clone $startTime)->subMinutes(10);

            $entranceCheck = ClassroomCheck::where('tutor_id', $tutorId)
                ->where('auditory_id', $slot->auditoryID)
                ->where('check_date', $todayStr)
                ->where('lesson_start', $slot->start)
                ->where('lesson_finish', $slot->finish)
                ->where('check_type', 'entrance')
                ->first();

            $exitCheck = ClassroomCheck::where('tutor_id', $tutorId)
                ->where('auditory_id', $slot->auditoryID)
                ->where('check_date', $todayStr)
                ->where('lesson_start', $slot->start)
                ->where('lesson_finish', $slot->finish)
                ->where('check_type', 'exit')
                ->first();

            $slot->entrance_check = $entranceCheck;
            $slot->exit_check = $exitCheck;

            if ($entranceCheck) {
                $slot->entrance_status = 'completed';
                $slot->can_check_entrance = false;
            } elseif ($isAdmin || $now->gte($entranceUnlockTime)) {
                $slot->entrance_status = 'available';
                $slot->can_check_entrance = true;
            } else {
                $slot->entrance_status = 'locked';
                $slot->can_check_entrance = false;
                $slot->entrance_unlock_hint = 'Откроется в ' . $entranceUnlockTime->format('H:i');
            }

            if ($exitCheck) {
                $slot->exit_status = 'completed';
                $slot->can_check_exit = false;
            } elseif ($entranceCheck && ($isAdmin || $now->gte($startTime))) {
                $slot->exit_status = 'available';
                $slot->can_check_exit = true;
            } else {
                $slot->exit_status = 'locked';
                $slot->can_check_exit = false;
                $slot->exit_unlock_hint = $entranceCheck ? 'Доступно во время пар' : 'Сначала выполните вход';
            }
        }

        return $schedule;
    }

    /**
     * Отобразить форму проверки инвентаря в аудитории.
     */
    public function showCheckForm(Request $request, $auditoryID, $checkType)
    {
        $start = $request->query('start');
        $finish = $request->query('finish');

        $auditory = DB::connection('mysql_platonus')->table('auditories AS a')
            ->leftJoin('buildings AS b', 'a.buildingID', '=', 'b.buildingID')
            ->select('a.auditoryID', 'a.auditoryName', 'b.buildingName')
            ->where('a.auditoryID', $auditoryID)
            ->first();

        if (!$auditory) {
            return redirect()->route('home')->with('error', 'Аудитория не найдена');
        }

        $items = in_product_lists::with([
            'characteristics' => function ($query) {
                $query->with('characteristic')
                    ->where('current_status', '0');
            }
        ])
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
            ->where('in_product_lists.auditoryID', $auditoryID)
            ->orderBy('id_product', 'desc')
            ->get();

        $groupedSummary = $items->groupBy(function($item) {
            return $item->name_product ?: 'Прочее оборудование';
        })->map(function($group, $name) {
            $isSystemBlock = str_contains(mb_strtolower($name), 'системн') 
                          || str_contains(mb_strtolower($name), 'компьютер') 
                          || str_contains(mb_strtolower($name), 'пк') 
                          || str_contains(mb_strtolower($name), 'моноблок');
            return [
                'name' => $name,
                'db_count' => count($group),
                'is_system_block' => $isSystemBlock,
            ];
        });

        $systemBlockCount = 0;
        foreach ($groupedSummary as $cat) {
            if ($cat['is_system_block']) {
                $systemBlockCount += $cat['db_count'];
            }
        }

        return view('backend.classroom_checks.check_form', compact('auditory', 'groupedSummary', 'systemBlockCount', 'checkType', 'start', 'finish'));
    }

    /**
     * Сохранение формы проверки инвентаря преподавателем.
     */
    public function storeCheck(Request $request)
    {
        $request->validate([
            'auditory_id'    => 'required|integer',
            'check_type'     => 'required|in:entrance,exit',
            'lesson_start'   => 'required',
            'lesson_finish'  => 'required',
            'comment'        => 'nullable|string',
            'keyboard_count' => 'nullable|integer|min:0',
            'mouse_count'    => 'nullable|integer|min:0',
            'categories'     => 'required|array',
        ]);

        $user = Auth::user();
        $tutorId = $user->TutorID;
        $auditoryId = $request->auditory_id;
        $checkType = $request->check_type;
        $lessonStart = $request->lesson_start;
        $lessonFinish = $request->lesson_finish;
        $todayStr = date('Y-m-d');

        DB::beginTransaction();
        try {
            $check = ClassroomCheck::create([
                'tutor_id'       => $tutorId,
                'auditory_id'    => $auditoryId,
                'check_date'     => $todayStr,
                'lesson_start'   => $lessonStart,
                'lesson_finish'  => $lessonFinish,
                'check_type'     => $checkType,
                'status'         => 'ok',
                'comment'        => $request->comment,
                'keyboard_count' => $request->keyboard_count,
                'mouse_count'    => $request->mouse_count,
            ]);

            $hasDiscrepancy = false;
            $discrepancies = [];
            $factSystemBlocks = 0;

            foreach ($request->categories as $index => $catData) {
                $productName = $catData['name'] ?? ('Категория #' . $index);
                $dbCount = (int)($catData['db_count'] ?? 0);
                $factCount = (int)($catData['fact_count'] ?? 0);
                $note = $catData['note'] ?? null;

                $pNameLower = mb_strtolower($productName);
                $isSysBlock = str_contains($pNameLower, 'системн') || str_contains($pNameLower, 'компьютер') || str_contains($pNameLower, 'пк') || str_contains($pNameLower, 'моноблок');
                if ($isSysBlock) {
                    $factSystemBlocks += $factCount;
                }

                $isDiscrepant = ($factCount !== $dbCount);
                if ($isDiscrepant) {
                    $hasDiscrepancy = true;
                    $discrepancies[] = [
                        'id_product'   => 0,
                        'product_name' => $productName,
                        'inv_number'   => '—',
                        'is_present'   => ($factCount >= $dbCount),
                        'condition'    => ($factCount < $dbCount ? 'missing' : 'damaged'),
                        'note'         => "По базе: {$dbCount} шт. | По факту: {$factCount} шт." . ($note ? " ({$note})" : ""),
                    ];
                }

                ClassroomCheckItem::create([
                    'check_id'     => $check->id,
                    'id_product'   => 0,
                    'product_name' => $productName,
                    'db_count'     => $dbCount,
                    'fact_count'   => $factCount,
                    'is_present'   => ($factCount >= $dbCount),
                    'condition'    => ($factCount < $dbCount ? 'missing' : 'ok'),
                    'note'         => $note,
                ]);
            }

            // Сверка периферии с количеством рабочих мест (системных блоков по базе)
            $dbSystemBlocks = 0;
            if ($request->has('categories') && is_array($request->categories)) {
                foreach ($request->categories as $catData) {
                    $pNameLower = mb_strtolower($catData['name'] ?? '');
                    if (str_contains($pNameLower, 'системн') || str_contains($pNameLower, 'компьютер') || str_contains($pNameLower, 'пк') || str_contains($pNameLower, 'моноблок')) {
                        $dbSystemBlocks += (int)($catData['db_count'] ?? 0);
                    }
                }
            }

            if ($dbSystemBlocks > 0) {
                if ($request->filled('keyboard_count') && (int)$request->keyboard_count !== $dbSystemBlocks) {
                    $hasDiscrepancy = true;
                    $discrepancies[] = [
                        'id_product'   => 0,
                        'product_name' => 'Клавиатуры (Периферия)',
                        'inv_number'   => '—',
                        'is_present'   => false,
                        'condition'    => 'damaged',
                        'note'         => "Количество клавиатур ({$request->keyboard_count} шт.) не совпадает с числом рабочих мест по базе ({$dbSystemBlocks} шт.)",
                    ];
                }

                if ($request->filled('mouse_count') && (int)$request->mouse_count !== $dbSystemBlocks) {
                    $hasDiscrepancy = true;
                    $discrepancies[] = [
                        'id_product'   => 0,
                        'product_name' => 'Мыши (Периферия)',
                        'inv_number'   => '—',
                        'is_present'   => false,
                        'condition'    => 'damaged',
                        'note'         => "Количество мышек ({$request->mouse_count} шт.) не совпадает с числом рабочих мест по базе ({$dbSystemBlocks} шт.)",
                    ];
                }
            }

            if ($hasDiscrepancy) {
                $check->update(['status' => 'discrepancy']);

                // Получаем название аудитории
                $auditory = DB::connection('mysql_platonus')->table('auditories')
                    ->where('auditoryID', $auditoryId)->first();
                $auditoryName = $auditory ? $auditory->auditoryName : ('Аудитория #' . $auditoryId);
                $tutorName = $user->name ?? $user->Login;

                // Отправляем Email администраторам
                $adminEmails = User::role('admin')->pluck('email')->filter()->toArray();
                if (empty($adminEmails)) {
                    $adminEmails = [config('mail.from.address', 'admin@kazetu.kz')];
                }

                try {
                    Mail::to($adminEmails)->send(new ClassroomDiscrepancyMail($check, $tutorName, $auditoryName, $discrepancies));
                } catch (\Exception $mailEx) {
                    Log::error('Ошибка отправки e-mail об аномалии инвентаря: ' . $mailEx->getMessage());
                }
            }

            DB::commit();

            $msg = ($checkType == 'entrance') ? 'Проверка при входе в аудиторию успешно сохранена.' : 'Проверка при выходе из аудитории успешно сохранена.';
            if ($hasDiscrepancy) {
                $msg .= ' Выявленные замечания зафиксированы и отправлены администраторам.';
            }

            return redirect()->route('home')->with('success', $msg);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ошибка сохранения проверки аудитории: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ошибка при сохранении: ' . $e->getMessage());
        }
    }

    /**
     * Административный журнал всех проверок и аномалий.
     */
    public function adminHistory(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['admin', 'super-admin'])) {
            abort(403, 'Доступ разрешен только администраторам.');
        }

        $query = ClassroomCheck::with(['items.product'])
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->where('check_date', $request->date);
        }

        if ($request->filled('tutor_id')) {
            $query->where('tutor_id', $request->tutor_id);
        }

        if ($request->filled('auditory_id')) {
            $query->where('auditory_id', $request->auditory_id);
        }

        if ($request->filled('check_type')) {
            $query->where('check_type', $request->check_type);
        }

        $checks = $query->get();

        // Подгрузим ФИО преподавателей и имена аудиторий
        $tutorIds = $checks->pluck('tutor_id')->unique()->filter()->toArray();
        $auditoryIds = $checks->pluck('auditory_id')->unique()->filter()->toArray();

        $tutorsMap = DB::connection('mysql_platonus')->table('tutors')
            ->whereIn('TutorID', $tutorIds)
            ->select('TutorID', 'lastname', 'firstname', 'patronymic')
            ->get()->keyBy('TutorID');

        $auditoriesMap = DB::connection('mysql_platonus')->table('auditories AS a')
            ->leftJoin('buildings AS b', 'a.buildingID', '=', 'b.buildingID')
            ->whereIn('a.auditoryID', $auditoryIds)
            ->select('a.auditoryID', 'a.auditoryName', 'b.buildingName')
            ->get()->keyBy('auditoryID');

        foreach ($checks as $check) {
            $t = $tutorsMap->get($check->tutor_id);
            $a = $auditoriesMap->get($check->auditory_id);

            $check->tutor_fullname = $t ? trim($t->lastname . ' ' . $t->firstname . ' ' . $t->patronymic) : 'ID: ' . $check->tutor_id;
            $check->auditory_name = $a ? ($a->auditoryName . ' (' . $a->buildingName . ')') : 'ID: ' . $check->auditory_id;
            $check->created_at_formatted = $check->created_at ? $check->created_at->format('d.m.Y H:i') : $check->check_date;
        }

        // Списки для фильтров в шапке
        $allTutors = DB::connection('mysql_platonus')->table('tutors')->select('TutorID', 'lastname', 'firstname')->get();
        $allAuditories = DB::connection('mysql_platonus')->table('auditories AS a')
            ->leftJoin('buildings AS b', 'a.buildingID', '=', 'b.buildingID')
            ->select('a.auditoryID', 'a.auditoryName', 'b.buildingName')
            ->get();

        return view('backend.classroom_checks.admin_history', compact('checks', 'allTutors', 'allAuditories'));
    }
}