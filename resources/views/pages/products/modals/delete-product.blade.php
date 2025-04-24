<div class="modal fade" id="deleteProductModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Вы уверены, что хотите удалить этот товар?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-danger text-white" id="confirmDeleteBtn">Удалить</button>
            </div>
        </div>
    </div>
</div>

<script>
    // When the delete button is clicked
    $(".deleteProduct").click(function() {
        // Capture the product ID and CSRF token from the clicked link
        var productId = $(this).data("id");
        var token = $(this).data("token");

        // Set up the confirmation button click to actually delete the product
        $("#confirmDeleteBtn").off("click").on("click", function() {
            // Send the AJAX DELETE request to delete the product
            $.ajax({
                url: "/destroy-product/" + productId, // Use the correct endpoint
                type: 'DELETE',
                dataType: "JSON",
                data: {
                    "_token": token, // Send the CSRF token
                },
                success: function(response) {
                    // Close the modal
                    $("#deleteProductModal").modal('hide');

                    // Optionally, show a success message
                    alert(response.message || "Товар успешно удалён!");

                    // Remove the deleted row from the table (assuming you have a table with the corresponding ID)
                    $("button[data-id='" + productId + "']").closest("tr").remove();
                },
                error: function(xhr, status, error) {
                    alert("Ошибка при удалении товара");
                }
            });
        });
    });
</script>
