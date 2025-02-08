<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/xml; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="<?php echo htmlspecialchars(PCPIN_INSTALL_TEMPLATES, ENT_QUOTES, 'UTF-8'); ?>/install.css" />
  <script type="text/javascript">
    function doSubmit(param){
      var myForm=document.getElementById('installform');
      if(typeof(param)!='string'){
        param='';
      }
      var argv=param.split('&');
      var arg=null;
      var p_name='';
      var p_value='';
      var element_found=false;
      for(var i=0; i<argv.length; i++){
        arg=argv[i].split('=');
        p_name=arg[0];
        p_value=arg[1];
        element_found=typeof(myForm[p_name])!='undefined';
        if(element_found){
          myForm[p_name].value=p_value;
        }
      }
      myForm.submit();
    }
  </script>
</head>
<body onload="<?php echo htmlspecialchars($_body_onload, ENT_QUOTES, 'UTF-8'); ?>">
<?php echo $_progress; ?>
<?php echo $_contents; ?>
</body>
</html>