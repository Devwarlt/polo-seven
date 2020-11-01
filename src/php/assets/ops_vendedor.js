function criarVendedor() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "criar-vendedor",
        args: {
            nome: $("#criar-vendedor-nome").val(),
            senha: $("#criar-vendedor-senha").val()
        },
        onBegin: function () {
            $("#criar-vendedor-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#criar-vendedor-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso")) {
                $("#criar-vendedor-nome").val("");
                $("#criar-vendedor-senha").val("");
            }

            $("#criar-vendedor-btn").attr("disabled", false);
        }
    });
}

function consultarVendedorId() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-vendedor-id",
        args: {
            id: $("#consultar-vendedor-index").val()
        },
        onBegin: function () {
            $("#consultar-vendedor-id-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-vendedor-id-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            $("#consultar-vendedor-id-btn").attr("disabled", false);
        }
    });
}

function consultarVendedorTodos() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-vendedor-todos",
        onBegin: function () {
            $("#consultar-vendedor-todos-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-vendedor-todos-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function () {
            $("#consultar-vendedor-todos-btn").attr("disabled", false);
        }
    });
}

function alterarVendedor() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "alterar-vendedor",
        args: {
            id: $(":hidden#alterar-vendedor-id").val(),
            nome: $("#alterar-vendedor-nome").val(),
            senha: $("#alterar-vendedor-senha").val(),
            id_usuario: $(":hidden#alterar-vendedor-id-usuario").val(),
            nivel: $("#alterar-vendedor-nivel option:selected")[0].getAttribute("value")
        },
        onBegin: function () {
            $("#alterar-vendedor-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-vendedor-id-result" : "#alterar-vendedor-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#alterar-vendedor-btn").attr("disabled", false);
        }
    });
}

function removerVendedor() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "remover-vendedor",
        args: {
            id: $(":hidden#remover-vendedor-id").val(),
            id_usuario: $(":hidden#remover-vendedor-id-usuario").val()
        },
        onBegin: function () {
            $("#remover-vendedor-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-vendedor-id-result" : "#remover-vendedor-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#remover-vendedor-btn").attr("disabled", false);
        }
    });
}