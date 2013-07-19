 function hideShowLinksNavigateStep(show){  
  if(document.getElementById('form[DYN_FORWARD]')){
    
    if(document.getElementById('form[DYN_FORWARD]').parentNode){
      if(document.getElementById('form[DYN_FORWARD]').parentNode.parentNode){
        document.getElementById('form[DYN_FORWARD]').parentNode.parentNode.style.display= show?'':'none';
      }
    }
  }
  if(document.getElementById('form[DYN_BACKWARD]')){
    
    if(document.getElementById('form[DYN_BACKWARD]').parentNode){
      if(document.getElementById('form[DYN_BACKWARD]').parentNode.parentNode){
        document.getElementById('form[DYN_BACKWARD]').parentNode.parentNode.style.display= show?'':'none';
      }
    }
  }
}

window.onload = function() {
hideShowLinksNavigateStep(false);
}