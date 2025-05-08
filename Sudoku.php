<!DOCTYPE html>
<html lang="pl">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
      background-color: transparent;
      border-radius: 10px;
      text-align: center;
      align-items: center;
      width: 100%;
    }
    .dark {
      background: #121212;
      color: #eee;
    }

    .sudoku {
      display: grid;
      grid-template-columns: repeat(9, 40px);
      grid-template-rows: repeat(9, 40px);
      gap: 1px;
      background: #000;
      margin-bottom: 20px;
    }

    input {
      width: 38px;
      height: 38px;
      text-align: center;
      font-size: 18px;
      border: none;
      transition: background 0.2s;
    }

    input:focus {
      outline: 2px solid #4CAF50;
    }

    input:disabled {
      background-color: #ddd;
    }

    .dark input:disabled {
      background-color: #444;
      color: #fff;
    }

    button, label {
      margin: 5px;
      padding: 8px 12px;
      font-size: 14px;
      cursor: pointer;
    }

    .info {
      margin-top: 10px;
      font-weight: bold;
    }

    .controls {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: center;
      margin-bottom: 10px;
    }

    .timer {
      font-size: 16px;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<?php 
if (isset($_GET['email'])) { 
    $email = $_GET['email']; 
} 
?> 

  <h2>🧩 Sudoku</h2>

  <div class="controls">
    <button onclick="checkSolution()">✅ Sprawdź</button>
    <button onclick="resetBoard()">🔄 Resetuj</button>
    <button onclick="loadRandomPuzzle()">🎲 Gotowe wzory plansz</button>
    <button onclick="loadGeneratedPuzzle()">🤖 Wygeneruj planszę</button>
    <label>
      <input type="checkbox" onchange="toggleDarkMode()"> 🌙 Tryb nocny
    </label>
  <form class="formularz" action="Sudoku.php" method="get"> 
  <input type="text" id="czas_grania" name="czas_grania" style="width: 100px;display: none;"/>
  <input type="email" id="email" name="email" value="<?php echo $email; ?>" style="width: 100px;display: none;"/>
  <button type="submit" id="zapisz" name="zapisz"> <i class="fas fa-save"></i> Zapisz wynik</button>
  </form>
  </div>

  <div class="sudoku" id="board"></div>
  <div class="timer" id="timer">⏱️ Czas: 0:00</div>
  <div class="info" id="message"></div>
  
  <script>
    const puzzles = [
      [
        [3, 8, 1, '', 5, '', 4, 6, 7],
        ['', '', '', '', '', '', '', '', ''],
        [7, '', '', 4, 8, 3, '','', 5],
        ['', '', 3, '', 9, '', 7, '', ''],
        [8, '', 6, 2, '', 7, 1, '', 4],
        ['', '', 4, '', 1, '', 6, '', ''],
        [5, '', '', 1, 2, 6, '', '', 3],
        ['', '', '', '', '', '', '', '', ''],
        [1, 9, 2, '', 7, '', 8, 4, 6]
      ],
	  [
        [3, 8, 1, 9, 5, 2, 4, 6, 7],
        [4, 2, 5, 7, 6, 1, 3, 8, 9],
        [7, 6, 9, 4, 8, 3, 2, 1, 5],
        [2, 1, 3, 6, 9, 4, 7, 5, 8],
        [8, 5, 6, 2, '', 7, 1, 9, 4],
        [9, 7, 4, 5, 1, 8, 6, 3, 2],
        [5, 4, 8, 1, 2, 6, 9, 7, 3],
        [6, 3, 7, 8, 4, 9, 5, 2, 1],
        [1, 9, 2, 3, 7, 5, 8, 4, 6]
      ],
      [
        ['', '', 4, '', '', '', 8, '', ''],
        [7, '', '', 2, '', '', '', '', 9],
        ['', '', '', '', 5, '', '', '', 3],
        ['', '', '', '', '', 6, 4, '', ''],
        [5, '', 8, '', '', '', 2, '', 1],
        ['', '', 2, 3, '', '', '', '', ''],
        [6, '', '', '', 1, '', '', '', ''],
        [1, '', '', '', '', 8, '', '', 7],
        ['', '', 5, '', '', '', 1, '', '']
      ]
    ];

    let originalPuzzle = [];
    let timerSeconds = 0;
    let timerInterval;

    function renderBoard(puzzle) {
      const board = document.getElementById("board");
      board.innerHTML = '';
      originalPuzzle = JSON.parse(JSON.stringify(puzzle));

      puzzle.forEach((row, r) => {
        row.forEach((cell, c) => {
          const input = document.createElement("input");
          input.maxLength = 1;
          input.dataset.row = r;
          input.dataset.col = c;
          if (cell !== '') {
            input.value = cell;
            input.disabled = true;
          } else {
            input.addEventListener("input", (e) => {
              const val = e.target.value;
              if (!/^[1-9]$/.test(val)) e.target.value = '';
            });
          }
          board.appendChild(input);
        });
      });

      resetTimer();
    }

    function getBoardState() {
      const inputs = document.querySelectorAll("#board input");
      const state = Array.from({ length: 9 }, () => Array(9).fill(''));
      inputs.forEach(input => {
        const r = input.dataset.row;
        const c = input.dataset.col;
        state[r][c] = input.value;
      });
      return state;
    }

    function checkSolution() {
      const state = getBoardState();
      const msg = document.getElementById("message");

      for (let i = 0; i < 9; i++) {
        const row = new Set();
        const col = new Set();
        const box = new Set();
        for (let j = 0; j < 9; j++) {
          const r = state[i][j];
          const c = state[j][i];
          const br = 3 * Math.floor(i / 3) + Math.floor(j / 3);
          const bc = 3 * (i % 3) + (j % 3);
          const b = state[br][bc];

          if (!r || !c || !b) {
            msg.textContent = "❗ Uzupełnij wszystkie pola.";
            return;
          }

          if (row.has(r) || col.has(c) || box.has(b)) {
            msg.textContent = "❌ Coś się nie zgadza!";
            return;
          }

          row.add(r);
          col.add(c);
          box.add(b);
        }
      }

      msg.textContent = "✅ Gratulacje! Rozwiązanie poprawne!";
    }

    function resetBoard() {
      renderBoard(originalPuzzle);
      document.getElementById("message").textContent = '';
    }

    function loadRandomPuzzle() {
      const random = puzzles[Math.floor(Math.random() * puzzles.length)];
      renderBoard(random);
      document.getElementById("message").textContent = '';
    }

    function toggleDarkMode() {
      document.body.classList.toggle("dark");
    }

    function updateTimer() {
      timerSeconds++;
      const min = Math.floor(timerSeconds / 60);
      const sec = timerSeconds % 60;
      document.getElementById("timer").textContent = `⏱️ Czas: ${min}:${sec < 10 ? '0' : ''}${sec}`;
      document.getElementById("czas_grania").value = `${min}:${sec < 10 ? '0' : ''}${sec}`;
    }

    function resetTimer() {
      clearInterval(timerInterval);
      timerSeconds = 0;
      updateTimer();
      timerInterval = setInterval(updateTimer, 1000);
    }

    // Start with first puzzle
    renderBoard(puzzles[0]);
  </script>
  
<?php
		$connection = mysqli_connect('projektyjk.cba.pl:3306', 'ugabuga', 'Ugabuga1');
		$db = mysqli_select_db($connection, 'nietoper');
		
				if(isset($_GET["zapisz"]))
				{
					$sql = "SELECT * FROM uzytkownik WHERE email='".$email."'";
					$wynik = mysqli_query($connection, $sql);
					if (mysqli_num_rows($wynik) > 0) {
						$czas_grania = $_GET["czas_grania"];
						$sql = "UPDATE uzytkownik SET czas_ostatniej_gry='".$czas_grania."' WHERE email='".$email."'";
						$wynik=mysqli_query($connection, $sql);
						$url = "http:///projektyjk.cba.pl/koniec.php?email=". urlencode($email);
						header('Location: '.$url);
						exit;
					}
					mysqli_close($connection);
				}

?>

</body>
</html>

<script>
  // ==== GENEROWANIE PEŁNEJ PLANSZY ====
  function generateFullBoard() {
    const board = Array.from({ length: 9 }, () => Array(9).fill(''));

    function isValid(row, col, num) {
      for (let i = 0; i < 9; i++) {
        if (board[row][i] == num || board[i][col] == num) return false;
        const br = 3 * Math.floor(row / 3) + Math.floor(i / 3);
        const bc = 3 * Math.floor(col / 3) + (i % 3);
        if (board[br][bc] == num) return false;
      }
      return true;
    }

    function solve(pos = 0) {
      if (pos >= 81) return true;
      const row = Math.floor(pos / 9);
      const col = pos % 9;

      const nums = [...Array(9).keys()].map(x => x + 1).sort(() => Math.random() - 0.5);
      for (let num of nums) {
        if (isValid(row, col, num)) {
          board[row][col] = num;
          if (solve(pos + 1)) return true;
          board[row][col] = '';
        }
      }
      return false;
    }

    solve();
    return board;
  }

  // ==== WERSJA Z UKRYCIEM CZĘŚCI POL - TRUDNOŚĆ ====
  function generatePuzzle(difficulty = 'medium') {
    const full = generateFullBoard();
    const clues = difficulty === 'easy' ? 40 : difficulty === 'hard' ? 25 : 32;

    const puzzle = full.map(row => row.slice());
    let removed = 81 - clues;
    while (removed > 0) {
      const r = Math.floor(Math.random() * 9);
      const c = Math.floor(Math.random() * 9);
      if (puzzle[r][c] !== '') {
        puzzle[r][c] = '';
        removed--;
      }
    }
    return puzzle;
  }

  function loadGeneratedPuzzle() {
    const puzzle = generatePuzzle('medium');
    renderBoard(puzzle);
    document.getElementById("message").textContent = '🧠 Wygenerowano planszę';
  }

  // ==== ZAPIS DO localStorage ====
  function saveWinToLocalStorage(seconds) {
    const record = {
      time: seconds,
      date: new Date().toLocaleString()
    };
    const data = JSON.parse(localStorage.getItem('sudokuResults') || '[]');
    data.push(record);
    localStorage.setItem('sudokuResults', JSON.stringify(data));
  }

  function showLastResults() {
    const data = JSON.parse(localStorage.getItem('sudokuResults') || '[]');
    if (data.length > 0) {
      const latest = data.slice(-3).map(d => `🕒 ${d.time}s - ${d.date}`).join('\n');
      alert("📜 Ostatnie wyniki:\n" + latest);
    }
  }

  // ==== ROZSZERZENIE checkSolution() ====
  function checkSolution() {
    const state = getBoardState();
    const msg = document.getElementById("message");

    for (let i = 0; i < 9; i++) {
      const row = new Set();
      const col = new Set();
      const box = new Set();
      for (let j = 0; j < 9; j++) {
        const r = state[i][j];
        const c = state[j][i];
        const br = 3 * Math.floor(i / 3) + Math.floor(j / 3);
        const bc = 3 * (i % 3) + (j % 3);
        const b = state[br][bc];

        if (!r || !c || !b) {
          msg.textContent = "❗ Uzupełnij wszystkie pola.";
          return;
        }

        if (row.has(r) || col.has(c) || box.has(b)) {
          msg.textContent = "❌ Coś się nie zgadza!";
          return;
        }

        row.add(r);
        col.add(c);
        box.add(b);
      }
    }

    msg.textContent = "✅ Gratulacje! Rozwiązanie poprawne!";
    clearInterval(timerInterval);
    saveWinToLocalStorage(timerSeconds);
  }

  // ==== Wywołaj na starcie ====
  showLastResults();
</script>
