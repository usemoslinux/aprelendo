$(document).ready((function(){-1===document.cookie.indexOf("accept_cookies")&&$("#eucookielaw").fadeIn(800,(function(){$(this).show()})),$("#removecookie").click((function(){setCookie("accept_cookies",!0,3650),$("#eucookielaw").slideDown(1500,(function(){$(this).remove()}))}))}));