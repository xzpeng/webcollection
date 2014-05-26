<style>
.postbox h3 {
	font-family: Georgia, "Times New Roman", "Bitstream Charter", Times, serif;
	font-size: 15px;
	padding: 10px 10px;
	margin: 0;
	line-height: 1;
}
.apdelete{padding:2px;color:#aa0000;text-decoration:none;} 
.apdelete:hover{background-color:#aa0000;color:#ffffff;}/*#d54e21;*/
.apmatchtype{
  margin-top:6px;
  border-top-style: dashed;
  border-top-width: 1px;
  border-top-color: #21759b;
}
.autoposttable select{vertical-align:middle;}
.autoposttable label{vertical-align:middle;}
.autoposttable input{vertical-align:middle;}
</style>
<?php global $t_f_ap_cnofig,$t_f_ap_updated_record;?>
<?php 
$id = @$_REQUEST['id'];
?>
<script type="text/javascript">
jQuery(function($){
   $("h3.hndle").click(function(){$(this).next(".inside").slideToggle('fast');});
});
jQuery(function($){
   $(".apShowHtml").click(function(){$(this).next(".apdebugHtml").slideToggle('fast');});
});
</script>

<?php 
$saction = @$_REQUEST['saction'];
switch($saction){
 case 'new':
?> 
<div class="wrap">
<div class="icon32" id="icon-wp-autopost"><br/></div>
<h2>Auto Post - New Task</h2>
<form id="myform"  method="post" action="admin.php?page=wp-autopost/wp-autopost-tasklist.php" > 
<input type="hidden" name="saction" id="saction" value="newConfig">
  <br/> 
<?php $count = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_cnofig); ?>
<?php if($count>=1){ ?>
  <div  class="updated fade"><p><?php echo __('Free version can only create one task','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></p></div> 
  <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php" class="button"><?php echo __('Return','wp-autopost'); ?></a>
<?php }else{ ?>
  <table> 	   
       <tbody id="the-list">         	  
       <tr> 
		 <td width="10%"><?php echo __('Task Name','wp-autopost'); ?>:</td>
		 <td><input type="text" name="config_name" id="config_name" value=""></td>
	   </tr>	   
	   </tbody>
  </table> 
  <p class="submit"><input type="button" class="button-primary" value="<?php echo __('Submit'); ?>"  onclick="addNew()"/> <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php" class="button"><?php echo __('Return','wp-autopost'); ?></a></p>
<?php } ?>
</form>

</div>

<script type="text/javascript">
function addNew(){
  if(document.getElementById("config_name").value=='')return;
  document.getElementById("myform").submit();
}
</script>


 
<?php break;
 case 'edit':
 case 'save_url_list':
 case 'test_url_list':
 case 'save_crawl':
 case 'test_crawl':
 case 'testFetch':
 case 'SaveConfigOption':
 case 'editSubmit':

?>

<?php
if($saction=='editSubmit'&&$_POST['saction1']=='changePostType'){
  $wpdb->query($wpdb->prepare("update $t_f_ap_cnofig  set
               post_type = %s 
			   WHERE id = %d",$_POST['post_type'],$_POST['id'])
			   );
  $showBox1=true;
}
elseif($saction=='editSubmit'){
 $post_category = @$_POST['post_category1'];
 
 $cat='';
 if($post_category!=null){
  foreach($post_category as $cate){
     $cat.= $cate.',';
  }
 }
 $cat = substr($cat,0,-1);
 if($_POST['post_type']=='page'){
   $cat=null;
 }


 $charset = $_POST['charset'];
 if($charset==0)$page_charset='0';
 else $page_charset= $_POST['page_charset'];
 if(trim($page_charset)=='')$page_charset='UTF-8';

 $auto_sets = array(); 
 $auto_sets[0] = 0;
 $auto_sets[1] = 0;
 $auto_sets[2] = intval($_POST['publish_status']);

 $proxy = array();
 if($_POST['use_proxy']==1){
   $proxyOptions = get_option('wp-autopost-proxy');
   if($proxyOptions['ip']==''||$proxyOptions['ip']==null){
     $msg = '<div class="error"><p><a href="admin.php?page=wp-autopost/wp-autopost-proxy.php">'.__( 'Use proxy please set Proxy Options first!', 'wp-autopost' ).'</a></p></div>';
	 $proxy[0]=0;
   }else{
     $proxy[0]=1;
   }
 }else{
   $proxy[0]=0;
 }
 $proxy[1]=intval($_POST['hide_ip']);

 $post_scheduled = array();
 $post_scheduled[0] = $_POST['post_scheduled'];
 $post_scheduled[1] = intval($_POST['post_scheduled_hour']);
 if($post_scheduled[1]<0)$post_scheduled[1]=0;
 if($post_scheduled[1]>23)$post_scheduled[1]=23;
 $post_scheduled[2] = intval($_POST['post_scheduled_minute']);
 if($post_scheduled[2]<0)$post_scheduled[1]=0;
 if($post_scheduled[2]>59)$post_scheduled[1]=59;
 
 $wpdb->query($wpdb->prepare("update $t_f_ap_cnofig set 
               m_extract = %d,
			   name = %s,
			   page_charset = %s,
			   cat = %s,
			   author = %d,
			   update_interval = %d,
			   published_interval = %d,
               post_scheduled = %s,
			   auto_tags = %s,
			   proxy = %s,
			   post_format = %s,
			   check_duplicate = %d,
               err_status = %d
			   WHERE id = %d",$_POST['manually_extraction'],$_POST['config_name'],$page_charset,$cat,$_POST['author'],$_POST['update_interval'],$_POST['published_interval'],json_encode($post_scheduled),json_encode($auto_sets),json_encode($proxy),$_POST['post_format'],$_POST['check_duplicate'],$_POST['err_status'],$_POST['id'])
			   );


 $showBox1=true;
}

 $config = $wpdb->get_row('SELECT * FROM '.$t_f_ap_cnofig.' WHERE id ='.$id ); 
?>

<div class="wrap">
  <div class="icon32" id="icon-wp-autopost"><br/></div>
  <h2>Auto Post - Setting : <?php echo $config->name; ?><a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=new" class="add-new-h2"><?php echo __('Add New Task','wp-autopost'); ?></a> </h2>
   <div class="clear"></div>

<br/>
<a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php" class="button"><?php echo __('Return','wp-autopost'); ?></a> 
&nbsp;<input type="button" class="button-primary"  value="<?php echo __('Test Fetch','wp-autopost'); ?>"    onclick="testFetch()"/>
<br/><br/>

<?php
if($saction=='editSubmit'){
  if(@$msg!='') echo  $msg;
  echo '<div  class="updated fade"><p>'.__('Updated!','wp-autopost').'</p></div>';
}

if($saction=='test_url_list'||$saction=='save_url_list'){
 if($_POST['a_match_type']==0)$a_selector = trim($_POST['a_selector_0']);
 elseif($_POST['a_match_type']==1)$a_selector = trim($_POST['a_selector_1']); 
 if(isset($_POST['reverse_sort'])&&$_POST['reverse_sort']=='on')$reverse_sort=1;else $reverse_sort=0;
 $wpdb->query('update '.$t_f_ap_cnofig.' set
               a_match_type = '.$_POST['a_match_type'].',
			   a_selector = "'.$a_selector.'",
			   source_type = '.$_POST['source_type'].',
			   reverse_sort = '.$reverse_sort.',
			   start_num = '.$_POST['start_num'].', 
			   end_num =  '.$_POST['end_num'].' WHERE id = '.$_POST['id']
			   );
 
 
 if($_POST['source_type']==0){
  $wpdb->query('delete from '.$t_f_ap_config_ur1_1ist.' where config_id ='.$_POST['id']);
  $urls = explode("\n",$_POST['urls']);  
  foreach($urls as $url){
    $url=trim($url);
	if($url!='')$wpdb->query('insert into '.$t_f_ap_config_ur1_1ist.'(config_id,url) values ('.$_POST['id'].',"'.$url.'")');
  }
 }

 if($_POST['source_type']==1){
  $wpdb->query('delete from '.$t_f_ap_config_ur1_1ist.' where config_id ='.$_POST['id']);
  $url=trim($_POST['url']);
  if($url!='')$wpdb->query('insert into '.$t_f_ap_config_ur1_1ist.'(config_id,url) values ('.$_POST['id'].',"'.$url.'")');
 }
 if($saction=='test_url_list'){
   test_url_list($_POST['id']);
 }else{ 
   echo '<div id="message" class="updated fade"><p>'.__('Updated!','wp-autopost').'</p></div>';
 }
 $showBox2=true;
}

if($saction=='save_crawl' || $saction=='test_crawl'){

 if($_POST['title_match_type']==0)$title_selector = stripslashes(trim($_POST['title_selector_0']));
 elseif($_POST['title_match_type']==1){
   $title_selector = stripslashes(trim($_POST['title_selector_1_start'])).'WPAPSPLIT'.stripslashes(trim($_POST['title_selector_1_end']));
 }

 $content_match_type = array();
 $content_selector = array();
 
 if(isset($_POST['outer_0'])&&$_POST['outer_0']=='on')$outer = 1;else $outer = 0;
 $objective=0;
 $content_match_type[] = $_POST['content_match_type_0'].','.$outer.','.$objective.','.$_POST['index_0'];
 if($_POST['content_match_type_0']==0){
	$content_selector[] = stripslashes(trim($_POST['content_selector_0_0']));
 }
 elseif($_POST['content_match_type_0']==1){
	$content_selector[] = stripslashes(trim($_POST['content_selector_1_start_0'])).'WPAPSPLIT'.stripslashes(trim($_POST['content_selector_1_end_0']));
 }

 if($_POST['cmrNum']>=1){
   for($i=1;$i<=$_POST['cmrNum'];$i++){
	  if(@$_POST['content_match_type_'.$i]==0){
		 if(@$_POST['content_selector_0_'.$i]==null||trim(@$_POST['content_selector_0_'.$i])=='')continue;
         
		 if($_POST['outer_'.$i]=='on')$outer = 1;else $outer = 0;
		 $objective = $_POST['objective_'.$i];
		 if($objective=='-1'||$objective=='4'||$objective=='5'){
            $objective = 0;
		 }
		 $content_match_type[] = $_POST['content_match_type_'.$i].','.$outer.','.$objective.','.$_POST['index_'.$i];
	     $content_selector[] = stripslashes(trim($_POST['content_selector_0_'.$i]));
	  }elseif($_POST['content_match_type_'.$i]==1){
		 if($_POST['content_selector_1_start_'.$i]==null||trim($_POST['content_selector_1_start_'.$i])=='')continue;
		 if($_POST['content_selector_1_end_'.$i]==null||trim($_POST['content_selector_1_end_'.$i])=='')continue;
         
		 if(isset($_POST['outer_'.$i])&&$_POST['outer_'.$i]=='on')$outer = 1;else $outer = 0;
		 $objective = $_POST['objective_'.$i];
		 if($objective=='-1'||$objective=='4'||$objective=='5'){
            $objective = 0;
		 }
		 $content_match_type[] = $_POST['content_match_type_'.$i].','.$outer.','.$objective.','.$_POST['index_'.$i];
		 $content_selector[] = stripslashes(trim($_POST['content_selector_1_start_'.$i])).'WPAPSPLIT'.stripslashes(trim($_POST['content_selector_1_end_'.$i]));
	  }
	  
   }
 }

 $wpdb->query($wpdb->prepare("update $t_f_ap_cnofig set           
			   title_match_type = %d,
			   title_selector = %s,
			   content_match_type = %s,
			   content_selector = %s 
			   WHERE id = %d",$_POST['title_match_type'],$title_selector,json_encode($content_match_type),json_encode($content_selector),$_POST['id'])
			  );


 if($saction=='test_crawl'){
   test_crawl($_POST['id'],$_POST['testUrl']);
 }else{ 
   echo '<div id="message" class="updated fade"><p>'.__('Updated!','wp-autopost').'</p></div>';
 }
   
 $showBox3=true;
}



if($saction=='testFetch'){	
  testFetch_apf($_POST['id']);
}

if(isset($_REQUEST['reset_scheduled'])&&$_REQUEST['reset_scheduled']==1){
  $wpdb->query("update $t_f_ap_cnofig  set post_scheduled_last_time=0 where id= ".$id); 

  $showBox1=true;
  $msg = '<div class="updated fade"><p>'.__('Updated!','wp-autopost').'</p></div>';
  echo $msg;
}

?>

<script type="text/javascript">
function findObj(theObj, theDoc){  var p, i, foundObj;    if(!theDoc) theDoc = document;  if( (p = theObj.indexOf("?")) > 0 && parent.frames.length)  {    theDoc = parent.frames[theObj.substring(p+1)].document;    theObj = theObj.substring(0,p);  }  if(!(foundObj = theDoc[theObj]) && theDoc.all) foundObj = theDoc.all[theObj];  for (i=0; !foundObj && i < theDoc.forms.length; i++)     foundObj = theDoc.forms[i][theObj];  for(i=0; !foundObj && theDoc.layers && i < theDoc.layers.length; i++)     foundObj = findObj(theObj,theDoc.layers[i].document);  if(!foundObj && document.getElementById) foundObj = document.getElementById(theObj);    return foundObj;}

function AddRowType1(){
 var TRLastIndex = findObj("Type1TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType1",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type1" + rowID; 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="type1_para1[]" value="" style="width:100%">';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<input type="text" name="type1_para2[]" value="" style="width:100%">';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType1(\'type1'+rowID+'\')"/>';

 TRLastIndex.value = (rowID + 1).toString() ;

}

function deleteRowType1(rowid){
 var table = findObj("OptionType1",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}

function AddRowType5(){
 var TRLastIndex = findObj("Type5TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType5",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type5" + rowID; 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="type5_para1[]" value="" style="width:100%" />';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<input type="text" name="type5_para2[]" value="0" size="1" />';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType5(\'type5'+rowID+'\')"/>';
 
 TRLastIndex.value = (rowID + 1).toString() ;
}

function deleteRowType5(rowid){
 var table = findObj("OptionType5",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}

function AddRowType2(){
 var TRLastIndex = findObj("Type2TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType2",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type2" + rowID;  

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="type2_para1[]" value="">';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<select name="type2_para2[]" ><option value="0"><?php echo __('No'); ?></option><option value="1" ><?php echo __('Yes'); ?></option></select>';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType2(\'type2'+rowID+'\')"/>';
 
 TRLastIndex.value = (rowID + 1).toString() ;

}
function deleteRowType2(rowid){
 var table = findObj("OptionType2",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}

function AddRowType3(){
 var TRLastIndex = findObj("Type3TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType3",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type3" + rowID; 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="type3_para1[]" value="" style="width:100%">';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<input type="text" name="type3_para2[]" value="" style="width:100%">';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType3(\'type3'+rowID+'\')"/>';
 
 TRLastIndex.value = (rowID + 1).toString() ;

}
function deleteRowType3(rowid){
 var table = findObj("OptionType3",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}

function AddRowType4(){
 var TRLastIndex = findObj("Type4TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType4",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type4" + rowID; 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="type4_para1[]" value="" style="width:100%">';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<input type="text" name="type4_para2[]" value="" style="width:100%">';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType4(\'type4'+rowID+'\')"/>';
 
 TRLastIndex.value = (rowID + 1).toString() ;
}

function deleteRowType4(rowid){
 var table = findObj("OptionType4",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}


function edit(){
  if(document.getElementById("config_name").value=='')return;
  document.getElementById("saction").value='editSubmit';
  document.getElementById("myform").submit();
}

function save_url_list(){
  document.getElementById("saction").value='save_url_list';
  document.getElementById("myform").submit();
}
function test_url_list(){
  document.getElementById("saction").value='test_url_list';
  document.getElementById("myform").submit();
}
function save_crawl(){
  if(document.getElementById("title_match_type").value==0 && document.getElementById("title_selector_0").value==''){
	 alert("<?php echo __('Please enter The Article Title Matching Rules!','wp-autopost'); ?>");
	 return;
  }
  if(document.getElementById("title_match_type").value==1 && (document.getElementById("title_selector_1_start").value=='' || document.getElementById("title_selector_1_end").value=='') ){
	 alert("<?php echo __('Please enter The Article Title Matching Rules!','wp-autopost'); ?>");
	 return;
  }
  if(document.getElementById("content_match_type_0").value==0 && document.getElementById("content_selector_0_0").value==''){
	  alert("<?php echo __('Please enter The Article Content Matching Rules!','wp-autopost'); ?>");
	 return;
  }
  if(document.getElementById("content_match_type_0").value==1 && (document.getElementById("content_selector_1_start_0").value==''||document.getElementById("content_selector_1_end_0").value=='')){
	  alert("<?php echo __('Please enter The Article Content Matching Rules!','wp-autopost'); ?>");
	 return;
  }
  document.getElementById("saction").value='save_crawl';
  document.getElementById("myform").submit();
}
function testFetch(){
  document.getElementById("saction").value='testFetch';
  document.getElementById("myform").submit();
}

function showTestCrawl(){ 
  jQuery("#test_crawl").show();	
}
function test_crawl(){
  if(document.getElementById("testUrl").value==''){
	 alert("<?php echo __('Please enter the URL of test!','wp-autopost'); ?>");
	 return;
  }
  document.getElementById("saction").value='test_crawl';
  document.getElementById("myform").submit();
}
function changePostType(){
  document.getElementById("saction").value='editSubmit';
  document.getElementById("saction1").value='changePostType';
  document.getElementById("myform").submit(); 
}

jQuery(document).ready(function($){    
	
	$('#published_interval').change(function(){
	    var theValue = $(this).val();
		$("#published_interval_1").val(theValue);
	});

	$('#published_interval_1').change(function(){
	    var theValue = $(this).val();
		$("#published_interval").val(theValue);
	});
	
	$('#post_scheduled').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#post_scheduled_more").hide();
		}else{
           $("#post_scheduled_more").show();
		}
	});
	
	$('.charset').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#ohterSet").hide();
		}else{
           $("#ohterSet").show();
		}
	});

	$('#download_img').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#img_insert_attachment_div").hide();
		}else{
           $("#img_insert_attachment_div").show();
		}
	});

	$('#set_featured_image').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#set_featured_image_div").hide();
		}else{
           $("#set_featured_image_div").show();
		}
	});

	$('#download_attach').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $(".download_attach_option").hide();
		}else{
           $(".download_attach_option").show();
		}
	});

	$('#auto_tags').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#tags_div").hide();
		}else{
           $("#tags_div").show();
		}
	});

	$('#auto_excerpt').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#auto_excerpt_div").hide();
		}else{
           $("#auto_excerpt_div").show();
		}
	});

	$('.source_type').change(function(){
	    var sSwitch = $(this).val();
        $("#source_type").val(sSwitch);
		if(sSwitch == 0){
           $("#urlArea1").show();
	       $("#urlArea2").hide();
		}else{
           $("#urlArea2").show();
	       $("#urlArea1").hide();;
		}
	});

	$('#add_source_url').change(function(){
		if(document.getElementById("add_source_url").checked==true){
          $("#source_url_custom_fields").show();
		}else{
          $("#source_url_custom_fields").hide();
		}
	});

	$('#post_filter').change(function(){
		if(document.getElementById("post_filter").checked==true){
          $("#post_filter_div").show();
		}else{
          $("#post_filter_div").hide();
		}
	});

	$('.a_match_type').change(function(){
	    var sSwitch = $(this).val();
        $("#a_match_type").val(sSwitch);
		if(sSwitch == 0){
           $("#a_selector_0").show();
	       $("#a_selector_1").hide();
		}else{
           $("#a_selector_1").show();
	       $("#a_selector_0").hide();;
		}
	});

	$('.title_match_type').change(function(){
	    var sSwitch = $(this).val();
        $("#title_match_type").val(sSwitch);
		if(sSwitch == 0){
           $("#title_match_0").show();
	       $("#title_match_1").hide();
		}else{
           $("#title_match_1").show();
	       $("#title_match_0").hide();;
		}
	});

	$('.login_set_mode').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 1){
           $("#login_mode1").show();
	       $("#login_mode2").hide();
		}else{
           $("#login_mode2").show();
	       $("#login_mode1").hide();
		}
	});

   $('#use_rewriter').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 0){
           $("#WordAi").hide();
		   $("#MicrosoftTranslator").hide();
		   $("#SpinRewriter").hide();
		}else if(sSwitch == 1){
           $("#MicrosoftTranslator").show();
		   
		   $("#WordAi").hide();
		   $("#SpinRewriter").hide();
		}else if(sSwitch == 2){
           $("#WordAi").show();

		   $("#MicrosoftTranslator").hide();
		   $("#SpinRewriter").hide();
		}else if(sSwitch == 3){
           $("#SpinRewriter").show();

		   $("#MicrosoftTranslator").hide();
		   $("#WordAi").hide();
		}
	});

    $('#rewrite_origi_language').change(function(){
        var sSwitch = $(this).find("option:selected").text();
		$("#rewrite_origi_language_span").html(sSwitch);
    });

	$('#rewrite_trans_language').change(function(){
        var sSwitch = $(this).find("option:selected").text();
		$("#rewrite_trans_language_span").html(sSwitch);
    });


	$('#wordai_spinner').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == 1){
           
		   $("#standard_quality").show();
		   $("#turing_quality").hide();
           
		   $("#standard_nonested").show();
		   $("#turing_nonested").hide();

		}else if(sSwitch == 2){
           $("#standard_quality").hide();
		   $("#turing_quality").show();

		   $("#standard_nonested").hide();
		   $("#turing_nonested").show();
		}
	});

	$('#post_method').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == -1){
           $("#translated_cat1").hide();
		   $("#translated_cat2").hide();
		   $("#translated_cat3").hide();
		}else if(sSwitch == -2){
           $("#translated_cat2").hide();
		   $("#translated_cat3").hide();
		   $("#translated_cat1").show();	   
		}else if(sSwitch == -3){
           $("#translated_cat1").hide();
		   $("#translated_cat3").hide();
		   $("#translated_cat2").show();	   
		}else{
		   $("#translated_cat1").hide();
		   $("#translated_cat2").hide();
           $("#translated_cat3").show();
		}
	}); 

});

