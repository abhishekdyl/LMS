
-----------------------------PDF---------------------------------

<center><button class="btn btn-success" onclick="printDiv('printableArea')">Click to Print here</button></center>

<script type="text/javascript">
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>

--------------------------CSV--------------------------------

<script type="text/javascript">
      function fnExcelReport()
      {
          var tab_text="<table border="+"2px"+"><tr bgcolor="+"#87AFC6"+">";
          var textRange; var j=0;
          tab = document.getElementById("headerTable");

          for(j = 0 ; j < tab.rows.length ; j++) 
          {     
              tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
          }

          tab_text=tab_text+"</table>";
          tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");
          tab_text= tab_text.replace(/<img[^>]*>/gi,"");
          tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, "");

          var ua = window.navigator.userAgent;
          var msie = ua.indexOf("MSIE "); 

          if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))   
          {
              txtArea1.document.open("txt/html","replace");
              txtArea1.document.write(tab_text);
              txtArea1.document.close();
              txtArea1.focus(); 
              sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
          }  
          else              
              sa = window.open("data:application/vnd.ms-excel," + encodeURIComponent(tab_text));  

          return (sa);
      }
</script>

<a id="btnExport" class="btnExport" onclick="fnExcelReport();"><i class="fa fa-save"></i> Save as Excel</a>

-------------------------------AJAX------------------------------------------

var courseid_data = $(this).data('courseid');
var startdate_data = $(this).data('start');
var enddate_data = $(this).data('end');
$.ajax({
    url: "<?php echo $CFG->wwwroot.'/blocks/mcdean_mandatory_course_search/enrolAjax.php'; ?>",
    type: "post",
    data: { courseid: courseid_data, startdate: startdate_data, enddate: enddate_data } ,
    success: function (response) {
		 if(response == 'done') {
		  	window.location.href = "<?php echo $CFG->wwwroot.'/course/view.php?id='; ?>"+courseid_data;
		 } else {
		  	window.location.href = "<?php echo $CFG->wwwroot.'/blocks/mcdean_mandatory_course_search/mandatory_courselist.php'; ?>";
		 }
   	},
});	

---------------------------GET DAY,MONTH,YEAR,WEEK,DAY-----------------------

	$datetime_1 = '2022-04-10 11:15:30'; 
	$datetime_2 = '2022-04-12 13:30:45'; 
	 
	$start_datetime = new DateTime($datetime_1); 
	$diff = $start_datetime->diff(new DateTime($datetime_2)); 
	 
	echo $diff->days.' Days total<br>'; 
	echo $diff->y.' Years<br>'; 
	echo $diff->m.' Months<br>'; 
	echo $diff->d.' Days<br>'; 
	echo $diff->h.' Hours<br>'; 
	echo $diff->i.' Minutes<br>'; 
	echo $diff->s.' Seconds<br>';