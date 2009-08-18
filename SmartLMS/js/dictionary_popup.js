$(document).ready(function(){
    $form_dic = $('#gurucore_dictionary_popup');        
    $btnTranslate = $('#gurucore_dictionary_translate');    
    
    $btnTranslate.click(function(){
            
        var var_dictionaries = $.trim($('#gurucore_dictionary_dictionaries').val());
        var var_word = $.trim($('#gurucore_dictionary_word').val());
                
        if(var_dictionaries == '' || var_word == '')
        {
            show_notice_msg('You have to type your word to translate');            
            return false;
        }
                
        $.get(
            ROOT_URL + '/GAction/dictionary_translate.php',
            {
                dic: var_dictionaries,
                word: var_word
            },
            
            function(data)
            {
                // show_notice_msg(data);
                alert(data);
            }
        );
                        
        return false;        
    });
});