</script>

<form id="myform"  method="post" action="admin.php?page=wp-autopost/wp-autopost-tasklist.php"> 
<input type="hidden" name="saction" id="saction" value="">
<input type="hidden" id="saction1" name="saction1" value="">
<input type="hidden" name="id"  value="<?php echo $id; ?>">
<?php $config = $wpdb->get_row('SELECT * FROM '.$t_f_ap_cnofig.' WHERE id ='.$id ); ?>

<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Basic Settings','wp-autopost'); ?></h3>
  <div class="inside" <?php if(@!$showBox1)echo 'style="display:none;"' ?> >
     <table width="100%"> 	         	  
       <tr> 
		 <td width="18%"><?php echo __('Task Name','wp-autopost'); ?>:</td>
		 <td><input type="text" name="config_name" id="config_name" size="80" value="<?php echo $config->name; ?>"></td>
	   </tr>
       
       <tr> 
		 <td style="padding:10px 0 10px 0;"><?php echo __('Post Type','wp-autopost'); ?>:</td>
         <td style="padding:10px 0 10px 0;">
		   <?php $custom_post_types = get_post_types( array('_builtin' => false), 'objects'); ?>
		   <input type="radio" name="post_type" value="post" onchange="changePostType()" <?php if($config->post_type=='post') echo 'checked="true"'; ?> /> <?php echo __('Post'); ?>
		   &nbsp;&nbsp;
		   <input type="radio" name="post_type" value="page" onchange="changePostType()" <?php if($config->post_type=='page') echo 'checked="true"'; ?> /> <?php echo __('Page'); ?>
     <?php foreach ( $custom_post_types  as $post_type ) { ?>
		     &nbsp;&nbsp;
		   <input type="radio" name="post_type" value="<?php echo $post_type->name; ?>" onchange="changePostType()" <?php if($config->post_type==$post_type->name) echo 'checked="true"'; ?> /> <?php echo  $post_type->label; ?>
	 <?php } ?>	          
		 </td>
       </tr>

<?php
class Walker_Terms_Checklist extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); 

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent<ul class='children'>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}

	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category1';
		else
			$name = 'post_category1';

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
	}

	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}
}
?>

<?php if($config->post_type=='post'): ?>
	   <tr> 
		 <td><?php echo __('Taxonomy','wp-autopost');  ?>:</td> 
		 <td>
<?php
          $Walker_Terms_Checklist = new Walker_Terms_Checklist();
	      $selected_cats = explode(',',$config->cat);
	      $taxonomy_names = get_object_taxonomies( 'post','objects');        
		  foreach($taxonomy_names as $taxonomy){
		    if($taxonomy->name=='post_tag' || $taxonomy->name =='post_format')continue;
			$args = array(
	         'descendants_and_self'  => 0,
	         'selected_cats'         => $selected_cats,
	         'popular_cats'          => false,
	         'walker'                => $Walker_Terms_Checklist,
	         'taxonomy'              => $taxonomy->name,
	         'checked_ontop'         => false
            );
		    echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';	 
			echo '<strong>'.$taxonomy->label.'</strong>';
			wp_terms_checklist( 0, $args );
			echo '</ul>';
		  }
?>
		 </td>
	   </tr>
	   
<?php elseif($config->post_type=='page'): ?>	   
      <tr> 
         <td colspan="2"></td>
	  </tr>
<?php else: ?>
      <tr> 
		 <td><?php echo __('Taxonomy','wp-autopost');  ?>:</td> 
		 <td>
<?php
          $Walker_Terms_Checklist = new Walker_Terms_Checklist();
	      $selected_cats = explode(',',$config->cat);
	      $taxonomy_names = get_object_taxonomies( $config->post_type,'objects');        
		  foreach($taxonomy_names as $taxonomy){
			$args = array(
	         'descendants_and_self'  => 0,
	         'selected_cats'         => $selected_cats,
	         'popular_cats'          => false,
	         'walker'                => $Walker_Terms_Checklist,
	         'taxonomy'              => $taxonomy->name,
	         'checked_ontop'         => false
            );
		    echo '<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">';	 
			echo '<strong>'.$taxonomy->label.'</strong>';
			wp_terms_checklist( 0, $args );
			echo '</ul>';
		  }
?>
		 </td>
	   </tr>
<?php endif; ?>

<?php if ( current_theme_supports( 'post-formats' ) ): ?>

<?php $post_formats = get_theme_support( 'post-formats' );
      if ( is_array( $post_formats[0] ) ) :	 $formatName = get_post_format_strings(); ?>
       <tr>
         <td style="padding:0 0 10px 0;"><?php echo __('Post Format','wp-autopost');  ?>:</td>
         <td style="padding:0 0 10px 0;">
		   <input type="radio" name="post_format" value="" <?php if($config->post_format==''||$config->post_format==null) echo 'checked="true"'; ?> /> <?php echo $formatName['standard']; ?>
    <?php foreach ( $post_formats[0]  as $post_format ) { ?>
		   &nbsp;&nbsp;
		   <input type="radio" name="post_format" value="<?php echo $post_format; ?>" <?php if($config->post_format==$post_format) echo 'checked="true"'; ?> /> <?php echo  $formatName[$post_format]; ?>		   
	 <?php } ?>	   

		 </td>
	   </tr>
      
<?php endif; ?>
   
