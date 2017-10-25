<div id="lateralHomeStudente">
    <div id="logoHomeStudente">
        <a href="home-admin.php">
            <!-- il logo prende l'intera grandezza del div logo stabilito dai css -->
            <img src="images/logoHome.png" style="width: 100%;"/>
        </a>
    </div>
    <!-- Il link del logout si comporta come i precedenti ma si trova in un punto differente quindi bisogna assegnargli
    uno stile interno particolare -->
	<div id="navigationUser" style="top: 85%; height: 40px;">
		<div id="user">
			<img src="images/iconUtente.png"><?= $_SESSION['email']; ?>
        </div>
    </div>
    <div id="navigation" style="top: 85%; height: 40px;">
        <a href="logout.php"><img src="images/iconLogout.png">Logout</a>
    </div>
</div>