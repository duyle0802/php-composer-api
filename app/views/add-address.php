<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="mb-4 text-center">Thêm địa chỉ mới</h2>
            
            <div class="card shadow-sm">
                <div class="card-body">
                    <form id="add-address-form" onsubmit="submitAddress(event)">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Thông tin địa chỉ</label>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <select class="form-select" id="province" required onchange="loadDistricts()">
                                        <option value="" selected disabled>Chọn Tỉnh/Thành phố</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="district" required disabled onchange="loadWards()">
                                        <option value="" selected disabled>Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" id="ward" required disabled>
                                        <option value="" selected disabled>Chọn Phường/Xã</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="street" class="form-label">Số nhà, Tên đường</label>
                            <input type="text" class="form-control" id="street" required placeholder="Ví dụ: 123 Đường Nguyễn Văn Linh">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default">
                            <label class="form-check-label" for="is_default">Đặt làm địa chỉ mặc định</label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo BASE_URL; ?>/?page=checkout" class="btn btn-outline-secondary me-md-2">Hủy bỏ</a>
                            <button type="submit" class="btn btn-primary" id="btn-save">
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                Lưu địa chỉ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// API for Vietnam Administrative Divisions
const PROVINCE_API = 'https://provinces.open-api.vn/api/';

let provinces = [];
let districts = [];
let wards = [];

document.addEventListener('DOMContentLoaded', function() {
    loadProvinces();
});

function loadProvinces() {
    fetch(PROVINCE_API + '?depth=1')
        .then(response => response.json())
        .then(data => {
            provinces = data;
            const select = document.getElementById('province');
            data.forEach(p => {
                const option = document.createElement('option');
                option.value = p.code;
                option.textContent = p.name;
                option.dataset.name = p.name;
                select.appendChild(option);
            });
        })
        .catch(err => console.error('Error loading provinces:', err));
}

function loadDistricts() {
    const provinceCode = document.getElementById('province').value;
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    // Reset dependant fields
    districtSelect.innerHTML = '<option value="" selected disabled>Chọn Quận/Huyện</option>';
    wardSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
    districtSelect.disabled = true;
    wardSelect.disabled = true;

    if (!provinceCode) return;

    fetch(PROVINCE_API + 'p/' + provinceCode + '?depth=2')
        .then(response => response.json())
        .then(data => {
            districts = data.districts;
            data.districts.forEach(d => {
                const option = document.createElement('option');
                option.value = d.code;
                option.textContent = d.name;
                option.dataset.name = d.name;
                districtSelect.appendChild(option);
            });
            districtSelect.disabled = false;
        })
        .catch(err => console.error('Error loading districts:', err));
}

function loadWards() {
    const districtCode = document.getElementById('district').value;
    const wardSelect = document.getElementById('ward');
    
    wardSelect.innerHTML = '<option value="" selected disabled>Chọn Phường/Xã</option>';
    wardSelect.disabled = true;

    if (!districtCode) return;

    fetch(PROVINCE_API + 'd/' + districtCode + '?depth=2')
        .then(response => response.json())
        .then(data => {
            wards = data.wards;
            data.wards.forEach(w => {
                const option = document.createElement('option');
                option.value = w.code;
                option.textContent = w.name;
                option.dataset.name = w.name;
                wardSelect.appendChild(option);
            });
            wardSelect.disabled = false;
        })
        .catch(err => console.error('Error loading wards:', err));
}

function submitAddress(event) {
    event.preventDefault();
    
    // Get display names
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    
    const provinceName = provinceSelect.options[provinceSelect.selectedIndex]?.dataset.name;
    const districtName = districtSelect.options[districtSelect.selectedIndex]?.dataset.name;
    const wardName = wardSelect.options[wardSelect.selectedIndex]?.dataset.name;
    const street = document.getElementById('street').value.trim();
    
    if (!provinceName || !districtName || !wardName || !street) {
        showAlert('Vui lòng điền đầy đủ thông tin địa chỉ', 'warning');
        return;
    }

    // Construct full address string compatible with geocoding
    // Format: "Street, Ward, District, Province"
    const fullAddress = `${street}, ${wardName}, ${districtName}, ${provinceName}`;
    
    const isDefault = document.getElementById('is_default').checked ? 1 : 0;
    const btnSave = document.getElementById('btn-save');
    const spinner = btnSave.querySelector('.spinner-border');

    // Disable button and show spinner
    btnSave.disabled = true;
    spinner.classList.remove('d-none');

    fetch(API_URL + '/address/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            address_line: fullAddress,
            is_default: isDefault,
            // We can send component parts if backend supports it, but currently it expects address_line
            // address_components: { street, ward: wardName, district: districtName, province: provinceName }
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
