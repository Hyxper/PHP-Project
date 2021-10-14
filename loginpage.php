<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/styles.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Login Page</title>


</head>
<body>
<form action="results.php" method="$_POST">
<section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-6">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body px-2 text-center d-flex justify-content-center">
            <div class="w-50">
              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
              <p class="text-white-50 mb-5">Please enter your username and password.</p>
              <div class="form-outline form-white mb-4">

                <input type="text" id="typeUID" name ="username"class="form-control form-control-lg" />
                <label class="form-label" for="typeUID">Username</label>

              </div>
              <div class="form-outline form-white mb-4">

                <input type="password" id="typePassword" name ="password" class="form-control form-control-lg" />
                <label class="form-label" for="typePassword">Password</label>

              </div>
              <button class="btn btn-outline-light btn-lg px-5" type="submit" name="submit">Login</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</form>
</body>
</html>