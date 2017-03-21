{include file="base.tpl" args=$header}
<main>
    <div class="container row" id="downloadsList">
        <form class="col s12 m9" method="post" action="" id="live-search">
            <div class="row" id="search">
                <div class="input-field col s12 ">
                    <i class="mddi mddi-magnify prefix grey-text text-darken-2"></i>
                    <input id="filter" type="text" class="validate">
                    <label for="filter">In Downloads suchen ...</label>
                </div>
            </div>
        </form>
        <div class="input-field col s6 m2 offset-m1">
            <select id="sort">
                <option value="idAsc">ID aufstg.</option>
                <option value="idDesc">ID abstg.</option>
                <option value="nameAsc">Name aufstg.</option>
                <option value="nameDesc">Name abstg.</option>
            </select>
            <label>Sortieren nach</label>
        </div>
        <ul id="pages" class="pagination col s12 center center-align">
        </ul>
        <table class="highlight col s12">
            <thead>
            <tr>
                <th data-field="id" width="50px">ID</th>
                <th data-field="name" width="40%">Name</th>
                <th data-field="email" width="30%">Details</th>
                <th data-field="actions" width="20%"></th>
            </tr>
            </thead>
            <tbody id="downloads">
            {for i 0 10}
                <tr>
                    <td><span class="placeholder-big light" style="width: 30px;"></span></td>
                    <td><span class="placeholder-small" style="width: 100%;"></span><br/><span class="placeholder-small light" style="width: 100%;"></span></td>
                    <td><span class="placeholder-small light" style="width: 100%;"></span><br/><span class="placeholder-small light" style="width: 100%;"></span></td>
                    <td>
                        <span class="placeholder-big right" style="width: 30px;"></span>
                        <span class="placeholder-big right" style="width: 30px;"></span>
                        <span class="placeholder-big right" style="width: 30px;"></span>
                    </td>
                </tr>
            {/for}
            </tbody>
        </table>
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large waves-effect waves-light blue" onclick="newDownload();">
                <i class="mddi mddi-network-download"></i>
            </a>
        </div>
    </div>
    <div class="container" id="newDownloadForm" style="display: none;">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 bolden indigo-text">
                    Neuen Download erstellen
                </div>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s12 m4">
                            <input id="new-name" type="text">
                            <label for="new-name">Name</label>
                        </div>
                        <div class="input-field col s12 m4">
                            <select class="articleSelect" id="new-aID">
                                <option value="" disabled selected>Wähle einen Artikel</option>
                                <option disabled>Lade Artikelliste...</option>
                            </select>
                            <label>Artikel</label>
                        </div>
                        <div class="input-field col s12 m4">
                            <select class="fileSelect" id="new-fID">
                                <option value="" disabled selected>Wähle eine Datei</option>
                                <option disabled>Lade Dateiliste...</option>
                            </select>
                            <label>Datei</label>
                        </div>
                    </div>
                </form>
                <div class="col s12">
                    <a class="waves-effect waves-light btn indigo right" onclick="submitNewDownload();">erstellen</a>
                    <a class="waves-effect waves-red btn-flat right" onclick="backToList();">abbrechen</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="editDownloadForm" style="display: none;">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 bolden indigo-text">
                    Download bearbeiten
                </div>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s12 m4">
                            <input id="edit-name" type="text">
                            <label for="edit-name">Name</label>
                        </div>
                        <div class="input-field col s12 m4">
                            <select class="articleSelect" id="edit-aID">
                                <option value="" disabled selected>Wähle einen Artikel</option>
                                <option disabled>Lade Artikelliste...</option>
                            </select>
                            <label>Artikel</label>
                        </div>
                        <div class="input-field col s12 m4">
                            <select class="fileSelect" id="edit-fID">
                                <option value="" disabled selected>Wähle eine Datei</option>
                                <option disabled>Lade Dateiliste...</option>
                            </select>
                            <label>Datei</label>
                        </div>
                    </div>
                </form>
                <div class="col s12">
                    <a class="waves-effect waves-light btn indigo right" onclick="submitEditDownload();">speichern</a>
                    <a class="waves-effect waves-red btn-flat right" onclick="backToList();">abbrechen</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function() {
        $('select').material_select();
    });
</script>
<script src="js/downloads.js"></script>
<script src="js/selectFields.js"></script>
{include file="end.tpl"}