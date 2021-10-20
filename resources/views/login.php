<div class="login-container">
    <div class="center-content margin-top">
        <h1 class="inline title">Welcome</h1>
    </div>

    <div class="center-content margin-top">
        <h2 class="inline subtitle">to demo shop administration!</h2>
    </div>
    <?php if (!empty($error)): ?>
        <div class="alert-container">
            <div class="alert-message"><?= $error ?></div>
            <div class="alert-button">×</div>
        </div>
    <?php endif; ?>
    <div class="center-content">
        <form method="post" class="login-form">
            <div class="input-row margin-top">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-row margin-top">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="input-row margin-top">
                <div class="checkbox-container">
                    <input type="checkbox" name="keepMeLoggedIn" id="keep-me-logged-in">
                    <label for="keep-me-logged-in">Keep me logged in</label>
                </div>
                <input type="submit" value="Log In" class="button">
            </div>
        </form>
    </div>
</div>
<script src="../../public/js/alert.js"></script>