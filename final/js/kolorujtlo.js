// Funkcja zmieniająca tło zawartości strony (elementu .content)
function changeBackground(hexNumber) {
  // Szukamy elementu z klasą 'content'
  var content = document.querySelector('.content'); // Możemy także użyć getElementById, jeśli masz ID
  
  // Zmieniamy tło elementu .content na podany kolor
  if (content) {
    content.style.backgroundColor = hexNumber;
  }
}

