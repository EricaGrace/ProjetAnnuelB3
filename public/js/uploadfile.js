 // Afficher le nom du fichier telecharge par l'utilisateur
 // https://dev.to/ibn_abubakre/styling-file-inputs-like-a-boss-mhm
 "use strict";
 const file = document.querySelector('#file');
 file.addEventListener('change', (e) => {
   const file = e.target.files[0];
   document.querySelector('.file-name').textContent = file.name;
 });