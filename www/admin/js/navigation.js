/* Mobile */
$('#menu-wrap').prepend('<div id="menu-trigger">Menu</div>');		
$("#menu-trigger").on("click", function(){
	$("#menu").slideToggle();
});

// iPad
var isiPad = navigator.userAgent.match(/iPad/i) != null;
if (isiPad) $('#menu ul').addClass('no-transition');

function changeState(newstate){

  document.mainForm.nextState.value = newstate;
  document.mainForm.submit();

}