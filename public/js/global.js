var global={init:function(){$("[rel=tooltip]").tooltip();$("#buymeabeer").click(function(a){a.preventDefault();$("#buymeabeer_button").trigger("click")});$(document).on("click",".add-to-list",function(){var b=$(this).data("itemId"),a=$(this).data("itemName");qty=$(this).data("itemQuantity");$.ajax({url:"/list/add",type:"post",data:{"item-id":b,qty:qty},beforeSend:function(){global.noty({type:"success",text:"Adding "+(qty>1?(qty+" x "):"")+a+" to your list"})}})})},noty:function(a){noty({text:a.text,type:a.type,layout:"bottomCenter",timeout:2500})},notification:function(a,b,c){$("#notifications").append('<div class="alert alert-'+a+'" id="'+c+'">'+b)},fade_and_destroy:function(a){a.fadeOut(500,function(){a.remove()})}};$(global.init);$(function(){var a=$.fn.popover.Constructor.prototype.show;$.fn.popover.Constructor.prototype.show=function(){var b=$("<div>").html('<div class="popover tour-tour">          <div class="arrow"></div>          <h3 class="popover-title"></h3>          <div class="popover-content"></div>          <nav class="popover-navigation">            <div class="btn-group">              <button class="btn btn-sm btn-default disabled" data-role="prev">« Prev</button>              <button class="btn btn-sm btn-default" data-role="next">Next »</button>            </div>            <button class="btn btn-sm btn-default" data-role="end">End tour</button>          </nav>        </div>');b.find("h3").before('<img src="/img/tour_moogle.png" class="tour_moogle">');this.options.template=b.html();a.call(this)}});String.prototype.capitalize=function(){return this.charAt(0).toUpperCase()+this.slice(1)};