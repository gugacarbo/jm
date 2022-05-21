

function closeAlertBox() {
    $("#alertBox").css("opacity", "0");
    setTimeout(() => {
        $("#alertBox").remove();
    }, 500);
}


function _alert(text, type = "warning", title = "Atenção", returnRef = 0) {
    alert("oie")
    var alertModel = `
        <div class="alertBox" id="alertBox">
            <div class="alertBoxTitle">
                    ${title}
                    ${type == "warning" ? `<i class="fas fa-exclamation-triangle"></i>` : `<i class="fas fa-check"></i>`}
            </div>
            <div class="alertBoxText">
                <p>
                    ${text}
                </p>
            </div>
            <div class="alertBoxButtons ">
                <div class="alertBoxTrue alertBoxButton">
                    <i class="fa fa-check" onclick="closeAlertBox()" ${returnRef == 0 ? "" : "id='" + returnRef + "'"}></i>
                </div>
                <div class="alertBoxFalse alertBoxButton" onclick="closeAlertBox()"  ${returnRef == 0 ? "" : "id='" + returnRef + "Not'"}>
                    <i class="fa fa-times"></i>
                </div>
            </div>
        </div>`;
        $("body").append(alertModel);
        setTimeout(() => {
            $("#alertBox").css("opacity", "1");
        }, 100);
}

export { _alert };