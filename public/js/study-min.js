$(document).ready((function(){let e="",a="";const t=$("#dicFrame");let n=$(),r=[],d=10,o=0,s=0,l=[["0",0,"bg-success","Excellent"],["1",0,"bg-info","Partial"],["2",0,"bg-warning","Fuzzy"],["3",0,"bg-danger","No recall"]];function c(e,a){$("#alert-msg").html(e).removeClass().addClass("alert "+a),$(window).scrollTop(0)}function i(e){(function(){if(0==d)return $("#card-header").text("Sorry, no cards to practice"),$("#card-text").html("<div class='fas fa-exclamation-circle text-danger display-3'></div><div class='mt-3'>It seems you don't have any cards left for learning.</div>"),$("#card-footer").addClass("d-none"),$("#card-loader").addClass("d-none"),!0;if(s>d-1){$("#card-header").text("Congratulations!");let e="";for(let a=0;a<l.length;a++){let t=l[a][1],n=t/d*100;e+="<div class='progress-bar "+l[a][2]+"' role='progressbar' aria-valuenow='"+n+"' aria-valuemin='0' aria-valuemax='100' style='width: "+n+"%' title='"+l[a][3]+": "+t+" answer(s)'>"+n+" %</div>"}return $("#card-text").html("<div class='fa-solid fa-flag-checkered text-primary display-3 mt-3'></div><div class='mt-3'>You have reached the end of your study.</div><div class='mt-3'>These were your results:</div><div class='progress mx-auto mt-3 fw-bold' style='height: 25px;max-width: 550px'>"+e+"</div><div class='small mt-4'>If you want to continue, you can refresh this page (F5).<br>However, we strongly recommend that you keep your study sessions short and take rest intervals.</div>"),$("#card-footer").addClass("d-none"),$("#card-loader").addClass("d-none"),!0}return!1})()||($("#card-loader").removeClass("d-none"),$("#card-text").empty(),$("#card-header").html("Looking for examples of "+e+"..."),$.ajax({type:"POST",url:"ajax/getcards.php",data:{word:e},dataType:"json"}).done((function(a){let t="",n=0;const l=new RegExp("[^\\n.?!]*(?<![\\p{L}])"+e+"(?![\\p{L}])[^\\n.?!]*[.?!\\n)]","gmiu"),c=new RegExp("(?<![\\p{L}|\\d])"+e+"(?![\\p{L}|\\d])","gmiu"),p=new RegExp("^[\\s\\d]+|[\\s\\d]+$","g");if(a.forEach((a=>{let r;for(;null!==(r=l.exec(a.text));)r.index===l.lastIndex&&l.lastIndex++,n<3&&r.forEach(((a,r)=>{(a=u(a=a.replace(p,"")))!==e&&(a=a.replace(c,(function(e,a){return void 0===a?e:"<a class='word fw-bold'>"+e.replace(/\s\s+/g," ")+"</a>"})),t+=t.search(a.replace(/[-\/\\^$*+?.()|[\]{}]/g,"\\$&"))>0?"":"<p>"+a+"</p>\n",n++)}))})),""==t)return $("#card-header").html("Skipped. No examples found."),o++,void i(r[o]);$("#card").data("word",e),$("#card-loader").addClass("d-none"),$("#card-counter").text(s+1+"/"+d),$("#card-header").html(e),$("#card-text").append(t),$(".btn-answer").prop("disabled",!1),o++,s++})).fail((function(e,a,t){c("Oops! There was an unexpected error trying to fetch example sentences for this word.","alert-danger")})))}function u(e){if((e.match(/"/g)||[]).length%2!=0){if(e.length<=4)return e;let a=e.substring(0,2).replace('"',""),t=e.substring(e.length-2).replace('"',"");return a+e.substring(2,e.length-2)+t}return e}function p(e){const t=e.parent("p").text().trim();return a.replace("%s",encodeURIComponent(t))}$("#btn-translate").removeClass("ps-0"),$("#btnremove").hide(),$("#btnadd").hide(),$("#btncancel").addClass("ms-auto").html("&#x2715"),$(".modal-header").addClass("p-0"),$(".btn-answer").prop("disabled",!0),$.ajax({url:"/ajax/getdicuris.php",type:"GET",dataType:"json"}).done((function(t){null==t.error_msg&&(e=t.dictionary_uri,a=t.translator_uri)})),$.ajax({type:"POST",url:"ajax/getcards.php",dataType:"json"}).done((function(e){r=e.map((function(e,a){return e.word.replace(/\r?\n|\r/g," ")})),d=r.length>d?d:r.length,$("#card-counter").text("1/"+d),i(r[0])})).fail((function(e,a,t){c("Oops! There was an unexpected error trying to fetch the list of words you are learning in this language.","alert-danger")})),$("body").on("click",".word",(function(a){n=$(this);const r=e.replace("%s",encodeURI(n.text()));$("#btnadd").text("Forgot"),$("#loading-spinner").attr("class","lds-ellipsis m-auto"),t.attr("class","d-none"),t.get(0).contentWindow.location.replace(r),$("#myModal").modal("show")})),t.on("load",(function(){$("#loading-spinner").attr("class","d-none"),t.removeClass()})),$(".btn-answer").click((function(e){e.preventDefault();const a=$("#card").data("word"),t=$(this).attr("value");l[t][1]=l[t][1]+1,$(".btn-answer").prop("disabled",!0),$.ajax({type:"POST",url:"ajax/updatecard.php",data:{word:a,answer:t}}).done((function(e){i(r[o])})).fail((function(e,a,t){c("There was an unexpected error updating this word's status","alert-danger")}))})),$("#btn-translate").on("click",(function(){window.open(p(n))})),$(document).on("contextmenu",(function(e){return!/iPhone|iPad|iPod|Android/i.test(navigator.userAgent)&&$(e.target).is(".word")&&window.open(p($(e.target))),!1})),$(document).keypress((function(e){if(!$(".btn-answer").prop("disabled"))switch(e.which){case 49:$("#btn-answer-no-recall").click();break;case 50:$("#btn-answer-fuzzy").click();break;case 51:$("#btn-answer-partial").click();break;case 52:$("#btn-answer-excellent").click();break;default:break}}))}));