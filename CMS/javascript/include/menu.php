<?php
require_once __DIR__ . '/../../classes/Services/MenuService.php';

use App\Classes\Services\MenuService;

$isLoggedIn = isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true;
$role = isset($_SESSION['tablica'][5]) ? $_SESSION['tablica'][5] : '';

$menuService = new MenuService();
$menuItems = $menuService->getMenuItems($isLoggedIn, $role);
?>
<div class="przerwa">
  <a href="index.php" class="link"><p id="title">IMAGE SPACE</p></a>
</div>


<div class="hamburger" onclick="toggleMenu()">
  <span></span>
  <span></span>
  <span></span>
</div>

<div class="nav" id="navMenu">
  <ol>
    <?php foreach ($menuItems as $item): ?>
      <?php if ($item['visible']): ?>
        <li id="<?= $item['id'] ?>">
          <a href="<?= $item['href'] ?>"><?= $item['label'] ?></a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>
</div>
<script>
function toggleMenu() {
  var nav = document.getElementById("navMenu");
  var hamburger = document.querySelector(".hamburger");

  nav.classList.toggle("menu-open");
  hamburger.classList.toggle("menu-open");
}

</script>



