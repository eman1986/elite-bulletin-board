/**
 *
 * Copyright (c) 2006/2007 Sam Collett (http://www.texotela.co.uk)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 * 
 * Version 2.0
 * Demo: http://www.texotela.co.uk/code/jquery/newsticker/
 *
 * $LastChangedDate: 2007-05-29 11:31:36 +0100 (Tue, 29 May 2007) $
 * $Rev: 2005 $
 *
 * A basic news ticker.
 *
 * @name     newsticker (or newsTicker)
 * @param    delay      Delay (in milliseconds) between iterations. Default 4 seconds (4000ms)
 * @author   Sam Collett (http://www.texotela.co.uk)
 * @example  $("#news").newsticker(); // or $("#news").newsTicker(5000);
 *
*/
 (function(a){a.fn.newsTicker=a.fn.newsticker=function(b){return b=b||4e3,initTicker=function(b){stopTicker(b),b.items=a("li",b),b.items.not(":eq(0)").hide().end(),b.currentitem=0,startTicker(b)},startTicker=function(a){a.tickfn=setInterval(function(){doTick(a)},b)},stopTicker=function(a){clearInterval(a.tickfn)},pauseTicker=function(a){a.pause=!0},resumeTicker=function(a){a.pause=!1},doTick=function(b){b.pause||(b.pause=!0,a(b.items[b.currentitem]).fadeOut("slow",function(){a(this).hide(),b.currentitem=++b.currentitem%b.items.size(),a(b.items[b.currentitem]).fadeIn("slow",function(){b.pause=!1})}))},this.each(function(){"ul"==this.nodeName.toLowerCase()&&initTicker(this)}).addClass("newsticker").hover(function(){pauseTicker(this)},function(){resumeTicker(this)}),this}})(jQuery);
