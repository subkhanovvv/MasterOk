<div class="modal fade" id="newProductModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Новый товар</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form class="forms-sample method-POST" method="POST" enctype="multipart/form-data"
                    action="{{ route('store-product') }}">
                    @csrf
                    <div class="d-flex justify-content-between gap-3">
                        <div class="w-50">
                            <div class="form-group">
                                <label for="exampleInputUsername1">Название товара </label>
                                <input type="text" class="form-control" id="exampleInputUsername1"
                                    placeholder="Username" name="name">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Бренд</label>
                                <select name="" id="" class="form-select" id="exampleSelectGender">
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Цена в USD</label>
                                <input class="form-control" type="text" placeholder="Цена в долларах США"
                                    name="price_usd" tabindex="0" value="" aria-required="true" required="">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Единица</label>
                                <select name="unit" class="form-select" id="exampleSelectGender">
                                    <option disabled selected>Выберите единицу</option>
                                    <option value="кг">кг</option>
                                    <option value="г">г</option>
                                    <option value="л">л</option>
                                    <option value="мл">мл</option>
                                    <option value="м">м</option>
                                    <option value="см">см</option>
                                    <option value="шт">шт</option>
                                    <option value="коробка">коробка</option>
                                    <option value="упаковка">упаковка</option>
                                    <option value="рулон">рулон</option>
                                    <option value="пара">пара</option>
                                    <option value="дюжина">дюжина</option>
                                    <option value="набор">набор</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputPassword1">Загрузить изображения</label>
                                <input type="file" class="form-control" id="exampleInputPassword1"
                                    placeholder="Password">
                                {{-- <div class="item" id="imgpreview" style="display">
                                    <img src="" class="effect8" alt="">
                                </div> --}}
                            </div>

                        </div>
                        <div>
                            <div class="form-group">
                                <label for="exampleInputEmail1">Краткое описание </label>
                                <input type="text" class="form-control" id="exampleInputEmail1"
                                    placeholder="short description" name="short_description">
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Категория</label>
                                <select name="category" class="form-select" id="exampleSelectGender">
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Цена в UZS </label>
                                <input type="text" class="form-control" type="text"
                                    placeholder="Цена в узбекских сумах" name="price_uzs" tabindex="0"
                                    value="" aria-required="true" required>
                                <div class="text-tiny text-warning">1 USD = <strong
                                        id="usd-uzs-rate">Загрузка...</strong> UZS
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="exampleInputConfirmPassword1">Цена продажи </label>
                                <input type="password" class="form-control" id="exampleInputConfirmPassword1"
                                    placeholder="Password">
                            </div>

                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-lg text-white">Сохранить</button>
            </div>
        </div>
    </div>
</div>
