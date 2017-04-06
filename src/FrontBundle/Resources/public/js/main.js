$(document).ready(function() {

  // If we detect that string, we will add the fixed class to our .navbar-header with jQuery
  if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
       $('.navigation').removeClass('container');
  }
  
});
