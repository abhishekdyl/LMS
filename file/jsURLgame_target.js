
<!DOCTYPE html>
<html>
<body>

<h2 onclick="newDoc()" >JavaScript</h2>

<h3>The window.location object</h3>

<p id="demo"></p>

<script>
document.getElementById("demo").innerHTML = 
"The full URL of this page is:<br>" + window.location.href + "<br/><br/>" +

window.location.protocol+ "<br/>"+
window.location.hostname + "<br/><br/>"+

window.location.origin+ "<br/>" +
window.location.pathname + "<br/>" + 
window.location.search+ "<br/>"  
;

function newDoc() {
  window.location.assign("https://www.w3schools.com")
}
</script>

</body>
</html>




<script>

$(document).ready(function(){
let searchParams = new URLSearchParams(window.location.search);
console.log('searchParams',searchParams);
    $(".cmid-4 .my_course_content_container").addClass("container");
$(".cmid-4 .custom-block-title").addClass("container");
});



$(function(){

setTimeout(function(){
console.log('ddddd');
$('body').find("[href='#panel-reports']").closest('.panel').hide();
},200);
if (window.location.pathname == "/" || window.location.pathname == "/index.php"  ) {
  // Index (home) page
   window.location.href=window.location.origin + "/mod/page/view.php?id=4";
}


$('[data-key="home"] a').click(function(e){
   e.preventDefault();
console.log(window.location.origin);
console.log( window.location.href);
  window.location.href=window.location.origin +"/mod/page/view.php?id=4";
});

});

</script>
