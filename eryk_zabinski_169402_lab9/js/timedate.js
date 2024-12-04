// Funkcja gettheDate - uzyskuje dzisiejszą datę i wyświetla ją w odpowiednim formacie
function gettheDate()
{
  // Pobiera dzisiejszą datę
  Todays = new Date();
  
  // Formatuje datę w formacie "miesiąc / rok"
  TheDate = "" + (Todays.getMonth()+1) + " / " +(Todays.getYear()-100);
  
  // Wyświetla datę w elemencie o id "data"
  document.getElementById("data").innerHTML = TheDate;
}

// Inicjalizacja zmiennych związanych z zegarem
var timerID = null; // Identyfikator timera
var timerRunning = false; // Flaga wskazująca, czy zegar jest uruchomiony

// Funkcja stopclock - zatrzymuje zegar
function stopclock()
{
  // Jeśli zegar jest uruchomiony, zatrzymuje go
  if(timerRunning)
    clearTimeout(timerID);
  
  // Zmiana stanu zegara na zatrzymany
  timerRunning = false;
}

// Funkcja startclock - uruchamia zegar
function startclock()
{
  // Zatrzymuje ewentualnie uruchomiony zegar przed ponownym rozpoczęciem
  stopclock();
  
  // Wywołanie funkcji gettheDate, która aktualizuje datę
  gettheDate();
  
  // Wywołanie funkcji showtime, która uruchamia zegar
  showtime();
}

// Funkcja showtime - wyświetla aktualny czas w formacie godzina: minuta: sekunda
function showtime()
{
  // Pobiera aktualną datę i godzinę
  var now = new Date();
  var hours = now.getHours(); // Pobiera godziny
  var minutes = now.getMinutes(); // Pobiera minuty
  var seconds = now.getSeconds(); // Pobiera sekundy
  
  // Formatuje godzinę na 12-godzinny format
  var timeValue = "" + ((hours>12) ? hours -12 :hours);
  
  // Dodaje minuty do godziny, zapewniając odpowiednie formatowanie
  timeValue += ((minutes < 10) ? ":0" : ":") + minutes;
  
  // Dodaje sekundy do czasu, zapewniając odpowiednie formatowanie
  timeValue += ((seconds < 10) ? ":0" : ":") + seconds;
  
  // Dodaje oznaczenie AM/PM w zależności od godziny
  timeValue += (hours >= 12) ? " P.M." : " A.M.";
  
  // Wyświetla czas w elemencie o id "zegarek"
  document.getElementById("zegarek").innerHTML = timeValue;
  
  // Ustawia funkcję showtime, aby była wywoływana co sekundę
  timerID = setTimeout("showtime()", 1000);
  
  // Flaga wskazująca, że zegar jest uruchomiony
  timerRunning = true;
}

// Uruchamia zegar po załadowaniu strony
window.onload = startclock;
