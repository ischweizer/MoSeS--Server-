<?php /*******************************************************************************
 * Copyright 2013
 * Telecooperation (TK) Lab
 * Technische Universität Darmstadt
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 ******************************************************************************/ ?>
<?php

/*
 * @author: Wladimir Schmidt
 */
                       
?><!-- Login form -->
<div id="dim_back">
    <form id="lightbox" class="form-signin" method="post" name="login_form" accept-charset="UTF-8">
    	<a class="boxclose" id="boxclose"></a>
        <h2 class="form-signin-heading muted">Please sign in</h2>
        <input type="email" class="input-block-level" placeholder="Email" name="email_login">
        <input type="password" class="input-block-level" placeholder="Password" name="password_login">
        <label class="text-error" id="login_error_message">Error</label>
        <label class="checkbox muted">
        <input id="rememberme" type="checkbox" value="remember-me"> Remember me</label>
        <label class="muted" style="margin-top: 15pt; cursor: default;">
          Not registered? <a href="registration.php">Register!</a>
        </label>
        <label class="muted" style="cursor: default;">
          Forgot your password? <a href="forgot.php">Reset it.</a>
        </label>
        <input type="hidden" name="submit" value="1">
        <button class="btn btn-success" type="submit">Sign in</button>
    </form>
</div>
