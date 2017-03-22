/**
 * Created by yanni on 2017-03-20.
 */

let sortName = "#sort";
let listName = "#files"
let linkList = "../api/files/getList.php";
let jsonField = "files"
let pagesize = 12;
///////////////////////////////////////////////////////////////////////
// TODO Fill List Template and update() method
let listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;">
        <td>{{id}}</td>
        <td><b>{{fileName}}</b><br/>{{filePath}}</td>
        <td>Hochgeladen von {{author}} <br/>am {{uploaded}}</td>
        <td>
        <a id="del{{id}}" onclick="del({{id}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right red-text tooltipped anim" data-position="top" data-delay="50" data-tooltip="Löschen"><i class="mddi mddi-delete"></i></a>
        <a id="denydel{{id}}" onclick="denydelete({{id}})" href="#!" style="padding-left:10px;padding-right:10px;display:none;" class="btn-flat right red-text tooltipped anim"><i class="mddi mddi-close"></i></a>
        <a id="confdel{{id}}" onclick="confdelete({{id}})" href="#!" style="padding-left:10px;padding-right:10px;display:none;" class="btn-flat right green-text tooltipped anim"><i class="mddi mddi-check"></i></a>
        <a href="../{{filePath}}" target="_blank" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Öffnen"><i class="mddi mddi-open-in-new"></i></a>
        <a href="files.php?edit={{id}}" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Bearbeiten"><i class="mddi mddi-pencil"></i></a>
        </td>
    </tr>
    `;
let template = Handlebars.compile(listElemTmplt);
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
let searchString = "";
let currPage = 1;
let reqPage = 1;
let maxPages = 1;
let size = 0;
let sort = "ascID";
var data = "";
let currEdit = -1;
let files;
///////////////////////////////////////////////////////////////////////

function setPage(apage) {
    reqPage = apage;
}

function updatePages() {
    if(currPage > maxPages) reqPage = maxPages;
    if(reqPage == 0) reqPage = 1;

    let nextPage = parseInt(currPage)+1;
    let prevPage = currPage-1;
    let p = $("#pages");
    p.html("");
    p.append("<div id='pagesPre' class='col s1'></div>");
    p.append("<div id='pagesSuf' class='col push-s10 s1'></div>");
    p.append("<div id='pagesNum' class='col pull-s1 s10'></div>");

    if(currPage <= 1) $("#pagesPre").append("<li class=\"disabled\"><a><i class=\"material-icons\">chevron_left</i></a></li>");
    else $("#pagesPre").append("<li class=\"waves-effect\"><a onclick=\"setPage("+prevPage+")\"><i class=\"material-icons\">chevron_left</i></a></li>");

    for(let i = 1; i <= maxPages; i++) {
        if(i != currPage) {
            $("#pagesNum").append("<li class=\"waves-effect\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        } else {
            $("#pagesNum").append("<li class=\"active indigo\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        }
    }

    if(currPage >= maxPages) $("#pagesSuf").append("<li class=\"disabled\"><a><i class=\"material-icons\">chevron_right</i></a></li>");
    else $("#pagesSuf").append("<li class=\"waves-effect\"><a onclick=\"setPage("+nextPage+")\"><i class=\"material-icons\">chevron_right</i></a></li>");
}

function updateData() {
    let sort = $(sortName).val();
    let filter = " ";
    let search = searchString;
    if(searchString.startsWith("filter:")) {
        filter = searchString.replace("filter:","");
        search = "";
    }
    let postdata = {
        page: reqPage,
        sort: sort,
        search: search,
        pagesize: pagesize,
        filter: filter
    }
    $.getJSON(linkList,postdata, function(json) {
        maxPages = json['maxPage'];
        currPage = json['page'];
        size = json['size'];
        let list = json[jsonField];

        if(JSON.stringify(list) != data) {
            $(listName).html("");
            data = JSON.stringify(list);
            for(let i = 0; i < list.length; i++) {
                let e = list[i];
                e.i = i;
                $(listName).append(template(e));
                size = i;
            }

            animate(0);
        }
    });
}

function animate(i) {
    if(i <= size) {
        $("#row-"+i).fadeIn(150);
        window.setTimeout("animate("+(i+1)+")", 150);
        $('.tooltipped').tooltip({delay: 50});
    }
}

function updateCaller() {
    updateData();
    updatePages();
    window.setTimeout("updateCaller()", 1000);
}

///////////////////////////////////////
function backToList() {
    $("#newFileForm").fadeOut(200, function() {
        $("#filesList").fadeIn(200);
    });
    currEdit = -1;
}

function newFile() {
    resetFormElement($("#new-name"));
    resetFormElement($("input[type=file]"));
    $("input[type=file]").on("change", (event) => files = event.target.files)
    $("#filesList").fadeOut(200, () => $("#newFileForm").fadeIn(200));
}

function submitNewFile() {
    let data = new FormData();
    $.each(files, function(key, value) {
        data.append(key, value);
    });

    data.append("name", $("#new-name").val());
    $.ajax({
        url: '../api/files/upload.php',
        type: 'POST',
        data: data,
        cache: false,
        dataType: 'json',
        processData: false, // Don't process the files
        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
        success: function(data, textStatus, jqXHR) {
            if(typeof data.error === 'undefined') {
                Materialize.toast("Datei hochgeladen", 1000, "green");
                backToList();
            } else Materialize.toast("Es ist ein Fehler aufgetreten?<br>"+data.error, 2000, "red");
        },
        error: (jqXHR, textStatus, errorThrown) => Materialize.toast("Es ist ein Fehler aufgetreten<br>"+textStatus, 2000, "red")
    });
}

function del(id) {
    $("#del"+id).hide();
    $("#confdel"+id).show();
    $("#denydel"+id).show();
}

function confdelete(id) {
    $("#del"+id).show();
    $("#confdel"+id).hide();
    $("#denydel"+id).hide();
    let data = {
        fID: id
    };
    $.getJSON("../api/files/delete.php", data, function(json) {
        if(json.success == true) {
            Materialize.toast("Artikel gelöscht", 1000, "green");
        } else {
            Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
        }
        updateData();
        updatePages();
    });
}

function denydelete(id) {
    $("#del"+id).show();
    $("#confdel"+id).hide();
    $("#denydel"+id).hide();
}
///////////////////////////////////////
function resetFormElement(e) {
    e.wrap('<form>').closest('form').get(0).reset();
    e.unwrap();
    Materialize.updateTextFields();
}

var delay = (function(){
    let timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

$(document).ready(function() {
    window.setTimeout("updateCaller()", 500);
    $("#filter").keyup(function () {
        delay(function(){
            searchString = $("#filter").val();
            data = "";
            reqPage = 1;
            updateData();
            updatePages();
        }, 500 );
    });
});