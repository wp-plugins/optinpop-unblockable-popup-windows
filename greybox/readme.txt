GreyBox is copyrighted work by Amir Salihefendic http://amix.dk/
It is based on AJS JavaScript library http://orangoo.com/labs/AJS/
It is realesed under LGPL 2.1
http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html

GreyBox official site: http://orangoo.com/labs/GreyBox/

----------------------------------------------------------------
This is modified version.

modified by iCoder.com 30 Nov 2007
modified file: loader_frame.html

added line #6:
	document.location = GB.url;

commented lines #8 - #11
	/*
    document.write('<script type="text/javascript" src="AJS.js"><\/script>');
    if(GB.use_fx) {
        document.write('<script type="text/javascript" src="AJS_fx.js"><\/script>');
	}
	*/

This Mod re-loads the page with GB.url URL
instead of loading the targed page in a nested frame.


modified file: gb_scripts.js

changed line #125
	var d={"name":"GB_frame","class":"GB_frame","frameBorder":0};

as:
	var d={"name":"GB_frame","class":"GB_frame","frameBorder":0,"scrolling":"no"};

This Mod forces a popup hides scrollers.

