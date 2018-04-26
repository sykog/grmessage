<!--This is the home page. It shows a welcome message and one sample post with a link to more posts-->

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Green River Messaging</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--Style sheets-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css" integrity="sha384-Zug+QiDoJOrZ5t4lssLdxGhVrurbmBWopoEl+M6BdEfwnCJZtKxi1KgxUyJq13dy" crossorigin="anonymous">
</head>
<body>
<div class="container">

    <div class="row">
        <h1>Green River Messaging</h1>

        <p class="alert alert-success" role="alert">
            This is not an official Green River service. This is a project by IT students to provide a messaging
            service for Green River College students and instructors.
        </p>
    </div>
    <div class="row">
        <form method="post" class="mx-auto">
            <div class="container">
                <div class="row mx-auto">
                    <label for="email">email</label>
                    <input type="text" id="email" name="email">
                </div>
                <div class="row mx-auto my-5">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="row align-bottom mx-auto">
                    <div class="col-sm-6 my-auto">
                        <input type="submit" name="login" value="Login" class="btn btn-success align-bottom">
                    </div>
                    <div class="col-sm-6">
                        <p>Not a member?</p>
                        <a class="btn btn-danger" href="register">Register</a>
                    </div>
                </div>
            </div>

        </form>
    </div>

</div>
<script type="text/javascript" src="assets/js/bootstrap.js"></script>
</body>
</html>