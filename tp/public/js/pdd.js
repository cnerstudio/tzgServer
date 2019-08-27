/**
 * 
 */
  var url='../../api/pddapi/';
 $('.synchroData').on('click',function(){//同步
	  var shopid=$("#shop option:selected").val();
		 if(shopid==0){
			 $('#A1r').text('请选择店铺');
			 $("#A1").modal("show")
			 return false;
		 }else{
				  $("#modal-demo").modal("show")
				  $('.y_synchroData').on('click',function(){
					  var mer=$('#cat option:selected').val();
					  var tt= mer.substring(1)
					  $("#modal-demo").modal('hide')
					  $('#wait').html("<p>同步中,第<string id='sysd'>一</string>步<string id='sysdm'></string>,用时<string id='time'>0秒</string>...</p>");//$('#wait').text("同步中，请等待...")
					  $("#synchro").modal("show")
					  setTimeSys(1); 
					  sysajax(shopid,1,0,1,tt);
				  })
		 }
	  
  })
  
function sysajax(shopid,schedule,min,total,tt){
	$.ajax({
		url:url+'synData',
		type:'POST',
		data:'shop='+shopid+'&schedule='+schedule+'&min='+min+'&total='+total,
		dataType:'json',
		success:function(a){
				if(a.code==400){
						setTimeSys(2);
						$("#synchro").modal("hide");
						$('#su_synchro').modal("show");
						$('#su_txt').text('同步失败，原因：'+a.msg)
						$('.btn-re').on('click',function(){
							window.location.href='getlist?'+tt
					})
				}
			  else if(a.code==200){
				    setTimeSys(2);
				    var ttime=$('#time').text();
					 $('#su_txt').html("<p>同步成功,用时<string id='time'>"+ttime+"秒</string> </p>")
					  $('.btn-re').on('click',function(){
							window.location.href='getlist?'+tt
					})
					$("#synchro").modal("hide")
					$('#su_synchro').modal("show")
			  }else if(a.result==2){
				  if(schedule==1){
						$('#sysd').text('一');
						schedule++;
				  }else if(schedule==2){
						$('#sysd').text('二');
						var sd=parseInt(min*100/total);
						$('#sysdm').text("已同步"+sd+"%(共"+min+"/"+total+"条)");
						if(min<a.total){
								total=a.total;
							min++;
						}else{
							$('#sysd').text('三'); 
							schedule++;
							min=0;
						}
				  }else if(schedule==3){
					  if(a.min==0){
						$('#sysdm').text("正在同步上下架 id:"+a.id);
						total=a.total+"&id="+a.id;
					  }else{
						min++;
						total=a.total;
						var sd=parseInt(min*100/total);
						$('#sysdm').text("已同步"+sd+"%(共"+min+"/"+total+"条)");
					  }
					}
				  sysajax(shopid,schedule,min,total,tt)
			  }else{
					alert(a);
			  }
			},
	})
}
  
 /*设置同步时间*/
 function setTimeSys(state){
	 if(state==1){
		 var i=parseFloat($('#time').text());
		 t=setInterval(function(){
			 i++;
			if(i<60)
				$('#time').text(i+'秒');
			else
				$('#time').text((i/60).toFixed(2)+'分钟');
		 },1000)
	 }else{
		 clearTimeout(t);
	 }
 }

  function shelf(s){//s=1 上架 s=2下架;
	  var shopid=$("#shop option:selected").val();
	 if(shopid==0){
		 $('#A1r').text('请选择店铺');
		 $("#A1").modal("show")
		 return false;
	 }
	  
	  var data='';//id
	  $("input:checkbox[name='s']:checked").each(function(){
		  data+=$(this).val()+','
	  })
	  if(data==''){
		  $('#su_txt').text('请选择要上架的商品!')
		  $('#su_synchro').modal("show")
		  return false;
	  }
	  $('.title0').text('请确认')
	  $('#textp').text('是否确定选择的商品？')
	  $('#popup').modal("show")
	  var mer=$('#cat option:selected').val();
	  var source=$("#source").val();
	  var shopid=$("#shop option:selected").val();
	  var tt= mer.substring(1)
	  $('.btn_ok').on('click',function(){
		  $('#popup').modal("hide")
		  $('#wait').text("处理中...")
		  $('#synchro').modal("show")
		  $.ajax({
			  url:'downShelf',
			  type:'POST',
			  data:'data='+data+'&shop='+shopid+'&status='+s,
			  success:function(a){
				  $('#synchro').modal("hide")
				  if(a==1){
					  window.location.href='getlist?'+tt+'&supply='+source
				  }else{
					  $('#su_txt').text('修改失败，错误原因：'+a)
				  	$('#su_synchro').modal("show")
					 $('.btn-re').on('click',function(){
						 window.location.href='getlist?'+tt+'&supply='+source
					 })
				  }
			  }
		  })
	  })
  }

  /* 全选 */
  $(".qx").on('click',function(){
	  $('input[name="s"]').each(function(){
		  $(this).prop("checked",true)
	  })
  })
   $(".qbx").on('click',function(){
	  $('input[name="s"]').each(function(){
		  $(this).prop("checked",false)
	  })
  })
    $(".fx").on('click',function(){
	  $('input[name="s"]').each(function(){
		  if($(this).prop('checked')==true){
			  $(this).prop("checked",false)
		  }else{
			  $(this).prop("checked",true)
		  }
	  })
  })
  
