/**
 * @file   modules/newsletter/tpl/js/newsletter_admin.js
 * @author Bajanica Bogdan-Dionisie (dionisrom@gmail.ro)
 * @brief  newsletter module admin javascript functions
 **/

/* List Item setup */
function getMembersType()
{
    chosen = "";
    element = document.getElementsByName("selMembersForNewsletter");
    len = element.length;

    for (i = 0; i <len; i++) 
    {
        if (element[i].checked) 
        {
            chosen = element[i].value;
        }
    }

    if (chosen == "") 
    {
        return false;
    }
    else 
    {
        return chosen; 
    }
}

function doInsertItem() {
    var target_obj = xGetElementById('targetItem');
    var display_obj = xGetElementById('members'+getMembersType());
    if(!target_obj || !display_obj) return;

    var text = target_obj.options[target_obj.selectedIndex].text;
    var value = target_obj.options[target_obj.selectedIndex].value;

    for(var i=0;i<display_obj.options.length;i++) if(display_obj.options[i].value == value) return;

    var obj = new Option(text, value, true, true);
    display_obj.options[display_obj.options.length] = obj;

}
function doDeleteItem() {
    var sel_obj = xGetElementById('members'+getMembersType());
    var idx = sel_obj.selectedIndex;
    if(idx<0 || sel_obj.options.length<2) return;
    sel_obj.remove(idx);
    sel_obj.selectedIndex = idx-1;
}
function doMoveUpItem() {
    var sel_obj = xGetElementById('members'+getMembersType());
    var idx = sel_obj.selectedIndex;
    if(idx<1 || !idx) return;

    var text = sel_obj.options[idx].text;
    var value = sel_obj.options[idx].value;

    sel_obj.options[idx].text = sel_obj.options[idx-1].text;
    sel_obj.options[idx].value = sel_obj.options[idx-1].value;
    sel_obj.options[idx-1].text = text;
    sel_obj.options[idx-1].value = value;
    sel_obj.selectedIndex = idx-1;
}
function doMoveDownItem() {
    var sel_obj = xGetElementById('members'+getMembersType());
    var idx = sel_obj.selectedIndex;
    if(idx>=sel_obj.options.length-1) return;

    var text = sel_obj.options[idx].text;
    var value = sel_obj.options[idx].value;

    sel_obj.options[idx].text = sel_obj.options[idx+1].text;
    sel_obj.options[idx].value = sel_obj.options[idx+1].value;
    sel_obj.options[idx+1].text = text;
    sel_obj.options[idx+1].value = value;
    sel_obj.selectedIndex = idx+1;
}

function getNewsletterLists()
{
    var sel_obj = jQuery('*[name*="members"]');
    var list = new Array();
    var id = null;
    lists = "";
    sel_obj.each(function(){
        var id = jQuery(this).attr("id");
        list[id] = new Array();
        for(var i=0; i<document.getElementById(id).options.length; i++)
        {
            list[id][i] = document.getElementById(id).options[i].value;
        }
        lists += list[id].join(",")+"|";
    })
    return lists;
}

function doSaveListConfig() 
{
    jQuery('#newsletter_lists').val(getNewsletterLists());
    document.forms[0].submit();
}

function doSaveAndSendListConfig()
{
    jQuery('#newsletter_lists').val(getNewsletterLists());
    jQuery('#act').attr('value','procNewsletterAdminInsertAndSend');
    document.forms[0].submit();
}

function doUpdateListConfig()
{
    jQuery('#newsletter_lists').val(getNewsletterLists());
    jQuery('#act').val('procNewsletterAdminUpdate');
    document.forms[0].submit();
}

function doUpdateAndSendListConfig()
{
    jQuery('#newsletter_lists').val(getNewsletterLists());
    jQuery('#act').val('procNewsletterAdminUpdateAndSend');
    document.forms[0].submit();
}

function doFormWithDetails(news_srl)
{
    jQuery('#news_srl').attr('value',news_srl);
    document.forms[0].submit();
}

function doSendNewsletter(news_srl)
{
    jQuery('#news_srl_send').attr('value',news_srl);
    document.forms[1].submit();
}

function doSendAgainNewsletter(news_srl)
{
    jQuery('#news_srl_resend').attr('value',news_srl);
    document.forms[2].submit();
}

function validateFileUpload(f)
{
    var filename = jQuery('#newsletter_attach').val();
    if(/\.(gif|jpg|jpeg|gif|png|swf|flv|doc|docx|pdf|xls|xlsx|txt|csv)$/i.test(filename)){
        return true;
    }else{
        alert('only image, documents and flash file');
        jQuery(f).attr({ value: '' });
        return false;
    }
}

jQuery(document).ready(function(){
    jQuery('input[name="selMembersForNewsletter"]')
        .click(function(){
            jQuery("div[id*=divSel]").addClass("hidden");
            jQuery("#div" + jQuery(this).val()).removeClass("hidden");
        })
    jQuery('div.button_send, div.button_resend, div.buttonSet')
        .mouseover(function(){
            jQuery(this).css('cursor','pointer');
        })
})
