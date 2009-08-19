$(document).ready(function(){

    var loadInIframeModal = function(hash){
               
        var var_dictionaries = $.trim($('#gurucore_dictionary_dictionaries').val());
        var var_word = $.trim($('#gurucore_dictionary_word').val());                
        if(var_dictionaries == '' || var_word == '')
        {
            alert('You have to type your word to translate');            
        }
        else
        {        
            var $trigger = $(hash.t);
            var $modal = $(hash.w);
            
            var myUrl = '/GAction/dictionary_translate.php?width=90%&height=99%&jqmRefresh=false' 
            + '&dic='+var_dictionaries + '&word='+var_word;;
                    
            var myTitle= $trigger.attr('title');
            var $modalContent = $("iframe", $modal);
            $modalContent.html('').attr('src', '').attr('src', myUrl);
            //let's use the anchor "title" attribute as modal window title
            $('#jqmTitleText').text(myTitle);
            $modal.fadeIn();
        }
    }
        
    // initialise jqModal
    $('#modalWindow').jqm({
        modal: false,
        trigger: 'a.thickbox',
        target: '#jqmContent',
        onShow:  loadInIframeModal        
    });    
    
    
    $("#gurucore_dictionary_popup").submit(function() {
        $('#modalWindow').jqmShow();
        return false;
    });

});