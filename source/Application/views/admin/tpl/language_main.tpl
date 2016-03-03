[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<script type="text/javascript">
<!--
window.onload = function ()
{
    [{if $updatelist == 1}]
        top.oxid.admin.updateList('[{$oxid}]');
    [{/if}]
    var oField = top.oxid.admin.getLockTarget();
    oField.onchange = oField.onkeyup = oField.onmouseout = top.oxid.admin.unlockSave;
}

function onSelect_country(country){
    var language_main_languageselect = document.getElementById('language_main_languageselect');
    var languages_by_country = document.getElementById('languages_' + country);
    var selects = language_main_languageselect.getElementsByTagName('select');

    language_main_languageselect.style = "";
    for (i = 0; i < selects.length; i++) {
        selects[i].style = 'display:none';
    }
    if ('--' == country) {
        language_main_languageselect.style = 'display:none';
    } else {
        languages_by_country.style = "";
    }
}

function onSelect_language(language, country, name){
    var languages = [{$smarty_languages}];
    var description = document.getElementById('editval[desc]');
    var abbreviation = document.getElementById('oLockTarget');
    abbreviation.value = languages[country][language]['abbreviation'];
    if ('' == abbreviation.value) {
        name = '';
    }
    description.value = name;
}

//-->
</script>

[{if $readonly}]
    [{assign var="readonly" value="readonly disabled"}]
[{else}]
    [{assign var="readonly" value=""}]
[{/if}]

<form name="transfer" id="transfer" action="[{$oViewConf->getSelfLink()}]" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="oxid" value="[{$oxid}]">
    <input type="hidden" name="oxidCopy" value="[{$oxid}]">
    <input type="hidden" name="cl" value="language_main">
    <input type="hidden" name="language" value="[{$actlang}]">
</form>

<form name="myedit" id="myedit" action="[{$oViewConf->getSelfLink()}]" method="post">
[{$oViewConf->getHiddenSid()}]
<input type="hidden" name="cl" value="language_main">
<input type="hidden" name="fnc" value="">
<input type="hidden" name="oxid" value="[{$oxid}]">
<input type="hidden" name="voxid" value="[{$oxid}]">
<input type="hidden" name="editval[oxid]" value="[{$oxid}]">
<input type="hidden" name="language" value="[{$actlang}]">


<table cellspacing="0" cellpadding="0" border="0" width="98%">
<tr>
    <td valign="top" class="edittext">

        <table cellspacing="0" cellpadding="0" border="0">
        [{block name="admin_language_main_form"}]

            [{if '-1' == $oxid}]
            <tr id="countryselect">
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_COUNTRY"}]
                </td>
                <td class="edittext">
                    <select class="editinput" name="editval[language_country]" id="language_country" [{$readonly}] onchange="javascript:onSelect_country(this.value, this.options[this.options.selectedIndex].innerHTML)" >
                        [{foreach from=$languagecountries item=country key=key}]
                        <option value="[{$key}]">[{$country}]</option>
                        [{/foreach}]
                    </select>
                </td>
            </tr>
            <tr id="language_main_languageselect" style="display:none">
                <td class="edittext">
                    [{oxmultilang ident="GENERAL_LANGUAGE_NAME"}]
                </td>
                <td class="edittext">
                    [{foreach from=$languagecountries item=country key=currentcountry}]
                        <select style="display:none" class="editinput" name="editval[language]" id="languages_[{$currentcountry}]" [{$readonly}] onchange="javascript:onSelect_language(this.value,'[{$currentcountry}]',this.options[this.options.selectedIndex].innerHTML)" >
                            [{foreach from=$languages.$currentcountry item=item key=key}]
                            <option value="[{$key}]" name="[{oxmultilang ident=$item.language}]">[{oxmultilang ident=$item.language}]</option>
                            [{/foreach}]
                        </select>
                    [{/foreach}]
                </td>
            </tr>
           [{/if}]

            <tr>
                <td class="edittext" width="120">
                [{oxmultilang ident="LANGUAGE_ACTIVE"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[active]" value='1' [{if $edit.active == 1}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_ACTIVE"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_ABBERVATION"}]
                </td>
                <td class="edittext">
                    <input type="text" class="editinput" size="20" maxlength="10" id="oLockTarget" name="editval[abbr]" value="[{$edit.abbr}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_ABBERVATION"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_DESCRIPTION"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="50" id="editval[desc]" name="editval[desc]" value="[{$edit.desc}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_DESCRIPTION"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_DEFAULT"}]
                </td>
                <td class="edittext">
                <input class="edittext" type="checkbox" name="editval[default]" value='1' [{if $edit.default}]checked[{/if}] [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_DEFAULT"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_BASEURL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="255" name="editval[baseurl]" value="[{$edit.baseurl}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_BASEURL"}]
                </td>
            </tr>
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_BASESSLURL"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="40" maxlength="255" name="editval[basesslurl]" value="[{$edit.basesslurl}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_LANGUAGE_BASESSLURL"}]
                </td>
            </tr>
            [{if $oxid != -1}]
            <tr>
                <td class="edittext">
                [{oxmultilang ident="LANGUAGE_LANGUAGEID"}]
                </td>
                <td class="edittext">
                    [{$edit.baseId == 1}]
                </td>
            </tr>
            [{/if}]
            <tr>
                <td class="edittext">
                [{oxmultilang ident="GENERAL_SORT"}]
                </td>
                <td class="edittext">
                <input type="text" class="editinput" size="5" maxlength="5" name="editval[sort]" value="[{$edit.sort}]" [{$readonly}]>
                [{oxinputhelp ident="HELP_GENERAL_SORT"}]
                </td>
            </tr>
        [{/block}]
        <tr>
            <td class="edittext"><br><br>
            </td>
            <td class="edittext"><br><br>
            <input type="submit"  [{if !$edit.abbr}]disabled[{/if}] class="edittext" id="oLockButton" name="saveArticle" value="[{oxmultilang ident="GENERAL_SAVE"}]" onClick="Javascript:document.myedit.fnc.value='save'"" [{$readonly}]><br>
            </td>
        </tr>
        </table>
    </td>

    </tr>
</table>

</form>

[{include file="bottomnaviitem.tpl"}]

[{include file="bottomitem.tpl"}]
