function CheckboxSeleziona_onclick(v, table) {
	var ck = document.getElementById(table).getElementsByTagName("input");
	for(var i = 0; i < ck.length; i++){
		if(ck[i].type == 'checkbox'){
		   if(ck[i].checked==false){ 
		      ck[i].checked = v.checked;
			  select_row(ck[i]);
		   }else{
			  ck[i].checked = v.checked;
			  select_row(ck[i]);
		   }//else-if  
		}//if
	}//for
}//CheckboxSeleziona_onclick