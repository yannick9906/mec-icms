/**
 * Created by yanni on 2017-03-21.
 */

function fillArticleSelect(selector) {
    let data = {
        search: null,
        page: 1,
        pagesize: 9999,
        sort: "dateDesc"
    }
    $.getJSON("../api/articles/getList.php",data, (json) => {
        let selectItems = "<option value='' disabled selected>Wähle einen Artikel</option>";
        for(let i = 0; i < json.articles.length; i++) {
            let e = json.articles[i];
            selectItems += "<option value='"+e.id+"'>"+e.name+"</option>";
        }
        selector.html(selectItems);
        selector.material_select();
    });
}

function fillFileSelect(selector) {
    let data = {
        search: null,
        page: 1,
        pagesize: 9999,
        sort: "dateDesc"
    }
    $.getJSON("../api/files/getList.php",data, (json) => {
        let selectItems = "<option value='' disabled selected>Wähle eine Datei</option>";
        for(let i = 0; i < json.files.length; i++) {
            let e = json.files[i];
            selectItems += "<option value='"+e.id+"'>"+e.fileName+"</option>";
        }
        selector.html(selectItems);
        selector.material_select();
    });
}


$(document).ready(function() {
    fillArticleSelect($("select.articleSelect"));
    fillFileSelect($("select.fileSelect"));
});