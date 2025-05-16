// kamishima-reserve/assets/js/app.js
import 'bootstrap';
import '../css/app.scss';
import 'flatpickr/dist/flatpickr.min.css';
import './flatpickr-custom.js';
//import './components/flatpickr-init';
console.log('Vite app loaded!');

// レスポンシブナビゲーションのための簡単なトグル (必要であれば)
// 例: ハンバーガーメニューなど
// document.addEventListener('DOMContentLoaded', () => {
//   const navToggle = document.getElementById('nav-toggle'); // HTMLに <button id="nav-toggle">MENU</button> がある想定
//   const adminNav = document.querySelector('.admin-navigation nav ul');
//   if (navToggle && adminNav) {
//     navToggle.addEventListener('click', () => {
//       adminNav.classList.toggle('active'); // .activeクラスで表示・非表示をCSSで制御
//     });
//   }
// });