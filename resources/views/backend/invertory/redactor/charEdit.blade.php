@extends('backend.layouts.app')
@section('content')
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <!-- general form elements -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Редактирование</h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form id="myform"
                          role="form"
                          action="{{ route('characteristics.store', $id_product) }}"
                          method="POST"
                          enctype="multipart/form-data"
                    >
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @csrf
                        <div class="container">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="card-body">
                                        @if($characteristics->isEmpty())
                                            <div class="alert alert-warning">
                                                У этого товара нет активных характеристик для редактирования.
                                            </div>
                                        @else
                                            @foreach($characteristics as $char)
                                                <input type="text" name="id_product"  class="form-control"
                                                       id="id_product" value="{{$char->id_product}}" required hidden>
                                                <input type="text" name="id_characteristic[]"  class="form-control"
                                                       id="id_characteristic" value="{{$char->id_characteristic}}" required hidden>
                                                @php
                                                    $html = $char->input_characteristic;
                                                    $val = $char->characteristic_value;
                                                    
                                                    if (strpos($html, '<select') !== false) {
                                                        $html = str_replace('value="' . $val . '"', 'value="' . $val . '" selected', $html);
                                                    } elseif (strpos($html, '<input') !== false) {
                                                        if (strpos($html, 'value=') === false) {
                                                            $html = str_replace('<input', '<input value="' . e($val) . '"', $html);
                                                        } else {
                                                            $html = preg_replace('/value="[^"]*"/', 'value="' . e($val) . '"', $html);
                                                        }
                                                    }
                                                @endphp
                                                {!! $html !!}
                                                <br>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        @if(!$characteristics->isEmpty())
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">Сохранить</button>
                            </div>
                        @endif
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <div class="col-md-2">
            </div>
        </div>
        <!-- /.row -->
    </div>
@endsection
