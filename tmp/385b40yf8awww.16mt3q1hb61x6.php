<!--This is the home page. It shows a welcome message and one sample post with a link to more posts-->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Green River Messaging</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Style sheets-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/main.css">
    <link type="text/css" rel="stylesheet" href="/355/grmessage/styles/login.css">

</head>
<body>

    <header class="header clearfix" id="mainHeader">
        <a href=""><h1>Green River Messaging</h1></a>
    </header>

    <div class="container">

        <div class="row">
            <p class="alert alert-warning text-dark my-3" role="alert">
                This is not an official Green River service. This is a project by IT students to provide a messaging
                service for Green River College students and instructors.
            </p>

            <div class="col-4 mx-auto">
                <form method="post" class="form mx-auto">
                    <div class="row mx-auto">
                        <label class="form-control-label mx-auto" for="email">Green River Email </label>
                        <input class="form-control" type="text" id="email" name="email">
                    </div>

                    <div class="row mx-auto">
                        <label class="form-control-label mx-auto" for="password">Password </label>
                        <input class="form-control" type="password" id="password" name="password">
                    </div>

                    <div class="row align-bottom mx-auto my-2">
                        <button type="submit" name="login">Login</button>
                        <button id="register"><a id="none" href="register">Not a member? Register</a></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>