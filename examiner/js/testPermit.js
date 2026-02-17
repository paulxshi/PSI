document.addEventListener("DOMContentLoaded", () => {

  fetch("php/testPermit.php")
    .then(res => res.json())
    .then(response => {

      if (!response.success) {
        console.error(response.message);
        return;
      }

      const data = response.data;

      // Format dates
      const dob = new Date(data.date_of_birth).toLocaleDateString('en-US');
      const testDate = data.date_of_test 
        ? new Date(data.date_of_test).toLocaleDateString('en-US')
        : "N/A";

      const paymentDate = data.payment_date
        ? new Date(data.payment_date).toLocaleDateString('en-US')
        : "N/A";



      // Fill values
      document.getElementById("testPermit").textContent = data.test_permit ?? "-";
      document.getElementById("status").textContent = data.status;
      document.getElementById("fullName").textContent =
        `${data.first_name} ${data.middle_name}. ${data.last_name}`;
      document.getElementById("dob").textContent = dob;
      document.getElementById("age").textContent = data.age;
      document.getElementById("gender").textContent = data.gender;
      document.getElementById("nationality").textContent = data.nationality;
      document.getElementById("contact").textContent = data.contact_number;
      document.getElementById("email").textContent = data.email;

      document.getElementById("dateOfTest").textContent = testDate;
      // document.getElementById("venue").textContent = data.venue ?? "-";

      document.getElementById("transactionNo").textContent = data.transaction_no ?? "-";
      document.getElementById("paymentDate").textContent = paymentDate;
      document.getElementById("amount").textContent =
        data.payment_amount ? `₱${data.payment_amount}.00` : "-";

    })
    .catch(err => console.error(err));

});
