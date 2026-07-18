<div class="wshc-auth-container" id="wshc-auth-container">

    <!-- Tabbed Navigation -->
    <div class="wshc-auth-tabs">
        <button class="wshc-auth-tab active" data-target="login">Login</button>
        <button class="wshc-auth-tab" data-target="registration">Register</button>
        <button class="wshc-auth-tab" data-target="forgot-password">Forgot Password</button>
    </div>

    <div class="wshc-auth-forms-wrapper">
        <!-- Login Form -->
        <div id="wshc-login-form-wrapper" class="wshc-auth-form-panel active-panel">
            <h2>Sign In</h2>
            <div class="wshc-message hidden"></div>
            <form id="wshc-login-form">
                <div class="wshc-auth-form-group">
                    <input type="text" name="username" placeholder="Username or Email" required minlength="4">
                </div>
                <div class="wshc-auth-form-group">
                    <input type="password" name="password" placeholder="Password" required minlength="8" maxlength="20">
                    <span class="password-toggle dashicons dashicons-visibility"></span>
                </div>
                <button type="submit" class="wshc-auth-btn">Sign In</button>
                <?php wp_nonce_field('wshc_auth_nonce', 'nonce'); ?>
            </form>
        </div>

        <!-- Registration Form -->
        <div id="wshc-registration-form-wrapper" class="wshc-auth-form-panel hidden">
            <h2>Create Account</h2>
            <div class="wshc-message hidden"></div>
            <form id="wshc-registration-form">
                <div class="wshc-auth-grid">
                    <div class="wshc-auth-form-group">
                        <input type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="wshc-auth-form-group">
                        <input type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="wshc-auth-form-group">
                        <input type="text" name="username" placeholder="Username" required minlength="4">
                    </div>
                    <div class="wshc-auth-form-group">
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="wshc-auth-form-group">
                        <input type="password" name="password" placeholder="Password" required minlength="8" maxlength="20">
                        <span class="password-toggle dashicons dashicons-visibility"></span>
                    </div>
                    <div class="wshc-auth-form-group">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="8" maxlength="20">
                        <span class="password-toggle dashicons dashicons-visibility"></span>
                    </div>
                </div>
                <button type="submit" class="wshc-auth-btn">Register</button>
                <?php wp_nonce_field('wshc_auth_nonce', 'nonce'); ?>
            </form>
        </div>

        <!-- Forgot Password Form -->
        <div id="wshc-forgot-password-form-wrapper" class="wshc-auth-form-panel hidden">
            <h2>Recover Account</h2>
            <div class="wshc-message hidden"></div>
            <form id="wshc-forgot-password-form">
                <div class="wshc-auth-form-group">
                    <input type="text" name="user_login" placeholder="Username or Email" required>
                </div>
                <button type="submit" class="wshc-auth-btn">Request OTP</button>
                <?php wp_nonce_field('wshc_auth_nonce', 'nonce'); ?>
            </form>
        </div>

        <!-- Reset Password Form -->
        <div id="wshc-reset-password-form-wrapper" class="wshc-auth-form-panel hidden">
            <h2>Reset Password</h2>
            <div class="wshc-message hidden"></div>
            <form id="wshc-reset-password-form">
                <input type="hidden" name="user_id" id="reset-user-id">
                <div class="wshc-auth-form-group">
                    <input type="text" name="otp" placeholder="Enter OTP" required>
                </div>
                <div class="wshc-auth-form-group">
                    <input type="password" name="new_password" placeholder="New Password" required minlength="8" maxlength="20">
                    <span class="password-toggle dashicons dashicons-visibility"></span>
                </div>
                <button type="submit" class="wshc-auth-btn">Reset Password</button>
                <?php wp_nonce_field('wshc_auth_nonce', 'nonce'); ?>
            </form>
        </div>
    </div>
</div>
