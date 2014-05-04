	var vote_status = new Object();
	vote_status = {};
	var list = new Array();
	var uiid = getCookie('_cl_uid');
	var vote;
	function vote()
	{
		uiid = getCookie('_cl_uid');
		var iid = $(a).attr('data-iid');
		if (uiid != 0)
		{
			list = JSON.parse(localStorage.getItem(uiid));
			if(list != null)
				vote_status = list;
			vote_status[iid] = true;
		    localStorage.setItem(uiid,JSON.stringify(vote_status));
	        //increase the vote
		}
        return false;
	}
        CL.funcs['vote_story_success'] = function(a, params, data)
    {
        //increase the vote
        alert_success("Vote up Successful");
        var votes = 1 + parseInt($("#collapse").attr('data-votes'));
        var block = $(a).closest('.well');
        var totalVotes = block.find('.voteCounter').attr('data-totalvotes');
        var counter = block.find('.voteCounter');
        var total = 1 + parseInt(totalVotes);
        block.find('.voteCounter').attr('data-totalvotes',total);
        $("#vote_up_count").html(votes);
        //here
        block.find("span.totalVotes").html(total);
       // $(a).addClass('disabled');
        $(a).parent().parent().addClass('up');
        return false;
    };
    
    
    /* general voting for samx. Here we will increase the votes and hide the vote button*/
    CL.funcs['unvote_story_success'] = function(a, params, data)
    {
        //decrease the vote
        alert_success("Vote down Successful");
        var votes = parseInt($("#collapse").attr('data-vd')) - 1;
        var block = $(a).closest('.well');
        var totalVotes = block.find('.voteCounter').attr('data-totalvotes');
        //var counter = block.find('span.totalVotes');
        var total = parseInt(totalVotes) -1 ;
        block.find('.voteCounter').attr('data-totalvotes',total);
        $("#vote_down_count").html(votes);
        //here
        block.find("span.totalVotes").html(total);
        $(a).parent().parent().addClass('down');
        $(a).addClass('disabled');
        return false;
    };

    /* general voting for samx. Here we will increase the votes and hide the vote button*/
    CL.funcs['mark_spam_success'] = function(a, params, data)
    {
        //decrease the vote
        alert_success("Post is reported as spam");
        var votes = parseInt($("#collapse").attr('data-spam')) + 1;
//        var block = $(a).closest('.well');
//        var totalVotes = block.find('.voteCounter').attr('data-totalvotes');
//        //var counter = block.find('span.totalVotes');
//        var total = parseInt(totalVotes) -1 ;
//        block.find('.voteCounter').attr('data-totalvotes',total);
        $("#mark_spam").html(votes);
        //here
//        block.find("span.totalVotes").html(total);
        $(a).find('.mark-spam-btn').css("color","#B23232");
        $(a).addClass('disabled');
        return false;
    };
    
    CL.funcs['vote_story_err'] = function(a, params, data)
    {
    	
        //increase the vote
        if(data['err_code'] == 'RELATION_EXISTS')
            {
                alert_error("Already Voted");
            }
        $(a).find('.vote-up-btn').css("color","#357EBD");
        return false;
    };
    
    CL.funcs['unvote_story_err'] = function(a, params, data)
    {
        //increase the vote
        if(data['err_code'] == 'RELATION_EXISTS')
            {
                alert_error("Unvote fail");
            }
        $(a).find('.vote-down-btn').css("color","#B23232");
        return false;
    };
	
    CL.funcs['choose_pair_one_success'] = function(a, params, data)
    {
        //increase the vote
        alert_success("Vote up Successful");
        var votes = 1 + parseInt($("#collapse").attr('data-votes'));
        var block = $(a).closest('.well');
        var totalVotes = block.find('.voteCounter').attr('data-totalvotes');
        var counter = block.find('.voteCounter');
        var total = 1 + parseInt(totalVotes);
        block.find('.voteCounter').attr('data-totalvotes',total);
        $("#vote_up_count").html(votes);
        //here
        block.find("span.totalVotes").html(total);
        $(a).addClass('disabled');
        $(a).find('.vote-up-btn').css("color","#357EBD");
        return false;
    };
    
    CL.funcs['choose_pair_two_success'] = function(a, params, data)
    {
        //increase the vote
        alert_success("Vote up Successful");
        var votes = 1 + parseInt($("#collapse").attr('data-votes'));
        var block = $(a).closest('.well');
        var totalVotes = block.find('.voteCounter').attr('data-totalvotes');
        var counter = block.find('.voteCounter');
        var total = 1 + parseInt(totalVotes);
        block.find('.voteCounter').attr('data-totalvotes',total);
        $("#vote_up_count").html(votes);
        //here
        block.find("span.totalVotes").html(total);
        $(a).addClass('disabled');
        $(a).find('.vote-up-btn').css("color","#357EBD");
        return false;
    };
    
    
    CL.funcs['vote_success'] = function(a, params, data)
    {
    	vote();
    };
    CL.funcs['vote_err'] = function(a, params, data)
    {
    	vote();
    };
    CL.funcs['unvote_success'] = function(a, params, data)
    {
    	vote();
    };
    CL.funcs['unvote_err'] = function(a, params, data)
    {
    	vote();
    };
    if (typeof CL.metadata.vote_story_success == 'undefined')
        CL.metadata.vote_story_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'vote_story_success'
                        }
                    ],
                    'err' : [
                                 {
                                     func : 'vote_story_err'
                                 }
                             ],
                }
        };          

    if (typeof CL.metadata.unvote_story_success == 'undefined')
        CL.metadata.unvote_story_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'unvote_story_success'
                        }
                    ],
                    'err' : [
                                 {
                                     func : 'unvote_story_err'
                                 }
                             ]
                }
        };

    if (typeof CL.metadata.mark_spam_success == 'undefined')
        CL.metadata.mark_spam_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'mark_spam_success'
                        }
                    ],
                    'err' : [
                                 {
                                     func : 'mark_spam_success'
                                 }
                             ]
                }
        };     
    if (typeof CL.metadata.vote_success == 'undefined')
        CL.metadata.vote_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'vote_success'
                        }
                    ],
                    'err' : [
                             {
                                 func : 'vote_err'
                             }
                         ],
                }
        }; 
    if (typeof CL.metadata.unvote_success == 'undefined')
        CL.metadata.unvote_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'unvote_success'
                        }
                    ],
                    'err' : [
                             {
                                 func : 'unvote_err'
                             }
                         ],
                }
        }; 
    if (typeof CL.metadata.choose_pair_one_success == 'undefined')
        CL.metadata.choose_pair_one_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'choose_pair_one_success'
                        }
                    ],
                    'err' : [
                                 {
                                     func : 'choose_pair_one_err'
                                 }
                             ],
                }
        }; 
    if (typeof CL.metadata.choose_pair_two_success == 'undefined')
        CL.metadata.choose_pair_one_success =
            {
                method : 'POST', 
                callbacks : {
                    'success' : [
                        {
                            func : 'choose_pair_two_success'
                        }
                    ],
                    'err' : [
                                 {
                                     func : 'choose_pair_two_err'
                                 }
                             ],
                }
        };    