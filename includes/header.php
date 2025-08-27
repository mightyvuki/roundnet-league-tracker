<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php"><img src="images/logo.png" alt="Roundnet League Lovcenac Logo"></a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Početna</a></li>
            <li><a href="leagues.php">Pregled liga</a></li>
            <li><a href="dashboard.php" class="btn-edit">
                <?php 
                    if(!isset($_SESSION['user'])) echo "Moji mečevi";
                    else 
                        echo ($_SESSION['user']['uloga'] == 'user' ? "Moji mečevi" : "Kontrolna tabla");
                ?>
            </a></li>
        </ul>
        <div class="login">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-info">
                    <div class="user-trigger">
                        <img src="<?php echo $_SESSION['user']['profile_pic']; ?>" 
                            alt="Profilna slika" class="avatar">
                        <span><?php echo htmlspecialchars($_SESSION['user']['ime']); ?></span>
                    </div>
                    <div class="dropdown-menu">
                        <a href="edit_profile.php" class="btn-edit">Izmeni profil</a>
                        <a href="logout.php" class="btn-logout">Odjava</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn-login-header">Prijava</a>
            <?php endif; ?>
        </div>
    </nav>
</header>