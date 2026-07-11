/**
 * Fișier: validation/admin.js
 * Descriere: Funcții de validare pentru formularele de administrare.
 */

// 1. Login Validation
function loginValidate(loginForm) {
  var validationVerified = true;
  var errorMessage = "";

  if (loginForm.login.value == "") {
    errorMessage += "Username not filled!\n";
    validationVerified = false;
  }
  if (loginForm.password.value == "") {
    errorMessage += "Password not filled!\n";
    validationVerified = false;
  }
  if (!validationVerified) alert(errorMessage);
  return validationVerified;
}

// 2. Password Update Validation
function updateValidate(updateForm) {
  var validationVerified = true;
  var errorMessage = "";

  if (updateForm.opassword.value == "") errorMessage += "Please provide your current password.\n";
  if (updateForm.npassword.value == "") errorMessage += "Please provide a new password.\n";
  if (updateForm.cpassword.value == "") errorMessage += "Please confirm your new password.\n";
  if (updateForm.cpassword.value != updateForm.npassword.value) errorMessage += "Confirm password and new password do not match!\n";

  if (errorMessage != "") {
    alert(errorMessage);
    validationVerified = false;
  }
  return validationVerified;
}

// 3. Staff Validation
function staffValidate(staffForm) {
  var validationVerified = true;
  var errorMessage = "";

  if (staffForm.fName.value == "") errorMessage += "Please provide the staff first name.\n";
  if (staffForm.lName.value == "") errorMessage += "Please provide the staff last name.\n";
  if (staffForm.sAddress.value == "") errorMessage += "Please provide the staff street address.\n";
  if (staffForm.mobile.value == "") errorMessage += "Please provide the staff mobile/telephone number.\n";

  if (errorMessage != "") {
    alert(errorMessage);
    validationVerified = false;
  }
  return validationVerified;
}

// 4. Specials Validation
function specialsValidate(specialsForm) {
  var validationVerified = true;
  var errorMessage = "";

  if (specialsForm.name.value == "") errorMessage += "name not filled!\n";
  if (specialsForm.description.value == "") errorMessage += "description not filled!\n";
  if (specialsForm.price.value == "") errorMessage += "price not filled!\n";
  if (specialsForm.start_date.value == "") errorMessage += "start date not filled!\n";
  if (specialsForm.end_date.value == "") errorMessage += "end date not filled!\n";
  if (specialsForm.photo.value == "") errorMessage += "photo not selected!\n";

  if (errorMessage != "") {
    alert(errorMessage);
    validationVerified = false;
  }
  return validationVerified;
}

// 5. Food Items Validation
function foodsValidate(foodsForm) {
  var validationVerified = true;
  var errorMessage = "";

  if (foodsForm.name.value == "") errorMessage += "food name not filled!\n";
  if (foodsForm.price.value == "") errorMessage += "food price not filled!\n";
  if (foodsForm.category.selectedIndex == 0) errorMessage += "please select a food category!\n";
  if (foodsForm.photo.value == "") errorMessage += "food photo not selected!\n";

  if (errorMessage != "") {
    alert(errorMessage);
    validationVerified = false;
  }
  return validationVerified;
}

// 6. Categories, Quantities, Currencies, Ratings, Timezones, Tables, Partyhalls, Questions
// Acestea pot fi grupate logic, dar le păstrăm separate pentru compatibilitate cu apelurile tale existente
function categoriesValidate(form) {
  if (form.name.value == "" || form.category.selectedIndex == 0) {
    alert("Please fill name and select category!");
    return false;
  }
  return true;
}
function quantitiesValidate(form) {
  if (form.name.value == "" || form.quantity.selectedIndex == 0) {
    alert("Please fill name and select quantity!");
    return false;
  }
  return true;
}
function currenciesValidate(form) {
  if (form.name.value == "" || form.currency.selectedIndex == 0) {
    alert("Please fill name and select currency!");
    return false;
  }
  return true;
}
function ratingsValidate(form) {
  if (form.name.value == "" || form.rating.selectedIndex == 0) {
    alert("Please fill name and select rating!");
    return false;
  }
  return true;
}
function timezonesValidate(form) {
  if (form.name.value == "" || form.timezone.selectedIndex == 0) {
    alert("Please fill name and select timezone!");
    return false;
  }
  return true;
}
function tablesValidate(form) {
  if (form.name.value == "" || form.table.selectedIndex == 0) {
    alert("Please fill name and select table!");
    return false;
  }
  return true;
}
function partyhallsValidate(form) {
  if (form.name.value == "" || form.partyhall.selectedIndex == 0) {
    alert("Please fill name and select hall!");
    return false;
  }
  return true;
}
function questionsValidate(form) {
  if (form.name.value == "" || form.question.selectedIndex == 0) {
    alert("Please fill name and select question!");
    return false;
  }
  return true;
}

// 7. Status, Allocation, Message
function statusValidate(form) {
  if (form.food.selectedIndex == 0) {
    alert("food not selected!");
    return false;
  }
  return true;
}
function ordersAllocationValidate(form) {
  if (form.orderid.selectedIndex == 0 || form.staffid.selectedIndex == 0) {
    alert("Please select Order and Staff!");
    return false;
  }
  return true;
}
function reservationsAllocationValidate(form) {
  if (form.reservationid.selectedIndex == 0 || form.staffid.selectedIndex == 0) {
    alert("Please select Reservation and Staff!");
    return false;
  }
  return true;
}
function messageValidate(form) {
  if (form.subject.value == "" || form.txtmessage.value == "") {
    alert("Subject and message are required!");
    return false;
  }
  return true;
}
