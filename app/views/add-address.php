<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h2 class="mb-4 text-center">Thêm địa chỉ mới</h2>
            
            <div class="card">
                <div class="card-body">
                    <form id="add-address-form" onsubmit="submitAddress(event)">
                        <div class="mb-3">
                            <label for="address_line" class="form-label">Địa chỉ (Số nhà, Đường, Phường/Xã, Quận/Huyện, Tỉnh/Thành phố)</label>
                            <input type="text" class="form-control" id="address_line" required placeholder="Ví dụ: 123 Đường ABC, Phường XYZ, Quận 1, TP. HCM">
                            <div class="form-text">Vui lòng nhập địa chỉ cụ thể để tính phí giao hàng chính xác.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default">
                            <label class="form-check-label" for="is_default">Đặt làm địa chỉ mặc định</label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary" id="btn-save">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Lưu địa chỉ
                            </button>
                            <a href="<?php echo BASE_URL; ?>/?page=checkout" class="btn btn-outline-secondary">Quay lại thanh toán</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function submitAddress(event) {
    event.preventDefault();
    
    const addressLine = document.getElementById('address_line').value;
    const isDefault = document.getElementById('is_default').checked ? 1 : 0;
    const btnSave = document.getElementById('btn-save');
    const spinner = btnSave.querySelector('.spinner-border');

    if (!addressLine.trim()) {
        showAlert('Vui lòng nhập địa chỉ', 'warning');
        return;
    }

    // Disable button and show spinner
    btnSave.disabled = true;
    spinner.classList.remove('d-none');

    fetch(API_URL + '/address/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            address_line: addressLine,
            is_default: isDefault
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Thêm địa chỉ thành công!', 'success');
            // Redirect back to checkout
            setTimeout(() => {
                window.location.href = '<?php echo BASE_URL; ?>/?page=checkout';
            }, 1000);
        } else {
            showAlert(data.message || 'Lỗi khi thêm địa chỉ', 'danger');
            btnSave.disabled = false;
            spinner.classList.add('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Lỗi kết nối', 'danger');
        btnSave.disabled = false;
        spinner.classList.add('d-none');
    });
}
</script>
