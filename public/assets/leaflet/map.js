let latitude = document.getElementById("latitude").value;
let longitude = document.getElementById("longitude").value;
let arrayCoordinates = [latitude,longitude];

let map = L.map('map').setView(arrayCoordinates, 15);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
let marker = L.marker(arrayCoordinates).addTo(map);