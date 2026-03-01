// Get invoice number from URL
const params = new URLSearchParams(window.location.search);
const invoice = params.get("invoice");

// Display it
document.getElementById("invoiceText").innerText =
  "Invoice #: " + (invoice ? invoice : "N/A");
