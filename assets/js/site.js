//Custom js files for site
/** Author: Chungth ; email: huychungtran@gmail.com**/

//pushState
var stateObj = { foo: "bar" };
var storyCache = {};
var nextOrPrev = 'next';
var getCookie,checkCookie;
var init_fb_js_for_page_view, init_fb_js_for_page_list;
var vote_store, fix_social_header;

// Load the SDK asynchronously
$(document).ready(function(){
	init_fb_js_for_page_view = function()
	{
		/*
		window.fbAsyncInit = function () {
	        FB.init({ 
	            status: false,
	            cookie: false, 
	            xfbml: true,
	            
	        });
		*/
	        //Additional
	        
	    //};     
	}
	
	init_fb_js_for_page_list = function()
	{
	  
	}
	/*
	(function(d, s, id){
		   var js, fjs = d.getElementsByTagName(s)[0];
		   if (d.getElementById(id)) {return;}
		   js = d.createElement(s); js.id = id;
		   js.src = "//connect.facebook.net/en_US/all.js";
		   fjs.parentNode.insertBefore(js, fjs);
		 }(document, 'script', 'facebook-jssdk'));
	*/
});
$(document).ready(function() {
	$.ajaxSetup({ cache: true });
	$.getScript('//connect.facebook.net/en_UK/all.js', function(){
		FB.init({
			appId: CL.FB_APP_ID,
			channelUrl: 'hoibi.net',
			  status: false,
	          cookie: false, 
	          xfbml: true,
	            
		});     
		var init_comment_cb = function()
		{
			FB.Event.subscribe('comment.create',
		            function (response) {
		                $.ajax({
		                    url : "/story/new-fb-comment",
		                    data : 
		                    {
		                        id  : CL.nid,
		                        url : window.location.href
		                    }
		                });
		            }
		         );
		        FB.Event.subscribe('comment.remove',
		            function (response) {
		               $.ajax();
		            }
	        );
		};
		
		var init_like_cb = function()
		{
			 FB.Event.subscribe('edge.create',
	            function (url) {
		 			//from URL : http://fun.local/truyen-cuoi/440-123123-2.html
			     //TF : http://fun.local/quiz/757.html
		 			//TODO: check for fun & thaifun url
			        if(CL.DOMAIN == 'thaifun.net')
			            var re = new RegExp('(.*)\/([0-9]*).html$');
			        else
			            var re = new RegExp('(.*)\/([0-9]*)-([^\]*)\.html$');
		 			var tmp = url.match(re);
		 			if (tmp.length >= 2)
	 				{
		 				$.ajax({
		 					url : "/story/new-fb-like",
		 					data : 
		 					{
		 					    rt : 1, //voteup
		 						iid  : tmp[2], //9440
		 						url : url//http://hoibi.net/anh-vui/9440-1.htm
		 					},
		 					success:function(){
		 						count=$( "#total-vote-" + tmp[2] ).text();
		 						$( "#total-vote-" + tmp[2]).text(parseInt(count)+1);
		 					}
		 					
		 				});
	 				}
	            }
	        );
	        FB.Event.subscribe('edge.remove',
        		function (url) {
	 			//from URL : http://fun.local/truyen-cuoi/440-123123-2.html
	 			//TODO: check for fun & thaifun url
	            if(CL.DOMAIN == 'thaifun.net')
                    var re = new RegExp('(.*)\/([0-9]*).html$');
                else
                    var re = new RegExp('(.*)\/([0-9]*)-([^\]*)\.html$');
	 			var tmp = url.match(re);
	 			if (tmp.length >= 2)
 				{
	 				$.ajax({
	 					url : "/story/new-fb-like",
	 					data : 
	 					{
	 					    rt : 4,
	 						iid  : tmp[2],
	 						url : url
	 					},
	 					success:function(){
	 						count=$( "#total-vote-" + tmp[2] ).text();
	 						$( "#total-vote-" + tmp[2]).text(parseInt(count)-1);
	 					}
	 				});
 				}
            }
            );
		};
		
		if (CL.page == 'story/index/view')
		{
			init_comment_cb();
		}
		init_like_cb();
	});
});
/*

function checkCookieVote()
{
	var uid = getCookie("_cl_uid");
	if (uid ==null || uid =="" || uid == 0 || uid =="undefined")
	  { 
		window.location.href = "/user/login";
	  }
	else
		return true;
}
function checkCookie()
{
	var uid = getCookie("_cl_uid");
	if (uid ==null || uid =="" || uid == 0 || uid =="undefined")
	  { 
		window.location.href = "/user/login";
	  }
	else
		window.location.href = "/rage/maker.php";
}
*/




