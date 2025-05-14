<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl-pl" lang="pl-pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rejestracja Użytkownika</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      display:flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .formularz {
      background-color: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }

    .formularz h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    .formularz-pola {
      margin-bottom: 15px;
    }

    .formularz-pola label {
      display: block;
      margin-bottom: 5px;
      color: #555;
    }

    .formularz-pola input,
    .formularz-pola select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .bt_rejestracja {
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

    .bt_rejestracja:hover {
      background-color: #2980b9;
    }
    .alert {
      padding: 20px;
      background-color: #f44336;
      color: white;
      text-align: center;
    }

  </style>
</head>
<body>
  <form class="formularz" action="Rejestracja_testy.php" method="get"> 
    <h2>Rejestracja do gry</h2>

    <div class="formularz-pola">
      <label for="Nick">Nick (*)</label>
      <input type="text" id="Nick" name="nick" required 
        oninvalid="this.setCustomValidity('Podaj swój nick')"
        oninput="this.setCustomValidity('')" />
    </div>

    <div class="formularz-pola">
      <label for="email">E-mail (*)</label>
      <input type="email" id="email" name="email" required 
        oninvalid="this.setCustomValidity('Podaj prawidłowy adres e-mail')"
        oninput="this.setCustomValidity('')" />
    </div>

    <div class="formularz-pola">
      <label for="Wiek">Wiek(*)</label>
      <input type="number" id="Wiek" name="Wiek" required 
        oninvalid="this.setCustomValidity('Podaj swój wiek')"
        oninput="this.setCustomValidity('')" />
    </div>

    <div class="formularz-pola">
      <label for="plec">Płeć</label>
      <select id="plec" name="plec" required
        oninvalid="this.setCustomValidity('Wybierz swoją płeć')"
        oninput="this.setCustomValidity('')">
        <option value="" disabled selected hidden>Wybierz płeć</option>
        <option value="kobieta">Kobieta</option>
        <option value="mężczyzna">Mężczyzna</option>
        <option value="inna">Inna</option>
        <option value="nie-podano">Nie chcę podawać</option>
      </select>
    </div>

    <div class="formularz-pola">
      <label>(*) - pola wymagane</label>
    </div>

    <button type="submit" name="zapisz" class="bt_rejestracja">Zarejestruj się</button>
  </form>
  
<?php

	if(isset($_GET["zapisz"]))
	{

	$config = require __DIR__ . '/./config/db_config.php';

	$connection = mysqli_connect($config['host'], $config['user'], $config['pass']);
	$db = mysqli_select_db($connection, $config['dbname']);

		$nick = $_GET["nick"];
		$email = $_GET["email"];
		$wiek = $_GET["wiek"];
		$plec = $_GET["plec"];
		
		$_SESSION['email'] = $email;
		
		$sql = "SELECT * FROM uzytkownik WHERE email='".$email."'";
		$wynik = mysqli_query($connection, $sql);
		if (mysqli_num_rows($wynik) > 0) {
			echo("<div class='alert'>");
			echo("<strong>Błąd!</strong> użytkownik o podanym adresie email jest juz zarejestrowany.");
			echo("</br>");
			echo("</br>");
			echo("Czy chcesz zagrać jako użytkownik <a href='Sudoku_testy.php?email=".urlencode($email)."'>".$email."</a>?");
			echo("</div>");
		} else {
			$sql = "insert into uzytkownik(nick, email, wiek, plec) values ('$nick', '$email', '$wiek', '$plec')"; 
			$wynik=mysqli_query($connection, $sql);
			mysqli_close($connection);
			$url = "http:///projektyjk.cba.pl/Sudoku_testy.php?email=". urlencode($email);
			header('Location: '.$url);
			exit;
		}
	mysqli_close($connection);
	}
	
?>

</body>
</html>
