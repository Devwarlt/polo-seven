const action = "/php/MVCRouter.php";

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