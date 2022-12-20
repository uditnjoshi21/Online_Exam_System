<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Navigation</title>
    <link rel="stylesheet" type="text/css" href="common.css" />
</head>

<body>
    <nav>
        <img style="height: 170px;" src="images\collage_logo.png" alt="Logo">
        <form method=post action=3.instructor_homepage.php>
            <p> <input type="submit" value="Home" name="Home"> </p>
        </form>
        <form method=post action=3.instructor_homepage.php>
            <p align="center"> <input type="submit" value="Profile" name="profile"> </p>
        </form>
        <form method=post action=2.reset_password.php>
            <p align="center"> <input type="submit" value="Update Password" name="update_paswd"> </p>
        </form>
        <form method=post action=1.login.php>
            <p align="center"> <input type="submit" value="Logout" name="logout_name"> </p>
        </form>
    </nav>



    <main>
        <header>
            <img style="height: 30px; width: 200px;" src="images\header_logo.png" alt="Logo">
            <div class="header_container">
                <strong >Welcome Udit Joshi</strong>
            </div>
        </header>
        <div class="main_container">

            <h1>Navigation page</h1>
        </div>
    </main>
</body>

</html>