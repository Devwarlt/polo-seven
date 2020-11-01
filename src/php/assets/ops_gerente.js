function showPassword(id) {
    var doc_id = document.getElementById(id);
    doc_id.type = doc_id.type === "password" ? "text" : "password";
}

function criarGerente() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "criar-gerente",
        args: {
            nome: $("#criar-gerente-nome").val(),
            senha: $("#criar-gerente-senha").val()
        },
        onBegin: function () {
            $("#criar-gerente-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#criar-gerente-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso")) {
                $("#criar-gerente-nome").val("");
                $("#criar-gerente-senha").val("");
            }

            $("#criar-gerente-btn").attr("disabled", false);
        }
    });
}

function consultarGerenteId() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-gerente-id",
        args: {
            id: $("#consultar-gerente-index").val()
        },
        onBegin: function () {
            $("#consultar-gerente-id-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-gerente-id-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            $("#consultar-gerente-id-btn").attr("disabled", false);
        }
    });
}

function consultarGerenteTodos() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-gerente-todos",
        onBegin: function () {
            $("#consultar-gerente-todos-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-gerente-todos-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function () {
            $("#consultar-gerente-todos-btn").attr("disabled", false);
        }
    });
}

function alterarGerente() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "alterar-gerente",
        args: {
            id: $(":hidden#alterar-gerente-id").val(),
            nome: $("#alterar-gerente-nome").val(),
            senha: $("#alterar-gerente-senha").val(),
            id_usuario: $(":hidden#alterar-gerente-id-usuario").val(),
            nivel: $("#alterar-gerente-nivel option:selected")[0].getAttribute("value")
        },
        onBegin: function () {
            $("#alterar-gerente-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-gerente-id-result" : "#alterar-gerente-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#alterar-gerente-btn").attr("disabled", false);
        }
    });
}

function removerGerente() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "remover-gerente",
        args: {
            id: $(":hidden#remover-gerente-id").val(),
            id_usuario: $(":hidden#remover-gerente-id-usuario").val()
        },
        onBegin: function () {
            $("#remover-gerente-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-gerente-id-result" : "#remover-gerente-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#remover-gerente-btn").attr("disabled", false);
        }
    });
}