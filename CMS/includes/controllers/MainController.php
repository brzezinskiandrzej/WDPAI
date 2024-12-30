<?php
require_once 'includes/views/Header.php';
require_once 'includes/views/Menu.php';
require_once 'includes/views/IndexContent.php';
require_once 'includes/views/Footer.php';

class MainController {
    public function index() {
        Header::display();
        echo '<div id="container"><div id="main">';
        Menu::display();
        IndexContent::display();
        echo '</div></div>';
        $this->loadLoader();
        Footer::display();
    }

    private function loadLoader() {
        echo '<div class="loader-wrapper">
                <span class="loader"><span class="loader-inner"></span></span>
              </div>';
        echo '<script src="javascript/jquery-3.6.0.min.js"></script>';
        echo '<script src="javascript/include/footer.js"></script>';
        echo '<script src="javascript/include/menunagorze.js"></script>';
        echo '<script>
                $(window).on("load", function () {
                    $(".loader-wrapper").fadeOut(3000, function () {
                        document.body.style.overflow = "unset";
                    });
                });
              </script>';
    }
}
?>