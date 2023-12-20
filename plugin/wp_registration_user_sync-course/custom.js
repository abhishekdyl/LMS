function increaseValue() 
{
	// alert("dskjjfskd");
	var value = 0;
	var m_count = jQuery('#member_count').val();
	if(m_count==undefined){
	 value = parseInt(document.getElementById('number').value, 10); 
	value = isNaN(value) ? 0 : value;
	value++;
	value > 10 ? value = 10 : value;
	updatePrice(value);
	document.getElementById('number').value = value;
	}else
	{
	 value = parseInt(document.getElementById('number').value, 10);
		value = isNaN(value) ? 0 : value;
		value = 1+value-m_count;
	value++;
	m_count++;
	parseInt(value+m_count-2) > 10 ? parseInt(value+m_count-2) = 10 : parseInt(value+m_count-2);
	
	updatePrice(value);
	document.getElementById('number').value = value+m_count-2;
	
	}
	
	jQuery('#child').text(value);
	if(value!=1)
	{
		jQuery('#cstring').text('children');
	}
	else
	{
		jQuery('#cstring').text('child');
	}
}

function decreaseValue(){
	var value = 0;
	var m_count = jQuery('#member_count').val();
	if(m_count==undefined)
	{
	 value = parseInt(document.getElementById('number').value, 10);
		value = isNaN(value) ? 0 : value;
		value--;
		value < 1 ? value = 1 : '';		
		updatePrice(value);
		document.getElementById('number').value = value;
	}
	else
	{
		value = parseInt(document.getElementById('number').value, 10);
		value = isNaN(value) ? 0 : value;
		
		
		//value--;
		value = parseInt(value)-parseInt(m_count);
		 			 
		value < 1 ? value = 1 : '';
		
		updatePrice(value);
		
		document.getElementById('number').value = parseInt(m_count)+parseInt(value)-1;
	}
	jQuery('#child').text(value);
	if(value!=1)
	{
		jQuery('#cstring').text('children');
	}
	else
	{
		jQuery('#cstring').text('child');
	}
}
function updatePrice(value){
	console.log(value+'price');
	var m = jQuery('[name=monthd]').val();
	
	var y = jQuery('[name=yeard]').val();
	//alert('mon '+m+' yr '+y);
	switch(jQuery('[name=planBtn]:checked').val()){
		case "0":
			var month = parseFloat(m,10)+(2*value-2);
			//alert(month+'month');
			jQuery('#pmonth').text(month.toFixed(2));
			jQuery('[name=month]').val('£'+month.toFixed(2));
			jQuery('#hprize').text('£'+month.toFixed(2));
		break;
		case "1":
			var year = parseFloat(y,10)+(20*value-20);
			//alert(year+'year');
			jQuery('#pyear').text(year.toFixed(2));
			jQuery('[name=year]').val('£'+year.toFixed(2));
			jQuery('#hprize').text('£'+year.toFixed(2));
			
		break; 
	}
}  