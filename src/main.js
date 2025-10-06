$(document).ready(function() {
    //USER CREATE && EDIT
    $(".btn-user-ctrl").click(function() {
        let userId = $(this).data("id");
        let userEmail = $(this).data("email");

        $("#editUserContent").html('<div class="text-center p-3"><div class="spinner-border text-primary"></div><p>Carregando...</p></div>');

        // Abre o modal
        let modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
        if(!userId){
            $.ajax({
                url: "user_controller.php",
                type: "GET",
                success: function(response) {
                    $("#editUserModalLabel").html("Novo Usuário");
                    $("#editUserContent").html(response);
                },
                error: function() {
                    $("#editUserContent").html('<div class="alert alert-danger">Erro ao carregar o formulário.</div>');
                }
            });
        }
        else{
            $.ajax({
                url: "user_controller.php",
                type: "GET",
                data: { id: userId },
                success: function(response) {
                    $("#editUserModalLabel").html("Editar Usuário");
                    $("#editUserContent").html(response);
                    if(response.success==true){
                        window.location.reload(true);
                    }
                },
                error: function() {
                    $("#editUserContent").html('<div class="alert alert-danger">Erro ao carregar o formulário.</div>');
                }
            });
        }
    });


    //USER DELETE JS METHOD
    $(".btn-delete-user").click(function() {
            let userId = $(this).data("id");

            // Faz a requisição AJAX
            if (confirm("Tem certeza que deseja excluir este usuário?")) {
                $("#editUserContent").html('<div class="text-center p-3"><div class="spinner-border text-primary"></div><p>Carregando...</p></div>');

                // Abre o modal
                let modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                modal.show();

                $.ajax({
                    url: "user_controller.php?id="+userId,
                    type: "DELETE",
                    success: function(response) {
                        $("#editUserContent").html(
                        '<div class="alert alert-success">'+response.message+'</div>'+
                        '<button class="btn btn-sm btn-success me-2 btn-user-ctrl" onclick=window.location.reload(true); >OK</button>'
                        );

                    },
                    error: function() {
                        $("#editUserContent").html('<div class="alert alert-danger">Erro ao carregar o formulário.</div>');
                    }
                });
            } else {
                 // Fecha a modal se o usuário cancelar
                 return false;
            }
        });

});
