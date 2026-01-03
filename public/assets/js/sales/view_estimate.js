function openDiscountModal(label, value, type, customerId) {
  document.getElementById("discountLabel").textContent = "Edit " + label;
  document.getElementById("discountType").value = type;
  document.getElementById("customerId").value = customerId;
  document.getElementById("modalAlert").style.display = "none";

  var select = document.getElementById("discountValueSelect");
  select.innerHTML = "";

  var maxByType = {
    special_discount: 5,
    additional_discount: 5,
    branch_additional_discount: 2,
    rm_additional_discount: 3,
  };

  var max = maxByType[type] || 5;
  value = parseInt(value, 10);

  for (let i = 0; i <= max; i++) {
    let opt = document.createElement("option");
    opt.value = i;
    opt.text = i + "%";
    if (i === value) opt.selected = true;
    select.appendChild(opt);
  }

  var modal = new bootstrap.Modal(document.getElementById("editDiscountModal"));
  modal.show();
}

function updateDiscountDropdown() {
  const type = document.getElementById("discountTypeSelect").value;
  const cfg = discountConfig[type];
  const select = document.getElementById("discountValueSelect");
  select.innerHTML = "";
  document.getElementById("currentValue").value = cfg ? cfg.current + "%" : "";
  if (!cfg) return;
  for (let i = 0; i <= cfg.max; i++) {
    const option = document.createElement("option");
    option.value = i;
    option.textContent = i + "%";
    if (i === cfg.current) option.selected = true; 
    select.appendChild(option);
  }
}

document.getElementById("editDiscountForm").onsubmit = function (e) {
  e.preventDefault();
  const submitBtn = document.getElementById("submitBtn");
  submitBtn.disabled = true;
  submitBtn.innerHTML = "Saving...";
  const formData = new FormData(this);
  fetch("../../services/sales/view/update_discounts_view.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        bootstrap.Modal.getInstance(
          document.getElementById("editDiscountModal")
        ).hide();
        // Reload page or update the UI
        setTimeout(() => location.reload(), 500);
      } else {
        document.getElementById("modalAlert").textContent = data.message;
        document.getElementById("modalAlert").style.display = "block";
      }
    })
    .catch((error) => {
      document.getElementById("modalAlert").textContent =
        "An error occurred: " + error;
      document.getElementById("modalAlert").style.display = "block";
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = "Save changes";
    });
};

// Monesh code start

document.addEventListener("DOMContentLoaded", function () {
  // ===== EDIT MODAL (already working) =====
  const editButtons = document.querySelectorAll(".edit-btn");
  const requestIdField = document.getElementById("request_id");
  const reasonField = document.getElementById("reason");

  editButtons.forEach((btn) => {
    btn.addEventListener("click", function () {
      requestIdField.value = this.getAttribute("data-id");
      reasonField.value = this.getAttribute("data-reason");
    });
  });

  document
    .getElementById("editRequestForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);
      const response = await fetch("../../services/sales/view/update_request.php", {
        method: "POST",
        body: formData,
      });
      const result = await response.json();

      if (result.success) {
        alert("Request updated successfully!");
        location.reload();
      } else {
        alert("Failed to update request.");
      }
    });

  // ===== DELETE MODAL =====
  let deleteId = null;
  let deleteCustomer = null;
  const deleteModal = new bootstrap.Modal(
    document.getElementById("deleteRequestModal")
  );
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      deleteId = this.getAttribute("data-id");
      deleteCustomer = this.getAttribute("data-customer");
      deleteModal.show();
    });
  });

  confirmDeleteBtn.addEventListener("click", async function () {
    if (!deleteId) return;

    const response = await fetch("../../services/sales/view/delete_request.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(
        deleteId
      )}&customer_id=${encodeURIComponent(deleteCustomer)}`,
    });

    const result = await response.json();

    if (result.success) {
      deleteModal.hide();
      const row = document
        .querySelector(`[data-id="${deleteId}"]`)
        .closest("tr");
      row.style.transition = "opacity 0.5s";
      row.style.opacity = "0";
      setTimeout(() => row.remove(), 500);
      alert("Deleted successfully!");
    } else {
      alert("Error deleting request.");
    }
  });
});

// Monesh code end 
