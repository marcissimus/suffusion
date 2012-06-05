// ColorBox v1.3.16 - a full featured, light-weight, customizable lightbox based on jQuery 1.3+
// Copyright (c) 2011 Jack Moore - jack@colorpowered.com
// Licensed under the MIT license: http://www.opensource.org/licenses/mit-license.php
(function(a,b,c){function ba(b){if(!T){O=b;Z(a.extend(J,a.data(O,e)));x=a(O);P=0;if(J.rel!=="nofollow"){x=a("."+V).filter(function(){var b=a.data(this,e).rel||this.rel;return b===J.rel});P=x.index(O);if(P===-1){x=x.add(O);P=x.length-1}}if(!R){R=S=true;q.show();if(J.returnFocus){try{O.blur();a(O).one(k,function(){try{this.focus()}catch(a){}})}catch(c){}}p.css({opacity:+J.opacity,cursor:J.overlayClose?"pointer":"auto"}).show();J.w=X(J.initialWidth,"x");J.h=X(J.initialHeight,"y");U.position(0);if(n){y.bind("resize."+o+" scroll."+o,function(){p.css({width:y.width(),height:y.height(),top:y.scrollTop(),left:y.scrollLeft()})}).trigger("resize."+o)}$(g,J.onOpen);I.add(C).hide();H.html(J.close).show()}U.load(true)}}function _(){var a,b=f+"Slideshow_",c="click."+f,d,e,g;if(J.slideshow&&x[1]){d=function(){E.text(J.slideshowStop).unbind(c).bind(i,function(){if(P<x.length-1||J.loop){a=setTimeout(U.next,J.slideshowSpeed)}}).bind(h,function(){clearTimeout(a)}).one(c+" "+j,e);q.removeClass(b+"off").addClass(b+"on");a=setTimeout(U.next,J.slideshowSpeed)};e=function(){clearTimeout(a);E.text(J.slideshowStart).unbind([i,h,j,c].join(" ")).one(c,d);q.removeClass(b+"on").addClass(b+"off")};if(J.slideshowAuto){d()}else{e()}}}function $(b,c){if(c){c.call(O)}a.event.trigger(b)}function Z(b){for(var c in b){if(a.isFunction(b[c])&&c.substring(0,2)!=="on"){b[c]=b[c].call(O)}}b.rel=b.rel||O.rel||"nofollow";b.href=a.trim(b.href||a(O).attr("href"));b.title=b.title||a(O).data("title")||O.title}function Y(a){return J.photo||/\.(gif|png|jpg|jpeg|bmp)(?:\?([^#]*))?(?:#(\.*))?$/i.test(a)}function X(a,b){b=b==="x"?y.width():y.height();return typeof a==="string"?Math.round(/%/.test(a)?b/100*parseInt(a,10):parseInt(a,10)):a}function W(c,d){var e=b.createElement("div");if(c){e.id=f+c}e.style.cssText=d||false;return a(e)}var d={transition:"elastic",speed:300,width:false,initialWidth:"600",innerWidth:false,maxWidth:false,height:false,initialHeight:"450",innerHeight:false,maxHeight:false,scalePhotos:true,scrolling:true,inline:false,html:false,iframe:false,fastIframe:true,photo:false,href:false,title:false,rel:false,opacity:.9,preloading:true,current:"image {current} of {total}",previous:"previous",next:"next",close:"close",open:false,returnFocus:true,loop:true,slideshow:false,slideshowAuto:true,slideshowSpeed:2500,slideshowStart:"start slideshow",slideshowStop:"stop slideshow",onOpen:false,onLoad:false,onComplete:false,onCleanup:false,onClosed:false,overlayClose:true,escKey:true,arrowKey:true},e="colorbox",f="cbox",g=f+"_open",h=f+"_load",i=f+"_complete",j=f+"_cleanup",k=f+"_closed",l=f+"_purge",m=a.browser.msie&&!a.support.opacity,n=m&&a.browser.version<7,o=f+"_IE6",p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J={},K,L,M,N,O,P,Q,R,S,T=false,U,V=f+"Element";U=a.fn[e]=a[e]=function(b,c){var f=this,g;if(!f[0]&&f.selector){return f}b=b||{};if(c){b.onComplete=c}if(!f[0]||f.selector===undefined){f=a("<a/>");b.open=true}f.each(function(){a.data(this,e,a.extend({},a.data(this,e)||d,b));a(this).addClass(V)});g=b.open;if(a.isFunction(g)){g=g.call(f)}if(g){ba(f[0])}return f};U.init=function(){y=a(c);q=W().attr({id:e,"class":m?f+(n?"IE6":"IE"):""});p=W("Overlay",n?"position:absolute":"").hide();r=W("Wrapper");s=W("Content").append(z=W("LoadedContent","width:0; height:0; overflow:hidden"),B=W("LoadingOverlay").add(W("LoadingGraphic")),C=W("Title"),D=W("Current"),F=W("Next"),G=W("Previous"),E=W("Slideshow").bind(g,_),H=W("Close"));r.append(W().append(W("TopLeft"),t=W("TopCenter"),W("TopRight")),W(false,"clear:left").append(u=W("MiddleLeft"),s,v=W("MiddleRight")),W(false,"clear:left").append(W("BottomLeft"),w=W("BottomCenter"),W("BottomRight"))).children().children().css({"float":"left"});A=W(false,"position:absolute; width:9999px; visibility:hidden; display:none");a("body").prepend(p,q.append(r,A));s.children().hover(function(){a(this).addClass("hover")},function(){a(this).removeClass("hover")}).addClass("hover");K=t.height()+w.height()+s.outerHeight(true)-s.height();L=u.width()+v.width()+s.outerWidth(true)-s.width();M=z.outerHeight(true);N=z.outerWidth(true);q.css({"padding-bottom":K,"padding-right":L}).hide();F.click(function(){U.next()});G.click(function(){U.prev()});H.click(function(){U.close()});I=F.add(G).add(D).add(E);s.children().removeClass("hover");a("."+V).live("click",function(a){if(!(a.button!==0&&typeof a.button!=="undefined"||a.ctrlKey||a.shiftKey||a.altKey)){a.preventDefault();ba(this)}});p.click(function(){if(J.overlayClose){U.close()}});a(b).bind("keydown."+f,function(a){var b=a.keyCode;if(R&&J.escKey&&b===27){a.preventDefault();U.close()}if(R&&J.arrowKey&&x[1]){if(b===37){a.preventDefault();G.click()}else if(b===39){a.preventDefault();F.click()}}})};U.remove=function(){q.add(p).remove();a("."+V).die("click").removeData(e).removeClass(V)};U.position=function(a,c){function g(a){t[0].style.width=w[0].style.width=s[0].style.width=a.style.width;B[0].style.height=B[1].style.height=s[0].style.height=u[0].style.height=v[0].style.height=a.style.height}var d,e=Math.max(b.documentElement.clientHeight-J.h-M-K,0)/2+y.scrollTop(),f=Math.max(y.width()-J.w-N-L,0)/2+y.scrollLeft();d=q.width()===J.w+N&&q.height()===J.h+M?0:a;r[0].style.width=r[0].style.height="9999px";q.dequeue().animate({width:J.w+N,height:J.h+M,top:e,left:f},{duration:d,complete:function(){g(this);S=false;r[0].style.width=J.w+N+L+"px";r[0].style.height=J.h+M+K+"px";if(c){c()}},step:function(){g(this)}})};U.resize=function(a){if(R){a=a||{};if(a.width){J.w=X(a.width,"x")-N-L}if(a.innerWidth){J.w=X(a.innerWidth,"x")}z.css({width:J.w});if(a.height){J.h=X(a.height,"y")-M-K}if(a.innerHeight){J.h=X(a.innerHeight,"y")}if(!a.innerHeight&&!a.height){var b=z.wrapInner("<div style='overflow:auto'></div>").children();J.h=b.height();b.replaceWith(b.children())}z.css({height:J.h});U.position(J.transition==="none"?0:J.speed)}};U.prep=function(b){function h(b){U.position(b,function(){var b,d,g,h,j=x.length,k,n;if(!R){return}n=function(){B.hide();$(i,J.onComplete)};if(m){if(Q){z.fadeIn(100)}}C.html(J.title).add(z).show();if(j>1){if(typeof J.current==="string"){D.html(J.current.replace(/\{current\}/,P+1).replace(/\{total\}/,j)).show()}F[J.loop||P<j-1?"show":"hide"]().html(J.next);G[J.loop||P?"show":"hide"]().html(J.previous);b=P?x[P-1]:x[j-1];g=P<j-1?x[P+1]:x[0];if(J.slideshow){E.show()}if(J.preloading){h=a.data(g,e).href||g.href;d=a.data(b,e).href||b.href;h=a.isFunction(h)?h.call(g):h;d=a.isFunction(d)?d.call(b):d;if(Y(h)){a("<img/>")[0].src=h}if(Y(d)){a("<img/>")[0].src=d}}}else{I.hide()}if(J.iframe){k=a("<iframe/>").addClass(f+"Iframe")[0];if(J.fastIframe){n()}else{a(k).load(n)}k.name=f+ +(new Date);k.src=J.href;if(!J.scrolling){k.scrolling="no"}if(m){k.frameBorder=0;k.allowTransparency="true"}a(k).appendTo(z).one(l,function(){k.src="//about:blank"})}else{n()}if(J.transition==="fade"){q.fadeTo(c,1,function(){q[0].style.filter=""})}else{q[0].style.filter=""}y.bind("resize."+f,function(){U.position(0)})})}function g(){J.h=J.h||z.height();J.h=J.mh&&J.mh<J.h?J.mh:J.h;return J.h}function d(){J.w=J.w||z.width();J.w=J.mw&&J.mw<J.w?J.mw:J.w;return J.w}if(!R){return}var c=J.transition==="none"?0:J.speed;y.unbind("resize."+f);z.remove();z=W("LoadedContent").html(b);z.hide().appendTo(A.show()).css({width:d(),overflow:J.scrolling?"auto":"hidden"}).css({height:g()}).prependTo(s);A.hide();a(Q).css({"float":"none"});if(n){a("select").not(q.find("select")).filter(function(){return this.style.visibility!=="hidden"}).css({visibility:"hidden"}).one(j,function(){this.style.visibility="inherit"})}if(J.transition==="fade"){q.fadeTo(c,0,function(){h(0)})}else{h(c)}};U.load=function(b){var c,d,g=U.prep;S=true;Q=false;O=x[P];if(!b){Z(a.extend(J,a.data(O,e)))}$(l);$(h,J.onLoad);J.h=J.height?X(J.height,"y")-M-K:J.innerHeight&&X(J.innerHeight,"y");J.w=J.width?X(J.width,"x")-N-L:J.innerWidth&&X(J.innerWidth,"x");J.mw=J.w;J.mh=J.h;if(J.maxWidth){J.mw=X(J.maxWidth,"x")-N-L;J.mw=J.w&&J.w<J.mw?J.w:J.mw}if(J.maxHeight){J.mh=X(J.maxHeight,"y")-M-K;J.mh=J.h&&J.h<J.mh?J.h:J.mh}c=J.href;B.show();if(J.inline){W().hide().insertBefore(a(c)[0]).one(l,function(){a(this).replaceWith(z.children())});g(a(c))}else if(J.iframe){g(" ")}else if(J.html){g(J.html)}else if(Y(c)){a(Q=new Image).addClass(f+"Photo").error(function(){J.title=false;g(W("Error").text("This image could not be loaded"))}).load(function(){var a;Q.onload=null;if(J.scalePhotos){d=function(){Q.height-=Q.height*a;Q.width-=Q.width*a};if(J.mw&&Q.width>J.mw){a=(Q.width-J.mw)/Q.width;d()}if(J.mh&&Q.height>J.mh){a=(Q.height-J.mh)/Q.height;d()}}if(J.h){Q.style.marginTop=Math.max(J.h-Q.height,0)/2+"px"}if(x[1]&&(P<x.length-1||J.loop)){Q.style.cursor="pointer";Q.onclick=function(){U.next()}}if(m){Q.style.msInterpolationMode="bicubic"}setTimeout(function(){g(Q)},1)});setTimeout(function(){Q.src=c},1)}else if(c){A.load(c,function(b,c,d){g(c==="error"?W("Error").text("Request unsuccessful: "+d.statusText):a(this).contents())})}};U.next=function(){if(!S&&x[1]&&(P<x.length-1||J.loop)){P=P<x.length-1?P+1:0;U.load()}};U.prev=function(){if(!S&&x[1]&&(P||J.loop)){P=P?P-1:x.length-1;U.load()}};U.close=function(){if(R&&!T){T=true;R=false;$(j,J.onCleanup);y.unbind("."+f+" ."+o);p.fadeTo(200,0);q.stop().fadeTo(300,0,function(){q.add(p).css({opacity:1,cursor:"auto"}).hide();$(l);z.remove();setTimeout(function(){T=false;$(k,J.onClosed)},1)})}};U.element=function(){return a(O)};U.settings=d;a(U.init)})(jQuery,document,this);