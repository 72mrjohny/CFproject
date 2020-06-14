<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>CrossFinance project</title>
</head>

<body>

  <h1>Lista Klientów</h1>

  <form action="upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="file" />
    <input type="submit" name="submit" value="Upload" /></form>



  <?php

  echo "<table border='1'>
<tr>
<th>imie_nazwisko</th>
<th>pesel/nip</th>
</tr>";

  // while($row = mysqli_fetch_array($result))
  // {
  // echo "<tr>";
  // echo "<td>" . $row['imie_nazwisko'] . "</td>";
  // echo "<td>" . $row['pesel/nip'] . "</td>";
  // echo "</tr>";
  // }
  echo "</table>";

  ?>




</body>

</html>