//isBack: if this is a result of a "back" button in the browser being clicked
function change_history_url(title, url, isBack)
{
   if (!isBack)
	   history.pushState(stateObj, title, url);
   window.document.title = title;
}

function is_posting(){
    if(CL.page == 'story/index/new' || CL.page == 'story/index/update')
        return true;
    else 
        return false;
    
}
function story_type_int(type)
{
    var conf = {
       'story' : 1,
       'image' : 2,
       'video' : 3,
       'quiz' : 4,
       'flash-game' : 5,
       'link' : 6,
       'quote' : 7,
       'multi-choice' : 8
    };
    
    if (typeof conf[type] != 'undefined')
        return conf[type];
    else 
        return 1;
}
function vote_store()
{
	var list = new Array();
	var uiid = getCookie('_cl_uid');
	if (uiid != 0)
	{
		list = JSON.parse(localStorage.getItem(uiid));
		if(list != null)
		{
			$.each(list,function(index,value){
				if(value == true)
				{
					if(index.match(/vote-up/g))
						$("#"+index).find('.vote-up-btn').css("color","#357EBD");
					else if(index.match(/vote-down/g))
						$("#"+index).find('.vote-down-btn').css("color","#B23232");
				}
			});
		}
	}
}
$(function()
	{ // document ready
	/*Resize embedded video*/
	$(".first-video").fitVids();
	$(".story-wrapper").fitVids();
	$(".featured").fitVids();
	/**end resize**/
	
	/** jquery lazyload* */
	$("img.lazy").show().lazyload({
		effect : "fadeIn",
		threshold : 200
	});
	/** end jquery lazyload* */


	/*
	if (is_posting())
    {
	    $('#myTab a').click(function (e) {
	        e.preventDefault();
	        $(this).tab('show');
	    });
    }
    */
	
	/**dropdown nav**/
	$('.dropdown-toggle').dropdown();

    populate_username();
    populate_view_counter();
    
    if($.fn.timeago)
    	$(".timeago").timeago();
    
  //redraw captcha
	$("#captcha-id").before("<a href='javascript:;' class='redraw_captcha' title='Refresh image'> <i class='icon-refresh'></i> <span class='redraw_captcha_text'>" + t('refresh_captcha') + "</span></a>");
	$("#captcha-input").closest('form').on('click', 'a.redraw_captcha', function(e){
		that = $(this);
		$.ajax({
			url : '/captcha.php?captcha=Image',
			dataType: 'json',
			success: function(jsonData)
			{
				if(jsonData.success) {
					that.prev('img').replaceWith(jsonData.result.image);
					$("#captcha-id").attr('value', jsonData.result.sessId);
				}
			}
		});
	});
	
	
	 $('.has-tooltip').tooltip({});

	 $("#show-answer").click(function(e){
	     if (is_guest())
         {
	         alert(t("login_first"));
         }
	     else 
         {
	         $("#answer").toggle();
         }
	     e.preventDefault();
	     return false;
	 });
});
/** End * */


if (CL.APPLICATION_ENV != 'development')
{
	/*
	/**  ----- Social button JS section -----------------**/
	window.___gcfg = {lang: 'vi'};

	(function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	})();
}	
/** ---utility function ---**/
/* Resize youtube video on site
 * */
