var cf7msm_ss;
jQuery(document).ready(function($) {
	var posted_data = cf7msm_posted_data;
	var step_field = $("input[name='cf7msm-step']");
	if ( step_field.length == 0 ) {
		//not a multi step form
		return;
	}
	if ( cf7msm_hasSS() ) {
		cf7msm_ss = sessionStorage.getObject( 'cf7msm' );

		//multi step forms
		if (cf7msm_ss != null && step_field.length > 0) {
			var cf7_form = $(step_field[0].form);
			$.each(cf7msm_ss, function(key, val){
				if (key == 'cf7msm_prev_urls') {
					cf7_form.find('.wpcf7-back, .wpcf7-previous').click(function(e) {
						window.location.href = val[step_field.val()];
						e.preventDefault();
					});
				}
			});
		}
		/* <fs_premium_only> */
		posted_data = cf7msm_ss;
		/* </fs_premium_only> */
	}
	else {
		$("input[name='cf7msm-no-ss']").val(1);
		$(".wpcf7-previous").hide();
	}

	/* <fs_premium_only> */
	var stepParts = step_field.val().split('-');
	var currStep = parseInt( stepParts[0] );
	/* </fs_premium_only> */

	if (posted_data) {
		var cf7_form = $(step_field[0].form);
		$.each(posted_data, function(key, val){
			if ( key.indexOf('[]') === key.length - 2 ) {
				key = key.substring(0, key.length - 2 );
			}

			/* <fs_premium_only> */
			if (key == 'cf7msm_prev_urls') {
				cf7_form.find('.wpcf7-back, .wpcf7-previous').click(function(e) {
					window.location.href = val[step_field.val()];
					e.preventDefault();
				});
			}
			/* </fs_premium_only> */

			if ( ( key.indexOf('_') != 0 || key.indexOf('_wpcf7_radio_free_text_') == 0 || key.indexOf('_wpcf7_checkbox_free_text_') == 0 ) && key != 'cf7msm-step') {
				var field = cf7_form.find('*[name="' + key + '"]:not([data-cf7msm-previous])');
				var checkbox_field = cf7_form.find('input[name="' + key + '[]"]:not([data-cf7msm-previous])'); //value is this or this or tihs
				if (field.length > 0) {
					if ( field.prop('type') == 'radio' || field.prop('type') == 'checkbox' ) {
						field.filter(function(){
								return $(this).val() == val;
						}).prop('checked', true);
					}
					else {
						field.val(val);	
					}
				}

				/* <fs_premium_only> */
				else if (key.indexOf( '-free-text' ) !== -1 && key.indexOf( '-free-text' ) == ( key.length - ('-free-text'.length) ) ) {
					var base_key = key.replace( '-free-text', '' );
					var base_field = cf7_form.find('input[name="' + base_key + '"]:not([data-cf7msm-previous])');
					if ( base_field.length > 0 && val != '' ) {
						var base_field_type = base_field.prop('type');
						var free_field = cf7_form.find('input[name="_wpcf7_' + base_field_type + '_free_text_' + base_key + '"]');
						free_field.val(val);
						free_field.prop("disabled", false);
					}
				}					
				/* </fs_premium_only> */

				else if ( checkbox_field.length > 0 && val.constructor === Array ) {
					//checkbox
					if ( val != '' && val.length > 0  ) {
						$.each(val, function(i, v){
							checkbox_field.filter(function(){
								return $(this).val() == v;
							}).prop('checked', true);
						});	
					}
				}

				/* <fs_premium_only> */
				//not on the current form, insert hidden data.
				else if ( key.indexOf( 'cf7msm' ) !== 0 && cf7msm_hasSS() ) {
					step_field.after( $('<input type="hidden" data-cf7msm-previous="1" name="' + key + '" value="' + quoteattr( val ) + '">') );
				}

				//read only
				var ro_field = cf7_form.find('#cf7msm_' + key);
				if ( ro_field.length > 0 ) {
					ro_field.text(val);
				}
				else if (key.indexOf( '-free-text' ) !== -1 && key.indexOf( '-free-text' ) == ( key.length - ('-free-text'.length) ) ) {
					var base_key = key.replace( '-free-text', '' );
					var ro_base_field = cf7_form.find('#cf7msm_' + base_key);
					if ( ro_base_field.length > 0 ) {
						var span = $('<span class="cf7msm-ro-free-text"/>');
						span.text(val);
						ro_base_field.append('<span class="cf7msm-ro-other-delimiter">:</span> ').append(span);
					}
				}
				/* </fs_premium_only> */
			}
		});
	}


	/* <fs_premium_only> */

	// if wpcf7cf is active, rewrite init form.
	if ( typeof cf7msm_wpcf7cf !== 'undefined' && cf7msm_wpcf7cf.length === 1 ) {
		if ( typeof wpcf7 !== 'undefined' && wpcf7 !== null ) {
			var savedInitForm = wpcf7.initForm;
			wpcf7.initForm = function( form ) {
				var $form = $( form );
				$form.submit( function( event ) {
					cf7msmBeforeSubmit( form );
				} );
				savedInitForm( form );
			};
		}
	}


	function cf7msmBeforeSubmit( form ) {
		var specialInputs = ['_wpcf7cf_hidden_group_fields', '_wpcf7cf_hidden_groups', '_wpcf7cf_visible_groups'];
		var currForm = $(form);
		var input_hidden_group_fields = currForm.find("input[name='_wpcf7cf_hidden_group_fields']");
		var input_hidden_groups = currForm.find("input[name='_wpcf7cf_hidden_groups']");
		var input_visible_groups = currForm.find("input[name='_wpcf7cf_visible_groups']");


		var hidden_group_fields = JSON.parse(input_hidden_group_fields.val());
		var hidden_groups = JSON.parse(input_hidden_groups.val());
		var visible_groups = JSON.parse(input_visible_groups.val());

		var all_groups = hidden_groups.concat(visible_groups);
		var all_field_names = [];
		var currInputs = currForm.find('*[name]:not([data-cf7msm-previous])');
		currInputs.each(function(i){
			all_field_names.push(this.name);
		});

		var prev_hidden_group_fields = [];
		var prev_hidden_groups = [];
		var prev_visible_groups = [];
		
		cf7msm_ss = sessionStorage.getObject('cf7msm');
		if ( !cf7msm_ss ) {
			cf7msm_ss = {};
		}
		if ( cf7msm_ss.hasOwnProperty('_wpcf7cf_hidden_group_fields') ) {
			prev_hidden_group_fields = JSON.parse(cf7msm_ss['_wpcf7cf_hidden_group_fields']);
		}
		if ( cf7msm_ss.hasOwnProperty('_wpcf7cf_hidden_groups') ) {
			prev_hidden_groups = JSON.parse(cf7msm_ss['_wpcf7cf_hidden_groups']);
		}
		if ( cf7msm_ss.hasOwnProperty('_wpcf7cf_visible_groups') ) {
			prev_visible_groups = JSON.parse(cf7msm_ss['_wpcf7cf_visible_groups']);
		}

		// remove all current data from saved data
		// https://stackoverflow.com/questions/10927722/compare-2-arrays-which-returns-difference
		var diff_hidden_groups = [];
		var diff_hidden_group_fields = [];
		var diff_visible_groups = [];
		jQuery.grep(prev_hidden_group_fields, function(el) {
			if (jQuery.inArray(el, all_field_names) == -1) diff_hidden_group_fields.push(el);
		});
		jQuery.grep(prev_hidden_groups, function(el) {
			if (jQuery.inArray(el, all_groups) == -1) diff_hidden_groups.push(el);
		});
		jQuery.grep(prev_visible_groups, function(el) {
			if (jQuery.inArray(el, all_groups) == -1) diff_visible_groups.push(el);
		});

		// merge current and prevData
		cf7msm_ss['_wpcf7cf_hidden_group_fields'] = JSON.stringify(diff_hidden_group_fields.concat(hidden_group_fields));
		cf7msm_ss['_wpcf7cf_hidden_groups'] = JSON.stringify(diff_hidden_groups.concat(hidden_groups));
		cf7msm_ss['_wpcf7cf_visible_groups'] = JSON.stringify(diff_visible_groups.concat(visible_groups));

		//save everything
		sessionStorage.setObject('cf7msm', cf7msm_ss);
		
		input_hidden_group_fields.val(cf7msm_ss['_wpcf7cf_hidden_group_fields']);
		input_hidden_groups.val(cf7msm_ss['_wpcf7cf_hidden_groups']);
		input_visible_groups.val(cf7msm_ss['_wpcf7cf_visible_groups']);
	};

	/* </fs_premium_only> */

	document.addEventListener( 'wpcf7mailsent', function( e ) {
		if ( cf7msm_hasSS() ) {
			var currStep = 0;
			var totalSteps = 0;
			var names = [];
			var currentInputs = {};
			cf7msm_ss = sessionStorage.getObject('cf7msm');
			if ( !cf7msm_ss ) {
				cf7msm_ss = {};
			}
			$.each(e.detail.inputs, function(i){
				var name = e.detail.inputs[i].name;
				var value = e.detail.inputs[i].value;

				//make it compatible with cookie version
				if ( name.indexOf('[]') === name.length - 2 ) {
					// name = name.substring(0, name.length - 2 );
					if ( $.inArray(name, names) === -1 ) {
						currentInputs[name] = [];
					}
					currentInputs[name].push(value);
				}
				else {
					currentInputs[name] = value;
				}

				//figure out prev url
				if ( name === 'cf7msm-step' ) {
					if ( value.indexOf("-") !== -1 ) {
						var steps_prev_urls = {};
						if ( cf7msm_ss && cf7msm_ss.cf7msm_prev_urls ) {
							steps_prev_urls = cf7msm_ss.cf7msm_prev_urls;
						}
						var stepParts = value.split('-');
						currStep = parseInt( stepParts[0] );
						totalSteps = parseInt( stepParts[1] );
						nextUrl = stepParts[2];
						if ( currStep < totalSteps ) {
							//is this the best way to get current url?
							var nextStep = (1+parseInt(currStep)) + '-' + totalSteps;
							steps_prev_urls[nextStep] = window.location.href;
							// hide the success messages on multi-step forms
							$('#' + e.detail.unitTag).find('div.wpcf7-mail-sent-ok').remove();
						}
						else if ( currStep === totalSteps ) {
							// hide the form on final multi-step form
							$('#' + e.detail.unitTag + ' form').children().not('div.wpcf7-response-output').hide();
						}
						cf7msm_ss.cf7msm_prev_urls = steps_prev_urls;
					}
				}
				else {
					names.push(name);
				}
			});
			/* <fs_premium_only> */
			if ( currStep != 0 ) {
				//this is a cf7msm form.
				if ( typeof cf7msm_ss['cf7msm-step-names'] === 'undefined' ) {
					cf7msm_ss['cf7msm-step-names'] = {};
				}
				if ( typeof cf7msm_ss['cf7msm-step-names']['step-' + currStep + '-names'] !== 'undefined' ) {
					//clear past inputs for checkboxes
					var pastInputNames = cf7msm_ss['cf7msm-step-names']['step-' + currStep + '-names'];
					$.each(pastInputNames, function(i){
						var name = pastInputNames[i];
						delete cf7msm_ss[name];
					});
					names = cf7msm_uniqueArray(cf7msm_ss['cf7msm-step-names']['step-' + currStep + '-names'], names);
				}
				cf7msm_ss['cf7msm-step-names']['step-' + currStep + '-names'] = names;

				//populate current
				$.each(currentInputs, function(name, value){
					cf7msm_ss[name] = value;
				});
			}
			/* </fs_premium_only> */
			if ( currStep != 0 && currStep === totalSteps ) {
				cf7msm_ss = {};
			}
			sessionStorage.setObject('cf7msm', cf7msm_ss);
		}
	}, false );
});

