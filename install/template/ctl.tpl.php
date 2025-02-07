<script>
  let installFinished = false;
  function showButton() {
    if (!installFinished) {
      const myDiv = parent.mainframe.document.getElementById('installButton');
      myDiv.style.visibility = 'visible';
      myDiv.innerHTML = '<input type="button" value="Click HERE to start installation" onclick="parent.ctl.startInstallProc()" />';
    }
  }

  function startInstallProc() {
    const myDiv = parent.mainframe.document.getElementById('installButton');
    myDiv.innerHTML = '';
    myDiv.style.visibility = 'hidden';
    setTimeout(doStep(), 500);
    return false;
  }

  function doStep(step, inProgress) {
    if (!installFinished) {
      if (typeof step === 'undefined') {
        step = 0;
      }
      const myDiv = parent.mainframe.document.getElementById('installSteps');
      
      if (typeof inProgress !== "boolean" || !inProgress) {
        switch (step) {
          case  0   :   // Display status
                        myDiv.style.visibility = 'visible';
                        myDiv.innerHTML = '<b>Store data...</b>';
                        break;
          case  1   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<b>Create database structure...</b>';
                        break;
          case  2   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<b>Install data...</b>';
                        break;
          case  3   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<b>Import stored data...</b>';
                        break;
          case  4   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<b>Update user accounts...</b>';
                        break;
          case  5   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<b>Cleanup...</b>';
                        break;
          case  6   :   // Empty step
          case  7   :   // Empty step
                        break;
          case  8   :   // Display status
                        myDiv.innerHTML += '<b>done!</b><br />';
                        myDiv.innerHTML += '<br />';
                        myDiv.innerHTML += '<b><span style="color:#008800;">Installation complete!</span></b>';
                        myDiv.innerHTML += '<br /><br />';
                        myDiv.innerHTML += '&nbsp;&nbsp;<b><span style="color:#EF0000;">IMPORTANT: Please delete directory &quot;install&quot; !!!</span></b>&nbsp;&nbsp;';
                        myDiv.innerHTML += '<br /><br />';
                        myDiv.innerHTML += '<input type="button" value="Start the chat" onclick="window.parent.document.location.href=\'./\'" />';
                        myDiv.innerHTML += '<br /><br />';
                        installFinished = true;
                        break;
        }
      } else {
        myDiv.innerHTML += '<b>.</b>';
      }

      if (!installFinished) {
        const myForm = document.getElementById('ctlForm');
        if (myForm) {
          myForm.step.value = step;
          myForm.submit();
        }
      }
    } else {
      alert('Installation complete!');
    }
  }
</script>
<form id="ctlForm" action="./install.php" method="post">
  <input type="hidden" name="framed" value="1" />
  <input type="hidden" name="include" value="10" />
  <input type="hidden" name="step" value="" />
  <input type="hidden" name="timestamp" value="<?php echo md5(microtime())?>" />
</form>