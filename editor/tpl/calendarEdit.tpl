{include file="base.tpl" args=$header}
<main class="row">
    <div class="col s12 m6 container row">
        <div class="card-panel col s12 container row">
            <div class="col s12 bolden indigo-text" style="margin-top: 15px;">
                Termin Meta-Daten
            </div>
            <input type="hidden" id="edit-cID" value="{$entry.id}"/>
            <div class="input-field col s6">
                <input id="edit-date" type="text" class="datetimepicker" value="{$entry.Tdate}">
                <label for="edit-date">Datum</label>
            </div>
            <div class="input-field col s6">
                <input id="edit-dateUntil" type="text" class="datetimepicker" value="{$entry.TdateUntil}">
                <label for="edit-dateUntil">Datum bis</label>
            </div>

            <div class="col s12 m4">
                <select id="edit-state">
                    <option value="" disabled {if $entry.state > 3 || $entry.state < -1}selected{/if}>Sichtbarkeit wählen</option>
                    <option value="-1"{if $entry.state == -1}selected{/if}>Gelöscht</option>
                    <option value="2" {if $entry.state == 2}selected{/if}>Nicht öffentlich</option>
                    <option value="1" {if $entry.state == 1}selected{/if} disabled>Warte auf Bestätigung</option>
                    <option value="0" {if $entry.state == 0}selected{/if}>Öffentlich</option>
                </select>
                <label for="edit-state">Sichtbarkeit</label>
            </div>
            <div class="col s12 m8">
                <a href="#!" class="btn blue right" onclick="save()">SPEICHERN</a>
                <a href="#!" class="btn-flat right" onclick="cancel()">ABBRECHEN</a>
            </div>
            <div class="col s12 bolden indigo-text" style="margin-top: 30px;">
                Termin bearbeiten
            </div>
            <div class="input-field col s12">
                <input id="edit-name" type="text" data-length="32767" value="{$entry.name}">
                <label for="edit-name">Titel</label>
            </div>
            <div class="col s12 blue lighten-3" style="padding: 2px 10px;">
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="bold()"><i class="mddi mddi-format-bold"></i></a>
                <a class="small btn-flat wifaves-effect waves-light" href="#!" onclick="italic()"><i class="mddi mddi-format-italic"></i></a>
                <!--<a class="small btn-flat waves-effect waves-light" href="#!" onclick=""><i class="mddi mddi-format-underline"></i></a>-->
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="strikeThru()"><i class="mddi mddi-format-strikethrough"></i></a>
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="header1()"><i class="mddi mddi-format-header-1"></i></a>
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="header2()"><i class="mddi mddi-format-header-2"></i></a>
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="header3()"><i class="mddi mddi-format-header-3"></i></a>
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="quote()"><i class="mddi mddi-format-quote"></i></a>
                <a class="small btn-flat waves-effect waves-light" href="#!" onclick="code()"><i class="mddi mddi-code-braces"></i></a>
                <a class="small btn-flat disabled" href="#!" onclick="pic()"><i class="mddi mddi-image"></i></a>
                <a class="small btn-flat disabled" href="#!" onclick="link()"><i class="mddi mddi-link"></i></a>
                <a class="small btn-flat right" href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet" target="_blank"><i class="mddi mddi-help"></i></a>
            </div>
            <div class="input-field col s12">
                <textarea id="edit-info" class="materialize-textarea" data-length="8388607" style="font-family: 'Roboto Mono'">{$entry.info}</textarea>
                <label for="edit-info">Info [Markdown aktiv]</label>
            </div>
        </div>
    </div>
    <div class="col s12 m6 container row">
        <div class="card-panel col s12 container row">
            <div class="col s12 bolden indigo-text" style="margin-top: 15px;">
                Vorschau
            </div>
            <div class="col s12 textstyles" id="preview-header">

            </div>
            <div class="col s12 textstyles" id="preview-text" style="width: 100%; min-height: 200px;">
                <br/><br/>
                <span class="placeholder-big" style="width: 80%;"></span><br/><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="margin-left: 20%; width: 80%;"></span><br/>
                <span class="placeholder-small light" style="margin-left: 20%; width: 80%;"></span><br/>
                <span class="placeholder-small light" style="margin-left: 20%; width: 80%;"></span><br/>
                <span class="placeholder-small light" style="margin-left: 20%; width: 80%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <span class="placeholder-small light" style="width: 100%;"></span><br/>
                <br/>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function() {
        $('input#edit-name, input#edit-header, input#edit-title, textarea#edit-text').characterCounter();
        $('select').material_select();
    });
</script>
<script src="js/calendarEdit.js"></script>
<script src="js/markdownEdit.js"></script>
{include file="end.tpl"}