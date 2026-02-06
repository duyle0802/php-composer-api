<div class="container py-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h2 class="mb-4">Liên hệ với chúng tôi</h2>
            
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Thông tin liên lạc</h5>
                            <p><strong>Email:</strong> <?php echo ADMIN_EMAIL; ?></p>
                            <p><strong>Điện thoại:</strong> 1900-xxxx</p>
                            <p><strong>Địa chỉ:</strong> TP. Hồ Chí Minh, Việt Nam</p>
                            <p><strong>Giờ làm việc:</strong> 9:00 - 18:00 (T2 - T7)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5>Gửi tin nhắn</h5>
                            <form id="contact-form">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Họ tên</label>
                                    <input type="text" class="form-control" id="name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Nội dung</label>
                                    <textarea class="form-control" id="message" rows="4" required></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Gửi</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    // Send via API
    fetch(API_URL + '/contact/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            email: email,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất có thể.', 'success');
            document.getElementById('contact-form').reset();
        } else {
            showAlert(data.message || 'Gửi tin nhắn thất bại', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Đã có lỗi xảy ra', 'danger');
    });
});
</script>
