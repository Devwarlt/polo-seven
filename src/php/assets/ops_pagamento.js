function criarPagamento() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "criar-pagamento",
        args: {
            nome: $("#criar-pagamento-nome").val()
        },
        onBegin: function () {
            $("#criar-pagamento-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#criar-pagamento-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso"))
                $("#criar-pagamento-nome").val("");

            $("#criar-pagamento-btn").attr("disabled", false);
        }
    });
}

function consultarPagamentoId() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-pagamento-id",
        args: {
            id: $("#consultar-pagamento-index").val()
        },
        onBegin: function () {
            $("#consultar-pagamento-id-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-pagamento-id-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            $("#consultar-pagamento-id-btn").attr("disabled", false);
        }
    });
}

function consultarPagamentoTodos() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-pagamento-todos",
        onBegin: function () {
            $("#consultar-pagamento-todos-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-pagamento-todos-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function () {
            $("#consultar-pagamento-todos-btn").attr("disabled", false);
        }
    });
}

function alterarPagamento() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "alterar-pagamento",
        args: {
            id: $(":hidden#alterar-pagamento-id").val(),
            nome: $("#alterar-pagamento-nome").val()
        },
        onBegin: function () {
            $("#alterar-pagamento-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-pagamento-id-result" : "#alterar-pagamento-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#alterar-pagamento-btn").attr("disabled", false);
        }
    });
}

function removerPagamento() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "remover-pagamento",
        args: {
            id: $(":hidden#remover-pagamento-id").val()
        },
        onBegin: function () {
            $("#remover-pagamento-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-pagamento-id-result" : "#remover-pagamento-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#remover-pagamento-btn").attr("disabled", false);
        }
    });
}