<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Đăng ký</h2>
            
            <div class="card">
                <div class="card-body">
                    <form id="register-form">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="username" class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="password_confirm" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
                    </form>

                    <hr>

                    <p class="text-center mb-0">Đã có tài khoản? <a href="<?php echo BASE_URL; ?>/?page=login">Đăng nhập</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('register-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const fullName = document.getElementById('full_name').value;
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;

    if (password !== passwordConfirm) {
        showAlert('Mật khẩu không khớp', 'danger');
        return;
    }

    fetch(API_URL + '/auth/register', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            full_name: fullName,
            username: username,
            email: email,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Đăng ký thành công! Vui lòng đăng nhập', 'success');
            setTimeout(() => {
                window.location.href = '<?php echo BASE_URL; ?>/?page=login';
            }, 2000);
        } else {
            showAlert(data.message || 'Đăng ký thất bại', 'danger');
        }
    });
});
</script>