function resizeVideo(vwith){
	 var vheight= vwith/1.77778;
	 $('object').attr('width', vwith+'px');
    $('embed').attr('width',vwith+'px');
    $('object').attr('height', vheight+'px');
    $('embed').attr('height',vheight+'px');
};

function populate_view_counter(){
	//var html = $('.icon-eye-open').html();
	if(typeof CL.view_counter != 'undefined'){
		$('.icon-eye-open').parent().html('<i class="icon-eye-open"></i>' + CL.view_counter);
	}else{
		$('.icon-eye-open').parent().html('<i class="icon-eye-open"></i>1');
	}
}

//====================================Login box =============================
$("html").on('click',"#show_signup", function(e){
	e.preventDefault();
	$("#login_form, #show_signup_wrapper,#remind_password_form").hide();
	$("#signup_form,#show_login_wrapper").show();
	$("#signup_form").find("input[type='text']:first").focus();
});

$("html").on('click',"#show_login", function(e){
	e.preventDefault();
	$("#show_login_wrapper,#remind_password_form,#signup_form").hide();
	$("#login_form,#show_signup_wrapper").show();
	$("#login_form").find("input[type='text']:first").focus();
});

$("html").on('click', "#remind_password", function(e){
	e.preventDefault();
	$("#login_form").hide();
	$("#remind_password_form").show().find("input[type='text']:first").focus();;
	
	$("#show_login_wrapper").show();
	$("#captcha-input-label, #captcha-element").show();
});

//===========================================+End login box==================================


function populate_username(){
	var uname = getCookie('_cl_uname');
	var uid = getCookie('_cl_uid');
	var uiid = getCookie('_cl_uiid');
	var is_admin = getCookie('_cl_is_admin');
	if(uid != 0){
	    $('#setting_menu').attr('href', '/user/update?id=' + uid + '&_cl_step=account');
		$('#c_uname #drop1').html(uname+'<b  class="caret"></b>');
		$('#c_uname #prof_page').attr('href', '/user/' + uiid);
		$('#c_logout, #c_uname').show();
		$('#c_login, #c_guest').hide();
		if (is_admin == '1') 
	    {
		    //append admin link for root user
		    $("#c_uname ul li:last").after('<li><a href="/site/admin" tabindex="-1">Admin</a></li>');
	    }
		
	}else{
		$('#c_uname #drop1').html(uname+'<b  class="caret"></b>');
		$('#c_uname #prof_page').attr('href', '#');
		$('#c_logout, #c_uname').hide();
		$('#c_login, #c_guest').show();
		
	}
}

