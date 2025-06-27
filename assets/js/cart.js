// LapTrinhWB/assets/js/cart.js

document.addEventListener("DOMContentLoaded", () => {
  const checkboxes = document.querySelectorAll(".select-product");
  const selectAll = document.getElementById("select-all");
  const countSpan = document.getElementById("count");
  const totalSpan = document.getElementById("total");
  const payBtn = document.getElementById("pay-btn");

  function updateTotal() {
    let count = 0;
    let total = 0;

    document.querySelectorAll(".product").forEach((product) => {
      const checkbox = product.querySelector(".select-product");
      const qty = parseInt(product.querySelector("input[type='number']").value);
      const price = parseFloat(product.dataset.price);

      if (checkbox.checked) {
        count++;
        total += qty * price;
      }
    });

    countSpan.textContent = count;
    totalSpan.textContent = total + "$";
  }

  checkboxes.forEach((chk) => {
    chk.addEventListener("change", updateTotal);
  });

  selectAll.addEventListener("change", (e) => {
    checkboxes.forEach((chk) => (chk.checked = e.target.checked));
    updateTotal();
  });

  document.querySelectorAll(".increase").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.parentElement.querySelector("input");
      input.value = parseInt(input.value) + 1;
      updateTotal();
    });
  });

  document.querySelectorAll(".decrease").forEach((btn) => {
    btn.addEventListener("click", () => {
      const input = btn.parentElement.querySelector("input");
      if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
        updateTotal();
      }
    });
  });

  // Hàm hiện popup noti mới
  function showPopupNoti(type, message) {
    const popup = document.getElementById('popup-noti');
    const iconDiv = popup.querySelector('.popup-icon');
    const titleDiv = popup.querySelector('.popup-title');
    const msgDiv = popup.querySelector('.popup-message');
    if (type === 'success') {
      iconDiv.innerHTML = '<i class="fa fa-check-circle"></i>';
      titleDiv.textContent = 'Đặt hàng thành công';
      msgDiv.textContent = message || 'Cảm ơn bạn đã mua hàng!';
    } else {
      iconDiv.innerHTML = '<i class="fa fa-times-circle"></i>';
      titleDiv.textContent = 'Đặt hàng thất bại';
      msgDiv.textContent = message || 'Vui lòng kiểm tra lại thông tin.';
    }
    popup.className = '';
    popup.classList.add('show', type);
    setTimeout(() => {
      popup.classList.remove('show');
    }, 2000);
  }

  payBtn.addEventListener("click", () => {
    // Validate các trường payment
    const delivery = document.getElementById("delivery-method");
    const address = document.getElementById("address");
    const payment = document.getElementById("payment-method");
    const checkedProducts = Array.from(document.querySelectorAll(".select-product")).some(chk => chk.checked);
    let valid = true;
    let firstInvalid = null;
    let errorMsg = '';
    // Xóa hiệu ứng cũ
    [delivery, address, payment].forEach(el => el && el.classList.remove("input-error", "shake"));
    // Kiểm tra từng trường
    if (!checkedProducts) {
      showPopupNoti("error", "Bạn chưa chọn sản phẩm để thanh toán!");
      return;
    }
    if (!delivery.value) {
      delivery.classList.add("input-error", "shake");
      valid = false;
      firstInvalid = delivery;
      errorMsg += 'Chưa chọn phương thức giao hàng.\n';
    }
    if (!address.value.trim()) {
      address.classList.add("input-error", "shake");
      valid = false;
      if (!firstInvalid) firstInvalid = address;
      errorMsg += 'Chưa nhập địa chỉ nhận hàng.\n';
    }
    if (!payment.value) {
      payment.classList.add("input-error", "shake");
      valid = false;
      if (!firstInvalid) firstInvalid = payment;
      errorMsg += 'Chưa chọn phương thức thanh toán.';
    }
    if (!valid) {
      setTimeout(() => {
        [delivery, address, payment].forEach(el => el && el.classList.remove("shake"));
      }, 500);
      if (firstInvalid) firstInvalid.focus();
      showPopupNoti("error", errorMsg.trim());
      return;
    }
    // Animation nút
    payBtn.classList.add("btn-success-anim");
    payBtn.textContent = "Đặt hàng...";
    setTimeout(() => {
      payBtn.classList.remove("btn-success-anim");
      payBtn.textContent = "PAYMENT";
      showPopupNoti("success", "Đơn hàng của bạn đã được ghi nhận. Chúng tôi sẽ liên hệ xác nhận sớm!");
    }, 1200);
  });
});

