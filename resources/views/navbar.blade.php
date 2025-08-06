<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top mb-4">
    <div class="container-fluid">
        <p class="navbar-brand">Anonymous chat</p>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul id="auth-nav" class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a>
                    </li>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <h5 class="modal-title mb-3">Login</h5>
            <form id="login-form">
                @csrf
                <div class="mb-3">
                    <label for="login-field" class="form-label">Login</label>
                    <input type="text" class="form-control" id="login-field" required>
                </div>
                <div class="mb-3">
                    <label for="password-field" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password-field" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Enter</button>
                <div id="login-error" class="text-danger mt-2" style="display: none;"></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <h5 class="modal-title mb-3">Register</h5>
            <form id="register-form">
                @csrf
                <div class="mb-3">
                    <label for="login-field-register" class="form-label">Login</label>
                    <input type="text" class="form-control" id="login-field-register" required>
                </div>
                <div class="mb-3">
                    <label for="password-field-register" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password-field-register" required>
                </div>
                <div class="mb-3">
                    <label for="password-confirmed-field" class="form-label">Confirm password</label>
                    <input type="password" class="form-control" id="password-confirmed-field" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Register</button>
                <div id="register-error" class="text-danger mt-2" style="display: none;"></div>
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            </form>
        </div>
    </div>
</div>

<script src="{{asset('assets/auth.js')}}"></script>