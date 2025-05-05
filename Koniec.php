<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Sudoku</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #f7f7f7;
      margin: 0;
      padding: 20px;
      transition: background 0.3s, color 0.3s;
    }
    .formularz {
      background: #f7f7f7;
      padding: 30px;
      border-radius: 10px;
      width: 100%;
      max-width: 368px;
      text-align: center;
      align-items: center;
    }

    .bt_zagrac {
      width: 100%;
      padding: 12px;
      background-color: #3498db;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

  </style>
</head>
<body>

<?php 
if (isset($_GET['email'])) { 
    $email = $_GET['email']; 
} 
?> 

  <h2>Dane zostały pomyślnie zapisane do bazy</h2>

  <form class="formularz" action="Sudoku.php" method="get"> 
  <input type="email" id="email" name="email" value="<?php echo $email; ?>" style="width: 100px;display: none;"/>
  <button type="submit" name="zagrac" class="bt_zagrac" >Chcesz zagrać jeszcze raz?</button>
  </form>
  
<?php
	if(isset($_GET["zagrac"]))
	{
		$url = "http:///projektyjk.cba.pl/Sudoku.php?email=". urlencode($email);
		header('Location: '.$url);
		exit;
	}
?>

</body>
</html>
