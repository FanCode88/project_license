// === CĂUTĂRI ȘI ACȚIUNI AJAX ===

// Ștergere rezervare
async function cancelReservation(resId, btnElement) {
  if (!confirm('Ești sigur că vrei să anulezi această rezervare?')) return;

  try {
    const response = await fetch(`member-index.php?action=delete_res&id=${resId}`);
    const data = await response.text();

    if (data.trim() === 'success') {
      btnElement.closest('tr')?.remove();
    } else {
      alert('Eroare la ștergerea rezervării.');
    }
  } catch (error) {
    console.error('Fetch error:', error);
    alert('A apărut o eroare de rețea.');
  }
}

// Trimitere cantitate (înlocuit XMLHttpRequest vechi cu fetch)
async function getQuantity(quantityId) {
  try {
    await fetch(`update-quantity.php?quantity_id=${quantityId}`);
  } catch (error) {
    console.error('Eroare la actualizarea cantității:', error);
  }
}

// === HELPERE DE VALIDARE ===

// Validare Email (Regex optimizat)
const isValidEmail = (val) => /^[\w+.-]+@[\w.-]+\.[a-zA-Z]{2,}$/.test(val);

// Validare PIN Special (ex: 12ABC1234567)
const isValidSpecialPIN = (val) => /^[0-9]{2}[A-Z]{3}[0-9]{7}$/.test(val);

// Validare lungime
const isValidLength = (val, expectedLength = 12) => val?.length === expectedLength;

// Helper generic pentru manipularea erorilor
function validateForm(errors) {
  if (errors.length > 0) {
    alert(errors.join('\n'));
    return false;
  }
  return true;
}

// === VALIDĂRI DE FORMULARE ===

function loginValidate(form) {
  const errors = [];

  if (!form.login.value.trim()) errors.push('Email not filled!');
  if (!form.password.value) errors.push('Password not filled!');
  if (form.login.value && !isValidEmail(form.login.value)) errors.push('Invalid email address provided!');

  return validateForm(errors);
}

function registerValidate(form) {
  const errors = [];

  if (!form.fname.value.trim()) errors.push('Firstname not filled!');
  if (!form.lname.value.trim()) errors.push('Lastname not filled!');
  if (!form.login.value.trim()) errors.push('Email not filled!');
  if (!form.password.value) errors.push('Password not provided!');
  if (!form.cpassword.value) errors.push('Confirm password not filled!');
  if (form.password.value !== form.cpassword.value) errors.push('Password and Confirm Password do not match!');
  if (form.login.value && !isValidEmail(form.login.value)) errors.push('Invalid email address provided!');
  if (form.question.selectedIndex === 0) errors.push('Question not selected!');
  if (!form.answer.value.trim()) errors.push('Answer not filled!');

  return validateForm(errors);
}

function passwordResetValidate(form) {
  const errors = [];

  if (!form.email.value.trim()) {
    errors.push('Please enter your account email! We need your email in order to reset your password.');
  } else if (!isValidEmail(form.email.value)) {
    errors.push('Invalid email address provided!');
  }

  return validateForm(errors);
}

function passwordResetValidate_2(form) {
  const errors = [];

  if (!form.answer.value.trim()) errors.push('Please enter your security answer to your provided security question.');
  if (!form.new_password.value) errors.push('New Password not set!');
  if (!form.confirm_new_password.value) errors.push('Confirm New Password not set!');
  if (form.new_password.value !== form.confirm_new_password.value) errors.push('New Password and Confirm New Password do not match!');

  return validateForm(errors);
}

function finalCheck(form) {
  const errors = [];
  const qty = parseInt(form.quantity.value, 10);

  if (!form.quantity.value || isNaN(qty)) errors.push('Please provide a quantity.');
  else if (qty === 0) errors.push('Please provide a quantity rather than 0.');

  if (!form.total.value) errors.push('Total has not been calculated! Please provide first the quantity.');

  return validateForm(errors);
}

function updateValidate(form) {
  const errors = [];

  if (!form.opassword.value) errors.push('Please provide your old password.');
  if (!form.npassword.value) errors.push('Please provide a new password.');
  if (!form.cpassword.value) errors.push('Please confirm your new password.');
  if (form.cpassword.value !== form.npassword.value) errors.push('Confirm Password and New Password do not match!');

  return validateForm(errors);
}

function billingValidate(form) {
  const errors = [];

  if (!form.sAddress.value.trim()) errors.push('Please provide a street address.');
  if (!form.box.value.trim()) errors.push('Please provide your postal box number.');
  if (!form.city.value.trim()) errors.push('Please provide your city.');
  if (!form.mNumber.value.trim()) errors.push('Please provide your mobile number.');

  return validateForm(errors);
}

function tableValidate(form) {
  const errors = [];

  if (form.table.selectedIndex === 0) errors.push('Please select a table by its name or number.');
  if (!form.date.value) errors.push('Please provide a reservation date.');
  if (!form.time.value) errors.push('Please provide a reservation time.');

  return validateForm(errors);
}

function partyhallValidate(form) {
  const errors = [];

  if (form.partyhall.selectedIndex === 0) errors.push('Please select a partyhall by its name or number.');
  if (!form.date.value) errors.push('Please provide a reservation date.');
  if (!form.time.value) errors.push('Please provide a reservation time.');

  return validateForm(errors);
}

function categoriesValidate(form) {
  const errors = [];
  if (form.category.selectedIndex === 0) errors.push('Please select a category first!');
  return validateForm(errors);
}

function updateQuantity(form) {
  const errors = [];
  if (form.item.selectedIndex === 0) errors.push('Please select an item id first!');
  if (form.quantity.selectedIndex === 0) errors.push('Please select a quantity first!');
  return validateForm(errors);
}

function ratingValidate(form) {
  const errors = [];
  if (form.food.selectedIndex === 0) errors.push('Please select the food. This information is necessary in order to serve you better.');
  if (form.scale.selectedIndex === 0) errors.push('Please select the scale. This information is necessary in order to serve you better.');
  return validateForm(errors);
}

// === UTILITARE UI ===

function resetPassword() {
  window.open(
    'password-reset.php',
    'resetPassword',
    'toolbar=no,location=no,directories=no,status=no,menubar=no,resizable=no,copyhistory=no,scrollbars=yes,width=480,height=320'
  );
}

// Ceas în timp real
function updateClock() {
  const now = new Date();
  const timeString = now.toLocaleTimeString('en-US', { hour12: true });

  const clockEl = document.getElementById('clock');
  if (clockEl) {
    clockEl.textContent = timeString;
  }
}
