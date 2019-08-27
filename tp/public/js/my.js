/**
 * 
 */
$(function(){
	//登录ajax请求
	$("input[name='login']").on("click",function(){
		var uname=$("input[name='name']").val();
		var upass=$("input[name='pass']").val();
		var capt=$("input[name='capt']").val();//验证码
		//ajax请求user
	$.ajax({
		url:"jub",//判断控制
		data:"n="+uname+"&p="+upass+"&capt="+capt,
		type:"POST",
		success:function(a){
			if(a==1){
				$(".modal-body").children("p").text("请输入用户名和密码!")
			}
			if(a==2){
				$(".modal-body").children("p").text("验证码错误!")
			}
			if(a==3){
				$(".modal-body").children("p").text("用户不存在!")
			}
			if(a==4){
				$(".modal-body").children("p").text("密码错误!")
			}
			if(a==5){
				window.location.href="../../admin/index/show";
				return;
				
			}
			
			
			$("#modal-demo").modal("show")
		}
	})
		
		
	})
	
	//退出登录
	$("#tlogin").on("click",function(){
		$.ajax({
			url:"td",
			success:function(a){
				if(a==1){
					window.location.href="../../admin/user/login";
				}
			}
		})
	})
	
	
	$(".ttp").each(function(){
		$(this).on("click",function(){
			var t=$(this).text();
			
			if(t=="显示详细"){
				$("#t1").hide()
				$("#t2").show()
			}else{
				$("#t2").hide()
				$("#t1").show();
			}
			
			
			
		})
	})
	
		$("#csvdown").on('click',function(){
           $('#iframe').attr('src',"csvDown");
	})
	$("#xlsxdown").on('click',function(){
		var num=$('#total').text();
		if(num>1000){
			$('#popuptext').text('记录大于1000条，请用csv下载');
			$('#z-popup').modal("show")
			
		}else{
			 $('#iframe').attr('src',"xlsxdown");
		}
       
		
	})
	
})



