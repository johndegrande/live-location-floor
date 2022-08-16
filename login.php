<?php $page_title = "login";
require("scripts/header_mobile.php"); ?>
<script type="text/javascript">
$(function() {
  $("#uname").focus();
});
</script>
<p>
<form action="index.php" id="login_form" name="login_form" method="post" data-ajax="false" enctype="multipart/form-data">
     <label for="uname">Username:</label>
     <input name="uname" id="uname" type="text" size="20" maxlength="32" value="<?php if (isset($_GET['uname'])) { echo $_GET['uname']; } ?>" />
     <label for="upass">Password:</label>
     <input name="upass" type="password" size="20" maxlength="32" />
     <input name="submit" type="submit" value="Submit" id="submit" />
</form>
</p>
<?php require("scripts/footer_mobile.php"); ?>