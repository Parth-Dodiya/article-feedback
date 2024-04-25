(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );

jQuery(document).ready(function() {

	var Ip;

	// jQuery.getJSON('https://api.ipify.org?format=json', function(data) {
    
    	// Ip = data.ip;
    	// console.log('Ip1:: ',Ip);
    // })

	if (jQuery('div#article-feedback-public').length ) {
		console.log('sggfht');
		if (jQuery('div#article-feedback-public')[0]){
            setTimeout(() => {
        		var networkId = jQuery('#article-feedback-public').data('network-id');
		        var articleId = jQuery('#article-feedback-public').attr('data-post-id');
		        var Ip = jQuery('#article-feedback-public').data('ip-address');
		        // console.log('Ntwrk:: ',networkId);
		        // console.log('articleId:: ',articleId);
		        console.log('Ip:: ',Ip);
		        jQuery.ajax({
		            type: 'POST',
		            url: af_ajax_object.ajaxurl,
		            dataType: 'JSON',
		            data: { action: 'article_feedback_check_ajax', postId: articleId, IP: Ip, network: networkId },
		            success: function (response) {
		            	console.log( 'hii', (response['past_feedback'] == 0));
		                if ( (response['past_feedback'] == 1) && (response['post_id'] == articleId) ) {
		                    jQuery('#feedback-button' + response['past_feedback']).css('pointer-events', 'none');
		                    jQuery('#feedback-button' + response['past_feedback']).addClass('active');
		                }
		                if ( (response['past_feedback'] == 0) && (response['post_id'] == articleId) ) {
		                    jQuery('#feedback-button' + response['past_feedback']).css('pointer-events', 'none');
		                    jQuery('#feedback-button' + response['past_feedback']).addClass('active');
		                }


		                if ( response['fb_count'] == '0' ) {

		                    jQuery('#article-feedback-count-block p.article-feedback-counts').text(' No feedback registered yet');
		                
		                } else {

		                    jQuery('#article-feedback-count-block p.article-feedback-counts').text(response['positive_fb_count'] + ' of ' + response['fb_count']);
		                
		                }
		            }
		        });
			}, 500);
            
        }


        var articleId = jQuery('#article-feedback-public').attr('data-post-id');
        var networkId = jQuery('#article-feedback-public').data('network-id');
        jQuery('.article-feedback-button').click(function(){
            var feedbackRes = jQuery(this).data('value-af');
            jQuery.ajax({
                type: 'POST',
                url: af_ajax_object.ajaxurl,
                dataType: 'JSON',
                data: { action: 'article_feedback_ajax', postId: articleId, feedback: feedbackRes, IP: Ip, network: networkId },
                success: function (response) {
                	jQuery('#article-feedback-thank-block').removeClass('display-none');
                    jQuery('#article-feedback-count-block p.article-feedback-counts').text(response['positive_fb_count'] + " of " + response['fb_count']);
                    if ( feedbackRes == '1' ) {
                        jQuery('span#feedback-button' + feedbackRes).css('pointer-events', 'none');
                        jQuery('span#feedback-button' + feedbackRes).addClass('active');
                        jQuery('span#feedback-button0').css('pointer-events', 'all');
                        jQuery('span#feedback-button0').removeClass('active');
                    }
                    if ( feedbackRes == '0' ) {
                        jQuery('span#feedback-button' + feedbackRes).css('pointer-events', 'none');
                        jQuery('span#feedback-button' + feedbackRes).addClass('active');
                        jQuery('span#feedback-button1').css('pointer-events', 'all');
                        jQuery('span#feedback-button1').removeClass('active');
                    }
                }
            });
        });
	}    
});