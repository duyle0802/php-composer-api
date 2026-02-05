<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Hồ sơ của tôi</h2>
            
            <div class="card">
                <div class="card-body">
                    <form id="profile-form">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên người dùng</label>
                            <input type="text" class="form-control" id="username" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" id="full_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phone">
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật hồ sơ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
});

function loadProfile() {
    fetch(API_URL + '/auth/profile')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const user = data.user;
                document.getElementById('username').value = user.username || '';
                document.getElementById('email').value = user.email || '';
                document.getElementById('full_name').value = user.full_name || '';
                document.getElementById('phone').value = user.phone || '';
                document.getElementById('address').value = user.address || '';
            }
        });
}

document.getElementById('profile-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const data = {
        id: '<?php echo $_SESSION['user_id']; ?>',
        email: document.getElementById('email').value,
        full_name: document.getElementById('full_name').value,
        phone: document.getElementById('phone').value,
        address: document.getElementById('address').value
    };

    fetch(API_URL + '/users/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Hồ sơ đã được cập nhật!', 'success');
        } else {
            showAlert(data.message || 'Lỗi khi cập nhật hồ sơ', 'danger');
        }
    });
});
</script>
