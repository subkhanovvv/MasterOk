<div class="modal fade" id="editProfileModal" aria-hidden="true">
    <div class="modal-dialog" style="max-width:800px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Изменить информацию профиля</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{route('profile.update')}}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Имя пользователя</label>
                        <input type="text" class="form-control" placeholder="Имя пользователя" name="name" 
                          value="{{Auth::user()->name }}">
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль</label>
                        <input type="text" class="form-control" placeholder="Новый пароль" name="password">
                    </div>
                 
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary text-white">Сохранить</button>
                </div>
            </form>
            </div>
    </div>
