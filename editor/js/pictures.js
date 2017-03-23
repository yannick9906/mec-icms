/**
 * Created by yanni on 2017-03-22.
 */

let sortName = "#sort";
let listName = "#articles"
let linkList = "../api/articles/getList.php";
let jsonField = "articles"
let pagesize = 12;
///////////////////////////////////////////////////////////////////////
// TODO Fill List Template and update() method
let listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;">
        <td>{{id}}</td>
        <td><b>{{name}}</b><br/>erstellt von {{authorReal}}</td>
        <td>Version {{version}} <i class="{{{stateCSS}}}"></i> <span class="{{{color}}}">{{stateText}}</span><br/>von {{lastEditAuthor}} - {{lastEdit}}</td>
        <td>
        <a onclick="pictures({{id}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Bilder"><i class="mddi mddi-image-multiple"></i></a>
        <a href="articles.php?edit={{vId}}" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Bearbeiten"><i class="mddi mddi-pencil"></i></a>
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
let sort = "dateDesc";
var data = "";
let currEdit = -1;
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
    let postdata = {
        page: reqPage,
        sort: sort,
        search: searchString,
        pagesize: pagesize
    }
    $.getJSON(linkList,postdata, function(json) {
        maxPages = json['maxPage'];
        currPage = json['page'];
        size = json['size'];
        let list = json[jsonField];

        if(JSON.stringify(list) != data) {
            $(listName).html("");
            for(let i = 0; i < list.length; i++) {
                let e = list[i];
                e.i = i;
                e.color = e.stateCSS.split(" ")[0];
                $(listName).append(template(e));
                size = i;
                delete e.i;
                delete e.color;
            }
            data = JSON.stringify(list);
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

function pictures(id) {
    currEdit = id;
    let data = {id: id};
    fillFileSelect($("select.fileSelect"),"img");
    $.getJSON("../api/pictures/detailsPerArticle.php",data, (json) => {
        $("#edit-name").val(json.name);
        $("#edit-fID").val(json.fIDs);
        $("#articleList").fadeOut(200, () => $("#editPicturesForm").fadeIn(200));
        Materialize.updateTextFields();
        $("select").material_select();
    });
}

function submitEditPictures() {
    let data = {
        id: currEdit,
        fIDs: $("#edit-fID").val()
    }
    $.post("../api/pictures/updatePerArticle.php",data,(json) => {
        json = JSON.parse(json);
        if(json.success == true) {
            Materialize.toast("Bilder gespeichert", 1000, "green");
            backToList();
        } else Materialize.toast("Es ist ein Fehler aufgetreten:<br/>"+json.error, 2000, "red");
    });
}

///////////////////////////////////////
function backToList() {
    $("#editPicturesForm").fadeOut(200, function() {
        $("#articleList").fadeIn(200);
    });
    currEdit = -1;
}

///////////////////////////////////////

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