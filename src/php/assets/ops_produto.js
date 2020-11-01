function criarProduto() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "criar-produto",
        args: {
            nome: $("#criar-produto-nome").val(),
            preco_unitario: $("#criar-produto-preco-unitario").val(),
            total_unidades: $("#criar-produto-total-unidades").val()
        },
        onBegin: function () {
            $("#criar-produto-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#criar-produto-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso")) {
                $("#criar-produto-nome").val("");
                $("#criar-produto-preco-unitario").val("");
                $("#criar-produto-total-unidades").val("");
            }

            $("#criar-produto-btn").attr("disabled", false);
        }
    });
}

function consultarProdutoId() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-produto-id",
        args: {
            id: $("#consultar-produto-index").val()
        },
        onBegin: function () {
            $("#consultar-produto-id-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-produto-id-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            $("#consultar-produto-id-btn").attr("disabled", false);
        }
    });
}

function consultarProdutoTodos() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-produto-todos",
        onBegin: function () {
            $("#consultar-produto-todos-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-produto-todos-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function () {
            $("#consultar-produto-todos-btn").attr("disabled", false);
        }
    });
}

function alterarProduto() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "alterar-produto",
        args: {
            id: $(":hidden#alterar-produto-id").val(),
            nome: $("#criar-produto-nome").val(),
            preco_unitario: $("#criar-produto-preco-unitario").val(),
            total_unidades: $("#criar-produto-total-unidades").val()
        },
        onBegin: function () {
            $("#alterar-produto-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-produto-id-result" : "#alterar-produto-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#alterar-produto-btn").attr("disabled", false);
        }
    });
}

function removerProduto() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "remover-produto",
        args: {
            id: $(":hidden#remover-produto-id").val()
        },
        onBegin: function () {
            $("#remover-produto-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-produto-id-result" : "#remover-produto-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#remover-produto-btn").attr("disabled", false);
        }
    });
}