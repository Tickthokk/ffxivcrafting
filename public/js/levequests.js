var levequests={init:function(){levequests.events(),levequests.decipher_hash()},decipher_hash:function(){var a=document.location.hash;return""==a?!1:!0},events:function(){$(".class-selector").click(function(){var a=$(this),b=parseInt(5*Math.floor(a.data("level")/5)),c=$(".leve-level-select a[data-level="+b+"]");$(".class-selector.active").removeClass("active"),a.addClass("active"),$(".leve-level-select a.active").removeClass("active"),c.addClass("active"),levequests.load_leves()}),$(".leve-level-select a").click(function(a){a.preventDefault();var b=$(this);$(".leve-level-select a.active").removeClass("active"),b.addClass("active"),levequests.load_leves()}),$(".class-selector.active").trigger("click")},load_leves:function(){var a=$(".jobs-list label.active").data("class"),b=$(".leve-level-select a.active").data("level"),c=$("#"+a+"-"+b+"-leves");$(".leve-section .table-responsive").addClass("hidden"),c.removeClass("hidden"),$('img[src=""]',c).each(function(){this.src=$(this).data("src")})}};$(levequests.init);