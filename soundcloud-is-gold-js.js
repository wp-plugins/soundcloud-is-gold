jQuery(document).ready(function($){

    /**INIT **/
    $(".soundcloudMMLoading").css('display', 'none');

    /*** OPTIONS ***/
    /*$(".soundcloudMMInput").each(function(){
	var cleanOnce = false;
	$(this).focus(function(){
	    if(!cleanOnce){
		$(this).val('');
		cleanOnce = true;
	    }
	});
    });*/
    
    
    /******************************************/
    /**              SOUNDCLOUD              **/
    /******************************************/
    //Attach Events for Player Preview and Shortcode
    $('.soundcloudMMWrapper').each(function(){
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
	//Format
	if($('.soundcloudMMWrapper').hasClass('sets')) format = 'sets';
	else format = 'tracks';
	//Set Shortocode Attributes
	if(!parent.hasClass('soundcloudMMOptions')) shortcode(parent, autoPlay, comments, width, classes, playerType, color, artwork, format);
        //Refresh Preview if reuqested
	if(refresh) preview(parent, autoPlay, comments, width, classes, playerType, color, artwork, format);

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
    function preview(parent, autoPlay, comments, width, classes, playerType, color, artwork, format){
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
		newHeight = width;
		break;
	    case 'html5':
		if(format == 'tracks') newHeight = '166px';
		else newHeight = '450px';
		break;
	}
	//Tell user it's loading
	$('.soundcloudMMEmbed', parent).fadeOut('fast', function(){
	    $('.soundcloudMMLoading', parent).fadeIn();
	    $('.soundcloudMMLoading', parent).animate({
		height: newHeight
	    }, 'slow', function(){
	    
	    });
	});
	
	//Set request
	var myData = {
            action: 'soundcloud_is_gold_player_preview',
            request: 'getSoundcloudIsGoldPlayerPreview',
            ID: getID($('.soundcloudMMId', parent)),
            comments: comments,
            autoPlay: autoPlay,
	    artwork: artwork,
	    width: width,
	    classes: classes,
	    playerType: playerType,
	    color: color,
	    format: format
        };
        jQuery.post(ajaxurl, myData, function(response) {
	    if(response){
                $('.soundcloudMMEmbed', parent).css('height', newHeight).html(response);
		$('.soundcloudMMLoading', parent).fadeOut(function(){
		    $(this).css('display', 'none');
		    $('.soundcloudMMEmbed', parent).fadeIn();  
		});
	    }
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
		colorInput.val(soundcloudIsGoldColor_default);
		updateMe(parent, true);
	    });
	});
    }
    
    function getID(t){
        myID = t.attr('id').match(/[0-9]+./);
        return myID[0];
    }

});

