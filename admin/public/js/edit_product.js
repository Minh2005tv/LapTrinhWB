document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('edit-product-form');
    const saveBtn = document.getElementById('save-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const confirmationDialog = document.getElementById('confirmation-dialog');
    const confirmAccept = document.getElementById('confirm-accept');
    const confirmCancel = document.getElementById('confirm-cancel');

    // Load product data for editing
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    if (productId) {
        fetch(`../../controllers/edit_product.php?id=${productId}`)
            .then(r => r.json())
            .then(product => {
                if (product.error) alert(product.error);
                else {
                    document.getElementById('product-id').value = product.id;
                    document.getElementById('name').value = product.name;
                    document.getElementById('type').value = product.type;
                    document.getElementById('price').value = product.price;
                    document.getElementById('date_time').value = product.date_time || '';
                    document.getElementById('sales').value = product.sales || '';

                    let sizes = product.sizes || [];
                    let colors = product.colors || [];
                    renderTags(document.getElementById('size-tags'), sizes, (i) => { sizes.splice(i, 1); renderTags(document.getElementById('size-tags'), sizes); });
                    renderTags(document.getElementById('color-tags'), colors, (i) => { colors.splice(i, 1); renderTags(document.getElementById('color-tags'), colors); });
                    document.getElementById('sizes_raw').value = JSON.stringify(sizes);
                    document.getElementById('color_input').value = JSON.stringify(colors);
                }
            });
    }

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
            document.getElementById('sizes_raw').value = JSON.stringify(sizes);
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
            document.getElementById('color_input').value = JSON.stringify(colors);
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

    // Image preview (same as admin.js, omitted for brevity but can be reused)

    // Save button
    saveBtn.addEventListener('click', function (e) {
        e.preventDefault();
        if (sizes.length === 0) return alert('Nhập ít nhất 1 size');
        if (colors.length === 0) return alert('Nhập ít nhất 1 màu');

        const dateTime = form.date_time.value.trim();
        const sales = form.sales.value.trim();
        if ((dateTime && !sales) || (!dateTime && sales)) {
            return alert('Nếu nhập "Date & Time" thì phải nhập cả "Sales", và ngược lại.');
        }

        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                confirmationDialog.style.display = 'block';
            } else {
                alert(data.message);
            }
        });
    });

    // Confirmation actions
    confirmAccept.addEventListener('click', function () {
        confirmationDialog.style.display = 'none';
        window.location.href = 'admin.html';
    });

    confirmCancel.addEventListener('click', function () {
        confirmationDialog.style.display = 'none';
    });

    // Cancel button
    cancelBtn.addEventListener('click', function () {
        window.location.href = 'admin.html';
    });
});