/**
 * Given 2 arrays, return a unique array
 * https://codegolf.stackexchange.com/questions/17127/array-merge-without-duplicates
 */
function cf7msm_uniqueArray(i,x) {
   var h = {};
   var n = [];
   for (var a = 2; a--; i=x)
      i.map(function(b){
        h[b] = h[b] || n.push(b);
      });
   return n
}

/**
 * check if local storage is usable.
 */
function cf7msm_hasSS() {
    var test = 'test';
    try {
        sessionStorage.setItem(test, test);
        sessionStorage.removeItem(test);
        return true;
    } catch(e) {
        return false;
    }
}
Storage.prototype.setObject = function(key, value) {
    this.setItem(key, JSON.stringify(value));
}

Storage.prototype.getObject = function(key) {
    var value = this.getItem(key);
    return value && JSON.parse(value);
}

/**
 * Escape values when inserting into HTML attributes
 * From SO: https://stackoverflow.com/questions/7753448/how-do-i-escape-quotes-in-html-attribute-values
 */
function quoteattr(s, preserveCR) {
    preserveCR = preserveCR ? '&#13;' : '\n';
    return ('' + s) /* Forces the conversion to string. */
        .replace(/&/g, '&amp;') /* This MUST be the 1st replacement. */
        .replace(/'/g, '&apos;') /* The 4 other predefined entities, required. */
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        /*
        You may add other replacements here for HTML only 
        (but it's not necessary).
        Or for XML, only if the named entities are defined in its DTD.
        */ 
        .replace(/\r\n/g, preserveCR) /* Must be before the next replacement. */
        .replace(/[\r\n]/g, preserveCR);
        ;
}
/**
 * Escape values when using in javascript first.
 * From SO: https://stackoverflow.com/questions/7753448/how-do-i-escape-quotes-in-html-attribute-values
 */
function escapeattr(s) {
    return ('' + s) /* Forces the conversion to string. */
        .replace(/\\/g, '\\\\') /* This MUST be the 1st replacement. */
        .replace(/\t/g, '\\t') /* These 2 replacements protect whitespaces. */
        .replace(/\n/g, '\\n')
        .replace(/\u00A0/g, '\\u00A0') /* Useful but not absolutely necessary. */
        .replace(/&/g, '\\x26') /* These 5 replacements protect from HTML/XML. */
        .replace(/'/g, '\\x27')
        .replace(/"/g, '\\x22')
        .replace(/</g, '\\x3C')
        .replace(/>/g, '\\x3E')
        ;
}