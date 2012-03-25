jQuery(document).ready(function($){

    /**INIT **/
    $(".soundcloudMMLoading").css('display', 'none');

    $("#soundcloudMMShowUsernames").click(function(e){
	e.preventDefault();
	$("#soundcloudMMUsermameTab").slideDown('fast');
	$("#soundcloudMMHideUsernames").removeClass('hidden');
	$(this).addClass('hidden');
    });
    $("#soundcloudMMHideUsernames").click(function(e){
	e.preventDefault();
	$("#soundcloudMMUsermameTab").slideUp('fast');
	$("#soundcloudMMShowUsernames").removeClass('hidden');
	$(this).addClass('hidden');
    });

    /*** OPTIONS ***/
    /*	CarouFredSel: an infinite, circular jQuery carousel.
	    Configuration created by the "Configuration Robot"
	    at caroufredsel.frebsite.nl
    */
    $("#soundcloudIsGoldUserError a").click(function(e){
	e.preventDefault();
	$(this).parent().fadeOut();
    }).parent().css("display", "none");

    carousel()
    $("#soundcloudMMUsermameTab").slideUp('fast');
    
    function carousel(){
	$("#soundcloudIsGoldUsernameCarousel").carouFredSel({
	    circular: false,
	    infinite: false,
	    width: 354,
	    height: 118,
	    align: "left",
	    padding: [0, 0, 0, 0],
	    margin: [0, 0, 0, 0],
	    items: {
		    visible: 3,
		    width: 118,
		    height: 118
	    },
	    auto: false,
	    pagination: "#soundcloudIsGoldUsernameCarouselNav"
    }).find("li .soundcloudIsGoldRemoveUser").click(function() {
	    removeUser($(this).parent());
	    removeUserFromOptions($("div input:first", $(this).parent()).val(), false);
	}).css("cursor", "pointer");
	makeUserActive();
	removeActiveUser();
    }
    
    /** Remove User **/
    function removeUser(that){
	that.animate({
	    opacity 	: 0
	    }, 500).animate({
		    width		: 0,
		    margin		: 0,
		    borderWidth	: 0
	    }, 400, function() {
		$("#soundcloudIsGoldUsernameCarousel").trigger("removeItem", that); 
	});
    }
    /** Remove User From Options (Tab only) **/
    function removeUserFromOptions(usernameToRemove, reload){
	if($("#soundcloudMMUsermameTab").length){
	    console.log(usernameToRemove);
	    //Set request
	    var myData = {
		action: 'soundcloud_is_gold_delete_user',
		request: 'soundcloudIsGoldDeleteUser',
		username: usernameToRemove
	    };
	    jQuery.post(ajaxurl, myData, function(response) {
		if(response == 'done' && reload) location.reload();
	    });
	}
    }
    /** Add new User **/
    $("#soundcloudIsGoldAddUser").click(function(e){
	e.preventDefault();
	$(".soundcloudMMLoading").fadeIn('fast');
	//Set request
	var myData = {
            action: 'soundcloud_is_gold_add_user',
            request: 'soundcloudIsGoldAddUser',
            username: $("#soundcloudIsGoldNewUser").val(),
	    updateOption : $("#soundcloudMMUsermameTab").length
        };
	jQuery.post(ajaxurl, myData, function(response) {
	    $(".soundcloudMMLoading").fadeOut('fast');
	    if(response != "error"){
		var args = [response, "#soundcloudIsGoldUsernameCarousel li:first", true, 0];
		$("#soundcloudIsGoldUsernameCarousel").trigger("insertItem", args);
		carousel();
	    }else{
		$("#soundcloudIsGoldUserError p").html("wrong username").parent().fadeIn();
	    }
	});
    });
    
    /** Make User Active **/
    function makeUserActive(){
	$("#soundcloudIsGoldUsernameCarousel .soundcloudIsGoldUserContainer div").click(function(){
	    previousActiveUser = $("#soundcloudIsGoldActiveUserContainer .soundcloudIsGoldUserContainer");
	    newActiveUser = $(this).parent();
	    //Remove from Carousel
	    $(this).parent().fadeOut(function(){
		//Copy new Active User to the Active User container and move active user label
		newActiveUser.clone().css("margin", "5px 4px").appendTo("#soundcloudIsGoldActiveUserContainer").prepend($("#soundcloudIsGoldActiveLabel")).fadeIn();
		//Update hidden field for active user
		$("#soundcloudIsGoldActiveUser").val($('p', newActiveUser).html());
		//Remove it from carousel
		$("#soundcloudIsGoldUsernameCarousel").trigger("removeItem", $(this));
		//Move old active user to carousel
		var args = [previousActiveUser, "#soundcloudIsGoldUsernameCarousel li:first", true, 0];
		$("#soundcloudIsGoldUsernameCarousel").trigger("insertItem", args);
		//Init Carousel
		carousel();
		//Tab: extra actions
		if($("#soundcloudMMUsermameTab").length) {
		    $(".soundcloudMMLoading").fadeIn('fast');
		    //Set request
		    var myData = {
			action: 'soundcloud_is_gold_set_active_user',
			request: 'soundcloudIsGoldSetActiveUser',
			username: $("#soundcloudIsGoldActiveUser").val()
		    };
		    jQuery.post(ajaxurl, myData, function(response) {
			if(response != "error"){
			    //Reload Tab
			    location.reload();
			}else{
			    //Error
			    $("#soundcloudIsGoldUserError p").html("wrong username").parent().fadeIn();
			}
		    });
		}
	    });
	    
	});
    }
    
    /** Remove Active user **/
    function removeActiveUser(){
	$("#soundcloudIsGoldActiveUserContainer .soundcloudIsGoldUserContainer .soundcloudIsGoldRemoveUser").click(function(){
	    removeUserFromOptions($("input:first", $(this).parent()).val(), true);
	    activeUserToRemove = $(this).parent().parent();
	    activeUserToRemove.fadeOut(function(){
		//Copy new Active User to the Active User container and move active user label
		$("#soundcloudIsGoldUsernameCarousel .soundcloudIsGoldUserContainer:first").clone().css("margin", "5px 4px").appendTo("#soundcloudIsGoldActiveUserContainer").prepend($("#soundcloudIsGoldActiveLabel")).fadeIn();
		//Delete Active User
		$(this).remove();
		//Move First User from Carousel to Active
		$("#soundcloudIsGoldUsernameCarousel .soundcloudIsGoldUserContainer:first").fadeOut(function(){
		    //Remove it from carousel
		    removeUser($(this));
		    //Update hidden field for active user
		    $("#soundcloudIsGoldActiveUser").val($('#soundcloudIsGoldActiveUserContainer .soundcloudIsGoldUserContainer div p').html());
		});
	    });
	});
    }
    
    /******************************************/
    /**              SOUNDCLOUD              **/
    /******************************************/
    //Attach Events for Player Preview and Shortcode
    $('.soundcloudMMMainWrapper').each(function(){
	var mySelf = $(this);
	//On changing settings
	$('input[type=checkbox], input[type=radio], .soundcloudMMWPSelectedWidth, .soundcloudMMColorPickerClose', this).click(function(){
	    updateMe(mySelf, true);
	});
	$('.soundcloudMMCustomSelectedWidth, .soundcloudMMClasses', this).focusout(function(){
	    updateMe(mySelf, true);
	});
	//Initialize color Picker
	initColorPicker(mySelf);
	//(Tab View) Event: Load First Time preview when show clicked
	if(!mySelf.hasClass('soundcloudMMOptions')){
	    $(".describe-toggle-on", mySelf.parent()).click(function(){
		updateMe(mySelf, true);
	    });
	}
    });
    
    //First Time On Option Page
    if($(".soundcloudMMOptions").length) updateMe($(this), true);
    
    function updateMe(parent, refresh){
	//Collect Settings
	if($('.soundcloudMMAutoPlay:checked', parent).val() == undefined) autoPlay = false;
	else autoPlay = true;
	if($('.soundcloudMMShowComments:checked', parent).val() == undefined) comments = false;
	else comments = true;
	if($('.soundcloudMMShowArtwork:checked', parent).val() == undefined) artwork = false;
	else artwork = true;
        //Set width
	if($(".soundcloudMMWpWidth", parent).is(":checked")) width = $('.soundcloudMMWidth option:selected', parent).val();
	if($(".soundcloudMMCustomWidth", parent).is(":checked")) width = $('input.soundcloudMMWidth', parent).val();
	//Class
	classes = $('.soundcloudMMClasses', parent).val();
        //Player Type
	$('.soundcloudMMPlayerType', parent).each(function(){
	    if( $(this).attr('checked') == 'checked' ) playerType = $(this).val();
	});
	//Color
	color = $('.soundcloudMMColor', parent).val();
	user = $('.soundcloudIsGoldUserContainer div p').text();
	//Format
	if($('.soundcloudMMWrapper').hasClass('sets')) format = 'sets';
	else format = 'tracks';
	//Set Shortocode Attributes
	if(!parent.hasClass('soundcloudMMOptions')) shortcode(parent, autoPlay, comments, width, classes, playerType, color, artwork, format);
        //Refresh Preview if requested
	if(refresh) preview(parent, user, autoPlay, comments, width, classes, playerType, color, artwork, format);

   };
    
    /********************************************/
    /**             INSERT TO POST             **/
    /********************************************/
    $(".soundcloudMMInsert").click(function(){
	var myID = getID($(this));
	insertShortcode($('#soundcloudMMShortcode-'+myID).val());
    });
    function insertShortcode(sh){
	//Insert Content at the end of the editor content
	//parent.tinyMCE.activeEditor.setContent(parent.tinyMCE.activeEditor.getContent() + sh);
	
	//Insert Content where the cursor is in the editor (plus refresh)
	parent.tinyMCE.activeEditor.execCommand('mceInsertRawHTML', false, sh);
	//Insert Content where the cursor is in the editor (no refresh)
	//parent.tinyMCE.activeEditor.execCommand('mceInsertContent', false, sh);
	//Close window
	parent.jQuery("#TB_closeWindowButton").click();
    }
    
    /********************************************/
    /**               SHORTCODE                **/
    /********************************************/
    function shortcode(parent, autoPlay, comments, width, classes, playerType, color, artwork, format){
        var shortcode = "soundcloud id='"+getID($('.soundcloudMMId', parent))+"'";
	if(comments != soundcloudIsGoldComments_default) shortcode += " comments='"+comments+"'";
	if(artwork != soundcloudIsGoldArtwork_default) shortcode += " artwork='"+artwork+"'";
        if(playerType != soundcloudIsGoldPlayerType_default) shortcode += " playerType='"+playerType+"'";
        if(autoPlay != soundcloudIsGoldAutoPlay_default) shortcode += " autoPlay='"+autoPlay+"'";
        if(width != soundcloudIsGoldWidth_default) shortcode += " width='"+width+"'";
        if(classes != soundcloudIsGoldClasses_default) shortcode += " classes='"+classes+"'";
        if(color != soundcloudIsGoldColor_default) shortcode += " color='"+color+"'";
	if(format != 'tracks') shortcode += " format='set'";
	
        $('.soundcloudMMShortcode', parent).val("["+shortcode+"]");
    }
    
    /********************************************/
    /**                PREVIEW                 **/
    /********************************************/
    function preview(parent, user, autoPlay, comments, width, classes, playerType, color, artwork, format){
	//Animate transition
	switch(playerType){
	    case 'Mini':
		newHeight = '18px';
		break;
	    case 'Standard':
		if(format == 'tracks') newHeight = '81px';
		else newHeight = '165px';
		break;
	    case 'Artwork':
		width = '300px';
		newHeight = width;
		break;
	    case 'html5':
		if(format == 'tracks') newHeight = '166px';
		else newHeight = '450px';
		break;
	}
	//Set request
	var myData = {
            action: 'soundcloud_is_gold_player_preview',
            request: 'getSoundcloudIsGoldPlayerPreview',
            ID: getID($('.soundcloudMMId', parent)),
            user: user,
	    comments: comments,
            autoPlay: autoPlay,
	    artwork: artwork,
	    width: width,
	    classes: classes,
	    playerType: playerType,
	    color: color,
	    format: format
        };
	
	//Tell user it's loading
	$('.soundcloudMMEmbed', parent).fadeOut('fast', function(){
	    $('.soundcloudMMPreviewLoading', parent).fadeIn();
	    $('.soundcloudMMPreviewLoading', parent).animate({
		height: newHeight
	    }, 'slow', function(){
		//The Ajax request
		jQuery.post(ajaxurl, myData, function(response) {
		    if(response){
			$('.soundcloudMMEmbed', parent).css('height', newHeight).html(response);
			$('.soundcloudMMPreviewLoading', parent).fadeOut('fast', function(){
			    $(this).css('display', 'none');
			    $('.soundcloudMMEmbed', parent).fadeIn();  
			});
		    }
		});
	
	    });
	});
        
    }
    
    /********************************************/
    /**             COLOR PICKER               **/
    /********************************************/
    function initColorPicker(parent){
	$('.soundcloudMMColorPickerContainer', parent).each(function(){
	    var mySelf = $(this);
	    var colorWheel = $('.soundcloudMMColorPicker', this);
	    var colorInput = $('.soundcloudMMColor', this);
	    colorWheel.hide();
	    $('.soundcloudMMColorPicker .soundcloudMMColorPickerSelect', this).farbtastic(colorInput);
	    var soundcloudMMColorPicker = $.farbtastic($('.soundcloudMMColorPicker .soundcloudMMColorPickerSelect', this));
	    soundcloudMMColorPicker.setColor('#'+colorInput.val());
	    //Select Color (Open Color Wheel)
	    colorInput.click(function(){
		colorWheel.css({'top' : $(this).position().top-colorWheel.height()*0.5, 'left' : $(this).position().left}).fadeIn();
	    });
	    //Close Color Wheel
	    $('.soundcloudMMColorPickerClose', this).click(function(){
		colorInput.val(soundcloudMMColorPicker.color);
		$(this).parent().fadeOut();
	    });
	    //Reset Color to Default
	    $('.soundcloudMMResetColor', this).click(function(e){
		e.preventDefault();
		soundcloudMMColorPicker.setColor(soundcloudIsGoldColor_default);
		colorInput.val(soundcloudIsGoldColor_default).css('background-color', '#'+soundcloudIsGoldColor_default);
		updateMe(parent, true);
	    });
	});
    }
    
    
    /************** ADVANCED SETTINGS **************/
    $('.soundcloudMMAdvancedSettingsPanels').css('display', 'none');
    var closedAvancedSettingText = $('.soundcloudMMAdvancedSettingsShowHide').text();
    var openedAvancedSettingText = "It's too much for me, take it away.";
    $('.soundcloudMMAdvancedSettingsShowHide').click(function(e) {
	e.preventDefault();
	if($(this).text() == closedAvancedSettingText) $(this).text(openedAvancedSettingText);
	else $(this).text(closedAvancedSettingText);
	$('~ .soundcloudMMAdvancedSettingsPanels', this).slideToggle('slow', function() {
	  // Animation complete.
	});
    });
    
    function getID(t){
        myID = t.attr('id').match(/[0-9]+./);
        return myID[0];
    }

});

