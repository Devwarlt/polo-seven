const action = "/php/MVCRouter.php";

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

/**
 * param.action: request URL
 * param.controller: controller target
 * param.operation (optional): controller operation target
 * param.args (optional): collection of data
 * param.onBegin (optional): page behavior before request
 * param.onCompleted: page behavior after request (response)
 * param.onFinished (optional): page behavioer after request (after param.onCompleted cast)
 * @param params
 */
function sendRequestAsync(params) {
    if (params.action === undefined || params.action === null || params.controller === undefined || params.controller === null || params.onCompleted === undefined || params.onCompleted === null)
        return;

    $("#loading").height("100%");

    if (params.onBegin !== null)
        params.onBegin();

    var args = {
        controller: params.controller,
        operation: params.operation
    };

    if (params.args !== undefined && params.args !== null)
        Object.keys(params.args).forEach(function (key) {
            args[key] = params.args[key];
        });

    window.setTimeout(function () {
        $.post(params.action, args).done(function (data) {
            params.onCompleted(data);

            if (params.onFinished !== null)
                params.onFinished(data);

            $("#loading").height("0%");
        });
    }, 200);
}