=== Contact Form 7 Multi-Step Forms ===
Contributors: webheadllc
Donate Link: https://webheadcoder.com/donate-cf7-multi-step-forms
Tags: contact form 7, multistep form, form, multiple pages, store form, contact, multi, step
Requires at least: 4.7
Tested up to: 4.9.6
Stable tag: 3.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enables the Contact Form 7 plugin to create multi-page, multi-step forms.

== Description ==

I needed a contact form that spanned across multiple pages and in the end would send an email with all the info collected.  This plugin adds onto the popular Contact Form 7 plugin to do just that.

Sample of this working is at [https://webheadcoder.com/contact-form-7-multi-step-form/](https://webheadcoder.com/contact-form-7-multi-step-form/)

Requires the [Contact Form 7 plugin](https://wordpress.org/plugins/contact-form-7/), version 4.8 or above, by Takayuki Miyoshi.

**Usage**

1. Create one page or post for each step in your multi-step form process.  If you have 3 steps, create 3 pages/posts.  You will need the urls to these when creating your forms.

1. Create a Contact Form 7 form.

1. Place your cursor at the end of the form.

1. On the "Form" tab of the Contact Form 7 form, click on the button named "multistep".

1. In the popup, type in the current step and total steps in your multi-step process.  For example, if this is the first form in a total of 3 forms, type in "1" for Current Step and "3" in Total Steps.  If this is the last form in your process, type in "3" for current Step and "3" in Total Steps.

1. The Next Page URL is the url that contains your next form.  If this form is the last step, you can leave the URL field blank or fill it in to redirect the user to some other page.

1.  Click "Insert Tag"

1.  Save your completed form and place the form's shortcode into the appropriate Page/Post you created in step 1.

1.  Repeat for **each form** in your multi-step form process.  

1.  Only the final step will send an email.  To include fields from your other forms simply enter the mail-tags from the other forms.  For example if your first form has the field `your-email` you can include `[your-email]` in the Mail tab on the last form.


**Additional Tags**
`[multiform "your-name"]` - The `multiform` form-tag can be used to display a field from a previous step.  Replace `your-name` with the name of your field.

`[previous "Go Back"]` - The `previous` form-tag can be used to display a button to go to a previous step.  Replace `Go Back` with text you want to show in the button.


**What this plugin DOES NOT do:**  

* This plugin does not support file uploads on every form.  If you need to use file uploads make sure to place it on the last step.

* This plugin does not load another form on the same page.  It only works when the forms are on separate pages.  Many have asked to make it load via ajax so all forms can reside on one page.  This plugin does not support that.


**PRO Version**
If you expect to have a lot of data submitted through your multi-step forms, the Pro version may be able to help you better.  The PRO version uses Session Storage so it is able to handle roughly 1,000 times more data for your multiple forms.  In total it can handle about 5MB vs 4KB in the free version.  **Currently the Pro version REQUIRES the WordPress REST API and Contact Form 7 AJAX Submission to be enabled.**   

== Frequently Asked Questions ==

= The Next button doesn't show up =
Like all Contact Form 7 forms, you still need to add a button to submit the form.  Use the normal submit button with any label you want like so `[submit "Next"]`.

The `multistep` form tag is a hidden field and tries not to add any spacing to your form.  In this effort, anything directly after this tag may be hidden.  To prevent this, add a carriage return after the `multistep` form tag, or just follow the directions and place the form tag at the end of the form.

= I keep getting the "Please fill out the form on the previous page" message.  What's wrong? =

If you have everything set up correctly and you get a message saying, "Please fill out the form on the previous page" after submitting the first form, then it's probably your caching system not allowing cookies to be set in the normal way.  No workarounds or fixes are planned at this time.  You will need to turn off caching for the affected pages.


= How can I show a summary of what the user entered or show fields from previous steps? =

`[multiform "your-name"]`  
The multiform form-tag can be used to display a field from a previous step.  Replace `your-name` with the name of your field.

= My form values aren't being sent in the email.  I get [multiform "your-name"] instead of the actual person's name. =

The multiform form-tag should only be used on the Form tab.  On the Mail tab follow the instructions from the Contact Fom 7 documentation.  So if you wanted to show the `your-name` field, type `[your-name]`.

= Can I have an email sent on the first step of the multi-step forms? =

Yes, you can, but it requires you to add code to your theme's functions.php file.  See this forum post for more details:
[https://wordpress.org/support/topic/send-auto-responder-on-step-one-of-multi-step-1?replies=6](https://wordpress.org/support/topic/send-auto-responder-on-step-one-of-multi-step-1?replies=6)


= My forms are not working as expected.  What's wrong? =

- Make sure you have the `multi-step` tag on each and every form.

- It is very common for other plugins to have javascript errors which can prevent this plugin from running properly.  Deactivate all other plugins and try again.

= Previous button leads to 'undefined' =

This is caused by a multistep form tag not having the correct step.  For example your form for step 2 of 3 may have `[multistep "1-3-http://a.com/step-3"]`, but instead it should be `[multistep "2-3-http://a.com/step-3"]`.

= Why "place your cursor at the end of the form" before inserting the multistep tag? =

The `multistep` form tag is a hidden field and tries not to add any spacing to your form.  In this effort, anything directly after this tag may be hidden.  To prevent this, add a carriage return after the `multistep` form tag, or just follow the directions and place the form tag at the end of the form.


== Changelog ==

= 3.0.7 =
updated freemius

= 3.0.6 =
PRO: fixed "Cannot use a scalar value as an array" warning when CF7 Conditional Fields plugin is active.  

= 3.0.5 =
PRO: fixed compatibility with Contact Form 7 Conditional Fields plugin to only show group that is supposed to show.  

= 3.0.4 =
deprecated wpcf7_form_field_value filters.  
added cf7msm_form_field_value filters.  

= 3.0.3 =
PRO: fixed conditional fields (from the Conditional Fields for Contact Form 7 plugin) not showing in email.  

= 3.0.2 =
fixed quotes in values causing errors.  
added plugin action links.  

= 3.0.1 =
fixed session storage not clearing after final step was submitted.  
fixed form not hiding after final step was submitted.  Thanks to @tschodde.  

= 3.0 =
changed internal field names to be prefixed with cf7msm.  
added PRO version to handle long forms.  
fixed minor issues.  

= 2.26 =  
updated i18n code.  

= 2.25 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed incompatible JSON_UNESCAPED_UNICODE for PHP versions < 5.4.  

= 2.24 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed not redirecting to next step on older iPad browsers.  
fixed illegal offset exception warning.  
added JSON_UNESCAPED_UNICODE for czech language.  

= 2.23 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed back button on firefox.  
fixed url not displaying correctly when it has the & symbol.  

= 2.22 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed back button going back more than one step.  

= 2.21 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed an issue where a notice occurred when using scan_form_tags on servers that displayed PHP notices.  


= 2.2 =  
**Contact From 7 version 4.8 or above is required for this version**.  
fixed back button not working when using with Contact Form 7 version 4.8.  
fixed fields from previous steps not showing up when using with Contact Form 7 version 4.8.  
Thanks to @eddraw, updated deprecated functions.  

= 2.1 =  
Use Contact Form 7's built-in hidden form tag if version 4.6 or above is present.  

= 2.0.9 =  
fixed issue where using the `[multiform]` tag causes the field to blank out and not show in emails on certain servers.  


= 2.0.8 =  
added field_name and value to wpcf7_form_field_value filter.  


= 2.0.7 =
fixed calls to deprecated CF7 functions.
Increased minimum WP version to match CF7's specs.  


= 2.0.6 =
Thanks to @eddraw for the updates!  
added translation: add pot file.  
fixed translation: use the name of the plugin as textdomain and load it.  


= 2.0.5 =
added form id to wh_hide_cf7_step_message filter.  


= 2.0.4 =
fixed plugin conflict.  


= 2.0.3 =
fixed issue where server variables may not be defined.  added some support for strings to be translatable.  


= 2.0.2 = 
Fix previous button not showing class attribute.  


= 2.0.1 = 
Minor fix to detecting if previous form was filled.  


= 2.0 = 
Added Form Tags to Form Tag Generator.  No more needing to update the Additional Settings tab.  
Added error alert when form is too large.  
Fixed Deprecated: preg_replace() error message.  
Fixed certain instances where the "Please fill out the form on the previous page" messages displayed unexpectedly.
Fixed issue where it was possible to type in the url of the next step after receiving validation errors on the current step.  


= 1.6 =
Added support for when contact form 7 ajax is disabled.

= 1.5 =
Added support for free_text in checkboxes and radio buttons.

= 1.4.4 =
fix empty checkboxes causing javascript error when going back.

= 1.4.3 =
fix exclusive checkboxes not saving on back.  added version to javascript.

= 1.4.2 =
fix radio button not saving on back. make sure its the last step before clearing cookies.

= 1.4.1 =
Fixed bug where tapping the Submit button on the final step submits form even with validation errors.

= 1.4 =
Updated to be compatible with Contact Form 7 version 3.9.

= 1.3.6 =
Updated readme to be more readable.
Fixed issue for servers with magic quotes turned off.  Fixes "Please fill out the form on the previous page" error.

= 1.3.5 =
Fix:  Also detect contact-form-7-3rd-party-integration/hidden.php so no conflicts arise if both are activated.

= 1.3.4 =
Fix:  Better detection of contact-form-7-modules plugin so no conflicts arise if both are activated.

= 1.3.3 =
Fixed back button functionality.

= 1.3.2 =
Some people are having trouble with cookies.  added 'cf7msm_force_session' filter to force to use session.

= 1.3.1 =
Added checks to prevent errors when contact form 7 is not installed.

= 1.3 =
Confused with the version numbers.  apparently 1.02 is greater than 1.1?

= 1.1 =
renamed all function names to be more consistent.
use cookies before falling back to session.
added back shortcode so users can go back to previous step.

= 1.02 =
updated version numbers.

= 1.01 =
updated readme.

= 1.0 =
Initial release.



== Upgrade Notice ==

= 2.2 =
As of version 2.2, Contact Form 7 version 4.8 is required.  There were some major changes in version 4.8 that broke a lot of the functionality of this plugin.  Additional Settings "on_sent_ok" is no longer used as of this version, please use the multistep form tag instead.


