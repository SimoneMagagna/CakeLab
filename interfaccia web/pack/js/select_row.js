function select_row(row) {
	if (row.checked){
 		row.value="off";
 		row.parentNode.parentNode.style.backgroundColor = 'red';
	}else{
 		row.value="on";
 		row.parentNode.parentNode.style.backgroundColor = '';
	}
}