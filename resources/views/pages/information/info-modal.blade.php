<!-- Button to open modal -->
<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#infoModal">
  ℹ Информация
</button>

<!-- Info Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="infoModalLabel">Информация для пользователей</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Закрыть"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">
          📞 <strong>Телефон поддержки:</strong><br>
          <a href="tel:+998901234567">+998 90 123 45 67</a>
        </p>
        <p class="mb-3">
          📧 <strong>Email для связи:</strong><br>
          <a href="mailto:support@example.com">support@example.com</a>
        </p>
        <div class="alert alert-success small rounded-3">
          🎁 <strong>Реферальная программа:</strong><br>
          Пригласите друга и получите бонус при его первом заказе!<br>
          Узнайте больше на <a href="#">нашем сайте</a>.
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Понятно</button>
      </div>
    </div>
  </div>
</div>