///automatically looad next page when scrolling down in viewing story list , e.g. /hot
var loaded = 0;
var isloading = false;
$(document).ready(function(){
	vote_store();
	if($(window).width()>800)
	{
		//story list stickem
		$(window).scroll(function(){ // scroll event
			var flag1st = true;
			var fWidth;
			var fLeft ;
			var marginLeft;
			var delta = 1;
			$('.stickem-container').each(function(){
				var parent = $(this);
				var info = $('.stickem', this);
				if(flag1st){
				 fWidth = info.width() + 30; //hack +30
				 fLeft = info.offset().left;
				 flag1st = false;
				 marginLeft =  parent.width() - info.width() - 30; //hack -30
				}
				if(parent.height()-delta <= info.outerHeight()) return;
				if(parent.offset().top < $(window).scrollTop() + $('.navbar').outerHeight()){
					if($(window).width()>800)
					{
						if($(window).scrollTop() + info.outerHeight() < parent.offset().top + parent.outerHeight() - 30){
							info.removeAttr('style');
							info.css({'position': 'fixed','top': '20px','margin-left': marginLeft + 'px','margin-top': '40px','margin-bottom': '10px','width': fWidth+'px'});
							
						}
						else{
							info.removeAttr('style');
							info.css({bottom: 0,position: 'absolute',right: 0});
						}
					}
					else{
						if($(window).scrollTop() + info.outerHeight() < parent.offset().top + parent.outerHeight() - 30){
							info.removeAttr('style');
							info.css({'position': 'relative','top': '0','margin-left': marginLeft + 'px','margin-top': '0px','margin-bottom': '-50px','width': fWidth+'px'});
							}
						else{
							info.removeAttr('style');
							info.css({bottom: 0,right: 0});
						}
					}
				}else{
					info.removeAttr('style');
				}
	
			});
		});	
	}

	
	$(window).on("resize", function() {
		if($(window).width()>800)
		{
			$("#affix-right-box").show();
		}
		else
		{
			$("#affix-right-box").hide();
		}
	}).trigger('resize');
	
	if (CL.page !== 'story/index/view')
    {
	    //story list load more
	    $(window).scroll(function(e){
	        if  ($(window).scrollTop() == $(document).height() - $(window).height()){
	            if (loaded < 2 && !isloading)
	            {
	                //$('#nav-footer').remove();
	                var url = $('#next-page').attr('data-href-widget');
	                if(url != '#/widget'){
	                    $("#nav-footer").hide();
	                    $('#loading').show();
	                    isloading = true;
	                    $.ajax({
	                        url : url,
	                        data : {
	                            _cl_modal_ajax : 1
	                        },
	                        success : function(data){
	                            isloading = false;
	                            loaded ++;
	                            $("#nav-footer").remove();
	                            $(data.result.content).insertBefore("#loading");
	                            $(".timeago").timeago();//refresh timeago for new items
	                            $('#loading').hide();
	                            $("img.lazy").show().lazyload({
	                                effect : "fadeIn",
	                                threshold : 200
	                            });
	                            vote_store();
	                            FB.XFBML.parse();
	                        }
	                    });	
	                }
	            }
	            else
	            {
	                // do noting
	            }
	        }
	    });
    }
    
	//TODO: 
    //set transparent mod for youtube video 
    $('iframe').each(function(){
		var url = $(this).attr("src");
		$(this).attr("src",url+"?wmode=transparent");
	});
    
    //======================view story ==================================
    if (CL.page == 'story/index/view')
    {
	    fix_social_header = function()
	    {
	           //social-header-widget: keep module hot|vote image widget while scroll touch menu
            if($('.social-header').offset()){ //check class exists
                // get offsetpix
                var social_header_offset = $('.social-header').offset().top;
                var swidth=$('.social-header').parent().width();
                $('.social-header').css({'width':swidth}); 
                    
                /** Affix social button header* */
                $('.social-header').affix({
                    offset: {
                        top: function () { return social_header_offset-$('.navbar').outerHeight(); },
                    }
                });
            }

            if($('.social-header-widget').offset()){ //check class exists
                // get offsetpix
                var topUserHeight = 373;
                var newImageHeight = 319;
                var social_header_widget_offset = 2 * $('.sidebar-nav').outerHeight(true)
                + topUserHeight + newImageHeight 
                + $('.social-header-widget').offset().top;
                
                var swidth=$('.social-header-widget').parent().width();
                $('.social-header-widget').css({'width':swidth}); 
                /** Affix social button header* */
                $('.social-header-widget').affix({
                    offset: {
                        top: function () { return social_header_widget_offset-$('.navbar').outerHeight(); },
                    }
                });
                //et social header width when scroll
            }
	    };
	    fix_social_header();
	    
        //et social header width when scroll
        document.onkeydown = function (e){
        	e = e || window.event;
        	if (e.keyCode == '37') {
        		$("#prev").trigger('click');
        		// up arrow
        	}
        	else if (e.keyCode == '39') {
        		$("#next").trigger('click');
        		// down arrow
        	}
        };
        
        init_fb_js_for_page_view();
        
        var preload_story = function()
        {
			//set this into a timer
			setTimeout(
					function(){preload_story_to_cache(
							$("#" + nextOrPrev).attr('href')
					)},
					2000); //2 seconds

        }
        var populate_ajax_widget_to_pushstate = function(url, data, isBack)
        {
			$("#story-wrapper").html(data.result.content);
			change_history_url(data.result.title, url, isBack);
			

			preload_story();
			$(".first-video").fitVids();
			$(".story-wrapper").fitVids();
			$(".featured").fitVids();
			FB.XFBML.parse();
        }
        
        var preload_story_to_cache = function(url)
        {
        	if (typeof storyCache[url] == 'undefined')
    		{
        		$.ajax({
        			url : url,
        			data : {
        				_cl_modal_ajax : 1
        			},
        			success : function(data){
        				storyCache[url] = data;
        			}
        		});
    		}
        };
        
        var load_story_to_pushstate = function(url, isBack, cb)
        {
        	if (typeof storyCache[url] != 'undefined')
        	{
        		populate_ajax_widget_to_pushstate(url, storyCache[url], isBack);
        		if (cb)
        		    cb.apply();
        	}
        	else 
    		{
        		$.ajax({
        			url : url,
        			data : {
        				_cl_modal_ajax : 1
        			},
        			success : function(data){
        				populate_ajax_widget_to_pushstate(url, data, isBack);
        				if (cb)
                            cb.apply();
        				
        				//save this data into localStorage
        				storyCache[url] = data;
        	            $(".timeago").timeago();//refresh timeago for new items
        	            vote_store();
        			}
        		});
    		}
        };
        
        //prev, next button
        $(document).on('click', '#prev,#next', function(e){
        	nextOrPrev = $(this).attr('id');
        	var url = $(this).attr('href');
        	load_story_to_pushstate(url, false, function()
	        {
    	        fix_social_header();
	        }
        	);
        	
            $(".timeago").timeago();//refresh timeago for new items
            vote_store();
        	e.preventDefault();
        	return false;
        });
        
        window.addEventListener('popstate', function(event) {
        	load_story_to_pushstate(window.location.href, true);
            $(".timeago").timeago();//refresh timeago for new items
            vote_store();
        	//updateContent(event.state);
        });
        
        preload_story();
    }
    else //story list 
	{
    	init_fb_js_for_page_list();
	}

    //======================end view story ==================================

    
    // concat images using html5 canvas
    /*   genCanvas = function() {
           var canvas = document.getElementById("canvas");
           var ctx = canvas.getContext("2d");

           // get total height from images to set canvas.height.
           var height = 0;
           var w = [];
           $.each($("#preview_area .images li img"), function(i, v) {
               height += parseInt(v.height);
               w.push(v.width);
           });
           var maxWidth = 500;
           canvas.width = maxWidth;
           canvas.height = height;

           // draw image into canvas
           var h = 0;
           $.each($("#preview_area .images li img"), function(i, v) {
               ctx.drawImage(v, 0, h, maxWidth, v.height);
               h += parseInt(v.height);
           });

           // get imageData
           var data = canvas.toDataURL();
           //console.log(data);
           $("#img_canvas").val(data);
       };
       */


    /*if ($('.sticky').offset()) { // make sure ".sticky" element exists
        var stickyTop = $('.sticky').offset().top; // returns number
        var firstTime = true;
        var sWidth;
        $(window).scroll(function(){ // scroll event
            if(firstTime){
                stickyTop = $('.sticky').offset().top; // returns number
                firstTime = false;
                sWidth = $('.sticky').width();
            }
            if(stickyTop < $(window).scrollTop() + $('.navbar').height()){
                $('.sticky').css({ position: 'fixed', top: '40px', width:sWidth});
            }else{
                $('.sticky').removeAttr("style");
                firstTime = true;
            }
        });
    }
    
    */ 
    

    //Load this last
    $(".sand-ajax-widget").each(function(){
        get_sand_ajax_request($(this));
    });

});   
