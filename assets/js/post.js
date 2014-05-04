$(function()
{
    if (is_posting())
    {
        $('#url').focusout(function(){
                var value=$(this).val();
                var step=$('#_cl_step').attr('value');
                
                if(value==''){
                }
                else{
                    if(step=='video'){
                        var you_id= getYoutubeIdFromUrl(value);
                        if(you_id == 'none'){
                            alert_error('Your Youtube link is not supported');
                        }
                        else{
                            $('#ytid').attr({value:you_id});
                            //Duc TODO: preview video
                        }
                    }   
                    
                }
            }
        );
        
        $(document).on('click', "a.choose_images, a.choose_flash",function(){
            if(!$(this).hasClass('add_url')) {
                if($(this).hasClass('choose_images')) {
                    $("#upload_img").closest('.btn-upload-container').find("input[type=file]").trigger('click');
                }
                else {
                    $("#upload_flash-element .btn-upload-container input[type=file]").trigger('click');
                }
            }
            else {
                $(this).text('Or upload files');
                $("#url").show().after($(this).eq(0));
                $("#url-label").show();
                $('#upload_img, #upload_flash, #upload_img-label, #upload_img-element, #upload_flash-label, #upload_flash-element').hide();
                $(this).removeClass('add_url');
            }
            
        });

        // upload mutil images
        var upload_story_images = function(file, $obj){
            var str = "";
            var previewStr = "";
            if(file.success) {
                var fileList = file.result.attachments;
                var images = [];
                $.each(fileList, function(i, val){
                    //str += "<div class='span1' style='position:relative;margin:10px;'><a href='javascript:;' id='" +val.id+ "' class='remove_img' style='position:absolute;top:-10px;right:-10px;'><i class='icon-remove'></i></a><img src='" + img + "' class='thumbnail' style='width:60px;height:60px;'/></div>";
                    str += "<div class='span1' style='position:relative;margin:10px;'><a href='javascript:;' data-id='" +val.id+ "' class='remove_img' style='position:absolute;top:-10px;right:-10px;'><i class='icon-remove'></i></a><img src='" + val.link + "' class='thumbnail' style='width:60px;height:60px;'/></div>";
                    previewStr += "<div><img src='" + val.link + "' data-id='" + val.id + "'/></div>";
                    images.push({id:val.id, ext : val.ext});
                });
                
                var imgObj = [];
                imgObj = images;
                if($("#images").val() != '') {
                    var currentList = JSON.parse($("#images").val());
                    if(currentList.length > 0) {
                        imgObj = $.merge(currentList, images);
                    }
                }
                
                $("#images").val(JSON.stringify(imgObj));
            }
            str += "<div class='clearfix'></div>";
            $obj.closest('form').find("#url-element").show().append(str);
            $("#preview_area").append(previewStr);
            
            var $ul = "<ul class='nav nav-list'>";
            $.each($("#upload_img-label a"), function(i, elm){
                var id = $(elm).attr('id');
                img = $(elm).parent().find('img').attr('src');
                $ul += "<li id='" +id+ "' style='position:relative;'><img src='" + img + "'/></li>";
            });
            str += "<div class='clearfix'></div>";
            $ul += "</ul>";
            $("#upload_img").closest('form').find("#upload_img-label").html(str);
            $(".images").html($ul);
            //genCanvas();
            /*disable for now. TODO: put it back
            $("#preview_area .images ul").sortable({
                update: function(e, ui) {
                    genCanvas();
                }
            });
            */
        };
        
        
        var upload_story_game_flash = function(file, $obj) {
            var str = "";
            if(file.success) {
                var file = file.result.attachments[0];
                img = "/ufiles/flash/" + file.id + "." + file.ext;
                str += "<div class='span1' style='position:relative;margin:10px;'><a href='javascript:;' id='" +file.id+ "' class='remove_img' style='position:absolute;top:-10px;right:-10px;'><i class='icon-remove'></i></a><img src='" + img + "' class='thumbnail' style='width:60px;height:60px;'/></div>";
                $("#url").val(file.id + "." + file.ext);
            }
            str += "<div class='clearfix'></div>";
            $obj.closest('form').find("#upload_flash-label").append(str);   
        };
        
        
        $(document).on('click', "a.remove_img", function(){
            var id = $(this).attr('id');
            var that = $(this);
            // remove image when newAction
            if(!that.hasClass('dom_remove')) {
                $.ajax({
                   url : "/file/index/delete",
                   data : {fileId : id},
                   dataType: 'json',
                   success: function(jsonData) {
                       if(jsonData.success) {
                           that.parent().remove(); 
                            var images = $("#images").val();
                            $img = JSON.parse(images);
                            var newImg = [];
                            $.each($img, function(i, v){
                               if(v.id != id) {
                                   newImg.push(v);
                               } 
                            });
                            $("#images").val(JSON.stringify(newImg));
                       }
                   }
                });
            }
            else { // update image action
                var idsDel = "";
                var imgDel = $("#images_deleted").val();
                if(imgDel.length > 0) {
                    idsDel += imgDel;
                }
                idsDel += that.attr('id') + ",";
                $("#images_deleted").val(idsDel);

                that.parent().remove(); 
                var newImg = [];
                var images = $("#images").val();
                $img = JSON.parse(images);
                $.each($img, function(i, v){
                   if(v.id != id) {
                       newImg.push(v);
                   } 
                });
                $("#images").val(JSON.stringify(newImg));
            }
        });
        
        $("#upload_img").cl_upload({
            url : "/story/upload-image",
            callback: upload_story_images,
            params: {
                isWM: true,
                resize: true,
                type: 'image'
            }
        });

        $("#upload_flash").cl_upload({
            url : "/story/upload-flash",
            callback: upload_story_game_flash,
            params: {folder : 'flash'}
        });
        
        //Default value
        if($('#_cl_step').attr('value')== ""){
            $('#_cl_step').attr({value:'story'});
            $('#type').attr({value:'1'});
            /**Handling default loading of post Story**/
            $('#url').hide();
            $('#url-label').hide();
            
        }
        $('.story-type-nav li.active a').trigger('click');
    } //end is posting
    
});