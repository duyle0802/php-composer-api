<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Đăng nhập</h2>
            
            <div class="card">
                <div class="card-body">
                    <form id="login-form">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
                    </form>

                    <hr>

                    <p class="text-center mb-0">Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>/?page=register">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    fetch(API_URL + '/auth/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            username: username,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Đăng nhập thành công!', 'success');
            setTimeout(() => {
                if (data.user.role === 'admin') {
                    window.location.href = '<?php echo BASE_URL; ?>/?page=admin';
                } else {
                    window.location.href = '<?php echo BASE_URL; ?>/?page=home';
                }
            }, 1000);
        } else {
            showAlert(data.message || 'Đăng nhập thất bại', 'danger');
        }
    });
});
</script>