function verif(){//验证店铺
	  var shopid=$("#shop option:selected").val();
		 if(shopid==0){
			 $('#A1r').text('请选择店铺');
			 $("#A1").modal("show")
			 return false;
		 }
  }
  
 $('.upprice').on('click',function(){
	 var symbol=$('input:radio[name="upprice"]:checked').val();
     if(symbol!=1 && symbol!=2){
    	 $('#A1r').text('请选择计算方法');
		 $("#A1").modal("show")
		 return false;
     }
     var vc=$('input[name="val"]').val();
     if(!vc){
    	 $('#A1r').text('请输入数值');
		 $("#A1").modal("show")
		 return false;
     }
	 var shopid=$("#shop option:selected").val();
		 if(shopid==0){
			 $('#A1r').text('请选择店铺');
			 $("#A1").modal("show")
			 return false;
		 }
		 var data='';
		  $("input:checkbox[name='s']:checked").each(function(){
			  data+=$(this).val()+','
		  })
		  if(data!=''){
			  $('#textp').text('是否确定修改选中的列？')
			  var id=1
		  }else{
			  $('#textp').text('是否确定修改选中的类目？')
			  data=$('#cat option:selected').attr('cit')
			  var id=2
			  if(data==-1){
				  $('#A1r').text('请选择修改的类目');
					 $("#A1").modal("show")
					 return false;
			  }
		  }
		  $('.title0').text('请确认')
		  var mer=$('#cat option:selected').val();
	     var tt= mer.substring(1)
		  $('#popup').modal("show")
		  $('.btn_ok').on('click',function(){
			  $('#popup').modal("hide")
			  $('#wait').text("处理中...")
			  $('#synchro').modal("show")
			  $.ajax({
				  url:'upprice',
				  type:'POST',
				  data:'id='+id+'&data='+data+'&synbol='+symbol+'&value='+vc+'&shop='+shopid,
				  dataType:'json',
				  success:function(a){
					  $('#synchro').modal("hide")
					 if(a.result==1){
						 $('#su_txt').html('<span>修改成功'+a.message.s+'条'+',修改失败'+a.message.e+'</span>')
					 }else{
						 $('#su_txt').html('<span>修改失败:'+a.message+'</span>')
					 }
					  $('#su_synchro').modal("show")
						 $('.btn-re').on('click',function(){
							 window.location.href='getlist?'+tt
						 })
				  }
			  })
		  }) 
		  
 }) 
  //关联商品
  $('.relation').on('click',function(){
	  var shopid=$("#shop option:selected").val();
		 if(shopid==0){
			 $('#A1r').text('请选择店铺');
			 $("#A1").modal("show")
			 return false;
		 }
	  var symbol=$('input:radio[name="upprice"]:checked').val();
	     if(symbol!=1 && symbol!=2){
	    	 $('#A1r').text('请选择计算方法');
			 $("#A1").modal("show")
			 return false;
	     }
	     var vc=$('input[name="val"]').val();
	     if(!vc){
	    	 $('#A1r').text('请输入数值');
			 $("#A1").modal("show")
			 return false;
	     }
	  $('#wait').html("处理中,用时<span id='ti'>0</span>秒...")
	  $('#synchro').modal("show")
	  var si=0;
	  times=setInterval(function(){
		  si++;
		  $('#ti').html(si)
	  },1000)
	  var mer=$('#cat option:selected').val();
	  var tt= mer.substring(1)
	  $.ajax({
		  url:url+'relation',
		  data:'shop='+shopid+'&enb='+vc+'&sym='+symbol,
		  type:'POST',
		  dataType:'json',
		  success:function(a){
			  clearInterval(times)
			  $('#synchro').modal("hide")
			  if(a.result==1){
					 $('#su_txt').html('<span>关联成功'+a.scc+'条 失败'+a.err+'条,用时<span id="sr"></span>秒</span>')
			  }else{
					 $('#su_txt').html('<span>运行错误，错误原因：'+a.message+',用时<span id="sr"></span>秒</span>')
			  }
			  $('#sr').text(si)
			  $('#su_synchro').modal("show")
				 $('.btn-re').on('click',function(){
					 window.location.href='getlist?'+tt
				 })
		  }
	  })
})
  