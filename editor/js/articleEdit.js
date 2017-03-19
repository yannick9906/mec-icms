/**
 * Created by yanni on 2017-03-18.
 */

function updatePreview() {
    $("#preview-header").html("<h1>"+$("#edit-header").val()+"</h1>");
    $("#preview-text").html(marked($("#edit-text").val()));
    $("#edit-text").trigger('autoresize');
}

function save() {
    let data = {
        aID: $("#edit-aID").val(),
        name: $("#edit-name").val(),
        title: $("#edit-title").val(),
        state: $("#edit-state").val(),
        header: $("#edit-header").val(),
        text: $("#edit-text").val()
    };

    $.post("../api/articles/update.php", data, function(json) {
       json = JSON.parse(json);
       if(json.success == true) {
           window.location = "articles.php";
       } else {
           Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
       }
    });

}

function cancel() {
    window.location = "articles.php";
}

$(document).ready(function() {
    $("#edit-text").on("keyup", updatePreview);
    $("#edit-header").on("keyup", updatePreview);
    $('input#edit-name, input#edit-header, input#edit-title, textarea#edit-text').characterCounter();
    updatePreview();
});