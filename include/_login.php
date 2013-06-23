<!-- Login form -->
<div id="dim_back">
    <form id="lightbox" class="form-signin" action="./" method="post" name="login_form" accept-charset="UTF-8">
        <h2 class="form-signin-heading muted">Please sign in</h2>
<!--         <div class="control-group error"> -->
        <input type="text" class="input-block-level" placeholder="Login" name="login">
<!--         </div> -->
<!--         <div class="control-group success"> -->
        <input type="password" class="input-block-level" placeholder="Password" name="password">
<!--         </div> -->
        <label class="text-error" id="login_error_message">Hier ist ein Error!</label>
        <label class="checkbox muted">
        <input id="rememberme" type="checkbox" value="remember-me"> Remember me</label>
        <label class="muted" style="margin-top: 15pt; cursor: default;">
          Don't have login? <a href="registration.php">Register!</a>
        </label>
        <label class="muted" style="cursor: default;">
          Forgot password? <a href="forgot.php">Send E-mail.</a>
        </label>
        <input type="hidden" name="submit" value="1">
        <button class="btn btn-warning" type="submit">Sign in</button>
    </form>
</div>