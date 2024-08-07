function loadingBlock(){
	$(".overlay").css("display", "block");
}
function HideloadingBlock(){
	$(".overlay").css("display", "none");
}
function addTab(title, url){
	if ($('#tt').tabs('exists', title)){
		$('#tt').tabs('select', title);
		
		var tab = $('#tt').tabs('getSelected'); 
		var content = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
		$('#tt').tabs('update', {
			tab: tab,
			options: {
				title: title,
				content: content
			}
		});

	} else {
		var content = '<iframe scrolling="auto" frameborder="0"  src="'+url+'" style="width:100%;height:100%;"></iframe>';
		$('#tt').tabs('add',{
			title:title,
			content:content,
			tools:[{
				iconCls:'fa fa-times',
				handler:function(){
					$('#tt').tabs('select', title);
					var tab = $('#tt').tabs('getSelected');
					var index = $('#tt').tabs('getTabIndex',tab);
					$('#tt').tabs('close',index);
					
					var currentAmtTab = $(".tabs-header ul.tabs" ).children().length;
					if(currentAmtTab<=1){
						$(".tabs-header ul.tabs" ).addClass( "hiddenBlog" );
					}
				}
			}]
		});
		
		var currentAmtTab = $(".tabs-header ul.tabs" ).children().length;
		if(currentAmtTab>1){
			$(".tabs-header ul.tabs" ).removeClass( "hiddenBlog" );
		}else{
			$(".tabs-header ul.tabs" ).addClass( "hiddenBlog" );
		}
	}
}
function infoMessageAlert(messsageTitle,messageType=2){
	classMessage="info";
	if(messageType==1){
		classMessage="success";
	}
	Swal.fire({
		  icon: classMessage,
		  title: messsageTitle,
		  showConfirmButton: false,
		  timer: 1000,
		  timerProgressBar: true,
		  didOpen: (toast) => {
			toast.addEventListener('mouseenter', Swal.stopTimer)
			toast.addEventListener('mouseleave', Swal.resumeTimer)
		  }
	})
}
function messageAlert(pushStateUrl){
	
	params = (new URL(document.location)).searchParams;
	alertmg = params.get("alertmg");
	if(alertmg){
		messsageTitle = params.get("messsageTitle");
		classMessage = params.get("classMessage");
		if(classMessage==''){
			classMessage="info";
		}
		Swal.fire({
			  icon: classMessage,
			  title: messsageTitle,
			  showConfirmButton: false,
			  timer: 1000,
			  timerProgressBar: true,
			  didOpen: (toast) => {
				toast.addEventListener('mouseenter', Swal.stopTimer)
				toast.addEventListener('mouseleave', Swal.resumeTimer)
			  }
		}).then((result) => {
				window.history.pushState(null, null, pushStateUrl);
		})
	}
}

function doPrint() {
	var currentTitle = $(document).attr('title');
	var w = 1000;
	var h = 600;
	var left = Number((screen.width/2)-(w/2));
	var tops = Number((screen.height/2)-(h/2));

	var winPrint = window.open('', '', 'toolbar=0,scrollbars=0,status=0,width='+w+', height='+h+', top='+tops+', left='+left);
	winPrint.document.write('<title>'+currentTitle+'</title>'+dojo.byId('divPrint').innerHTML);
	winPrint.document.close();
	winPrint.focus();
	winPrint.print();
	winPrint.close();
}
function preview()
{ 
	var currentTitle = $(document).attr('title');
	var w = 1000;
	var h = 600;
	var left = Number((screen.width/2)-(w/2));
	var tops = Number((screen.height/2)-(h/2));
	
	var winPrint = window.open('', '', 'toolbar=0,scrollbars=0,status=0,width='+w+', height='+h+', top='+tops+', left='+left);
	winPrint.document.write('<title>'+currentTitle+'</title>'+dojo.byId('divPrint').innerHTML);
	
   docprint.document.close(); 
   docprint.focus(); 
}
function exportExcel(){
	var currentTitle = $(document).attr('title');
	currentTitle = currentTitle;
	$('#exportExcel').tableExport({type:'excel',escape:'false',fileName:currentTitle});
}