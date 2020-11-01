pIdCriar = 0;
pIdAlterar = 0;

function criarVenda() {
    var id_produtos = "";

    $("input:hidden[id^='criar-venda-id-produtos-']").each(function () {
        id_produtos = id_produtos.concat($(this).attr("value") + ",");
    });

    if (id_produtos.substr(id_produtos.length - 1) === ",")
        id_produtos = id_produtos.slice(0, -1);

    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "criar-venda",
        args: {
            id_usuario: $("#criar-venda-id-usuario").val(),
            id_pagamento: $("#criar-venda-id-pagamento").val(),
            id_produtos: id_produtos
        },
        onBegin: function () {
            $("#criar-venda-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#criar-venda-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso")) {
                $("#criar-venda-id-usuario")[0].selectedIndex = 0;
                $("#criar-venda-id-pagamento")[0].selectedIndex = 0;
                $("#criar-venda-id-produtos-container").html("");

                pIdCriar = 0;
            }

            $("#criar-venda-btn").attr("disabled", false);
        }
    });
}

function consultarVendaId() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-venda-id",
        args: {
            id: $("#consultar-venda-index").val()
        },
        onBegin: function () {
            $("#consultar-venda-id-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-venda-id-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            $("#consultar-venda-id-btn").attr("disabled", false);
        }
    });
}

function consultarVendaTodos() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "consultar-venda-todos",
        onBegin: function () {
            $("#consultar-venda-todos-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $("#consultar-venda-todos-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                data +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function () {
            $("#consultar-venda-todos-btn").attr("disabled", false);
        }
    });
}

function alterarVenda() {
    var id_produtos = "";

    $("input:hidden[id^='alterar-venda-id-produtos-']").each(function () {
        id_produtos = id_produtos.concat($(this).attr("value") + ",");
    });

    if (id_produtos.substr(id_produtos.length - 1) === ",")
        id_produtos = id_produtos.slice(0, -1);

    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "alterar-venda",
        args: {
            id: $(":hidden#alterar-venda-id").val(),
            id_usuario: $("#alterar-venda-id-usuario").val(),
            id_pagamento: $("#alterar-venda-id-pagamento").val(),
            id_produtos: id_produtos
        },
        onBegin: function () {
            $("#alterar-venda-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-venda-id-result" : "#alterar-venda-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (data.includes("sucesso"))
                pIdAlterar = 0;

            $("#alterar-venda-btn").attr("disabled", false);
        }
    });
}

function removerVenda() {
    sendRequestAsync({
        action: action,
        controller: "dashboard",
        operation: "remover-venda",
        args: {
            id: $(":hidden#remover-venda-id").val(),
            id_usuario: $(":hidden#remover-venda-id-usuario").val()
        },
        onBegin: function () {
            $("#remover-venda-btn").attr("disabled", true);
        },
        onCompleted: function (data) {
            $(data.includes("sucesso") ? "#consultar-venda-id-result" : "#remover-venda-result").html(
                "<div class='alert alert-light border-secondary alert-dismissible fade show' role='alert'>" +
                "<hr/>" +
                "<p align='justify'>" + data + "</p>" +
                "<button type='button' class='close' data-dismiss='alert' aria-label='Fechar'>&times;</button>" +
                "</div>"
            );
        },
        onFinished: function (data) {
            if (!data.includes("sucesso"))
                $("#remover-venda-btn").attr("disabled", false);
        }
    });
}

function adicionarItemProduto(label) {
    var pId = label === "criar"
        ? pIdCriar
        : label === "alterar"
            ? pIdAlterar
            : undefined;

    if (pId === undefined) {
        console.log("Label não suportada! -> label: " + label);
        return;
    }

    var btn = $("#" + label + "-venda-id-produtos-btn");
    btn.attr("disabled", true);
    $("#" + label + "-venda-id-produtos-select option:selected").each(function () {
        $("#" + label + "-venda-id-produtos-container").append(
            "<div id='" + label + "-venda-id-produtos-div-" + ++pId + "' class='card text-white bg-success'>" +
            "<input type='hidden' id='" + label + "-venda-id-produtos-" + pId + "' value='" + $(this).val() + "'/>" +
            "<button class='close' style='margin: 4px' data-toggle='collapse' data-target='" + label + "-venda-id-produtos-div-" + pId + "' " +
            "aria-expanded='false' aria-controls='" + label + "-venda-id-produtos-div-" + pId + "' " +
            "onclick='removerItemProduto('" + label + "', " + pId + ")'>&times;</button>" +
            "<div class='card-body'>" +
            "<p class='card-text'>" + $(this).text() + "</p>" +
            "</div></div>"
        );
    });
    btn.attr("disabled", false);

    if (label === "criar")
        pIdCriar = pId;
    else
        pIdAlterar = pId;
}

function removerItemProduto(label, id) {
    var btn = $("#" + label + "-venda-id-produtos-btn");
    btn.attr("disabled", true);
    $("#" + label + "-venda-id-produtos-div-" + id).remove();
    btn.attr("disabled", false);
}