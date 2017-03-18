/**
 * Created by yanni on 2017-03-18.
 */

function updatePreview() {
    $("#preview-header").html("<h1>"+$("#edit-header").val()+"</h1>");
    $("#preview-text").html(marked($("#edit-text").val()));
    $("#edit-text").trigger('autoresize');
}

function save() {
    window.location = "articles.php";
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

//// Texttools
function bold() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("****");
        setCaretToPos($("#edit-text")[0], selection.start+2);
    } else if(selection.text.startsWith("**")) {
        $("#edit-text").fieldSelection(selection.text.replace(/\*\*/g, ""));
    } else
        $("#edit-text").fieldSelection("**"+selection.text+"**");
    updatePreview();
}

function italic() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("__");
        setCaretToPos($("#edit-text")[0], selection.start+1);
    } else if(selection.text.startsWith("_")) {
        $("#edit-text").fieldSelection(selection.text.replace(/_/g, ""));
    } else
        $("#edit-text").fieldSelection("_"+selection.text+"_");
    updatePreview();
}

function strikeThru() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("~~~~");
        setCaretToPos($("#edit-text")[0], selection.start+2);
    } else if(selection.text.startsWith("~~")) {
        $("#edit-text").fieldSelection(selection.text.replace(/~~/g, ""));
    } else
        $("#edit-text").fieldSelection("~~"+selection.text+"~~");
    updatePreview();
}

function header1() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("# ");
        setCaretToPos($("#edit-text")[0], selection.start+1);
    } else if(selection.text.startsWith("# ")) {
        $("#edit-text").fieldSelection(selection.text.replace("# ", ""));
    } else
        $("#edit-text").fieldSelection("# "+selection.text.replace("### ", "").replace("## ", ""));
    updatePreview();
}

function header2() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("## ");
        setCaretToPos($("#edit-text")[0], selection.start+2);
    } else if(selection.text.startsWith("## ")) {
        $("#edit-text").fieldSelection(selection.text.replace("## ", ""));
    } else
        $("#edit-text").fieldSelection("## "+selection.text.replace("### ", "").replace("# ", ""));
    updatePreview();
}

function header3() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("### ");
        setCaretToPos($("#edit-text")[0], selection.start+3);
    } else if(selection.text.startsWith("### ")) {
        $("#edit-text").fieldSelection(selection.text.replace("### ", ""));
    } else
        $("#edit-text").fieldSelection("### "+selection.text.replace("## ", "").replace("# ", ""));
    updatePreview();
}

function quote() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("> ");
        setCaretToPos($("#edit-text")[0], selection.start+2);
    } else if(selection.text.startsWith("> ") && !selection.text.includes("\n")) {
        $("#edit-text").fieldSelection(selection.text.replace("> ", ""));
    } else if(selection.text.startsWith("> ") && selection.text.includes("\n")) {
        $("#edit-text").fieldSelection(selection.text.replace(/> /g, ""));
    } else if(selection.text.includes("\n")) {
        $("#edit-text").fieldSelection("> "+selection.text.replace(/\n/g, "\n> "));
    } else
        $("#edit-text").fieldSelection("> "+selection.text);
    updatePreview();
}

function code() {
    let selection = $("#edit-text").fieldSelection();
    if (selection.length == 0) {
        $("#edit-text").fieldSelection("``");
        setCaretToPos($("#edit-text")[0], selection.start+1);
    } else if(selection.text.startsWith("`") && !selection.text.includes("\n")) {
        $("#edit-text").fieldSelection(selection.text.replace(/`/g, ""));
    } else if(selection.text.startsWith("```") && selection.text.includes("\n")) {
        $("#edit-text").fieldSelection(selection.text.replace(/```\n/g, ""));
    } else if(selection.text.includes("\n")) {
        $("#edit-text").fieldSelection("```\n"+selection.text+"\n```\n");
    } else
        $("#edit-text").fieldSelection("`"+selection.text+"`");
    updatePreview();
}

function setSelectionRange(input, selectionStart, selectionEnd) {
    if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
    }
    else if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
    }
}

function setCaretToPos (input, pos) {
    setSelectionRange(input, pos, pos);
}