<?php endif; ?>

	   
	   <tr>
        <td><?php echo __('Author','wp-autopost'); ?>:</td>
        <td>
		<?php
		    $querystr = "SELECT $wpdb->users.ID,$wpdb->users.display_name FROM $wpdb->users";
            $users = $wpdb->get_results($querystr, OBJECT);		   
		?>
         <select name="author" >
		    <option value="0"><?php echo __('Random Author','wp-autopost'); ?></option>
          <?php foreach ($users as $user) { ?> 
		    <option value="<?php echo $user->ID; ?>" <?php if(($user->ID)==($config->author)) echo 'selected' ?> ><?php echo $user->display_name; ?></option>
		  <?php } ?>
		 </select>
		</td> 
	   </tr>
	   <tr>
        <td><?php echo __('Update Interval','wp-autopost'); ?>:</td>
        <td>
		   <input type="text" name="update_interval" id="update_interval" size="2" value="<?php echo $config->update_interval; ?>"> <?php echo __('Minute','wp-autopost'); ?> <span class="gray">( <?php echo __('How long Intervals detect whether there is a new article can be updated','wp-autopost'); ?> )</span>
		</td> 
	   </tr>
	   <tr>
        <td><?php echo __('Published Date Interval','wp-autopost'); ?>:</td>
        <td>
		   <input type="text" name="published_interval" id="published_interval" size="2" value="<?php echo $config->published_interval; ?>"> <?php echo __('Minute','wp-autopost'); ?> <span class="gray">( <?php echo __('The published date interval between each post','wp-autopost'); ?> )</span>
		</td> 
	   </tr>

	   <tr>
	   <?php
          $post_scheduled = json_decode($config->post_scheduled);
		  if(!is_array($post_scheduled)){
             $post_scheduled = array();
             $post_scheduled[0] = 0;
             $post_scheduled[1] = 12;
			 $post_scheduled[2] = 0; 
		  }
		?>
        <td><?php echo __('Post Scheduled','wp-autopost'); ?>:</td>
        <td>
		  <select id="post_scheduled" name="post_scheduled">
           <option value="0" <?php if($post_scheduled[0]==0) echo 'selected="true"'; ?>><?php echo __('No'); ?></option>
		   <option value="1" <?php if($post_scheduled[0]==1) echo 'selected="true"'; ?>><?php echo __('Yes'); ?></option>
		  </select>
		</td> 
	   </tr>

	   <tr>
         <td></td>
		 <td>
           <div id="post_scheduled_more" <?php if($post_scheduled[0]==0)echo 'style="display:none;"' ?> >
	        <table>
              <tr>
                <td><?php echo __('Start Time','wp-autopost'); ?>:</td>
				<td>
				 <input type="text" name="post_scheduled_hour" id="hh" size="2" maxlength="2"  value="<?php echo ($post_scheduled[1]<10)?'0'.$post_scheduled[1]:$post_scheduled[1];?>" />
			     :
                 <input type="text" name="post_scheduled_minute" id="mn" size="2" maxlength="2" value="<?php echo ($post_scheduled[2]<10)?'0'.$post_scheduled[2]:$post_scheduled[2];?>" />
				</td>
			  </tr>
			  <tr>
               <td><?php echo __('Published Date Interval','wp-autopost'); ?>:</td>
			   <td><input type="text" name="published_interval_1" id="published_interval_1" size="3" value="<?php echo $config->published_interval; ?>"> <?php echo __('Minute','wp-autopost'); ?> <span class="gray">( <?php echo __('The published date interval between each post','wp-autopost'); ?> )</span></td> 
			  </tr>
			  
			  <tr>
               <td colspan="2">
			     <?php 
				 if($post_scheduled[0]==1): 
                   $currentTime = current_time('timestamp');
			       
			       
				   if( ($config->post_scheduled_last_time) > 0){
                     $post_scheduled_last_time = $config->post_scheduled_last_time;
					 if($post_scheduled_last_time < $currentTime){
                       $postTime = mktime($post_scheduled[1],$post_scheduled[2],0,date('m',$currentTime),date('d',$currentTime),date('Y',$currentTime)); 
					 }else{
                       $postTime =  $post_scheduled_last_time + $config->published_interval*60 + rand(0,60);  
					 }
					 
			       }else{
					 $postTime = mktime($post_scheduled[1],$post_scheduled[2],0,date('m',$currentTime),date('d',$currentTime),date('Y',$currentTime));
         
				   }
                   
				   if($postTime<$currentTime){
                     $postTime += 86400; // add one day
                   }	
                   
				   echo __('Expected newest publish date','wp-autopost').': <code>'.date('Y-m-d H:i:s',$postTime).'</code>';


				   if( ($config->post_scheduled_last_time) > 0){ ?> 
                   
				   <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=edit&id=<?php echo $id;?>&p=<?php echo $_REQUEST['p']; ?>&reset_scheduled=1" ><?php echo __('Reset','wp-autopost'); ?></a>
                 
				 <?php
				   }

                 endif; ?>
			   </td>
			  </tr>

			</table>
			
		   </div>
		 </td>
	   </tr>
	   
       <tr>
        <td style="height:28px;"><?php echo __('Charset','wp-autopost'); ?>:</td>
        <td>
		   <input class="charset" type="radio" name="charset" value="0"  <?php if($config->page_charset=='0') echo 'checked="true"'; ?>> <?php echo __('Automatic Detection','wp-autopost'); ?> 
		   <input class="charset" type="radio" name="charset" value="1"  <?php if($config->page_charset!='0') echo 'checked="true"'; ?>> <?php echo __('Other','wp-autopost'); ?>
		   <span id="ohterSet" <?php if($config->page_charset=='0') echo 'style="display:none;"' ?>><input type="text" name="page_charset" id="page_charset"  value="<?php if($config->page_charset!='0') echo $config->page_charset; ?>"></span>		   
		</td> 
	   </tr>

	   <tr>
        <td><?php echo __('Download Remote Images','wp-autopost'); ?>:</td>
        <td>
         <select id="download_img" name="download_img">
           <option value="0" ><?php echo __('No'); ?></option>
		   <option value="1" ><?php echo __('Yes'); ?></option>
		 </select> 
		</td> 
	   </tr>
	   <tr>
         <td></td>
         <td>
         <span id="img_insert_attachment_div" style="display:none;">
		   <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>
		   <p>
		     <div>
			   <input type="radio" name="img_insert_attachment" checked="true" disabled="true" /> <?php echo __('Do not save','wp-autopost'); ?>
			   <br/>
			   <input type="radio" name="img_insert_attachment" disabled="true" /> <?php echo __('Save the images to wordpress media library','wp-autopost'); ?>
			   <br/>
			   <input type="radio" name="img_insert_attachment" disabled="true" /> <?php echo __('Save the images to Flickr','wp-autopost'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<a href="admin.php?page=wp-autopost/wp-autopost-flickr.php" ><?php echo __('Flickr Options','wp-autopost'); ?></a>)
			   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			   <span class="gray"><?php echo __('Automatically upload images to <a href="http://www.flickr.com" target="_blank">Flickr</a> ( 1TB Free Storage ), save bandwidth and space, speed up your website.','wp-autopost'); ?></span>
               <br/>
			   <input type="radio" name="img_insert_attachment" disabled="true" /> <?php echo __('Save the images to Qiniu','wp-autopost'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<a href="admin.php?page=wp-autopost/wp-autopost-qiniu.php" ><?php echo __('Qiniu Options','wp-autopost'); ?></a>)
			   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			   <span class="gray"><?php echo __('Automatically upload images to <a href="http://www.qiniu.com" target="_blank" >Qiniu</a> ( 10GB Free Storage ), save bandwidth and space, speed up your website.','wp-autopost'); ?></span>
			   <br/>
			   <input type="radio" name="img_insert_attachment" disabled="true" /> <?php echo __('Save the images to Upyun','wp-autopost'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<a href="admin.php?page=wp-autopost/wp-autopost-upyun.php" ><?php echo __('Upyun Options','wp-autopost'); ?></a>)
			   <br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			   <span class="gray"><?php echo __('Automatically upload images to Upyun, save bandwidth and space, speed up your website.','wp-autopost'); ?></span>
			    
			   <br/><br/>
			 </div>
			 
			 <input type="checkbox" name="set_watermark_image" disabled="true"/> <?php echo __('Add a watermark to downloaded images automatically','wp-autopost'); ?> (<a href="admin.php?page=wp-autopost/wp-autopost-watermark.php" ><?php echo __('Watermark Options','wp-autopost'); ?></a>)
			 <?php $wms = $wpdb->get_results('SELECT id,name FROM '.$t_ap_watermark.' order by id'); ?>
			 <br/>
			 <?php echo __('Watermark Name','wp-autopost'); ?> : 
			 <select name="watermark_id" id="watermark_id">
			    <option value="0" ><?php echo __('Please Select','wp-autopost'); ?></option>
			  <?php foreach($wms as $wm){ ?>
			    <option value="<?php echo $wm->id; ?>" ><?php echo $wm->name; ?></option>
			  <?php } ?>
			 </select>

		   </p>

		 </span>
		 </td>
	   </tr>

       <tr>
        <td><?php echo __('Set Featured Image','wp-autopost'); ?>:</td>
		<td>
         <select id="set_featured_image" name="set_featured_image">
           <option value="0" ><?php echo __('No'); ?></option>
		   <option value="1" ><?php echo __('Yes'); ?></option>
		 </select>
		</td>
	   </tr>
       <tr>
         <td></td>
         <td>
          <span id="set_featured_image_div" style="display:none;">
		     <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>
             <p><?php echo __('Set images as the featured image automatically','wp-autopost'); ?>
            &nbsp;&nbsp;&nbsp;<?php echo __('Index','wp-autopost'); ?>:<input type="text" size="1" name="set_featured_image_index" value="1" /><a title='<?php echo __('1:the first image; 2:the second image','wp-autopost'); ?>'>[?]</a></p>
		  </span>
		 </td>
       </tr>
       
	   <tr>
         <td><?php echo __('Download Remote Attachments','wp-autopost'); ?>:</td>
         <td>
          <select id="download_attach" name="download_attach" >
            <option value="0" ><?php echo __('No'); ?></option>
		    <option value="1" ><?php echo __('Yes'); ?></option>
		  </select>
		  <span class="download_attach_option"  style="display:none;" >
		  (<a href="admin.php?page=wp-autopost/wp-autopost-options.php#RemoteAttachmentDownloadOption" target="_blank"><?php echo __('Remote Attachment Download Option','wp-autopost'); ?></a>)
		 </span>
		 </td>
	   </tr>
	   <tr>
         <td></td>
         <td>
           <span class="download_attach_option" style="display:none;" >
		    <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>
			<p>
			  <input type="checkbox" name="attach_insert_attachment" id="attach_insert_attachment"  disabled="true"/> <?php echo __('Save the attachments to wordpress media library','wp-autopost'); ?>
			</p>      
		   </span>
		 </td>
	   </tr>
       
	   <?php
          $auto_set = json_decode($config->auto_tags);
		  if(!is_array($auto_set)){
             $auto_set = array();
             $auto_set[0] = $config->auto_tags;
             $auto_set[1] = 0;
			 $auto_set[2] = 0;
		  }
		?>

	   <tr>
        <td><?php echo __('Auto Tags','wp-autopost'); ?>:</td>
        <td>
         <select id="auto_tags" name="auto_tags" >
           <option value="0" ><?php echo __('No'); ?></option>
		   <option value="1" ><?php echo __('Yes'); ?></option>
		 </select> 
		</td> 
	   </tr>
	   <tr>
         <td></td>
		 <td>
		  <span id="tags_div"  style="display:none;" >
		   <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>		   
		   <p>
		   <input type="checkbox" name="use_wp_tags"  disabled="true"/> <?php echo __('Use Wordpress Tags Library','wp-autopost'); ?>
           <br/><br/>
		   <?php echo __('Tags List','wp-autopost'); ?>: <span class="gray">(<?php echo __('Separated with a comma','wp-autopost'); ?>)</span><br/><input type="text" name="tags" id="tags"  value="" style="width:100%" disabled="true" />
		   <br/><br/>
		   <input type="checkbox" name="whole_word" id="whole_word" disabled="true" /> <?php echo __('Match Whole Word','wp-autopost'); ?> <span class="gray">(<?php echo __('Autotag only a post when terms finded in the content are a the same name','wp-autopost'); ?>)</span></p>
		 </span></td>
       </tr>

	   <tr>
        <td><?php echo __('Auto Excerpt','wp-autopost'); ?>:</td>
        <td>
         <select id="auto_excerpt" name="auto_excerpt" >
           <option value="0" ><?php echo __('No'); ?></option>
		   <option value="1" ><?php echo __('Yes'); ?></option>
		 </select>
		</td>
	   </tr>
	   <tr>
         <td></td>
		 <td>
           <span id="auto_excerpt_div" style="display:none;">
            <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>
			<p><?php echo __('Set the paragraph as an excerpt automatically','wp-autopost'); ?>
			&nbsp;&nbsp;&nbsp;<?php echo __('Index','wp-autopost'); ?>:<input type="text" size="1" name="auto_excerpt_index" value="1" /><a title='<?php echo __('1:beginning of paragraph 1; 2:beginning of paragraph 2','wp-autopost'); ?>'>[?]</a></p>
		   </span>
		 </td>
	   </tr>

       <tr>
        <td><?php echo __('Publish Status','wp-autopost'); ?>:</td>
        <td>
         <select id="publish_status" name="publish_status">
           <option value="0" <?php if($auto_set[2]==0) echo 'selected="true"'; ?>><?php echo __('Published'); ?></option>
		   <option value="1" <?php if($auto_set[2]==1) echo 'selected="true"'; ?>><?php echo __('Draft'); ?></option>
		   <option value="2" <?php if($auto_set[2]==2) echo 'selected="true"'; ?>><?php echo __('Pending Review'); ?></option>
		 </select>
		</td>
	   </tr>

	   <tr>
        <td><?php echo __('Manually Selective Extraction','wp-autopost'); ?>:</td>
        <td>
         <select id="manually_extraction" name="manually_extraction">
           <option value="0" <?php if(($config->m_extract)==0) echo 'selected="true"'; ?>><?php echo __('No'); ?></option>
		   <option value="1" <?php if(($config->m_extract)==1) echo 'selected="true"'; ?>><?php echo __('Yes'); ?></option>
		 </select>
		 <a title='<?php echo __('Manually select which article can be posted in your site','wp-autopost'); ?>'>[?]</a>
		</td>
	   </tr>

	   <tr> 
        <td style="padding:10px 0 10px 0;"><?php echo __('Check Extracted Method','wp-autopost'); ?>:</td>
        <td style="padding:10px 0 10px 0;">
          <input type="radio" name="check_duplicate"  value="0" <?php if(($config->check_duplicate)==0)echo 'checked="true"'; ?> /> <?php echo __('URL','wp-autopost'); ?>
          &nbsp;&nbsp;&nbsp;
          <input type="radio" name="check_duplicate"  value="1" <?php if(($config->check_duplicate)==1)echo 'checked="true"'; ?> /> <?php echo __('URL + Title','wp-autopost'); ?>
		</td>
	   </tr>

	   <tr> 
        <td colspan="2"><hr/></td>
	   </tr>
       
	   <?php
         $proxy = json_decode($config->proxy);
	   ?>
	   <tr> 
        <td><?php echo __('Use Proxy','wp-autopost'); ?>:</td>
        <td>
         <select id="use_proxy" name="use_proxy"> 
           <option value="0" <?php if($proxy[0]==0) echo 'selected="true"'; ?>><?php echo __('No'); ?></option>
		   <option value="1" <?php if($proxy[0]==1) echo 'selected="true"'; ?>><?php echo __('Yes'); ?></option>
		 </select>
		</td>
	   </tr>

	   <tr> 
        <td><?php echo __('Hide IP','wp-autopost'); ?>:</td>
        <td>
         <select id="hide_ip" name="hide_ip">
           <option value="0" <?php if($proxy[1]==0) echo 'selected="true"'; ?>><?php echo __('No'); ?></option>
		   <option value="1" <?php if($proxy[1]==1) echo 'selected="true"'; ?>><?php echo __('Yes'); ?></option>
		 </select>
		</td>
	   </tr>

	   <tr> 
        <td colspan="2"><hr/></td>
	   </tr>
       

	   <tr> 
        <td colspan="2"><?php echo __('When extract error set the status to','wp-autopost'); ?>:
         &nbsp;&nbsp;
         <select id="err_status" name="err_status">
           <option value="1" <?php if($config->err_status==1) echo 'selected="true"'; ?>><?php echo __('Not set','wp-autopost'); ?></option>
		   <option value="0" <?php if($config->err_status==0) echo 'selected="true"'; ?>><?php echo __('Pending Extraction','wp-autopost'); ?></option>
		   <option value="-1" <?php if($config->err_status==-1) echo 'selected="true"'; ?>><?php echo __('Ignored','wp-autopost'); ?></option>
		 </select>
		</td>
	   </tr>

	   <tr>
         <td colspan="2">
		   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"    onclick="edit()"/>
		 </td>
	   </tr>
    </table>
  </div>
</div>
<div class="clear"></div>





<?php $urls = $wpdb->get_results('SELECT * FROM '.$t_f_ap_config_ur1_1ist.' WHERE config_id ='.$id.' ORDER BY id' ); ?>
<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Article Source Settings','wp-autopost'); ?></h3>
  <div class="inside" <?php if(@!$showBox2)echo 'style="display:none;"' ?>>
     <table width="100%"> 
	   <tr>
        <td>
		 <input type="hidden" id="source_type" value="<?php echo $config->source_type; ?>" />
		 <input class="source_type" type="radio" name="source_type" value="0" <?php if(($config->source_type)== 0) echo 'checked="true"'; ?> /> <?php echo __('Manually specify','wp-autopost'); ?> <b><?php echo __('The URL of Article List','wp-autopost'); ?></b> &nbsp;
		 <input class="source_type" type="radio" name="source_type" value="1" <?php if(($config->source_type)== 1) echo 'checked="true"'; ?> /> <?php echo __('Batch generate','wp-autopost'); ?> <b><?php echo __('The URL of Article List','wp-autopost'); ?></b>		 
		</td>
	   </tr>
	   <tr> 
		 <td>	 
		   <div id="urlArea1" <?php if(($config->source_type)!=0) echo 'style="display:none;"'; ?> >
		     <textarea name="urls" id="urls" rows="8" style="width:100%"><?php if(($config->source_type)==0){foreach($urls as $url)echo $url->url."\n"; } ?></textarea>
			 <br/>
			 <span class="gray"><?php echo __('You can add multiple URLs, each URL begin at a new line','wp-autopost'); ?></span>
		   </div>
		
		   <div id="urlArea2" <?php if(($config->source_type)!=1) echo 'style="display:none;"'; ?>>
		     <input type="text" name="url" id="url" style="width:100%" value="<?php if(($config->source_type)==1){foreach($urls as $url)echo $url->url."\n"; } ?>" />
			 <br/>
			 <span class="gray"><?php echo __('For example','wp-autopost'); ?>：http://wp-autopost.org/html/test/list_(*).html</span><br/>
			 (*) <?php echo __('From','wp-autopost'); ?> <input type="text" name="start_num" id="start_num" value="<?php echo $config->start_num; ?>" size="1"> <?php echo __('To','wp-autopost'); ?> <input type="text" name="end_num" id="end_num" value="<?php echo $config->end_num; ?>" size="1">
		   </div>	 
		 </td>
	   </tr>
	   <tr><td></td></tr>
	   <tr>
         <td> <input type="checkbox" name="reverse_sort" id="reverse_sort" <?php if(($config->reverse_sort)==1)echo 'checked="true"'; ?> /> <?php echo __('Reverse the sort of articles','wp-autopost'); ?> <span class="gray">(<?php echo __('Click Test to see the difference','wp-autopost'); ?>)</span> </td>
	   </tr>
     </table>
	 <br/>
	 <h4><?php echo __('Article URL matching rules','wp-autopost'); ?></h4>
      <input type="hidden" id="a_match_type" value="<?php echo $config->a_match_type; ?>" />
	  <input class="a_match_type" type="radio" name="a_match_type" value="0" <?php if(($config->a_match_type)== 0) echo 'checked="true"'; ?> /><?php echo __('Use URL wildcards match pattern','wp-autopost'); ?> 
	  &nbsp;
	  <input class="a_match_type" type="radio" name="a_match_type" value="1" <?php if(($config->a_match_type)== 1) echo 'checked="true"'; ?> /><?php echo __('Use CSS Selector','wp-autopost'); ?> 
	  
	  <div id="a_selector_0" <?php if(($config->a_match_type)!=0) echo 'style="display:none;"'; ?> >
	  <?php echo __('Article URL','wp-autopost'); ?>:
	  <input type="text" name="a_selector_0" id="a_selector_0" size="80" value="<?php if(($config->a_match_type)==0){echo $config->a_selector; }?>"><br/>
	  <span class="gray"><?php echo __('The articles URL, (*) is wildcards','wp-autopost'); ?>, <?php echo __('For example','wp-autopost'); ?>: http://www.domain.com/article/(*)/</span>
	  </div>
      
      <div id="a_selector_1" <?php if(($config->a_match_type)!=1) echo 'style="display:none;"'; ?> >
	  <?php echo __('The Article URLs CSS Selector','wp-autopost'); ?>:
	  <input type="text" name="a_selector_1" id="a_selector_1" size="80" value="<?php if(($config->a_match_type)==1){echo $config->a_selector; }?>"><br/>
	  <span class="gray"><?php echo __('Must select to the HTML &lta> tag','wp-autopost'); ?>, <?php echo __('For example','wp-autopost'); ?>: #list a</span>
	  </div>
      <br/>

      <div>
	    <input type="checkbox" name="add_source_url" id="add_source_url" /> <?php echo __('Add the source URL to custom fields','wp-autopost'); ?>
		<a title='<?php echo __('Add the custom fields to each post, can use the get_post_meta() function get the value.','wp-autopost'); ?>'>[?]</a>
        <div id="source_url_custom_fields"  style="display:none;">
		  <?php echo __('Custom Fields'); ?>: <input type="text" name="source_url_custom_fields"  size="30" value=""  disabled="true"/>
		  <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
		</div>
      </div>

	  <br/> 
	  <h4><input type="checkbox" name="post_filter" id="post_filter" /> <?php echo __('Article Filtering','wp-autopost'); ?> - <?php echo __('Extraction base on keyword','wp-autopost'); ?></h4>
	  <div id="post_filter_div" style="display:none;">
	   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	   <br/>
	   <br/>

	   <input type="radio" name="af0" value="0" checked="true" disabled="true" /> <?php echo __('<strong>Only Extract</strong> when Title or Content contains following keywords','wp-autopost'); ?> 
	  &nbsp;
	  <input type="radio" name="af0" value="1" disabled="true" /> <?php echo __('<strong>Do Not Extract</strong> when Title or Content contains following keywords','wp-autopost'); ?> 
      
	  <br/>
	  <br/>

	  <input type="radio" name="af3" value="1" checked="true" disabled="true" /> <?php echo __('Only Check <strong>Title</strong>','wp-autopost'); ?> 
	  &nbsp;
	  <input type="radio" name="af3" value="2" disabled="true" /> <?php echo __('Only Check <strong>Content</strong>','wp-autopost'); ?> 
	  &nbsp;
	  <input type="radio" name="af3" value="3" disabled="true" /> <?php echo __('Check <strong>Title Or Content</strong>','wp-autopost'); ?> 
      
      <br/>
	  <br/>
      
      <?php echo __('Keyword List','wp-autopost'); ?>: <span class="gray">(<?php echo __('Separated with a comma','wp-autopost'); ?>)</span><br/>
	  <textarea style="width:100%" name="af2" disabled="true" ></textarea>
	  <br/>
      <?php echo __('Keyword Occurrence Times','wp-autopost'); ?>: <input type="text" name="af4" value="2" size="1" disabled="true"  /> <span class="gray"><?php echo __('[Keyword Occurrence Times] Only effective on check the content','wp-autopost'); ?></span>

	  <br/><br/>
	  <?php echo __('Filtered article status','wp-autopost'); ?>:&nbsp;
	  <input type="radio" name="af1" value="-1" checked="true" disabled="true" /> <?php echo __('Ignored','wp-autopost'); ?> 
	  &nbsp;
	  <input type="radio" name="af1" value="0"  disabled="true" /> <?php echo __('Pending Extraction','wp-autopost'); ?>
	
     </div>
	 
	 <br/>
	 <br/>
	 <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  onclick="save_url_list()"/>
	 <input type="button" class="button"  value="<?php echo __('Test','wp-autopost'); ?>"  onclick="test_url_list()"/>

  </div>
