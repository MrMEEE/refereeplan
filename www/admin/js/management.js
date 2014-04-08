function changeGameSource(){
	$.post("ajax/refereeplan.ajax.administration.php", {'changeSource':$("#sourceSelector :selected").text()});
	showUpdated();
}
