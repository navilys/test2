<html>
<head>
<title>PM In Outlook Plugin - Autopilot Test</title>
</head>
<body>
<form method="post" action="services/rest">
<table style="width:60%;margin-left:auto;margin-right:auto;border: solid 1px #000000;">
  <tr>
    <th colspan="2" style="width:100%;text-align:center;">PM In Outlook Plugin - Autopilot Test</th>
  </tr>
  <tr style="display:none;">
    <td colspan="2">
      <input type="hidden" name="method" value="autopilot" />
    </td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">userUID</td>
    <td style="width:70%;text-align:left;"><input type="text" name="userUID" size="35" value="00000000000000000000000000000001" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">task</td>
    <td style="width:70%;text-align:left;"><input type="text" name="task" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">from</td>
    <td style="width:70%;text-align:left;"><input type="text" name="from" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">to</td>
    <td style="width:70%;text-align:left;"><input type="text" name="to" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">cc</td>
    <td style="width:70%;text-align:left;"><input type="text" name="cc" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">bcc</td>
    <td style="width:70%;text-align:left;"><input type="text" name="bcc" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">subject</td>
    <td style="width:70%;text-align:left;"><input type="text" name="subject" size="35" value="" /></td>
  </tr>
  <tr>
    <td style="width:30%;text-align:right;">body</td>
    <td style="width:70%;text-align:left;"><textarea name="body" cols="27" rows="5"></textarea></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align:center;">
      <input type="submit" value=" Send " />
    </td>
  </tr>
</table>
</form>
</body>
</html>