</div>
<div class="clear"></div>

<div class="postbox">
 <h3 class="hndle" style="cursor:pointer;"><?php echo __('Article Extraction Settings','wp-autopost'); ?></h3>
 <div class="inside" <?php if(@!$showBox3)echo 'style="display:none;"' ?> >
  <table>
   <tr> 
    <td><strong><?php echo __('The Article Title Matching Rules','wp-autopost'); ?></strong></td>
   </tr>
   <tr> 
    <td>
	  <input type="hidden" id="title_match_type" value="<?php echo $config->title_match_type; ?>" />
	  <input class="title_match_type" type="radio" name="title_match_type" value="0" <?php if(($config->title_match_type)== 0) echo 'checked="true"'; ?> /><?php echo __('Use CSS Selector','wp-autopost'); ?>
	  &nbsp;
	  <input class="title_match_type" type="radio" name="title_match_type" value="1" <?php if(($config->title_match_type)== 1) echo 'checked="true"'; ?> /><?php echo __('Use Wildcards Match Pattern','wp-autopost'); ?>
	  <span class="gray"><code><?php echo __('(*) is Wildcards','wp-autopost'); ?></code></span>
	</td>
   </tr>
   <tr> 
    <td>
	  <div id="title_match_0"  <?php if(($config->title_match_type)!=0) echo 'style="display:none;"'; ?>>
        <?php echo __('CSS Selector','wp-autopost'); ?>: 
	    <input type="text" name="title_selector_0" id="title_selector_0" size="40" value="<?php if(($config->title_match_type)== 0) echo htmlspecialchars($config->title_selector); ?>">
	    <span class="gray"><?php echo __('For example','wp-autopost'); ?>: #title h1</span>
	  </div>
	  <div id="title_match_1"  <?php if(($config->title_match_type)!=1) echo 'style="display:none;"'; ?>>
	     <?php
		  //兼容前面版本
	      if(strpos($config->title_selector,'WPAPSPLIT')===false){
             $match = explode('(*)',trim($config->title_selector));
		  }else{
             $match = explode('WPAPSPLIT',trim($config->title_selector));
		  }

	    ?>

		<table>
          <tr> 
            <td><?php echo __('Starting Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="title_selector_1_start" id="title_selector_1_start" size="40" value="<?php if(($config->title_match_type)== 1) echo htmlspecialchars($match[0]); ?>">
			  <span class="gray"> 
                <?php echo __('For example','wp-autopost'); ?>: &lt;h1 id="(*)" >
			  </span>
			</td>
		  </tr>
		  <tr>
            <td><?php echo __('Ending Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="title_selector_1_end" id="title_selector_1_end" size="40" value="<?php if(($config->title_match_type)== 1) echo htmlspecialchars($match[1]); ?>">
			  <span class="gray"> 
                <?php echo __('For example','wp-autopost'); ?>: &lt;/h1>
			  </span>
			</td>
		  </tr>
		</table>
		
	  
	  </div>
	</td>
   </tr>
  </table>
  <br/>

  <?php
      $content_selector = json_decode($config->content_selector);
      if($content_selector==null){ //Compatible with previous versions
		  $content_selector = array();
		  $content_selector[0] = $config->content_selector;
	  }
      $content_match_types = json_decode($config->content_match_type);
	  if($content_match_types==null){
		  $content_match_type = array();
		  $content_match_type[0] = $config->content_match_type;
		  $outer = array();
		  $outer[0] = 0;
		  $objective = array();
		  $objective[0]=0;
		  $index = array();
          $index[0] = 0;
	  }else{
	    $content_match_type = array();
		$outer = array();
		$objective = array();
		$index = array();
		foreach($content_match_types as $cmts){
          $cmt = explode(',',$cmts);
          $content_match_type[] = $cmt[0];
          $outer[]=$cmt[1];
		  if(@$cmt[2]==NULL||@$cmt[2]=='')$objective[]='0';  //Compatible with previous versions
          else $objective[]=$cmt[2];
		  if(@$cmt[3]==NULL||@$cmt[3]=='')$index[]=0;  //Compatible with previous versions
          else $index[]=$cmt[3];
	    }
	  }
  ?>
<script type="text/javascript">

function addMoreMR(){
 var cmrNum = parseInt(document.getElementById("cmrNum").value);
 
 cmrNum = cmrNum+1;
 document.getElementById("cmrNum").value = cmrNum;

 var TRLastIndex = findObj("cmrTRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);
 
 var table = findObj("cmr",document);
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "cmr" + rowID;

 var newTD1=newTR.insertCell(0);

  newTD1.innerHTML = '<div class="apmatchtype"><p><input type="hidden" id="content_match_type_'+cmrNum+'" value="0" />'+
	   '<input class="content_match_type_'+cmrNum+'" type="radio" name="content_match_type_'+cmrNum+'" value="0"  checked="true" /><?php echo __("Use CSS Selector","wp-autopost"); ?>&nbsp;&nbsp;&nbsp;'+
	   '<input class="content_match_type_'+cmrNum+'" type="radio" name="content_match_type_'+cmrNum+'" value="1" /><?php echo __("Use Wildcards Match Pattern","wp-autopost"); ?>&nbsp;&nbsp;&nbsp;'+
	   '<input type="checkbox" name="outer_'+cmrNum+'" /> <?php echo __("Contain The Outer HTML Text","wp-autopost"); ?><a style="float:right;" class="apdelete" title="delete" href="javascript:;" onclick="deleteRowCmr(\'cmr'+rowID+'\')" ><?php echo __('Delete'); ?></a></p></div>'+ 
	   '<span id="content_match_0_'+cmrNum+'" >'+
       '<?php echo __("CSS Selector","wp-autopost"); ?>: <input type="text" name="content_selector_0_'+cmrNum+'" id="content_selector_0_'+cmrNum+'" size="40" value="">'+  
	   ' <span class="clickBold" id="index_'+cmrNum+'"><?php echo __("Index","wp-autopost"); ?></span><span id="index_num_'+cmrNum+'" style="display:none;">: <input type="text" name="index_'+cmrNum+'" size="1" value="0" /><input type="hidden" id="index_show_'+cmrNum+'" value="0" /></span>'+	   
	   ' </span>'+
	   '<span id="content_match_1_'+cmrNum+'"  style="display:none;" ><table><tr><td><?php echo __('Starting Unique HTML','wp-autopost'); ?>:</td><td><input type="text" name="content_selector_1_start_'+cmrNum+'" id="content_selector_1_start_'+cmrNum+'" size="40" value="" /></td><tr><td><?php echo __('Ending Unique HTML','wp-autopost'); ?>:</td><td><input type="text" name="content_selector_1_end_'+cmrNum+'" id="content_selector_1_end_'+cmrNum+'" size="40" value="" /></td></tr></table></span>'+
	   '<p><label><?php echo __("To: ","wp-autopost"); ?></label> <select name="objective_'+cmrNum+'" id="objective_'+cmrNum+'" ><option value="0" ><?php echo __('Post Content','wp-autopost'); ?></option><option value="2" ><?php echo __('Post Excerpt','wp-autopost'); ?></option><option value="3" ><?php echo __('Post Tags','wp-autopost'); ?></option><option value="5" ><?php echo __('Categories'); ?></option><option value="4" ><?php echo __('Featured Image'); ?></option><option value="1" ><?php echo __('Post Date','wp-autopost'); ?></option><option value="-1" ><?php echo __('Custom Fields'); ?></option></select>'+
	   '<span><input id="objective_customfields_'+cmrNum+'" name="objective_customfields_'+cmrNum+'" style="display:none;" type="text" value="<?php echo __('This feature is not available in the free version','wp-autopost'); ?>" disabled="true" /></span></p>';

 TRLastIndex.value = (rowID + 1).toString() ;
 jQuery(function($){
	$('.content_match_type_'+cmrNum).change(function(){
	    var sSwitch = $(this).val();
        $("#content_match_type_"+cmrNum).val(sSwitch);
		if(sSwitch == 0){
           $("#content_match_0_"+cmrNum).show();
	       $("#content_match_1_"+cmrNum).hide();
		}else{
           $("#content_match_1_"+cmrNum).show();
	       $("#content_match_0_"+cmrNum).hide();;
		}
	});
	
	$('#objective_'+cmrNum).change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == -1 || sSwitch == 4 || sSwitch == 5){
           $("#objective_customfields_"+cmrNum).show();
		}else{
           $("#objective_customfields_"+cmrNum).hide();
		}
    });

	$('#index_'+cmrNum).click(function(){
	   var s = $('#index_show_'+cmrNum).val(); 
	   if(s==0){
	     $("#index_num_"+cmrNum).show();
		 $('#index_show_'+cmrNum).val('1');
	   }else{
         $("#index_num_"+cmrNum).hide();
		 $('#index_show_'+cmrNum).val('0');
	   }
    });
 
 });
}

function deleteRowCmr(rowid){
  var table = findObj("cmr",document);
  var signItem = findObj(rowid,document);
  var rowIndex = signItem.rowIndex;
  table.deleteRow(rowIndex);
}

