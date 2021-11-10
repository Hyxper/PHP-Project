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
<form action="verifyuser.php" method="POST">
<section class="vh-100">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-6">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body px-2 text-center d-flex justify-content-center">
            <div class="w-50">
                
              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>

              <p class="text-white-50 mb-2">Please enter your username and password.</p>
              
                <?php
                require __DIR__ . '/functions.php';
                session_start();
                set_timezone("GMT");
                if(isset($_SESSION["usercreds"])){ 
                    if($_SESSION["usercreds"] == 1){
                        echo
                      "<div class='alert alert-danger' role='alert'>
                        User not found!
                      </div>";

                       session_destroy();
                    }elseif($_SESSION["usercreds"] == 2){
                        echo
                      "<div class='alert alert-danger' role='alert'>
                        Invalid Password!
                      </div>";
                      session_destroy();    
                    }   
                }
                ?>
                
              <div class="form-outline form-white">
                <input type="text" id="typeUID" name ="username" class="form-control form-control-lg" required autofocus> <!-- added required so form cant process if empty -->
                <label class="form-label" for="typeUID">Username</label>
              </div>

              <div class="form-outline form-white">
                <input type="password" id="typePassword" name ="password" class="form-control form-control-lg" required> <!-- added required so form cant process if empty -->
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