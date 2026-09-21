<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\in_characteristics_for_product;
use App\Models\in_list_characteristics;
use App\Models\in_product_list_characteristics;
use App\Models\in_product_lists;
use App\Models\in_messages;
use App\Models\Move;
use App\Models\Note;
use App\Models\Transmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Auditory;
use App\Models\Building;
use Mockery\Matcher\Not;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class MoveAndChangeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function move()
    {

        $building = DB::table('buildings')->get();
        $auditories = DB::table('auditories')->get();

        $sortedAuditories = $auditories->sortBy('auditoryName');

        return view('backend.invertory.redactor.move', ['building' => $building, 'auditories' =>$sortedAuditories]);
    }

    public function editAll ($id_product)
    {
        $edit = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('tutors', 'in_product_lists.TutorID', '=', 'tutors.TutorID')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                DB::raw("CONCAT(tutors.lastname, ' ', tutors.firstname) AS tutor_fullname")
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->find($id_product);

        $building = DB::table('buildings')->get();

        $auditories = DB::table('auditories')->get();

        $sortedAuditories = $auditories->sortBy('auditoryName');
        return view('backend.invertory.redactor.move', compact('edit', 'building', 'sortedAuditories'));
    }

    public function getForm($id_name)
    {
        $productListCharacteristics = in_product_list_characteristics::where('id_name', $id_name)->get();

        $forms = $productListCharacteristics->map(function ($item) {
            $listCharacteristic = in_list_characteristics::where('id_characteristic', $item->id_characteristic)->first();
            return [
                'id_characteristic' => $item->id_characteristic,
                'input_characteristic' => $listCharacteristic ? $listCharacteristic->input_characteristic : ''
            ];
        });

        return response()->json(['forms' => $forms]);
    }

    public function change()
    {
        return view('backend.invertory.redactor.change');
    }

    public function updateAll(Request $request, $id_product)
    {
        // Сначала проверим, есть ли у товара уже активные (current_status = 0) характеристики
        $hasActive = in_characteristics_for_product::where('id_product',$id_product)
            ->where('current_status',0)
            ->exists();

        // Базовые правила
        $rules = [
            'buildingID'  => 'required',
            'auditoryID'  => 'required',
            'TutorID'     => 'required',
        ];

        // Если у товара ещё нет активных характеристик — требуем массивы names и id_characteristic
        if (! $hasActive) {
            $rules['names']              = 'required|array|min:1';
            $rules['id_characteristic']  = 'required|array|min:1';
        }

        // Валидируем
        $validated = $request->validate($rules);

        $edit = DB::table('in_product_lists')->where('id_product', $id_product)->first();
        if (! $edit) {
            return back()->with('error','Запись не найдена');
        }

        DB::beginTransaction();
        try {
            $data = [];

            // обновляем buildingID только если оно не было заполнено ранее
            if (empty($edit->buildingID) && $request->filled('buildingID')) {
                $data['buildingID'] = $request->buildingID;
            }
            // то же для auditoryID
            if (empty($edit->auditoryID) && $request->filled('auditoryID')) {
                $data['auditoryID'] = $request->auditoryID;
            }
            // и TutorID
            if (empty($edit->TutorID) && $request->filled('TutorID')) {
                $data['TutorID'] = $request->TutorID;
            }
            // назначение (type) — если ещё не установлено
            if (empty($edit->type) && $request->filled('type')) {
                $data['type'] = $request->type;
            }
            // inv_number
            if (empty($edit->inv_number) && $request->filled('inv_number')) {
                $data['inv_number'] = $request->inv_number;
            }
            // верификация/редактор всегда обновляем
            $data['verification_status'] = 1;
            $data['redactor_id'] = Auth::user()->TutorID;

            if (!empty($data)) {
                DB::table('in_product_lists')
                    ->where('id_product', $id_product)
                    ->update($data);
            }

            // характеристики — только если их ещё нет
            $hasActive = in_characteristics_for_product::where('id_product',$id_product)
                ->where('current_status',0)
                ->exists();

            if (! $hasActive) {
                // закроем старые (если есть)
                in_characteristics_for_product::where('id_product', $id_product)
                    ->update(['current_status'=>1]);

                foreach ($request->names as $i => $val) {
                    in_characteristics_for_product::create([
                        'current_status'      => 0,
                        'id_product'          => $id_product,
                        'id_characteristic'   => $request->id_characteristic[$i],
                        'characteristic_value'=> $val,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success','Инвентарь успешно обновлен!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Ошибка при обновлении: '.$e->getMessage());
        }
    }

    public function editCharacteristic($id_product){
            $characteristics = DB::table('in_characteristics_for_products')->where('id_product', $id_product)
                ->leftJoin('in_list_characteristics', 'in_list_characteristics.id_characteristic', '=', 'in_characteristics_for_products.id_characteristic')
                ->where('current_status',0)
                ->get();

            return view('backend.invertory.redactor.charEdit', compact('characteristics', 'id_product'));

    }

    public function store(Request $request, $id_product)
    {
        // 1) Валидация
        $data = $request->validate([
            'id_product'           => 'required|integer|exists:in_product_lists,id_product',
            'id_characteristic'    => 'required|array|min:1',
            'id_characteristic.*'  => 'required|integer|exists:in_list_characteristics,id_characteristic',
            'names'                => 'required|array|min:1',
            'names.*'              => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 2) «Закрываем» все существующие записи этого продукта
            in_characteristics_for_product::where('id_product', $data['id_product'])
                ->update(['current_status' => 1]);

            // 3) Готовим новые записи
            $now   = now();
            $items = [];
            foreach ($data['id_characteristic'] as $i => $charId) {
                $items[] = [
                    'id_product'           => $data['id_product'],
                    'id_characteristic'    => $charId,
                    'characteristic_value' => $data['names'][$i],
                    'current_status'       => 0,      // новый «активный»
                    'created_at'           => $now,
                    'updated_at'           => $now,
                ];
            }

            // 4) Вставляем за раз
            in_characteristics_for_product::insert($items);

            DB::commit();
            return redirect()
                ->route('editAll', ['id' => $id_product])
                ->with('success', 'Характеристики успешно сохранены!');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Ошибка при сохранении характеристик: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Не удалось сохранить характеристики.');
        }
    }

    public function confirmStatus($id)
    {
        // Находим запись по ID
        $item = in_product_lists::where('id_product', $id)->firstOrFail();

        $adminTutorID = [646, 359];
        // Проверяем, является ли текущий пользователь администратором
        if (in_array(Auth::user()->TutorID, $adminTutorID)) {
            // Обновляем значение поля "status"
            $item->verification_status = 2; // Замените 2 на нужное значение для подтвержденного статуса
            $item->save();

            in_messages::where('id_product', $id)->delete();

            Note::where('id_product', $id)->delete();

        }

        // Перенаправляем обратно на предыдущую страницу
        return back()->with('success', 'Статус подтвержден!');
    }

    public function refuseStatus(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'inv_number' => 'nullable|string',
            'redactor_id' => 'required',
            'id_product' => 'required',
            'id_name' => 'required',
        ]);

        $adminTutorID = [646, 359];

        if (in_array(Auth::user()->TutorID, $adminTutorID)) {
            $item = in_product_lists::findOrFail($validated['id_product']);
            $item->verification_status = 3;
            $item->save();

            $message = new in_messages();
            $message->message = $validated['message'];
            $message->TutorID = $validated['redactor_id'];
            $message->inv_number = $validated['inv_number'];
            $message->id_name = $validated['id_name'];
            $message->id_product = $validated['id_product'];
            $message->save();
        }

        return back()->with('warning', 'Отправлен на доработку');
    }

    public function change_tutor(){

        return view('backend.invertory.redactor.change');

    }

    public function search_item(Request $request)
    {
        // Получаем поисковой запрос из запроса GET
        $query = $request->input('query', '');

        // Выполняем поиск в таблице InProductList по полю inv_number
        $products = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic')->where('current_status', '0');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('tutors', 'in_product_lists.TutorID', '=', 'tutors.TutorID')
            ->where('in_product_lists.inv_number', 'like', "%$query%") // Фильтруем по inv_number
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                DB::raw("CONCAT(tutors.lastname, ' ', tutors.firstname) AS tutor_fullname")
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->get();

        // Возвращаем результат поиска на страницу search.blade.php
        return view('backend.invertory.redactor.search')->with('product', $products)->with('query', $query);
    }

    public function editChange ($id_product)
    {
        $edit = in_product_lists::with(['characteristics' => function ($query) {
            $query->with('characteristic');
        }])
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('tutors', 'in_product_lists.TutorID', '=', 'tutors.TutorID')
            ->select(
                'in_product_lists.*',
                'buildings.buildingName',
                'auditories.auditoryName',
                'in_product_name.name_product',
                DB::raw("CONCAT(tutors.lastname, ' ', tutors.firstname) AS tutor_fullname")
            )
            ->where('in_product_lists.actual_inventory', 1)
            ->find($id_product);
        return view('backend.invertory.redactor.edit_change', compact('edit'));
    }


    public function insert(Request $request, $id_product)
    {
        $new_id = DB::table('in_product_lists')->insertGetId([
            'id_name' => $request->input('id_name'),
            'buildingID' => $request->input('buildingID'),
            'auditoryID' => $request->input('auditoryID'),
            'TutorID' => $request->input('TutorID'),
            'type' => $request->input('type'),
            'inv_number' => $request->input('inv_number'),
            'verification_status' => 1,
            'redactor_id'=> Auth::user()->TutorID,
        ]);

        $date = $request->input('date');

        // Проверка наличия загруженного файла
        if ($request->hasFile('file')) {
            try {
                $uniqueFileName = Str::uuid() . '.' . $request->file('file')->getClientOriginalExtension();
                $filePath = $request->file('file')->storeAs('public/move_document', $uniqueFileName);

                Move::create([
                    'file' => 'move_document/' . $uniqueFileName,
                    'inv_number' => $request->input('inv_number'),
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Ошибка при загрузке файла: ' . $e->getMessage());
            }
        } else {
            return redirect()->back()->with('error', 'Файл не был загружен.');
        }

        in_product_lists::where('id_product', $id_product)->update(['actual_inventory' => 0]);

        Note::where('id_product', $id_product)->delete();

        Note::create([
            'id_product' => $new_id,
            'note' => 'Перемещено'
        ]);



        return view('backend.invertory.redactor.change')->with('success','Перемещение успешно совершено!');
    }

    public function story($id_name)
    {
        $results = DB::table('in_product_lists')
            ->where('id_name', $id_name)
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('tutors AS tutor', 'in_product_lists.TutorID', '=', 'tutor.TutorID')
            ->leftJoin('tutors AS redactor', 'in_product_lists.redactor_id', '=', 'redactor.TutorID')
            ->leftJoin('move_inventory', 'in_product_lists.inv_number', '=', 'move_inventory.inv_number')
            ->whereNotNull('move_inventory.id') // Исключаем записи без связанных документов
            ->select(
                'auditories.auditoryName',
                'move_inventory.file',
                'move_inventory.inv_number',
                'move_inventory.id',
                DB::raw("CONCAT(tutor.lastname, ' ', tutor.firstname) AS tutor_fullname"),
                DB::raw("CONCAT(redactor.lastname, ' ', redactor.firstname) AS redactor_fullname"),
                'in_product_lists.updated_at'
            )
            ->orderBy('updated_at')
            ->get();

        return view('backend.invertory.redactor.story', compact('results'));
    }

    public function move_view(Request $request, $id)
    {
        $view = DB::table('move_inventory')->where('id', $id)->first();

        if (!$view) {
            return redirect()->back()->with('error', 'Документ не найден.');
        }

        return view('backend.invertory.redactor.move_document_view', compact('view'));
    }


    /*public function writeOff($id_product)
    {
        in_product_lists::where('id_product', $id_product)->update(['write_off' => 0]);

        return back()->with('success', 'Инвертарь успешно списан');
    }*/

    public function transmission_send()
    {
        $inventory = in_product_lists::with(['characteristics.characteristic'])
            ->where('in_product_lists.TutorID', Auth::user()->TutorID)
            ->leftJoin('auditories', 'in_product_lists.auditoryID', '=', 'auditories.auditoryID')
            ->leftJoin('buildings', 'in_product_lists.buildingID', '=', 'buildings.buildingID')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->leftJoin('tutors', 'in_product_lists.TutorID', '=', 'tutors.TutorID')
            ->select(
                'in_product_lists.*',
                'auditories.auditoryName',
                'auditories.auditoryType as auditoryType',
                'in_product_name.name_product AS name',
                DB::raw("CONCAT(tutors.lastname, ' ', tutors.firstname) AS full_name")
            )
            ->get();

        $tutor = DB::connection('mysql_platonus')->table('tutors')->get();

        return view('backend.invertory.redactor.transmission', compact('inventory', 'tutor'));
    }

    public function store_trans(Request $request)
    {
        $request->validate([
            'TutorID' => 'required',
        ]);

        $newTutorID = $request->TutorID;
        $currentTutorID = Auth::user()->TutorID;
        $auditoryID = $request->auditoryID ?? null;
        $idProducts = $request->id_products ?? [];

        if ($auditoryID) {
            $idProducts = DB::table('in_product_lists')
                ->where('auditoryID', $auditoryID)
                ->pluck('id_product')
                ->toArray();
        }

        if (empty($idProducts)) {
            return back()->with('error', 'Нет инвентаря для перемещения.');
        }

        DB::beginTransaction();

        try {
            foreach ($idProducts as $idProduct) {
                // Проверяем, есть ли уже активная запись для этого товара
                $existingTransmission = DB::table('transmissions')
                    ->where('id_product', $idProduct)
                    ->where('status', '!=', 0)
                    ->first();

                if ($existingTransmission) {
                    // Если есть — просто обновляем статус на 2 (повторная передача)
                    DB::table('transmissions')
                        ->where('id_trans', $existingTransmission->id_trans)
                        ->update([
                            'status' => 0,
                            'updated_at' => now(),
                        ]);
                } else {
                    // Если нет — создаём новую запись
                    $auditoryIDItem = DB::table('in_product_lists')
                        ->where('id_product', $idProduct)
                        ->value('auditoryID');

                    DB::table('transmissions')->insert([
                        'id_product'   => $idProduct,
                        'auditoryID'   => $auditoryIDItem,
                        'TutorID'      => $currentTutorID,
                        'NewTutorID'   => $newTutorID,
                        'send_status'  => 1,
                        'status'       => 1,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ]);
                }

                DB::table('in_product_lists')
                    ->where('id_product', $idProduct)
                    ->update(['transmission_status' => 2]);
            }

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
                ->where('transmissions.TutorID', $currentTutorID)
                ->whereIn('transmissions.status', [1, 2])
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

            $pdf = Pdf::loadView('backend.invertory.redactor.transmission_pdf', compact('pdfData', 'sender', 'receiver'))
                ->setPaper('a4', 'portrait');

            session([
                'pdf_data' => $pdfData,
                'sender' => $sender,
                'receiver' => $receiver,
            ]);

            return redirect()->route('showPdfTransmission');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при передаче ОС: ' . $e->getMessage());
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