jQuery(document).ready(function($){ 
  <?php 
       $cmrNum = count($content_selector);
       for($i=0;$i<$cmrNum;$i++){ ?>  
    $('.content_match_type_<?php echo $i; ?>').change(function(){
	    var sSwitch = $(this).val();
        $("#content_match_type_<?php echo $i; ?>").val(sSwitch);
		if(sSwitch == 0){
           $("#content_match_0_<?php echo $i; ?>").show();
	       $("#content_match_1_<?php echo $i; ?>").hide();
		}else{
           $("#content_match_1_<?php echo $i; ?>").show();
	       $("#content_match_0_<?php echo $i; ?>").hide();;
		}
	});

	$('#index_<?php echo $i; ?>').click(function(){
	   var s = $('#index_show_<?php echo $i; ?>').val(); 
	   if(s==0){
	     $("#index_num_<?php echo $i; ?>").show();
		 $('#index_show_<?php echo $i; ?>').val('1');
	   }else{
         $("#index_num_<?php echo $i; ?>").hide();
		 $('#index_show_<?php echo $i; ?>').val('0');
	   }
    });
  <?php } ?>
	  
  <?php 
       for($i=1;$i<$cmrNum;$i++){ ?>  
    $('#objective_<?php echo $i; ?>').change(function(){
	    var sSwitch = $(this).val();
		if(sSwitch == -1 || sSwitch == 4 || sSwitch == 5){
           $("#objective_customfields_<?php echo $i; ?>").show();
		}else{
           $("#objective_customfields_<?php echo $i; ?>").hide();
		}
	});  
  <?php } ?>
});
</script>
  
  <strong><?php echo __('The Article Content Matching Rules','wp-autopost'); ?></strong>
  <table id="cmr" class="autoposttable" >
   <tr> 
    <td>
	  <div>
	   <p>
	   <input type="hidden" id="content_match_type_0" value="<?php echo $content_match_type[0]; ?>" />
	   <input class="content_match_type_0" type="radio" name="content_match_type_0" value="0" <?php if($content_match_type[0]== 0) echo 'checked="true"'; ?> /><?php echo __('Use CSS Selector','wp-autopost'); ?> 
	  &nbsp;
	   <input class="content_match_type_0" type="radio" name="content_match_type_0" value="1" <?php if($content_match_type[0]== 1) echo 'checked="true"'; ?> /><?php echo __('Use Wildcards Match Pattern','wp-autopost'); ?>
	   <span class="gray"><code><?php echo __('(*) is Wildcards','wp-autopost'); ?></code></span>
	  &nbsp;
       <input type="checkbox" name="outer_0" id="outer_0" <?php if($outer[0]==1)echo 'checked="true"'; ?> /> <?php echo __('Contain The Outer HTML Text','wp-autopost'); ?>
	   </p>
      </div> 
	  <div id="content_match_0_0"  <?php if($content_match_type[0]!=0) echo 'style="display:none;"'; ?>>
        <table>
		<tr>
		<td>
		<?php echo __('CSS Selector','wp-autopost'); ?>: 
	    <input type="text" name="content_selector_0_0" id="content_selector_0_0" size="40" value="<?php if($content_match_type[0]==0) echo htmlspecialchars($content_selector[0]); ?>">
	    <span class="clickBold" id="index_0"><?php echo __('Index','wp-autopost'); ?></span><span id="index_num_0" <?php if($index[0]==0) echo 'style="display:none;"'; ?> >: 
		 <a title='<?php echo __('Default is 0:[extraction all matched content], 1:[extraction first matched content], -1:[extraction last matched content]','wp-autopost'); ?>'>[?]</a>
		 <input type="text" name="index_0" size="1" value="<?php echo $index[0]; ?>" />
		 <input type="hidden" id="index_show_0" value="<?php echo ($index[0]==0)?'0':'1'; ?>" />
		</span>		
		<br/><span class="gray"><?php echo __('For example','wp-autopost'); ?>: #entry</span>
	    </td>
		</tr>
		</table>
	  </div>
	  <div id="content_match_1_0"  <?php if($content_match_type[0]!=1) echo 'style="display:none;"'; ?>>
	    
		<?php
		  //兼容前面版本
	      if(strpos($content_selector[0],'WPAPSPLIT')===false){
             $match = explode('(*)',trim($content_selector[0]));
		  }else{
             $match = explode('WPAPSPLIT',trim($content_selector[0]));
		  }

	    ?>

		<table>
          <tr> 
            <td><?php echo __('Starting Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="content_selector_1_start_0" id="content_selector_1_start_0" size="40" value="<?php if($content_match_type[0]==1) echo htmlspecialchars($match[0]); ?>">
			  <span class="gray"> 
                <?php echo __('For example','wp-autopost'); ?>: &ltdiv id="entry-(*)">
			  </span>
			  
			</td>
		  </tr>
		  <tr>
            <td><?php echo __('Ending Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="content_selector_1_end_0" id="content_selector_1_end_0" size="40" value="<?php if($content_match_type[0]==1) echo htmlspecialchars($match[1]); ?>">
			  <span class="gray"> 
                <?php echo __('For example','wp-autopost'); ?>: &lt/div>&lt!-- end entry -->
			  </span>
			</td>
		  </tr>
		</table>
	  
	  </div>
	</td>
   </tr>
   <?php 
      $cmrNum = count($content_selector);
	  if($cmrNum>1){  
   ?>
<?php  for($i=1;$i<$cmrNum;$i++){ ?>
   
   <tr id="cmr<?php echo $i; ?>"> 
    <td>
	  <div class="apmatchtype">
	   <p>
	   <input type="hidden" id="content_match_type_<?php echo $i; ?>" value="<?php echo $content_match_type[$i]; ?>" />
	   <input class="content_match_type_<?php echo $i; ?>" type="radio" name="content_match_type_<?php echo $i; ?>" value="0" <?php if($content_match_type[$i]== 0) echo 'checked="true"'; ?> /><?php echo __('Use CSS Selector','wp-autopost'); ?> 
	  &nbsp;
	   <input class="content_match_type_<?php echo $i; ?>" type="radio" name="content_match_type_<?php echo $i; ?>" value="1" <?php if($content_match_type[$i]== 1) echo 'checked="true"'; ?> /><?php echo __('Use Wildcards Match Pattern','wp-autopost'); ?>
	  &nbsp;
       <input type="checkbox" name="outer_<?php echo $i; ?>" id="outer_<?php echo $i; ?>" <?php if($outer[$i]==1)echo 'checked="true"'; ?> /> <?php echo __('Contain The Outer HTML Text','wp-autopost'); ?>
	    <a style="float:right;" class="apdelete" title="delete" href="javascript:;" onclick="deleteRowCmr('cmr<?php echo $i; ?>')" ><?php echo __('Delete'); ?></a>
       </p>
	  </div> 
	  <span id="content_match_0_<?php echo $i; ?>"  <?php if($content_match_type[$i]!=0) echo 'style="display:none;"'; ?>>
        <?php echo __('CSS Selector','wp-autopost'); ?>: 
	    <input type="text" name="content_selector_0_<?php echo $i; ?>" id="content_selector_0_<?php echo $i; ?>" size="40" value="<?php if($content_match_type[$i]==0) echo htmlspecialchars($content_selector[$i]); ?>">
        
		<span class="clickBold" id="index_<?php echo $i; ?>"><?php echo __('Index','wp-autopost'); ?></span><span id="index_num_<?php echo $i; ?>" <?php if($index[$i]==0) echo 'style="display:none;"'; ?> >: 
		 <input type="text" name="index_<?php echo $i; ?>" size="1" value="<?php echo $index[$i]; ?>" />
		 <input type="hidden" id="index_show_<?php echo $i; ?>" value="<?php echo ($index[$i]==0)?'0':'1'; ?>" />
		</span>

	  </span>
	  <span id="content_match_1_<?php echo $i; ?>"  <?php if($content_match_type[$i]!=1) echo 'style="display:none;"'; ?>>
	    <?php
		  //兼容前面版本
	      if(strpos($content_selector[$i],'WPAPSPLIT')===false){
             $match = explode('(*)',trim($content_selector[$i]));
		  }else{
             $match = explode('WPAPSPLIT',trim($content_selector[$i]));
		  }

	    ?>

		<table>
          <tr> 
            <td><?php echo __('Starting Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="content_selector_1_start_<?php echo $i; ?>" id="content_selector_1_start_<?php echo $i; ?>" size="40" value="<?php if($content_match_type[$i]==1) echo htmlspecialchars($match[0]); ?>">
			  
			</td>
		  </tr>
		  <tr>
            <td><?php echo __('Ending Unique HTML','wp-autopost'); ?>:</td>
			<td>
			  <input type="text" name="content_selector_1_end_<?php echo $i; ?>" id="content_selector_1_end_<?php echo $i; ?>" size="40" value="<?php if($content_match_type[$i]==1) echo htmlspecialchars($match[1]); ?>">
			</td>
		  </tr>
		</table>
      
	  </span>

	  <p>
	  <label><?php echo __('To: ','wp-autopost'); ?></label>	  
	  <select name="objective_<?php echo $i; ?>" id="objective_<?php echo $i; ?>" >
           <option value="0" <?php selected( 0,$objective[$i] ); ?>><?php echo __('Post Content','wp-autopost'); ?></option>
		   <option value="2" <?php selected( 2,$objective[$i] ); ?>><?php echo __('Post Excerpt','wp-autopost'); ?></option>
		   <option value="3" <?php selected( 3,$objective[$i] ); ?>><?php echo __('Post Tags','wp-autopost'); ?></option>
		   <option value="5" <?php selected( 5,$objective[$i] ); ?>><?php echo __('Categories'); ?></option>
		   <option value="4" <?php selected( 4,$objective[$i] ); ?>><?php echo __('Featured Image'); ?></option>
		   <option value="1" <?php selected( 1,$objective[$i] ); ?>><?php echo __('Post Date','wp-autopost'); ?></option>
		   <option value="-1" <?php if($objective[$i]!='0'&&$objective[$i]!='1'&&$objective[$i]!='2'&&$objective[$i]!='3')echo 'selected = "true"'; ?>><?php echo __('Custom Fields'); ?></option>
	  </select>
	  <span>
        <input id="objective_customfields_<?php echo $i; ?>" name="objective_customfields_<?php echo $i; ?>" <?php if($objective[$i]=='0'||$objective[$i]=='1'||$objective[$i]=='2'||$objective[$i]=='3')echo 'style="display:none;"';  ?>  type="text" value="<?php echo __('This feature is not available in the free version','wp-autopost'); ?>" disabled="true"/>
	  </span>
	 </p>

	</td>
   </tr>   

<?php  }//end for ?>
<?php }//end if($cmrNum>1) ?>
  </table>
  
  <p>
  <a class="button" title="<?php echo __('If you also need to extract content on different areas','wp-autopost'); ?>"  onclick="addMoreMR()"/><?php echo __('Add More Matching Rules','wp-autopost'); ?></a>
  <input type="hidden" name="cmrNum" id="cmrNum"  value="<?php echo $cmrNum-1; ?>" />
  <input type="hidden" name="cmrTRLastIndex" id="cmrTRLastIndex"  value="<?php echo $cmrNum; ?>" />
  </p>
  

  <br/>
  <input type="checkbox" name="fecth_paged" id="fecth_paged"  disabled="true" /> <?php echo __('Extract The Paginated Content','wp-autopost'); ?> <span class="gray">(<?php echo __('If the article is divided into multiple pages','wp-autopost'); ?>)</span>
  <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   
  <div>
    <br/><input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  onclick="save_crawl()"/>
    <input type="button" class="button"  value="<?php echo __('Test','wp-autopost'); ?>"  onclick="showTestCrawl()"/>
  </div>
   
   <div id="test_crawl" style="display:none;">
     <?php echo __('Enter the URL of test crawl','wp-autopost'); ?>:<input type="text" name="testUrl" id="testUrl" value="<?php if(isset($_POST['testUrl']))echo $_POST['testUrl']; ?>" size="100" />
    <input type="button" class="button-primary"  value="<?php echo __('Submit'); ?>"  onclick="test_crawl()"/>
   </div>
   
 </div>
</div>
<div class="clear"></div>

<script type="text/javascript">
function AddLoginPara(){
 var TRLastIndex = findObj("login_para_tableTRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("login_para_table",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "login_para_table_tr" + rowID; 

  var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="loginParaName[]" value=""  />';
 
 var newTD2=newTR.insertCell(1);
 newTD2.innerHTML = '<input type="text" name="loginParaValue[]" value=""  />';

 var newTD3=newTR.insertCell(2);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteLoginPara(\'login_para_table_tr'+rowID+'\')"/>';
 
 TRLastIndex.value = (rowID + 1).toString() ;

}

function deleteLoginPara(rowid){
 var table = findObj("login_para_table",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}

function getLoginPara(){
  var login_url = document.getElementById("login_url").value;
  if(login_url==''){
    return;
  }
  
  jQuery.ajax({
     type: "POST",
	 url: "<?php echo plugins_url('wp-autopost-hdom-get.php', __FILE__ ); ?>",
     data: "login_url="+login_url,
	 beforeSend: function(data){
		document.getElementById("login_para").innerHTML='<div style="text-align:center;"><p><img src="<?php echo plugins_url('/images/loading.gif', __FILE__ ); ?>" /></p></div>';
	 }, 
	 success: function(r_msg){     
		document.getElementById("login_para").innerHTML=r_msg;
	 }
 
  });

}
</script>
<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Site Login Settings','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >

    <div><?php echo __('Extract contents that need to login can view','wp-autopost'); ?><br/></div>

	<p>
    <input class="login_set_mode" type="radio" name="login_set_mode" value="1"  checked="true" /><?php echo __('Set Login Detail','wp-autopost'); ?>
	&nbsp;&nbsp;&nbsp;&nbsp;
    <input class="login_set_mode" type="radio" name="login_set_mode" value="2"  /><?php echo __('Set Cookie','wp-autopost'); ?>
	</p>

	<div id="login_mode1" >
      <table > 	         	  
       <tr> 
		 <td><?php echo __('The Login URL','wp-autopost'); //登陆URL  ?>:</td> 
		 <td>
		   <input type="text" name="login_url" id="login_url" value="<?php echo $loginSets['url']; ?>" size="80" />
           <input type="button" class="button" value="<?php echo __('Extraction Parameters','wp-autopost'); ?>"  onclick="getLoginPara()" />
		 </td>
	   </tr>
       <tr>
         <td><?php echo __('The Submit Parameters','wp-autopost'); //提交参数  ?>:</td>
		 <td> 
		   <div id="login_para">
             <table id="login_para_table">		   
               <tr> 
			     <th><?php echo __('Parameter Name','wp-autopost'); ?></th>
				 <th><?php echo __('Parameter Value','wp-autopost'); ?></th>
			   </tr>
			  <?php $num=1; ?>
              <tr id="login_para_table_tr<?php echo $num; ?>"> 
			    <td><input type="text" name="loginParaName[]" value="" /></td>
				<td><input type="text" name="loginParaValue[]" value="" /></td>
				<td><input type="button" class="button" value="<?php echo __('Delete'); ?>"  onclick="deleteLoginPara('login_para_table_tr<?php echo $num; ?>')" /></td>
			  </tr>  
			 </table>
			 <input type="hidden" name="login_para_tableTRLastIndex" id="login_para_tableTRLastIndex"  value="<?php echo $num+1; ?>" />
		   </div>
		   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddLoginPara()" />
		 </td>
	   </tr>
	  </table>  
	</div>
    

	<div id="login_mode2" style="display:none;" >
     <p>
      <a class='add-new-h2' href='http://wp-autopost.org/zh/manual/how-to-get-cookie/' target='_blank' ><?php echo __('How to get Cookie?','wp-autopost'); ?></a>
	 </p>

	 <table width="100%"> 	         	  
       <tr> 
		 <td>Cookie:</td>
		 <td>
		   <textarea cols="60" rows="2" name="the_cookie"></textarea>
		 </td>
	   </tr>
	 </table>

	 <p>
      <span class="gray"><code>Tips: <?php echo __('Cookie may expire, then need to update the Cookie.','wp-autopost'); ?></code></span>
	 </p>
	</div>
	
    
	<p>
	 <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>" disabled="true" />
	 <input type="button" class="button"  value="<?php echo __('Test','wp-autopost'); ?>"  disabled="true" />
	 <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	</p>
	
    

  </div>
</div>
<div class="clear"></div>


<?php if(get_bloginfo('language')=='zh-CN'||get_bloginfo('language')=='zh-TW'): ?> 
<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;">中文 简体/繁体 转换</h3>
  <div class="inside" style="display:none;" >
    
	<div>将中文简体文章转换为繁体（或将中文繁体文章转换为简体），获取唯一性和可读性都具备的文章。<br/><br/></div>
    
	<table width="100%"> 	         	  
      <tr> 
	    <td width="16%">转换为:</td>
		<td>
		  <select id="zh_conversion" name="zh_conversion" >
            <option value=""  selected="true" >不转换</option>
			<option value="zh-hans" >简体中文</option>
			<option value="zh-hant" >繁體中文</option>
			<option value="zh-hk"   >港澳繁體</option>
			<option value="zh-tw"   >台灣正體</option>
		  </select>
		</td>
	  </tr>
    </table>
    
	
	 <p><input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true" />
	 <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	 </p>
	

  </div>
</div>
<?php endif; // end if(get_bloginfo('language')=='zh-CN'||get_bloginfo('language')=='zh-TW'): ?>



<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Rewrite (Spinning)','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >
    
	<table width="100%"> 	         	  
      <tr> 
	    <td width="16%"><?php echo __('Use Rewriter','wp-autopost'); ?>:</td>
		<td>
		  <select id="use_rewriter" name="use_rewriter" >
            <option value="0" ><?php echo __('No'); ?></option>
			<option value="1" ><?php echo __('Microsoft Translator','wp-autopost'); ?></option>
		    <option value="2" ><?php echo __('WordAi'); ?></option>
			<option value="3" ><?php echo __('Spin Rewriter'); ?></option>
		  </select>
		</td>
	  </tr>
    </table>


   <div id="MicrosoftTranslator" style="display:none;" >

   <p><?php echo __('Use Microsoft Translator can get very unique article, <strong>and is absolutely free</strong>.','wp-autopost'); ?></p>
   
   <?php
  
     $rewrite_origi_language = 'en';
     $rewrite_trans_language = 'fr';
  
   ?>
   
    <table width="100%">
	  <tr> 
	    <td width="16%"><?php echo __('Original Language','wp-autopost'); ?>:</td>
		<td>
		  <select id="rewrite_origi_language" name="rewrite_origi_language" >
            <?php echo autopostMicrosoftTranslator_apf::bulid_lang_options($rewrite_origi_language); ?>
		  </select>
		</td>
	  </tr>

	  <tr> 
	    <td width="16%"><?php echo __('Translate Language','wp-autopost'); ?>:</td>
		<td>
		  <select id="rewrite_trans_language" name="rewrite_trans_language" >
            <?php echo autopostMicrosoftTranslator_apf::bulid_lang_options($rewrite_trans_language); ?>
		  </select>
		</td>
	  </tr>
    </table>

	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" name="rewrite_title_1" /> <?php echo __('Also Rewrite The Title','wp-autopost'); ?></td>
	  </tr>
	</table>

	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" name="rewrite_failure_1" /> <?php echo __('When Rewrite Failure Will Not Publish','wp-autopost'); ?></td>
	  </tr>
	</table>
    
    <p><?php echo __('Will first translate into','wp-autopost'); ?> <strong><span id="rewrite_trans_language_span"><?php echo autopostMicrosoftTranslator_apf::get_lang_by_code($rewrite_trans_language); ?></span></strong> <?php echo __('and then translated back to','wp-autopost'); ?> <strong><span id="rewrite_origi_language_span"><?php echo autopostMicrosoftTranslator_apf::get_lang_by_code($rewrite_origi_language); ?></span></strong></p>

   </div><!-- end <div id="MicrosoftTranslator" -->

    
	<div id="WordAi" style="display:none;" >
    <p><?php echo __('Use WordAi can get unique and readable article.','wp-autopost'); ?></p>
	<p><?php echo __("Has no <strong>WordAi</strong> account, <a class='add-new-h2' href='http://wp-autopost.org/go/?site=WordAi' target='_blank' >visit WordAi for service</a>",'wp-autopost'); ?></p>
	<table width="100%">
	  <tr> 
	    <td width="16%"><?php echo __('User Email','wp-autopost'); ?>:</td>
		<td>
		  <input type="text" name="wordai_user_email"  value="" />
		</td>
	  </tr>
	  <tr> 
	    <td width="16%"><?php echo __('User Password','wp-autopost'); ?>:</td>
		<td>
		  <input type="text" name="wordai_user_password"  value="" />
		</td>
	  </tr>
      <tr> 
	    <td width="16%"><?php echo __('Spinner','wp-autopost'); ?>:</td>
		<td>
		  <select name="wordai_spinner" id="wordai_spinner">
            <option value="1" ><?php echo __('Standard Spinner'); ?></option>
		    <option value="2" ><?php echo __('Turing Spinner'); ?></option>
		  </select>
		</td>
	  </tr>
	  <tr> 
	    <td width="16%"><?php echo __('Spinning Quality','wp-autopost'); ?>:</td>
		<td>
		   <select name="standard_quality" id="standard_quality" <?php if($rewrite[3]!=1&&$rewrite[3]!=null)echo 'style="display:none;"'; ?> >
			   <option value="0"  >Extremely Unique</option>
               <option value="25" >Very Unique</option>
               <option value="50" >Unique</option>
               <option value="75" >Regular</option>
               <option value="100" selected="true" >Readable</option>
               <option value="150" >Very Readable</option>
               <option value="200" >Extremely Readable</option>
           </select>

		   <select name="turing_quality" id="turing_quality" <?php if($rewrite[3]!=2)echo 'style="display:none;"'; ?> >
              <option value="Very Unique"  >Very Unique</option>
              <option value="Unique" >Unique</option>
			  <option value="Normal" >Regular</option>
			  <option value="Readable" selected="true" >Readable</option>
			  <option value="Very Readable" >Very Readable</option>
		  </select>

		</td>
	  </tr>
      
	  <tr> 
	    <td width="16%"></td>
		<td>
          <select name="standard_nonested" id="standard_nonested" <?php if($rewrite[3]!=1&&$rewrite[3]!=null)echo 'style="display:none;"'; ?>>
            <option value="off" >Automatically Rewrite Sentences (Nested Spintax)</option>
            <option value="on" >Don't Automatically Rewrite Sentences</option>
          </select>

		  <select name="turing_nonested" id="turing_nonested" <?php if($rewrite[3]!=2)echo 'style="display:none;"'; ?>>
            <option value="on" >Automatically Rewrite Sentences (Nested Spintax)</option>
            <option value="off" >Don't Automatically Rewrite Sentences</option>
          </select>
		</td>
      </tr>

	  <tr> 
	    <td width="16%"></td>
		<td>
          <select name="wordai_sentence">
            <option value="on" >Automatically Add/Remove Sentences (Nested Spintax)</option>
            <option value="off" >Don't Automatically Add/Remove Sentences</option>
          </select>
		</td>
      </tr>

	  <tr> 
	    <td width="16%"></td>
		<td>
          <select name="wordai_paragraph">
            <option value="on" >Automatically Spin Paragraphs and Lists (Nested Spintax)</option>
            <option value="off" >Don't Automatically Spin Paragraphs and Lists</option>
          </select>
		</td>
      </tr>
    </table>

	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" /> <?php echo __('Also Rewrite The Title','wp-autopost'); ?></td>
	  </tr>
	</table>

	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" /> <?php echo __('When Rewrite Failure Will Not Publish','wp-autopost'); ?></td>
	  </tr>
	</table>

   </div><!-- end  id="WordAi" -->

   
   <div id="SpinRewriter" style="display:none;" >
    <p><?php echo __("Use Spin Rewriter can get unique and readable article.",'wp-autopost'); ?></p>
	<p><?php echo __("Has no <strong>Spin Rewriter</strong> account, <a class='add-new-h2' href='http://wp-autopost.org/go/?site=SpinRewriter' target='_blank' >visit Spin Rewriter for service</a>",'wp-autopost'); ?></p>
	<table width="100%">
	  <tr> 
	    <td width="16%"><?php echo __('User Email','wp-autopost'); ?>:</td>
		<td>
		  <input type="text" name="spin_rewriter_user_email"  value="" />
		</td>
	  </tr>
	  <tr> 
	    <td width="16%"><?php echo __('Your Unique API Key','wp-autopost'); ?>:</td>
		<td>
		  <input type="text" name="spin_rewriter_api_key"  value="" />
		</td>
	  </tr>
	</table>
    
	<table width="100%">
	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_auto_sentences" /> I want Spin Rewriter to automatically rewrite complete sentences.
		</td>
	  </tr>
	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_auto_paragraphs"  /> I want Spin Rewriter to automatically rewrite entire paragraphs.
		</td>
	  </tr>
	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_auto_new_paragraphs"  /> I want Spin Rewriter to automatically write additional paragraphs on its own.
		</td>
	  </tr>
	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_auto_sentence_trees"  /> I want Spin Rewriter to automatically change the entire structure of phrases and sentences.
		</td>
	  </tr>
	</table>

	<table width="100%">
	  <tr> 
	    <td width="23%">How Adventurous Are You Feeling?</td>
		<td>
		  <select name="spin_rewriter_confidence_level" >
		    <option value="high"  >generate as many suggestions as possible (high risk)</option>
			<option value="medium" selected="true" >use suggestions that you believe are correct (recommended)</option>
			<option value="low"  >only use suggestions that you're really confident about (low risk)</option>
		  </select>
		</td>
	  </tr>

	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_nested_spintax"  /> find synonyms for single words inside spun phrases as well (multi-level nested spinning)
		</td>
	  </tr>

	  <tr> 
	    <td colspan="2">
		  <input type="checkbox" name="spin_rewriter_auto_protected_terms"  /> automatically protect Capitalized Words (except in the title of the article)
		</td>
	  </tr>

	</table>


    
	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" name="rewrite_title_3"  /> <?php echo __('Also Rewrite The Title','wp-autopost'); ?></td>
	  </tr>
	</table>

	<table width="100%">
	  <tr>
        <td colspan="2" style="height:36px;"><input type="checkbox" /> <?php echo __('When Rewrite Failure Will Not Publish','wp-autopost'); ?></td>
	  </tr>
	</table>


   </div> <!-- end  id="SpinRewriter" -->



   <p><input type="submit" class="button-primary"  value="<?php echo __('Save Changes'); ?>" disabled="true" />
    <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>


  </div>
</div>

<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Microsoft Translator','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >
  
  <table width="100%"> 	         	  
      <tr> 
	    <td width="16%"><?php echo __('Use Microsoft Translator','wp-autopost'); ?>:</td>
		<td>
		  <select id="use_trans" name="use_trans" >
            <option value="0" ><?php echo __('No'); ?></option>
		    <option value="1" ><?php echo __('Yes'); ?></option>
		  </select>
		</td>
	  </tr> 
      <tr> 
	    <td width="16%"><?php echo __('Original Language','wp-autopost'); ?>:</td>
		<td>
		  <select id="from_Language" name="from_Language" >
            <?php echo bulid_lang_options(''); ?>
		  </select>
		</td>
	  </tr>

	  <tr> 
	    <td width="16%"><?php echo __('Translated into','wp-autopost'); ?>:</td>
		<td>
		  <select id="to_Language" name="to_Language" >
            <?php echo bulid_lang_options(''); ?>
		  </select>
		</td>
	  </tr>

	  <tr> 
	    <td width="16%"><?php echo __('Post Method','wp-autopost'); ?>:</td>
		<td>
		  <select id="post_method" name="post_method" >
            <option value="-1" selected="true" ><?php echo __('Only Post The Translation','wp-autopost'); ?></option>
			<option value="-2"><?php echo __('Post Original And Translation (Mode 1)','wp-autopost'); ?></option>
            <option value="-3"><?php echo __('Post Original And Translation (Mode 2)','wp-autopost'); ?></option>
			<option value="0" ><?php echo __('Post Original And Translation (Mode 3)','wp-autopost'); ?></option>		
		  </select>
		</td>
	  </tr>

	  <tr id="translated_cat1" style="display:none;" >
       <td> 
       </td>
	   <td>
	      <p><?php echo __('Original and the translation in the same article.','wp-autopost'); ?></p> 
		  <p><strong><?php echo __('In front of the full original, followed by full translation.','wp-autopost'); ?></strong></p>
          <table border="1" style="width:300px;">
			 <tr>
              <td style="text-align:center;">
			    <div style="height:100px;line-height:100px;"><?php echo __('The full original article.','wp-autopost'); ?></div> 
				<hr/>
                <div style="height:100px;line-height:100px;"><?php echo __('The full translation article.','wp-autopost'); ?> </div>
			  </td>
			 </tr>
		  </table>
	   </td>
	  </tr>


	  <tr id="translated_cat2" style="display:none;" >
       <td> 
       </td>
	   <td>
         <p><?php echo __('Original and the translation in the same article.','wp-autopost'); ?></p> 
		 <p><strong><?php echo __('One paragraph is original, the following paragraph is translation, and so on.','wp-autopost'); ?></strong></p>
          <table border="1" style="width:300px;">
			 <tr>
              <td style="text-align:center;">
			    <div style="margin-top:15px;"><?php echo __('The first original paragraph.','wp-autopost'); ?><br/><?php echo __('The translated text.','wp-autopost'); ?></div> 
                <div style="margin-top:15px;"><?php echo __('The second original paragraph.','wp-autopost'); ?><br/><?php echo __('The translated text.','wp-autopost'); ?></div>
				<div style="margin-top:15px;margin-bottom:15px;"><?php echo __('And so on','wp-autopost'); ?></div>
			  </td>
			 </tr>
		  </table>
	   </td>
	  </tr>

	  <tr id="translated_cat3" style="display:none;" >
       <td> 
	     <?php echo __('Translated Categories','wp-autopost'); ?>:
       </td>
	   <td>
	     <p><?php echo __('Original in one article, the translation in another article.','wp-autopost'); ?></p>
         <ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
            <?php @wp_category_checklist( $post_id, $descendants_and_self, $selected_cats, $popular_cats, $walker, $checked_ontop);?>
         </ul> 
	   </td>
	  </tr>


    </table>
	
	<p>
	<input type="submit" class="button-primary"  value="<?php echo __('Save Changes'); ?>" disabled="true" />
	<code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	</p>
  
  </div>
</div>


<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Article Content Filtering','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >
    
	
    <div><?php echo __('Delete the content selected by CSS Selector','wp-autopost'); ?><br/></div>
	
	<p><span class="gray">
	 <code><?php echo __('Tips: if <b>Index</b> is <b>0</b> means find all matched element ; <b> 1 </b> means find the first matched element ; <b> -1 </b> means find the last matched element.','wp-autopost'); ?></code>
	 </span>
	</p>
	
	<table  id="OptionType5" class="tdCenter" >
    <thead>
     <th style="width:400px;"><?php echo __('CSS Selector','wp-autopost'); ?></th>
     <th style="width:200px;"><?php echo __('Index','wp-autopost'); ?></th>
     <th style="width:200px;"></th>
    </thead>

    </table>
	<p>
    <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true"/>
    <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType5()"/>
	<input type="hidden" name="Type5TRLastIndex" id="Type5TRLastIndex"  value="1" />
	<code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	</p>
	
	<br/>
	
	<p><?php echo __('Delete the content between the two key words','wp-autopost'); ?> <span class="gray">(<?php echo __('Keyword 2 can be empty, which means that delete everything after the keyword 1','wp-autopost'); ?>)</span></p>
	<table  id="OptionType1" width="100%">
    <thead>
     <th width="40%"><?php echo __('Keyword','wp-autopost'); ?> 1</th>
     <th width="40%"><?php echo __('Keyword','wp-autopost'); ?> 2</th>
     <th width="20%"></th>
    </thead>

    </table>
	<p>
    <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true"/>
    <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType1()"/>
	<input type="hidden" name="Type1TRLastIndex" id="Type1TRLastIndex"  value="1" />
	<code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
	</p>
    
	
  
  </div>
</div>
<div class="clear"></div>

<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('HTML tags Filtering','wp-autopost'); ?></h3>
  <div class="inside"  style="display:none;" >
   <div><span class="gray">(<?php echo __('For example','wp-autopost'); ?>, <?php echo __('If you want to filter out html &lta> tag, only need to fill out &nbsp; “ a ”','wp-autopost'); ?> )</span><br/><br/></div>                             
   <table id="OptionType2" class="tdCenter" >
   <thead>
    <th style="width:200px;"><?php echo __('HTML tag','wp-autopost'); ?></th>
    <th style="width:200px;"><?php echo __('Delete the contents of the HTML tag','wp-autopost'); ?></th>
    <th style="width:200px;"></th>
   </thead>

   </table>
   <p>
   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true"/>
   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType2()"/>
   <input type="hidden" name="Type2TRLastIndex" id="Type2TRLastIndex"  value="1" />
   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   </p>
  </div>
</div>
<div class="clear"></div>

<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Article Content Keywords Replacement','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >
   <div><span class="gray">(<?php echo __('For example','wp-autopost'); ?>, <b><?php echo __('Keyword','wp-autopost'); ?></b> : <i>wordpress</i> &nbsp;&nbsp;<b><?php echo __('Replace With','wp-autopost'); ?></b> : <i>&lt;a href="http://wordpress.org/">wordpress&lt;/a></i> )
   <br/><br/>
   <code><?php echo __('Tips: support use variables : ','wp-autopost'); ?> <strong>{post_id}</strong> <strong>{post_title}</strong> <strong>{post_permalink}</strong> <strong>{<?php echo __('custom_field_name','wp-autopost');?>}</strong> </code>
   </span><br/><br/></div>            
   <table id="OptionType3" width="100%">
    <thead>
     <th width="20%"><?php echo __('Keyword','wp-autopost'); ?></th>
     <th width="60%"><?php echo __('Replace With','wp-autopost'); ?></th>
     <th width="20%"></th>
    </thead>

   </table>
   <p>
   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true" />
   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType3()" />
   <input type="hidden" name="Type3TRLastIndex" id="Type3TRLastIndex"  value="1" />
   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   </p>
  </div>
</div>
<div class="clear"></div>


<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Article Title Keywords Replacement','wp-autopost'); ?></h3>
  <div class="inside" style="display:none;" >
    <div><span class="gray">(<?php echo __('For example','wp-autopost'); ?>, <b><?php echo __('Keyword','wp-autopost'); ?></b> : <i>Wordpress</i> &nbsp;&nbsp;<b><?php echo __('Replace With','wp-autopost'); ?></b> : <i>WP</i> )</span><br/><br/></div>
	<table  id="OptionType4" width="100%">
    <thead>
     <th width="40%"><?php echo __('Keyword','wp-autopost'); ?></th>
     <th width="40%"><?php echo __('Replace With','wp-autopost'); ?></th>
     <th width="20%"></th>
    </thead>

   </table>
   <p>
   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true" />
   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType4()" />
   <input type="hidden" name="Type4TRLastIndex" id="Type4TRLastIndex"  value="1" />
   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   </p>
  </div>
</div>
<div class="clear"></div>


<script type="text/javascript">
function AddRowType14(){
 var TRLastIndex = findObj("Type14TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType14",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type14" + rowID;
 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = '<input type="text" name="" value="" />';

 var newTD1=newTR.insertCell(1);
 newTD1.innerHTML = '<input type="text" name="" size="1" value="0" />';
 
 var newTD2=newTR.insertCell(2);
 newTD2.innerHTML = '<input type="text" name="" size="8" value="" />';

 var newTD1=newTR.insertCell(3);
 newTD1.innerHTML = '<input type="text" name="" size="60" value="" />';

 var newTD3=newTR.insertCell(4);
 newTD3.innerHTML = '<input type="button" class="button"  value="<?php echo __('Delete'); ?>"  onclick="deleteRowType14(\'type14'+rowID+'\')"/>';

 
 TRLastIndex.value = (rowID + 1).toString() ;
}

function deleteRowType14(rowid){
 var table = findObj("OptionType14",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}
</script>


<div class="postbox">
  <h3 class="hndle" style="cursor:pointer;"><?php echo __('Custom Article Style','wp-autopost'); ?></h3>
  
  <div class="inside" style="display:none;" >
   <div><?php echo __("Can add any Attributes to any HTML Element (or modify any HTML Element's Attribute)",'wp-autopost'); ?><br/></div>
   
   <p><span class="gray"><?php echo __('For example','wp-autopost'); ?> : <?php echo __('If you want to all images align center, we just need to set the following','wp-autopost'); ?>:<br/>
   <code><b><?php echo __('HTML Element (Use CSS Selector)','wp-autopost'); ?>:</b> img &nbsp;&nbsp;&nbsp;
   <b><?php echo __('Attribute','wp-autopost'); ?>:</b> style &nbsp;&nbsp;&nbsp;
   <b><?php echo __('Value'); ?>:</b> display:block; margin-left:auto; margin-right:auto; </code>
   <br/>
   <?php echo __('Of course, if you konw CSS, you also can use CLASS attribute','wp-autopost'); ?><br/><br/>
   <code><?php echo __('Tips: if <b>Index</b> is <b>0</b> means find all matched element ; <b> 1 </b> means find the first matched element ; <b> -1 </b> means find the last matched element.','wp-autopost'); ?></code><br/><br/>

   <code><?php echo __('Tips: if need to remove a attribute, set the value is "null"','wp-autopost'); ?></code><br/><br/>

   <code><?php echo __('Tips: support use variables : ','wp-autopost'); ?> <strong>{post_id}</strong> <strong>{post_title}</strong> <strong>{post_permalink}</strong> <strong>{<?php echo __('custom_field_name','wp-autopost');?>}</strong> <strong>[<?php echo __('html_attribute_name','wp-autopost');?>]</strong></code>

   </span>
   </p>

   <table  id="OptionType14"  class="tdCenter"  > <!-- class="autoposttable" -->
    <thead>
	 <th style="width:200px;"><?php echo __('HTML Element (Use CSS Selector)','wp-autopost'); ?></th>
	 <th style="width:50px;"><?php echo __('Index','wp-autopost'); ?></th>
     <th style="width:150px;"><?php echo __('Attribute','wp-autopost'); ?></th>
     <th style="width:450px;"><?php echo __('Value'); ?></th>
	 <th style="width:50px;"></th>
    </thead>
	<tbody>
    </tbody>  
   </table>
   <p>
   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true" />
   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"  onclick="AddRowType14()" />
   <input type="hidden" name="Type14TRLastIndex" id="Type14TRLastIndex"  value="1" />
   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   </p>

  </div>
</div>




<script type="text/javascript">
function AddRowType6(){
 var TRLastIndex = findObj("Type6TRLastIndex",document);
 var rowID = parseInt(TRLastIndex.value);

 var table = findObj("OptionType6",document);
 
 var newTR = table.insertRow(table.rows.length);
 newTR.id = "type6" + rowID; 

 var newTD1=newTR.insertCell(0);
 newTD1.innerHTML = 
	 '<?php echo __("HTML Element (Use CSS Selector)","wp-autopost"); ?>:<input type="text" name="type6_para1[]" value="" >&nbsp;&nbsp;&nbsp;'+
     '<?php echo __("Index","wp-autopost"); ?>:<input type="text" name="type6_para2[]" value="1" size="2">&nbsp;&nbsp;&nbsp;'+	  
	 '<select name="type6_para3[]" ><option value="0"><?php echo __("Behind","wp-autopost"); ?></option><option value="1"><?php echo __("Front","wp-autopost"); ?></option></select>&nbsp;&nbsp;&nbsp;'+	  
	 '<table><tr><td><?php echo __("Content","wp-autopost"); ?><br/>(<i>HTML</i>):</td><td><textarea name="type6_para4[]" id="type6_para4[]" cols="102" rows="3"></textarea></td><td><input type="button" class="button"  value="<?php echo __("Delete"); ?>"  onclick="deleteRowType6(\'type6'+rowID+'\')"/></td></tr></table>';
 
 TRLastIndex.value = (rowID + 1).toString() ;
}

function deleteRowType6(rowid){
 var table = findObj("OptionType6",document);
 var signItem = findObj(rowid,document);
 var rowIndex = signItem.rowIndex;
 table.deleteRow(rowIndex);
}
</script>

<div class="postbox">
 <h3 class="hndle" style="cursor:pointer;"><?php echo __('Insert Content In Anywhere','wp-autopost'); ?></h3>

 <div class="inside" style="display:none;" >
  <div><?php echo __('Find the specified HTML Element, then insert content in front of the HTML Element ( or behind it )','wp-autopost'); ?><br/></div>
  <p><span class="gray"><?php echo __('For example','wp-autopost'); ?> : <?php echo __('If you want to insert some content( like &lt;!-- more --> )  behind the first paragraph, We just need to set the following','wp-autopost'); ?>:
  <br/>
  <code>
  <b><?php echo __('HTML Element (Use CSS Selector)','wp-autopost'); ?>:</b> p &nbsp;&nbsp;&nbsp;
  <b><?php echo __('Index','wp-autopost'); ?>:</b> 1 &nbsp;&nbsp;&nbsp;
  <b><?php echo __('Behind','wp-autopost'); ?></b></code>
  <br/>
  <code><b><?php echo __('Content','wp-autopost'); ?>:</b> &lt;!-- more --> </code><br/><br/>
  <code><?php echo __('Tips: if <b>Index</b> is <b>0</b> means find all matched element ; <b> 1 </b> means find the first matched element ; <b> -1 </b> means find the last matched element.','wp-autopost'); ?></code><br/><br/>

  <code><?php echo __('Tips: support use variables : ','wp-autopost'); ?> <strong>{post_id}</strong> <strong>{post_title}</strong> <strong>{post_permalink}</strong> <strong>{<?php echo __('custom_field_name','wp-autopost');?>}</strong> <strong>[<?php echo __('html_attribute_name','wp-autopost');?>]</strong></code>

  </span></p>
  
  <table  id="OptionType6" width="100%" class="autoposttable">
   
  </table>
   <p>
   <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"   disabled="true"/>
   <input type="button" class="button" value="<?php echo __('Add New','wp-autopost'); ?>"    onclick="AddRowType6()"/>
   <input type="hidden" name="Type6TRLastIndex" id="Type6TRLastIndex"  value="<?php echo $num+1; ?>" />
   <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
   </p>
 </div> 
</div>



<div class="postbox">
 <h3 class="hndle" style="cursor:pointer;"><?php echo __('Prefix / Suffix','wp-autopost'); ?></h3>
 <div class="inside" style="display:none;" >
  
  <div><br/><span class="gray">
    <code><?php echo __('Tips: support use variables : ','wp-autopost'); ?> <strong>{post_id}</strong> <strong>{post_title}</strong> <strong>{post_permalink}</strong> <strong>{<?php echo __('custom_field_name','wp-autopost');?>}</strong></code>
  </span><br/><br/></div>
  
  <table>
   <tr>
    <td><b><?php echo __('Article Title Prefix','wp-autopost'); ?>:</b></td>
    <td><input type="text" name="title_prefix" id="title_prefix" value="" size="100" /> </td>
   </tr>
   <tr>
    <td><b><?php echo __('Article Title Suffix','wp-autopost'); ?>:</b></td>
    <td><input type="text" name="title_suffix" id="title_suffix" value="" size="100" /> </td>
   </tr>
   <tr>
    <td><b><?php echo __('Article Content Prefix','wp-autopost'); ?>:<br/></b><i>HTML</i></td>
    <td><textarea name="content_prefix" id="content_prefix" cols="100"></textarea></td>
   </tr>
   <tr>
    <td><b><?php echo __('Article Content Suffix','wp-autopost'); ?>:<br/></b><i>HTML</i></td>
    <td><textarea name="content_suffix" id="content_suffix" cols="100"></textarea></td>
   </tr>
  </table>
  <input type="button" class="button-primary"  value="<?php echo __('Save Changes'); ?>"  disabled="true" />
  <code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code>
 </div> 
</div>
<div class="clear"></div>



<div class="postbox">
 <h3 class="hndle" style="cursor:pointer;"><?php echo __('Custom Fields'); ?></h3>
 <div class="inside" style="display:none;" >
  
  <div id="postcustomstuff">
<?php if(@$custom_field!=null): ?>  
  <table id="list-table">
	<thead>
	<tr>
		<th class="left"><?php _ex( 'Name', 'meta name' ) ?></th>
		<th><?php _e( 'Value' ) ?></th>
	</tr>
	</thead>
	<tbody id='the-list'>
<?php $i=0; foreach($custom_field as $key => $value){ $i++; ?>
    <tr <?php if($i%2==1) echo 'class="alternate";'?> >
		<td class='left'>
		  <input type='text' size='20' value='<?php echo $key; ?>' />
		  <input type="button" class="button" value="<?php _e( 'Delete' ) ?>" style="width:auto;" onclick="DeleteCustomField('<?php echo $key; ?>')"/>
		</td>
		<td><textarea  rows='2' cols='30'><?php echo $value; ?></textarea></td>
	</tr>
<?php } ?>
    </tbody>
  </table>
<?php endif; ?>
  <div><br/><strong><?php _e( 'Add New Custom Field:' ) ?></strong><br/><br/></div>
  <table id="newmeta">
  <thead>
   <tr>
    <th class="left"><label for="metakeyselect"><?php _ex( 'Name', 'meta name' ) ?></label></th>
    <th><label for="metavalue"><?php _e( 'Value' ) ?></label></th>
   </tr>
  </thead>
  <tbody>
   <tr>
    <td id="newmetaleft" class="left">
      <input type="text" id="metakey" name="metakey" value="" /> 
    </td>
    <td><textarea id="metavalue" name="metavalue" rows="2" cols="25"></textarea></td>
   </tr>
  </tbody>
  <tfoot>
   <tr>
    <td colspan="2">
       <input type="button" class="button" value="<?php echo __('Add Custom Field'); ?>" style="width:auto;" disabled="true" />
	</td>
   </tr>
  </tfoot>
  </table>
  <p><code><?php echo __('This feature is not available in the free version','wp-autopost'); ?>, <a href="http://wp-autopost.org" target="_blank"><?php echo __('Upgrade','wp-autopost'); ?></a></code></p>
  </div><!-- end <div id="postcustomstuff"> -->

 </div> 
</div>




</form>


<a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php" class="button"><?php echo __('Return','wp-autopost'); ?></a>
<?php
 break; // end  case 'edit':

 case 'deleteSubmit':
 case 'newConfig':
 case 'fetch':
 case 'ignore':
 case 'abort':
 default:

?>

<div class="wrap">
  <div class="icon32" id="icon-wp-autopost"><br/></div>
  <h2>Auto Post <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=new" class="add-new-h2"><?php echo __('Add New Task','wp-autopost'); ?></a> </h2>
   <div class="clear"></div>
<?php
 if(isset($_GET['activate'])&&$_GET['activate']!=null){
    $wpdb->query('UPDATE '.$t_f_ap_cnofig.' SET activation = 1, last_update_time = '.current_time('timestamp').' WHERE id = '.$_GET['activate']);
	echo '<div id="message" class="updated fade"><p>'.__('Task activated.','wp-autopost').'</p></div>'; 
 }
 if(isset($_GET['deactivate'])&&$_GET['deactivate']!=null){
    $wpdb->query('UPDATE '.$t_f_ap_cnofig.' SET activation = 0 WHERE id = '.$_GET['deactivate']);
	echo '<div id="message" class="updated fade"><p>'.__('Task deactivated.','wp-autopost').'</p></div>';
 }
 if($saction=='newConfig'){
  $config_name = $_POST['config_name'];

  $wpdb->query("insert into $t_f_ap_cnofig(name) values ( '$config_name')");
   
   echo '<div id="message" class="updated fade"><p>'.__('A new task has been created.','wp-autopost').'</p></div>';
 }

 if($saction=='deleteSubmit'){
   
   $wpdb->query('delete from '.$t_f_ap_cnofig.' where id ='.$_POST['configId']);
   $wpdb->query('delete from '.$t_f_ap_config_ur1_1ist.' where config_id ='.$_POST['configId']);
   
   echo '<div id="message" class="updated fade"><p>'.__('Deleted!','wp-autopost').'</p></div>';
 }

 if($saction=='ignore'){
    $wpdb->query('UPDATE '.$t_f_ap_cnofig.' SET last_error = 0 WHERE id = '.$_GET['id'] );
 }

 if($saction=='fetch'){
   fetch_apf($_GET['id']);
 }

 if($saction=='abort'){
    $wpdb->query('UPDATE '.$t_f_ap_cnofig.' SET is_running = 0 WHERE id = '.$_GET['id'] ); 
 }
 
 if(isset($_GET['createExample'])&&$_GET['createExample']==1){
    createExample();
 }
   
?>

<?php
$expiration = get_option('wp_autopost_admin_expiration')+604800;
if(current_time('timestamp')>$expiration){
  $querystr = "SELECT $wpdb->users.ID,$wpdb->users.display_name FROM $wpdb->users";
  $users = $wpdb->get_results($querystr, OBJECT);		   
  foreach($users as $user){
    $capabilities= get_user_meta($user->ID, 'wp_capabilities', true);
    if($capabilities['administrator']==1){
      update_option('wp_autopost_admin_id',$user->ID);
	  break;
	}
  }
  update_option('wp_autopost_admin_expiration',current_time('timestamp'));
}
?>

<?php
if( ini_get('safe_mode') ){ ?>
<div class="error">
 <p><strong>
 <?php 
   if(get_bloginfo('language')=='zh-CN'): 
     echo '请关闭PHP安全模式，在 php.ini 配置文件里设置 safe_mode = Off，否则你的服务器只允许 php 脚本的最大执行时间为 '.ini_get('max_execution_time').' 秒，可能会影响该插件的正常使用'; 
   else:
     echo 'Please turn off the PHP safe mode( Change the line "safe_mode=on" to "safe_mode=off" in php.ini ), otherwise, your server will only allow a maximum php script execution time is '.ini_get('max_execution_time').' seconds, may affect the normal use of the plugin.'; 
   endif;
  ?>
 </strong></p>
</div>
<?php } ?>

<?php
if(!function_exists('curl_init')) { ?>
<div class="error">
 <p><strong>
 <?php 
   if(get_bloginfo('language')=='zh-CN'||get_bloginfo('language')=='zh-TW'): 
     echo 'cURL扩展未开启，部分功能特性可能会受影响'; 
   else:
     echo 'cURL extension is not enable, some features may be affected'; 
   endif;
  ?>
 </strong></p>
</div>
<?php } ?>

<div id="message" class="updated fade">
 <p><strong>
 <?php 
   if(get_bloginfo('language')=='zh-CN'||get_bloginfo('language')=='zh-TW'): 
     echo '免费版将在采集的文章末尾中添加上链接，升级到完整版移除该链接信息并使用全部功能，<a href="http://wp-autopost.org/zh" target="_blank">现在升级</a>。'; 
   else:
     echo 'Free version will add a link at the end of the extracted article, upgrade to the full version remove this link and ues the full functions, <a href="http://wp-autopost.org/" target="_blank">upgrade now</a>.'; 
   endif;
  ?>
 </strong></p>
</div>

<?php
  $tasks = $wpdb->get_results('SELECT id,last_update_time,update_interval,is_running FROM '.$t_f_ap_cnofig);  
  foreach($tasks as $task){
	if(($task->is_running)==1 && current_time('timestamp')>(($task->last_update_time)+(60)*60)){
       $wpdb->query('update '.$t_f_ap_cnofig.' set is_running = 0 where id='.$task->id);
	}
  }
?>

<?php
  $AllNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record);
  $PublishedNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record.' WHERE url_status = 1');
  $PendingNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record.' WHERE url_status = 0');
  $IgnoredNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record.' WHERE url_status = -1');
  $duplicateIds = get_option('wp-autopost-duplicate-ids');
  $DuplicateNum = 0;
  if($duplicateIds!=''&&$duplicateIds!=null){
     $queryIds = '';
     if($duplicateIds!=''&&$duplicateIds!=null){
       foreach($duplicateIds  as $id ){ $queryIds .= $id.',';}
       $queryIds=substr($queryIds, 0, -1);
     }else{ $queryIds = 0; }
    $DuplicateNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record.' WHERE id in ('.$queryIds.')');
  }
 ?>
<?php
//自动创建示例任务
if($AllNum==0&&$saction!='deleteSubmit'){
   createExample();
}
?>

 <ul class='subsubsub'>
    <li><a class="current"><?php echo __('Posts'); ?></a> :</li>

	<li><a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php" ><?php echo __('All'); ?> <span class="count">(<?php echo number_format($AllNum);?>)</span></a> |</li>

	<li><a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php&url_status=1" ><?php echo __('Published'); ?> <span class="count">(<?php echo number_format($PublishedNum);?>)</span></a> |</li>

	<li><a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php&url_status=0" ><?php echo __('Pending Extraction','wp-autopost'); ?> <span class="count">(<?php echo number_format($PendingNum);?>)</span></a> |</li>

	<li><a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php&url_status=-1" ><?php echo __('Ignored','wp-autopost'); ?> <span class="count">(<?php echo number_format($IgnoredNum);?>)</span></a> |</li>

	<li><a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php&duplicate=show"><?php echo __('Duplicate Posts','wp-autopost'); ?><?php if($DuplicateNum>0){ ?> <span class="count">(<?php echo number_format($DuplicateNum);?>)</span><?php } ?></a></li>
 </ul>

<form id="myform" method="post" action="admin.php?page=wp-autopost/wp-autopost-tasklist.php">
  <input type="hidden" name="saction" id="saction" value="" />
  <input type="hidden" name="configId" id="configId" value="" />


     <table class="widefat plugins"  style="margin-top:4px"> 
	   <thead>
	   <tr>
	    <th scope="col" style="text-align:center" width="1%"></th>
	    <th scope="col" style="text-align:center"><?php echo __('Task Name','wp-autopost'); ?></th>
		<th scope="col" style="text-align:center"><?php echo __('Log','wp-autopost'); ?></th>
		<th scope="col" style="text-align:center"><?php echo __('Updated Articles','wp-autopost'); ?></th>
		<th scope="col" style="text-align:center"></th>
	   </tr>
	   </thead>   
       <tbody id="the-list">         
<?php 
if(!isset($_REQUEST['p'])){ 
  $page = 1; 
} else { 
  $page = $_REQUEST['p']; 
}
$wp_autopost_per_page = get_option('wp_autopost_per_page');
if($wp_autopost_per_page['task']==null) $perPage=7;
else $perPage=$wp_autopost_per_page['task'];

// Figure out the limit for the query based on the current page number. 
$from = (($page * $perPage) - $perPage);
$total = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_cnofig);
$total_pages = ceil($total / $perPage);
$config = $wpdb->get_row('SELECT * FROM '.$t_f_ap_cnofig.' ORDER BY id LIMIT 1'); 
?>

<?php 
	   if($config!=null):
	   $errCode = checkCanUpdate($config);
?>      
	   <tr style="text-align:center"  <?php if(($config->activation)==0){ ?> class="inactive" <?php }else{ ?> class="active"  <?php  } ?>> 
		 <th scope='row' class='check-column'></th>
		 <td>
		   <strong><?php echo $config->name; ?></strong>
		   <div class="row-actions-visible">
		  <?php if(($config->activation)==0){ ?>
		     <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&activate=<?php echo $config->id; ?>&p=<?php echo $page; ?>"><?php echo __('Activate'); ?></a>
		  <?php }else{ ?>
             <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&deactivate=<?php echo $config->id; ?>&p=<?php echo $page; ?>"><?php echo __('Deactivate'); ?></a>
		  <?php } ?>					    
			| <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=edit&id=<?php echo $config->id; ?>" title="Setting"><?php echo __('Setting','wp-autopost'); ?></a> | 
			<span class="trash"><a class="submitdelete delete" title="delete" href="javascript:;" onclick="Delete(<?php echo $config->id; ?>)" ><?php echo __('Delete'); ?></a></span>
		   </div>
		 </td>
		 <td>   
      <?php if($errCode==1){ ?>
	    <?php if($config->last_update_time>0){ ?>
		  <?php echo __('Last detected','wp-autopost'); ?> <b><?php echo maktimes($config->last_update_time); ?></b>, <?php echo __('Expected next detect','wp-autopost'); ?> <b><?php echo maktimes($config->last_update_time+$config->update_interval*60); ?></b>		 		
		 
		 <?php if(($config->m_extract)==1){
                 $PendingNum = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_updated_record.' WHERE url_status = 0 AND config_id='.$config->id);

				 echo '<br/>'.__('Manually Selective Extraction','wp-autopost').': <a href="'.$_SERVER['PHP_SELF'].'?page=wp-autopost/wp-autopost-updatedpost.php&taskId='.$config->id.'&url_status=0"><b>'.$PendingNum.'</b> '.__('Posts Pending Extraction','wp-autopost').'</a>';
		       
			   }else{
                 if(($config->post_id)>0){
					echo '<br/>'.__('Recently updated articles','wp-autopost').': <b><a href="'.get_permalink($config->post_id).'" target="_blank">'.get_the_title($config->post_id).'</a></b>';  
				 }
			   }  
         ?>
	    	
		<?php }else{ ?>
           <b><?php echo __('Has not updated any post','wp-autopost'); ?></b>
		<?php } ?>
	  <?php }else{ ?>
	   <?php foreach($errCode as $c){    
               if($c==-1){ echo '<span class="red"><b>'.__('[Article Source URL] is not set yet','wp-autopost').'</b></span>'; break; }
			   if($c==-2){ echo '<span class="red"><b>'.__('[The Article URL matching rules] is not set yet','wp-autopost').'</b></span>'; break; }
			   if($c==-3){ echo '<span class="red"><b>'.__('[The Article Title Matching Rules] is not set yet','wp-autopost').'</b></span>'; break; }
			   if($c==-4){ echo '<span class="red"><b>'.__('[The Article Content Matching Rules] is not set yet','wp-autopost').'</b></span>'; break; }  
		     } ?>
	  <?php } ?>
      <?php if(($config->last_error)>0){ ?>
         <br/><b><?php echo __('An error occurred','wp-autopost'); ?></b>: <span class="trash"><a class="delete" href="admin.php?page=wp-autopost/wp-autopost-logs.php&taskId=<?php echo $config->id ?>&logId=<?php echo $config->last_error; ?>"><b><?php echo $wpdb->get_var('SELECT info FROM '.$t_f_ap_log.' WHERE id='.$config->last_error); ?></b></a></span> [<a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=ignore&id=<?php echo $config->id; ?>&p=<?php echo $page; ?>"><?php echo __('Ignore','wp-autopost'); ?></a>]
	  <?php } ?>
		 </td>

		 <td>
		   <a href="admin.php?page=wp-autopost/wp-autopost-updatedpost.php&taskId=<?php echo $config->id; ?>&url_status=1"><?php echo $config->updated_num; ?></a>
		 </td>
		 <td>
		<?php if(($config->is_running)==1){ ?>
		  <?php echo __('Is running','wp-autopost'); ?> <img src="<?php echo $wp_autopost_root; ?>images/running.gif" width="15" height="15" style="vertical-align:text-bottom;" />
		  <div class="row-actions-visible">
            <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=abort&id=<?php echo $config->id; ?>"><?php echo __('Abort','wp-autopost'); ?></a>	
		  </div>
		<?php }elseif(($config->activation)==1){ ?>
		  <a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&saction=fetch&id=<?php echo $config->id; ?>"><?php echo __('Update Now','wp-autopost'); ?></a>		
		<?php }else{ ?>
           <?php echo  __('Task deactivated.','wp-autopost'); ?>
		<?php } ?>
		 </td>
       </tr>
     <?php else: ?>
       <tr>
         <td colspan="5" >
		    <strong><?php echo  __('Please add new task.','wp-autopost'); ?></strong>
		    <strong><a href="admin.php?page=wp-autopost/wp-autopost-tasklist.php&createExample=1"><?php echo  __('Or Create an &lt;Example Task> to quick start.','wp-autopost'); ?></a></strong>
		 </td>
	   </tr>
	 <?php endif; ?>
	   </tbody>
	 </table>
	 <div class="tablenav">
      <div class="tablenav-pages alignright">
	   <?php
					if ($total_pages>1) {						
						$arr_params = array (
						  'page' => 'wp-autopost/wp-autopost-tasklist.php',  
						  'p' => "%#%"
						);
						$query_page = add_query_arg( $arr_params , $query_page );				
						echo paginate_links( array(
							'base' => $query_page,
							'prev_text' => __('&laquo; Previous'),
							'next_text' => __('Next &raquo;'),
							'total' => $total_pages,
							'current' => $page,
							'end_size' => 1,
							'mid_size' => 5,
						));
					}
		?>	
       </div> 
	</div>
  </form>
</div>

<?php 
}// end switch($saction){

function createExample(){
  
  global $wpdb;
  global $t_f_ap_cnofig,$t_f_ap_config_ur1_1ist,$t_f_ap_updated_record,$t_f_ap_log,$t_autolink;
  
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  
  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_cnofig'") != $t_f_ap_cnofig)){
    $sql = "CREATE TABLE " . $t_f_ap_cnofig . " (
    id SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
	m_extract TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	activation TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	name CHAR(200) NOT NULL COLLATE 'utf8_unicode_ci',
	page_charset CHAR(30) NOT NULL DEFAULT '0' COLLATE 'utf8_unicode_ci',
	a_match_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	title_match_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
    content_match_type VARCHAR(300) NOT NULL DEFAULT '0',
	a_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	title_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	content_selector VARCHAR(400) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	source_type TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	start_num SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	end_num SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	updated_num MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
	cat VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	author SMALLINT(5) UNSIGNED NULL DEFAULT NULL,
	update_interval SMALLINT(5) UNSIGNED NOT NULL DEFAULT '60',
	published_interval SMALLINT(5) UNSIGNED NOT NULL DEFAULT '60',
	post_scheduled VARCHAR(20)  NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	last_update_time INT(10) UNSIGNED NOT NULL DEFAULT '0',
	post_id INT(10) UNSIGNED NOT NULL DEFAULT '0',
	last_error INT(10) UNSIGNED NOT NULL DEFAULT '0',
	is_running TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	reverse_sort TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	auto_tags CHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
    proxy  CHAR(10) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	post_type VARCHAR(50) NULL DEFAULT 'post' COLLATE 'utf8_unicode_ci',
	post_format VARCHAR(20) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	check_duplicate TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
	err_status TINYINT(3) NOT NULL DEFAULT '1',
	 PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_updated_record'") != $t_f_ap_updated_record)){
    $sql = "CREATE TABLE " . $t_f_ap_updated_record . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id SMALLINT(5) UNSIGNED NOT NULL,
	url VARCHAR(1000) NOT NULL COLLATE 'utf8_unicode_ci',
	title VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8_unicode_ci',
	post_id INT(10) UNSIGNED NOT NULL,
	date_time INT(10) UNSIGNED NOT NULL,
	url_status TINYINT(3) NOT NULL DEFAULT '1',
	PRIMARY KEY (id),
	INDEX url (url),
	INDEX title (title)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_log'") != $t_f_ap_log)){
    $sql = "CREATE TABLE " . $t_f_ap_log . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id INT(10) UNSIGNED NULL DEFAULT NULL,
	date_time INT(10) UNSIGNED NULL DEFAULT NULL,
	info VARCHAR(255) NULL DEFAULT NULL,
	url VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }

  if(($wpdb->get_var("SHOW TABLES LIKE '$t_autolink'") != $t_autolink)){
    $sql = "CREATE TABLE " . $t_autolink . " (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	keyword VARCHAR(50) NOT NULL COLLATE 'utf8_unicode_ci',  
	details VARCHAR(200) NOT NULL COLLATE 'utf8_unicode_ci',
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }


  if(($wpdb->get_var("SHOW TABLES LIKE '$t_f_ap_config_ur1_1ist'") != $t_f_ap_config_ur1_1ist)){
	
	$sql = "CREATE TABLE " . $t_f_ap_config_ur1_1ist . " (
	id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	config_id SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0',
	url CHAR(255) NOT NULL,
	PRIMARY KEY (id)
     ) COLLATE='utf8_unicode_ci' ENGINE=MyISAM";

	 dbDelta($sql);  
  }







  $num = $wpdb->get_var("SELECT count(*) FROM $t_f_ap_cnofig");
  if($num>0)return;

  if(get_bloginfo('language')=='zh-CN'||get_bloginfo('language')=='zh-TW'):   
    $name = '示例任务-作为参考以快速掌握该插件的使用';
	$page_charset = '0';
	$a_selector = '.contList  a';
    $title_selector = '#artibodyTitle';
    $content_selector = '["#artibody"]';
    $a_match_type = 1;
	$title_match_type = 0;
    $content_match_type = '["0,0"]';
	$url = 'http://roll.tech.sina.com.cn/internet_worldlist/index_1.shtml';
  else:
	$name = 'Example Task - As a reference, you can quickly master use of this plugin';
	$page_charset = '0';
	$a_selector = 'http://www.engadget.com/(*)/(*)/(*)/(*)/';
    $title_selector = 'title';
    $content_selector = '[".post-body"]';
    $a_match_type = 0;
	$title_match_type = 0;
    $content_match_type = '["0,0"]';
	$url = 'http://www.engadget.com/';  
  endif;
  
  
  $wpdb->query($wpdb->prepare("insert into $t_f_ap_cnofig(name,page_charset,a_selector,title_selector,content_selector,a_match_type,title_match_type,content_match_type) values (%s,%s,%s,%s,%s,%d,%d,%s)",$name,$page_charset,$a_selector,$title_selector,$content_selector,$a_match_type,$title_match_type,$content_match_type));

  $configId = $wpdb->get_var("SELECT LAST_INSERT_ID()");

  $wpdb->query($wpdb->prepare("insert into $t_f_ap_config_ur1_1ist(config_id,url) values (%d,%s)",$configId,$url));

  echo '<div id="message" class="updated fade"><p>'.__('An Example Task has been created. As a reference, you can quickly master use of this plugin.','wp-autopost').'</p></div>';

}
function checkCanUpdate($config){
  global $wpdb,$t_f_ap_config_ur1_1ist;
  $urls = $wpdb->get_var('SELECT count(*) FROM '.$t_f_ap_config_ur1_1ist.' WHERE config_id ='.$config->id );
  $i=0;
  if($urls==0){ $errCode[$i++]= -1;}
  if(trim($config->a_selector)==''){$errCode[$i++]= -2;}
  if(trim($config->title_selector)==''){$errCode[$i++]= -3;}
  if(trim($config->content_selector)==''){$errCode[$i++]= -4;}
  if($i>0)return $errCode;
  else return 1;
}

