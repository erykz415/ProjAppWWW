// =============================
// FUNKCJA: Zmiana tła zawartości strony (elementu .content)
// =============================
function changeBackground(hexNumber) {
  
  // Szukamy elementu z klasą 'content'
  // Używamy metody querySelector, aby znaleźć element o klasie 'content'
  // Możemy także użyć getElementById, jeśli element posiada unikalne ID
  var content = document.querySelector('.content'); 

  // Sprawdzamy, czy element z klasą 'content' istnieje
  // Jeśli istnieje, zmieniamy jego tło na wartość podaną w parametrze 'hexNumber'
  if (content) {
    content.style.backgroundColor = hexNumber;
  }
}

