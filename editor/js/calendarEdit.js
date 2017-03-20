/**
 * Created by yanni on 2017-03-20.
 */

function updatePreview() {
    //TODO Livepreview w/ styles on frontend
    //$("#preview-header").html("<h1>"+$("#edit-header").val()+"</h1>");
    $("#preview-text").html(marked($("#edit-info").val()));
    $("#edit-info").trigger('autoresize');
}

function save() {
    let data = {
        cID: $("#edit-cID").val(),
        name: $("#edit-name").val(),
        info: $("#edit-info").val(),
        date: $("#edit-date").val(),
        dateUntil: $("#edit-dateUntil").val(),
        state: $("#edit-state").val()
    };

    $.post("../api/calendar/update.php", data, function(json) {
        json = JSON.parse(json);
        if(json.success == true) {
            window.location = "calendar.php";
        } else {
            Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
        }
    });

}

function cancel() {
    window.location = "calendar.php";
}

$(document).ready(function() {
    $("#edit-info").on("keyup", updatePreview);
    $("#edit-name").on("keyup", updatePreview);
    $('input#edit-name, input#edit-header, input#edit-title, textarea#edit-text').characterCounter();

    $(".datetimepicker").bootstrapMaterialDatePicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        lang: 'de',
        weekStart: 1,
        cancelText: 'ABBRECHEN',
        switchOnClick: true,
    });

    updatePreview();
});