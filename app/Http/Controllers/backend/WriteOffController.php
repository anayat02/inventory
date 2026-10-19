<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\in_product_lists;
use App\Models\Write_off;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WriteOffController extends Controller
{
    public function write_off(){

        return view('backend.invertory.create_invertory.write_off');

    }

    public function write_off_save(Request $request)
    {
        $request->validate([
            'inv_numbers'     => 'required|array|min:1',
            'inv_numbers.*'   => 'required|string',
            'document_number' => 'required|string|max:255',
            'theme'           => 'required|string|max:255',
            'date'            => 'required|date',
            'file'            => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/act', $fileName);

        DB::transaction(function () use ($request, $fileName) {
            $products = DB::table('in_product_lists')
                ->whereIn('inv_number', $request->inv_numbers)
                ->pluck('id_product', 'inv_number');

            foreach ($request->inv_numbers as $inv_number) {
                $id_product = $products[$inv_number] ?? null;

                if (!$id_product) {
                    continue; // или throw, если номер обязан существовать
                }

                Write_off::create([
                    'inv_number'      => $inv_number,
                    'document_number' => $request->document_number,
                    'theme'           => $request->theme,
                    'file'            => 'act/' . $fileName,
                    'date'            => $request->date,
                    'id_product'      => $id_product,
                ]);
            }

            DB::table('in_product_lists')
                ->whereIn('id_product', $products->values())
                ->update(['write_off' => 0]);
        });

        return redirect()->back()->with('success', 'Записи успешно добавлены в список списанных инвентарей.');
    }

    public function write_off_list(){

        $results=DB::table('write_off')
            ->leftJoin('in_product_lists', 'write_off.id_product', '=', 'in_product_lists.id_product')
            ->leftJoin('in_product_name', 'in_product_lists.id_name', '=', 'in_product_name.id_name')
            ->get();

        return view('backend.invertory.create_invertory.write_off_list', compact('results'));

    }

    public function doc_view(Request $request, $id){
        $view = DB::table('write_off')->where('id', $id)->first();

        return view('backend.invertory.create_invertory.document_view', compact('view'));
    }
}
