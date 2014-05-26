<?php
if(function_exists('curl_init')) {
   define('Method',0);
}else{
   define('Method',1);
}
include dirname(__FILE__).'/wp-autopost-hdom.php';


$login_url = $_POST['login_url'];

$html_string = get_html_string_ap($login_url,Method);
$charset = getHtmlCharset($html_string);
$dom = str_get_html_ap($html_string,$charset);

$inputs = $dom->find('form input[name]');

if($inputs!=null):
  echo '<table id="login_para_table">';
  echo '<tr><th>Parameter Name</th><th>Parameter Value</th></tr>';
  
  $num=0;
  $nameArray = array();
  foreach($inputs as $input){
    if(!in_array($input->name,$nameArray)){
	  $num++;
	  $nameArray[] = $input->name;
	  $value = '';
	  if($input->value!=null&&$input->value!=''){
        $value = $input->value;
	  }
	  if($input->type == 'checkbox'){
        if($input->checked == 'checked' || $input->checked == 'true'){
          $value='on';
		}
	  }
	  echo '<tr id="login_para_table_tr'.$num.'" >';
      echo   '<td><input type="text" name="loginParaName[]"  value="'.$input->name.'" /></td>';
      echo   '<td><input type="text" name="loginParaValue[]" value="'.$value.'" /></td>';
	  echo   '<td><input type="button" class="button" value="Delete"  onclick="deleteLoginPara(\'login_para_table_tr'.$num.'\')" /></td>';
	  echo '</tr>';
	}
  }
  echo '</table>';
  echo '<input type="hidden" name="login_para_tableTRLastIndex" id="login_para_tableTRLastIndex"  value="'.($num+1).'" />';
else:
  $num=1;
  echo '<table id="login_para_table">';
  echo '<tr><th>Parameter Name</th><th>Parameter Value</th></tr>';
  echo '<tr id="login_para_table_tr'.$num.'" >';
  echo   '<td><input type="text" name="loginParaName[]"  value="" /></td>';
  echo   '<td><input type="text" name="loginParaValue[]" value="" /></td>';
  echo   '<td><input type="button" class="button" value="Delete"  onclick="deleteLoginPara(\'login_para_table_tr'.$num.'\')" /></td>';
  echo '</tr>';
  echo '</table>';
  echo '<input type="hidden" name="login_para_tableTRLastIndex" id="login_para_tableTRLastIndex"  value="'.($num+1).'" />';
endif; // end if($inputs!=null):