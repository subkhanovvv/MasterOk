@if(count($consumptions) > 0)
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Продукт</th>
                    <th>Единица</th>
                    <th>Кол-во</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                    <th width="50px"></th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($consumptions as $index => $item)
                    <tr>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['unit'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'], 2) }}</td>
                        <td>{{ number_format($item['total'], 2) }}</td>
                        <td class="text-center">
                            <form method="POST" action="{{ route('consumption.remove', $index) }}" class="consumption-remove-form">

                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @php $total += $item['total']; @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Общая сумма:</strong></td>
                    <td colspan="2"><strong>{{ number_format($total, 2) }}</strong> UZS</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <form method="POST" action="{{ route('consumption.store') }}">
        @csrf
        @foreach($consumptions as $item)
            <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}">
            <input type="hidden" name="unit[]" value="{{ $item['unit'] }}">
            <input type="hidden" name="quantity[]" value="{{ $item['quantity'] }}">
            <input type="hidden" name="price[]" value="{{ $item['price'] }}">
            <input type="hidden" name="total[]" value="{{ $item['total'] }}">
        @endforeach

        <div class="mb-3 mt-3">
            <label for="notes" class="form-label">Примечание</label>
            <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Сохранить расход
            </button>
        </div>
    </form>
@else
    <div class="alert alert-info text-center">
        Нет добавленных продуктов
    </div>
@endif
