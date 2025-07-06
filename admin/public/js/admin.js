document.addEventListener('DOMContentLoaded', function () {
    // Size tags
    const sizeInput = document.getElementById('size-input');
    const sizeTags = document.getElementById('size-tags');
    let sizes = [];

    sizeInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && sizeInput.value.trim() !== '') {
            e.preventDefault();
            if (sizes.length >= 5) return alert('Tối đa 5 size');
            let val = sizeInput.value.trim();
            if (!/^\d+$/.test(val) || parseInt(val) <= 0) return alert('Size phải là số dương');
            sizes.push(val);
            renderTags(sizeTags, sizes, (i) => { sizes.splice(i, 1); renderTags(sizeTags, sizes); });
            sizeInput.value = '';
        }
    });

    // Color tags
    const colorInput = document.getElementById('color-input-field');
    const colorTags = document.getElementById('color-tags');
    let colors = [];

    colorInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && colorInput.value.trim() !== '') {
            e.preventDefault();
            if (colors.length >= 5) return alert('Tối đa 5 màu');
            let val = colorInput.value.trim();
            if (val === '') return alert('Màu không được rỗng');
            colors.push(val);
            renderTags(colorTags, colors, (i) => { colors.splice(i, 1); renderTags(colorTags, colors); });
            colorInput.value = '';
        }
    });

    function renderTags(container, arr, onRemove) {
        container.innerHTML = '';
        arr.forEach((val, i) => {
            let tag = document.createElement('span');
            tag.className = 'tag';
            tag.textContent = val;
            let rm = document.createElement('span');
            rm.textContent = ' ×';
            rm.style.cursor = 'pointer';
            rm.onclick = () => onRemove(i);
            tag.appendChild(rm);
            container.appendChild(tag);
        });
    }

    // Image preview with delete option
    const imageInput = document.getElementById('image-input');
    const preview = document.getElementById('preview-images');
    let selectedImages = [];

    imageInput.addEventListener('change', function () {
        const files = Array.from(imageInput.files);
        if (selectedImages.length + files.length > 10) {
            alert('Tối đa 10 ảnh cho mỗi sản phẩm');
            return;
        }

        files.forEach(file => {
            selectedImages.push(file);

            const reader = new FileReader();
            reader.onload = e => {
                const container = document.createElement('div');
                container.style.display = 'inline-block';
                container.style.position = 'relative';
                container.style.margin = '5px';

                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '60px';
                img.style.height = '60px';
                img.style.objectFit = 'cover';

                const removeBtn = document.createElement('span');
                removeBtn.textContent = '×';
                removeBtn.style.position = 'absolute';
                removeBtn.style.top = '0';
                removeBtn.style.right = '5px';
                removeBtn.style.cursor = 'pointer';
                removeBtn.style.color = 'red';
                removeBtn.style.fontWeight = 'bold';

                removeBtn.onclick = () => {
                    selectedImages = selectedImages.filter(f => f !== file);
                    preview.removeChild(container);
                };

                container.appendChild(img);
                container.appendChild(removeBtn);
                preview.appendChild(container);
            };
            reader.readAsDataURL(file);
        });

        imageInput.value = '';
    });

    // Load products and render to table
    function loadProducts() {
        fetch('../../controllers/get_products.php')
            .then(r => r.json())
            .then(data => {
                const tbody = document.querySelector('#product-table tbody');
                tbody.innerHTML = '';
                data.forEach(p => {
                    let row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${p.images && p.images[0] ? `<img src="/Website_Selling sports shoes/${p.images[0]}" style="width:50px">` : ''}</td>
                        <td>${p.name || ''}</td>
                        <td>${p.type || ''}</td>
                        <td>${(p.sizes || []).join(', ')}</td>
                        <td>${(p.colors || []).join(', ')}</td>
                        <td>${p.price?.toLocaleString('vi-VN') || ''}</td>
                        <td>${p.date_time || ''}</td>
                        <td>${p.sales ?? ''}</td>
                        <td>
                            <div class="dropdown">
                                <span class="menu-btn">⋮</span>
                                <div class="dropdown-content">
                                    <div class="edit-btn" data-id="${p.id}">Chỉnh sửa</div>
                                    <div class="delete-btn" data-id="${p.id}">Xóa</div>
                                </div>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
    }

    // Submit form
    const form = document.getElementById('product-form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (sizes.length === 0) return alert('Nhập ít nhất 1 size');
        if (colors.length === 0) return alert('Nhập ít nhất 1 màu');
        if (selectedImages.length === 0) return alert('Chọn ít nhất 1 ảnh');

        document.getElementById('sizes_raw').value = JSON.stringify(sizes);
        document.getElementById('color_input').value = JSON.stringify(colors);

        const dateTime = form.date_time.value.trim();
        const sales = form.sales.value.trim();

        if ((dateTime && !sales) || (!dateTime && sales)) {
            return alert('Nếu nhập "Date & Time" thì phải nhập cả "Sales", và ngược lại.');
        }

        const formData = new FormData();
        formData.append('name', form.name.value);
        formData.append('type', form.type.value);
        formData.append('price', form.price.value);
        formData.append('add_product', '1');
        formData.append('sizes_raw', JSON.stringify(sizes));
        formData.append('color', JSON.stringify(colors));
        formData.append('date_time', dateTime);
        formData.append('sales', sales);
        selectedImages.forEach(file => formData.append('images[]', file));

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                form.reset();
                sizes = [];
                colors = [];
                selectedImages = [];
                renderTags(sizeTags, sizes);
                renderTags(colorTags, colors);
                preview.innerHTML = '';
                loadProducts();
            }
        })
        .catch(() => alert('Lỗi kết nối server'));
    });

    // Cancel button
    const cancelBtn = form.querySelector('.cancel-btn');
    if (cancelBtn) {
        cancelBtn.onclick = () => {
            sizes = [];
            colors = [];
            selectedImages = [];
            renderTags(sizeTags, sizes);
            renderTags(colorTags, colors);
            preview.innerHTML = '';
        };
    }

    // Xử lý hành động chỉnh sửa và xóa
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-btn')) {
            const productId = e.target.dataset.id;
            window.location.href = `edit_product.html?id=${productId}`;
        }

        if (e.target.classList.contains('delete-btn')) {
            const productId = e.target.dataset.id;
            const confirmed = confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?');
            if (confirmed) {
                fetch(`../../controllers/delete_product.php?id=${productId}`, {
                    method: 'GET'
                })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) loadProducts();
                })
                .catch(() => alert('Lỗi kết nối khi xóa sản phẩm'));
            }
        }
    });

    // Initial load
    loadProducts();
});