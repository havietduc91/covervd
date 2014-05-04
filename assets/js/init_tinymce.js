$(document).ready(function (){
    //Text editor
    tinymceConfigs = 
    {
        mode : "specific_textareas",
        editor_selector : "isEditor",
        plugins : "media,table,fullscreen",
        setup : function(ed) {
            //var $ed = ed;
            if($(ed.getElement('class')).hasClass('full'))
            {
                ed.settings.plugins += ",table";
                ed.settings.theme_advanced_buttons1 += ",mce_media,table";
                // Create an alternate "insert media" button
                ed.addButton ('mce_media', {
                    'title' : 'Embed a video',
                    'image' : CL.SAND_ASSETS_CDN + '/images/youtube.gif',
                    'onclick' : function () {
                        var url = prompt('Please enter the embed code for your video:','');
                        var code, regexRes;
                        if(url == null)
                            return;
                        regexRes = url.match("[\\?&]v=([^&#]*)");
                        code = (regexRes === null) ? "" : regexRes[1];
                        if (code === "") { return; }
                        
                        flash_mov = "http://www.youtube.com/v/" + code + "?version=3&f=videos";
                        embedHTML = '';
                        embedHTML += '<object>';
                        embedHTML += '<param name="movie" value="'+flash_mov+'"></param>';
                        embedHTML += '<param name="allowFullScreen" value="true"></param>';
                        embedHTML += '<param name="allowscriptaccess" value="always"></param>'; 
                        embedHTML += '<param name="movie" value="' + flash_mov + '" />';
                        embedHTML += '<param name="wmode" value="transparent" />';
                        embedHTML += '<embed src="' + flash_mov + '" type="application/x-shockwave-flash" wmode="transparent" type="application/x-shockwave-flash" width="530" height="315" allowscriptaccess="always" allowfullscreen="true"></embed></object>';
                        
                        //ed.execCommand('mceInsertContent', false, '<img src="http://img.youtube.com/vi/' + code + '/0.jpg" class="mceItem" alt="' + code + '"/>');
                        ed.execCommand('mceInsertContent', false, embedHTML);
                    }
                });
                
            }
            
            ed.onLoad.add(function(ed, e){
                if($(ed.getElement('class')).hasClass('image_canvas')) {
                    html_to_canvas(ed.getContent());
                }
            });
            
            ed.onChange.add(function(ed, e) {
                //console.debug('Key up event: ' + e.keyCode);
                if($(ed.getElement('class')).hasClass('image_canvas')) {
                    html_to_canvas(ed.getContent());
                }
            });
        },
        formats : {
            underline : {inline : 'u', exact : true},
            strikethrough : {inline : 'del'},
        },
        style_formats: [
            {title: 'Heading 1',block: 'h1', exact: true},
            {title: 'Heading 2',block: 'h2', exact: true},
            {title: 'Heading 3',block: 'h3', exact: true},
            {title: 'Heading 4',block: 'h4', exact: true},
            {title: 'Heading 5',block: 'h5', exact: true},
            {title: 'Heading 6',block: 'h6', exact: true},
        ],
        font_size_style_values : "xx-small,x-small,small,medium,large,x-large,xx-large",
        content_css : CL.ASSETS_CDN + "/css/tinymce.css", 
        imagemanager_contextmenu: true,
        theme_advanced_toolbar_align : "left",
        theme_advanced_resizing : true,
        theme_advanced_buttons1 : "bold,italic,underline,strikethrough,bullist,numlist,|,styleselect,fontsizeselect,|" + 
            ",forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull",
        theme_advanced_buttons2 : "link,unlink,|,undo,redo,|,image,media,|,table,|,cleanup,removeformat,newdocument,|,|,fullscreen",
        theme_advanced_buttons3 : "",
        theme : 'advanced',
        width : "100%",
        height: "200",
        entity_encoding : "raw",
        
        force_br_newlines : false,
        force_p_newlines : true,
        forced_root_block : 'div',
        remove_linebreaks: true,
        convert_newlines_to_brs: false,
        remove_trailing_brs: true,
        
        relative_urls : false,
        file_browser_callback : MadFileBrowser,
        extended_valid_elements: "iframe[src|title|width|height|allowfullscreen|frameborder|div]"
    };  
    tinyMCE.init(tinymceConfigs);
    //console.log(tinyMCE.minorVersion);
    
    function MadFileBrowser(field_name, url, type, win) {
          tinyMCE.activeEditor.windowManager.open({
              file : "/mfm.php?field=" + field_name + "&url=" + url + "",
              //file: "/file/master/file-manager?field=" + field_name + "&url=" + url + "",
              title : 'File Manager',
              width : "640",
              height : "450",
              resizable : "no",
              inline : "yes",
              close_previous : "no"
          }, {
              window : win,
              input : field_name
          });
          return false;
    }
    
});