function maktimes($time){
 $now = current_time('timestamp');
 if($now >= $time){$t=$now-$time; $s=__(' ago','wp-autopost'); }
 else { $t=$time-$now; $s=__(' after','wp-autopost'); }
 if($t==0)$t=1;
 $f=array(
   '31536000'=> __(' years','wp-autopost'),
   '2592000' => __(' months','wp-autopost'),
   '604800'  => __(' weeks','wp-autopost'),
   '86400'   => __(' days','wp-autopost'),
   '3600'    => __(' hours','wp-autopost'),
   '60'      => __(' minutes','wp-autopost'),
   '1'       => __(' seconds','wp-autopost')
 );
 foreach ($f as $k=>$v){        
  if (0 !=$c=floor($t/(int)$k)){
    return $c.$v.$s;
  }
 }
}

function bulid_lang_options($selected=''){
   $language_code = array (
     'ar'=>'Arabic',
     'bg'=>'Bulgarian',
     'ca'=>'Catalan',
     'zh-CHS'=>'Chinese (Simplified)',
     'zh-CHT'=>'Chinese (Traditional)',
     'cs'=>'Czech',
     'da'=>'Danish',
     'nl'=>'Dutch',
     'en'=>'English',
     'et'=>'Estonian',
     'fa'=>'Persian (Farsi)',
     'fi'=>'Finnish',
     'fr'=>'French',
     'de'=>'German',
     'el'=>'Greek',
     'ht'=>'Haitian Creole',
     'he'=>'Hebrew',
     'hi'=>'Hindi',
     'hu'=>'Hungarian',
     'id'=>'Indonesian',
     'it'=>'Italian',
     'ja'=>'Japanese',
     'ko'=>'Korean',
     'lv'=>'Latvian',
     'lt'=>'Lithuanian',
     'ms'=>'Malay',
     'mww'=>'Hmong Daw',
     'no'=>'Norwegian',
     'pl'=>'Polish',
     'pt'=>'Portuguese',
     'ro'=>'Romanian',
     'ru'=>'Russian',
     'sk'=>'Slovak',
     'sl'=>'Slovenian',
     'es'=>'Spanish',
     'sv'=>'Swedish',
     'th'=>'Thai',
     'tr'=>'Turkish',
     'uk'=>'Ukrainian',
     'ur'=>'Urdu',
     'vi'=>'Vietnamese'
  );
  $options = '';
  foreach ($language_code as $key => $value){
    $options .= '<option value="'.$key.'" '.(($selected==$key)?'selected="true"':'').'>'.$value.'</option>';
  }
  return $options;
}
?>